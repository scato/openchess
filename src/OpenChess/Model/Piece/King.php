<?php

/**
 * @package OpenChess_Model
 * @subpackage OpenChess_Model_Piece
 */
class OpenChess_Model_Piece_King extends OpenChess_Model_Piece {
	protected $_type = OpenChess_Model_Piece::TYPE_KING;
	
	protected function _getMoves($protectKing) {
		$moveHelper = new OpenChess_Model_MoveHelper($this);
		
		$moveHelper->addSingleMove(-1, -1);
		$moveHelper->addSingleMove(-1, 0);
		$moveHelper->addSingleMove(-1, 1);
		$moveHelper->addSingleMove(0, -1);
		$moveHelper->addSingleMove(0, 1);
		$moveHelper->addSingleMove(1, -1);
		$moveHelper->addSingleMove(1, 0);
		$moveHelper->addSingleMove(1, 1);
		$moveHelper->addCastlingMoves();
		
		return $moveHelper->getMoves($protectKing);
	}
}
