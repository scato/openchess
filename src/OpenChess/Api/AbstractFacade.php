<?php

/**
 * Common methods for all remote facades
 *
 * @package OpenChess_Api
 */
abstract class OpenChess_Api_AbstractFacade {
	/**
	 * @param string $sessionId
	 * @return OpenChess_Model_Session
	 */
	protected function _findSession($sessionId) {
		// SessionTable::find
		// check of sessie bestaat (anders gooi SessionNotFoundException)
	}
	
	/**
	 * @param OpenChess_Model_Session $session
	 * @return void
	 */
	protected function _checkSessionNoPlayer($session) {
		// Session::getPlayerId
		// check of sessie niet al een speler heeft (anders gooi PlayerAlreadyCreatedException)
	}
	
	/**
	 * @param OpenChess_Model_Session $session
	 * @return void
	 */
	protected function _findSessionPlayer($session, $playerId) {
		// Session::getPlayerId
		// check of sessie al een speler heeft (anders gooi PlayerNotYetCreatedException)
		// check of sessie dezelfde speler heeft (anders gooi PlayerMismatchException)
		// PlayerTable::find
		// check of speler bestaat (anders gooi PlayerNotFoundException)
	}
	
	/**
	 * @param OpenChess_Model_Session $session
	 * @return void
	 */
	protected function _checkSessionNoOpponent($session) {
		// Session::getOpponentId
		// check of sessie niet al een tegenstander heeft (anders gooi OpponentAlreadyFoundException)
	}
	
	protected function _findSessionOpponent($session, $opponentId) {
		// Session::getOpponentId
		// check of sessie al een tegenstander heeft (anders gooi OpponentNotYetCreatedException)
		// check of sessie dezelfde tegenstander heeft (anders gooi OpponentMismatchException)
		// PlayerTable::find
		// check of tegenstander bestaat (anders gooi OpponentNotFoundException)
	}
}
