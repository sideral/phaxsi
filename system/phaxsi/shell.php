<?php

/**
 *

 * Copyright 2008-2014, Alejandro Zuleta (http://phplab.co)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2008-2014, Alejandro Zuleta (http://phplab.co)
 * @link          http://phaxsi.net Phaxsi PHP Framework
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

if(!defined('APPD')){
	exit;
}

if(!AppConfig::DEBUG_MODE){
	error_reporting(0);
}

require_once('core/includes.php');

require_once(PHAXSIC_SHELL);
require_once(PHAXSIC_SHELLMAPPER);

#Global environment settings
date_default_timezone_set(AppConfig::DEFAULT_TIMEZONE);
if(function_exists('mb_internal_encoding')){
	mb_internal_encoding(AppConfig::CHARSET);
	if(AppConfig::DEFAULT_LANGUAGE == 'en' || AppConfig::DEFAULT_LANGUAGE == 'ja' ){
		mb_language(AppConfig::DEFAULT_LANGUAGE);
	}
	else{
		mb_language('uni');
	}
}

$url_mapper = new ShellMapper();
$context = $url_mapper->map($_SERVER['argv']);

if(!$context){
	trigger_error("Controller or Action not found", E_USER_ERROR);
	exit;
}

$router = new Router();
$controller = $router->getController($context);

if(!$controller){
	trigger_error("Controller or Action not found", E_USER_ERROR);
	exit;
}

$controller->_execute();
