<?php

/**
 * A remote facade for a game of Chess
 *
 * @package OpenChess_Api
 */
class OpenChess_Api_Game extends OpenChess_Api_Lobby {
	/**
	 * @param string $gameId
	 * @return OpenChess_Model_Game
	 */
	protected function _findPlayerGame($player) {
		$gameTable = OpenChess_Db_GameTable::getInstance();
		
		$games = $gameTable->findByPlayer($player);
		
		if(empty($games)) {
			throw new OpenChess_Api_Exception_GameNotFoundException();
		} else {
			return $games[0];
		}
	}
	
	protected function _checkPlayerOpponent($player) {
		$opponent = $player->getOpponent();
		
		if($opponent === null) {
			throw new OpenChess_Api_Exception_OpponentNotYetFoundException();
		}
	}
	
	protected function _checkPlayerNoGame($player) {
		$gameTable = OpenChess_Db_GameTable::getInstance();
		
		$games = $gameTable->findByPlayer($player);
		
		if(!empty($games)) {
			throw new OpenChess_Api_Exception_GameAlreadyStartedException();
		}
	}
	
	/**
	 * Start new game for player and return the new game info
	 *
	 * @param string $sessionId
	 * @return array
	 */
	public function startGame($sessionId) {
		$gameTable = OpenChess_Db_GameTable::getInstance();
		$gameSerializer = new OpenChess_Model_GameSerializer();
		
		$session = $this->_findSession($sessionId);
		$player = $this->_findSessionPlayer($session);
		$this->_checkPlayerOpponent($player);
		$this->_checkPlayerNoGame($player);
		
		$opponent = $player->getOpponent();
		
		if(rand(0, 1) === 0) {
			$game = new OpenChess_Model_Game($player, $opponent);
		} else {
			$game = new OpenChess_Model_Game($opponent, $player);
		}
		
		$gameTable->save($game);
		
		return $gameSerializer->serialize($game, $player);
	}
	
	/**
	 * Get a game object containing all relevant information
	 *
	 * @param string $sessionId
	 * @return array
	 */
	public function getGameInfo($sessionId) {
		$gameTable = OpenChess_Db_GameTable::getInstance();
		$gameSerializer = new OpenChess_Model_GameSerializer();
		
		$session = $this->_findSession($sessionId);
		$player = $this->_findSessionPlayer($session);
		
		$games = $gameTable->findByPlayer($player);
		
		if(empty($games)) {
			return null;
		} else {
			return $gameSerializer->serialize($games[0], $player);
		}
	}
	
	/**
	 * Get a game object as soon as the game state changes (from $state to something else)
	 *
	 * @param string $sessionId
	 * @param string $state
	 * @return array
	 */
	public function pollGameInfo($sessionId, $state) {
		$timeout = 10;
		$start = time();
		
		while(time() < $start + $timeout) {
			$info = $this->getGameInfo($sessionId);
			
			if($info === null) {
				return null;
			} elseif($info['state'] != $state) {
				return $info;
			}
			
			sleep(1);
		}
		
		return null;
	}
	
	/**
	 * Make a move and return the new game info
	 * 
	 * a move is identified by the position of the piece that moves and a destination
	 * if a pawn moves to the last rank, promotionType is mandatory
	 * 
	 * @param string $playerId
	 * @param string $gameId
	 * @param string $positionFile
	 * @param int $positionRank
	 * @param string $destinationFile
	 * @param int $destinationRank
	 * @param string $promotionType
	 * @return array
	 */
	public function makeMove($sessionId, $playerId, $gameId, $positionFile, $positionRank, $destinationFile, $destinationRank, $promotionType = null) {
		$gameTable = OpenChess_Db_GameTable::getInstance();
		$gameSerializer = new OpenChess_Model_GameSerializer();
		
		$session = $this->_findSession($sessionId);
		$player = $this->_findSessionPlayer($session);
		$game = $this->_findPlayerGame($player);
		
		// Game::getState
		// check of het spel nog wel bezig is (anders gooi GameEndedException)
		// Game::findPlayerToMove
		// check of deze speler aan de beurt is (anders gooi PlayerNotToMoveException)
		
		$board = $game->getBoard();
		$occupying = $board->findPieceByPosition($positionFile, $positionRank);
		
		if($occupying === null) {
			throw new OpenChess_Api_Exception_SquareNotOccupiedException();
		}
		
		// Piece::getColor
		// Game::findPlayerByColor
		// check of het stuk van deze speler is (anders gooi NotControlledByPlayerException)
		
		$destination = $board->findSquareByPosition($destinationFile, $destinationRank);
		$move = $occupying->findValidMoveByDestination($destination);
		
		if($move === null) {
			throw new OpenChess_Api_Exception_InvalidMoveException();
		}
		
		if($promotionType !== null) {
			$move->setPromotionType($promotionType);
		}
		
		$move->make();
		$game->updateState();
		
		$gameTable->save($game);
		
		return $gameSerializer->serialize($game, $player);
	}
	
	/**
	 * Take an action and return the new game info
	 *
	 * @param string $sessionId
	 * @param string $playerId
	 * @param string $opponentId
	 * @param string $gameId
	 * @param string $type
	 * @return array
	 */
	public function takeAction($sessionId, $playerId, $opponentId, $gameId, $type) {
		$gameTable = OpenChess_Db_GameTable::getInstance();
		$gameSerializer = new OpenChess_Model_GameSerializer();
		
		$session = $this->_findSession($sessionId);
		$player = $this->_findSessionPlayer($session);
		$game = $this->_findPlayerGame($player);
		
		// check of de gebruiker deze actie kan doen (anders gooi InvalidActionException)
		$action = $game->findValidActionByType($player, $type);
		
		if($action === null) {
			throw new OpenChess_Api_Exception_InvalidActionException();
		}
		
		$action->take();
		
		$gameTable->save($game);
		
		return $gameSerializer->serialize($game, $player);
	}
}
