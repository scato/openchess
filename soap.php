<?php

require_once 'inc/bootstrap.php';

ini_set("soap.wsdl_cache_enabled", 0);

$service = $self->getQuery();
$className = 'OpenChess_Api_' . $service;

if(!@class_exists($className)) {
	$soap = new Zend_Soap_Server($wsdlUrl->getUri());
	$fault = $soap->fault("Service `$service` not found");
	
	header('Content-Type: text/xml');
	echo $fault;
} else {
	$wsdlUrl = clone $self;
	$wsdlUrl->setPath(dirname($self->getPath()) . '/wsdl.php');
	
	$soap = new Zend_Soap_Server($wsdlUrl->getUri());
	$soap->setClass($className);
	$soap->registerFaultException('OpenChess_Api_Exception_GameEndedException');
	$soap->registerFaultException('OpenChess_Api_Exception_GameNotFoundException');
	$soap->registerFaultException('OpenChess_Api_Exception_InvalidActionException');
	$soap->registerFaultException('OpenChess_Api_Exception_InvalidMoveException');
	$soap->registerFaultException('OpenChess_Api_Exception_NotControlledByPlayerException');
	$soap->registerFaultException('OpenChess_Api_Exception_OpponentAlreadyFoundException');
	$soap->registerFaultException('OpenChess_Api_Exception_OpponentMismatchException');
	$soap->registerFaultException('OpenChess_Api_Exception_OpponentNotAvailableException');
	$soap->registerFaultException('OpenChess_Api_Exception_OpponentNotFoundException');
	$soap->registerFaultException('OpenChess_Api_Exception_OpponentNotYetFoundException');
	$soap->registerFaultException('OpenChess_Api_Exception_PlayerAlreadyCreatedException');
	$soap->registerFaultException('OpenChess_Api_Exception_PlayerMismatchException');
	$soap->registerFaultException('OpenChess_Api_Exception_PlayerNotFoundException');
	$soap->registerFaultException('OpenChess_Api_Exception_PlayerNotToMoveException');
	$soap->registerFaultException('OpenChess_Api_Exception_PlayerNotYetCreatedException');
	$soap->registerFaultException('OpenChess_Api_Exception_SessionNotFoundException');
	$soap->registerFaultException('OpenChess_Api_Exception_SquareNotOccupiedException');
	$soap->registerFaultException('OpenChess_Api_Exception_GameAlreadyStartedException');
	$soap->handle();
}
