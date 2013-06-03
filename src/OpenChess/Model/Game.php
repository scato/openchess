<?php

/**
 * A game of Chess
 *
 * @package OpenChess_Model
 */
class OpenChess_Model_Game extends Zend_Db_Table_Row_Abstract {
	const OUTCOME_NONE = 'GAME_OUTCOME_NONE';
	const OUTCOME_WHITE_WON = 'GAME_OUTCOME_WHITE_WON';
	const OUTCOME_BLACK_WON = 'GAME_OUTCOME_BLACK_WON';
	const OUTCOME_DRAW = 'GAME_OUTCOME_DRAW';
	
	const STATE_WHITE_TO_MOVE = 'GAME_STATE_WHITE_TO_MOVE';
	const STATE_BLACK_TO_MOVE = 'GAME_STATE_BLACK_TO_MOVE';
	const STATE_CHECKMATE = 'GAME_STATE_CHECKMATE';
	const STATE_STALEMATE = 'GAME_STATE_STALEMATE';
	const STATE_RESIGNATION = 'GAME_STATE_RESIGNATION';
	
	private $_id;
	private $_white;
	private $_black;
	private $_board;
	private $_outcome;
	private $_state;
	
	/**
	 * Set up the board and initialize the players, outcome and state
	 *
	 * @param OpenChess_Model_Player $white
	 * @param OpenChess_Model_Player $black
	 */
	public function __construct($white, $black) {
		$this->_white = $white;
		$this->_black = $black;
		$this->setup();
	}
	
	/**
	 * Set up the board and initialize the outcome and state
	 *
	 */
	public function setup() {
		$this->_outcome = self::OUTCOME_NONE;
		$this->_state = self::STATE_WHITE_TO_MOVE;
		
		$this->_board = new OpenChess_Model_Board();
		$this->_board->setup();
	}
	
	/**
	 * Get this games ID
	 *
	 * @return int
	 */
	public function getId() {
		return $this->_id;
	}
	
	/**
	 * Set this games ID
	 *
	 * @param int $id
	 * @return void
	 */
	public function setId($id) {
		$this->_id = $id;
	}
	
	/**
	 * Find the player that controls the pieces of this color
	 * 
	 * throws an UnknownColorException if a wrong color was passed
	 * 
	 * @param string $color
	 * @return OpenChess_Model_Player
	 * @throws OpenChess_Model_Exception_UnknownColorException
	 */
	public function findPlayerByColor($color) {
		switch($color) {
			case OpenChess_Model_Piece::COLOR_BLACK:
				return $this->_black;
			case OpenChess_Model_Piece::COLOR_WHITE:
				return $this->_white;
			default:
				throw new OpenChess_Model_Exception_UnknownColorException($color);
		}
	}
	
	/**
	 * Find the player that must make the next move or null if the game has ended
	 * 
	 * throws an InvalidStateException if the state of the game is not valid
	 * 
	 * @return OpenChess_Model_Player
	 * @throws OpenChess_Model_Exception_InvalidStateException
	 */
	public function findPlayerToMove() {
		switch($this->_state) {
			case OpenChess_Model_Game::STATE_BLACK_TO_MOVE:
				return $this->_black;
			case OpenChess_Model_Game::STATE_WHITE_TO_MOVE:
				return $this->_white;
			case OpenChess_Model_Game::STATE_CHECKMATE:
			case OpenChess_Model_Game::STATE_STALEMATE:
			case OpenChess_Model_Game::STATE_RESIGNATION:
				return null;
			default:
				throw new OpenChess_Model_Exception_InvalidStateException($this->_state);
		}
	}
	
	/**
	 * Get all valid actions for a player
	 * 
	 * @param OpenChess_Model_Player $player
	 * @return array
	 */
	public function getValidActions($player) {
		if($player === $this->findPlayerToMove()) {
			return array(
				new OpenChess_Model_Action($this, $player, OpenChess_Model_Action::TYPE_RESIGN)
			);
		} else {
			return array();
		}
	}
	
	/**
	 * Find one valid action for a player by type
	 * 
	 * @param OpenChess_Model_Player $player
	 * @param string $type
	 * @return OpenChess_Model_Action
	 */
	public function findValidActionByType($player, $type) {
		$actions = $this->getValidActions($player);
		
		foreach($actions as $action) {
			if($action->getType() === $type) {
				return $action;
			}
		}
		
		return null;
	}
	
	/**
	 * Get the player controlling the white pieces
	 * 
	 * @return OpenChess_Model_Player
	 */
	public function getWhite() {
		return $this->_white;
	}

	/**
	 * Get the player controlling the black pieces
	 * 
	 * @return OpenChess_Model_Player
	 */
	public function getBlack() {
		return $this->_black;
	}
	
	/**
	 * Get the board on which the game is played
	 * 
	 * @return OpenChess_Model_Board
	 */
	public function getBoard() {
		return $this->_board;
	}
	
	/**
	 * Get the outcome of the game
	 * 
	 * one of OUTCOME_NONE, OUTCOME_WHITE_WON, OUTCOME_BLACK_WON and OUTCOME_DRAW
	 * equals OUTCOME_NONE if the game is in progress
	 * 
	 * @return string
	 */
	public function getOutcome() {
		return $this->_outcome;
	}
	
	/**
	 * Get the state of the game
	 * 
	 * one of STATE_WHITE_TO_MOVE, STATE_BLACK_TO_MOVE and STATE_ENDED
	 * 
	 * @return string
	 */
	public function getState() {
		return $this->_state;
	}
	
	/**
	 * Set the board on which the game is played
	 * 
	 * @param OpenChess_Model_Board $board
	 * @return void
	 */
	public function setBoard($board) {
		$this->_board = $board;
	}
	
	/**
	 * Set the state of the game
	 * 
	 * one of STATE_WHITE_TO_MOVE, STATE_BLACK_TO_MOVE and STATE_ENDED
	 * 
	 * @param string $state
	 * @return void
	 */
	public function setState($state) {
		$this->_state = $state;
	}
	
	/**
	 * Set the outcome of the game
	 * 
	 * one of OUTCOME_WHITE_WON, OUTCOME_BLACK_WON and OUTCOME_DRAW
	 * 
	 * @param string $state
	 * @return void
	 */
	public function setOutcome($outcome) {
		$this->_outcome = $outcome;
	}
	
	/**
	 * Update the state of the game depending on the state of the board
	 * 
	 * @return void
	 */
	public function updateState() {
		$board = $this->getBoard();
		
		$move = $board->getLastMove();
		$color = $move->getPiece()->getColor();
		
		if($color === OpenChess_Model_Piece::COLOR_BLACK) {
			$this->setState(self::STATE_WHITE_TO_MOVE);
			$color = OpenChess_Model_Piece::COLOR_WHITE;
		} else {
			$this->setState(self::STATE_BLACK_TO_MOVE);
			$color = OpenChess_Model_Piece::COLOR_BLACK;
		}
		
		$pieces = $this->_board->getPieces();
		$playerHasMoves = false;
		foreach($pieces as $piece) {
			if($piece->getColor() === $color && count($piece->getValidMoves()) > 0) {
				$playerHasMoves = true;
				break;
			}
		}
		
		if(!$playerHasMoves) {
			$king = $this->_board->findKingByColor($color);
			if($king->getInCheck()) {
				$this->setState(self::STATE_CHECKMATE);
				
				if($color === OpenChess_Model_Piece::COLOR_BLACK) {
					$this->setOutcome(self::OUTCOME_WHITE_WON);
				} else {
					$this->setOutcome(self::OUTCOME_BLACK_WON);
				}
			} else {
				$this->setState(self::STATE_STALEMATE);
				$this->setOutcome(self::OUTCOME_DRAW);
			}
		}
	}
}
