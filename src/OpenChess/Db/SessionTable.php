<?php

class OpenChess_Db_SessionTable extends Zend_Db_Table_Abstract {
	protected $_name = 'session';
	static protected $_instance = null;
	
	/**
	 * @return OpenChess_Db_SessionTable
	 */
	public static function getInstance() {
		if(self::$_instance === null) {
			self::$_instance = new self();
		}
		
		return self::$_instance;
	}
	
	/**
	 * 
	 * @param int $sessionId
	 * @return OpenChess_Model_Session
	 */
	public function find($sessionId) {
		/* var $rowset Zend_Db_Table_Rowset */
		$rowset = parent::find($sessionId);
		
		if($rowset->count() === 0) {
			return null;
		} else {
			$row = $rowset->getRow(0);
			
			return $this->_load($row);
		}
	}
	
	protected function _load($row) {
		$session = new OpenChess_Model_Session();
		$session->setId($row['id']);
		
		return $session;
	}
	
	/**
	 * @param OpenChess_Model_Session $session
	 */
	public function save($session) {
		$id = $session->getId();
		
		$data = array(
		);
		
		if($id) {
			$where = $this->getAdapter()->quoteInto('`id` = ?', $id);
			
			$this->update($data, $where);
		} else {
			$id = $this->insert($data);
			
			$session->setId($id);
		}
	}
}
