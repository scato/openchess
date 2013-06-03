<?php

/**
 * A session ID was given for a session, but this ID was not found
 *
 * @package OpenChess_Api
 * @subpackage OpenChess_Api_Exception
 */
class OpenChess_Api_Exception_SessionNotFoundException extends Exception {
	public function __construct() {
		parent::__construct("Session was not found");
	}
}
