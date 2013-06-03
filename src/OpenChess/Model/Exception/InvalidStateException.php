<?php

/**
 * The state was attempted to be changed to something invalid or the game apears to be in an invalid state
 * 
 * @package OpenChess_Model
 * @subpackage OpenChess_Model_Exception
 */
class OpenChess_Model_Exception_InvalidStateException extends Exception {
	public function __construct($state) {
		parent::__construct("Unknown state `$state`", $code);
	}
}
