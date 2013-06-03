<?php

class OpenChess_Test_Player extends PHPUnit_Framework_TestCase {
	public function testGetName() {
		$name = 'Kasparov';
		$player = new OpenChess_Model_Player($name);
		
		$this->assertEquals($name, $player->getName());
	}
}
