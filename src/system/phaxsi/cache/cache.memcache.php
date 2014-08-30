<?php

/**
 * Cache provider for storing cache on memcache.
 * 

 * Copyright 2008-2014, Alejandro Zuleta (http://phplab.co)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2008-2014, Alejandro Zuleta (http://phplab.co)
 * @link          http://phaxsi.net Phaxsi PHP Framework
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @package       Phaxsi.Cache
 * @since         Phaxsi v 0.1
 */

class CacheMemcacheProvider implements ICacheProvider{

	const HOST = "localhost";
	const PORT = 11211;

	private $memcache;
	private $contents;

	function __construct(){
		if(!class_exists('Memcache')){
			trigger_error('Memcache is unavailable', E_USER_ERROR);
			return;
		}
		$this->memcache = new Memcache();
		$this->memcache->connect(self::HOST,self::PORT);
	}

	function delete($key){
		return $this->memcache->delete($key);
	}

	function get($key){
		if($this->contents){
			return $this->contents;
		}
		return $this->memcache->get($key);
	}

	function set($key, $value, $duration){

		if(is_string($duration)){
			$current_time = time();
			$date = getdate();
			switch($duration){
				case 's':
					$expires = mktime($date['hours'], $date['minutes'], $date['seconds']+1);
					break;
				case 'm':
					$expires = mktime($date['hours'], $date['minutes']+1, 0);
					break;
				case 'h':
					$expires = mktime($date['hours']+1, 0, 0);
					break;
				case 'd':
					$expires = mktime(0, 0, 0, $date['mon'], $date['mday'] + 1);
					break;
				case 'w':
					$expires = mktime(0, 0, 0, $date['mon'], $date['mday'] + 7 - $date['wday']);
					break;
				default:
					$expires = $current_time;
			}
		}
		else{
			$expires = time() + $duration;
		}

		if(!$expires){
			return false;
		}

		$this->memcache->set($key, $value, false, $expires);
	}

	function isHit($key, $duration = 0){
		if($this->contents = $this->memcache->get($key)){
			return true;
		}
		return false;
	}

}