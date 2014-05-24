<?php

/**
 * Base class for plugins
 * 

 * Copyright 2008-2012, Alejandro Zuleta (http://phplab.co)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2008-2012, Alejandro Zuleta (http://phplab.co)
 * @link          http://phaxsi.net Phaxsi PHP Framework
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @package       Phaxsi.Plugins
 * @since         Phaxsi v 0.1
 */


abstract class Plugin{

	protected $config;
	protected $context;
	protected $load, $db;
	protected $name;

	final function __construct($context, $config){
		$this->context = $context;
		$this->config = $config;

		$this->load = new Loader($context);
		$this->db = new DatabaseProxy($this->load);
	}

	final function getName(){
		if($this->name){
			return $this->name;
		}
		else{
			$class_name = get_class($this);
			$suffix_length = strlen(PhaxsiConfig::$type_info['plugin']['suffix']);
			return $this->name = substr($class_name, 0, strlen($class_name)-$suffix_length);
		}
	}

	final function getConfig($key){
		if(!isset($this->config[$key])){
			trigger_error("Config value 'key' does not exist in this plugin.", E_USER_ERROR);
		}
		return $this->config[$key];
	}

	final function __get($name){
		return $this->$name = $this->load->service($name);
	}

	function initialize(){}

	function isEnabled(){
		return $this->config['enabled'];
	}

	function requestStart($context){}
	function requestEnd($context){}

	function queryStart($query){}
	function queryEnd($query){}

	function controllerStart($context){}
	function controllerEnd($context){}

	function renderStart($context){}
	function renderEnd($context){}

	function onRedirect($url){}

}
