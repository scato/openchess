<?php

set_include_path(get_include_path() . ':./lib:./src');

require_once 'Zend/Loader.php';
Zend_Loader::registerAutoload();

$self = Zend_Uri::factory('http');
$self->setHost($_SERVER['HTTP_HOST']);
$self->setPort($_SERVER['SERVER_PORT'] == '80' ? '' : $_SERVER['SERVER_PORT']);
$self->setPath(preg_replace('/^([^?]*)(?:\\?(.*))?$/', '\\1', $_SERVER['REQUEST_URI']));
$self->setQuery(preg_replace('/^([^?]*)(?:\\?(.*))?$/', '\\2', $_SERVER['REQUEST_URI']));

$base = clone $self;
$base->setPath(dirname($base->getPath()) . '/');
$base->setQuery('');

$db = new Zend_Db_Adapter_Pdo_Mysql(array(
    'host'     => '127.0.0.1',
    'username' => 'root',
    'password' => 'sC945o',
    'dbname'   => 'openchessapi'
));

Zend_Db_Table_Abstract::setDefaultAdapter($db);
