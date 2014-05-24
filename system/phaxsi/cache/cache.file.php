<?php

/**
 * Cache provider for storing cache on the filesystem.
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

class CacheFileProvider implements ICacheProvider{

	const FILE_EXTENSION = 'html';

	function get($key){
		$filename = $this->getFilename($key);
		return @file_get_contents($filename);
	}

	function set($key, $value, $duration){
		$this->createDirectories($key);
		$filename = $this->getFilename($key);
		return @file_put_contents($filename, $value, LOCK_EX);
	}

	function delete($key){
		$filename = $this->getFilename($key);
		$success = @unlink($filename);
		clearstatcache();
		return (bool)$success;
	}

	function isHit($key, $duration = 0){

		$filename = $this->getFilename($key);

		if(!@file_exists($filename)){
			return  false;
		}

		$filetime = @filemtime($filename);

		if((int)$duration){
			return $filetime > (time() - $duration);
		}

		$is_hit = false;

		switch($duration){
			case 'h':
				$is_hit = date('Y:z:H', $filetime) == date('Y:z:H');
				break;
			case 'd':
				$is_hit = date('Y:z', $filetime) == date('Y:z');
				break;
			case 'm':
				$is_hit = date('Y:z:H:i', $filetime) == date('Y:z:H:i');
				break;
			case 'w':
				$is_hit = date('Y:W', $filetime) == date('Y:W');
				break;
			default:
				break;
		}

		return $is_hit;

	}

	private function getFilename($key){
		return APPD_CACHE . $key . '.' . self::FILE_EXTENSION;
	}

	private function createDirectories($key){

		$key_parts = explode(DS,$key);

		$dir = APPD_CACHE . DS. $key_parts[1]. DS. $key_parts[2]. DS . $key_parts[3];

		$old_umask = umask(0);

		if(!is_dir($dir)){
			if(!@mkdir($dir, 0777, true)){
				trigger_error("Cache directory '$dir' cannot be created", E_USER_WARNING);
				return;
			}
		}

		umask($old_umask);

	}

}
