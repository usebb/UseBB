<?php

namespace UseBB\System\Session;

use UseBB\System\ServiceAccessor;

/**
 * %Session system.
 * 
 * Keeps session information for the current user, including any data saved.
 * 
 * \author Dietrich Moerman
 */
class Session extends ServiceAccessor {
	private $info = array();
	private $new = FALSE;
	
	/**
	 * Generate a new session ID.
	 * 
	 * \returns Session ID
	 */
	private function generateSessionId() {
		$sessId = NULL;
		
		do {
			$sessId = md5(uniqid(NULL, TRUE));
			
			$stmt = $this->getService("database")->newQuery()
				->select("s.id")->from("sessions", "s")
				->where("s.id = :sess_id")
				->setParameter(":sess_id", $sessId)
				->execute();
			$count = $stmt->rowCount();
			$stmt->closeCursor();
		} while ($count > 0);
		
		return $sessId;
	}
	
	/**
	 * Get the session ID and continue status.
	 * 
	 * This will retrieve any session cookie value or pick a new ID.
	 * 
	 * \returns Array with continue status and ID
	 */
	private function getSessionId() {
		$sessId = $this->getService("input")->key("sessId", "COOKIE");
		
		if (!$sessId->hasValue()) {
			return array(FALSE, $this->generateSessionId());
		}
		
		return array(TRUE, $sessId->getValue());
	}
	
	/**
	 * Start a new session.
	 * 
	 * \param $sessId Session ID
	 * \param $ipAddr IP address
	 * \param $disableCookies Disable cookies
	 */
	private function newSession($sessId, $ipAddr, $disableCookies) {
		$now = new \DateTime();
		$context = $this->getService("context");
		
		$this->info = array(
			"id"       => $sessId,
			"ip_addr"  => $ipAddr,
			"user_id"  => 0,
			"started"  => $now,
			"updated"  => $now,
			"requests" => 1,
			"browser"  => $context->getBrowser(),
			"language" => $this->getService("translation")->getLanguage(),
			"data"     => array(),
		);
		
		if (!$disableCookies) {
			$context->setCookie("sessId", $sessId);
		}
		
		$this->new = TRUE;
	}
	
	/**
	 * Continue an existing session.
	 * 
	 * \param $currentInfo Current session info
	 */
	private function continueSession(array $currentInfo) {
		$currentInfo["data"] = $this->unserialize($currentInfo["data"]);
		$currentInfo["started"] = new \DateTime($currentInfo["started"]);
		$currentInfo["updated"] = new \DateTime();
		$currentInfo["requests"]++;
		$currentInfo["browser"] = $this->getService("context")->getBrowser();
		
		$this->info = $currentInfo;
		$this->getService("translation")->setLanguage($currentInfo["language"]);
	}
	
	/**
	 * Get min update date.
	 * 
	 * \returns DateTime instance
	 */
	private function getMinUpdateDate() {
		$seconds = $this->getService("config")
			->get("system", "sessionLifetime");
		
		return new \DateTime($seconds . " seconds ago");
	}
	
	/**
	 * Start or continue a session.
	 * 
	 * This will either start a new or continue an existing session. The IP
	 * address of the session must match the current one, and too old sessions
	 * are not continued.
	 * 
	 * \param $disableCookies Disable cookies (unit tests)
	 */
	public function startOrContinue($disableCookies = FALSE) {
		if (!$this->systemIsInstalled()) {
			return;
		}
		
		list($continue, $sessId) = $this->getSessionId();
		$ipAddr = $this->getService("context")->getIPAddress();
		$currentInfo = NULL;
		
		if ($continue) {
			$stmt = $this->getService("database")->newQuery()
				->select("s.*")->from("sessions", "s")
				->where("s.id = :sess_id")
				->andWhere("s.ip_addr = :ip_addr")
				->andWhere("s.updated >= :min_updated")
				->setParameters(array(
					":sess_id"     => $sessId, 
					":ip_addr"     => $ipAddr,
					":min_updated" => $this->getMinUpdateDate()
				), array(
					":min_updated" => \Doctrine\DBAL\Types\Type::DATETIME
				))
				->execute();
			
			if ($stmt->rowCount() == 0) {
				$stmt->closeCursor();
				$continue = FALSE;
				$sessId = $this->generateSessionId();
			} else {
				$currentInfo = $stmt->fetch(\PDO::FETCH_ASSOC);
				$stmt->closeCursor();
			}
		}
		
		if (!$continue) {
			return $this->newSession($sessId, $ipAddr, $disableCookies);
		}
		
		return $this->continueSession($currentInfo);
	}
	
	/**
	 * Get the session ID.
	 * 
	 * \returns %Session ID
	 * 
	 * \exception NotStartedException When session not started
	 */
	public function getId() {
		if (count($this->info) == 0) {
			throw new NotStartedException();
		}
		
		return $this->info["id"];
	}
	
	/**
	 * Get the request count.
	 * 
	 * \returns Request count
	 * 
	 * \exception NotStartedException When session not started
	 */
	public function getRequestCount() {
		if (count($this->info) == 0) {
			throw new NotStartedException();
		}
		
		return $this->info["requests"];
	}
	
	/**
	 * Get the start time.
	 * 
	 * \returns DateTime instance
	 * 
	 * \exception NotStartedException When session not started
	 */
	public function getStartTime() {
		if (count($this->info) == 0) {
			throw new NotStartedException();
		}
		
		return $this->info["started"];
	}
	
	/**
	 * Get the update time.
	 * 
	 * \returns DateTime instance
	 * 
	 * \exception NotStartedException When session not started
	 */
	public function getUpdateTime() {
		if (count($this->info) == 0) {
			throw new NotStartedException();
		}
		
		return $this->info["updated"];
	}
	
	/**
	 * Get the value for a key.
	 * 
	 * \param $key Key
	 * \returns Value
	 * 
	 * \exception NotStartedException When session not started
	 * \exception ValueNotFoundException When value not found
	 */
	public function getValue($key) {
		if (count($this->info) == 0) {
			throw new NotStartedException();
		}
		
		if (!isset($this->info["data"][$key])) {
			throw new ValueNotFoundException($key);
		}
		
		return $this->info["data"][$key];
	}
	
	/**
	 * Has a value for a key.
	 * 
	 * \param $key Key
	 * \returns Boolean
	 * 
	 * \exception NotStartedException When session not started
	 */
	public function hasValue($key) {
		try {
			$this->getValue($key);
		} catch (NotFoundException $e) {
			return FALSE;
		}
		
		return TRUE;
	}
	
	/**
	 * Set the value for a key.
	 * 
	 * \param $key Key
	 * \param $value Value
	 * 
	 * \exception NotStartedException When session not started
	 */
	public function setValue($key, $value) {
		if (count($this->info) == 0) {
			throw new NotStartedException();
		}
		
		$this->info["data"][$key] = $value;
	}
	
	/**
	 * Delete the value for a key.
	 * 
	 * \param $key Key
	 * 
	 * \exception NotStartedException When session not started
	 */
	public function deleteValue($key) {
		if (count($this->info) == 0) {
			throw new NotStartedException();
		}
		
		unset($this->info["data"][$key]);
	}
	
	/**
	 * Save a session.
	 * 
	 * This will enter the data in the database.
	 */
	public function save() {
		if (!$this->systemIsInstalled() || count($this->info) == 0) {
			return;
		}
		
		$aData = $this->info["data"];
		$this->info["data"] = $this->serialize($this->info["data"]);
		
		if ($this->new) {
			$this->getService("database")
				->insert("sessions", $this->info, TRUE);
			$this->new = FALSE;
		} else {		
			$this->getService("database")->update("sessions", array(
				"updated"  => $this->info["updated"],
				"requests" => $this->info["requests"],
				"browser"  => $this->info["browser"],
				"data"     => $this->info["data"],
			), array(
				"id"       => $this->info["id"],
			), TRUE);
		}
		
		$this->info["data"] = $aData;
	}
}
