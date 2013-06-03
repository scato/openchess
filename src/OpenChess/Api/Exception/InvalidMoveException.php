<?php

/**
 * A player tried to make move that is not valid
 *
 * @package OpenChess_Api
 * @subpackage OpenChess_Api_Exception
 */
class OpenChess_Api_Exception_InvalidMoveException extends Exception {
	public function __construct() {
		parent::__construct("Invalid move");
	}
}
