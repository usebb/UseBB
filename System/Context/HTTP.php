<?php

namespace UseBB\System\Context;

/**
 * %HTTP context.
 * 
 * \author Dietrich Moerman
 */
class HTTP extends AbstractContext {
	public function handleRequest() {
		$this->getService("session")->startOrContinue();
		$this->getService("navigation")->handleRequest($_GET);
		$this->getService("session")->save();
	}
	
	/**
	 * Get language priorities.
	 * 
	 * \returns Array with language codes and float priorities
	 */
	private function getLanguagePriorities() {
		$priorities = array();
		
		if (empty($_SERVER["HTTP_ACCEPT_LANGUAGE"])) {
			return $priorities;
		}
		
		$accepted = explode(",", $_SERVER["HTTP_ACCEPT_LANGUAGE"]);
		
		foreach ($accepted as $language) {
			$parts = explode(";", $language);
			$priorities[$parts[0]] = !empty($parts[1])
				? (float) str_replace("q=", "", $parts[1])
				: 1.0;
		}
		
		$priorities = array_merge($priorities, 
			$this->getLanguageMoreShortCodes($priorities));
		arsort($priorities);
		
		return $priorities;
	}
	
	/**
	 * Get more language short codes.
	 * 
	 * Get more (shorter) codes from the priorities array. E.g. if list is
	 * ("en-GB", "nl-NL"), "en" and "nl" will be returned.
	 * 
	 * \returns Array with extra codes and priotities.
	 */
	private function getLanguageMoreShortCodes(array $priorities) {
		$added = array();
		foreach ($priorities as $lang => $priority) {
			$dashPos = strpos($lang, "-");
			
			if ($dashPos === FALSE) {
				continue;
			}
			
			$shortCode = substr($lang, 0, $dashPos);
			
			if (isset($priorities[$shortCode]) || isset($added[$shortCode])) {
				continue;
			}
			
			$added[$shortCode] = $priority;
		}
		
		return $added;
	}
	
	public function getLanguage(array $available) {
		$priorities = $this->getLanguagePriorities();
		$choose = array_values(array_filter(array_keys($priorities), 
			function($l) use($available) {
				return in_array($l, $available);
			}));
		
		if (count($choose) == 0) {
			return "en";
		}
		
		return $choose[0];
	}
	
	public function escapeString($string) {
		return htmlspecialchars($string, ENT_QUOTES, "UTF-8");
	}
	
	public function applyArgumentsToString($string, array $args) {
		foreach ($args as $k => $v) {
			$type = substr($k, 0, 1);
			switch ($type) {
				case '%':
					$args[$k] = "<em>" . $this->escapeString($v) . "</em>";
					break;
				case '@':
				default:
					$args[$k] = $this->escapeString($v);
					break;
				case '!':
					// Nothing
			}
		}
		
		return strtr($string, $args);
	}
	
	/**
	 * Get the IP address.
	 * 
	 * \returns IP address
	 */
	public function getIPAddress() {
		return $_SERVER["REMOTE_ADDR"];
	}
	
	/**
	 * Get the browser.
	 * 
	 * \returns Browser
	 */
	public function getBrowser() {
		return !empty($_SERVER["HTTP_USER_AGENT"])
			? $_SERVER["HTTP_USER_AGENT"]
			: "";
	}
	
	/**
	 * Is secure %HTTP.
	 * 
	 * \returns Boolean
	 */
	public function isSecureHTTP() {
		return !empty($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] != "off";
	}
	
	/**
	 * Set a cookie.
	 * 
	 * \param $name Name
	 * \param $value Value
	 * \param $days Days or 0 for session lifetime
	 * \param $httpOnly %HTTP only cookie
	 */
	public function setCookie($name, $value, $days = 0, $httpOnly = TRUE) {
		$config = $this->getService("config")->forModule("system");
		$expire = $days > 0
			? time() + 60*60*24 * $days
			: 0;
		
		setcookie($name, $value, $expire, $config->get("cookiePath"), 
			$config->get("cookieDomain"), $this->isSecureHTTP(), $httpOnly);
	}
	
	/**
	 * Delete a cookie.
	 * 
	 * \param $name Name
	 */
	public function deleteCookie($name) {
		$config = $this->getService("config")->forModule("system");
		
		setcookie($name, "", -86400, $config->get("cookiePath"), 
			$config->get("cookieDomain"), $this->isSecureHTTP(), FALSE);
	}

	/**
	 * PHP error handler.
	 *
	 * \attention Do not call this method yourself.
	 *
	 * \param $number Error type
	 * \param $string Description
	 * \param $file File
	 * \param $line Line number
	 * \param $context Context
	 *
	 * \hook{error, number\, string\, file\, line\, context} \hookEndsMethodOnFalse
	 */
	public function handleError($number, $string, $file, $line, 
		array $context) {
		if (parent::handleError($number, $string, $file, $line, $context) 
			=== FALSE) {
			return;
		}
		
		printf("<p><strong>Error</strong> \"<em>%s</em>\" "
			."at <code>%s:%d</code>.</p>", 
			$string, $file, $line);
	}

	/**
	 * Uncaught exception handler.
	 *
	 * \attention Do not call this method yourself.
	 *
	 * \param $e Exception
	 *
	 * \hook{exception, exception} \hookEndsMethodOnFalse
	 */
	public function handleException(\Exception $e) {
		if (parent::handleException($e) === FALSE) {
			return;
		}
		
		$names = explode("\\", get_class($e));
		$name = end($names);
		
		$trace = $this->escapeString($e->getTraceAsString());
		
		printf("<p><strong>%s</strong> \"<em>%s</em>\" "
			."at <code>%s:%d</code>.</p>"
			."<blockquote><pre>%s</pre></blockquote>", 
			$name, $e->getMessage(), $e->getFile(), $e->getLine(), $trace);
	}
}
