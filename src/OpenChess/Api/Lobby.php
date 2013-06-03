<?php

/**
 * A remote facade for the lobby of a game of Chess
 *
 * @package OpenChess_Api
 */
class OpenChess_Api_Lobby extends OpenChess_Api_AbstractFacade {
	/**
	 * Create a session and return the sessionId
	 * 
	 * @return string
	 */
	public function startSession() {
		$sessionTable = OpenChess_Db_SessionTable::getInstance();
		
		$session = new OpenChess_Model_Session();
		$sessionTable->save($session);
		
		return $session->getId();
	}
	
	protected function _findSession($sessionId) {
		$sessionTable = OpenChess_Db_SessionTable::getInstance();
		
		$session = $sessionTable->find($sessionId);
		
		if(!$session) {
			throw new OpenChess_Api_Exception_SessionNotFoundException();
		} else {
			return $session;
		}
	}
	
	protected function _checkSessionNoPlayer($session) {
		$playerTable = OpenChess_Db_PlayerTable::getInstance();
		
		$players = $playerTable->findBySession($session);
		
		if(!empty($players)) {
			throw new OpenChess_Api_Exception_PlayerAlreadyCreatedException();
		}
	}
	
	/**
	 * Create new player and return player info
	 *
	 * @param string $sessionId
	 * @param string $name
	 * @return array
	 */
	public function createPlayer($sessionId, $name) {
		$playerTable = OpenChess_Db_PlayerTable::getInstance();
		$playerSerializer = new OpenChess_Model_PlayerSerializer();
		
		$session = $this->_findSession($sessionId);
		$this->_checkSessionNoPlayer($session);
		
		$player = new OpenChess_Model_Player($name);
		$player->setSession($session);
		
		$playerTable->save($player);
		
		return $playerSerializer->serialize($player);
	}
	
	protected function _findSessionPlayer($session) {
		$playerTable = OpenChess_Db_PlayerTable::getInstance();
		
		$players = $playerTable->findBySession($session);
		
		if(empty($players)) {
			throw new OpenChess_Api_Exception_PlayerNotYetCreatedException();
		} else {
			return $players[0];
		}
	}
	
	protected function _checkPlayerNoOpponent($player) {
		if($player->getOpponent() !== null) {
			throw new OpenChess_Api_Exception_OpponentAlreadyFoundException();
		}
	}
	
	/**
	 * Check wether player has an opponent and return info
	 * 
	 * @param int $sessionId
	 * @return array
	 */
	public function getOpponentInfo($sessionId) {
		$playerSerializer = new OpenChess_Model_PlayerSerializer();
		
		$session = $this->_findSession($sessionId);
		$player = $this->_findSessionPlayer($session);
		
		$opponent = $player->getOpponent();
		
		if($opponent === null) {
			return null;
		} else {
			return $playerSerializer->serialize($opponent);
		}
	}
	
	/**
	 * Find opponent for player and return opponent info
	 *
	 * @param string $sessionId
	 * @return array
	 */
	public function findOpponent($sessionId) {
		$playerTable = OpenChess_Db_PlayerTable::getInstance();
		$playerSerializer = new OpenChess_Model_PlayerSerializer();
		
		$session = $this->_findSession($sessionId);
		$player = $this->_findSessionPlayer($session);
		$this->_checkPlayerNoOpponent($player);
		
		$opponent = $playerTable->findAvailableOpponent($player);
		
		if($opponent === null) {
			throw new OpenChess_Api_Exception_OpponentNotAvailableException();
		}
		
		$player->setOpponent($opponent);
		$opponent->setOpponent($player);
		
		$playerTable->save($player);
		$playerTable->save($opponent);
		
		return $playerSerializer->serialize($opponent);
	}
}
