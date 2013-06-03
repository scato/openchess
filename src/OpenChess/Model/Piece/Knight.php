<?php

/**
 * @package OpenChess_Model
 * @subpackage OpenChess_Model_Piece
 */
class OpenChess_Model_Piece_Knight extends OpenChess_Model_Piece {
	protected $_type = OpenChess_Model_Piece::TYPE_KNIGHT;
	
	protected function _getMoves($protectKing) {
		$moveHelper = new OpenChess_Model_MoveHelper($this);
		
		$moveHelper->addSingleMove(-2, -1);
		$moveHelper->addSingleMove(-2, 1);
		$moveHelper->addSingleMove(-1, -2);
		$moveHelper->addSingleMove(-1, 2);
		$moveHelper->addSingleMove(1, -2);
		$moveHelper->addSingleMove(1, 2);
		$moveHelper->addSingleMove(2, -1);
		$moveHelper->addSingleMove(2, 1);
		
		return $moveHelper->getMoves($protectKing);
	}
}
