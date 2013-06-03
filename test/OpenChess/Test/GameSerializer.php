<?php

class OpenChess_Test_GameSerializer extends PHPUnit_Framework_TestCase {
	private $_white;
	private $_black;
	
	/**
	 * @var OpenChess_Model_Game
	 */
	private $_game;
	
	/**
	 * @var OpenChess_Model_GameSerializer
	 */
	private $_gameSerializer;
	
	public function setUp() {
		$this->_white = new OpenChess_Model_Player('Barry White');
		$this->_black = new OpenChess_Model_Player('Frank Black');
		
		$this->_game = new OpenChess_Model_Game($this->_white, $this->_black);
		$this->_game->setId(md5(time()));
		
		$this->_gameSerializer = new OpenChess_Model_GameSerializer();
	}
	
	public function testSerialize() {
		$result = $this->_gameSerializer->serialize($this->_game, $this->_white);
		
		$this->assertType('array', $result);
		
		$this->assertArrayHasKey('class', $result);
		$this->assertEquals('Game', $result['class']);
		
		$this->assertArrayHasKey('id', $result);
		$this->assertType('string', $result['id']);
		$this->assertEquals($this->_game->getId(), $result['id']);
		
		$this->assertArrayHasKey('white', $result);
		$this->assertType('array', $result['white']);
		$this->assertArrayHasKey('name', $result['white']);
		$this->assertEquals($this->_white->getName(), $result['white']['name']);
		
		$this->assertArrayHasKey('black', $result);
		$this->assertType('array', $result['black']);
		$this->assertArrayHasKey('name', $result['black']);
		$this->assertEquals($this->_black->getName(), $result['black']['name']);
		
		$this->assertArrayHasKey('pieces', $result);
		$this->assertType('array', $result['pieces']);
		
		$this->assertArrayHasKey('validActions', $result);
		$this->assertType('array', $result['validActions']);
		
		$this->assertArrayHasKey('state', $result);
		$this->assertType('string', $result['state']);
		
		$this->assertArrayHasKey('outcome', $result);
		$this->assertType('string', $result['outcome']);
		
		$this->assertArrayHasKey('version', $result);
		$this->assertType('string', $result['version']);
	}
	
	public function testSerializeFinishedGame() {
		$this->_game->setState(OpenChess_Model_Game::STATE_RESIGNATION);
		
		$this->_gameSerializer->serialize($this->_game, $this->_white);
	}
}
