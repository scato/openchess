<?php

/**
 * Serializer for games
 *
 * @package OpenChess_Model
 */
class OpenChess_Model_PieceSerializer {
	/**
	 * Convert a piece and all its valid moves into (associative) arrays, integers and strings
	 * 
	 * @param OpenChess_Model_Piece $piece
	 * @return array
	 */
	public function serialize($piece, $toMove) {
		return array(
			'class' => 'Piece',
			'color' => $piece->getColor(),
			'type' => $piece->getType(),
			'position' => array(
				'file' => $piece->getPosition()->getFile(),
				'rank' => $piece->getPosition()->getRank()
			),
			'validMoves' => $toMove ? array_map(array($this, 'serializeMove'), $piece->getValidMoves()) : array()
		);
	}
	
	private function serializeMove($move) {
		return array(
			'class' => 'Move',
			'type' => $move->getType(),
			'destination' => array(
				'file' => $move->getDestination()->getFile(),
				'rank' => $move->getDestination()->getRank()
			)
		);
	}
}
