<?php

class OpenChess_Test_PlayerSerializer extends PHPUnit_Framework_TestCase {
	/**
	 * @var OpenChess_Model_Player
	 */
	private $_player;
	
	/**
	 * @var OpenChess_Model_PlayerSerializer
	 */
	private $_playerSerializer;
	
	public function setUp() {
		$this->_player = new OpenChess_Model_Player('Chekov');
		$this->_playerSerializer = new OpenChess_Model_PlayerSerializer();
	}
	
	public function testSerialize() {
		$result = $this->_playerSerializer->serialize($this->_player);
		
		$this->assertType('array', $result);
		
		$this->assertArrayHasKey('class', $result);
		$this->assertEquals('Player', $result['class']);
		
		$this->assertArrayHasKey('id', $result);
		$this->assertEquals($this->_player->getId(), $result['id']);
		
		$this->assertArrayHasKey('name', $result);
		$this->assertEquals($this->_player->getName(), $result['name']);
	}
}
