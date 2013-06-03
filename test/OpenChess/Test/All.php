<?php

class OpenChess_Test_All extends PHPUnit_Framework_TestSuite {
	public static function suite() {
		$suite = new self();
		$suite->addTestSuite('OpenChess_Test_Action');
		$suite->addTestSuite('OpenChess_Test_Board');
		$suite->addTestSuite('OpenChess_Test_Game');
		$suite->addTestSuite('OpenChess_Test_GameSerializer');
		$suite->addTestSuite('OpenChess_Test_Move');
		$suite->addTestSuite('OpenChess_Test_MoveHelper');
		$suite->addTestSuite('OpenChess_Test_Piece');
		$suite->addTestSuite('OpenChess_Test_PieceSerializer');
		$suite->addTestSuite('OpenChess_Test_Player');
		$suite->addTestSuite('OpenChess_Test_PlayerSerializer');
		$suite->addTestSuite('OpenChess_Test_Square');
		
		// NOTE: OpenChess_Model_Piece::getValidMoves() test ik niet
		// dat is prima functioneel te testen
		// bovendien test ik MoveHelper uitvoerig
		
		return $suite;
	}
}
