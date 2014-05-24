<?php

/**
 * Driver for SQLite.
 * 

 * Copyright 2008-2012, Alejandro Zuleta (http://phplab.co)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2008-2012, Alejandro Zuleta (http://phplab.co)
 * @link          http://phaxsi.net Phaxsi PHP Framework
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @package       Phaxsi.Database.Drivers
 * @since         Phaxsi v 0.1
 */


final class SQLiteDriver implements IDatabaseDriver{

	private $config;
	private $link;

	function __construct($config){
		$this->config = $config;
	}

	private function connect(){

		if(!is_writable(dirname($this->config['name']))){
			return trigger_error("Permission denied. Cannot create the file '{$this->config['name']}'.", E_USER_ERROR);
		}

		$error_message = '';

		$this->link = new SQLiteDatabase($this->config['name'], 0666, $error_message);

		if(!$this->link){
			return trigger_error("Could not connect to the database '{$this->config['name']}'. Reason: '$error_message'", E_USER_ERROR);
		}			

	}

	function getConnection(){
		if(!$this->link) {
			$this->connect();
		}
		return $this->link;
	}

	function isReady(){
		return file_exists($this->config['name']);
	}

	function query($query){
		if(!$this->link) {
			$this->connect();
		}
		return @$this->link->query($query);
	}

	function quote($text){
		return "'".@sqlite_escape_string ($text)."'";
	}

	function fetchAllRows($result){
		return @$result->fetchAll(SQLITE_ASSOC);
	}

	function fetchAllRowsNum($result){
		return @$result->fetchAll(SQLITE_NUM);
	}

	function fetchAssocArray($result){
		return @$result->fetch(SQLITE_ASSOC);
	}

	function fetchNumArray($result){
		return @$result->fetch(SQLITE_NUM);
	}

	function countRows($result){
		return @$result->numRows();
	}

	function lastInsertId(){
		return @$this->link->lastInsertRowid();
	}

	function lastError(){
		return sqlite_error_string ( $this->link->lastError() );
	}

	function execute($query, $params){

		if(!$this->link) {
			$this->connect();
		}

		$query =  $this->parse($query, $params);

		if(AppConfig::DEBUG_MODE) {
			PluginManager::getInstance()->queryStart($query);
		}

		$result = $this->query($query);

		if(!$result) {
			trigger_error($this->lastError(), E_USER_WARNING);
			return false;
		}

		if(AppConfig::DEBUG_MODE) {
			PluginManager::getInstance()->queryEnd($query);
		}

		return $result;

	}

	private function parse($query, $binds){
		$parts = explode('?', $query);
		for($i=0; $i < count($parts)-1; $i++){
			$parts[$i] .= $this->quote(isset($binds[$i]) ? $binds[$i] : '');
		}
		$query = implode('', $parts);
		return $query;
	}

}
