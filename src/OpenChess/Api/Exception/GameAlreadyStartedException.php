<?php

class OpenChess_Api_Exception_GameAlreadyStartedException extends Exception {
	public function __construct() {
		parent::__construct("Game was already started");
	}
}
