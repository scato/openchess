<?php

/**
 * A player was attempted to be created, but the current session already has a player
 *
 * @package OpenChess_Api
 * @subpackage OpenChess_Api_Exception
 */
class OpenChess_Api_Exception_PlayerAlreadyCreatedException extends Exception {
	public function __construct() {
		parent::__construct("Player already created");
	}
}
