<?php

/**
 * The board for a game of Chess
 *
 * @package OpenChess_Model
 */
class OpenChess_Model_Board {
	private $_squares;
	private $_pieces;
	private $_lastMove = null;
	
	/**
	 * Create a board with 64 squares
	 */
	public function __construct() {
		$files = array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h');
		$ranks = array(1, 2, 3, 4, 5, 6, 7, 8);
		
		$this->_squares = array();
		
		foreach($files as $file) {
			$this->_squares[$file] = array();
			
			foreach($ranks as $rank) {
				$this->_squares[$file][$rank] = new OpenChess_Model_Square($this, $file, $rank);
			}
		}
		
		$this->_pieces = array();
	}
	
	private function setupPiece($color, $type, $file, $rank) {
		$position = $this->findSquareByPosition($file, $rank);
		$piece = OpenChess_Model_Piece::createPiece($color, $type, $position);
		$this->addPiece($piece);
	}
	
	/**
	 * Set up all the pieces
	 *
	 * http://en.wikipedia.org/wiki/Rules_of_chess#Initial_setup
	 */
	public function setup() {
		$this->setupPiece(OpenChess_Model_Piece::COLOR_WHITE, OpenChess_Model_Piece::TYPE_ROOK,   'a', 1);
		$this->setupPiece(OpenChess_Model_Piece::COLOR_WHITE, OpenChess_Model_Piece::TYPE_KNIGHT, 'b', 1);
		$this->setupPiece(OpenChess_Model_Piece::COLOR_WHITE, OpenChess_Model_Piece::TYPE_BISHOP, 'c', 1);
		$this->setupPiece(OpenChess_Model_Piece::COLOR_WHITE, OpenChess_Model_Piece::TYPE_QUEEN,  'd', 1);
		$this->setupPiece(OpenChess_Model_Piece::COLOR_WHITE, OpenChess_Model_Piece::TYPE_KING,   'e', 1);
		$this->setupPiece(OpenChess_Model_Piece::COLOR_WHITE, OpenChess_Model_Piece::TYPE_BISHOP, 'f', 1);
		$this->setupPiece(OpenChess_Model_Piece::COLOR_WHITE, OpenChess_Model_Piece::TYPE_KNIGHT, 'g', 1);
		$this->setupPiece(OpenChess_Model_Piece::COLOR_WHITE, OpenChess_Model_Piece::TYPE_ROOK,   'h', 1);
				
		$this->setupPiece(OpenChess_Model_Piece::COLOR_WHITE, OpenChess_Model_Piece::TYPE_PAWN, 'a', 2);
		$this->setupPiece(OpenChess_Model_Piece::COLOR_WHITE, OpenChess_Model_Piece::TYPE_PAWN, 'b', 2);
		$this->setupPiece(OpenChess_Model_Piece::COLOR_WHITE, OpenChess_Model_Piece::TYPE_PAWN, 'c', 2);
		$this->setupPiece(OpenChess_Model_Piece::COLOR_WHITE, OpenChess_Model_Piece::TYPE_PAWN, 'd', 2);
		$this->setupPiece(OpenChess_Model_Piece::COLOR_WHITE, OpenChess_Model_Piece::TYPE_PAWN, 'e', 2);
		$this->setupPiece(OpenChess_Model_Piece::COLOR_WHITE, OpenChess_Model_Piece::TYPE_PAWN, 'f', 2);
		$this->setupPiece(OpenChess_Model_Piece::COLOR_WHITE, OpenChess_Model_Piece::TYPE_PAWN, 'g', 2);
		$this->setupPiece(OpenChess_Model_Piece::COLOR_WHITE, OpenChess_Model_Piece::TYPE_PAWN, 'h', 2);
		
		$this->setupPiece(OpenChess_Model_Piece::COLOR_BLACK, OpenChess_Model_Piece::TYPE_ROOK,   'a', 8);
		$this->setupPiece(OpenChess_Model_Piece::COLOR_BLACK, OpenChess_Model_Piece::TYPE_KNIGHT, 'b', 8);
		$this->setupPiece(OpenChess_Model_Piece::COLOR_BLACK, OpenChess_Model_Piece::TYPE_BISHOP, 'c', 8);
		$this->setupPiece(OpenChess_Model_Piece::COLOR_BLACK, OpenChess_Model_Piece::TYPE_QUEEN,  'd', 8);
		$this->setupPiece(OpenChess_Model_Piece::COLOR_BLACK, OpenChess_Model_Piece::TYPE_KING,   'e', 8);
		$this->setupPiece(OpenChess_Model_Piece::COLOR_BLACK, OpenChess_Model_Piece::TYPE_BISHOP, 'f', 8);
		$this->setupPiece(OpenChess_Model_Piece::COLOR_BLACK, OpenChess_Model_Piece::TYPE_KNIGHT, 'g', 8);
		$this->setupPiece(OpenChess_Model_Piece::COLOR_BLACK, OpenChess_Model_Piece::TYPE_ROOK,   'h', 8);
		
		$this->setupPiece(OpenChess_Model_Piece::COLOR_BLACK, OpenChess_Model_Piece::TYPE_PAWN, 'a', 7);
		$this->setupPiece(OpenChess_Model_Piece::COLOR_BLACK, OpenChess_Model_Piece::TYPE_PAWN, 'b', 7);
		$this->setupPiece(OpenChess_Model_Piece::COLOR_BLACK, OpenChess_Model_Piece::TYPE_PAWN, 'c', 7);
		$this->setupPiece(OpenChess_Model_Piece::COLOR_BLACK, OpenChess_Model_Piece::TYPE_PAWN, 'd', 7);
		$this->setupPiece(OpenChess_Model_Piece::COLOR_BLACK, OpenChess_Model_Piece::TYPE_PAWN, 'e', 7);
		$this->setupPiece(OpenChess_Model_Piece::COLOR_BLACK, OpenChess_Model_Piece::TYPE_PAWN, 'f', 7);
		$this->setupPiece(OpenChess_Model_Piece::COLOR_BLACK, OpenChess_Model_Piece::TYPE_PAWN, 'g', 7);
		$this->setupPiece(OpenChess_Model_Piece::COLOR_BLACK, OpenChess_Model_Piece::TYPE_PAWN, 'h', 7);
	}
	
	/**
	 * Find a square by its position
	 * 
	 * @param string $file
	 * @param int $rank
	 * @return OpenChess_Model_Square
	 */
	public function findSquareByPosition($file, $rank) {
		return $this->_squares[$file][$rank];
	}
	
	/**
	 * Find a piece by its position
	 *
	 * @param string $file
	 * @param int $rank
	 * @return OpenChess_Model_Piece
	 */
	public function findPieceByPosition($file, $rank) {
		return $this->_squares[$file][$rank]->getOccupier();
	}
	
	/**
	 * Find a king by its color
	 *
	 * @param string $color
	 * @return OpenChess_Model_Piece_King
	 */
	public function findKingByColor($color) {
		foreach($this->_pieces as $piece) {
			if($piece->getType() === OpenChess_Model_Piece::TYPE_KING && $piece->getColor() === $color) {
				return $piece;
			}
		}
		
		return null;
	}
	
	/**
	 * Get all the pieces on the board
	 * 
	 * @return array
	 */
	public function getPieces() {
		return $this->_pieces;
	}
	
	/**
	 * Add a piece to this board
	 *
	 * @param OpenChess_Model_Piece $piece
	 */
	public function addPiece($piece) {
		if(!in_array($piece, $this->_pieces, true)) {
			array_push($this->_pieces, $piece);
		}
	}
	
	/**
	 * Remove a piece from this board
	 *
	 * @param OpenChess_Model_Piece $piece
	 */
	public function removePiece($piece) {
		$position = array_search($piece, $this->_pieces, true);
		
		if($position !== false) {
			array_splice($this->_pieces, $position, 1);
		}
		
		$piece->getPosition()->setOccupier(null);
	}
	
	/**
	 * Get the last move made on this board
	 * 
	 * @return OpenChess_Model_Move
	 */
	public function getLastMove() {
		return $this->_lastMove;
	}
	
	/**
	 * Set the last move made on this board
	 * 
	 * @param OpenChess_Model_Move $move
	 */
	public function setLastMove($move) {
		$this->_lastMove = $move;
	}
	
	/**
	 * Make a copy of the board with all its pieces and their positions
	 * 
	 * @return OpenChess_Model_Board
	 */
	public function copy() {
		$copy = new self();
		
		foreach($this->_pieces as $piece) {
			$position = $piece->getPosition();
			$positionCopy = $copy->findSquareByPosition($position->getFile(), $position->getRank());
			$pieceCopy = OpenChess_Model_Piece::createPiece($piece->getColor(), $piece->getType(), $positionCopy);
			$copy->addPiece($pieceCopy);
		}
		
		return $copy;
	}
	
	public function dump() {
		$files = array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h');
		$ranks = array_reverse(array(1, 2, 3, 4, 5, 6, 7, 8));
		
		$dump = '';
		$dump .= ' ';
		foreach($files as $file) {
			$dump .= $file;
		}
		$dump .= ' ';
		$dump .= "\n";
		foreach($ranks as $rank) {
			$dump .= $rank;
			foreach($files as $file) {
				$piece = $this->findPieceByPosition($file, $rank);
				if($piece === null) {
					$dump .= '.';
				} else {
					switch($piece->getType()) {
						case OpenChess_Model_Piece::TYPE_KING:   $type = 'K'; break;
						case OpenChess_Model_Piece::TYPE_QUEEN:  $type = 'Q'; break;
						case OpenChess_Model_Piece::TYPE_BISHOP: $type = 'B'; break;
						case OpenChess_Model_Piece::TYPE_KNIGHT: $type = 'N'; break;
						case OpenChess_Model_Piece::TYPE_ROOK:   $type = 'R'; break;
						case OpenChess_Model_Piece::TYPE_PAWN:   $type = 'P'; break;
					}
					if($piece->getColor() === OpenChess_Model_Piece::COLOR_BLACK) {
						$type = strtolower($type);
					}
					$dump .= $type;
				}
			}
			$dump .= $rank;
			$dump .= "\n";
		}
		$dump .= ' ';
		foreach($files as $file) {
			$dump .= $file;
		}
		$dump .= ' ';
		$dump .= "\n";
		
		return $dump;
	}
}
