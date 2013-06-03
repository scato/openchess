<?php

class OpenChess_Test_MoveHelper extends PHPUnit_Framework_TestCase {
	/**
	 * @var OpenChess_Model_Board
	 */
	private $_board;
	
	public function setUp() {
		$this->_board = new OpenChess_Model_Board();
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
	
	private function _placeWhiteRookAt($file, $rank) {
		$type = OpenChess_Model_Piece::TYPE_ROOK;
		
		return $this->_placeWhiteSomethingAt($type, $file, $rank);
	}
	
	private function _placeWhiteKingAt($file, $rank) {
		$type = OpenChess_Model_Piece::TYPE_KING;
		
		return $this->_placeWhiteSomethingAt($type, $file, $rank);
	}
	
	private function _placeWhiteSomethingAt($type, $file, $rank) {
		$color = OpenChess_Model_Piece::COLOR_WHITE;
		$position = $this->_board->findSquareByPosition($file, $rank);
		
		$piece = OpenChess_Model_Piece::createPiece($color, $type, $position);
		$this->_board->addPiece($piece);
		
		return $piece;
	}
	
	public function testPawnSingleMove() {
		$piece = $this->_placeWhitePawnAt('d', 2);
		
		$moveHelper = new OpenChess_Model_MoveHelper($piece);
		$moveHelper->addPawnSingleMove();
		$moves = $moveHelper->getMoves(false);
		
		$this->_assertOneMove($moves);
		$this->_assertMoveTo('d', 3, $moves[0]);
		$this->_assertMoveType(OpenChess_Model_Move::TYPE_PLAIN, $moves[0]);
		
		$piece = $this->_placeBlackPawnAt('d', 7);
		
		$moveHelper = new OpenChess_Model_MoveHelper($piece);
		$moveHelper->addPawnSingleMove();
		$moves = $moveHelper->getMoves(false);
		
		$this->_assertOneMove($moves);
		$this->_assertMoveTo('d', 6, $moves[0]);
		$this->_assertMoveType(OpenChess_Model_Move::TYPE_PLAIN, $moves[0]);
	}
		
	public function testNoPawnSingleMove() {
		$piece = $this->_placeWhitePawnAt('d', 2);
		$oppPiece = $this->_placeBlackPawnAt('d', 3);
		
		$moveHelper = new OpenChess_Model_MoveHelper($piece);
		$moveHelper->addPawnSingleMove();
		$moves = $moveHelper->getMoves(false);
		
		$this->_assertNoMoves($moves);
	}
	
	public function testNoNonPromotionMove() {
		$piece = $this->_placeWhitePawnAt('d', 7);
		
		$moveHelper = new OpenChess_Model_MoveHelper($piece);
		$moveHelper->addPawnSingleMove();
		$moves = $moveHelper->getMoves(false);
		
		$this->_assertNoMoves($moves);
	}
	
	public function testPawnDoubleMove() {
		$piece = $this->_placeWhitePawnAt('d', 2);
		
		$moveHelper = new OpenChess_Model_MoveHelper($piece);
		$moveHelper->addPawnDoubleMove();
		$moves = $moveHelper->getMoves(false);
		
		$this->_assertOneMove($moves);
		$this->_assertMoveTo('d', 4, $moves[0]);
		$this->_assertMoveType(OpenChess_Model_Move::TYPE_TWO_SQUARES, $moves[0]);
		
		$piece = $this->_placeBlackPawnAt('d', 7);
		
		$moveHelper = new OpenChess_Model_MoveHelper($piece);
		$moveHelper->addPawnDoubleMove();
		$moves = $moveHelper->getMoves(false);
		
		$this->_assertOneMove($moves);
		$this->_assertMoveTo('d', 5, $moves[0]);
		$this->_assertMoveType(OpenChess_Model_Move::TYPE_TWO_SQUARES, $moves[0]);
	}
	
	public function testNoPawnDoubleMove() {
		$piece = $this->_placeWhitePawnAt('d', 2);
		$oppPiece = $this->_placeBlackPawnAt('d', 3);
		
		$moveHelper = new OpenChess_Model_MoveHelper($piece);
		$moveHelper->addPawnDoubleMove();
		$moves = $moveHelper->getMoves(false);
		
		$this->_assertNoMoves($moves);
		
		$piece = $this->_placeWhitePawnAt('f', 2);
		
		$moveHelper = new OpenChess_Model_MoveHelper($piece);
		$moveHelper->addPawnSingleMove();
		$moves = $moveHelper->getMoves(false);
		
		// pawn moves to f3
		$this->_assertOneMove($moves);
		$moves[0]->make();
		
		$moveHelper = new OpenChess_Model_MoveHelper($piece);
		$moveHelper->addPawnDoubleMove();
		$moves = $moveHelper->getMoves(false);
		
		$this->_assertNoMoves($moves);
	}
	
	public function testPawnCaptureMove() {
		$whitePawn = $this->_placeWhitePawnAt('d', 3);
		$blackPawn = $this->_placeBlackPawnAt('e', 4);
		
		$moveHelper = new OpenChess_Model_MoveHelper($whitePawn);
		$moveHelper->addPawnCaptureMoves();
		$moves = $moveHelper->getMoves(false);
		
		$this->_assertOneMove($moves);
		$this->_assertMoveTo('e', 4, $moves[0]);
		$this->_assertMoveType(OpenChess_Model_Move::TYPE_CAPTURING, $moves[0]);
		
		$moveHelper = new OpenChess_Model_MoveHelper($blackPawn);
		$moveHelper->addPawnCaptureMoves();
		$moves = $moveHelper->getMoves(false);
		
		$this->_assertOneMove($moves);
		$this->_assertMoveTo('d', 3, $moves[0]);
		$this->_assertMoveType(OpenChess_Model_Move::TYPE_CAPTURING, $moves[0]);
	}
	
	public function testNoPawnCaptureMove() {
		$whitePawn = $this->_placeWhitePawnAt('d', 3);
		$blackPawn = $this->_placeBlackPawnAt('f', 4);
		
		$moveHelper = new OpenChess_Model_MoveHelper($whitePawn);
		$moveHelper->addPawnCaptureMoves();
		$moves = $moveHelper->getMoves(false);
		
		$this->_assertNoMoves($moves);
		
		$moveHelper = new OpenChess_Model_MoveHelper($blackPawn);
		$moveHelper->addPawnCaptureMoves();
		$moves = $moveHelper->getMoves(false);
		
		$this->_assertNoMoves($moves);
	}
	
	public function testPawnCaptureEnPassantMoves() {
		$whitePawn = $this->_placeWhitePawnAt('d', 2);
		$blackPawn = $this->_placeBlackPawnAt('e', 4);
		
		$moveHelper = new OpenChess_Model_MoveHelper($whitePawn);
		$moveHelper->addPawnDoubleMove();
		$moves = $moveHelper->getMoves(false);
		
		// white moves to d4
		$this->_assertOneMove($moves);
		$moves[0]->make();
		
		$moveHelper = new OpenChess_Model_MoveHelper($blackPawn);
		$moveHelper->addPawnCaptureEnPassantMoves();
		$moves = $moveHelper->getMoves(false);
		
		$this->_assertOneMove($moves);
		$this->_assertMoveTo('d', 3, $moves[0]);
		$this->_assertMoveType(OpenChess_Model_Move::TYPE_CAPTURING_EN_PASSANT, $moves[0]);
	}
	
	public function testNoPawnCaptureEnPassantMoves() {
		$whitePawn = $this->_placeWhitePawnAt('d', 3);
		$blackPawn = $this->_placeBlackPawnAt('e', 4);
		
		$moveHelper = new OpenChess_Model_MoveHelper($whitePawn);
		$moveHelper->addPawnSingleMove();
		$moves = $moveHelper->getMoves(false);
		
		// white moves to d4
		$this->_assertOneMove($moves);
		$moves[0]->make();
		
		$moveHelper = new OpenChess_Model_MoveHelper($blackPawn);
		$moveHelper->addPawnCaptureMoves();
		$moves = $moveHelper->getMoves(false);
		
		$this->_assertNoMoves($moves);
	}
	
	public function testPawnPromotionMove() {
		$piece = $this->_placeWhitePawnAt('d', 7);
		
		$moveHelper = new OpenChess_Model_MoveHelper($piece);
		$moveHelper->addPawnPromotionMove();
		$moves = $moveHelper->getMoves(false);
		
		$this->_assertOneMove($moves);
		$this->_assertMoveTo('d', 8, $moves[0]);
		$this->_assertMoveType(OpenChess_Model_Move::TYPE_PROMOTION, $moves[0]);
		$this->_assertPromotionType(OpenChess_Model_Piece::TYPE_NONE, $moves[0]);
	}
		
	public function testNoPawnPromotionMove() {
		$piece = $this->_placeWhitePawnAt('d', 6);
		
		$moveHelper = new OpenChess_Model_MoveHelper($piece);
		$moveHelper->addPawnSingleMove();
		$moveHelper->addPawnPromotionMove();
		$moves = $moveHelper->getMoves(false);
		
		$this->_assertOneMove($moves);
		$this->_assertMoveTo('d', 7, $moves[0]);
		$this->_assertMoveType(OpenChess_Model_Move::TYPE_PLAIN, $moves[0]);
	}
	
	public function testSingleMove() {
		$piece = $this->_placeWhiteQueenAt('d', 4);
		
		$df = rand(-1, 1);
		$dr = rand(-1, 1);
		
		while($df === 0 && $dr === 0) {
			$df = rand(-1, 1);
			$dr = rand(-1, 1);
		}
		
		$targetFile = chr(ord('d') + $df);
		$targetRank = 4 + $dr;
		
		$moveHelper = new OpenChess_Model_MoveHelper($piece);
		$moveHelper->addSingleMove($df, $dr);
		$moves = $moveHelper->getMoves(false);
		
		$this->_assertOneMove($moves);
		$this->_assertMoveTo($targetFile, $targetRank, $moves[0]);
		$this->_assertMoveType(OpenChess_Model_Move::TYPE_PLAIN, $moves[0]);
		
		$this->_placeBlackQueenAt($targetFile, $targetRank);
		
		$moveHelper = new OpenChess_Model_MoveHelper($piece);
		$moveHelper->addSingleMove($df, $dr);
		$moves = $moveHelper->getMoves(false);
		
		$this->_assertOneMove($moves);
		$this->_assertMoveTo($targetFile, $targetRank, $moves[0]);
		$this->_assertMoveType(OpenChess_Model_Move::TYPE_CAPTURING, $moves[0]);
	}
	
	public function testNoSingleMove() {
		$piece = $this->_placeWhiteQueenAt('a', 1);
		
		$moveHelper = new OpenChess_Model_MoveHelper($piece);
		$moveHelper->addSingleMove(-1, 0);
		$moveHelper->addSingleMove(-1, -1);
		$moveHelper->addSingleMove(0, -1);
		$moves = $moveHelper->getMoves(false);
		
		$this->_assertNoMoves($moves);
		
		$piece = $this->_placeWhiteQueenAt('h', 8);
		
		$moveHelper = new OpenChess_Model_MoveHelper($piece);
		$moveHelper->addSingleMove(0, 1);
		$moveHelper->addSingleMove(1, 1);
		$moveHelper->addSingleMove(1, 0);
		$moves = $moveHelper->getMoves(false);
		
		$this->_assertNoMoves($moves);
	}
	
	public function testLinearMoves() {
		$piece = $this->_placeWhiteQueenAt('c', 4);
		
		$moveHelper = new OpenChess_Model_MoveHelper($piece);
		$moveHelper->addLinearMoves(1, 0);
		$this->assertEquals(5, count($moveHelper->getMoves(false)));
		
		$moveHelper = new OpenChess_Model_MoveHelper($piece);
		$moveHelper->addLinearMoves(1, -1);
		$this->assertEquals(3, count($moveHelper->getMoves(false)));
		
		$moveHelper = new OpenChess_Model_MoveHelper($piece);
		$moveHelper->addLinearMoves(0, -1);
		$this->assertEquals(3, count($moveHelper->getMoves(false)));
		
		$moveHelper = new OpenChess_Model_MoveHelper($piece);
		$moveHelper->addLinearMoves(-1, -1);
		$this->assertEquals(2, count($moveHelper->getMoves(false)));
		
		$moveHelper = new OpenChess_Model_MoveHelper($piece);
		$moveHelper->addLinearMoves(-1, 0);
		$this->assertEquals(2, count($moveHelper->getMoves(false)));
		
		$moveHelper = new OpenChess_Model_MoveHelper($piece);
		$moveHelper->addLinearMoves(-1, 1);
		$this->assertEquals(2, count($moveHelper->getMoves(false)));
		
		$moveHelper = new OpenChess_Model_MoveHelper($piece);
		$moveHelper->addLinearMoves(0, 1);
		$this->assertEquals(4, count($moveHelper->getMoves(false)));
		
		$moveHelper = new OpenChess_Model_MoveHelper($piece);
		$moveHelper->addLinearMoves(1, 1);
		$this->assertEquals(4, count($moveHelper->getMoves(false)));
		
		$moves = $moveHelper->getMoves(false);
		$this->_assertMoveTo('d', 5, $moves[0]);
		$this->_assertMoveType(OpenChess_Model_Move::TYPE_PLAIN, $moves[0]);
		$this->_assertMoveTo('e', 6, $moves[1]);
		$this->_assertMoveType(OpenChess_Model_Move::TYPE_PLAIN, $moves[1]);
		$this->_assertMoveTo('f', 7, $moves[2]);
		$this->_assertMoveType(OpenChess_Model_Move::TYPE_PLAIN, $moves[2]);
		$this->_assertMoveTo('g', 8, $moves[3]);
		$this->_assertMoveType(OpenChess_Model_Move::TYPE_PLAIN, $moves[3]);
		
		$this->_placeBlackQueenAt('f', 7);
		
		$moveHelper = new OpenChess_Model_MoveHelper($piece);
		$moveHelper->addLinearMoves(1, 1);
		$this->assertEquals(3, count($moveHelper->getMoves(false)));
		
		$moves = $moveHelper->getMoves(false);
		$this->_assertMoveTo('d', 5, $moves[0]);
		$this->_assertMoveType(OpenChess_Model_Move::TYPE_PLAIN, $moves[0]);
		$this->_assertMoveTo('e', 6, $moves[1]);
		$this->_assertMoveType(OpenChess_Model_Move::TYPE_PLAIN, $moves[1]);
		$this->_assertMoveTo('f', 7, $moves[2]);
		$this->_assertMoveType(OpenChess_Model_Move::TYPE_CAPTURING, $moves[2]);
	}
	
	public function testLongCastlingMove() {
		$rook = $this->_placeWhiteRookAt('a', 1);
		$king = $this->_placeWhiteKingAt('e', 1);
		
		$moveHelper = new OpenChess_Model_MoveHelper($king);
		$moveHelper->addCastlingMoves();
		$moves = $moveHelper->getMoves(false);
		
		$this->_assertOneMove($moves);
		$this->_assertMoveTo('c', 1, $moves[0]);
		$this->_assertMoveType(OpenChess_Model_Move::TYPE_CASTLING, $moves[0]);
	}
		
	public function testShortCastlingMove() {
		$king = $this->_placeWhiteKingAt('e', 1);
		$rook = $this->_placeWhiteRookAt('h', 1);
		
		$moveHelper = new OpenChess_Model_MoveHelper($king);
		$moveHelper->addCastlingMoves();
		$moves = $moveHelper->getMoves(false);
		
		$this->_assertOneMove($moves);
		$this->_assertMoveTo('g', 1, $moves[0]);
		$this->_assertMoveType(OpenChess_Model_Move::TYPE_CASTLING, $moves[0]);
	}
	
	public function testNoCastlingMoves() {
		$rook1 = $this->_placeWhiteRookAt('a', 2);
		$king = $this->_placeWhiteKingAt('e', 1);
		$rook2 = $this->_placeWhiteRookAt('h', 2);
		
		$moveHelper = new OpenChess_Model_MoveHelper($king);
		$moveHelper->addCastlingMoves();
		$this->_assertNoMoves($moveHelper->getMoves(false));
		
		$moveHelper = new OpenChess_Model_MoveHelper($rook1);
		$moveHelper->addLinearMoves(0, -1);
		$moves = $moveHelper->getMoves(false);
		
		// move rook1 to a1
		$this->_assertOneMove($moves);
		$moves[0]->make();
		
		$moveHelper = new OpenChess_Model_MoveHelper($rook2);
		$moveHelper->addLinearMoves(0, -1);
		$moves = $moveHelper->getMoves(false);
		
		// move rook2 to h1
		$this->_assertOneMove($moves);
		$moves[0]->make();
		
		$moveHelper = new OpenChess_Model_MoveHelper($king);
		$moveHelper->addCastlingMoves();
		$this->_assertNoMoves($moveHelper->getMoves(false));
	}
	
	public function testNoFriendlyFire() {
		$queen = $this->_placeWhiteQueenAt('d', 1);
		$rook = $this->_placeWhiteRookAt('a', 1);
		
		$moveHelper = new OpenChess_Model_MoveHelper($queen);
		$moveHelper->addLinearMoves(-1, 0);
		
		$this->assertEquals(2, count($moveHelper->getMoves(false)));
	}
	
	private function _assertOneMove($moves) {
		try {
			$this->assertEquals(1, count($moves));
			$move = $moves[0];
			$this->assertType('OpenChess_Model_Move', $move);
		} catch(PHPUnit_Framework_ExpectationFailedException $exception) {
			throw new PHPUnit_Framework_ExpectationFailedException("Failed asserting that getMoves() returns one and only one move");
		}
	}
	
	private function _assertNoMoves($moves) {
		try {
			$this->assertEquals(0, count($moves));
		} catch(PHPUnit_Framework_ExpectationFailedException $exception) {
			throw new PHPUnit_Framework_ExpectationFailedException("Failed asserting that getMoves() returns no moves");
		}
	}
	
	private function _assertMoveTo($file, $rank, $move) {
		try {
			$origin = $move->getPiece()->getPosition();
			$destination = $move->getDestination();
			$moveString = $origin->getFile() . $origin->getRank() . '-' . $destination->getFile() . $destination->getRank();
			$this->assertEquals($file, $destination->getFile());
			$this->assertEquals($rank, $destination->getRank());
		} catch(PHPUnit_Framework_ExpectationFailedException $exception) {
			throw new PHPUnit_Framework_ExpectationFailedException("Failed asserting that <move:$moveString> has destination <position:$file$rank>");
		}
	}
	
	private function _assertMoveType($type, $move) {
		try {
			$this->assertEquals($type, $move->getType());
		} catch(PHPUnit_Framework_ExpectationFailedException $exception) {
			throw new PHPUnit_Framework_ExpectationFailedException("Failed asserting that move has type <type:$type> instead of <type:" . $move->getType() . ">");
		}
	}
	
	private function _assertPromotionType($type, $move) {
		try {
			$this->assertEquals($type, $move->getPromotionType());
		} catch(PHPUnit_Framework_ExpectationFailedException $exception) {
			throw new PHPUnit_Framework_ExpectationFailedException("Failed asserting that move has promotionType <type:$type>");
		}
	}
	
	public function testNoCastlingMoveWhenInCheck() {
		$rook = $this->_placeWhiteRookAt('a', 1);
		$king = $this->_placeWhiteKingAt('e', 1);
		$this->_placeBlackQueenAt('e', 5);
		
		$moveHelper = new OpenChess_Model_MoveHelper($king);
		$moveHelper->addCastlingMoves();
		$moves = $moveHelper->getMoves(true);
		
		$this->_assertNoMoves($moves);
	}
	
	public function testNoFutileMovesWhenInCheck() {
		$rook = $this->_placeWhiteRookAt('a', 1);
		$king = $this->_placeWhiteKingAt('e', 1);
		$this->_placeBlackQueenAt('e', 5);
		
		$moveHelper = new OpenChess_Model_MoveHelper($rook);
		$moveHelper->addLinearMoves(1, 0);
		$moves = $moveHelper->getMoves(true);
		
		$this->_assertNoMoves($moves);
	}
	
	public function testNoSuicideMoves() {
		$rook = $this->_placeWhiteRookAt('e', 2);
		$king = $this->_placeWhiteKingAt('e', 1);
		$this->_placeBlackQueenAt('e', 5);
		
		$moveHelper = new OpenChess_Model_MoveHelper($rook);
		$moveHelper->addLinearMoves(1, 0);
		$moves = $moveHelper->getMoves(true);
		
		$this->_assertNoMoves($moves);
	}
}
