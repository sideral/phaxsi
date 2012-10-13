<?php

/**
 *
 * Phaxsi PHP Framework (http://phaxsi.net)
 * Copyright 2008-2012, Alejandro Zuleta (http://slopeone.net)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2008-2012, Alejandro Zuleta (http://slopeone.net)
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

/**
 * Finished loading things? Ok... Let's process the request!
 */

RedirectHelper::ifDifferentHost();

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

#Undo magic quotes (From http://www.php.net/manual/en/security.magicquotes.disabling.php#91653)
if (get_magic_quotes_gpc()) {
	function undo_magic_quotes(&$value, $key) {$value = stripslashes($value);}
	$gpc = array(&$_GET, &$_POST, &$_COOKIE, &$_REQUEST);
	array_walk_recursive($gpc, 'undo_magic_quotes');
}

$plugin_manager = PluginManager::getInstance();

$url_mapper = new UrlMapper();	
$context = $url_mapper->map($_SERVER['REQUEST_URI']);

if(!$context){
	header("HTTP/1.1 400 Bad Request");
	@include(PHAXSI_ERROR_400);
	exit;
}

if(AppConfig::$language_redirect && !Lang::wasSet()){
	$lang = Lang::autoDetect();
	RedirectHelper::to(UrlHelper::current($lang));
}

if(AppConfig::CUSTOM_ROUTER){
	$router_name = Loader::includeApplicationClass(new Context('router', AppConfig::CUSTOM_ROUTER));
	if($router_name)
		$router = new $router_name();
	else
		trigger_error('Custom router "'. AppConfig::CUSTOM_ROUTER.'" was not found', E_USER_ERROR);
}
else{
	$router = new Router();
}

$controller = $router->getController($context);

if(!$controller){
	header("HTTP/1.1 404 Not Found");
	@include(PHAXSI_ERROR_404);
	exit;
}

$context = $controller->_getContext();
$plugin_manager->requestStart($context);
$controller->_execute();
$plugin_manager->requestEnd($context);

Session::end();
