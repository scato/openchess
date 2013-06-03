<?php

class OpenChess_Db_GameTable extends Zend_Db_Table_Abstract {
	protected $_name = 'game';
	static protected $_instance = null;
	
	/**
	 * @return OpenChess_Db_GameTable
	 */
	public static function getInstance() {
		if(self::$_instance === null) {
			self::$_instance = new self();
		}
		
		return self::$_instance;
	}
	
	/**
	 * 
	 * @param int $gameId
	 * @return OpenChess_Model_Game
	 */
	public function find($gameId) {
		/* var $rowset Zend_Db_Table_Rowset */
		$rowset = parent::find($gameId);
		
		if($rowset->count() === 0) {
			return null;
		} else {
			$row = $rowset->getRow(0);
			
			return $this->_load($row);
		}
	}
	
	public function findByPlayer($player) {
		/* var $rowset Zend_Db_Table_Rowset */
		$rowset = $this->fetchAll(
			$this->select()
				->where('`white_id` = ? OR `black_id` = ?', $player->getId(), $player->getId())
			);
		
		$games = array();
		
		foreach($rowset as $row) {
			$games[] = $this->_load($row);
		}
		
		return $games;
	}
	
	protected function _load($row) {
		$playerTable = OpenChess_Db_PlayerTable::getInstance();
		
		$white = $playerTable->find($row['white_id']);
		$black = $playerTable->find($row['black_id']);
		
		$game = new OpenChess_Model_Game($white, $black);
		$game->setId($row['id']);
		$game->setOutcome($row['outcome']);
		$game->setState($row['state']);
		$game->setBoard(unserialize($row['board']));
		
		return $game;
	}
	
	/**
	 * @param OpenChess_Model_Game $game
	 */
	public function save($game) {
		$id = $game->getId();
		$white = $game->getWhite();
		$black = $game->getBlack();
		
		$data = array(
			'white_id' => $white ? $white->getId() : null,
			'black_id' => $black ? $black->getId() : null,
			'outcome' => $game->getOutcome(),
			'state' => $game->getState(),
			'board' => serialize($game->getBoard())
		);
		
		if($id) {
			$where = $this->getAdapter()->quoteInto('`id` = ?', $id);
			
			$this->update($data, $where);
		} else {
			$id = $this->insert($data);
			
			$game->setId($id);
		}
	}
}
