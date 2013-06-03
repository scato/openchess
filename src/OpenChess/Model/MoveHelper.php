<?php

/**
 * Helper object for creating valid moves
 *
 * @package OpenChess_Model
 */
class OpenChess_Model_MoveHelper {
	private $_piece;
	private $_origin;
	private $_board;
	private $_moves = array();
	
	/**
	 * Set up an list for valid moves that this piece may make
	 * 
	 * @param OpenChess_Model_Piece
	 */
	public function __construct($piece) {
		$this->_piece = $piece;
		$this->_origin = $this->_piece->getPosition();
		$this->_board = $this->_origin->getBoard();
	}
	
	public function addPawnSingleMove() {
		$file = $this->_origin->getFile();
		$rank = $this->_origin->getRank();
		
		$rank = $this->_calcRank($rank, 1);
		if($rank !== null && $rank !== $this->_getOpponentRank()) {
			$occupying = $this->_board->findPieceByPosition($file, $rank);
		
			if($occupying === null) {
				$destination = $this->_board->findSquareByPosition($file, $rank);
				$move = new OpenChess_Model_Move(OpenChess_Model_Move::TYPE_PLAIN, $this->_piece, $destination);
				
				$this->_moves[] = $move;
			}
		}
	}
	
	public function addPawnDoubleMove() {
		if(!$this->_piece->getHasMoved()) {
			$file = $this->_origin->getFile();
			$rank = $this->_origin->getRank();
			
			$rank = $this->_calcRank($rank, 1);
			if($rank !== null) {
				$occupying = $this->_board->findPieceByPosition($file, $rank);
				
				if($occupying === null) {
					$rank = $this->_calcRank($rank, 1);
					if($rank !== null) {
						$occupying = $this->_board->findPieceByPosition($file, $rank);
						
						if($occupying === null) {
							$destination = $this->_board->findSquareByPosition($file, $rank);
							$move = new OpenChess_Model_Move(OpenChess_Model_Move::TYPE_TWO_SQUARES, $this->_piece, $destination);
							
							$this->_moves[] = $move;
						}
					}
				}
			}
		}
	}
	
	public function addPawnCaptureMoves() {
		$file = $this->_origin->getFile();
		$rank = $this->_origin->getRank();
		$color = $this->_piece->getColor();
		
		$rank = $this->_calcRank($rank, 1);
		if($rank !== null) {
			$fileLeft = $this->_calcFile($file, -1);
			
			if($fileLeft !== null) {
				$occupyingLeft = $this->_board->findPieceByPosition($fileLeft, $rank);
				
				if($occupyingLeft !== null && $occupyingLeft->getColor() !== $color) {
					$destination = $this->_board->findSquareByPosition($fileLeft, $rank);
					$move = new OpenChess_Model_Move(OpenChess_Model_Move::TYPE_CAPTURING, $this->_piece, $destination);
					
					$this->_moves[] = $move;
				}
			}
			
			$fileRight = $this->_calcFile($file, 1);
			
			if($fileRight !== null) {
				$occupyingRight = $this->_board->findPieceByPosition($fileRight, $rank);
				
				if($occupyingRight !== null && $occupyingRight->getColor() !== $color) {
					$destination = $this->_board->findSquareByPosition($fileRight, $rank);
					$move = new OpenChess_Model_Move(OpenChess_Model_Move::TYPE_CAPTURING, $this->_piece, $destination);
					
					$this->_moves[] = $move;
				}
			}
		}
	}
	
	public function addPawnCaptureEnPassantMoves() {
		$lastMove = $this->_board->getLastMove();
		
		if($lastMove !== null && $lastMove->getType() === OpenChess_Model_Move::TYPE_TWO_SQUARES) {
			$lastDestination = $lastMove->getDestination();
			
			$file = $this->_origin->getFile();
			$rank = $this->_origin->getRank();
			
			if($rank === $lastDestination->getRank()) {
				$rank = $this->_calcRank($rank, 1);
				
				$fileLeft = $this->_calcFile($file, 1);
				
				if($lastDestination->getFile() === $fileLeft) {
					$destination = $this->_board->findSquareByPosition($fileLeft, $rank);
					$move = new OpenChess_Model_Move(OpenChess_Model_Move::TYPE_CAPTURING_EN_PASSANT, $this->_piece, $destination);
					
					$this->_moves[] = $move;
				}
				
				$fileRight = $this->_calcFile($file, -1);
				
				if($lastDestination->getFile() === $fileRight) {
					$destination = $this->_board->findSquareByPosition($fileRight, $rank);
					$move = new OpenChess_Model_Move(OpenChess_Model_Move::TYPE_CAPTURING_EN_PASSANT, $this->_piece, $destination);
					
					$this->_moves[] = $move;
				}
			}
		}
	}
	
	public function addPawnPromotionMove() {
		$file = $this->_origin->getFile();
		$rank = $this->_origin->getRank();
		
		$rank = $this->_calcRank($rank, 1);
		if($rank !== null && $rank === $this->_getOpponentRank()) {
			$destination = $this->_board->findSquareByPosition($file, $rank);
			$move = new OpenChess_Model_Move(OpenChess_Model_Move::TYPE_PROMOTION, $this->_piece, $destination);
			
			$this->_moves[] = $move;
		}
	}
	
	public function addSingleMove($df, $dr) {
		$file = $this->_origin->getFile();
		$rank = $this->_origin->getRank();
		
		$file = $this->_calcFile($file, $df);
		$rank = $this->_calcRank($rank, $dr);
		if($file !== null && $rank !== null) {
			$destination = $this->_board->findSquareByPosition($file, $rank);
			$occupying = $destination->getOccupier();
			
			if($occupying === null) {
				$move = new OpenChess_Model_Move(OpenChess_Model_Move::TYPE_PLAIN, $this->_piece, $destination);
			} elseif($occupying->getColor() !== $this->_piece->getColor()) {
				$move = new OpenChess_Model_Move(OpenChess_Model_Move::TYPE_CAPTURING, $this->_piece, $destination);
			} else {
				return null;
			}
			
			$this->_moves[] = $move;
			
			return $move;
		}
		
		return null;
	}
	
	public function addLinearMoves($df, $dr) {
		$f = 1;
		$move = $this->addSingleMove($df * $f, $dr * $f);
		
		while($move !== null && $move->getType() !== OpenChess_Model_Move::TYPE_CAPTURING) {
			$f++;
			$move = $this->addSingleMove($df * $f, $dr * $f);
		}
	}
	
	public function addCastlingMoves() {
		$king = $this->_piece;
		$rank = $this->_origin->getRank();
		
		if(!$king->getHasMoved()) {
			$rook = $this->_board->findPieceByPosition('a', $rank);
			
			if($rook !== null) {
				$occupying = array(
					'b' => $this->_board->findPieceByPosition('b', $rank),
					'c' => $this->_board->findPieceByPosition('c', $rank),
					'd' => $this->_board->findPieceByPosition('d', $rank),
				);
				$hasMoved = $rook->getHasMoved();
				
				if($occupying['b'] === null && $occupying['c'] === null && $occupying['d'] === null && !$hasMoved) {
					$destination = $this->_board->findSquareByPosition('c', $rank);
					$move = new OpenChess_Model_Move(OpenChess_Model_Move::TYPE_CASTLING, $king, $destination);
					
					$this->_moves[] = $move;
				}
			}
			
			$rook = $this->_board->findPieceByPosition('h', $this->_origin->getRank());
			
			if($rook !== null) {
				$occupying = array(
					'f' => $this->_board->findPieceByPosition('f', $rank),
					'g' => $this->_board->findPieceByPosition('g', $rank),
				);
				$hasMoved = $rook->getHasMoved();
				
				if($occupying['f'] === null && $occupying['g'] === null && !$hasMoved) {
					$destination = $this->_board->findSquareByPosition('g', $rank);
					$move = new OpenChess_Model_Move(OpenChess_Model_Move::TYPE_CASTLING, $king, $destination);
					
					$this->_moves[] = $move;
				}
			}
		}
	}
	
	/**
	 * Get all moves that were added in the process
	 * 
	 * Include the ones that leave our king in check by passing false
	 * 
	 * @param boolean $protectKing
	 * @return array
	 */
	public function getMoves($protectKing) {
		if($protectKing) {
			$color = $this->_piece->getColor();
			$king = $this->_board->findKingByColor($color);
			$inCheck = $king !== null && $king->getInCheck();
			
			$validMoves = array();
			
			foreach($this->_moves as $move) {
				if(!$inCheck || $move->getType() !== OpenChess_Model_Move::TYPE_CASTLING) {
					$simulation = $move->simulate();
					$king = $simulation->findKingByColor($color);
					if($king === null || !$king->getInCheck()) {
						$validMoves[] = $move;
					}
				}
			}
			
			return $validMoves;
		} else {
			return $this->_moves;
		}
	}
	
	private static $_calcFileCache = array();
	
	private function _calcFile($f0, $df) {
		if(!isset(self::$_calcFileCache[$f0.$df])) {
			$files = array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h');
			
			$n = array_search($f0, $files) + $df;
			
			if(0 <= $n && $n <= 7) {
				self::$_calcFileCache[$f0.$df] = $files[$n];
			} else {
				self::$_calcFileCache[$f0.$df] = null;
			}
		}
		
		return self::$_calcFileCache[$f0.$df];
	}
	
	private static $_calcRankCache = array();
	
	private function _calcRank($r0, $dr) {
		$isBlack = $this->_piece->getColor() === OpenChess_Model_Piece::COLOR_BLACK;
		if(!isset(self::$_calcRankCache[$r0.$dr.$isBlack])) {
			if($isBlack) {
				$rank = $r0 - $dr;
			} else {
				$rank = $r0 + $dr;
			}
			
			if(1 <= $rank && $rank <= 8) {
				self::$_calcRankCache[$r0.$dr.$isBlack] = $rank;
			} else {
				self::$_calcRankCache[$r0.$dr.$isBlack] = null;
			}
		}
		
		return self::$_calcRankCache[$r0.$dr.$isBlack];
	}
	
	private function _getPlayerRank() {
		if($this->_piece->getColor() === OpenChess_Model_Piece::COLOR_BLACK) {
			return 8;
		} else {
			return 1;
		}
	}
		
	private function _getOpponentRank() {
		if($this->_piece->getColor() === OpenChess_Model_Piece::COLOR_BLACK) {
			return 1;
		} else {
			return 8;
		}
	}
}
