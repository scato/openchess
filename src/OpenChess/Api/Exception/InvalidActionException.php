<?php

/**
 * A player tried to take an action that is not valid
 *
 * @package OpenChess_Api
 * @subpackage OpenChess_Api_Exception
 */
class OpenChess_Api_Exception_InvalidActionException extends Exception {
	public function __construct() {
		parent::__construct("Invalid action");
	}
}
