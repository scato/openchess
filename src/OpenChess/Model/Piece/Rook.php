<?php

/**
 * @package OpenChess_Model
 * @subpackage OpenChess_Model_Piece
 */
class OpenChess_Model_Piece_Rook extends OpenChess_Model_Piece {
	protected $_type = OpenChess_Model_Piece::TYPE_ROOK;
	
	protected function _getMoves($protectKing) {
		$moveHelper = new OpenChess_Model_MoveHelper($this);
		
		$moveHelper->addLinearMoves(-1, 0);
		$moveHelper->addLinearMoves(0, -1);
		$moveHelper->addLinearMoves(0, 1);
		$moveHelper->addLinearMoves(1, 0);
		
		return $moveHelper->getMoves($protectKing);
	}
}
