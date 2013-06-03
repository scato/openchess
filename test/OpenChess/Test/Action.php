<?php

class OpenChess_Test_Action extends PHPUnit_Framework_TestCase {
	private $_white;
	private $_black;
	
	/**
	 * @var OpenChess_Model_Game
	 */
	private $_game;
	
	private function _getRandomPlayer() {
		if(rand(0, 1) == 0) {
			return $this->_white;
		} else {
			return $this->_black;
		}
	}
	
	public function setUp() {
		$this->_white = new OpenChess_Model_Player('Barry White');
		$this->_black = new OpenChess_Model_Player('Frank Black');
		
		$this->_game = new OpenChess_Model_Game($this->_white, $this->_black);
	}
	
	public function testGetGame() {
		$player = $this->_getRandomPlayer();
		$type = OpenChess_Model_Action::TYPE_RESIGN;
		
		$action = new OpenChess_Model_Action($this->_game, $player, $type);
		
		$this->assertEquals($this->_game, $action->getGame());
	}
	
	public function testGetPlayer() {
		$player = $this->_getRandomPlayer();
		$type = OpenChess_Model_Action::TYPE_RESIGN;
		
		$action = new OpenChess_Model_Action($this->_game, $player, $type);
		
		$this->assertEquals($player, $action->getPlayer());
	}
	
	public function testGetType() {
		$player = $this->_getRandomPlayer();
		$type = OpenChess_Model_Action::TYPE_RESIGN;
		
		$action = new OpenChess_Model_Action($this->_game, $player, $type);
		
		$this->assertEquals($type, $action->getType());
	}
	
	public function testResignAction() {
		$type = OpenChess_Model_Action::TYPE_RESIGN;
		
		$action = new OpenChess_Model_Action($this->_game, $this->_black, $type);
		
		$this->assertEquals(OpenChess_Model_Game::OUTCOME_NONE, $this->_game->getOutcome());
		
		$action->take();
		
		$this->assertEquals(OpenChess_Model_Game::OUTCOME_WHITE_WON, $this->_game->getOutcome());
		
		$this->_game->setOutcome(OpenChess_Model_Game::OUTCOME_NONE);
		$action = new OpenChess_Model_Action($this->_game, $this->_white, $type);
		
		$this->assertEquals(OpenChess_Model_Game::OUTCOME_NONE, $this->_game->getOutcome());
		
		$action->take();
		
		$this->assertEquals(OpenChess_Model_Game::OUTCOME_BLACK_WON, $this->_game->getOutcome());
	}
}
