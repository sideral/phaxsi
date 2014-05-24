<?php

/**
 * Cache provider for storing cache on a Sqlite database.
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

class CacheSQLiteProvider extends DataSource implements ICacheProvider{

	const DBNAME = "cache.sqlite";

	private $contents;

	function __construct(){

		$filename = APPD_CACHE.DS.self::DBNAME;
		$config = array('adapter' => 'sqlite',
						'name' => $filename);
		parent::__construct($config);

		if(!file_exists($filename)){
			$this->createCacheTable();
		}
	}

	private function createCacheTable(){
		$query = 'CREATE TABLE cache
				(cache_id VARCHAR PRIMARY KEY NOT NULL ,
				 contents TEXT NOT NULL,
				 expires INTEGER NOT NULL );';
		$this->query($query);
	}

	function delete($key){
		return $this->query("DELETE FROM cache WHERE cache_id = ?", $key);
	}

	function get($key){
		if($this->contents){
			return $this->contents;
		}
		$current_time = time();
		$result = $this->query("SELECT contents FROM cache WHERE cache_id = ? AND expires > ?", $key, $current_time);
		return $result->fetchScalar();
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

		$result = $this->query("SELECT 1 FROM cache WHERE cache_id = ?", $key);

		if($result->count() > 0){
			/**
			 * Refresh cache
			 */
			$result = $this->query("UPDATE cache
									SET contents = ?, expires = ?
									WHERE cache_id = ?", $value, $expires, $key);
			return !$result->isError();
		}
		else{
			/**
			 * Insert new item
			 */
			$result = $this->query("INSERT INTO cache (cache_id, contents, expires)
									VALUES(?, ?, ?)", $key, $value, $expires);
			return !$result->isError();
		}

	}

	function isHit($key, $duration = 0){
		$this->contents = $this->get($key);
		return $this->contents !== false;
	}

}
