<?php

class OpenChess_Test_Board extends PHPUnit_Framework_TestCase {
	private $_square_files;
	private $_square_ranks;
	
	public function setUp() {
		$this->_square_files = array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h');
		$this->_square_ranks = array(1, 2, 3, 4, 5, 6, 7, 8);
	}
	
	private function _getRandomFile() {
		$key = array_rand($this->_square_files);
		
		return $this->_square_files[$key];
	}
	
	private function _getRandomRank() {
		$key = array_rand($this->_square_ranks);
		
		return $this->_square_ranks[$key];
	}
	
	public function testFindSquare() {
		$board = new OpenChess_Model_Board();
		$file = $this->_getRandomFile();
		$rank = $this->_getRandomRank();
		
		$square = $board->findSquareByPosition($file, $rank);
		
		$this->assertType('OpenChess_Model_Square', $square);
		$this->assertEquals($file, $square->getFile());
		$this->assertEquals($rank, $square->getRank());
	}
	
	public function testAddPiece() {
		$board = new OpenChess_Model_Board();
		$file = $this->_getRandomFile();
		$rank = $this->_getRandomRank();
		$color = OpenChess_Model_Piece::COLOR_WHITE;
		$type = OpenChess_Model_Piece::TYPE_PAWN;
		$square = $board->findSquareByPosition($file, $rank);
		$piece = OpenChess_Model_Piece::createPiece($color, $type, $square);
		
		$board->addPiece($piece);
		
		$this->assertContains($piece, $board->getPieces());
	}
	
	public function testRemovePiece() {
		$board = new OpenChess_Model_Board();
		$file = $this->_getRandomFile();
		$rank = $this->_getRandomRank();
		$color = OpenChess_Model_Piece::COLOR_WHITE;
		$type = OpenChess_Model_Piece::TYPE_PAWN;
		$square = $board->findSquareByPosition($file, $rank);
		$piece = OpenChess_Model_Piece::createPiece($color, $type, $square);
		
		$board->addPiece($piece);
		$board->removePiece($piece);
		
		$this->assertNotContains($piece, $board->getPieces());
	}
	
	public function testFindPiece() {
		$board = new OpenChess_Model_Board();
		$file = $this->_getRandomFile();
		$rank = $this->_getRandomRank();
		$color = OpenChess_Model_Piece::COLOR_WHITE;
		$type = OpenChess_Model_Piece::TYPE_PAWN;
		$square = $board->findSquareByPosition($file, $rank);
		$piece = OpenChess_Model_Piece::createPiece($color, $type, $square);
		$board->addPiece($piece);
		
		$testPiece = $board->findPieceByPosition($file, $rank);
		
		$this->assertSame($piece, $testPiece);
	}
	
	public function testNoPieceFound() {
		$board = new OpenChess_Model_Board();
		$file = $this->_getRandomFile();
		$rank = $this->_getRandomRank();
		
		$testPiece = $board->findPieceByPosition($file, $rank);
		
		$this->assertNull($testPiece);
	}
	
	public function testSetup() {
		$board = new OpenChess_Model_Board();
		$board->setup();
		
		$color_white = OpenChess_Model_Piece::COLOR_WHITE;
		$color_black = OpenChess_Model_Piece::COLOR_BLACK;
		
		$type_rook = OpenChess_Model_Piece::TYPE_ROOK;
		$type_knight = OpenChess_Model_Piece::TYPE_KNIGHT;
		$type_bishop = OpenChess_Model_Piece::TYPE_BISHOP;
		$type_queen = OpenChess_Model_Piece::TYPE_QUEEN;
		$type_king = OpenChess_Model_Piece::TYPE_KING;
		$type_pawn = OpenChess_Model_Piece::TYPE_PAWN;
		
		$this->_assertTypeAndColorAt($type_rook,   $color_white, $board, 'a', 1);
		$this->_assertTypeAndColorAt($type_knight, $color_white, $board, 'b', 1);
		$this->_assertTypeAndColorAt($type_bishop, $color_white, $board, 'c', 1);
		$this->_assertTypeAndColorAt($type_queen,  $color_white, $board, 'd', 1);
		$this->_assertTypeAndColorAt($type_king,   $color_white, $board, 'e', 1);
		$this->_assertTypeAndColorAt($type_bishop, $color_white, $board, 'f', 1);
		$this->_assertTypeAndColorAt($type_knight, $color_white, $board, 'g', 1);
		$this->_assertTypeAndColorAt($type_rook,   $color_white, $board, 'h', 1);
		
		$this->_assertTypeAndColorAt($type_pawn,   $color_white, $board, 'a', 2);
		$this->_assertTypeAndColorAt($type_pawn,   $color_white, $board, 'b', 2);
		$this->_assertTypeAndColorAt($type_pawn,   $color_white, $board, 'c', 2);
		$this->_assertTypeAndColorAt($type_pawn,   $color_white, $board, 'd', 2);
		$this->_assertTypeAndColorAt($type_pawn,   $color_white, $board, 'e', 2);
		$this->_assertTypeAndColorAt($type_pawn,   $color_white, $board, 'f', 2);
		$this->_assertTypeAndColorAt($type_pawn,   $color_white, $board, 'g', 2);
		$this->_assertTypeAndColorAt($type_pawn,   $color_white, $board, 'h', 2);
		
		for($i = 3; $i <= 6; $i++) {
			$this->_assertNullAt($board, 'a', $i);
			$this->_assertNullAt($board, 'b', $i);
			$this->_assertNullAt($board, 'c', $i);
			$this->_assertNullAt($board, 'd', $i);
			$this->_assertNullAt($board, 'e', $i);
			$this->_assertNullAt($board, 'f', $i);
			$this->_assertNullAt($board, 'g', $i);
			$this->_assertNullAt($board, 'h', $i);
		}
		
		$this->_assertTypeAndColorAt($type_pawn,   $color_black, $board, 'a', 7);
		$this->_assertTypeAndColorAt($type_pawn,   $color_black, $board, 'b', 7);
		$this->_assertTypeAndColorAt($type_pawn,   $color_black, $board, 'c', 7);
		$this->_assertTypeAndColorAt($type_pawn,   $color_black, $board, 'd', 7);
		$this->_assertTypeAndColorAt($type_pawn,   $color_black, $board, 'e', 7);
		$this->_assertTypeAndColorAt($type_pawn,   $color_black, $board, 'f', 7);
		$this->_assertTypeAndColorAt($type_pawn,   $color_black, $board, 'g', 7);
		$this->_assertTypeAndColorAt($type_pawn,   $color_black, $board, 'h', 7);
		
		$this->_assertTypeAndColorAt($type_rook,   $color_black, $board, 'a', 8);
		$this->_assertTypeAndColorAt($type_knight, $color_black, $board, 'b', 8);
		$this->_assertTypeAndColorAt($type_bishop, $color_black, $board, 'c', 8);
		$this->_assertTypeAndColorAt($type_queen,  $color_black, $board, 'd', 8);
		$this->_assertTypeAndColorAt($type_king,   $color_black, $board, 'e', 8);
		$this->_assertTypeAndColorAt($type_bishop, $color_black, $board, 'f', 8);
		$this->_assertTypeAndColorAt($type_knight, $color_black, $board, 'g', 8);
		$this->_assertTypeAndColorAt($type_rook,   $color_black, $board, 'h', 8);
	}
	
	public function testGetSetLastMove() {
		$board = new OpenChess_Model_Board();
		
		$this->assertNull($board->getLastMove());
		
		$position = $board->findSquareByPosition('a', 1);
		$piece = OpenChess_Model_Piece::createPiece(OpenChess_Model_Piece::COLOR_WHITE, OpenChess_Model_Piece::TYPE_ROOK, $position);
		
		$destination = $board->findSquareByPosition('a', 2);
		$move = new OpenChess_Model_Move(OpenChess_Model_Move::TYPE_PLAIN, $piece, $destination);
		
		$board->setLastMove($move);
		
		$this->assertEquals($move, $board->getLastMove());
	}
	
	private function _assertTypeAndColorAt($type, $color, $board, $file, $rank) {
		try {
			$piece = $board->findPieceByPosition($file, $rank);
			
			$this->assertType('OpenChess_Model_Piece', $piece);
			$this->assertEquals($type, $piece->getType());
			$this->assertEquals($color, $piece->getColor());
		} catch(PHPUnit_Framework_ExpectationFailedException $exception) {
			throw new PHPUnit_Framework_ExpectationFailedException("Failed asserting that a <color:$color> <type:$type> is at <position:$file$rank>");
		}
	}
	
	private function _assertNullAt($board, $file, $rank) {
		try {
			$piece = $board->findPieceByPosition($file, $rank);
			
			$this->assertNull($piece);
		} catch(PHPUnit_Framework_ExpectationFailedException $exception) {
			throw new PHPUnit_Framework_ExpectationFailedException("Failed asserting that nothing is at <position:$file$rank>");
		}
	}
	
	public function testFindKing() {
		$board = new OpenChess_Model_Board();
		$board->setup();
		
		// find the black king
		$king = $board->findKingByColor(OpenChess_Model_Piece::COLOR_BLACK);
		
		$this->assertType('OpenChess_Model_Piece_King', $king);
		$this->assertEquals(OpenChess_Model_Piece::COLOR_BLACK, $king->getColor());
	}
	
	public function testCopy() {
		$board = new OpenChess_Model_Board();
		
		$oppQueen = OpenChess_Model_Piece::createPiece(
			OpenChess_Model_Piece::COLOR_BLACK,
			OpenChess_Model_Piece::TYPE_QUEEN,
			$board->findSquareByPosition('d', 5)
		);
		
		$oppPawn = OpenChess_Model_Piece::createPiece(
			OpenChess_Model_Piece::COLOR_BLACK,
			OpenChess_Model_Piece::TYPE_PAWN,
			$board->findSquareByPosition('e', 5)
		);
		
		$piece = OpenChess_Model_Piece::createPiece(
			OpenChess_Model_Piece::COLOR_WHITE,
			OpenChess_Model_Piece::TYPE_QUEEN,
			$board->findSquareByPosition('d', 3)
		);
		
		$board->addPiece($oppQueen);
		$board->addPiece($oppPawn);
		$board->addPiece($piece);
		
		$copy = $board->copy();
		
		// on the copied board d5 contains a black queen, e5 contains a black pawn and d3 contains a white queen
		$this->assertEquals(OpenChess_Model_Piece::TYPE_QUEEN, $copy->findPieceByPosition('d', 5)->getType());
		$this->assertEquals(OpenChess_Model_Piece::COLOR_BLACK, $copy->findPieceByPosition('d', 5)->getColor());
		$this->assertEquals(OpenChess_Model_Piece::TYPE_PAWN, $copy->findPieceByPosition('e', 5)->getType());
		$this->assertEquals(OpenChess_Model_Piece::COLOR_BLACK, $copy->findPieceByPosition('e', 5)->getColor());
		$this->assertEquals(OpenChess_Model_Piece::TYPE_QUEEN, $copy->findPieceByPosition('d', 3)->getType());
		$this->assertEquals(OpenChess_Model_Piece::COLOR_WHITE, $copy->findPieceByPosition('d', 3)->getColor());
		
		// now move the white queen on the original board
		$type = OpenChess_Model_Move::TYPE_CAPTURING;
		$destination = $board->findSquareByPosition('d', 5);
		
		$move = new OpenChess_Model_Move($type, $piece, $destination);
		$move->make();
		
		// nothing changed on the copied board
		$this->assertEquals(OpenChess_Model_Piece::TYPE_QUEEN, $copy->findPieceByPosition('d', 5)->getType());
		$this->assertEquals(OpenChess_Model_Piece::COLOR_BLACK, $copy->findPieceByPosition('d', 5)->getColor());
		$this->assertEquals(OpenChess_Model_Piece::TYPE_PAWN, $copy->findPieceByPosition('e', 5)->getType());
		$this->assertEquals(OpenChess_Model_Piece::COLOR_BLACK, $copy->findPieceByPosition('e', 5)->getColor());
		$this->assertEquals(OpenChess_Model_Piece::TYPE_QUEEN, $copy->findPieceByPosition('d', 3)->getType());
		$this->assertEquals(OpenChess_Model_Piece::COLOR_WHITE, $copy->findPieceByPosition('d', 3)->getColor());
	}
}
