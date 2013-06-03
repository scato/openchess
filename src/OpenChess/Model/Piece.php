<?php

/**
 * A piece on the board
 *
 * @package OpenChess_Model
 */
class OpenChess_Model_Piece {
	const COLOR_WHITE = 'PIECE_COLOR_WHITE';
	const COLOR_BLACK = 'PIECE_COLOR_BLACK';
	
	const TYPE_NONE = 'PIECE_TYPE_NONE';
	const TYPE_PAWN = 'PIECE_TYPE_PAWN';
	const TYPE_ROOK = 'PIECE_TYPE_ROOK';
	const TYPE_KNIGHT = 'PIECE_TYPE_KNIGHT';
	const TYPE_BISHOP = 'PIECE_TYPE_BISHOP';
	const TYPE_QUEEN = 'PIECE_TYPE_QUEEN';
	const TYPE_KING = 'PIECE_TYPE_KING';
	
	protected $_advancedTwoSquares = false;
	protected $_color;
	protected $_hasMoved = false;
	protected $_position;
	protected $_type = OpenChess_Model_Piece::TYPE_NONE;
	
	/**
	 * Factory method
	 * 
	 * @param string $color
	 * @param string $type
	 * @param OpenChess_Model_Square $position
	 * @return OpenChess_Model_Piece
	 */
	public static function createPiece($color, $type, $position) {
		switch($type) {
			case OpenChess_Model_Piece::TYPE_PAWN:
				return new OpenChess_Model_Piece_Pawn($color, $position);
			case OpenChess_Model_Piece::TYPE_ROOK:
				return new OpenChess_Model_Piece_Rook($color, $position);
			case OpenChess_Model_Piece::TYPE_KNIGHT:
				return new OpenChess_Model_Piece_Knight($color, $position);
			case OpenChess_Model_Piece::TYPE_BISHOP:
				return new OpenChess_Model_Piece_Bishop($color, $position);
			case OpenChess_Model_Piece::TYPE_QUEEN:
				return new OpenChess_Model_Piece_Queen($color, $position);
			case OpenChess_Model_Piece::TYPE_KING:
				return new OpenChess_Model_Piece_King($color, $position);
			default:
				throw new OpenChess_Model_Exception_UnknownTypeException($type);
		}
	}
	
	/**
	 * Create a piece and place it on a certain square
	 * 
	 * @param string $color
	 * @param OpenChess_Model_Square $position
	 */
	protected function __construct($color, $position) {
		$this->_color = $color;
		$this->_position = $position;
		
		$this->_position->setOccupier($this);
	}
	
	/**
	 * Get boolean indicating if a pawn has just moved two squares during its last move, in which case it can be captured en passant
	 * 
	 * @return boolean
	 */
	public function getAdvancedTwoSquares() {
		return $this->_advancedTwoSquares;
	}
	
	/**
	 * Set boolean indicating if a pawn has just moved two squares during its last move, in which case it can be captured en passant
	 * 
	 * @param boolean $advancedTwoSquares
	 */
	public function setAdvancedTwoSquares($advancedTwoSquares) {
		$this->_advancedTwoSquares = $advancedTwoSquares;
	}
	
	/**
	 * Get this pieces color
	 * 
	 * one of COLOR_WHITE and COLOR_BLACK
	 *
	 * @return string
	 */
	public function getColor() {
		return $this->_color;
	}
	
	/**
	 * Get boolean indicating if this piece has moved
	 *
	 * @return boolean
	 */
	public function getHasMoved() {
		return $this->_hasMoved;
	}
	
	/**
	 * Get the square that this piece occupies
	 *
	 * @return OpenChess_Model_Square
	 */
	public function getPosition() {
		return $this->_position;
	}
	
	/**
	 * Set the square that this piece occupies
	 *
	 * @param OpenChess_Model_Square $position
	 * @param boolean $advancedTwoSquares
	 */
	public function moveTo($position, $advancedTwoSquares = false) {
		$this->_position->setOccupier(null);
		
		$this->_position = $position;
		$this->_hasMoved = true;
		$this->_advancedTwoSquares = $advancedTwoSquares;
		
		$this->_position->setOccupier($this);
	}
	
	/**
	 * Get this pieces type
	 * 
	 * one of TYPE_PAWN, TYPE_ROOK, TYPE_KNIGHT, TYPE_BISHOP, TYPE_QUEEN or TYPE_KING
	 *
	 * @return string
	 */
	public function getType() {
		return $this->_type;
	}
	
	/**
	 * Get a list of valid moves
	 *
	 * @return array
	 */
	public function getValidMoves() {
		return $this->_getMoves(true);
	}
	
	/**
	 * Get a list of possible moves, this includes the ones that leave our king in check
	 * 
	 * @param boolean $protectKing
	 * @return array
	 */
	public function getPossibleMoves() {
		return $this->_getMoves(false);
	}
	
	protected function _getMoves($protectKing) {
		return array();
	}
	
	/**
	 * Get a valid move by destination
	 * 
	 * returns null if none is available
	 * 
	 * @return OpenChess_Model_Move
	 */
	public function findValidMoveByDestination($destination) {
		$moves = $this->getValidMoves();
		
		foreach($moves as $move) {
			if($move->getDestination() === $destination) {
				return $move;
			}
		}
		
		return null;
	}
	
	/**
	 * Find out whether this piece is in check
	 * 
	 * TODO: use backtracing instead of checking all the pieces???
	 * 
	 * @return boolean
	 */
	public function getInCheck() {
		foreach($this->_position->getBoard()->getPieces() as $piece) {
			foreach($piece->getPossibleMoves() as $move) {
				if($move->captures($this)) {
					return true;
				}
			}
		}
		
		return false;
	}
}
