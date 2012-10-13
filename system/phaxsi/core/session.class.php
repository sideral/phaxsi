<?php

/**
 * A simple class for session management
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
 * @package       Phaxsi.Core
 * @since         Phaxsi v 0.1
 */


final class Session{

	private $namespace;

	private static $started = false;

	function __construct($namespace){
		self::start();
		$this->namespace = $namespace;
	}

	function __isset ($key){
		return isset($_SESSION[$this->namespace][$key]);
	}

	function __unset($key){
		unset($_SESSION[$this->namespace][$key]);
	}

	function exists($key, $namespace = null){
		if(is_null($namespace)){
			$namespace = $this->namespace;
		}
		return isset($_SESSION[$namespace][$key]);
	}

	function get($key, $namespace = null){
		if(is_null($namespace)){
			$namespace = $this->namespace;
		}

		return isset($_SESSION[$namespace][$key]) ? $_SESSION[$namespace][$key] : null;
	}

	function set($key, $value, $namespace = null){
		if(is_null($namespace)){
			$namespace = $this->namespace;
		}

		if(!isset($_SESSION[$namespace])){
			$_SESSION[$namespace] = array();
		}

		$_SESSION[$namespace][$key] = $value;
		return $value;
	}

	function delete($key, $namespace = null){
		if(is_null($namespace)){
			$namespace = $this->namespace;
		}
		unset($_SESSION[$namespace][$key]);
	}

	static function setFlash($key, $value){
		self::start();
		if(!isset($_SESSION['phaxsi_flash'])){
			$_SESSION['phaxsi_flash'] = array();
		}

		$_SESSION['phaxsi_flash'][$key] = $value;

		return $value;
	}

	static function getFlash($key){
		self::start();
		if(isset($_SESSION['phaxsi_flash'][$key])){
			return $_SESSION['phaxsi_flash'][$key];
		}
		return null;
	}

	static function start(){
		if(!self::$started){
			session_set_cookie_params(0, AppConfig::BASE_URL);
			session_start();
			self::$started = true;
		}
	}

	static function end($preserve_flash = false){
		if(!$preserve_flash && self::$started)
			unset($_SESSION['phaxsi_flash']);
	}

	static function getId(){
		return session_id();
	}

	static function regenerateId(){
		self::start();
		session_regenerate_id();
	}

	static function destroy(){
		self::start();
		session_unset();
		session_regenerate_id();
		session_destroy();		
	}

}
