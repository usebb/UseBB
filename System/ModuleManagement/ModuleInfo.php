<?php

namespace UseBB\System\ModuleManagement;

use UseBB\System\ServiceAccessor;
use UseBB\System\ServiceRegistry;

/**
 * Module info class.
 * 
 * This class keeps information about the available module, whether it has been
 * installed and enabled and has means of changing its status.
 * 
 * \attention You should not instantiate this class yourself, but use
 * Registry::getModuleInfo().
 * 
 * \author Dietrich Moerman
 */
class ModuleInfo extends ServiceAccessor {
	private $info;
	private $modules;
	private $module;
	private $versionChanged = FALSE;
	private $requirements;
	
	/**
	 * Constructor.
	 * 
	 * \param $services ServiceRegistry instance
	 * \param $info Array with module info
	 * \param $modules Registry instance
	 */
	public function __construct(ServiceRegistry $services, array $info, 
		Registry $modules) {
		parent::__construct($services);
		
		$this->info = $info;
		$this->modules = $modules;
		
		$this->checkVersions();
	}
	
	/**
	 * Compare the installed and available version.
	 * 
	 * Disable the module when not equal.
	 */
	private function checkVersions() {
		if (!$this->info["installed"] 
			|| $this->info["installedVersion"] == $this->info["version"]) {
			return;
		}
		
		$this->versionChanged = TRUE;
		$this->disable();
	}
	
	/**
	 * Check the dependencies and their status.
	 */
	private function checkDependencies() {
		$available = $this->modules->getModuleNames();
		
		if (!isset($this->info["dependencies"]) 
			|| !is_array($this->info["dependencies"])) {
			return;
		}
		
		foreach ($this->info["dependencies"] as $name => $version) {
			if (!in_array($name, $available)) {
				$this->requirements[$name] = array($version, FALSE);
				
				continue;
			}
			
			$depMod = $this->modules->getModuleInfo($name);
			$this->requirements[$name] = array($version, 
				version_compare($version, $depMod->getVersion(), ">")
				&& $depMod->isInstalled());
		}
	}
	
	/**
	 * Check requirements for installation.
	 * 
	 * \returns Boolean
	 */
	public function requirementsMet() {
		$statuses = array_map(
			function($e) { return $e[1]; }, 
			array_values($this->getRequirements()));
		
		$status = TRUE;
		foreach ($statuses as $thisStatus) {
			$status = $status && $thisStatus;
		}
		
		return $status;
	}
	
	/**
	 * Get requirements for installation.
	 * 
	 * \returns Array with requirements and statuses
	 */
	public function getRequirements() {
		if (is_array($this->requirements)) {
			return $this->requirements;
		}
		
		$this->requirements = array();
		$sysInfo = $this->getService("info");
		
		$this->requirements["systemMajorVersion"] = 
			array($this->info["systemMajorVersion"],
				$this->info["systemMajorVersion"] 
				== $sysInfo->getMajorUseBBVersion());
		
		$this->requirements["systemMinVersion"] = 
			array($this->info["systemMinVersion"],
				$sysInfo->compareVersion($this->info["systemMinVersion"]));
		
		$this->checkDependencies();
		
		return $this->requirements;
	}
	
	/**
	 * Get the short name.
	 * 
	 * \returns Short name
	 */
	public function getShortName() {
		return $this->info["shortName"];
	}
	
	/**
	 * Get the long name.
	 * 
	 * \returns Long name
	 */
	public function getLongName() {
		return $this->info["longName"];
	}
	
	/**
	 * Get the version.
	 * 
	 * \returns Version
	 */
	public function getVersion() {
		return $this->info["version"];
	}
	
	/**
	 * Get the installed version.
	 * 
	 * \returns Installed version
	 */
	public function getInstalledVersion() {
		return $this->info["installed"]
			? $this->info["installedVersion"]
			: FALSE;
	}
	
	/**
	 * Get the category.
	 * 
	 * \returns Category
	 */
	public function getCategory() {
		return $this->info["category"];
	}
	
	/**
	 * Get the authors.
	 * 
	 * \returns Authors array
	 */
	public function getAuthors() {
		return $this->info["authors"];
	}
	
	/**
	 * Get the install status.
	 * 
	 * \returns Installed
	 */
	public function isInstalled() {
		return $this->info["installed"];
	}
	
	/**
	 * Get the enable status.
	 * 
	 * \returns Enabled
	 */
	public function isEnabled() {
		return $this->info["enabled"];
	}
	
	/**
	 * Did the version change?
	 * 
	 * \returns Changed
	 */
	public function isVersionChanged() {
		return $this->versionChanged;
	}
	
	/**
	 * Get the module's main class.
	 * 
	 * \returns AbstractModule instance
	 */
	public function getModule() {
		if ($this->module === NULL) {
			$className = "UseBB\Modules\\" . 
				$this->info["shortName"] . "\Module";
			$this->module = new $className($this->getServiceRegistry());
		}
		
		return $this->module;
	}
	
	/**
	 * Install the module.
	 * 
	 * \exception UnmetDependenciesException When unmet dependencies
	 */
	public function install() {
		if ($this->info["installed"]) {
			return;
		}
		
		if (!$this->requirementsMet()) {
			throw new UnmetDependenciesException($this->info["shortName"]);
		}
		
		$schemaManager = $this->getModule()->getSchemaManager();
		
		if ($schemaManager !== NULL) {
			$schemaManager->install();
		}
		
		$this->getService("database")->insert("modules", array(
			"name"    => $this->info["shortName"],
			"enabled" => 0,
			"version" => $this->info["version"]
		));
		
		$this->info["installed"] = TRUE;
	}
	
	/**
	 * Uninstall the module.
	 */
	public function uninstall() {
		if (!$this->info["installed"]) {
			return;
		}
		
		if ($this->info["enabled"]) {
			$this->disable();
		}
		
		$schemaManager = $this->getModule()->getSchemaManager();
		
		if ($schemaManager !== NULL) {
			$schemaManager->uninstall();
		}
		
		$this->getService("database")->delete("modules", array(
			"name" => $this->info["shortName"]
		));
		
		$this->info["installed"] = FALSE;
	}
	
	/**
	 * Change enable status.
	 * 
	 * \param $enabled Enabled
	 */
	private function setEnabled($enabled) {
		$this->getService("database")->update("modules", array(
			"enabled" => (int) $enabled
		), array(
			"name" => $this->info["shortName"]
		));
		
		$this->info["enabled"] = (bool) $enabled;
	}
	
	/**
	 * Enable the module.
	 * 
	 * This will call \c %enable() on the module itself.
	 * 
	 * \exception EnableOutdatedModuleException When enabling outdated module
	 */
	public function enable() {
		if ($this->info["enabled"]) {
			return;
		}
		
		if ($this->versionChanged) {
			throw new EnableOutdatedModuleException($this->info["shortName"],
				$this->info["version"], $this->info["installedVersion"]);
		}
		
		if (!$this->info["installed"]) {
			$this->install();
		}
		
		$this->setEnabled(TRUE);
		$this->getModule()->enable();
	}
	
	/**
	 * Disable the module.
	 * 
	 * This will call \c %disable() on the module itself.
	 */
	public function disable() {
		if (!$this->info["enabled"]) {
			return;
		}
		
		$this->setEnabled(FALSE);
		$this->getModule()->disable();
	}
	
	/**
	 * Update the module.
	 * 
	 * This will perform schema updates (if any) and adjust the version number. 
	 * It will also call \c %update() on the module itself.
	 * 
	 * \exception UpdateToOlderVersionException When updating to older module version
	 * \exception UnmetDependenciesException When unmet dependencies
	 */
	public function update() {
		if (!$this->versionChanged) {
			return;
		}
		
		if (version_compare($this->info["version"], 
			$this->info["installedVersion"], "<")) {
			throw new UpdateToOlderVersionException($this->info["shortName"],
				$this->info["version"], $this->info["installedVersion"]);
		}
		
		if (!$this->requirementsMet()) {
			throw new UnmetDependenciesException($this->info["shortName"]);
		}
		
		$schemaManager = $this->getModule()->getSchemaManager();
		
		if ($schemaManager !== NULL) {
			$schemaManager->update();
		}
		
		$this->getService("database")->update("modules", array(
			"version" => $this->info["version"]
		), array(
			"name" => $this->info["shortName"]
		));
		
		$this->getModule()->update($this->info["installedVersion"]);
		$this->versionChanged = FALSE;
	}
}
