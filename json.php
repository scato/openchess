<?php

require_once 'inc/bootstrap.php';

$service = preg_replace('/&.*$/', '', $self->getQuery());
$className = 'OpenChess_Api_' . $service;

if(!@class_exists($className)) {
	$json = new Zend_Json_Server();
	$fault = $json->fault("Service `$service` not found");
	
	header('Content-Type: application/json');
	echo $fault;
} else {
	$json = new Zend_Json_Server();
	$json->setClass($className);
	
	if ('GET' == $_SERVER['REQUEST_METHOD']) {
		$json->setTarget('json.php');
		$json->setEnvelope(Zend_Json_Server_Smd::ENV_JSONRPC_2);
		
		$smd = $json->getServiceMap();
		
		header('Content-Type: application/json');
		echo $smd;
	} else {
		$json->handle();
	}
}
