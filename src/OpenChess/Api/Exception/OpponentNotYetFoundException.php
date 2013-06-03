<?php

/**
 * A call was made which requires an opponent, but no opponent was found yet
 *
 * @package OpenChess_Api
 * @subpackage OpenChess_Api_Exception
 */
class OpenChess_Api_Exception_OpponentNotYetFoundException extends Exception {
	public function __construct() {
		parent::__construct("Opponent not yet found");
	}
}
