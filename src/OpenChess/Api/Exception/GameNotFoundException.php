<?php

/**
 * A game ID was given for a game, but this ID was not found
 *
 * @package OpenChess_Api
 * @subpackage OpenChess_Api_Exception
 */
class OpenChess_Api_Exception_GameNotFoundException extends Exception {
	public function __construct() {
		parent::__construct("Game not found");
	}
}
