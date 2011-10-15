<?php

namespace UseBB\Utils\Translation;

use UseBB\System\ServiceAccessor;
use UseBB\System\ServiceRegistry;
use UseBB\Utils\File\InfoFile;

/**
 * Translation service.
 * 
 * This reads translation files from system and modules, and provides access
 * to the translations available. It supports per-module and section files,
 * and plural forms.
 * 
 * \author Dietrich Moerman
 */
class Service extends ServiceAccessor {
	private $available;
	private $pluralFunctions = array();
	private $numberFormats = array();
	private $cache = array();
	private $language;
	
	public function __construct(ServiceRegistry $registry) {
		parent::__construct($registry);
		
		$this->pluralFunctions["en"] = array($this, "defaultPluralFunction");
		$this->numberFormats["en"] = array(".", ",");
	}
	
	/**
	 * Default plural form (English etc).
	 * 
	 * \param $n Num
	 * \returns Plural form
	 */
	private function defaultPluralFunction($n) {
		return $n != 1;
	}
	
	/**
	 * Read language information.
	 * 
	 * \param $file File
	 * \param $langDir Languages directory
	 */
	private function readLanguageInfo(\SplFileInfo $file, $langDir) {
		if (!$file->isDir() || $file->isDot()) {
			return;
		}
		
		$name = $file->getFilename();
		$infoFile = new InfoFile($langDir . $name . "/translationInfo.php");
		
		$translationInfo = $infoFile->getInfo();
		
		$defaultLongName = array($name, $name);
		$defaultPluralFunction = array($this, "defaultPluralFunction");
		$defaultNumberFormat = array(".", ",");
		
		$longName = isset($translationInfo["longName"]) 
			&& is_array($translationInfo["longName"]) 
			&& count($translationInfo["longName"]) === 2
			? $translationInfo["longName"]
			: $defaultLongName;
		$pluralFunction = isset($translationInfo["pluralFunction"])
			&& is_callable($translationInfo["pluralFunction"])
			? $translationInfo["pluralFunction"]
			: $defaultPluralFunction;
		$numberFormat = isset($translationInfo["numberFormat"]) 
			&& is_array($translationInfo["numberFormat"]) 
			&& count($translationInfo["numberFormat"]) === 2
			? $translationInfo["numberFormat"]
			: $defaultNumberFormat;
		
		$this->available[$name] = $longName;
		$this->pluralFunctions[$name] = $pluralFunction;
		$this->numberFormats[$name] = $numberFormat;
	}
	
	/**
	 * Get available languages.
	 * 
	 * \returns Array with code as key and names as values.
	 */
	public function getAvailableLanguages() {
		if (isset($this->available)) {
			return $this->available;
		}
		
		$this->available = array("en" => array("English", "English"));
		$langDir = USEBB_ROOT_PATH . "/includes/translations/";
		$iterator = new \DirectoryIterator($langDir);
		
		foreach ($iterator as $file) {
			$this->readLanguageInfo($file, $langDir);
		}
		
		return $this->available;
	}
	
	/**
	 * Initialise languages.
	 */
	private function initLanguages() {
		$available = array_keys($this->getAvailableLanguages());
		$this->language = $this->getService("context")->getLanguage($available);
	}
	
	/**
	 * Get the language code used.
	 * 
	 * \returns Language code
	 */
	public function getLanguage() {
		if ($this->language === NULL) {
			$this->initLanguages();
		}
		
		return $this->language;
	}
	
	/**
	 * Set the language code to use.
	 * 
	 * \param $language Language code
	 */
	public function setLanguage($language) {
		$available = array_keys($this->getAvailableLanguages());
		
		if (!in_array($language, $available)) {
			$language = "en";
		}
		
		$this->language = $language;
	}
	
	/**
	 * Load a section for a module and language.
	 * 
	 * \param $module Module name
	 * \param $section Section name
	 * \param $language Language name
	 */
	private function loadSection($module, $section, $language) {
		// Already loaded.
		if (isset($this->cache[$module][$section][$language])) {
			return;
		}
		
		if (!isset($this->cache[$module])) {
			$this->cache[$module] = array();
		}
		
		if (!isset($this->cache[$module][$section])) {
			$this->cache[$module][$section] = array();
		}
		
		// Create an array as placeholder, even if not loaded later on.
		$this->cache[$module][$section][$language] = array();
		
		// Do not attempt to load a language that is not available (in system).
		if (!isset($this->available[$language])) {
			return;
		}
		
		$file = new TranslationFile($module, $section, $language);
		$this->cache[$module][$section][$language] = $file->getTranslations();
	}
	
	/**
	 * Get the translation in specific place.
	 * 
	 * Try to find the string in the specified module and section,
	 * but fallback to main section and system translation if necessary.
	 * 
	 * \param $source Source string
	 * \param $module Module name
	 * \param $section Section name
	 * \param $language Language name
	 * \returns Translation or \c FALSE
	 */
	private function getTranslation($source, $module, $section, $language) {
		$tryModules = $module == "system"
			? array($module)
			: array($module, "system");
		$trySections = $section == "main"
			? array($section)
			: array($section, "main");
		
		foreach ($tryModules as $tryModule) {
			foreach ($trySections as $trySection) {
				$this->loadSection($tryModule, $trySection, 
					$language);
				$strings = &$this->cache[$tryModule][$trySection];
				
				if (isset($strings[$language][$source])) {
					return $strings[$language][$source];
				}
			}
		}
		
		return FALSE;
	}
	
	/**
	 * Translate a string.
	 * 
	 * Arguments are applied using AbstractContext::applyArgumentsToString().
	 * 
	 * Available options:
	 * \li \c language: language code
	 * \li \c context: context string (appended to source string with \c __)
	 * \li \c noNumberFormat: disable number formatting
	 * \li \c decimals: number of decimals for numbers
	 * 
	 * \param $module Module name
	 * \param $section Section name
	 * \param $source Source string
	 * \param $args Arguments
	 * \param $options Options
	 */
	public function translate($module, $section, $source, array $args = array(), 
		array $options = array()) {
		if ($this->language === NULL) {
			$this->initLanguages();
		}
		
		if (!isset($options["language"])) {
			$options["language"] = $this->language;
		}
		
		if (empty($options["noNumberFormat"])) {
			$this->doNumberFormatting($args, $options);
		}
		
		$context = $this->getService("context");
		
		// No translations used.
		if ($options["language"] == "en") {
			return $context->applyArgumentsToString($source, $args);
		}
		
		if (!isset($section)) {
			$section = "main";
		}
		
		$options["context"] = isset($options["context"])
			? "__" . $options["context"]
			: "";
		
		$translation = $this->getTranslation($source . $options["context"], 
			$module, $section, $options["language"]);
		
		if ($translation !== FALSE) {
			return $context->applyArgumentsToString($translation, $args);
		}
		
		// Nothing found, use internal English string.
		return $context->applyArgumentsToString($source, $args);
	}
	
	/**
	 * Select a translation for plurals and apply arguments.
	 * 
	 * \param $translations Array with translations (plural forms)
	 * \param $count Count
	 * \param $language Language code
	 * \param $args Arguments
	 * \param $context Context name
	 */
	private function selectTranslation($translations, $count, $language, 
		array $args, $context) {
		$func = $this->pluralFunctions[$language];
		$form = (int) call_user_func($func, $count);
		$translation = $translations[$form];
		
		return $context->applyArgumentsToString($translation, $args);
	}
	
	/**
	 * Translate plural strings.
	 * 
	 * The plural string is used as a key for the translation files.
	 * 
	 * The \c $count parameter will be added to the arguments as \c @@count.
	 * Arguments are applied using AbstractContext::applyArgumentsToString().
	 * 
	 * Available options are equal to translate().
	 * 
	 * \param $module Module name
	 * \param $section Section name
	 * \param $singular Singular source string
	 * \param $plural Plural source string
	 * \param $count Count
	 * \param $args Arguments
	 * \param $options Options
	 */
	public function translatePlural($module, $section, $singular, $plural, 
		$count, array $args = array(), array $options = array()) {
		if ($this->language === NULL) {
			$this->initLanguages();
		}
		
		if (!isset($options["language"])) {
			$options["language"] = $this->language;
		}
		
		$args["@count"] = $count;
		
		if (empty($options["noNumberFormat"])) {
			$this->doNumberFormatting($args, $options);
		}
		
		$context = $this->getService("context");
		
		// No translations used.
		if ($options["language"] == "en") {
			return $this->selectTranslation(array($singular, $plural), $count, 
				$options["language"], $args, $context);
		}
		
		if (!isset($section)) {
			$section = "main";
		}
		
		$options["context"] = isset($options["context"])
			? "__" . $options["context"]
			: "";
		
		$translation = $this->getTranslation($plural . $options["context"], 
			$module, $section, $options["language"]);
		
		if ($translation !== FALSE) {
			return $this->selectTranslation($translation, $count, 
				$options["language"], $args, $context);
		}
		
		// Nothing found, use internal English string.
		return $this->selectTranslation(array($singular, $plural), $count, 
			"en", $args, $context);
	}
	
	/**
	 * Format a number according to the language.
	 * 
	 * Available options:
	 * \li \c language: language code
	 * \li \c decimals: number of decimals for numbers
	 * 
	 * \param $number Number
	 * \param $options Options
	 * \returns String
	 */
	public function formatNumber($number, array $options = array()) {
		if ($this->language === NULL) {
			$this->initLanguages();
		}
		
		if (!isset($options["language"])) {
			$options["language"] = $this->language;
		} elseif (!isset($this->available[$options["language"]])) {
			$options["language"] = "en";
		}
		
		list($decPoint, $thousands) = 
			$this->numberFormats[$options["language"]];
		$decimals = isset($options["decimals"]) 
			? $options["decimals"]
			: (int) !is_int($number);
		
		return number_format($number, $decimals, $decPoint, $thousands);
	}
	
	/**
	 * Apply number formatting to an array.
	 * 
	 * \param $args Arguments by reference
	 * \param $options Options
	 */
	private function doNumberFormatting(array &$args, array $options) {
		foreach ($args as $k => $v) {
			if (!is_numeric($v)) {
				continue;
			}
			
			$args[$k] = $this->formatNumber($v, $options);
		}
	}
}
