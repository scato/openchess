<?php

require_once 'inc/bootstrap.php';

$parts = explode('&', $_SERVER['QUERY_STRING']);
$request = new Zend_Json_Server_Request();

$reflect = true;
foreach($parts as $i => $part) {
	if($i === 0) {
		$service = $part;
	} elseif(!preg_match('/=/', $part)) {
		$reflect = false;
		$request->loadJson(rawurldecode($part));
	}
}

$className = 'OpenChess_Api_' . $service;
$callback = $_GET['callback'];

if(!@class_exists($className)) {
	$json = new Zend_Json_Server();
	$fault = $json->fault("Service `$service` not found [{$self->getQuery()}]");
	
	header('Content-Type: application/javascript');
	echo "$callback($fault)";
} else {
	$json = new Zend_Json_Server();
	$json->setClass($className);
	
	if ($reflect) {
		$json->setTarget('jsonp.php');
		$json->setEnvelope(Zend_Json_Server_Smd::ENV_JSONRPC_2);
		
		$smd = $json->getServiceMap();
		
		header('Content-Type: application/javascript');
		echo "$callback($smd)";
	} else {
		ob_start();
		$json->handle($request);
		$result = ob_get_contents();
		ob_end_clean();
		
		header('Content-Type: application/javascript');
		echo "$callback($result)";
	}
}
