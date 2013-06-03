<?php

require_once 'inc/bootstrap.php';

$service = $self->getQuery();
$className = 'OpenChess_Api_' . $service;

if(!@class_exists($className)) {
	$soap = new Zend_Soap_Server();
	$fault = $soap->fault("Service `$service` not found");
	
	header('Content-Type: text/xml');
	echo $fault;
} else {
	$soapUrl = clone $self;
	$soapUrl->setPath(dirname($self->getPath()) . '/soap.php');
	
	$autodiscover = new Zend_Soap_AutoDiscover();
	$autodiscover->setClass($className);
	$autodiscover->setUri($soapUrl->getUri());
	$autodiscover->handle();
}
