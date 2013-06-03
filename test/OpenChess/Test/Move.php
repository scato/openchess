<?php

class OpenChess_Test_Move extends PHPUnit_Framework_TestCase {
	/**
	 * @var OpenChess_Model_Board
	 */
	private $_board;
	
	public function setUp() {
		$this->_board = new OpenChess_Model_Board();
	}
	
	private function _placeSomeonesQueenAt($color, $file, $rank) {
		$type = OpenChess_Model_Piece::TYPE_QUEEN;
		$position = $this->_board->findSquareByPosition($file, $rank);
		
		$piece = OpenChess_Model_Piece::createPiece($color, $type, $position);
		$this->_board->addPiece($piece);
		
		return $piece;
	}
	
	private function _placeWhiteQueenAt($file, $rank) {
		$color = OpenChess_Model_Piece::COLOR_WHITE;
		
		return $this->_placeSomeonesQueenAt($color, $file, $rank);
	}
	
	private function _placeBlackQueenAt($file, $rank) {
		$color = OpenChess_Model_Piece::COLOR_BLACK;
		
		return $this->_placeSomeonesQueenAt($color, $file, $rank);
	}
	
	private function _placeWhiteSomethingAt($type, $file, $rank) {
		$color = OpenChess_Model_Piece::COLOR_WHITE;
		$position = $this->_board->findSquareByPosition($file, $rank);
		
		$piece = OpenChess_Model_Piece::createPiece($color, $type, $position);
		$this->_board->addPiece($piece);
		
		return $piece;
	}
	
	private function _placeWhiteRookAt($file, $rank) {
		$type = OpenChess_Model_Piece::TYPE_ROOK;
		
		return $this->_placeWhiteSomethingAt($type, $file, $rank);
	}
	
	private function _placeWhiteKingAt($file, $rank) {
		$type = OpenChess_Model_Piece::TYPE_KING;
		
		return $this->_placeWhiteSomethingAt($type, $file, $rank);
	}
	
	private function _placeSomeonesPawnAt($color, $file, $rank) {
		$type = OpenChess_Model_Piece::TYPE_PAWN;
		$position = $this->_board->findSquareByPosition($file, $rank);
		
		$piece = OpenChess_Model_Piece::createPiece($color, $type, $position);
		$this->_board->addPiece($piece);
		
		return $piece;
	}
	
	private function _placeWhitePawnAt($file, $rank) {
		$color = OpenChess_Model_Piece::COLOR_WHITE;
		
		return $this->_placeSomeonesPawnAt($color, $file, $rank);
	}
	
	private function _placeBlackPawnAt($file, $rank) {
		$color = OpenChess_Model_Piece::COLOR_BLACK;
		
		return $this->_placeSomeonesPawnAt($color, $file, $rank);
	}
	
	public function testGetType() {
		$type = OpenChess_Model_Move::TYPE_PLAIN;
		$piece = $this->_placeWhiteQueenAt('d', 3);
		$destination = $this->_board->findSquareByPosition('d', 5);
		
		$move = new OpenChess_Model_Move($type, $piece, $destination);
		
		$this->assertEquals($type, $move->getType());
	}
	
	public function testGetPiece() {
		$type = OpenChess_Model_Move::TYPE_PLAIN;
		$piece = $this->_placeWhiteQueenAt('d', 3);
		$destination = $this->_board->findSquareByPosition('d', 5);
		
		$move = new OpenChess_Model_Move($type, $piece, $destination);
		
		$this->assertSame($piece, $move->getPiece());
	}
		
	public function testGetDestination() {
		$type = OpenChess_Model_Move::TYPE_PLAIN;
		$piece = $this->_placeWhiteQueenAt('d', 3);
		$destination = $this->_board->findSquareByPosition('d', 5);
		
		$move = new OpenChess_Model_Move($type, $piece, $destination);
		
		$this->assertSame($destination, $move->getDestination());
	}
		
	public function testGetSetPromotionType() {
		$type = OpenChess_Model_Move::TYPE_PLAIN;
		$piece = $this->_placeWhiteQueenAt('d', 3);
		$destination = $this->_board->findSquareByPosition('d', 5);
		
		$move = new OpenChess_Model_Move($type, $piece, $destination);
		
		$this->assertEquals(OpenChess_Model_Piece::TYPE_NONE, $move->getPromotionType());
		
		$promotionType = OpenChess_Model_Piece::TYPE_QUEEN;
		$move->setPromotionType($promotionType);
		
		$this->assertEquals($promotionType, $move->getPromotionType());
	}
		
	public function testPlainMove() {
		$type = OpenChess_Model_Move::TYPE_PLAIN;
		$piece = $this->_placeWhiteQueenAt('d', 3);
		$destination = $this->_board->findSquareByPosition('d', 5);
		
		$move = new OpenChess_Model_Move($type, $piece, $destination);
		
		$this->assertSame($piece, $this->_board->findPieceByPosition('d', 3));
		$this->assertNull($this->_board->findPieceByPosition('d', 5));
		
		$move->make();
		
		$this->assertNull($this->_board->findPieceByPosition('d', 3));
		$this->assertSame($piece, $this->_board->findPieceByPosition('d', 5));
	}
		
	public function testCaptureMove() {
		$oppPiece = $this->_placeBlackQueenAt('d', 5);
		
		$type = OpenChess_Model_Move::TYPE_CAPTURING;
		$piece = $this->_placeWhiteQueenAt('d', 3);
		$destination = $this->_board->findSquareByPosition('d', 5);
		
		$move = new OpenChess_Model_Move($type, $piece, $destination);
		
		$this->assertSame($piece, $this->_board->findPieceByPosition('d', 3));
		$this->assertSame($oppPiece, $this->_board->findPieceByPosition('d', 5));
		
		$move->make();
		
		$this->assertNull($this->_board->findPieceByPosition('d', 3));
		$this->assertSame($piece, $this->_board->findPieceByPosition('d', 5));
	}
	
	public function testCastlingMove() {
		$type = OpenChess_Model_Move::TYPE_CASTLING;
		$rook = $this->_placeWhiteRookAt('a', 1);
		$king = $this->_placeWhiteKingAt('e', 1);
		$destination = $this->_board->findSquareByPosition('c', 1);
		
		$move = new OpenChess_Model_Move($type, $king, $destination);
		
		$this->assertSame($rook, $this->_board->findPieceByPosition('a', 1));
		$this->assertNull($this->_board->findPieceByPosition('c', 1));
		$this->assertNull($this->_board->findPieceByPosition('d', 1));
		$this->assertSame($king, $this->_board->findPieceByPosition('e', 1));
		
		$move->make();
		
		$this->assertNull($this->_board->findPieceByPosition('a', 1));
		$this->assertSame($king, $this->_board->findPieceByPosition('c', 1));
		$this->assertSame($rook, $this->_board->findPieceByPosition('d', 1));
		$this->assertNull($this->_board->findPieceByPosition('e', 1));
	}
	
	public function testCaptureEnPassantMove() {
		$type = OpenChess_Model_Move::TYPE_CAPTURING_EN_PASSANT;
		$piece = $this->_placeWhitePawnAt('b', 5);
		$oppPiece = $this->_placeBlackPawnAt('c', 5);
		$destination = $this->_board->findSquareByPosition('c', 6);
		
		$move = new OpenChess_Model_Move($type, $piece, $destination);
		
		$this->assertSame($piece, $this->_board->findPieceByPosition('b', 5));
		$this->assertSame($oppPiece, $this->_board->findPieceByPosition('c', 5));
		
		$move->make();
		
		$this->assertNull($this->_board->findPieceByPosition('b', 5));
		$this->assertNull($this->_board->findPieceByPosition('c', 5));
		$this->assertSame($piece, $this->_board->findPieceByPosition('c', 6));
	}
	
	public function testPromotionMove() {
		$type = OpenChess_Model_Move::TYPE_PROMOTION;
		$pawnType = OpenChess_Model_Piece::TYPE_PAWN;
		$piece = $this->_placeWhitePawnAt('e', 7);
		$destination = $this->_board->findSquareByPosition('e', 8);
		$promotionType = OpenChess_Model_Piece::TYPE_QUEEN;
		
		$move = new OpenChess_Model_Move($type, $piece, $destination);
		$move->setPromotionType($promotionType);
		
		$this->assertNotNull($this->_board->findPieceByPosition('e', 7));
		$this->assertEquals($pawnType, $this->_board->findPieceByPosition('e', 7)->getType());
		$this->assertNull($this->_board->findPieceByPosition('e', 8));
		
		$move->make();
		
		$this->assertNull($this->_board->findPieceByPosition('e', 7));
		$this->assertNotNull($this->_board->findPieceByPosition('e', 8));
		$this->assertEquals($promotionType, $this->_board->findPieceByPosition('e', 8)->getType());
	}
	
	public function testCaptures() {
		$oppQueen = $this->_placeBlackQueenAt('d', 5);
		$oppPawn = $this->_placeBlackPawnAt('e', 5);
		
		$type = OpenChess_Model_Move::TYPE_CAPTURING;
		$piece = $this->_placeWhiteQueenAt('d', 3);
		$destination = $this->_board->findSquareByPosition('d', 5);
		
		$move = new OpenChess_Model_Move($type, $piece, $destination);
		
		$this->assertTrue($move->captures($oppQueen));
		$this->assertFalse($move->captures($oppPawn));
		
		$type = OpenChess_Model_Move::TYPE_PLAIN;
		$piece = $this->_placeWhiteQueenAt('d', 3);
		$destination = $this->_board->findSquareByPosition('e', 3);
		
		$move = new OpenChess_Model_Move($type, $piece, $destination);
		
		$this->assertFalse($move->captures($oppQueen));
	}
	
	public function testSimulate() {
		$oppQueen = $this->_placeBlackQueenAt('d', 5);
		$oppPawn = $this->_placeBlackPawnAt('e', 5);
		
		$type = OpenChess_Model_Move::TYPE_CAPTURING;
		$piece = $this->_placeWhiteQueenAt('d', 3);
		$destination = $this->_board->findSquareByPosition('d', 5);
		
		$move = new OpenChess_Model_Move($type, $piece, $destination);
		
		$simulation = $move->simulate();
		
		// in the simulation d5 contains a white queen, e5 contains a black pawn and d3 is empty
		$this->assertEquals(OpenChess_Model_Piece::TYPE_QUEEN, $simulation->findPieceByPosition('d', 5)->getType());
		$this->assertEquals(OpenChess_Model_Piece::COLOR_WHITE, $simulation->findPieceByPosition('d', 5)->getColor());
		$this->assertEquals(OpenChess_Model_Piece::TYPE_PAWN, $simulation->findPieceByPosition('e', 5)->getType());
		$this->assertEquals(OpenChess_Model_Piece::COLOR_BLACK, $simulation->findPieceByPosition('e', 5)->getColor());
		$this->assertNull($simulation->findPieceByPosition('d', 3));
		
		// on the real board d5 contains a black queen, e5 contains a black pawn and d3 contains a white queen
		$this->assertEquals(OpenChess_Model_Piece::TYPE_QUEEN, $this->_board->findPieceByPosition('d', 5)->getType());
		$this->assertEquals(OpenChess_Model_Piece::COLOR_BLACK, $this->_board->findPieceByPosition('d', 5)->getColor());
		$this->assertEquals(OpenChess_Model_Piece::TYPE_PAWN, $this->_board->findPieceByPosition('e', 5)->getType());
		$this->assertEquals(OpenChess_Model_Piece::COLOR_BLACK, $this->_board->findPieceByPosition('e', 5)->getColor());
		$this->assertEquals(OpenChess_Model_Piece::TYPE_QUEEN, $this->_board->findPieceByPosition('d', 3)->getType());
		$this->assertEquals(OpenChess_Model_Piece::COLOR_WHITE, $this->_board->findPieceByPosition('d', 3)->getColor());
	}
	
	public function testSimulatePromotionMove() {
		$type = OpenChess_Model_Move::TYPE_PROMOTION;
		$pawnType = OpenChess_Model_Piece::TYPE_PAWN;
		$piece = $this->_placeWhitePawnAt('e', 7);
		$destination = $this->_board->findSquareByPosition('e', 8);
		$promotionType = OpenChess_Model_Piece::TYPE_QUEEN;
		
		$move = new OpenChess_Model_Move($type, $piece, $destination);
		
		$move->simulate();
	}
}
