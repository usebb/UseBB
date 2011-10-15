<?php

namespace UseBB\Utils\Translation;

use UseBB\Utils\File\InfoFile;

/**
 * Translation file
 * 
 * Reads a translation file and returns the translation array.
 * 
 * \author Dietrich Moerman
 */
class TranslationFile {
	private $infoFile;
	
	/**
	 * Constructor.
	 * 
	 * \param $module Module name
	 * \param $section Section name
	 * \param $language Language name
	 */
	public function __construct($module, $section, $language) {
		$dir = $module == "system"
			? USEBB_ROOT_PATH . "/includes/translations/"
			: USEBB_ROOT_PATH . "/Modules/" . $module . "/translations/";
		$this->infoFile = new InfoFile($dir . $language . "/" . $section . 
			".php", "translations");
	}
	
	/**
	 * Get the translation array.
	 * 
	 * \returns Array with translations
	 */
	public function getTranslations() {
		return $this->infoFile->getInfo();
	}
}
