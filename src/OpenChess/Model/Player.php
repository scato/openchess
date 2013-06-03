<?php

/**
 * One of the players
 *
 * @package OpenChess_Model
 */
class OpenChess_Model_Player extends Zend_Db_Table_Row_Abstract {
	private $_id;
	private $_name;
	private $_session;
	private $_opponent;
	
	/**
	 * Create a player with this name
	 * 
	 * @param string $name
	 */
	public function __construct($name) {
		$this->_name = $name;
	}
	
	/**
	 * Get this players ID
	 *
	 * @return int
	 */
	public function getId() {
		return $this->_id;
	}
    
    /**
     * Set this players ID
     * 
     * @param int $id
	 * @return void
     */
	public function setId($id) {
        $this->_id = $id;
    }
    
    /**
     * Get this players session
     *
     * @return OpenChess_Model_Session
     */
    public function getSession() {
    	return $this->_session;
    }
	
    /**
     * Set this players session
     * 
     * @param OpenChess_Model_Session $session
	 * @return void
     */
    public function setSession($session) {
    	$this->_session = $session;
    }
	
    /**
     * Get this players opponent
     *
     * @return OpenChess_Model_Player
     */
    public function getOpponent() {
    	return $this->_opponent;
    }
	
    /**
     * Set this players opponent
     * 
     * @param OpenChess_Model_Player $opponent
	 * @return void
     */
    public function setOpponent($opponent) {
    	$this->_opponent = $opponent;
    }
	
    /**
	 * Get this players name
	 * 
	 * @return string
	 */
	public function getName() {
		return $this->_name;
	}
	
	/**
	 * Is this the same player?
	 * 
	 * @param OpenChess_Model_Player
	 * @return boolean
	 */
	public function equals($player) {
		return $this->_id == $player->_id;
	}
}
