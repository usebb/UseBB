<?php

namespace UseBB\System\ModuleManagement;

use UseBB\System\ServiceAccessor;

/**
 * Module management class.
 * 
 * This class can read through the Modules directory and read the available 
 * modules' info, see their current status and return ModuleInfo instances.
 * 
 * \author Dietrich Moerman
 */
class Registry extends ServiceAccessor {
	private $allModules;
	private $moduleStatuses = array();
	private $moduleVersions = array();
	
	/**
	 * Load in the module statuses from the database.
	 */
	private function getModuleStatuses() {
		if (!$this->systemIsInstalled()) {
			// Not installed - only activate SimpleInstall.
			$this->moduleStatuses["SimpleInstall"] = TRUE;
			$this->moduleVersions["SimpleInstall"] = "1.0.0";
			
			return;
		}
		
		$stmt = $this->getService("database")->newQuery()
			->select("m.name", "m.enabled", "m.version")
			->from("modules", "m")->execute();
		
		$all = $stmt->fetchAll();
		
		foreach ($all as $module) {
			$this->moduleStatuses[$module["name"]] = (bool) $module["enabled"];
			$this->moduleVersions[$module["name"]] = $module["version"];
		}
		
		$stmt->closeCursor();
	}
	
	/**
	 * Fill a module's info array.
	 * 
	 * \param $moduleName Module short name
	 * \param $info Info array
	 */
	private function fillModuleInfo($moduleName, array &$info) {
		$sysInfo = $this->getService("info");
		
		if (!isset($info["longName"])) {
			$info["longName"] = $moduleName;
		}
		
		if (!isset($info["version"])) {
			$info["version"] = "1.0.0";
		}
		
		if (!isset($info["systemMajorVersion"])) {
			$info["systemMajorVersion"] = $sysInfo->getMajorUseBBVersion();
		}
		
		if (!isset($info["systemMinVersion"])) {
			$info["systemMinVersion"] = $sysInfo->getUseBBVersion();
		}
		
		if (!isset($info["category"])) {
			$info["category"] = "uncategorized";
		}
		
		if (!isset($info["authors"])) {
			$info["authors"] = array("unknown");
		} elseif (!is_array($info["authors"])) {
			$info["authors"] = (array) $info["authors"];
		}
		
		// Always overwrite
		$info["shortName"] = $moduleName;
		$info["installed"] = isset($this->moduleStatuses[$moduleName]);
		$info["enabled"] = $info["installed"]
			&& $this->moduleStatuses[$moduleName];
		
		if ($info["installed"]) {
			$info["installedVersion"] = $this->moduleVersions[$moduleName];
		}
	}
	
	/**
	 * Read a single module.
	 * 
	 * \param $file File information
	 * \param $modulesDir Modules directory
	 */
	private function readModule(\SplFileInfo $file, $modulesDir) {
		$moduleName = $file->getFilename();
		$moduleDir = $modulesDir . $moduleName;
		$infoFile = $moduleDir . "/moduleInfo.php";
		$moduleFile = $moduleDir . "/Module.php";
		
		if (!$file->isDir() || $file->isDot() || !file_exists($infoFile) 
			|| !file_exists($moduleFile)) {
			return;
		}
		
		require $infoFile;
		
		if (!isset($moduleInfo) || !is_array($moduleInfo)) {
			return;
		}
		
		$this->fillModuleInfo($moduleName, $moduleInfo);
		$this->allModules[$moduleName] = 
			new ModuleInfo($this->getServiceRegistry(), $moduleInfo, $this);
	}
	
	/**
	 * Read all modules.
	 */
	private function readAllModules() {
		$this->getModuleStatuses();
		
		$modulesDir = USEBB_ROOT_PATH . "Modules/";
		$iterator = new \DirectoryIterator($modulesDir);
		$this->allModules = array();
		
		foreach ($iterator as $file) {
			$this->readModule($file, $modulesDir);
		}
		
		ksort($this->allModules);
	}
	
	/**
	 * Get a list of all available modules' short names.
	 * 
	 * \return Array of short names
	 */
	public function getModuleNames() {
		if (!is_array($this->allModules)) {
			$this->readAllModules();
		}
		
		return array_keys($this->allModules);
	}
	
	/**
	 * Get an array with all available modules.
	 * 
	 * \returns Array with short names and ModuleInfo instances
	 */
	public function getAllModulesInfo() {
		if (!is_array($this->allModules)) {
			$this->readAllModules();
		}
		
		return $this->allModules;
	}
	
	/**
	 * Get the ModuleInfo instance for a specific short name.
	 * 
	 * \param $name Module short name
	 * \returns ModuleInfo instance
	 * 
	 * \exception ModuleNotFoundException When module name not found
	 */
	public function getModuleInfo($name) {
		if (!is_array($this->allModules)) {
			$this->readAllModules();
		}
		
		if (!isset($this->allModules[$name])) {
			throw new ModuleNotFoundException($name);
		}
		
		return $this->allModules[$name];
	}
	
	/**
	 * Run all enabled modules.
	 * 
	 * This will call \c runForX() on every module, so it can register handlers
	 * and plugins. \c X varies with the current context name.
	 */
	public function runModules() {
		if (!is_array($this->allModules)) {
			$this->readAllModules();
		}
		
		$contextName = $this->getService("context")->getName();
		
		foreach ($this->allModules as $module) {
			if (!$module->isEnabled()) {
				continue;
			}
			
			$main = $module->getModule();
			call_user_func(array(&$main, "runFor" . $contextName));
		}
	}
	
	/**
	 * Refresh the module list.
	 * 
	 * \warning Existing ModuleInfo instances will get out of sync, and 
	 * results of previously running modules cannot be undone!
	 */
	public function refresh() {
		$this->moduleStatuses = array();
		$this->moduleVersions = array();
		$this->readAllModules();
	}
}
