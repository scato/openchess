<?php

/**
 * @package OpenChess_Model
 * @subpackage OpenChess_Model_Piece
 */
class OpenChess_Model_Piece_Pawn extends OpenChess_Model_Piece {
	protected $_type = OpenChess_Model_Piece::TYPE_PAWN;
	
	protected function _getMoves($protectKing) {
		$moveHelper = new OpenChess_Model_MoveHelper($this);
		
		$moveHelper->addPawnSingleMove();
		$moveHelper->addPawnDoubleMove();
		$moveHelper->addPawnCaptureMoves();
		$moveHelper->addPawnCaptureEnPassantMoves();
		$moveHelper->addPawnPromotionMove();
		
		return $moveHelper->getMoves($protectKing);
	}
}
