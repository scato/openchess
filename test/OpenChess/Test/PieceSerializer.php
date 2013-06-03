<?php

class OpenChess_Test_PieceSerializer extends PHPUnit_Framework_TestCase {
	private $_piece_colors;
	private $_piece_types;
	
	/**
	 * @var OpenChess_Model_Piece
	 */
	private $_piece;
	
	/**
	 * @var OpenChess_Model_PieceSerializer
	 */
	private $_pieceSerializer;
	
	private function _getRandomColor() {
		$key = array_rand($this->_piece_colors);
		
		return $this->_piece_colors[$key];
	}
	
	private function _getRandomType() {
		$key = array_rand($this->_piece_types);
		
		return $this->_piece_types[$key];
	}
	
	public function setUp() {
		$this->_piece_colors = array(
			OpenChess_Model_Piece::COLOR_WHITE,
			OpenChess_Model_Piece::COLOR_BLACK
		);
		
		$this->_piece_types = array(
			OpenChess_Model_Piece::TYPE_BISHOP,
			OpenChess_Model_Piece::TYPE_KING,
			OpenChess_Model_Piece::TYPE_KNIGHT,
			OpenChess_Model_Piece::TYPE_PAWN,
			OpenChess_Model_Piece::TYPE_QUEEN,
			OpenChess_Model_Piece::TYPE_ROOK
		);
		
		$board = new OpenChess_Model_Board();
		$position = $board->findSquareByPosition('g', 6);
		
		$this->_piece = OpenChess_Model_Piece::createPiece($this->_getRandomColor(), $this->_getRandomType(), $position);
		$board->addPiece($this->_piece);
		
		$this->_pieceSerializer = new OpenChess_Model_PieceSerializer();
	}
	
	public function testSerialize() {
		$result = $this->_pieceSerializer->serialize($this->_piece, true);
		
		$this->assertType('array', $result);
		
		$this->assertArrayHasKey('class', $result);
		$this->assertEquals('Piece', $result['class']);
		
		$this->assertArrayHasKey('color', $result);
		$this->assertEquals($this->_piece->getColor(), $result['color']);
		
		$this->assertArrayHasKey('type', $result);
		$this->assertEquals($this->_piece->getType(), $result['type']);
		
		$this->assertArrayHasKey('position', $result);
		$this->assertType('array', $result['position']);
		
		$this->assertArrayHasKey('validMoves', $result);
		$this->assertType('array', $result['validMoves']);
	}
	
	public function testSerializeToMove() {
		$result = $this->_pieceSerializer->serialize($this->_piece, true);
		
		$this->assertNotEquals(0, count($result['validMoves']));
	}
	
	public function testSerializeNotToMove() {
		$result = $this->_pieceSerializer->serialize($this->_piece, false);
		
		$this->assertEquals(0, count($result['validMoves']));
	}
}
