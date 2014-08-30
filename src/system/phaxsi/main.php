<?php

/**
 *
 * The bootstrap file for every web request.
 * 
 * Phaxsi PHP Framework (http://phaxsi.net)
 * Copyright 2008-2014, Alejandro Zuleta (http://phplab.co)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2008-2014, Alejandro Zuleta (http://phplab.co)
 * @link          http://phaxsi.net Phaxsi PHP Framework
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

#This constant should be defined in index.php, which loaded this file.
if(!defined('APPD')){
	exit;
}

#Turn off error reporting if this is not a development enviroment.
if(!AppConfig::DEBUG_MODE){
	error_reporting(0);
}

#Bootstrap some files.
require_once('core/includes.php');

#Create some missing functions.
if(!function_exists('mb_strlen')){
	function mb_strlen($string){
		return strlen($string);
	}
}

if(!function_exists('mb_strtoupper')){
	function mb_strtoupper($string){
		return strtoupper($string);
	}
}

if(!function_exists('mb_substr')){
	function mb_substr($str, $start, $length = null){
		return substr($str, $start, $length);
	}
}

/**
 * Finished loading things? Ok... Let's process the request!
 */

#Make a redirection is the current host is not the same as the configured host.
RedirectHelper::ifDifferentHost();

#Sets the timezone established in AppConfig.
date_default_timezone_set(AppConfig::DEFAULT_TIMEZONE);

#Sets the internal encoding for the mb_ functions.
if(function_exists('mb_internal_encoding')){
    mb_internal_encoding(AppConfig::CHARSET);
	#For English and Japanese use the special 'en' and 'ja', otherwise use unicode simply.
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
#Creates instance of plugin manager.
$plugin_manager = PluginManager::getInstance();

#Creates a context object for this request.
$url_mapper = new UrlMapper();	
$context = $url_mapper->map($_SERVER['REQUEST_URI']);

#Could not create context means there is something wrong in the request.
if(!$context){
	header("HTTP/1.1 400 Bad Request");
	@include(PHAXSI_ERROR_400);
	exit;
}

#For multi-lingual sites, detect if automatic redirect for different languages was set 
#and execute the redirection.
if(AppConfig::$language_redirect && !Lang::wasSet()){
	$lang = Lang::autoDetect();
	RedirectHelper::to(UrlHelper::current($lang));
}

#Custom routers can be used for special functionality.
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

#Get the controller from the router.
$controller = $router->getController($context);

#If no controller was found, show the 404 generic page.
if(!$controller){
	header("HTTP/1.1 404 Not Found");
	@include(PHAXSI_ERROR_404);
	exit;
}

$context = $controller->_getContext();

#Execute the request, first by starting the plugins, executing the controller and 
#finally, ending the requests.
$plugin_manager->requestStart($context);
$controller->_execute();
$plugin_manager->requestEnd($context);

#Time to close any sessions.
Session::end();
