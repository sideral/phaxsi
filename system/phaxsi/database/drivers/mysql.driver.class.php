<?php

/**
 * Driver for mysql extension.
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
 * @package       Phaxsi.Database.Drivers
 * @since         Phaxsi v 0.1
 */


final class MySQLDriver implements IDatabaseDriver{

	private $config;
	private $link;


	function __construct($config){
		$this->config = $config;
	}

	private function connect(){
		$this->link =  @mysql_connect($this->config['host'], $this->config['user'], $this->config['password'])
						or trigger_error("Could not connect to database '{$this->config['driver']}'. Reason: ".mysql_error(), E_USER_ERROR);
		return @mysql_select_db($this->config['name'], $this->link)
						or trigger_error("Database '{$this->config['name']}' does not exist", E_USER_ERROR);
	}

	function getConnection(){
		if(!$this->link) {
			$this->connect();
		}
		return $this->link;
	}

	function query($query){

		if(!$this->link) {
			$this->connect();
		}

		return @mysql_query($query, $this->link);
	}

	function quote($text){
		if(!$this->link) {
			$this->connect();
		}
		return "'".@mysql_real_escape_string($text)."'";
	}

	function fetchAllRows($result){
		$multi = array();

		while($row = mysql_fetch_assoc($result)) {
			$multi[] = $row;
		}

		return $multi;
	}

	function fetchAllRowsNum($result){
		$multi = array();

		while($row = mysql_fetch_array($result, MYSQL_NUM)) {
			$multi[] = $row;
		}

		return $multi;
	}

	function fetchAssocArray($result){
		return mysql_fetch_assoc($result);
	}

	function fetchNumArray($result){
		return mysql_fetch_array($result, MYSQL_NUM);
	}

	function countRows($result){
		return mysql_num_rows($result);
	}

	function lastInsertId(){
		return mysql_insert_id();
	}

	function lastError(){
		return mysql_error($this->link);
	}

	function isReady(){
		return true;
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

		if(!$binds){
			return $query;
		}

		$parts = explode('?', $query);
		for($i=0; $i < count($parts)-1; $i++){
			$parts[$i] .= $this->quote(isset($binds[$i]) ? $binds[$i] : '');
		}
		$query = implode('', $parts);
		return $query;
	}

}
