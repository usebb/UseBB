<?php

/*
	Copyright (C) 2003-2012 UseBB Team
	http://www.usebb.net
	
	$Id$
	
	This file is part of UseBB.
	
	UseBB is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; either version 2 of the License, or
	(at your option) any later version.
	
	UseBB is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.
	
	You should have received a copy of the GNU General Public License
	along with UseBB; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/**
 * Functions
 *
 * Contains all kinds of procedural functions and the functions class.
 *
 * @author	UseBB Team
 * @link	http://www.usebb.net
 * @license	GPL-2
 * @version	$Revision$
 * @copyright	Copyright (C) 2003-2012 UseBB Team
 * @package	UseBB
 * @subpackage Core
 */

//
// Die when called directly in browser
//
if ( !defined('INCLUDED') )
	exit();

/**
 * Debug output function
 *
 * Takes variable number of arguments that get printed out to template.
 */
function usebb_debug_output() {
	
	global $template;

	$numargs = func_num_args();

	if ( $template == null || USEBB_IS_PROD_ENV || $numargs == 0 )
		return;
	
	$values = array_map('unhtml', array_map('print_r', func_get_args(), array_fill(0, $numargs, true)));
	$template->add_raw_content('<pre>'.implode('<br />', $values).'</pre>');

}

/**
 * Callback for array_walk
 *
 * Will add slashes to and trim the value.
 * Third parameter disables addslashes (magic_quotes_gpc on)
 */
function usebb_clean_input_value(&$value, $key, $mq=false) {
	
	if ( is_array($value) ) {
		
		array_walk($value, 'usebb_clean_input_value', $mq);
		
	} else {
		
		if ( !$mq )
			$value = addslashes($value);
		
		$value = trim($value);
		
	}

}

/**
 * Callback for array_walk
 *
 * Will add slashes to the value.
 */
function usebb_clean_db_value(&$value, $key) {
	
	if ( is_array($value) )
		array_walk($value, 'usebb_clean_db_value');
	else
		$value = addslashes($value);
	
}

/**
 * Check whether the string contains HTML entities.
 *
 * @param string $string String to check
 * @param bool $num_only Look for &#...; only
 * @returns bool Contains entities
 */
function contains_entities($string, $num_only=false) {
	
	return preg_match(( $num_only ? '#&\#[^;]+;#' : '#&\#?[^;]+;#' ), $string);
	
}

/**
 * Resets the named entities to the code ones.
 *
 * Code from the Drupal Atom module, patch by stefanor (at Drupal.org).
 * @link http://drupal.org/node/579286
 *
 * @param string $string String
 * @returns string String
 */
function named_entities_to_numeric($string) {
	
  $table = array(
    "&nbsp;"     => "&#160;",
    "&iexcl;"    => "&#161;",
    "&cent;"     => "&#162;",
    "&pound;"    => "&#163;",
    "&curren;"   => "&#164;",
    "&yen;"      => "&#165;",
    "&brvbar;"   => "&#166;",
    "&sect;"     => "&#167;",
    "&uml;"      => "&#168;",
    "&copy;"     => "&#169;",
    "&ordf;"     => "&#170;",
    "&laquo;"    => "&#171;",
    "&not;"      => "&#172;",
    "&shy;"      => "&#173;",
    "&reg;"      => "&#174;",
    "&macr;"     => "&#175;",
    "&deg;"      => "&#176;",
    "&plusmn;"   => "&#177;",
    "&sup2;"     => "&#178;",
    "&sup3;"     => "&#179;",
    "&acute;"    => "&#180;",
    "&micro;"    => "&#181;",
    "&para;"     => "&#182;",
    "&middot;"   => "&#183;",
    "&cedil;"    => "&#184;",
    "&sup1;"     => "&#185;",
    "&ordm;"     => "&#186;",
    "&raquo;"    => "&#187;",
    "&frac14;"   => "&#188;",
    "&frac12;"   => "&#189;",
    "&frac34;"   => "&#190;",
    "&iquest;"   => "&#191;",
    "&Agrave;"   => "&#192;",
    "&Aacute;"   => "&#193;",
    "&Acirc;"    => "&#194;",
    "&Atilde;"   => "&#195;",
    "&Auml;"     => "&#196;",
    "&Aring;"    => "&#197;",
    "&AElig;"    => "&#198;",
    "&Ccedil;"   => "&#199;",
    "&Egrave;"   => "&#200;",
    "&Eacute;"   => "&#201;",
    "&Ecirc;"    => "&#202;",
    "&Euml;"     => "&#203;",
    "&Igrave;"   => "&#204;",
    "&Iacute;"   => "&#205;",
    "&Icirc;"    => "&#206;",
    "&Iuml;"     => "&#207;",
    "&ETH;"      => "&#208;",
    "&Ntilde;"   => "&#209;",
    "&Ograve;"   => "&#210;",
    "&Oacute;"   => "&#211;",
    "&Ocirc;"    => "&#212;",
    "&Otilde;"   => "&#213;",
    "&Ouml;"     => "&#214;",
    "&times;"    => "&#215;",
    "&Oslash;"   => "&#216;",
    "&Ugrave;"   => "&#217;",
    "&Uacute;"   => "&#218;",
    "&Ucirc;"    => "&#219;",
    "&Uuml;"     => "&#220;",
    "&Yacute;"   => "&#221;",
    "&THORN;"    => "&#222;",
    "&szlig;"    => "&#223;",
    "&agrave;"   => "&#224;",
    "&aacute;"   => "&#225;",
    "&acirc;"    => "&#226;",
    "&atilde;"   => "&#227;",
    "&auml;"     => "&#228;",
    "&aring;"    => "&#229;",
    "&aelig;"    => "&#230;",
    "&ccedil;"   => "&#231;",
    "&egrave;"   => "&#232;",
    "&eacute;"   => "&#233;",
    "&ecirc;"    => "&#234;",
    "&euml;"     => "&#235;",
    "&igrave;"   => "&#236;",
    "&iacute;"   => "&#237;",
    "&icirc;"    => "&#238;",
    "&iuml;"     => "&#239;",
    "&eth;"      => "&#240;",
    "&ntilde;"   => "&#241;",
    "&ograve;"   => "&#242;",
    "&oacute;"   => "&#243;",
    "&ocirc;"    => "&#244;",
    "&otilde;"   => "&#245;",
    "&ouml;"     => "&#246;",
    "&divide;"   => "&#247;",
    "&oslash;"   => "&#248;",
    "&ugrave;"   => "&#249;",
    "&uacute;"   => "&#250;",
    "&ucirc;"    => "&#251;",
    "&uuml;"     => "&#252;",
    "&yacute;"   => "&#253;",
    "&thorn;"    => "&#254;",
    "&yuml;"     => "&#255;",
    "&fnof;"     => "&#402;",
    "&Alpha;"    => "&#913;",
    "&Beta;"     => "&#914;",
    "&Gamma;"    => "&#915;",
    "&Delta;"    => "&#916;",
    "&Epsilon;"  => "&#917;",
    "&Zeta;"     => "&#918;",
    "&Eta;"      => "&#919;",
    "&Theta;"    => "&#920;",
    "&Iota;"     => "&#921;",
    "&Kappa;"    => "&#922;",
    "&Lambda;"   => "&#923;",
    "&Mu;"       => "&#924;",
    "&Nu;"       => "&#925;",
    "&Xi;"       => "&#926;",
    "&Omicron;"  => "&#927;",
    "&Pi;"       => "&#928;",
    "&Rho;"      => "&#929;",
    "&Sigma;"    => "&#931;",
    "&Tau;"      => "&#932;",
    "&Upsilon;"  => "&#933;",
    "&Phi;"      => "&#934;",
    "&Chi;"      => "&#935;",
    "&Psi;"      => "&#936;",
    "&Omega;"    => "&#937;",
    "&alpha;"    => "&#945;",
    "&beta;"     => "&#946;",
    "&gamma;"    => "&#947;",
    "&delta;"    => "&#948;",
    "&epsilon;"  => "&#949;",
    "&zeta;"     => "&#950;",
    "&eta;"      => "&#951;",
    "&theta;"    => "&#952;",
    "&iota;"     => "&#953;",
    "&kappa;"    => "&#954;",
    "&lambda;"   => "&#955;",
    "&mu;"       => "&#956;",
    "&nu;"       => "&#957;",
    "&xi;"       => "&#958;",
    "&omicron;"  => "&#959;",
    "&pi;"       => "&#960;",
    "&rho;"      => "&#961;",
    "&sigmaf;"   => "&#962;",
    "&sigma;"    => "&#963;",
    "&tau;"      => "&#964;",
    "&upsilon;"  => "&#965;",
    "&phi;"      => "&#966;",
    "&chi;"      => "&#967;",
    "&psi;"      => "&#968;",
    "&omega;"    => "&#969;",
    "&thetasym;" => "&#977;",
    "&upsih;"    => "&#978;",
    "&piv;"      => "&#982;",
    "&bull;"     => "&#8226;",
    "&hellip;"   => "&#8230;",
    "&prime;"    => "&#8242;",
    "&Prime;"    => "&#8243;",
    "&oline;"    => "&#8254;",
    "&frasl;"    => "&#8260;",
    "&weierp;"   => "&#8472;",
    "&image;"    => "&#8465;",
    "&real;"     => "&#8476;",
    "&trade;"    => "&#8482;",
    "&alefsym;"  => "&#8501;",
    "&larr;"     => "&#8592;",
    "&uarr;"     => "&#8593;",
    "&rarr;"     => "&#8594;",
    "&darr;"     => "&#8595;",
    "&harr;"     => "&#8596;",
    "&crarr;"    => "&#8629;",
    "&lArr;"     => "&#8656;",
    "&uArr;"     => "&#8657;",
    "&rArr;"     => "&#8658;",
    "&dArr;"     => "&#8659;",
    "&hArr;"     => "&#8660;",
    "&forall;"   => "&#8704;",
    "&part;"     => "&#8706;",
    "&exist;"    => "&#8707;",
    "&empty;"    => "&#8709;",
    "&nabla;"    => "&#8711;",
    "&isin;"     => "&#8712;",
    "&notin;"    => "&#8713;",
    "&ni;"       => "&#8715;",
    "&prod;"     => "&#8719;",
    "&sum;"      => "&#8721;",
    "&minus;"    => "&#8722;",
    "&lowast;"   => "&#8727;",
    "&radic;"    => "&#8730;",
    "&prop;"     => "&#8733;",
    "&infin;"    => "&#8734;",
    "&ang;"      => "&#8736;",
    "&and;"      => "&#8743;",
    "&or;"       => "&#8744;",
    "&cap;"      => "&#8745;",
    "&cup;"      => "&#8746;",
    "&int;"      => "&#8747;",
    "&there4;"   => "&#8756;",
    "&sim;"      => "&#8764;",
    "&cong;"     => "&#8773;",
    "&asymp;"    => "&#8776;",
    "&ne;"       => "&#8800;",
    "&equiv;"    => "&#8801;",
    "&le;"       => "&#8804;",
    "&ge;"       => "&#8805;",
    "&sub;"      => "&#8834;",
    "&sup;"      => "&#8835;",
    "&nsub;"     => "&#8836;",
    "&sube;"     => "&#8838;",
    "&supe;"     => "&#8839;",
    "&oplus;"    => "&#8853;",
    "&otimes;"   => "&#8855;",
    "&perp;"     => "&#8869;",
    "&sdot;"     => "&#8901;",
    "&lceil;"    => "&#8968;",
    "&rceil;"    => "&#8969;",
    "&lfloor;"   => "&#8970;",
    "&rfloor;"   => "&#8971;",
    "&lang;"     => "&#9001;",
    "&rang;"     => "&#9002;",
    "&loz;"      => "&#9674;",
    "&spades;"   => "&#9824;",
    "&clubs;"    => "&#9827;",
    "&hearts;"   => "&#9829;",
    "&diams;"    => "&#9830;",
    "&OElig;"    => "&#338;",
    "&oelig;"    => "&#339;",
    "&Scaron;"   => "&#352;",
    "&scaron;"   => "&#353;",
    "&Yuml;"     => "&#376;",
    "&circ;"     => "&#710;",
    "&tilde;"    => "&#732;",
    "&ensp;"     => "&#8194;",
    "&emsp;"     => "&#8195;",
    "&thinsp;"   => "&#8201;",
    "&zwnj;"     => "&#8204;",
    "&zwj;"      => "&#8205;",
    "&lrm;"      => "&#8206;",
    "&rlm;"      => "&#8207;",
    "&ndash;"    => "&#8211;",
    "&mdash;"    => "&#8212;",
    "&lsquo;"    => "&#8216;",
    "&rsquo;"    => "&#8217;",
    "&sbquo;"    => "&#8218;",
    "&ldquo;"    => "&#8220;",
    "&rdquo;"    => "&#8221;",
    "&bdquo;"    => "&#8222;",
    "&dagger;"   => "&#8224;",
    "&Dagger;"   => "&#8225;",
    "&permil;"   => "&#8240;",
    "&lsaquo;"   => "&#8249;",
    "&rsaquo;"   => "&#8250;",
    "&euro;"     => "&#8364;",
  );

  return strtr($string, $table);

}

/**
 * Disable HTML in a string without disabling entities
 *
 * @param string $string String to un-HTML
 * @param bool $rss_mode Do hexadecimal escaping of &, < and > ONLY
 * @returns string Parsed $string
 */
function unhtml($string, $rss_mode=false) {
	
	$string = htmlspecialchars($string);
	
	//
	// Code which is necessary to not break numeric entities (quirky support for strange encodings on a page).
	// Broken entities (without trailing ;) at string end are stripped since they break XML well-formedness.
	//
	if ( strpos($string, '&') !== false )
		$string = preg_replace(array('#&amp;\#([0-9]+)#', '#&\#?[a-z0-9]+$#'), array('&#\\1', ''), $string);
	
	//
	// RSS mode
	//
	if ( $rss_mode )
		$string = named_entities_to_numeric($string);
	
	return $string;
	
}

/**
 * Gives the length of a string and counts a HTML entitiy as one character.
 *
 * @param string $string String to find length of
 * @returns int Length of $string
 */
function entities_strlen($string) {
	
	if ( strpos($string, '&') !== false )
		$string = preg_replace('#&\#?[^;]+;#', '.', $string);
	
	return strlen($string);
	
}

/**
 * Right trim a string to $length characters, keeping entities as one character.
 *
 * @param string $string String to trim
 * @param int $length Length of new string
 * @returns string Trimmed string
 */
function entities_rtrim($string, $length) {
	
	if ( function_exists('mb_language') && mb_language() != 'neutral') {
		
		$strlen = 'mb_strlen';
		$substr = 'mb_substr';
		
	} else {
		
		$strlen = 'strlen';
		$substr = 'substr';
		
	}
	
	if ( strpos($string, '&') === false )
		return $substr($string, 0, $length);
	
	$new_string = '';
	$new_length = $pos = 0;
	$entity_open = false;
	
	while ( $pos < $strlen($string) && ( $new_length < $length || $entity_open ) ) {
		
		$char = $substr($string, $pos, 1);
		
		if ( $char == '&' ) {
			
			$entity_open = true;
			
		} elseif ( $char == ';' && $entity_open ) {
			
			$entity_open = false;
			$new_length++;
			
		} elseif ( !$entity_open ) {
			
			$new_length++;
			
		}
		
		$new_string .= $char;
		$pos++;
		
	}
	
	return $new_string;
	
}

/**
 * Check if a variable contains a valid integer.
 * If so, correct it (intval).
 *
 * @param string $string String to check
 * @returns bool Contains valid integer
 */
function valid_int(&$string) {
	
	if ( $string == strval(intval($string)) ) {
		
		$string = (int) $string;
		
		return true;
		
	} else {
		
		return false;
		
	}
	
}

/**
 * checkdnsrr replacement for Windows
 *
 * @author Zend.com
 * @link http://www.zend.com/codex.php?id=370&single=1
 * @param string $host host
 * @param string $type type
 * @returns bool Contains valid integer
 */
function checkdnsrr_win($host, $type='') {
	
	$types = array(
		'A',
		'MX',
		'NS',
		'SOA',
		'PTR',
		'CNAME',
		'AAAA',
		'A6',
		'SRV',
		'NAPTR',
		'ANY'
	);
	$type = ( !empty($type) && in_array($type, $types) ) ? $type : 'MX';
	
	$output = array();
	exec('nslookup -type='.$type.' '.$host, $output);
	
	$host_len = strlen($host);
	foreach ( $output as $line ) {
		
		if ( !strncasecmp($line, $host, $host_len) )
			return true;
		
	}
	
	return false;
	
}

/**
 * Functions
 *
 * All kinds of functions used everywhere.
 *
 * @author	UseBB Team
 * @link	http://www.usebb.net
 * @license	GPL-2
 * @version	$Revision$
 * @copyright	Copyright (C) 2003-2012 UseBB Team
 * @package	UseBB
 * @subpackage Core
 */
class functions {
	
	/**#@+
	 * @access private
	 */
	var $board_config = array();
	var $board_config_original = array();
	var $board_config_defined = array();
	var $statistics = array();
	var $languages = array();
	var $language_sections = array();
	var $mod_auth;
	var $badwords;
	var $updated_forums;
	var $available = array('templates' => array(), 'languages' => array());
	var $db_tables = array();
	var $server_load;
	var $is_mbstring;
	var $date_format_from_db = FALSE;
	/**#@-*/
	
	/**
	 * @access private
	 */
	function usebb_die($errno, $error, $file, $line) {
		
		global $db, $dbs, $template, $session;
		
		//
		// Ignore the ones we don't want
		//
		if ( ($errno & error_reporting()) == 0 )
			return;
		
		//
		// Ignore certain messages
		//
		foreach ( array(
			// Might be disabled
			'ini_set', 'ini_get', 'exec()',
			// Available since PHP 5.0.0. Removed in PHP 5.3.0
			'ze1_compatibility_mode',
			// Not able to access
			'/proc/loadavg',
			// Unknown languages and such
			'mb_language',
			// Garbage data
			'unserialize'
		) as $ignore_warning ) {
			
			if ( strpos($error, $ignore_warning) !== FALSE )
				return;
			
		}
		
		//
		// Error processing...
		//
		
		$errtypes = array(
			1     => 'E_ERROR',
			2     => 'E_WARNING',
			4     => 'E_PARSE',
			8     => 'E_NOTICE',
			16    => 'E_CORE_ERROR',
			32    => 'E_CORE_WARNING',
			64    => 'E_COMPILE_ERROR',
			128   => 'E_COMPILE_WARNING',
			256   => 'E_USER_ERROR',
			512   => 'E_USER_WARNING',
			1024  => 'E_USER_NOTICE',
			2048  => 'E_STRICT',
			4096  => 'E_RECOVERABLE_ERROR',
			8192  => 'E_DEPRECATED',
			16384 => 'E_USER_DEPRECATED',
		);
		
		if ( !strncmp($error, 'SQL:', 4) ) {
			
			$errtype = 'SQL_ERROR';
			$error = substr($error, 5);
			
		} else {
			
			$errtype = $errtypes[$errno];
			
		}
		
		//
		// Log using PHP's mechanism
		//
		if ( $this->get_config('enable_error_log') ) {
			
			$ip_addr = ( is_object($session) && !empty($session->sess_info['ip_addr']) ) ? $session->sess_info['ip_addr'] : '?';
			error_log('[UseBB Error] '
				.'['.date('Y-m-d H:i:s').'] '
				.'['.$ip_addr.'] '
				.'['.$errtype.' - '.preg_replace('#(?:\s+|\s)#', ' ', $error).'] '
				.'['.$file.':'.$line.']');

		}
		
		//
		// Ignore hidden errors on production env (after being logged).
		//
		if ( USEBB_IS_PROD_ENV && ( ($errno & (USEBB_DEV_ERROR_LEVEL ^ USEBB_PROD_ERROR_LEVEL)) > 0 ) )
			return;
		
		//
		// Filter some sensitive data
		//
		
		//
		// Full script path
		//
		$full_path = substr(dirname(__FILE__), 0, -7);
		$file = str_replace($full_path, '', $file);
		$error = str_replace($full_path, '', $error);
		
		//
		// MySQL username and host for debug levels < extended
		//
		if ( ( !strncmp($error, 'mysql', 5) || $errtype == 'SQL_ERROR' ) && $this->get_config('debug') < DEBUG_EXTENDED )
			$error = preg_replace("#'[^ ]+'?@'?[^ ]+'#", '<em>-filtered-</em>', $error);
		
		$html_msg  = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	<head>
		<title>UseBB General Error</title>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
		<style type="text/css">
			body {
				font-family: sans-serif;
				font-size: 10pt;
			}
			h1 {
				color: #369;
			}
			blockquote {
				width: 55%;
				border-top: 2px solid silver;
				border-bottom: 2px solid silver;
				font-family: monospace;
				font-size: 8pt;
			}
			#error {
				color: #7f0000;
			}
			textarea {
				width: 98%;
				border: 1px solid silver;
				padding: 3px;
			}
		</style>
	</head>
	<body>
		<h1>UseBB General Error</h1>
		<p>An error was encountered. We apologize for any inconvenience.</p>
		<blockquote>
			<p>In file <strong>'.$file.'</strong> on line <strong>'.$line.'</strong>:</p>
			<p id="error"><em>'.$errtype.'</em> - '.nl2br($error).'</p>';
				
		//
		// Show query with extended debug
		//
		if ( $errtype == 'SQL_ERROR' && $this->get_config('debug') == DEBUG_EXTENDED ) {
			
			$used_queries = $db->get_used_queries();
			
			if ( count($used_queries) ) {
				
				$html_msg .= '
			<p>SQL query causing the error:</p><p><textarea rows="10" cols="60" readonly="readonly">'.unhtml(end($used_queries)).'</textarea></p>';
				
			}
			
		}
		
		$html_msg .= '
		</blockquote>';
		
		//
		// Installation note if
		// - config.php does not exist
		// - error "'install' must be removed"
		// - mysql*() error "Access denied for user"
		// - sql error "Table 'x' doesn't exist" or "Access denied for user"
		//
		if ( strpos($error, 'config.php does not exist') !== false 
			|| strpos($error, '\'install\' must be removed') !== false
			|| ( !strncmp($error, 'mysql', 5) && strpos($error, 'Access denied for user') !== false )
			|| ( $errtype == 'SQL_ERROR' && preg_match("#(?:Table '.+' doesn't exist|Access denied for user)#i", $error) ) ) {
			
			$html_msg .= '
		<p><strong>UseBB may not have been installed yet.</strong></p>
		<p>If this is the case and you are the owner of this board, please <a href="docs/index.html">see docs/index.html for <strong>installation instructions</strong></a>.</p>
		<p>Otherwise, please report this error to the owner.</p>';
			
		} else {
			
			$html_msg .= '
		<p>This error should probably not have occured, so please report it to the owner. Thank you for your help.</p>
		<p>If you are the owner of this board and you believe this is a bug, please send a bug report.</p>';
			
		}
		
	$html_msg .= '
	</body>
</html>';
		
		if ( isset($template) )
			ob_end_clean();
		die($html_msg);
		
	}
	
	/**
	 * Get configuration variables
	 *
	 * Rewritten to speed things up and use a cache array at July 8th, 2007.
	 *
	 * @param string $setting Setting to retrieve
	 * @param bool $original Use original config.php configuration
	 * @returns mixed Value of setting
	 */
	function get_config($setting, $original=false) {
		
		global $session;

		//
		// Really early stage where config file is not loaded yet.
		//
		if ( !defined('USEBB_VERSION') )
			return FALSE;
		
		//
		// Load settings into array.
		//
		if ( !count($this->board_config_original) ) {
			
			$this->board_config_original = array_merge($GLOBALS['dbs'], $GLOBALS['conf']);
			$this->board_config_defined = array_keys($this->board_config_original);
			
		}
		
		//
		// users_must_activate was renamed to activation_mode.
		//
		if ( $setting == 'activation_mode' && !isset($this->board_config_original[$setting]) )
			$setting = 'users_must_activate';
		
		//
		// Some missing (newer) settings have default values and are added to original config.
		//
		if ( !isset($this->board_config_original[$setting]) ) {
			
			switch ( $setting ) {
				
				case 'search_limit_results':
				case 'sig_max_length':
					$set_to = 1000;
					break;
				case 'search_nonindex_words_min_length':
				case 'username_min_length':
					$set_to = 3;
					break;
				case 'enable_ip_bans':
				case 'enable_badwords_filter':
				case 'guests_can_see_contact_info':
				case 'show_raw_entities_in_code':
				case 'show_never_activated_members':
				case 'disable_xhtml_header':
				case 'cookie_httponly':
				case 'enable_error_log':
				case 'error_log_log_hidden':
				case 'dnsbl_powered_banning_globally':
					$set_to = true;
					break;
				case 'view_search_min_level':
				case 'view_active_topics_min_level':
					$set_to = LEVEL_GUEST;
					break;
				case 'dnsbl_powered_banning_whitelist':
				case 'dnsbl_powered_banning_servers':
					$set_to = array();
					break;
				case 'username_max_length':
					$set_to = 30;
					break;
				case 'edit_post_timeout':
					$set_to = 900;
					break;
				case 'mass_email_msg_recipients':
					$set_to = 50;
					break;
				case 'acp_auto_logout':
					$set_to = 10;
					break;
				default:
					$set_to = null;
				
			}
			
			if ( isset($set_to) )
				$this->board_config_original[$setting] = $set_to;
			
		}

		//
		// Get original settings when requested.
		// Treat a missing one as "false".
		//
		if ( defined('IS_INSTALLER') || $original )
			return ( isset($this->board_config_original[$setting]) ) ? $this->board_config_original[$setting] : false;
		
		//
		// As of here, settings are altered and no longer "original",
		// e.g. can contain inherited settings from user accounts or be computed.
		//
		// ====================
		//

		//
		// Settings cache for this request.
		//
		if ( isset($this->board_config[$setting]) )
			return $this->board_config[$setting];
		
		//
		// User-based settings.
		//
		if ( is_object($session) && !empty($session->sess_info['user_id']) && isset($session->sess_info['user_info'][$setting]) ) {
			
			switch ( $setting ) {
				
				case 'language':
					$keep_default = ( !in_array($session->sess_info['user_info'][$setting], $this->get_language_packs()) );
					break;
				case 'template':
					$keep_default = ( !in_array($session->sess_info['user_info'][$setting], $this->get_template_sets()) );
					break;
				default:
					$keep_default = false;
				
			}
			
			$this->board_config[$setting] = ( $keep_default ) ? $this->board_config_original[$setting] : $session->sess_info['user_info'][$setting];
			
			if ( !$keep_default && $setting == 'date_format' )
				$this->date_format_from_db = TRUE;
			
			return $this->board_config[$setting];
			
		}
		
		//
		// Auto-detected settings when empty.
		//
		if ( in_array($setting, array('board_url', 'cookie_domain', 'cookie_path')) && empty($this->board_config_original[$setting]) ) {
			
			switch ( $setting ) {
				
				case 'board_url':
					$path_parts = pathinfo($_SERVER['SCRIPT_NAME']);
					if ( ON_WINDOWS )
						$path_parts['dirname'] = str_replace('\\', '/', $path_parts['dirname']);
					if ( substr($path_parts['dirname'], -1) != '/' )
						$path_parts['dirname'] .= '/';
					$protocol = ( isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ) ? 'https' : 'http';
					$set_to = $protocol.'://'.$_SERVER['HTTP_HOST'].$path_parts['dirname'];
					break;
				case 'cookie_domain':
					$set_to = ( !empty($_SERVER['SERVER_NAME']) && preg_match('#^(?:[a-z0-9\-]+\.){1,}[a-z]{2,}$#i', $_SERVER['SERVER_NAME']) ) ? preg_replace('#^www\.#', '.', $_SERVER['SERVER_NAME']) : '';
					break;
				case 'cookie_path':
					$set_to = '/';
				
			}
			
			$this->board_config[$setting] = $set_to;
			return $this->board_config[$setting];
			
		}
		
		//
		// Settings that need validity checking.
		//
		if ( in_array($setting, array('board_url', 'session_name', 'debug')) ) {
			
			$set_to = $this->board_config_original[$setting];
			
			if ( $setting == 'board_url' && substr($set_to, -1) != '/' )
				$set_to .= '/';
			if ( $setting == 'session_name' && ( !preg_match('#^[A-Za-z0-9]+$#', $set_to) || preg_match('#^[0-9]+$#', $set_to) ) )
				$set_to = 'usebb';
			// Only allow extended debug when not in production environment.
			if ( $setting == 'debug' && $set_to == DEBUG_EXTENDED && USEBB_IS_PROD_ENV )
				$set_to = DEBUG_SIMPLE;
			
			$this->board_config[$setting] = $set_to;
			return $this->board_config[$setting];
			
		}
		
		//
		// All other settings taken from the original array.
		// Use false when setting does not exist.
		//
		$this->board_config[$setting] = isset($this->board_config_original[$setting]) ? $this->board_config_original[$setting] : false;

		return $this->board_config[$setting];
		
	}
	
	/**
	 * Get board statistics
	 *
	 * @param string $stat Statistical value to retrieve
	 * @returns mixed Statistical value
	 */
	function get_stats($stat) {
		
		global $db;
		
		//
		// Already requested, return
		//
		if ( isset($this->statistics[$stat]) )
			return $this->statistics[$stat];
		
		//
		// Get requested value
		//
		switch ( $stat ) {
			
			case 'categories':
				$result = $db->query("SELECT COUNT(id) AS count FROM ".TABLE_PREFIX."cats");
				$out = $db->fetch_result($result);
				$this->statistics[$stat] = $out['count'];
				break;
			
			case 'forums':
				$result = $db->query("SELECT COUNT(id) AS count FROM ".TABLE_PREFIX."forums");
				$out = $db->fetch_result($result);
				$this->statistics[$stat] = $out['count'];
				break;

			case 'viewable_forums':
				$result = $db->query("SELECT id, auth FROM ".TABLE_PREFIX."forums");
				$this->statistics[$stat] = 0;
				
				while ( $forumdata = $db->fetch_result($result) ) {
					
					if ( $this->auth($forumdata['auth'], 'view', $forumdata['id']) )
						$this->statistics[$stat]++;
					
				}
				break;
			
			case 'latest_member':
				$never_activated_sql = ( $this->get_config('show_never_activated_members') ) ? "" : " WHERE ( active <> 0 OR last_login <> 0 )";
				$result = $db->query("SELECT id, displayed_name, regdate FROM ".TABLE_PREFIX."members".$never_activated_sql." ORDER BY id DESC LIMIT 1");
				$this->statistics[$stat] = $db->fetch_result($result);
				break;
			
			default:
				$result = $db->query("SELECT name, content FROM ".TABLE_PREFIX."stats");
				while ( $out = $db->fetch_result($result) )
					$this->statistics[$out['name']] = $out['content'];
			
		}
		
		if ( isset($this->statistics[$stat]) )
			return $this->statistics[$stat];
		else
			trigger_error('The statistic variable "'.$stat.'" does not exist!', E_USER_ERROR);
		
	}
	
	/**
	 * Set board statistics
	 *
	 * @param string $stat Statistical value to set
	 * @param mixed $value New value
	 * @param bool $add Add to current value or not
	 */
	function set_stats($stat, $value, $add=false) {
		
		global $db;
		
		if ( $add )
			$value = $this->get_stats($stat) + $value;

		$db->query("UPDATE ".TABLE_PREFIX."stats SET content = '".$value."' WHERE name = '".$stat."'");
		$this->statistics[$stat] = $value;

	}

	/**
	 * Friendly URL builder
	 *
	 * @param string $filename base filename to link to
	 * @param array $vars GET variabeles
	 * @returns string URL
	 */
	function _make_friendly_url($filename, $vars) {
		
		if ( $filename == 'index' && count($vars) == 0 )
			return './';

		$url = $filename;
		$keyed = array('forum', 'topic', 'post', 'quotepost', 'al');

		foreach ( $vars as $key => $val )
			$url .= '-' . urlencode(( in_array($key, $keyed) ) ? $key . $val : $val);

		$url .= ( $filename == 'rss' ) ? '.xml' : '.html';

		return $url;

	}

	/**
	 * Interactive URL builder
	 *
	 * @param string $filename .php filename to link to
	 * @param array $vars GET variabeles
	 * @param bool $html Return HTML URL
	 * @param bool $enable_sid Enable session ID's
	 * @param bool $force_php Force linking to .php files
	 * @param bool $enable_token Enable token (forces .php link)
	 * @returns string URL
	 */
	function make_url($filename, $vars=array(), $html=true, $enable_sid=true, $force_php=false, $enable_token=false) {
		
		global $session;
		
		//
		// Base name
		//
		$filename = basename($filename, '.php');

		//
		// Don't keep session key variable
		//
		if ( is_array($vars) )
			unset($vars[$this->get_config('session_name').'_sid']);
		else
			$vars = array();

		//
		// No session IDs for search engines
		//
		$enable_sid = ( $enable_sid && !$session->is_search_engine() );

		//
		// No friendly URLs for tokenized URLs, admin, installer and activation links
		//
		$force_php = ( $force_php || $enable_token || $filename == 'admin' || defined('IS_INSTALLER') || ( $filename == 'panel' && isset($vars['act']) && $vars['act'] == 'activate' ) );

		//
		// Friendly URLs
		//
		if ( !$force_php && $this->get_config('friendly_urls') )
			return $this->_make_friendly_url($filename, $vars);

		//
		// Build URL
		//

		$url = $filename . '.php';

		//
		// Add session variable when needed
		//
		$SID = SID;
		if ( !empty($SID) && $enable_sid && ( !$html || !ini_get('session.use_trans_sid') ) ) {
			
			$SID_parts = explode('=', $SID, 2);
			$vars[$SID_parts[0]] = $SID_parts[1];

		}

		//
		// Add token
		//
		if ( $enable_token )
			$vars['_url_token_'] = $this->generate_token();

		if ( count($vars) == 0 )
			return $url;

		$url .= '?';
		$delim = ( $html ) ? '&amp;' : '&';

		foreach ( $vars as $key => $val )
			$url .= urlencode($key) . '=' . urlencode($val) . $delim;

		return substr($url, 0, - strlen($delim));
		
	}
	
	/**
	 * Attaches a SID to URLs which should contain one (e.g. referer URLs)
	 *
	 * @param string $url URL
	 * @returns string URL
	 */
	function attach_sid($url) {
		
		$SID = SID;
		
		if ( empty($SID) || $this->get_config('friendly_urls') || preg_match('/'.preg_quote($SID, '/').'$/', $url) )
			return $url;
		
		if ( strpos($url, '?') !== false )
			return $url . '&' . $SID;
		
		return $url . '?' . $SID;

	}
	
	/**
	 * Fetch a language file
	 *
	 * @param string $language Language name (default language is used when missing)
	 * @param string $section Section name (main section is used when missing)
	 * @returns array Language variables
	 */
	function fetch_language($language='', $section='') {
		
		$language = ( !empty($language) && in_array($language, $this->get_language_packs()) ) ? $language : $this->get_config('language');
		$section = ( !empty($section) ) ? $section : 'lang';
		
		if ( !isset($this->language_sections[$language]) || !in_array($section, $this->language_sections[$language]) ) {
			
			//
			// Not loaded yet
			//

			if ( $section != 'lang' ) {
				
				//
				// Add to current $lang
				//
				$lang = $GLOBALS['lang'];

				if ( !file_exists(ROOT_PATH.'languages/'.$section.'_'.$language.'.php') ) {
					
					//
					// Fallback to English
					//
					if ( $language != 'English' && in_array('English', $this->get_language_packs()) )
						require(ROOT_PATH.'languages/'.$section.'_English.php');
					else
						trigger_error('Section "'.$section.'" for language pack "'.$language.'" could not be found. No English fallback was available. Please use an updated language pack or also upload the English one.', E_USER_ERROR);
					
				} else {
					
					require(ROOT_PATH.'languages/'.$section.'_'.$language.'.php');
					
					//
					// Merge with English for missing strings
					//
					if ( $language != 'English' && in_array('English', $this->get_language_packs()) )
						$lang = array_merge($this->fetch_language('English', $section), $lang);
					
				}
				
			} else {
				
				require(ROOT_PATH.'languages/'.$section.'_'.$language.'.php');
				
				//
				// Merge with English for missing strings
				//
				if ( $language != 'English' && in_array('English', $this->get_language_packs()) )
					$lang = array_merge($this->fetch_language('English', $section), $lang);
				
				if ( empty($lang['character_encoding']) )
					$lang['character_encoding'] = 'iso-8859-1';
				
				//
				// UTF-8 patching
				//
				if ( function_exists('mb_internal_encoding') ) {
					
					// Setting mbstring
					$mb_internal_encoding = ( $lang['character_encoding'] == 'iso-8859-8-i' ) ? 'iso-8859-8' : $lang['character_encoding'];

					$is_mb_language = mb_language($language);
					$is_mb_internal_encoding = mb_internal_encoding($mb_internal_encoding);
					
					if ( $is_mb_language !== FALSE || $is_mb_internal_encoding !== FALSE ) {
						
						$this->is_mbstring = TRUE;
						
					} else {
						 
						// mbstring can not be used, reset
						mb_language('neutral');
						mb_internal_encoding('ISO-8859-1');
						
					}

					// Reset other parameters
					ini_set('mbstring.http_input', 'pass');
					ini_set('mbstring.http_output', 'pass');
					ini_set('mbstring.func_overload', 0);
					ini_set('mbstring.substitute_character', 'none');
				}
				
			}
			
			$this->languages[$language] = $lang;
		}
		
		if ( !isset($this->language_sections[$language]) )
			$this->language_sections[$language] = array();
		$this->language_sections[$language][] = $section;
		
		$returned = &$this->languages[$language];
		return $returned;
		
	}
	
	/**
	 * Kick a user to the login form
	 */
	function redir_to_login() {
		
		global $session, $template, $lang;
		
		if ( !$session->sess_info['user_id'] ) {
			
			$_SESSION['referer'] = $_SERVER['REQUEST_URI'];
			$this->redirect('panel.php', array('act' => 'login'));
			
		} else {
			
			header(HEADER_403);
			$template->clear_breadcrumbs();
			$template->add_breadcrumb($lang['Note']);
			$template->parse('msgbox', 'global', array(
				'box_title' => $lang['Note'],
				'content' => $lang['NotPermitted']
			));
			
		}
		
	}
	
	/**
	 * Generate a date given a timestamp
	 *
	 * @param int $stamp Unix timestamp
	 * @param string $format Date format syntax (identical to PHP's date() - default is used when missing)
	 * @param bool $keep_gmt Use GMT and no time zones
	 * @param bool $translate Localize dates
	 * @returns string Date
	 */
	function make_date($stamp, $format='', $keep_gmt=false, $translate=true) {
		
		global $lang;
		
		$format = ( !empty($format) ) ? $format : strip_tags($this->get_config('date_format'));
		
		if ( $this->date_format_from_db )
			$format = stripslashes($format);
		
		if ( $keep_gmt )
			$date = gmdate($format, $stamp);
		else
			$date = gmdate($format, $stamp + (3600 * $this->get_config('timezone')) + (3600 * $this->get_config('dst')));
		
		if ( $translate && isset($lang['date_translations']) && is_array($lang['date_translations']) )
			$date = ucfirst(strtr($date, $lang['date_translations']));
		
		return $date;
		
	}
	
	/**
	 * Generate a time past string
	 *
	 * @param int $timestamp Unix timestamp
	 * @param int $until Calculate time past until this Unix timestamp (current is used when missing)
	 * @returns string Time past
	 */
	function time_past($timestamp, $until=null) {
	
		global $lang;
	
		$seconds = ( ( is_int($until) ) ? $until : time() ) - $timestamp;
	
		$times = array();
		$sections = array(
			'weeks' => 604800,
			'days' => 86400,
			'hours' => 3600,
			'minutes' => 60,
			'seconds' => 1
		);
	
		foreach( $sections as $what => $length ) {
			
			if ( $seconds >= $length ) {
				
				$times[$what] = ( $length >0 ) ? floor($seconds / $length) : $length;
				$seconds %= $length;
				
			}
			
		}
	
		$sections = array();
		foreach ( $times as $key => $val )
			$sections[] = $val.' '.$lang[ucfirst($key)];
	
		return array($times, join(', ', $sections));
	
	}
	
	/**
	 * Generate an e-mail link/text
	 *
	 * @param array $user User information containing id, email and email_show
	 * @returns string HTML
	 */
	function show_email($user) {
		
		global $session, $lang;
		
		//
		// Possible email_view_level values:
		// - 0: Hide all
		// - 1: Use mail form
		// - 2: Show spam proof
		// - 3: Show raw
		//
		
		$email_view_level = $this->get_config('email_view_level');
		
		if ( $this->get_user_level() >= $this->get_config('view_hidden_email_addresses_min_level') ) {
			
			//
			// This user may view hidden e-mail addresses
			//
			$return = '<a href="mailto:'.$user['email'].'">'.$user['email'].'</a>';
			if ( $email_view_level == 1 )
				$return = '<a href="'.$this->make_url('mail.php', array('id' => $user['id'])).'">'.$lang['SendMessage'].'</a> ('.$return.')';
			
		} else {
			
			if ( $email_view_level == 0 || ( !$user['email_show'] && $user['id'] != $session->sess_info['user_id'] ) ) {
				
				//
				// E-mail addresses are hidden or the user has chosen to keep it hidden
				//
				$return = $lang['Hidden'];
				
			} else {
				
				switch ( $email_view_level ) {
					
					case 1:
						$return = '<a href="'.$this->make_url('mail.php', array('id' => $user['id'])).'">'.$lang['SendMessage'].'</a>';
						break;
					case 2:
						$user['email'] = $this->string_to_entities($user['email']);
						// No break here, since we just want to convert $user['email']
					default:
						$return = '<a href="mailto:'.$user['email'].'">'.$user['email'].'</a>';
					
				}
				
			}
			
		}
		
		return $return;
		
	}
	
	/**
	 * Translate an ASCII string to HTML entities
	 *
	 * This function only works for ASCII characters, nothing else.
	 *
	 * @param string $string String to convert
	 * @returns string Converted string
	 */
	function string_to_entities($string) {
		
		$length = strlen($string);
		$new_string = '';
		for ( $i = 0; $i < $length; $i++ )
			$new_string .= '&#'.ord(substr($string, $i, $i+1)).';';
		
		return $new_string;
		
	}

	/**
	 * Generate a random key
	 *
	 * @param bool $is_password Is the random key used as a password?
	 * @returns string Random key
	 */
	function random_key($is_password=false) {
		
		if ( !$is_password )
			return md5(mt_rand());
		
		$chars = range(33, 126); // ! until ~
		$max = count($chars) - 1;

		$passwd_min_length = (int) $this->get_config('passwd_min_length');
		$length = ( $passwd_min_length > 10 ) ? $passwd_min_length : 10;
		
		do {
		
			$key = '';
			for ( $i = 0; $i < $length; $i++ )
				$key .= chr($chars[mt_rand(0, $max)]);

			$valid = $this->validate_password($key, true);

		} while ( !$valid );
		
		return $key;
		
	}
	
	/**
	 * Send an email
	 *
	 * Why don't they just send me an e-mail? -- Belgian ad for coffee
	 *
	 * @param string $subject Subject of e-mail
	 * @param string $rawbody Body of e-mail
	 * @param array $bodyvars Variables for e-mail body
	 * @param string $from_name Name of sender
	 * @param string $from_email E-mail of sender
	 * @param string $to E-mail of recipient
	 * @param string $bcc_email E-mail of BCC recipient (no BCC when missing)
	 * @param string $language Language name the e-mail is in (default language when missing)
	 * @param string $charset Character set the e-mail is in (default charset when missing)
	 */
	function usebb_mail($subject, $rawbody, $bodyvars=array(), $from_name, $from_email, $to, $bcc_email='', $language='', $charset='') {
		
		global $lang;
		
		$bodyvars = ( is_array($bodyvars) ) ? $bodyvars : array();
		
		$is_enable_mbstring = ( function_exists('mb_language') && mb_language() != 'neutral' );

		//
		// Eventually use the right language and character encoding which may be passed
		// in the parameters when another language is used (e.g. subscription notices)
		//
		$language = ( !empty($language) ) ? $language : $this->get_config('language');
		$charset = ( !empty($charset) ) ? $charset : $lang['character_encoding'];
		
		//
		// Set the correct mb_language when neccessary (when mbstring enabled)
		//
		$is_mbstring = FALSE;
		if ( $this->is_mbstring ) {
			
			$backup_mb_language = mb_language();
			$backup_mb_internal_encoding = mb_internal_encoding();
			
			if ( @mb_language($language) !== FALSE && @mb_internal_encoding($charset) !== FALSE )
				$is_mbstring = TRUE;
			
		}
		
		$body = str_replace(array("\r\n", "\r"), "\n", $rawbody);
		
		//
		// Windows: \r\n; other: \n
		//
		$cr = ( ON_WINDOWS ) ? "\r\n" : "\n";
		$body = str_replace("\n", $cr, $rawbody);
		
		$bodyvars['board_name'] = $this->get_config('board_name');
		$bodyvars['board_link'] = $this->get_config('board_url');
		$bodyvars['admin_email'] = $this->get_config('admin_email');
		
		foreach ( $bodyvars as $key => $val )
			$body = str_replace('['.$key.']', $val, $body);
		
		$headers = array();
		
		if ( $is_mbstring && function_exists('mb_encode_mimeheader') ) {
			
			$from_name = mb_encode_mimeheader($from_name);
			
		} else {
			
			if ( strtolower($charset) == 'utf-8' ) {

				$subject = '=?'.$charset.'?B?'.base64_encode($subject).'?=';
				$from_name = '=?'.$charset.'?B?'.base64_encode($from_name).'?=';

			}
			
		}
		
		if ( !empty($bcc_email) )
			$headers[] = 'Bcc: '.$bcc_email;
		$headers[] = 'Date: '.date('r');
		$headers[] = 'Message-Id: '.sprintf("<%s.%s>", substr(md5(time()), 4, 10), $from_email);
		$headers[] = 'X-Mailer: UseBB';
		
		//
		// Fix for hosts that require From to be a domain name hosted on the same host
		// So, instead we can use a Reply-To header to contain the sender email
		//
		if ( $from_email != $this->get_config('admin_email') && $this->get_config('email_reply-to_header') ) {
			
			$headers[] = 'From: "'.$from_name.'" <'.$this->get_config('admin_email').'>';
			$headers[] = 'Reply-To: '.$from_email;
			
		} else {
			
			$headers[] = 'From: "'.$from_name.'" <'.$from_email.'>';
			
		}
		
		// TODO safe mode to be removed in PHP 5.4
		$is_safe_mode = in_array(strtolower(ini_get('safe_mode')), array('1', 'on'));

		if ( $is_mbstring && function_exists('mb_send_mail') ) {

			$mail_func = 'mb_send_mail';

		} else {
			
			$mail_func = 'mail';
			$headers[] = 'MIME-Version: 1.0';
			$headers[] = 'Content-Type: text/plain; charset='.$charset;
			if ( preg_match('/^(iso-8859-|iso-2022-)/i', $charset))
				$headers[] = 'Content-Transfer-Encoding: 7bit';
			else
				$headers[] = 'Content-Transfer-Encoding: 8bit';
			
		}

		if ( $is_safe_mode || !$this->get_config('sendmail_sender_parameter') )
			$mail_result = $mail_func($to, $subject, $body, join($cr, $headers));
		else
			$mail_result = $mail_func($to, $subject, $body, join($cr, $headers), '-f'.$from_email);

		if ( !$mail_result )
			trigger_error('Unable to send e-mail!', E_USER_ERROR);
		
		//
		// Restored language and character encoding.
		//
		if ( $this->is_mbstring ) {
			
			mb_language($backup_mb_language);
			mb_internal_encoding($backup_mb_internal_encoding);
			
		}
		
	}
	
	/**
	 * Set the remember cookie
	 *
	 * @param int $user_id User ID
	 * @param string $passwd_hash Password hash
	 */
	function set_al($user_id, $passwd_hash) {
		
		$content = array(
			intval($user_id),
			$passwd_hash
		);
		$this->setcookie($this->get_config('session_name').'_al', serialize($content), time()+31536000);
		
	}
	
	/**
	 * Unset the remember cookie
	 */
	function unset_al() {
		
		$this->setcookie($this->get_config('session_name').'_al', '');
		
	}
	
	/**
	 * Is the remember cookie set?
	 *
	 * @returns bool Remember cookie set
	 */
	function isset_al() {
		
		if ( !empty($_COOKIE[$this->get_config('session_name').'_al']) )
			return true;
		else
			return false;
		
	}
	
	/**
	 * Get the remember cookie's value
	 *
	 * @returns mixed Array with user ID and password hash -or- false when not set
	 */
	function get_al() {
		
		if ( $this->isset_al() ) {
			
			$content = stripslashes($_COOKIE[$this->get_config('session_name').'_al']);
			if ( substr($content, 0, 1) == 'a' )
				return unserialize($content);
			else
				return explode(':', $content, 2);
			
		} else {
			
			return false;
			
		}
		
	}
	
	/**
	 * Get the user's level
	 *
	 * @returns int User level
	 */
	function get_user_level() {
		
		global $session;
		
		if ( !isset($session->sess_info['user_id']) )
			trigger_error('You first need to call $session->update() before you can get any session info.', E_USER_ERROR);
		
		if ( $session->sess_info['user_id'] )
			return $session->sess_info['user_info']['level'];
		else
			return LEVEL_GUEST;
		
	}
	
	/**
	 * Authorization function
	 *
	 * Defines whether a user has permission to take a certain action.
	 *
	 * @param string $auth_int Authorization "integer" (string because of leading zeroes)
	 * @param string $action Action to establish
	 * @param int $forum_id ID of forum
	 * @param bool $self For own account
	 * @param array $alternative_user_info When not for own account, array with user information
	 * @returns bool Allowed
	 */
	function auth($auth_int, $action, $forum_id, $self=true, $alternative_user_info=null) {
		
		global $session, $db;
		
		if ( $self )
			$user_info = ( $session->sess_info['user_id'] ) ? $session->sess_info['user_info'] : array('id' => LEVEL_GUEST, 'level' => LEVEL_GUEST);
		else
			$user_info = $alternative_user_info;
		
		if ( ( $self && $session->sess_info['ip_banned'] ) || ( $this->get_config('board_closed') && $user_info['level'] < LEVEL_ADMIN ) )
			return false;
		
		//
		// Define the user level
		//
		if ( $user_info['id'] ) {
			
			//
			// Logged in user
			//
			if ( $user_info['level'] == LEVEL_MOD ) {
				
				if ( !is_array($this->mod_auth) ) {
					
					$result = $db->query("SELECT forum_id FROM ".TABLE_PREFIX."moderators WHERE user_id = ".$user_info['id']);
					$this->mod_auth = array();
					while ( $out = $db->fetch_result($result) )
						$this->mod_auth[] = intval($out['forum_id']);
					
				}
				
				$userlevel = ( in_array($forum_id, $this->mod_auth) ) ? LEVEL_MOD : LEVEL_MEMBER;
				
			} else {
				
				$userlevel = $user_info['level'];
				
			}
			
		} else {
			
			//
			// Guest
			//
			if ( !$this->get_config('guests_can_access_board') )
				return false;
			else
				$userlevel = LEVEL_GUEST;
			
		}
		
		//
		// Get the part of the auth integer that
		// corresponds with the action given
		//
		$actions = array(
			'view' => 0,
			'read' => 1,
			'post' => 2,
			'reply' => 3,
			'edit' => 4,
			'move' => 5,
			'delete' => 6,
			'lock' => 7,
			'sticky' => 8,
			'html' => 9
		);
		$min_level = intval($auth_int[$actions[$action]]);
		
		//
		// If the user level is equal or greater than the
		// auth integer, return a true, otherwise return a false.
		//
		if ( $userlevel >= $min_level )
			return true;
		else
			return false;
		
	}
	
	/**
	 * Return a list of moderators, clickable and separated with commas
	 *
	 * @param int $forum Forum ID
	 * @param array $listarray Array with all moderators (automatically requested when missing)
	 * @returns string Moderator list
	 */
	function get_mods_list($forum, $listarray=false) {
		
		global $db, $lang;
		
		$forum_moderators = array();
		
		if ( is_array($listarray) && count($listarray) ) {
			
			foreach ( $listarray as $modsdata ) {
				
				if ( $modsdata['forum_id'] == $forum )
					$forum_moderators[] = $this->make_profile_link($modsdata['id'], $modsdata['displayed_name'], $modsdata['level']);
				
			}
			
			if ( !count($forum_moderators) ) {
				
				return $lang['Nobody'];
				
			}
			
		} else {
			
			$result = $db->query("SELECT u.id, u.displayed_name, u.level FROM ".TABLE_PREFIX."members u, ".TABLE_PREFIX."moderators m WHERE m.forum_id = ".$forum." AND m.user_id = u.id ORDER BY u.displayed_name");
			while ( $modsdata = $db->fetch_result($result) )
				$forum_moderators[] = $this->make_profile_link($modsdata['id'], $modsdata['displayed_name'], $modsdata['level']);
				
			if ( !count($forum_moderators) ) {
				
				return $lang['Nobody'];
				
			}
			
		}
		
		//
		// Join all values in the array
		//
		return join(', ', $forum_moderators);
		
	}
	
	/**
	 * Return a clickable list of pages
	 *
	 * @param int $pages_number Total number of pages
	 * @param int $current_page Current page
	 * @param int $items_number Number of items
	 * @param int $items_per_page Items per page
	 * @param string $page_name .php page name
	 * @param int $page_id_val URL id GET value
	 * @param bool $back_forward_links Enable back and forward links
	 * @param array $url_vars Other URL vars
	 * @param bool $force_php Force linking to .php files
	 * @returns string HTML
	 */
	function make_page_links($pages_number, $current_page, $items_number, $items_per_page, $page_name, $page_id_val=NULL, $back_forward_links=true, $url_vars=array(), $force_php=false) {
		
		global $lang;
		
		if ( intval($items_number) > intval($items_per_page) ) {
			
			$page_links = array();
			$page_links_groups_length = 4;
			
			if ( !$current_page ) {
				
				$current_page = $pages_number+1;
				$page_links_groups_length++;
				
			}
			
			for ( $i = 1; $i <= $pages_number; $i++ ) {
				
				if ( $current_page != $i ) {
					
					if ( $i+$page_links_groups_length >= $current_page && $i-$page_links_groups_length <= $current_page ) {
						
						if ( valid_int($page_id_val) )
							$url_vars['id'] = $page_id_val;
						$url_vars['page'] = $i;
						$page_links[] = '<a href="'.$this->make_url($page_name, $url_vars, true, true, $force_php).'">'.$i.'</a>';
						
					} else {
						
						if ( end($page_links) != '...' )
							$page_links[] = '...';
						
					}
					
				} else {
					
					$page_links[] = '<strong>'.$i.'</strong>';
					
				}
				
			}
			
			$page_links = join(' ', $page_links);
			
			if ( $back_forward_links ) {
				
				if ( valid_int($page_id_val) )
					$url_vars['id'] = $page_id_val;
				
				if ( $current_page > 1 ) {
					
					$url_vars['page'] = $current_page-1;
					$page_links = '<a href="'.$this->make_url($page_name, $url_vars, true, true, $force_php).'">&lt;</a> '.$page_links;
					
				}
				if ( $current_page < $pages_number ) {
					
					$url_vars['page'] = $current_page+1;
					$page_links .= ' <a href="'.$this->make_url($page_name, $url_vars, true, true, $force_php).'">&gt;</a>';
					
				}
				if ( $current_page > 2 ) {
					
					$url_vars['page'] = 1;
					$page_links = '<a href="'.$this->make_url($page_name, $url_vars, true, true, $force_php).'">&laquo;</a> '.$page_links;
					
				}
				if ( $current_page+1 < $pages_number ) {
					
					$url_vars['page'] = $pages_number;
					$page_links .= ' <a href="'.$this->make_url($page_name, $url_vars, true, true, $force_php).'">&raquo;</a>';
					
				}
				
			}
			
			$page_links = sprintf($lang['PageLinks'], $page_links);
			
		} else {
			
			$page_links = sprintf($lang['PageLinks'], '1');
			
		}
		
		return $page_links;
		
	}
	
	/**
	 * Removes BBCode
	 *
	 * @param string $string Text string to clean
	 * @returns string Cleaned text
	 */
	function bbcode_clear($string) {
		
		$existing_tags = array('code', 'b', 'i', 'u', 's', 'img', 'url', 'mailto', 'color', 'size', 'google', 'quote');
		return preg_replace('#\[/?(?:'.join('|', $existing_tags).')(?:=[^\]]*)?\]#i', '', $string);
		
	}
	
	/**
	 * Check if a post is empty
	 *
	 * Checks if the post is empty, with and without BBCode
	 *
	 * @param string $string Text
	 * @returns bool Is empty
	 */
	function post_empty(&$string) {
		
		if ( empty($string) || is_array($string) )
			return true;
		
		$copy = $string;
		$copy = $this->bbcode_clear($copy);
		
		if ( empty($copy) )
			return true;
		
		return false;
		
	}
	
	/**
	 * Cleans up BBCode for parsing
	 *
	 * Automatically called from within ::markup.
	 *
	 * @param string $string Text string to preparse
	 * @returns string Corrected BBCoded text
	 */
	function bbcode_prepare($string) {
		
		$string = trim($string);
		$existing_tags = array('code', 'b', 'i', 'u', 's', 'img', 'url', 'mailto', 'color', 'size', 'google', 'quote');
		
		//
		// BBCode tags start with an alphabetic character, eventually followed by non [ and ] characters.
		//
		$parts = array_reverse(preg_split('#(\[/?[a-z][^\[\]]*\])#i', $string, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY));
		
		$open_tags = $open_parameters = array();
		$new_string = '';
		
		while ( count($parts) ) {
			
			$part = array_pop($parts);
			$matches = array();
			
			//
			// Add open tag
			//
			if ( preg_match('#^\[([a-z]+)(=[^\]]*)?\]$#i', $part, $matches) ) {
				
				$matches[1] = strtolower($matches[1]);
				
				//
				// Transform tags
				//
				if ( end($open_tags) == 'code' ) {
					
					$new_string .= str_replace(array('[', ']'), array('&#91;', '&#93;'), $part);
					continue;
					
				}
				
				//
				// Is already open
				//
				if ( $matches[1] != 'quote' && in_array($matches[1], $open_tags) )
					continue;
				
				//
				// Only add this if it exists
				//
				if ( in_array($matches[1], $existing_tags) ) {
					
					array_push($open_tags, $matches[1]);
					array_push($open_parameters, ( isset($matches[2]) ) ? $matches[2] : '');
					
				}
				
				$new_string .= $part;
				continue;
				
			}
			
			//
			// Add close tag
			//
			if ( preg_match('#^\[/([a-z]+)\]$#i', $part, $matches) ) {
				
				$matches[1] = strtolower($matches[1]);
				
				//
				// Transform tags
				//
				if ( end($open_tags) == 'code' && $matches[1] != 'code' ) {
					
					$new_string .= str_replace(array('[', ']'), array('&#91;', '&#93;'), $part);
					continue;
					
				}
				
				//
				// Unexisting tag
				//
				if ( !in_array($matches[1], $existing_tags) ) {
					
					$new_string .= $part;
					continue;
					
				}
				
				//
				// Is current open tag
				//
				if ( end($open_tags) == $matches[1] ) {
					
					array_pop($open_tags);
					array_pop($open_parameters);
					
					$new_string .= $part;
					continue;
					
				}
				
				//
				// Is other open tag
				//
				if ( in_array($matches[1], $open_tags) ) {
					
					$to_reopen_tags = $to_reopen_parameters = array();
					
					while ( $open_tag = array_pop($open_tags) ) {
						
						$open_parameter = array_pop($open_parameters);
						$new_string .= '[/'.$open_tag.']';
						
						if ( $open_tag == $matches[1] )
							break;
						
						array_push($to_reopen_tags, $open_tag);
						array_push($to_reopen_parameters, $open_parameter);
						
					}
					
					$to_reopen_tags = array_reverse($to_reopen_tags);
					$to_reopen_parameters = array_reverse($to_reopen_parameters);
					
					while ( $open_tag = array_pop($to_reopen_tags) ) {
						
						$open_parameter = array_pop($to_reopen_parameters);
						
						$new_string .= '['.$open_tag.$open_parameter.']';
						array_push($open_tags, $open_tag);
						array_push($open_parameters, $open_parameter);
						
					}
					
				}
				
			} else {
				
				//
				// Plain text
				//
				$new_string .= ( end($open_tags) == 'code' && $this->get_config('show_raw_entities_in_code') ) ? str_replace('&#', '&amp;#', $part) : $part;
				
			}
			
		}
		
		//
		// Close opened tags
		//
		while ( $open_tag = array_pop($open_tags) ) {
			
			$open_parameter = array_pop($open_parameters);
			$new_string .= '[/'.$open_tag.$open_parameter.']';
			
		}
		
		//
		// Remove empties
		//
		foreach ( $existing_tags as $existing_tag )
			$new_string = preg_replace('#\[('.$existing_tag.')([^\]]+)?\]\[/(\1)\]#i', '', $new_string);
		
		return $new_string;
		
	}
	
	/**
	 * Apply BBCode and smilies to a string
	 *
	 * @param string $string String to markup
	 * @param bool $bbcode Enable BBCode
	 * @param bool $smilies Enable smilies
	 * @param bool $html Enable HTML
	 * @param bool $rss_mode Enable RSS mode
	 * @param bool $links Enable links parsing
	 * @returns string HTML
	 */
	function markup($string, $bbcode=true, $smilies=true, $html=false, $rss_mode=false, $links=true) {
		
		global $db, $template, $lang;
		static $random;
		
		$string = preg_replace('#(script|about|applet|activex|chrome):#is', '\\1&#058;', $string);
		
		//
		// Needed by some BBCode regexps and smilies
		//
		$string = ' '.$string.' ';
		
		if ( !$html )
			$string = unhtml($string, $rss_mode);
		
		if ( $smilies ) {
			
			$all_smilies = $template->get_config('smilies');
			krsort($all_smilies);
			$full_path = ( $rss_mode ) ? $this->get_config('board_url') : ROOT_PATH;
			
			foreach ( $all_smilies as $pattern => $img )
				$string = preg_replace('#([^"])('.preg_quote(unhtml($pattern), '#').')#', '\\1<img src="'.$full_path.'templates/'.$this->get_config('template').'/smilies/'.$img.'" alt="'.unhtml($pattern).'" />', $string);
			
			//
			// Entity + smiley fix
			//
			$string = preg_replace('#(&\#?[a-zA-Z0-9]+)<img src="[^"]+" alt="([^"]+)" />#', '\\1\\2', $string);
			
		}
		
		if ( $bbcode ) {
			
			$string = ' '.$this->bbcode_prepare($string).' ';
			
			$rel = array();
			if ( $this->get_config('target_blank') )
				$rel[] = 'external';
			if ( $this->get_config('rel_nofollow') )
				$rel[] = 'nofollow';
			$rel = ( count($rel) ) ? ' rel="'.join(' ', $rel).'"' : '';
			
			//
 			// Protect from infinite loops.
 			// The while loop to parse nested quote tags has the sad side-effect of entering an infinite loop
 			// when the parsed text contains $0 or \0.
 			// Admittedly, this is a quick and dirty fix. For a nice "fix" I refer to the stack based parser in 2.0.
 			//
 			if ( $random == NULL )
 				$random = $this->random_key();
 			
 			$string = str_replace(array('$', "\\"), array('&#36;'.$random, '&#92;'.$random), $string);
			
			//
			// Parse quote tags
			//
			// Might seem a bit difficultly done, but trimming doesn't work the usual way
			//
			while ( preg_match("#\[quote\](.*?)\[/quote\]#is", $string, $matches) ) {

				$string = preg_replace("#\[quote\]".preg_quote($matches[1], '#')."\[/quote\]#is", sprintf($template->get_config('quote_format'), $lang['Quote'], ' '.trim($matches[1])).' ', $string);
				unset($matches);

			}
			while ( preg_match("#\[quote=(.*?)\](.*?)\[/quote\]#is", $string, $matches) ) {

				$string = preg_replace("#\[quote=".preg_quote($matches[1], '#')."\]".preg_quote($matches[2], '#')."\[/quote\]#is", sprintf($template->get_config('quote_format'), sprintf($lang['Wrote'], $matches[1]), ' '.trim($matches[2]).' '), $string);
				unset($matches);

			}
			
			//
			// Undo the dirty fixing.
 			//
 			$string = str_replace(array('&#36;'.$random, '&#92;'.$random), array('$', "\\"), $string);
			
			//
			// Parse code tags
			//
			preg_match_all("#\[code\](.*?)\[/code\]#is", $string, $matches);				
			foreach ( $matches[1] as $oldpart ) {
				
				$newpart = preg_replace(array('#<img src="[^"]+" alt="([^"]+)" />#', "#\n#", "#\r#"), array('\\1', '<br />', ''), $oldpart); // replace smiley image tags
				$string = str_replace('[code]'.$oldpart.'[/code]', '[code]'.$newpart.'[/code]', $string);
				
			}
			$string = preg_replace("#\[code\](.*?)\[/code\]#is", sprintf($template->get_config('code_format'), '\\1'), $string);
			
			//
			// Parse URL's and e-mail addresses enclosed in special characters
			//
			if ( $links ) {

				$ignore_chars = "([^a-z0-9/]|&\#?[a-z0-9]+;)*?";
				for ( $i = 0; $i < 2; $i++ ) {

					$string = preg_replace(array(
						"#([\s]".$ignore_chars.")([\w]+?://[\w\#\$%&~/\.\-;:=,\?@\[\]\+\\\\\'!\(\)\*]*?)(".$ignore_chars."[\s])#is",
						"#([\s]".$ignore_chars.")(www\.[\w\#\$%&~/\.\-;:=,\?@\[\]\+\\\\\'!\(\)\*]*?)(".$ignore_chars."[\s])#is",
						"#([\s]".$ignore_chars.")([a-z0-9&\-_\.\+]+?@[\w\-]+\.([\w\-\.]+\.)?[\w]+)(".$ignore_chars."[\s])#is"
					), array(
						'\\1<a href="\\3" title="\\3"'.$rel.'>\\3</a>\\4',
						'\\1<a href="http://\\3" title="http://\\3"'.$rel.'>\\3</a>\\4',
						'\\1<a href="mailto:\\2" title="\\3">\\3</a>\\5'
					), $string);

				}

			}
			
			//
			// All kinds of BBCode regexps
			//
			$regexps = array(
				// [b]text[/b]
					"#\[b\](.*?)\[/b\]#is" => '<strong>\\1</strong>',
				// [i]text[/i]
					"#\[i\](.*?)\[/i\]#is" => '<em>\\1</em>',
				// [u]text[/u]
					"#\[u\](.*?)\[/u\]#is" => '<span style="text-decoration:underline">\\1</span>',
				// [s]text[/s]
					"#\[s\](.*?)\[/s\]#is" => '<del>\\1</del>',
				// [img]image[/img]
					"#\[img\]([\w]+?://[\w\#\$%&~/\.\-;:=,\?@\[\]\+\\\\\'!\(\)\*]*?)\[/img\]#is" => ( $links ) ? '<img src="\\1" alt="'.$lang['UserPostedImage'].'" class="user-posted-image" />' : '\\1',
				// www.usebb.net
					"#([\s])(www\.[\w\#\$%&~/\.\-;:=,\?@\[\]\+\\\\\'!\(\)\*]*?)#is" => ( $links ) ? '\\1<a href="http://\\2" title="http://\\2"'.$rel.'>\\2</a>\\3' : '\\1\\2\\3',
				// ftp.usebb.net
					"#([\s])(ftp\.[\w\#\$%&~/\.\-;:=,\?@\[\]\+\\\\\'!\(\)\*]*?)([\s])#is" => ( $links ) ? '\\1<a href="ftp://\\2" title="ftp://\\2"'.$rel.'>\\2</a>\\3' : '\\1\\2\\3',
				// [url]http://www.usebb.net[/url]
					"#\[url\]([\w]+?://[\w\#\$%&~/\.\-;:=,\?@\[\]\+\\\\\'!\(\)\*]*?)\[/url\]#is" => ( $links ) ? '<a href="\\1" title="\\1"'.$rel.'>\\1</a>' : '\\1',
				// [url=http://www.usebb.net]UseBB[/url]
					"#\[url=([\w]+?://[\w\#\$%&~/\.\-;:=,\?@\[\]\+\\\\\'!\(\)\*]*?)\](.*?)\[/url\]#is" => ( $links ) ? '<a href="\\1" title="\\1"'.$rel.'>\\2</a>' : '\\2 [\\1]',
				// [mailto]somebody@nonexistent.com[/mailto]
					"#\[mailto\]([a-z0-9&\-_\.\+]+?@[\w\-]+\.([\w\-\.]+\.)?[\w]+)\[/mailto\]#is" => ( $links ) ? '<a href="mailto:\\1" title="\\1">\\1</a>' : '\\1',
				// [mailto=somebody@nonexistent.com]mail me[/mailto]
					"#\[mailto=([a-z0-9&\-_\.\+]+?@[\w\-]+\.([\w\-\.]+\.)?[\w]+)\](.*?)\[/mailto\]#is" => ( $links ) ? '<a href="mailto:\\1" title="\\1">\\3</a>' : '\\3 [\\1]',
				// [color=red]text[/color]
					"#\[color=([\#a-z0-9]+)\](.*?)\[/color\]#is" => '<span style="color:\\1">\\2</span>',
				// [size=999]too big text[/size]
					"#\[size=([0-9]{3,})\](.*?)\[/size\]#is" => '\\2',
				// [size=14]text[/size]
					"#\[size=([0-9]*?)\](.*?)\[/size\]#is" => '<span style="font-size:\\1pt">\\2</span>',
				// [google=keyword]text[/google]
					"#\[google=(.*?)\](.*?)\[/google\]#is" => '<a href="http://www.google.com/search?q=\\1"'.$rel.'>\\2</a>',
			);
			
			//
			// Now parse those regexps
			//
			foreach ( $regexps as $find => $replace )
				$string = preg_replace($find, $replace, $string);
			
			//
			// Remove tags from attributes
			//
			if ( strpos($string, '<') !== false ) {
	
				preg_match_all('#[a-z]+="[^"]*<[^>]*>[^"]*"#', $string, $matches);
	
				foreach ( $matches[0] as $match )
					$string = str_replace($match, strip_tags($match), $string);
	
			}
			
		}
		
		if ( !$html ) {
			
			$string = str_replace("\n", "<br />", $string);
			$string = str_replace("\r", "", $string);
			
		}
		
		//
		// XML (RSS/Atom) does not define elements such as a, pre, etc.
		// Though, make sure the already escaped < and > are still/double escaped.
		//
		if ( $rss_mode )
			$string = str_replace(array('&lt;', '&gt;', '<', '>'), array('&amp;lt;', '&amp;gt;', '&lt;', '&gt;'), $string);
		
		return trim($string);
		
	}
	
	/**
	 * Return the BBCode control buttons
	 *
	 * @param bool $links Enable controls for links
	 * @returns string HTML BBCode controls
	 */
	function get_bbcode_controls($links=true) {
		
		global $lang, $template;
		
		$controls = array(
			array('[b]', '[/b]', 'B', 'font-weight: bold'),
			array('[i]', '[/i]', 'I', 'font-style: italic'),
			array('[u]', '[/u]', 'U', 'text-decoration: underline'),
			array('[s]', '[/s]', 'S', 'text-decoration: line-through'),
			array('[quote]', '[/quote]', $lang['Quote'], ''),
			array('[code]', '[/code]', $lang['Code'], ''),
		);

		if ( $links ) {

			$controls = array_merge($controls, array(
				array('[img]', '[/img]', $lang['Img'], ''),
				array('[url=http://www.example.com]', '[/url]', $lang['URL'], ''),
			));

		}

		$controls = array_merge($controls, array(
			array('[color=red]', '[/color]', $lang['Color'], ''),
			array('[size=14]', '[/size]', $lang['Size'], '')
		));
		
		$out = array();
		foreach ( $controls as $data )
			$out[] = '<a href="javascript:void(0);" onclick="insert_tags(\''.$data[0].'\', \''.$data[1].'\')" style="'.$data[3].'">'.$data[2].'</a>';
		
		return join($template->get_config('post_form_bbcode_seperator'), $out);
		
	}
	
	/**
	 * Return the smiley control graphics
	 *
	 * @returns string HTML smiley controls
	 */
	function get_smiley_controls() {
		
		global $template;
		
		$smilies = $template->get_config('smilies');
		$smilies = array_unique($smilies);
		$out = array();
		foreach ( $smilies as $pattern => $img )
			$out[] = '<a href="javascript:void(0)" onclick="insert_smiley(\''.addslashes(unhtml($pattern)).'\')"><img src="templates/'.$this->get_config('template').'/smilies/'.$img.'" alt="'.unhtml($pattern).'" /></a>';
		
		return join($template->get_config('post_form_smiley_seperator'), $out);
		
	}
	
	/**
	 * Censor text
	 *
	 * @param string $string Text to censor
	 * @returns string Censored text
	 */
	function replace_badwords($string) {
		
		global $db;
		
		if ( $this->get_config('enable_badwords_filter') ) {
			
			//
			// Algorithm borrowed from phpBB
			//
			if ( !isset($this->badwords) ) {
				
				$result = $db->query("SELECT word, replacement FROM ".TABLE_PREFIX."badwords ORDER BY word ASC");
				$this->badwords = array();
				while ( $data = $db->fetch_result($result) )
					$this->badwords['#\b(?:' . str_replace('\*', '\w*?', preg_quote(stripslashes($data['word']), '#')) . ')\b#i'] = stripslashes($data['replacement']);
				
			}
			
			foreach ( $this->badwords as $badword => $replacement )
				$string = preg_replace($badword, $replacement, $string);
			
		}
		
		return $string;
		
	}
	
	/**
	 * Timezone handling
	 *
	 * @param string $action 'get_zones' or 'check_existance'
	 * @param mixed $param Time zone param for 'check_existance'
	 * @returns mixed Array with timezones or bool
	 */
	function timezone_handler($action, $param=NULL) {
		
		$timezones = array(
			'-12' => '-12:00',
			'-11' => '-11:00',
			'-10' => '-10:00',
			'-9' => '-9:00',
			'-8' => '-8:00',
			'-7' => '-7:00',
			'-6' => '-6:00',
			'-5' => '-5:00',
			'-4' => '-4:00',
			'-3.5' => '-3:30',
			'-3' => '-3:00',
			'-2' => '-2:00',
			'-1' => '-1:00',
			'0' => '+0:00',
			'+1' => '+1:00',
			'+2' => '+2:00',
			'+3' => '+3:00',
			'+3.5' => '+3:30',
			'+4' => '+4:00',
			'+4.5' => '+4:30',
			'+5' => '+5:00',
			'+5.5' => '+5:30',
			'+6' => '+6:00',
			'+7' => '+7:00',
			'+8' => '+8:00',
			'+9' => '+9:00',
			'+9.5' => '+9:30',
			'+10' => '+10:00',
			'+11' => '+11:00',
			'+12' => '+12:00',
		);
		
		if ( $action == 'get_zones' ) {
			
			return $timezones;
			
		} elseif ( $action == 'check_existance' ) {
			
			if ( !empty($timezones[$param]) )
				return true;
			else
				return false;
			
		}
		
	}

	/**
	 * Make a user's profile link
	 *
	 * @param int $user_id User ID
	 * @param string $username Username
	 * @param int $level Level
	 * @param string $title Title attribute
	 * @returns string HTML
	 */
	function make_profile_link($user_id, $username, $level, $title=null) {
		
		switch ( $level ) {
			
			case LEVEL_ADMIN:
				$levelclass = ' class="administrator"';
				break;
			case LEVEL_MOD:
				$levelclass = ' class="moderator"';
				break;
			case LEVEL_MEMBER:
				$levelclass = '';
				break;
			default:
				trigger_error('User ID '.$user_id.' has a level of '.$level.' which is not possible within UseBB.', E_USER_ERROR);
			
		}
		
		$title = ( !empty($title) ) ? ' title="'.unhtml($title).'"' : '';
		
		return '<a href="'.$this->make_url('profile.php', array('id' => $user_id)).'"'.$levelclass.$title.'>'.unhtml(stripslashes($username)).'</a>';
		
	}
	
	/**
	 * Create a forum statistics box like on the forum index
	 */
	function forum_stats_box() {
		
		global $db, $template, $lang, $session;
		
		if ( $this->get_config('enable_forum_stats_box') && $this->get_user_level() >= $this->get_config('view_forum_stats_box_min_level') ) {
			
			//
			// Timestamp for defining last updated sessions
			//
			$min_updated = time() - ( $this->get_config('online_min_updated') * 60 );
			
			//
			// Get the session and user information
			//
			$result = $db->query("SELECT u.displayed_name, u.level, u.hide_from_online_list, s.user_id AS id, s.ip_addr, s.updated FROM ( ".TABLE_PREFIX."sessions s LEFT JOIN ".TABLE_PREFIX."members u ON s.user_id = u.id ) WHERE s.updated > ".$min_updated." ORDER BY s.updated DESC");
			
			//
			// Arrays for holding a list of online guests and members.
			//
			$count = array(
				'total_members' => 0,
				'hidden_members' => 0,
				'guests' => 0
			);
			$list = array(
				'members' => array(),
				'guests' => array()
			);
			$memberlist = array();
			
			while ( $onlinedata = $db->fetch_result($result) ) {
				
				if ( !$onlinedata['id'] ) {
					
					//
					// This is a guest
					// Guests will only be counted per IP address
					//
					if ( !in_array($onlinedata['ip_addr'], $list['guests']) ) {
						
						$count['guests']++;
						$list['guests'][] = $onlinedata['ip_addr'];
						
					}
					
				} else {
					
					//
					// This is a member
					//
					if ( !in_array($onlinedata['id'], $list['members']) ) {
						
						$title = $this->make_date($onlinedata['updated'], 'h:i:s a');
						
						if ( !$onlinedata['hide_from_online_list'] ) {
							
							$memberlist[] = $this->make_profile_link($onlinedata['id'], $onlinedata['displayed_name'], $onlinedata['level'], $title);
							
						} else {
							
							if ( $this->get_user_level() == LEVEL_ADMIN )
								$memberlist[] = '<em>'.$this->make_profile_link($onlinedata['id'], $onlinedata['displayed_name'], $onlinedata['level'], $title).'</em>';
							
							$count['hidden_members']++;
							
						}
						
						$count['total_members']++;
						$list['members'][] = $onlinedata['id'];
						
					}
					
				}
				
			}
			
			$latest_member = $this->get_stats('latest_member');
			
			if ( $count['total_members'] === 1 && $count['guests'] === 1 )
				$users_online = $lang['MemberGuestOnline'];
			elseif ( $count['total_members'] !== 1 && $count['guests'] === 1 )
				$users_online = $lang['MembersGuestOnline'];
			elseif ( $count['total_members'] === 1 && $count['guests'] !== 1 )
				$users_online = $lang['MemberGuestsOnline'];
			else
				$users_online = $lang['MembersGuestsOnline'];
			
			//
			// Parse the online box
			//
			$template->parse('forum_stats_box', 'various', array(
				'small_stats' => sprintf($lang['IndexStats'], $this->get_stats('posts'), $this->get_stats('topics'), $this->get_stats('members')),
				'newest_member' => ( !$this->get_stats('members') ) ? '' : ' '.sprintf($lang['NewestMemberExtended'], '<a href="'.$this->make_url('profile.php', array('id' => $latest_member['id'])).'">'.unhtml(stripslashes($latest_member['displayed_name'])).'</a>'),
				'users_online' => sprintf($users_online, $this->get_config('online_min_updated'), $count['total_members'], $count['hidden_members'], $count['guests']),
				'members_online' => ( count($memberlist) ) ? join(', ', $memberlist) : '',
				'detailed_list_link' => ( $this->get_config('enable_detailed_online_list') && $this->get_user_level() >= $this->get_config('view_detailed_online_list_min_level') ) ? '<a href="'.$this->make_url('online.php').'">'.$lang['Detailed'].'</a>' : ''
			));
			
		}
		
	}
	
	/**
	 * Get the server's load avarage value
	 *
	 * @param integer $which What load variable to call ('all' for an array of all)
	 * @returns float Server load average
	 */
	function get_server_load($which=1) {
		
		//
		// Afaik, this does not exist at Windows
		//
		if ( ON_WINDOWS )
			return false;
		
		//
		// Load has not been requested yet
		//
		if ( is_null($this->server_load) ) {
			
			$found_load = false;
			
			//
			// First attempt: reading /proc/loadavg
			//
			$file = '/proc/loadavg';
			if ( file_exists($file) && is_readable($file) ) {
				
				$fh = fopen($file, 'r');
				
				if ( is_resource($fh) ) {
					
					$out = fread($fh, 1024);
					fclose($fh);
					
					if ( preg_match('#([0-9]+\.[0-9]{2}) ([0-9]+\.[0-9]{2}) ([0-9]+\.[0-9]{2})#', $out, $match) ) {
						
						$this->server_load = array(
							(float)$match[1],
							(float)$match[2],
							(float)$match[3]
						);
						$found_load = true;
						
					}
					
				}
				
			}
			
			if ( !$found_load ) {
				
				//
				// Second attempt: executing uptime
				//
				$tmp = array();
				$retval = 1;
				$out = exec('uptime', $tmp, $retval);
				unset($tmp);
				
				if ( !$retval ) {
					
					if ( preg_match('#([0-9]+\.[0-9]{2}),? ([0-9]+\.[0-9]{2}),? ([0-9]+\.[0-9]{2})#', $out, $match) ) {
						
						$this->server_load = array(
							(float)$match[1],
							(float)$match[2],
							(float)$match[3]
						);
						
					} else {
						
						$this->server_load = false;
						
					}
					
				} else {
					
					$this->server_load = false;
					
				}
				
			}
			
		}
		
		if ( !$this->server_load )
			return false;
		elseif ( $which == 'all' )
			return $this->server_load;
		elseif ( is_int($which) )
			return $this->server_load[$which-1];
		
	}
	
	/**
	 * Define the icon for forums
	 *
	 * @param int $id Forum ID
	 * @param bool $open Open (or locked)
	 * @param int $post_time Unix timestamp of update
	 * @returns array Array with forum icon and status
	 */
	function forum_icon($id, $open, $post_time) {
		
		global $db, $session, $template, $lang;
		
		if ( $session->sess_info['user_id'] && !empty($_SESSION['previous_visit']) && !is_array($this->updated_forums) ) {
			
			$result = $db->query("SELECT t.id, t.forum_id, p.post_time FROM ".TABLE_PREFIX."topics t, ".TABLE_PREFIX."posts p WHERE p.id = t.last_post_id AND p.post_time > ".$_SESSION['previous_visit']);
			$this->updated_forums = array();
			while ( $topicsdata = $db->fetch_result($result) ) {
				
				if ( !in_array($topicsdata['forum_id'], $this->updated_forums) && ( !isset($_SESSION['viewed_topics']['t'.$topicsdata['id']]) || $_SESSION['viewed_topics']['t'.$topicsdata['id']] < $topicsdata['post_time'] ) )
					$this->updated_forums[] = $topicsdata['forum_id'];
				
			}
			
		}
		
		if ( $session->sess_info['user_id'] && !empty($_SESSION['previous_visit']) && in_array($id, $this->updated_forums) ) {
			
			if ( $open ) {
				
				$forum_icon = $template->get_config('open_newposts_icon');
				$forum_status = $lang['NewPosts'];
				
			} else {
				
				$forum_icon = $template->get_config('closed_newposts_icon');
				$forum_status = $lang['LockedNewPosts'];
				
			}
			
		} else {
			
			if ( $open ) {
				
				$forum_icon = $template->get_config('open_nonewposts_icon');
				$forum_status = $lang['NoNewPosts'];
				
			} else {
				
				$forum_icon = $template->get_config('closed_nonewposts_icon');
				$forum_status = $lang['LockedNoNewPosts'];
				
			}
			
		}
		
		return array($forum_icon, $forum_status);
		
	}
	
	/**
	 * Define the icon for topics
	 *
	 * @param int $id Topic ID
	 * @param bool $locked Locked (or open)
	 * @param int $post_time Unix timestamp of update
	 * @returns array Array with topic icon and status
	 */
	function topic_icon($id, $locked, $post_time) {
		
		global $session, $template, $lang;
		
		if ( $session->sess_info['user_id'] && !empty($_SESSION['previous_visit']) && $_SESSION['previous_visit'] < $post_time && ( !isset($_SESSION['viewed_topics']['t'.$id]) || $_SESSION['viewed_topics']['t'.$id] < $post_time ) ) {
			
			if ( !$locked ) {
				
				$topic_icon = $template->get_config('open_newposts_icon');
				$topic_status = $lang['NewPosts'];
				
			} else {
				
				$topic_icon = $template->get_config('closed_newposts_icon');
				$topic_status = $lang['LockedNewPosts'];
				
			}
			
		} else {
			
			if ( !$locked ) {
				
				$topic_icon = $template->get_config('open_nonewposts_icon');
				$topic_status = $lang['NoNewPosts'];
				
			} else {
				
				$topic_icon = $template->get_config('closed_nonewposts_icon');
				$topic_status = $lang['LockedNoNewPosts'];
				
			}
			
		}
		
		return array($topic_icon, $topic_status);
		
	}
	
	/**
	 * Return birthday input fields
	 *
	 * @param string $input Input birthday field
	 * @returns array Input fields
	 */
	function birthday_input_fields($input) {
		
		global $lang;
		$months = array('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');
		
		if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
			
			$birthday_year = $_POST['birthday_year'];
			$birthday_month = $_POST['birthday_month'];
			$birthday_day = $_POST['birthday_day'];
			
		} else {
			
			$birthday = $input;
			$birthday_year = ( $birthday ) ? intval(substr($birthday, 0, 4)) : '';
			$birthday_month = ( $birthday ) ? intval(substr($birthday, 4, 2)) : 0;
			$birthday_day = ( $birthday ) ? intval(substr($birthday, 6, 2)) : 0;
			
		}
		$birthday_month_input = '<select name="birthday_month"><option value="">'.$lang['Month'].'</option>';
		for ( $i = 1; $i <= 12; $i++ ) {
			
			$selected = ( $birthday_month == $i ) ? ' selected="selected"' : '';
			$month_name = ( isset($lang['date_translations']) && is_array($lang['date_translations']) ) ? $lang['date_translations'][$months[$i-1]] : $months[$i-1];
			$birthday_month_input .= '<option value="'.$i.'"'.$selected.'>'.$month_name.'</option>';
			
		}
		$birthday_month_input .= '</select>';
		$birthday_day_input = '<select name="birthday_day"><option value="">'.$lang['Day'].'</option>';
		for ( $i = 1; $i <= 31; $i++ ) {
			
			$selected = ( $birthday_day == $i ) ? ' selected="selected"' : '';
			$birthday_day_input .= '<option value="'.$i.'"'.$selected.'>'.$i.'</option>';
			
		}
		$birthday_day_input .= '</select>';
		$birthday_year_input = '<select name="birthday_year"><option value="">'.$lang['Year'].'</option>';
		for ( $i = intval(date('Y')); $i >= 1900; $i-- ) {
			
			$selected = ( $birthday_year == $i ) ? ' selected="selected"' : '';
			$birthday_year_input .= '<option value="'.$i.'"'.$selected.'>'.$i.'</option>';
			
		}
		$birthday_year_input .= '</select>';
		
		return array($birthday_year_input, $birthday_month_input, $birthday_day_input);
		
	}
	
	/**
	 * Calculate the age of a person based on a birthday date
	 *
	 * @param int $birthday Unix timestamp
	 * @returns int Age
	 */
	function calculate_age($birthday) {
		
		$month = intval(substr($birthday, 4, 2));
		$day = intval(substr($birthday, 6, 2));
		$year = intval(substr($birthday, 0, 4));
		
		//
		// Because Windows doesn't allow dates before 1970 with mktime(),
		// we perform a trick to calculate dates before 1970.
		//
		if ( $year < 1970 ) {
			
			$years_before_unix_epoch = 1970 - $year;
			$false_year = $year + ( $years_before_unix_epoch * 2 );
			$timestamp = mktime(0, 0, 0, $month, $day, $false_year);
			$timestamp -= ( $years_before_unix_epoch * 31556926 * 2 );
			
		} else {
			
			$timestamp = mktime(0, 0, 0, $month, $day, $year);
			
		}
		
		return floor((time()-$timestamp)/31556926);
		
	}
	
	/**
	 * Get a list of template sets
	 *
	 * @returns array List of available template sets
	 */
	function get_template_sets() {
		
		if ( !count($this->available['templates']) ) {
			
			$handle = opendir(ROOT_PATH.'templates');
			while ( false !== ( $template_name = readdir($handle) ) ) {
				
				if ( is_dir(ROOT_PATH.'templates/'.$template_name) && is_readable(ROOT_PATH.'templates/'.$template_name) && ( $this->get_user_level() == LEVEL_ADMIN || preg_match('#^[^\.]#', $template_name) ) && file_exists(ROOT_PATH.'templates/'.$template_name.'/global.tpl.php') )
					$this->available['templates'][] = $template_name;
				
			}
			closedir($handle);
			sort($this->available['templates']);
			reset($this->available['templates']);
			
		}
		
		return $this->available['templates'];
		
	}
	
	/**
	 * Get a list of language packs
	 *
	 * @returns array List of available language packs
	 */
	function get_language_packs() {
		
		if ( !count($this->available['languages']) ) {
			
			$handle = opendir(ROOT_PATH.'languages');
			while ( false !== ( $language_name = readdir($handle) ) ) {
				
				if ( preg_match('#^lang_(.+)\.php$#', $language_name, $language_name) )
					$this->available['languages'][] = $language_name[1];
				
			}
			closedir($handle);
			sort($this->available['languages']);
			reset($this->available['languages']);
			
		}
		
		return $this->available['languages'];
		
	}
	
	/**
	 * Return the sql tables with the table prefix
	 *
	 * @returns array List of SQL tables with UseBB table prefix
	 */
	function get_usebb_tables() {
		
		global $db;
		
		if ( !count($this->db_tables) ) {
			
			$result = $db->query("SHOW TABLES LIKE '".TABLE_PREFIX."%'");
			while ( $out = $db->fetch_result($result) )
				$this->db_tables[] = current($out);
			
		}
		
		return $this->db_tables;
		
	}
	
	/**
	 * Redirect the user to a certain location within UseBB
	 *
	 * @param string $page .php file to link to
	 * @param array $vars Array with GET variables
	 * @param string $anchor HTML anchor
	 */
	function redirect($page, $vars=array(), $anchor='') {
		
		$goto = $this->get_config('board_url').$this->make_url($page, $vars, false);
		
		if ( substr($goto, -2) == './' )
			$goto = substr($goto, 0, strlen($goto)-2);

		if ( !empty($anchor) )
			$goto .= '#'.$anchor;
		
		$this->raw_redirect($goto);
		
	}
	
	/**
	 * Redirect with a predefined URL
	 *
	 * @param string $url URL
	 */
	function raw_redirect($url) {
		
		//
		// Don't use Location on IIS or Abyss
		//
		if ( strpos($_SERVER['SERVER_SOFTWARE'], 'Microsoft-IIS') === false && strpos($_SERVER['SERVER_SOFTWARE'], 'Abyss') === false )
			header('Location: '.$url);
		die('<meta http-equiv="refresh" content="0;URL='.$url.'" />');
		
	}

	/**
	 * Validate a password
	 *
	 * @param string $password Password
	 * @param bool $extended Extended checking (new passwords)
	 * @returns bool Valid
	 */
	function validate_password($password, $extended=false) {
		
		$valid = ( preg_match(PWD_PREG, $password) );
		
		//
		// Only do this for new passwords.
		//
		if ( $valid && $extended )
			$valid = ( !contains_entities($password, true) && preg_match('#[[:alpha:]]#', $password) && preg_match('#[[:digit:]]#', $password) );
		
		return $valid;
		
	}
	
	/**
	 * Validate an email address
	 *
	 * @param string $email_address Email address
	 * @returns bool Valid
	 */
	function validate_email($email_address) {
		
		if ( !preg_match(EMAIL_PREG, $email_address) )
			return false;
		
		if ( $this->get_config('enable_email_dns_check') ) {
			
			$parts = explode('@', $email_address);
			
			if ( function_exists('checkdnsrr') && !ON_WINDOWS ) {
				
				return checkdnsrr($parts[1], 'MX');
				
			} elseif ( ON_WINDOWS ) {
				
				return checkdnsrr_win($parts[1], 'MX');
				
			}
			
		}
		
		return true;
		
	}
	
	/**
	 * Set a cookie
	 *
	 * This function takes care of past expire values for empty cookies, and
	 * uses the HttpOnly flag when enabled.
	 *
	 * The HttpOnly hack for < PHP 5.2 taken from
	 * @link http://blog.mattmecham.com/archives/2006/09/http_only_cookies_without_php.html
	 *
	 * Note: HttpOnly is disabled when working on a non domain (localhost, IP address)
	 * since when cookie_domain is empty and HttpOnly is used, IE 6 and 7 fail to set
	 * the cookie, even though the Set-Cookie header is well-formed and valid.
	 *
	 * @param string $name Name
	 * @param string $value Value
	 * @param int $expires Expire timestamp (when necessary)
	 */
	function setcookie($name, $value, $expires=null) {
		
		$expires = ( is_null($expires) && empty($value) ) ? time()-31536000 : $expires;
		$domain = $this->get_config('cookie_domain');
		$secure = ( $this->get_config('cookie_secure') ) ? 1 : 0;
		
		if ( empty($domain) || !$this->get_config('cookie_httponly') )
			setcookie($name, $value, $expires, $this->get_config('cookie_path'), $domain, $secure);
		elseif ( version_compare(PHP_VERSION, '5.2.0RC2', '>=') )
			setcookie($name, $value, $expires, $this->get_config('cookie_path'), $domain, $secure, true);
		else
			setcookie($name, $value, $expires, $this->get_config('cookie_path'), $domain.'; HttpOnly', $secure);
		
	}

	/**
	 * Generate an antispam question
	 *
	 * @param int $mode Anti-spam mode
	 */
	function generate_antispam_question($mode) {
		
		global $lang;

		switch ( $mode ) {
			
			case ANTI_SPAM_MATH:
				//
				// Random math question
				//
				$operator = mt_rand(1, 2);
				if ( $operator == 1 ) {
					
					$num1 = mt_rand(1, 9);
					$num2 = mt_rand(1, 9);
					$_SESSION['antispam_question_question'] = sprintf($lang['AntiSpamQuestionMathPlus'], $num1, $num2);
					$_SESSION['antispam_question_answer'] = $num1 + $num2;

				} else {
					
					$num1 = mt_rand(1, 9);
					$num2 = mt_rand(1, $num1);
					$_SESSION['antispam_question_question'] = sprintf($lang['AntiSpamQuestionMathMinus'], $num1, $num2);
					$_SESSION['antispam_question_answer'] = $num1 - $num2;
					
				}
				break;
			
			case ANTI_SPAM_CUSTOM:
				//
				// Custom admin-defined question
				//
				$questionPairs = $this->get_config('antispam_question_questions');
				if ( !is_array($questionPairs) || !count($questionPairs) )
					trigger_error('No custom anti-spam questions found.', E_USER_ERROR);
				$questions = array_keys($questionPairs);
				$answers = array_values($questionPairs);
				unset($questionPairs);
				
				$questionId = ( count($questions) == 1 ) ? 0 : mt_rand(0, count($questions)-1);
				
				$_SESSION['antispam_question_question'] = $questions[$questionId];
				$_SESSION['antispam_question_answer'] = $answers[$questionId];
				break;
			
			default:
				trigger_error('Spam check mode '.$mode.' does not exist.', E_USER_ERROR);
			
		}

	}

	/**
	 * Pose the anti-spam question
	 *
	 * This might render a form and halt further page execution.
	 */
	function pose_antispam_question() {
		
		global $session, $template, $lang, $db;

		if ( !$session->sess_info['pose_antispam_question'] )
			return;
		
		$template->clear_breadcrumbs();
		$template->add_breadcrumb($lang['AntiSpamQuestion']);
		
		$mode = (int)$this->get_config('antispam_question_mode');

		if ( empty($_SESSION['antispam_question_question']) )
			$this->generate_antispam_question($mode);
	
		if ( isset($_POST['answer']) && !is_array($_POST['answer']) && !strcasecmp(strval($_POST['answer']), strval($_SESSION['antispam_question_answer'])) ) {
			
			//
			// Question passed, continuing...
			//
			$_SESSION['antispam_question_posed'] = true;
			unset($_SESSION['antispam_question_question'], $_SESSION['antispam_question_answer']);
			$this->redirect($_SERVER['PHP_SELF'], $_GET);

			return;
			
		}
		
		if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
			
			$template->parse('msgbox', 'global', array(
				'box_title' => $lang['Error'],
				'content' => $lang['AntiSpamWrongAnswer']
			));
			
		}
		
		$size = ( $mode === ANTI_SPAM_MATH ) ? 'size="2" maxlength="2"' : 'size="35"';
		$template->parse('anti_spam_question', 'various', array(
			'form_begin' => '<form action="'.$this->make_url($_SERVER['PHP_SELF'], $_GET).'" method="post">',
			'question' => unhtml($_SESSION['antispam_question_question']),
			'answer_input' => '<input type="text" name="answer" id="answer" '.$size.' />',
			'submit_button' => '<input type="submit" name="submit" value="'.$lang['Send'].'" />',
			'form_end' => '</form>'
		));
		$template->set_js_onload("set_focus('answer')");
		
		//
		// Include the page footer
		//
		require(ROOT_PATH.'sources/page_foot.php');
		
		exit();
		
	}

	/**
	 * Generate a security token
	 *
	 * @link https://github.com/usebb/UseBB/wiki/UseBB-1-CSRF
	 *
	 * @returns string Token
	 */
	function generate_token() {

		static $token;

		if ( isset($token) )
			return $token;
		
		list($usec, $sec) = explode(' ', microtime());
		$time = (float)$usec + (float)$sec;
		$key = $this->random_key();

		if ( !$_SESSION['oldest_token'] )
			$_SESSION['oldest_token'] = $time;
		
		// For some reason, PHP juggled between dot and comma as decimal separator
		// when using strval() and others. (PHP 5.3.6 on OS X 10.6.7)
		$stime = number_format($time, 4, '.', '');
		$_SESSION['tokens'][$stime] = $key;
		$token = $stime.'-'.$key;

		return $token;

	}

	/**
	 * Verify a token
	 *
	 * @link https://github.com/usebb/UseBB/wiki/UseBB-1-CSRF
	 *
	 * @param string $try_token Token to test
	 * @returns bool Verified
	 */
	function verify_token($try_token) {

		if ( !preg_match('#^[0-9]+\.[0-9]{4}\-[0-9a-f]{32}$#', $try_token) )
			return false;
		
		list($time, $key) = explode('-', $try_token);
		$sess_idx = $time;

		return ( !empty($_SESSION['tokens'][$sess_idx]) && $_SESSION['tokens'][$sess_idx] === $key );

	}

	/**
	 * Token error
	 *
	 * Parse a msgbox template with a suitable message.
	 *
	 * @link https://github.com/usebb/UseBB/wiki/UseBB-1-CSRF
	 *
	 * @param string $type Error type ("form" or "url")
	 */
	function token_error($type) {
		
		global $template, $lang;

		$content = '';
		switch ( $type ) {

			case 'form':
				$content = $lang['InvalidFormTokenNotice'];
				break;
			case 'url':
				$content = $lang['InvalidURLTokenNotice'];
				break;

		}
		
		$template->parse('msgbox', 'global', array(
			'box_title' => $lang['Note'],
			'content' => nl2br($content)
		));

	}

	/**
	 * Verify a form for tokens
	 *
	 * @link https://github.com/usebb/UseBB/wiki/UseBB-1-CSRF
	 *
	 * @param bool $enable_message Enable error message
	 * @returns bool Verified
	 */
	function verify_form($enable_message=true) {

		$post_idx = '_form_token_';
		$result = ( !empty($_POST[$post_idx]) && $this->verify_token($_POST[$post_idx]) );

		if ( $enable_message && !$result )
			$this->token_error('form');

		return $result;

	}

	/**
	 * Verify a URL for tokens
	 *
	 * @link https://github.com/usebb/UseBB/wiki/UseBB-1-CSRF
	 *
	 * @param bool $enable_message Enable error message
	 * @returns bool Verified
	 */
	function verify_url($enable_message=true) {

		$get_idx = '_url_token_';
		$result = ( !empty($_GET[$get_idx]) && $this->verify_token($_GET[$get_idx]) );

		if ( $enable_message && !$result )
			$this->token_error('url');

		return $result;

	}

	/**
	 * Read a remote URL into string
	 *
	 * @param string $url URL
	 * @returns string Contents
	 */
	function read_url($url) {
		
		if ( function_exists('curl_init') && function_exists('curl_exec') ) {
			
			//
			// cURL
			//
			$curl = curl_init($url);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_HEADER, false);
			curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 3);
			$result = curl_exec($curl);

			if ( $result === FALSE )
				return FALSE;

			$contents = trim($result);
			curl_close($curl);

			return $contents;
			
		}

		//
		// URL fopen()
		//
		if ( !ini_get('allow_url_fopen') )
			return false;
		
		$fp = fopen($url, 'r');

		if ( !$fp )
			return false;

		$contents = '';

		if ( function_exists('stream_get_contents') ) {
			
			//
			// PHP 5 stream
			//
			$result = stream_get_contents($fp);

			if ( $result === FALSE )
				return FALSE;

			$contents = trim($result);
			
		} else {
			
			//
			// fread() packet reading
			//
			while ( !feof($fp) ) {

				$result = fread($fp, 8192);

				if ( $result === FALSE )
					return FALSE;

				$contents .= $result;

			}

			$contents = trim($contents);
			
		}

		fclose($fp);

		return $contents;
		
	}

	/**
	 * Stop Forum Spam API request
	 *
	 * @link http://www.stopforumspam.com/usage
	 *
	 * @param string $email Email address
	 * @returns mixed FALSE if nothing found, array otherwise
	 */
	function sfs_api_request($email) {

		//
		// Not really clean XML parsing code. Will improve for UseBB 2.
		//
		
		//
		// Session cache
		//
		if ( isset($_SESSION['sfs_ban_cache'][$email]) )
			return $_SESSION['sfs_ban_cache'][$email];
		
		$result = $this->read_url('http://www.stopforumspam.com/api?email='.urlencode($email));

		//
		// Failed request
		//
		if ( $result === FALSE || !preg_match('#<response[^>]+success="true"[^>]*>#', $result) )
			return FALSE;
		
		//
		// Not in database
		//
		if ( strpos($result, '<appears>yes</appears>') === FALSE ) {

			$_SESSION['sfs_ban_cache'][$email] = FALSE;
			
			return FALSE;

		}
		
		$return = array();

		if ( preg_match('#<lastseen>([0-9]{4}\-[0-9]{2}\-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2})</lastseen>#i', $result, $matches) )
			$return['lastseen'] = strtotime($matches[1]);

		if ( preg_match('#<frequency>([0-9]+)</frequency>#i', $result, $matches) )
			$return['frequency'] = (int) $matches[1];

		$_SESSION['sfs_ban_cache'][$email] = $return;

		return $return;

	}

	/**
	 * Stop Forum Spam email check
	 *
	 * Check Stop Forum Spam for a banned email address.
	 *
	 * @param string $email Email address
	 * @returns bool Banned
	 */
	function sfs_email_banned($email) {

		global $db;
		
		if ( !$this->get_config('sfs_email_check') )
			return FALSE;

		$info = $this->sfs_api_request($email);

		//
		// Not banned
		//
		if ( $info === FALSE )
			return FALSE;

		$min_frequency = $this->get_config('sfs_min_frequency');
		$max_lastseen = $this->get_config('sfs_max_lastseen');

		//
		// Does not meet requirements
		//
		if ( ( $min_frequency > 0 && ( !isset($info['frequency']) || $info['frequency'] < $min_frequency ) )
			|| ( $max_lastseen > 0 && ( !isset($info['lastseen']) || $info['lastseen'] < time() - $max_lastseen * 86400 ) ) )
			return FALSE;

		if ( $this->get_config('sfs_save_bans') )
			$db->query("INSERT INTO ".TABLE_PREFIX."bans VALUES(NULL, '', '".$email."', '')");
		
		return TRUE;

	}

	/**
	 * Stop Forum Spam API submit
	 *
	 * Submit account information to the Stop Forum Spam database.
	 *
	 * @param array $data Array with username, email and ip_addr.
	 * @returns bool Success
	 */
	function sfs_api_submit($data) {

		$key = $this->get_config('sfs_api_key');

		if ( empty($data['username']) || empty($data['email']) || empty($data['ip_addr']) || empty($key) )
			return FALSE;
		
		$url = 'http://www.stopforumspam.com/add.php'
			.'?username='.urlencode($data['username'])
			.'&ip_addr='.urlencode($data['ip_addr'])
			.'&email='.urlencode($data['email'])
			.'&api_key='.urlencode($key);
		$result = $this->read_url($url);
		
		return ( $result !== FALSE );

	}

	/**
	 * Active value for user
	 *
	 * Calculate whether the user gets (in)active or is a potential spammer.
	 *
	 * @param array $user User array with active, level and posts.
	 * @param bool $new_post Whether this is in a query increasing the post count.
	 * @param bool $activate Whether this is when activating a user.
	 * @returns int Active value
	 */
	function user_active_value($user=NULL, $new_post=FALSE, $activate=FALSE) {
		
		//
		// Potential spammer status not enabled
		//
		if ( !$this->get_config('antispam_disable_post_links') 
			&& !$this->get_config('antispam_disable_profile_links') )
			return USER_ACTIVE;
		
		//
		// New (no) user = potential spammer
		//
		if ( $user === NULL )
			return USER_POTENTIAL_SPAMMER;

		//
		// poster_level is sometimes used
		//
		if ( !isset($user['level']) && isset($user['poster_level']) )
			$user['level'] = $user['poster_level'];

		if ( !isset($user['level']) )
			trigger_error('Missing data for calculating active value.', E_USER_ERROR);

		//
		// Guests are potential spammers (when enabled)
		//
		if ( $user['level'] == LEVEL_GUEST && $this->get_config('antispam_status_for_guests') )
			return USER_POTENTIAL_SPAMMER;

		//
		// Only for regular members
		//
		if ( $user['level'] != LEVEL_MEMBER )
			return USER_ACTIVE;

		if ( !isset($user['active']) )
			trigger_error('Missing data for calculating active value.', E_USER_ERROR);

		//
		// Keep status for no new post or active user, unless is activating
		//
		if ( !$activate && ( !$new_post || $user['active'] == USER_ACTIVE ) )
			return $user['active'];
		
		if ( !isset($user['posts']) )
			trigger_error('Missing data for calculating active value.', E_USER_ERROR);

		$max_posts = (int) $this->get_config('antispam_status_max_posts');
		if ( $new_post )
			$user['posts'] += 1;

		//
		// When max posts is set and user has more posts,
		// user gets active status, otherwise still potential spammer.
		//
		return ( $max_posts > 0 && $user['posts'] > $max_posts ) 
			? USER_ACTIVE : USER_POTENTIAL_SPAMMER;

	}

	/**
	 * Is potential spammer
	 *
	 * @param array $user User array with active, level and posts.
	 * @param bool $new_post Whether this is for a request increasing the post count.
	 * @returns bool Is potential spammer
	 */
	function antispam_is_potential_spammer($user, $new_post=FALSE) {
		
		//
		// poster_level is sometimes used
		//
		if ( !isset($user['level']) && isset($user['poster_level']) )
			$user['level'] = $user['poster_level'];
		
		//
		// Inactive members are potential spammers whenever the status is enabled
		//
		if ( ($this->get_config('antispam_disable_post_links') || $this->get_config('antispam_disable_profile_links')) 
			&& $user['level'] == LEVEL_MEMBER && $user['active'] == USER_INACTIVE )
			return TRUE;
		
		return ( $this->user_active_value($user, $new_post) == USER_POTENTIAL_SPAMMER );

	}

	/**
	 * Can post links
	 *
	 * @param array $user User array with active, level and posts.
	 * @param bool $new_post Whether this is for a request increasing the post count.
	 * @returns bool Whether can post links
	 */
	function antispam_can_post_links($user, $new_post=FALSE) {

		return ( !$this->antispam_is_potential_spammer($user, $new_post) 
			|| !$this->get_config('antispam_disable_post_links') );

	}

	/**
	 * Can add profile links
	 *
	 * @param array $user User array with active, level and posts.
	 * @returns bool Whether can add profile links
	 */
	function antispam_can_add_profile_links($user) {

		return ( !$this->antispam_is_potential_spammer($user, FALSE) 
			|| !$this->get_config('antispam_disable_profile_links') );

	}

}

?>
