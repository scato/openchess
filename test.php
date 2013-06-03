<?php

set_include_path(get_include_path() . ':./lib:./src:./test');

require_once 'Zend/Loader.php';
Zend_Loader::registerAutoload();

$runner = new PHPUnit_TextUI_TestRunner();
$suite = $runner->getTest('OpenChess_Test_All');
$runner->doRun($suite);
