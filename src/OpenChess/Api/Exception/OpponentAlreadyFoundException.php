<?php

/**
 * A opponent was attempted to be found, but the current session already has an opponent
 *
 * @package OpenChess_Api
 * @subpackage OpenChess_Api_Exception
 */
class OpenChess_Api_Exception_OpponentAlreadyFoundException extends Exception {
	public function __construct() {
		parent::__construct("Opponent already found");
	}
}
