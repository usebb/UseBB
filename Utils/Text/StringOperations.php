<?php

namespace UseBB\Utils\Text;

require USEBB_ROOT_PATH . "includes/utf8/utf8.php";

use DokuWiki\UTF8;

/**
 * String operations with UTF-8 support.
 * 
 * This class uses the UTF-8 functions implemented for DokuWiki.
 * See \c includes/utf8/utf8.php for implementations.
 * 
 * \note Not all available functions are currently implemented.
 * 
 * For more information on string operations, please see the PHP manual.
 * 
 * \author Dietrich Moerman
 */
class StringOperations {
	/**
	 * Get string length.
	 * 
	 * \param $string String
	 * \returns Length
	 */
	public function strlen($string) {
		return UTF8\utf8_strlen($string);
	}
	
	/**
	 * Get substring.
	 * 
	 * \param $str String
	 * \param $offset Offset
	 * \param $length Length
	 * \returns Substring
	 */
	public function substr($str, $offset, $length = NULL) {
		return UTF8\utf8_substr($str, $offset, $length);
	}
	
	/**
	 * Replace substring.
	 * 
	 * \param $string String
	 * \param $replacement Replacement
	 * \param $start Start
	 * \param $length Length
	 * \returns New string
	 */
	public function substr_replace($string, $replacement, $start, $length = 0) {
		return UTF8\utf8_substr_replace($string, $replacement, $start, $length);
	}
	
	/**
	 * Trim from the left.
	 * 
	 * \param $str String
	 * \param $charlist Character list
	 * \returns Trimmed string
	 */
	public function ltrim($str, $charlist = "") {
		return UTF8\utf8_ltrim($str, $charlist);
	}
	
	/**
	 * Trim from the right.
	 * 
	 * \param $str String
	 * \param $charlist Character list
	 * \returns Trimmed string
	 */
	public function rtrim($str, $charlist = "") {
		return UTF8\utf8_rtrim($str, $charlist);
	}
	
	/**
	 * Trim from both sides.
	 * 
	 * \param $str String
	 * \param $charlist Character list
	 * \returns Trimmed string
	 */
	public function trim($str, $charlist = "") {
		return UTF8\utf8_trim($str, $charlist);
	}
	
	/**
	 * Get lowercase string.
	 * 
	 * \param $string String
	 * \returns Lowercase string
	 */
	public function strtolower($string) {
		return UTF8\utf8_strtolower($string);
	}
	
	/**
	 * Get uppercase string.
	 * 
	 * \param $string String
	 * \returns Uppercase string
	 */
	public function strtoupper($string) {
		return UTF8\utf8_strtoupper($string);
	}
	
	/**
	 * Uppercase first character.
	 * 
	 * \param $str String
	 * \returns New string
	 */
	public function ucfirst($str) {
		return UTF8\utf8_ucfirst($str);
	}
	
	/**
	 * Uppercase first character of all words.
	 * 
	 * \param $str String
	 * \returns New string
	 */
	public function ucwords($str) {
		return UTF8\utf8_ucwords($str);
	}
	
	/**
	 * Get string position.
	 * 
	 * \param $haystack Haystack
	 * \param $needle Needle
	 * \param $offset Offset
	 * \returns String position
	 */
	public function strpos($haystack, $needle, $offset = 0) {
		return UTF8\utf8_strpos($haystack, $needle, $offset);
	}
	
	/**
	 * Encodes UTF-8 characters to HTML entities.
	 * 
	 * \param $str String
	 * \returns Converted string
	 */
	public function toHTMLEntities($str) {
		return UTF8\utf8_tohtml($str);
	}
	
	/**
	 * Decodes HTML entities to UTF-8 characters.
	 * 
	 * \param $str String
	 * \param $entities If \c HTML_ENTITIES, named entities are also decoded
	 * \returns Converted string
	 */
	public function fromHTMLEntities($str, $entities = NULL) {
		return UTF8\utf8_unhtml($str, $entities);
	}
	
	/**
	 * Convert to UTF-8 MIME string.
	 * 
	 * \param $str String
	 * \returns Converted string
	 */
	public function toMIME($str) {
		if (empty($str)) {
			return "";
		}
		
		return "=?utf-8?B?" . base64_encode($str) . "?=";
	}
}
