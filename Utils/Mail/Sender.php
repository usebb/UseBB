<?php

namespace UseBB\Utils\Mail;

use UseBB\System\ServiceAccessor;
use UseBB\Utils\Events\Event;

/**
 * Email sending class.
 * 
 * Sends email messages with support for multiple recipients, CC, BCC and UTF-8.
 * 
 * \author Dietrich Moerman
 */
class Sender extends ServiceAccessor {
	/**
	 * Combine recipients into single string.
	 * 
	 * \param $recipients Recipients
	 * \returns String
	 */
	private function combineRecipients(array &$recipients) {
		$str = "";
		
		foreach ($recipients as $k => $v) {
			if (is_int($k)) {
				$str .= $v . ", ";
				
				continue;
			}
			
			$str .= $this->getService("string")->toMIME($v) . 
				" <" . $k . ">, ";
		}
		
		return substr($str, 0, -2);
	}
	
	/**
	 * Send a new message.
	 * 
	 * Available options are:
	 * \li \c cc: array for CC recipients
	 * \li \c bcc: array for BCC recipients
	 * 
	 * \param $fromName From name (optional)
	 * \param $fromAddress From address
	 * \param $to Array with email addresses or name/address combinations
	 * \param $subject Subject
	 * \param $message Message
	 * \param $options Options
	 * \param $return Return \c mail() parameters instead of sending (unit tests)
	 */
	public function send($fromName, $fromAddress, array $to, $subject, 
		$message, array $options = array(), $return = FALSE) {
		$strOps = $this->getService("string");
		$config = $this->getService("config")->forModule("system");
		
		// Standard fields.
		
		$from = !empty($fromName)
			? $strOps->toMIME($fromName) . " <" . $fromAddress . ">"
			: $fromAddress;
		$to = $this->combineRecipients($to);
		$subject = $strOps->toMIME($subject);
		
		// Headers.
		
		$headers = array(
			"MIME-Version: 1.0",
			"Content-Type: text/plain; charset=UTF-8",
			"Content-Transfer-Encoding: 8bit",
			"From: " . $from
		);
		
		if (isset($options["cc"]) && is_array($options["cc"])) {
			$headers[] = "Cc: " . $this->combineRecipients($options["cc"]);
		}
		
		if (isset($options["bcc"]) && is_array($options["bcc"])) {
			$headers[] = "Bcc: " . $this->combineRecipients($options["bcc"]);
		}
		
		$headers = implode("\n", $headers);
		
		// Parameters.
		
		$params = array();
		
		if ($config->get("mailEnableSenderParameter")) {
			$params[] = "-f" . $fromAddress;
		}
		
		$params = implode(" ", $params);
		
		// Send.
		
		$mailParams = array($to, $subject, $message, $headers, $params);
		
		if ($return) {
			return $mailParams;
		}
		
		$result = call_user_func_array("mail", $mailParams);
		
		if ($result === FALSE) {
			$this->log(Event::TYPE_WARNING, "Unable to send email.");
		}
	}
}
