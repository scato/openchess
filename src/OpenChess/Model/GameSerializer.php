<?php

/**
 * Serializer for games
 *
 * @package OpenChess_Model
 */
class OpenChess_Model_GameSerializer {
	/**
	 * Convert a game and all its child objects into (associative) arrays, integers and strings
	 * 
	 * @param OpenChess_Model_Game $game
	 * @return array
	 */
	public function serialize($game, $player) {
		$playerSerializer = new OpenChess_Model_PlayerSerializer();
		$pieceSerializer = new OpenChess_Model_PieceSerializer();
		
		$toMove = $game->findPlayerToMove() !== null && $game->findPlayerToMove()->equals($player);
		$pieces = array();
		
		foreach($game->getBoard()->getPieces() as $piece) {
			if($toMove) {
				$powned = $game->findPlayerByColor($piece->getColor())->equals($player);
				$pieces[] = $pieceSerializer->serialize($piece, $powned);
			} else {
				$pieces[] = $pieceSerializer->serialize($piece, false);
			}
		}
		
		return array(
			'class' => 'Game',
			'id' => $game->getId(),
			'white' => $playerSerializer->serialize($game->getWhite()),
			'black' => $playerSerializer->serialize($game->getBlack()),
			'pieces' => $pieces,
			'validActions' => array_map(array($this, "serializeAction"), $game->getValidActions($player)),
			'state' => $game->getState(),
			'outcome' => $game->getOutcome(),
			'version' => '1.0'
		);
	}
	
	private function serializeAction($action) {
		return $action->getType();
	}
}
