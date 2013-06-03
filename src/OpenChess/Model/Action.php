<?php

/**
 * One action
 *
 * @package OpenChess_Model
 */
class OpenChess_Model_Action {
	private $_game;
	private $_player;
	private $_type;
	
	const TYPE_RESIGN = 'ACTION_TYPE_RESIGN';
	
	/**
	 * Create an action
	 * 
	 * @param OpenChess_Model_Game $game
	 * @param OpenChess_Model_Player $player
	 * @param string $type
	 */
	public function __construct($game, $player, $type) {
		$this->_game = $game;
		$this->_player = $player;
		$this->_type = $type;
	}
	
	/**
	 * Take this action
	 * 
	 * @return void
	 */
	public function take() {
		switch($this->_type) {
			case self::TYPE_RESIGN:
				if($this->_player == $this->_game->getWhite()) {
					$outcome = OpenChess_Model_Game::OUTCOME_BLACK_WON;
				} else {
					$outcome = OpenChess_Model_Game::OUTCOME_WHITE_WON;
				}
				
				$state = OpenChess_Model_Game::STATE_RESIGNATION;
				
				$this->_game->setOutcome($outcome);
				$this->_game->setState($state);
				
				break;
		}
	}
	
	/**
	 * Get the game for which action will be taken
	 * 
	 * @return OpenChess_Model_Game
	 */
	public function getGame() {
		return $this->_game;
	}
		
	/**
	 * Get the player which will take the action
	 * 
	 * @return OpenChess_Model_Player 
	 */
	public function getPlayer() {
		return $this->_player;
	}
	
	/**
	 * Get the type of action
	 * 
	 * Only TYPE_RESIGN supported so far
	 * TYPE_OFFER_DRAW and TYPE_ACCEPT_DRAW should be implemented as well
	 * 
	 * @return string 
	 */
	public function getType() {
		return $this->_type;
	}
}
