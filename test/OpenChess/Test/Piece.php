<?php

class OpenChess_Test_Piece extends PHPUnit_Framework_TestCase {
	private $_piece_colors;
	private $_piece_types;
	private $_board;
	
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
		
		$this->_board = new OpenChess_Model_Board();
	}
	
	private function _getRandomColor() {
		$key = array_rand($this->_piece_colors);
		
		return $this->_piece_colors[$key];
	}
	
	private function _getRandomType() {
		$key = array_rand($this->_piece_types);
		
		return $this->_piece_types[$key];
	}
	
	public function testGetColor() {
		$color = $this->_getRandomColor();
		$type = $this->_getRandomType();
		$square = $this->_board->findSquareByPosition('a', 1);
		$piece = OpenChess_Model_Piece::createPiece($color, $type, $square);
		
		$this->assertEquals($color, $piece->getColor());
	}
		
	public function testGetType() {
		$color = $this->_getRandomColor();
		$type = $this->_getRandomType();
		$square = $this->_board->findSquareByPosition('a', 1);
		$piece = OpenChess_Model_Piece::createPiece($color, $type, $square);
		
		$this->assertEquals($type, $piece->getType());
	}
			
	public function testGetPosition() {
		$color = $this->_getRandomColor();
		$type = $this->_getRandomType();
		$square = $this->_board->findSquareByPosition('a', 1);
		$piece = OpenChess_Model_Piece::createPiece($color, $type, $square);
		
		$this->assertSame($square, $piece->getPosition());
	}
	
	public function testMoveTo() {
		$color = $this->_getRandomColor();
		$type = $this->_getRandomType();
		$square = $this->_board->findSquareByPosition('a', 1);
		$square2 = $this->_board->findSquareByPosition('b', 2);
		$piece = OpenChess_Model_Piece::createPiece($color, $type, $square);
		
		$piece->moveTo($square2);
		
		$this->assertSame($square2, $piece->getPosition());
	}
	
	public function testGetHasMoved() {
		$color = $this->_getRandomColor();
		$type = $this->_getRandomType();
		$square = $this->_board->findSquareByPosition('d', 3);
		$piece = OpenChess_Model_Piece::createPiece($color, $type, $square);
		$this->_board->addPiece($piece);
		
		$this->assertFalse($piece->getHasMoved());
		
		$moves = $piece->getValidMoves();
		$move = $moves[0];
		$move->make();
		
		$this->assertTrue($piece->getHasMoved());
	}
	
	public function testGetAdvancedTwoSquares() {
		$color = OpenChess_Model_Piece::COLOR_WHITE;
		$type = OpenChess_Model_Piece::TYPE_PAWN;
		$square = $this->_board->findSquareByPosition('a', 2);
		$piece = OpenChess_Model_Piece::createPiece($color, $type, $square);
		$this->_board->addPiece($piece);
		
		$this->assertFalse($piece->getAdvancedTwoSquares());
		
		$destination = $this->_board->findSquareByPosition('a', 4);
		$move = $piece->findValidMoveByDestination($destination);
		$move->make();
		
		$this->assertTrue($piece->getAdvancedTwoSquares());
		
		$destination = $this->_board->findSquareByPosition('a', 5);
		$move = $piece->findValidMoveByDestination($destination);
		$move->make();
		
		$this->assertFalse($piece->getAdvancedTwoSquares());
	}
	
	public function testIsInCheck() {
		// place white king at e1
		$color = OpenChess_Model_Piece::COLOR_WHITE;
		$type = OpenChess_Model_Piece::TYPE_KING;
		$square = $this->_board->findSquareByPosition('e', 1);
		$king = OpenChess_Model_Piece::createPiece($color, $type, $square);
		$this->_board->addPiece($king);
		
		$this->assertFalse($king->getInCheck());
		
		// place black queen at e5
		$color = OpenChess_Model_Piece::COLOR_BLACK;
		$type = OpenChess_Model_Piece::TYPE_QUEEN;
		$square = $this->_board->findSquareByPosition('e', 5);
		$queen = OpenChess_Model_Piece::createPiece($color, $type, $square);
		$this->_board->addPiece($queen);
		
		$this->assertTrue($king->getInCheck());
		
		// place white rook at e2
		$color = OpenChess_Model_Piece::COLOR_WHITE;
		$type = OpenChess_Model_Piece::TYPE_ROOK;
		$square = $this->_board->findSquareByPosition('e', 2);
		$rook = OpenChess_Model_Piece::createPiece($color, $type, $square);
		$this->_board->addPiece($rook);
		
		$this->assertFalse($king->getInCheck());
	}
}
