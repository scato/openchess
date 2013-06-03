<?php

class OpenChess_Db_PlayerTable extends Zend_Db_Table_Abstract {
	protected $_name = 'player';
	static protected $_instance = null;
	protected $_idMap = array();
	
	/**
	 * @return OpenChess_Db_PlayerTable
	 */
	public static function getInstance() {
		if(self::$_instance === null) {
			self::$_instance = new self();
		}
		
		return self::$_instance;
	}
	
	public function find($playerId) {
		/* var $rowset Zend_Db_Table_Rowset */
		$rowset = parent::find($playerId);
		
		if($rowset->count() === 0) {
			return null;
		} else {
			$row = $rowset->getRow(0);
			
			return $this->_load($row);
		}
	}
	
	public function findBySession($session) {
		/* var $rowset Zend_Db_Table_Rowset */
		$rowset = $this->fetchAll(
			$this->select()
				->where('`session_id` = ?', $session->getId())
			);
		
		$players = array();
		
		foreach($rowset as $row) {
			$players[] = $this->_load($row);
		}
		
		return $players;
	}
	
	public function findAvailableOpponent($player) {
		/* var $rowset Zend_Db_Table_Rowset */
		$rowset = $this->fetchAll(
			$this->select()
				->where('`opponent_id` IS NULL')
				->where('`id` <> ?', $player->getId())
			);
		
		if($rowset->count() === 0) {
			return null;
		} else {
			$row = $rowset->getRow(0);
			
			return $this->_load($row);
		}
	}
	
	protected function _load($row) {
		if(isset($this->_idMap[$row['id']])) {
			return $this->_idMap[$row['id']];
		} else {
			$sessionTable = OpenChess_Db_SessionTable::getInstance();
			
			$this->_idMap[$row['id']] = new OpenChess_Model_Player($row['name']);
			
			$player = $this->_idMap[$row['id']];
			$player->setId($row['id']);
			
			$session = $sessionTable->find($row['session_id']);
			$player->setSession($session);
			
			if($row['opponent_id'] !== null) {
				$opponent = $this->find($row['opponent_id']);
				$player->setOpponent($opponent);
			}
			
			return $player;
		}
	}
	
	/**
	 * @param OpenChess_Model_Player $player
	 */
	public function save($player) {
		$id = $player->getId();
		$session = $player->getSession();
		$opponent = $player->getOpponent();
		
		$data = array(
			'session_id' => $session ? $session->getId() : null,
			'opponent_id' => $opponent ? $opponent->getId() : null,
			'name' => $player->getName()
		);
		
		if($id) {
			$where = $this->getAdapter()->quoteInto('`id` = ?', $id);
			
			$this->update($data, $where);
		} else {
			$id = $this->insert($data);
			
			$player->setId($id);
		}
	}
}
