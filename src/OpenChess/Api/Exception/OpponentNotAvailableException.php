<?php

/**
 * An opponent was searched for, but no opponent was available
 *
 * @package OpenChess_Api
 * @subpackage OpenChess_Api_Exception
 */
class OpenChess_Api_Exception_OpponentNotAvailableException extends Exception {
	public function __construct() {
		parent::__construct("No opponent is available at the moment");
	}
}
