<?php

/**
 * A piece was attempted to be created, but the type is unknown
 * 
 * @package OpenChess_Model
 * @subpackage OpenChess_Model_Exception
 */
class OpenChess_Model_Exception_UnknownTypeException extends Exception {
	public function __construct($type) {
		parent::__construct("Unknown type `$type`", $code);
	}
}
