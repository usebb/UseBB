<?php

namespace UseBB\Utils\File;

use SplFileInfo;

/**
 * Information file loader.
 * 
 * Currently, UseBB uses PHP files with a single array.
 * 
 * \author Dietrich Moerman
 */
class InfoFile {
	private $fileInfo;
	private $rootName;
	
	/**
	 * Constructor.
	 * 
	 * \param $fileInfo SplFileInfo instance or file path
	 * \param $rootName Root (array) name (optional)
	 */
	public function __construct($fileInfo, $rootName = NULL) {
		if (!($fileInfo instanceof SplFileInfo)) {
			$fileInfo = new SplFileInfo($fileInfo);
		}
		
		$this->fileInfo = $fileInfo;
		$this->rootName = $rootName === NULL
			? pathinfo($fileInfo->getPathname(), \PATHINFO_FILENAME)
			: (string) $rootName;
	}
	
	/**
	 * Get the information in the file.
	 * 
	 * \returns Array with info, empty when unavailable
	 */
	public function getInfo() {
		$f = $this->fileInfo;
		$r = $this->rootName;
		
		if (!$f->isFile() || !$f->isReadable()) {
			return array();
		}
		
		require $f->getPathname();
		
		if (!isset(${$r}) || !is_array(${$r})) {
			return array();
		}
		
		return ${$r};
	}
}
