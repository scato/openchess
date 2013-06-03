<?php

/**
 * A player was attempted to be found by color, but the color is unknown
 * 
 * @package OpenChess_Model
 * @subpackage OpenChess_Model_Exception
 */
class OpenChess_Model_Exception_UnknownColorException extends Exception {
	public function __construct($color) {
		parent::__construct("Unknown color `$color`", $code);
	}
}
