<?php

class OpenChess_Test_Square extends PHPUnit_Framework_TestCase {
	private $_square_files;
	private $_square_ranks;
	private $_board;
	
	public function setUp() {
		$this->_square_files = array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h');
		$this->_square_ranks = array(1, 2, 3, 4, 5, 6, 7, 8);
		
		$this->_board = new OpenChess_Model_Board();
	}
	
	private function _getRandomFile() {
		$key = array_rand($this->_square_files);
		
		return $this->_square_files[$key];
	}
	
	private function _getRandomRank() {
		$key = array_rand($this->_square_ranks);
		
		return $this->_square_ranks[$key];
	}
	
	public function testGetFile() {
		$file = $this->_getRandomFile();
		$rank = $this->_getRandomRank();
		
		$square = new OpenChess_Model_Square($this->_board, $file, $rank);
		
		$this->assertEquals($file, $square->getFile());
	}
		
	public function testGetRank() {
		$file = $this->_getRandomFile();
		$rank = $this->_getRandomRank();
		
		$square = new OpenChess_Model_Square($this->_board, $file, $rank);
		
		$this->assertEquals($rank, $square->getRank());
	}
	
	public function testGetBoard() {
		$file = $this->_getRandomFile();
		$rank = $this->_getRandomRank();
		
		$square = new OpenChess_Model_Square($this->_board, $file, $rank);
		
		$this->assertEquals($this->_board, $square->getBoard());
	}
	
	public function testEquals() {
		$file1 = $this->_getRandomFile();
		$rank1 = $this->_getRandomRank();
		
		$square1 = new OpenChess_Model_Square($this->_board, $file1, $rank1);
		
		$this->assertTrue($square1->equals($square1));
		
		do {
			$file2 = $this->_getRandomFile();
			$rank2 = $this->_getRandomRank();
		} while($file1 === $file2 || $rank1 === $rank2);
		
		$square2 = new OpenChess_Model_Square($this->_board, $file2, $rank2);
		
		$this->assertFalse($square2->equals($square1));
	}
	
	public function testGetSetOccupier() {
		$file = $this->_getRandomFile();
		$rank = $this->_getRandomRank();
		
		$square = new OpenChess_Model_Square($this->_board, $file, $rank);
		
		$this->assertNull($square->getOccupier());
		
		$occupier = OpenChess_Model_Piece::createPiece(OpenChess_Model_Piece::COLOR_WHITE, OpenChess_Model_Piece::TYPE_QUEEN, $square);
		
		$this->assertEquals($occupier, $square->getOccupier());
	}
}
