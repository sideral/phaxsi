<?php

/**
 * Caching main class.
 * 

 * Copyright 2008-2012, Alejandro Zuleta (http://phplab.co)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2008-2012, Alejandro Zuleta (http://phplab.co)
 * @link          http://phaxsi.net Phaxsi PHP Framework
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @package       Phaxsi.Cache
 * @since         Phaxsi v 0.1
 */

class PhaxsiCache{

	private $provider;

	private $context;
	private $key;
	private $duration;

	private $is_hit = null;

	function __construct($context){
		$this->context = $context;
	}

	function enable($duration = 3600, $params = array(), $provider = AppConfig::DEFAULT_CACHE_PROVIDER){
		$this->provider = $this->_createProvider($provider);
		$this->duration = $this->_calculateDuration($duration);
		$this->key = $this->_generateKey($params);
	}

	function disable(){
		$this->duration = 0;
	}

	private function _createProvider($provider){
		$class_name = 'Cache'.$provider.'Provider';
		return new $class_name();
	}

	function isHit(){

		if(!$this->isEnabled()){
			return false;
		}

		if(is_bool($this->is_hit)){
			return $this->is_hit;
		}

		return $this->is_hit = $this->provider->isHit($this->key, $this->duration);

	}

	function isEnabled(){
		return $this->duration && AppConfig::CACHE_ENABLED;
	}

	function setContents($contents){	
		$success = $this->provider->set($this->key, $contents, $this->duration);
		if(!$success){
			trigger_error("Could not cache data with key '$this->key'", E_USER_WARNING);
		}
		return $success;
	}

	function getContents(){
		if($this->isHit()){
			return $this->provider->get($this->key);
		}
		return false;
	}

	function delete(){
		return $this->provider->delete($this->key);
	}

	private function _calculateDuration($duration){

		if(is_numeric($duration)){
			return $duration;
		}

		$matches = array();
		if(!preg_match('/^(\*|[0-9]+) *(s|d|m|h|w)$/', $duration, $matches)){
			trigger_error("Invalid cache duration entered", E_USER_WARNING);
			return 0;
		}

		if($matches[1] == '*'){
			return $matches[2];
		}

		$s = 1;	$m = 60; $h = $m*60;
		$d = $h*24; $w = $d*7; 

		return $matches[1]*${$matches[2]};

	}

	private function _generateKey($params){

		$type_info = PhaxsiConfig::$type_info[$this->context->getType()];

		$file_root = $this->context->getAction() . '.' . $this->context->getType();

		$DS = $type_info['basedir'] == '' ? "": DS;

		$params_str = "";
		if($params){
			foreach($params as $key => $value){
				$params_str .=  preg_replace('/[^a-z0-9_-]/', '_', '_' . $key.'-'.$value);
			}
		}
		else{
			$params_str = $file_root;
		}

		return DS. Lang::getCurrent() . DS .$this->context->getModule() . DS . $type_info['basedir']
							. $DS . $file_root . DS . $params_str;

	}

}
