<?php

/**
 * A call was made which requires a player, but no player was created yet
 *
 * @package OpenChess_Api
 * @subpackage OpenChess_Api_Exception
 */
class OpenChess_Api_Exception_PlayerNotYetCreatedException extends Exception {
	public function __construct() {
		parent::__construct("Player not yet created");
	}
}
