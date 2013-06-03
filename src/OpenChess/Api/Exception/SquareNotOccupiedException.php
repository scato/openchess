<?php

/**
 * The square is expected to be occupied, but it's empty
 *
 * @package OpenChess_Api
 * @subpackage OpenChess_Api_Exception
 */
class OpenChess_Api_Exception_SquareNotOccupiedException extends Exception {
	public function __construct() {
		parent::__construct("Square not occupied");
	}
}
