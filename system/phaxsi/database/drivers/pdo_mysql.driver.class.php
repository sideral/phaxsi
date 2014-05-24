<?php

/**
 * Driver for PDO/mysql.
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


final class PDO_MySQLDriver implements IDatabaseDriver{

	private $config;
	private $pdo;

	function __construct($config){
		$this->config = $config;
	}

	private function connect(){
		try{
			$this->pdo =  new PDO('mysql:host='.$this->config['host'].';dbname='.$this->config['name'], $this->config['user'], $this->config['password']);
		}
		catch(PDOException $e){
			trigger_error($e->getMessage(), E_USER_ERROR);
		}
	}

	function getConnection(){
		if(!$this->pdo) {
			$this->connect();
		}
		return $this->pdo;
	}

	function query($query){
		if(!$this->pdo) {
			$this->connect();
		}
		return $this->pdo->query($query);
	}

	function quote($text){
		if(!$this->pdo) {
			$this->connect();
		}
		return $this->pdo->quote($text);
	}

	function fetchAllRows($stm){
		return $stm->fetchAll(PDO::FETCH_ASSOC);
	}

	function fetchAllRowsNum($stm){
		return $stm->fetchAll(PDO::FETCH_NUM);
	}

	function fetchAssocArray($stm){
		return $stm->fetch(PDO::FETCH_ASSOC);
	}

	function fetchNumArray($stm){
		return $stm->fetch(PDO::FETCH_NUM);
	}

	function countRows($stm){
		return $stm->rowCount();
	}

	function lastInsertId(){
		return $this->pdo->lastInsertId();
	}

	function lastError(){
		$error = $this->pdo->errorInfo();
		return $error[2];
	}

	function isReady(){
		return true;
	}

	function execute($query, $params){

		if(!$this->pdo) {
			$this->connect();
		}

		if(AppConfig::DEBUG_MODE) {
			$parsed_query = $this->parse($query, $params);
			PluginManager::getInstance()->queryStart($parsed_query);
		}

		$stm = $this->pdo->prepare($query);

		$success = $stm->execute($params);

		if(!$success) {
			$error_info = $stm->errorInfo();
			trigger_error($error_info[2], E_USER_WARNING);
			return false;
		}

		if(AppConfig::DEBUG_MODE) {
			PluginManager::getInstance()->queryEnd($parsed_query);
		}

		return $stm;

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
