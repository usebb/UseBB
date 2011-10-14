<?php

namespace UseBB\Modules\SimpleInstall;

use UseBB\System\AbstractController;
use UseBB\Utils\SchemaManagement\SystemSchema;

/**
 * Simple %Installer.
 * 
 * \author Dietrich Moerman
 */
class Installer extends AbstractController {
	/**
	 * Handling the root request.
	 * 
	 * \request{\rootRequest}
	 */
	public function doIt() {
		$output = "<h1>" . $this->t("UseBB Simple Installer") . "</h1>";
		$can = $this->canConnect();
		
		if ($can && $this->getService("input")
			->key("install", "POST")->hasValue()) {
			$this->doInstall($output);
		} else {
			$this->mainScreen($output, $can);
		}
		
		echo $output;
	}
	
	/**
	 * Whether can connect to DB.
	 * 
	 * \returns Status
	 */
	private function canConnect() {
		try {
			$this->getService("database");
		} catch (\PDOException $e) {
			return FALSE;
		}
		
		return TRUE;
	}
	
	/**
	 * Main screen.
	 * 
	 * \param $output Output
	 * \param $can Can connect
	 */
	private function mainScreen(&$output, $can) {
		$output .= "<ol>
			<li>" . $this->t("Edit <code>dbConfig.php</code> to match your database settings.") . "</li>
			<li>" . $this->t("Access or reload this page.") . "</li>
			<li>" . $this->t("Click <em>Perform installation</em>.") . "</li>
		</ol>";
		
		if ($can) {
			$output .= "<form method='POST'>
				<p><input type='submit' name='install' value='" . $this->t("Perform installation") . "' /></p>
			</form>";
		} else {
			$output .= "<p>" . $this->t("No working database connection... yet.") . "</p>";
		}
	}
	
	/**
	 * Install screen.
	 * 
	 * \param $output Output
	 */
	private function doInstall(&$output) {
		$systemSchema = new SystemSchema($this->getServiceRegistry());
		$systemSchema->install();
		
		$output .= "<p>" . $this->t("Installation is done!") . "</p>";
	}
}
