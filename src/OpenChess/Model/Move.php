<?php

/**
 * One move
 *
 * @package OpenChess_Model
 */
class OpenChess_Model_Move {
	private $_type;
	
	const TYPE_PLAIN = 'MOVE_TYPE_PLAIN';
	const TYPE_TWO_SQUARES = 'MOVE_TYPE_TWO_SQUARES';
	const TYPE_CAPTURING = 'MOVE_TYPE_CAPTURING';
	const TYPE_CASTLING = 'MOVE_TYPE_CASTLING';
	const TYPE_CAPTURING_EN_PASSANT = 'MOVE_TYPE_CAPTURING_EN_PASSANT';
	const TYPE_PROMOTION = 'MOVE_TYPE_PROMOTION';
	
	private $_destination;
	/**
	 * @var OpenChess_Model_Piece
	*/
	private $_piece;
	private $_promotionType;
	
	public function __construct($type, $piece, $destination) {
		$this->_type = $type;
		$this->_piece = $piece;
		$this->_destination = $destination;
		$this->_promotionType = OpenChess_Model_Piece::TYPE_NONE;
	}
	
	/**
	 * Simulate what would happen to the board if this move was made
	 *
	 * @return OpenChess_Model_Board
	 */
	public function simulate() {
		$piece = $this->getPiece();
		$board = $piece->getPosition()->getBoard();
		
		$boardCopy = $board->copy();
		$pieceCopy = $boardCopy->findPieceByPosition($piece->getPosition()->getFile(), $piece->getPosition()->getRank());
		$destinationCopy = $boardCopy->findSquareByPosition($this->_destination->getFile(), $this->_destination->getRank());
		
		$moveCopy = new self($this->_type, $pieceCopy, $destinationCopy);
		// set type to OpenChess_Model_Piece::TYPE_QUEEN for safety...
		$moveCopy->setPromotionType(OpenChess_Model_Piece::TYPE_QUEEN);
		
		$moveCopy->make();
		
		return $boardCopy;
	}
	
	/**
	 * Make this move
	 *
	 * @return void
	 */
	public function make() {
		$pieces = $this->_destination->getBoard()->getPieces();
		
		foreach($pieces as $piece) {
			$piece->setAdvancedTwoSquares(false);
		}
		
		switch($this->_type) {
			case OpenChess_Model_Move::TYPE_PLAIN:
				$this->_piece->moveTo($this->_destination);
				
				break;
			case OpenChess_Model_Move::TYPE_TWO_SQUARES:
				$this->_piece->moveTo($this->_destination);
				$this->_piece->setAdvancedTwoSquares(true);
				
				break;
			case OpenChess_Model_Move::TYPE_CAPTURING:
				$board = $this->_destination->getBoard();
				$capturedPiece = $board->findPieceByPosition($this->_destination->getFile(), $this->_destination->getRank());
				
				$board->removePiece($capturedPiece);
				$this->_piece->moveTo($this->_destination);
				
				break;
			case OpenChess_Model_Move::TYPE_CAPTURING_EN_PASSANT:
				$board = $this->_destination->getBoard();
				// capturing en passant means captured piece was next to capturing piece
				$capturedPiece = $board->findPieceByPosition($this->_destination->getFile(), $this->_piece->getPosition()->getRank());
				
				$board->removePiece($capturedPiece);
				$this->_piece->moveTo($this->_destination);
				
				break;
			case OpenChess_Model_Move::TYPE_CASTLING:
				$board = $this->_destination->getBoard();
				$rank = $this->_destination->getRank();
				
				if($this->_destination->getFile() === 'c') {
					$rook = $board->findPieceByPosition('a', $rank);
					$rookDestination = $board->findSquareByPosition('d', $rank);
				} else {
					$rook = $board->findPieceByPosition('h', $rank);
					$rookDestination = $board->findSquareByPosition('f', $rank);
				}
				
				$rook->moveTo($rookDestination);
				$this->_piece->moveTo($this->_destination);
				
				break;
			case OpenChess_Model_Move::TYPE_PROMOTION:
				$board = $this->_destination->getBoard();
				$piece = OpenChess_Model_Piece::createPiece($this->_piece->getColor(), $this->_promotionType, $this->_destination);
				
				$board->removePiece($this->_piece);
				$board->addPiece($piece);
				
				break;
		}
		
		$this->_piece->getPosition()->getBoard()->setLastMove($this);
	}
	
	/**
	 * Get the specific type of move
	 * 
	 * one of TYPE_PLAIN, TYPE_CAPTURING, TYPE_CASTLING, TYPE_CAPTURING_EN_PASSANT or TYPE_PROMOTION
	 * 
	 * @return string
	 */
	public function getType() {
		return $this->_type;
	}
	
	/**
	 * Get this moves destination square
	 *
	 * @return OpenChess_Model_Square
	 */
	public function getDestination() {
		return $this->_destination;
	}
	
	/**
	 * Get the moving piece
	 *
	 * @return OpenChess_Model_Piece
	 */
	public function getPiece() {
		return $this->_piece;
	}
	
	/**
	 * Type of piece to which the moving pawn will be promoted
	 * 
	 * one of TYPE_ROOK, TYPE_KNIGHT, TYPE_BISHOP or TYPE_QUEEN (of OpenChess_Model_Piece)
	 * equals TYPE_NONE is not applicable
	 *
	 * @return string
	 */
	public function getPromotionType() {
		return $this->_promotionType;
	}
		
	/**
	 * @param string $promotionType
	 * @return void
	 */
	public function setPromotionType($promotionType) {
		$this->_promotionType = $promotionType;
	}
	
	/**
	 * Find out whether this move captures a certain piece
	 * 
	 * @param OpenChess_Model_Piece
	 * @return boolean
	 */
	public function captures($piece) {
		switch($this->_type) {
			case self::TYPE_CAPTURING:
			case self::TYPE_CAPTURING_EN_PASSANT:
				return $this->_destination->equals($piece->getPosition());
			default:
				return false;
		}
	}
}
