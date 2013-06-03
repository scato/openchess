<?php

/**
 * The session
 *
 * @package OpenChess_Model
 */
class OpenChess_Model_Session extends Zend_Db_Table_Row_Abstract {
	private $_id;
	private $_playerId;
	private $_opponentId;
	
	public function getId() {
		return $this->_id;
	}
	
	public function setId($id) {
		$this->_id = $id;
	}
	
	public function getPlayerId() {
		return $this->_playerId;
	}
	
	public function getOpponentId() {
		return $this->_opponentId;
	}
}
