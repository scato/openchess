<?php

/**
 * A square on the board
 *
 * @package OpenChess_Model
 */
class OpenChess_Model_Square {
	private $_board;
	private $_file;
	private $_rank;
	private $_occupier = null;
	
	/**
	 * @param OpenChess_Model_Board $board
	 * @param string $file
	 * @param int $rank
	 */
	public function __construct($board, $file, $rank) {
		$this->_board = $board;
		$this->_file = $file;
		$this->_rank = $rank;
	}
	
	/**
	 * Get the board this square belongs to
	 * 
	 * @return OpenChess_Model_Board
	 */
	public function getBoard() {
		return $this->_board;
	}
	
	/**
	 * Get this squares file
	 *
	 * one of 'a' to 'h'
	 * 
	 * @return string
	 */
	public function getFile() {
		return $this->_file;
	}
	
	/**
	 * Get this squares rank
	 * 
	 * one of 1 to 8
	 *
	 * @return int
	 */
	public function getRank() {
		return $this->_rank;
	}
	
	/**
	 * Compare this square to another one
	 * 
	 * @param OpenChess_Model_Square $square
	 * @return boolean
	 */
	public function equals($square) {
		return $this->_file === $square->_file && $this->_rank === $square->_rank;
	}
	
	/**
	 * Get the piece that occupies this square
	 * 
	 * @return OpenChess_Model_Piece
	 */
	public function getOccupier() {
		return $this->_occupier;
	}
	
	/**
	 * Set the piece that occupies this square
	 * 
	 * @param OpenChess_Model_Piece $occupier
	 * @return void
	 */
	public function setOccupier($occupier) {
		$this->_occupier = $occupier;
	}
}
