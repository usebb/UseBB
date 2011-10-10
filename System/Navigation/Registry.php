<?php

namespace UseBB\System\Navigation;

use UseBB\System\Plugins\PluginRunningClass;

/**
 * Navigation registry.
 * 
 * Modules register controllers for specific requests. A request is a (group of)
 * input variable keys/values, such as HTTP GET variables or CLI parameters.
 * 
 * Components in the system can retrieve links to registered requests using
 * the controller and name strings.
 * 
 * \author Dietrich Moerman
 */
class Registry extends PluginRunningClass {
	private $requests = array();
	private $registry = array();
	private $reverseRegistry = array();
	
	/**
	 * Fill a request array: create parameters for key-only parts.
	 * 
	 * \param $request Request
	 * \returns New request
	 */
	private function fillRequestArray(array $request) {
		$new = array();
		
		foreach ($request as $k => $v) {
			// Key and value.
			if (!is_int($k)) {
				$new[$k] = $v;
				
				continue;
			}
			
			// Key only: set as key and @key as value
			$new[$v] = "@" . $v;
		}
		
		return $new;
	}
	
	/**
	 * Register a controller for a certain request.
	 * 
	 * Parameters (\c @@name) can be used. They are used for generating system
	 * links (such as URLs) and can match any value.
	 * 
	 * A value without key is treated as a key with a parameter with same name.
	 * E.g. in example below \c foobar = \c @@foobar
	 * 
	 * Example:
	 * \code
	 * $nav->register(array("foobar", "foo" => "@bar"), 
	 * 	"UseBB\Modules\Test\TestController");
	 * \endcode
	 * will make \c TestController handle the matching requests.
	 * 
	 * The method called on the controller is \c %handleRequest(), unless a name 
	 * \c foo is given, in which case \c handleFooRequest() is called.
	 * 
	 * \param $request Request
	 * \param $controller Controller class name (handler)
	 * \param $name Request name on controller (optional)
	 * 
	 * \exception DuplicateRequestException When more than one handler is set 
	 * for the same request
	 */
	public function register($request, $controller, $name = "") {
		$request = $this->fillRequestArray((array) $request);
		
		if (in_array($request, $this->requests)) {
			throw new DuplicateRequestException($request);
		}
		
		$i = "c" . count($this->requests);
		$this->requests[$i] = $request;
		$this->registry[$i] = array($controller, $name);
		$this->reverseRegistry[$controller . "__" . $name] = $i;
	}
	
	/**
	 * Check whether a registered request matches.
	 * 
	 * \param $request Registered request
	 * \param $current Current request
	 * \returns Matches
	 */
	private function requestMatches($request, $current) {
		// Definately some values are missing.
		if (count($current) < count($request)) {
			return FALSE;
		}
		
		$diff = array_diff($request, $current);
		
		// Identical.
		if (count($diff) == 0) {
			return TRUE;
		}
		
		foreach ($diff as $k => $v) {
			// Missing key.
			if (!isset($current[$k])) {
				return FALSE;
			}
			
			// No parameter = wrong value.
			if (substr($v, 0, 1) !== "@") {
				return FALSE;
			}
		}
		
		return TRUE;
	}
	
	/**
	 * Handle the request.
	 * 
	 * For the current request, a controller will be selected with a request
	 * matching as closely as possible. This means the current request has all
	 * the keys and eventual values of the controller's registered request.
	 * 
	 * Parameters (\c @@name) used as values in registered requests will match 
	 * any value in the current request.
	 * 
	 * When registered requests are compared, the most specific ones are tried
	 * first.
	 * 
	 * \param $current Request
	 * 
	 * \exception NoControllerFoundException When no suitable controller found
	 * 
	 * \attention This method is called automatically.
	 */
	public function handleRequest(array $current) {
		// Sort with descending number of values.
		uasort($this->requests, function($x, $y) {
			$x = count($x);
			$y = count($y);
			
			if ($x == $y) {
				return 0;
			}
			
			return $x > $y ? -1 : 1;
		});
		
		$found = NULL;
		foreach ($this->requests as $key => $request) {
			if ($this->requestMatches($request, $current)) {
				list($found, $name) = $this->registry[$key];
				break;
			}
		}
		
		if ($found === NULL) {
			throw new NoControllerFoundException();
		}
		
		$controller = new $found($this->getServiceRegistry());
		$name = ucfirst($name);
		call_user_func(array($controller, "handle" . $name . "Request"));
	}
	
	/**
	 * Get the link to a specific controller and optional name.
	 * 
	 * Pass a controller and name with eventual parameters and a context-
	 * specific link will be returned.
	 * 
	 * Parameters are \c @@name formatted values used when registering requests.
	 * They will be replaced by the new value in the parameter array.
	 * 
	 * Example (using code from register()):
	 * \code
	 * $nav->getLink("UseBB\Modules\Test\TestController", NULL, array(
	 * 	"@foobar" => "something",
	 * 	"@bar" => "baz"
	 * ));
	 * \endcode
	 * 
	 * \exception NoRegisteredRequestException When no request could be found 
	 * for the specified controller and name
	 * 
	 * \param $controller Controller
	 * \param $name Name
	 * \param $params Parameters
	 * \returns Link
	 */
	public function getLink($controller, $name = "", array $params = array()) {
		$key = $controller  . "__" . $name;
		
		if (!isset($this->reverseRegistry[$key])) {
			throw new NoRegisteredRequestException($controller, $name);
		}
		
		$request = $this->requests[$this->reverseRegistry[$key]];
		
		return $this->getService("context")
			->generateLink(array_replace($request, $params));
	}
}
