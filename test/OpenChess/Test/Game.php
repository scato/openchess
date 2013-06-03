<?php

class OpenChess_Test_Game extends PHPUnit_Framework_TestCase {
	private $_white;
	private $_black;
	
	/**
	 * @var OpenChess_Model_Game
	 */
	private $_game;
	
	public function setUp() {
		$this->_white = new OpenChess_Model_Player('Barry White');
		$this->_black = new OpenChess_Model_Player('Frank Black');
		
		$this->_game = new OpenChess_Model_Game($this->_white, $this->_black);
	}
	
	public function testId() {
		$id = rand(1, 100);
		$this->_game->setId($id);
		
		$this->assertEquals($id, $this->_game->getId());
	}
	
	public function testGetSetState() {
		$this->assertEquals(OpenChess_Model_Game::STATE_WHITE_TO_MOVE, $this->_game->getState());
		
		$state = OpenChess_Model_Game::STATE_BLACK_TO_MOVE;
		$this->_game->setState($state);
		
		$this->assertEquals($state, $this->_game->getState());
	}
	
	public function testGetWhite() {
		$this->assertSame($this->_white, $this->_game->getWhite());
	}
	
	public function testGetBlack() {
		$this->assertSame($this->_black, $this->_game->getBlack());
	}
	
	public function testGetSetBoard() {
		$this->assertType('OpenChess_Model_Board', $this->_game->getBoard());
		
		$this->_game->setBoard(null);
		
		$this->assertNull($this->_game->getBoard());
	}
	
	public function testGetSetOutcome() {
		$this->assertEquals(OpenChess_Model_Game::OUTCOME_NONE, $this->_game->getOutcome());
		
		$outcome = OpenChess_Model_Game::OUTCOME_BLACK_WON;
		$this->_game->setOutcome($outcome);
		
		$this->assertEquals($outcome, $this->_game->getOutcome());
	}
	
	public function testFindPlayerByColor() {
		$color = OpenChess_Model_Piece::COLOR_BLACK;
		$player = $this->_game->findPlayerByColor($color);
		
		$this->assertSame($this->_black, $player);
		
		$color = OpenChess_Model_Piece::COLOR_WHITE;
		$player = $this->_game->findPlayerByColor($color);
		
		$this->assertSame($this->_white, $player);
	}
	
	public function testFindPlayerToMove() {
		$state = OpenChess_Model_Game::STATE_BLACK_TO_MOVE;
		$this->_game->setState($state);
		$player = $this->_game->findPlayerToMove();
		
		$this->assertSame($this->_black, $player);
		
		$state = OpenChess_Model_Game::STATE_WHITE_TO_MOVE;
		$this->_game->setState($state);
		$player = $this->_game->findPlayerToMove();
		
		$this->assertSame($this->_white, $player);
	}
	
	public function testGetValidActions() {
		$actions = $this->_game->getValidActions($this->_white);
		
		$this->assertEquals(1, count($actions));
		$this->assertType('OpenChess_Model_Action', $actions[0]);
		$this->assertEquals(OpenChess_Model_Action::TYPE_RESIGN, $actions[0]->getType());
	}
	
	public function testFindValidActionByType() {
		$type = OpenChess_Model_Action::TYPE_RESIGN;
		$action = $this->_game->findValidActionByType($this->_white, $type);
		
		$this->assertType('OpenChess_Model_Action', $action);
		$this->assertEquals($type, $action->getType());
	}
	
	public function testBlackToMoveAfterWhiteMoves() {
		$board = $this->_game->getBoard();
		$piece = $board->findPieceByPosition('e', 2);
		$destination = $board->findSquareByPosition('e', 4);
		
		$move = $piece->findValidMoveByDestination($destination);
		$move->make();
		$this->_game->updateState();
		$player = $this->_game->findPlayerToMove();
		
		$this->assertSame($this->_black, $player);
	}
	
	private function _makeMove($posFile, $posRank, $destFile, $destRank) {
		$board = $this->_game->getBoard();
		$piece = $board->findPieceByPosition($posFile, $posRank);
		$destination = $board->findSquareByPosition($destFile, $destRank);
		$move = $piece->findValidMoveByDestination($destination);
		$move->make();
		$this->_game->updateState();
	}
	
	public function testCheckmate() {
		// Fool's Mate
		
		// 1. f3 e5
		$this->_makeMove('f', 2, 'f', 3);
		$this->_makeMove('e', 7, 'e', 5);
		// 2. g4 Qh4#
		$this->_makeMove('g', 2, 'g', 4);
		$this->_makeMove('d', 8, 'h', 4);
		
		$this->assertEquals(OpenChess_Model_Game::STATE_CHECKMATE, $this->_game->getState());
		$this->assertEquals(OpenChess_Model_Game::OUTCOME_BLACK_WON, $this->_game->getOutcome());
	}
		
	public function testStalemate() {
		// Sam Loyd's Stalemate
		
		// 1.e3 a5
		$this->_makeMove('e', 2, 'e', 3);
		$this->_makeMove('a', 7, 'a', 5);
		// 2.Qh5 Ra6
		$this->_makeMove('d', 1, 'h', 5);
		$this->_makeMove('a', 8, 'a', 6);
		// 3.Qxa5 h5
		$this->_makeMove('h', 5, 'a', 5);
		$this->_makeMove('h', 7, 'h', 5);
		// 4.Qxc7 Rah6
		$this->_makeMove('a', 5, 'c', 7);
		$this->_makeMove('a', 6, 'h', 6);
		// 5.h4 f6
		$this->_makeMove('h', 2, 'h', 4);
		$this->_makeMove('f', 7, 'f', 6);
		// 6.Qxd7+ Kf7
		$this->_makeMove('c', 7, 'd', 7);
		$this->_makeMove('e', 8, 'f', 7);
		// 7.Qxb7 Qd3
		$this->_makeMove('d', 7, 'b', 7);
		$this->_makeMove('d', 8, 'd', 3);
		// 8.Qxb8 Qh7
		$this->_makeMove('b', 7, 'b', 8);
		$this->_makeMove('d', 3, 'h', 7);
		// 9.Qxc8 Kg6
		$this->_makeMove('b', 8, 'c', 8);
		$this->_makeMove('f', 7, 'g', 6);
		// 10.Qe6
		$this->_makeMove('c', 8, 'e', 6);
		
		$this->assertEquals(OpenChess_Model_Game::STATE_STALEMATE, $this->_game->getState());
		$this->assertEquals(OpenChess_Model_Game::OUTCOME_DRAW, $this->_game->getOutcome());
	}
	
	public function testResign() {
		$player = $this->_game->getWhite();
		$type = OpenChess_Model_Action::TYPE_RESIGN;
		$action = $this->_game->findValidActionByType($player, $type);
		
		$action->take();
		
		$this->assertEquals(OpenChess_Model_Game::STATE_RESIGNATION, $this->_game->getState());
		$this->assertEquals(OpenChess_Model_Game::OUTCOME_BLACK_WON, $this->_game->getOutcome());
	}
}
