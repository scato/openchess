<?php

/**
 * Serializer for players
 *
 * @package OpenChess_Model
 */
class OpenChess_Model_PlayerSerializer {
	/**
	 * Convert a player into an (associative) array, integers and strings
	 * 
	 * @param OpenChess_Model_Player $player
	 * @return array
	 */
	public function serialize($player) {
		return array(
			'class' => 'Player',
			'id' => $player->getId(),
			'name' => $player->getName()
		);
	}
}
