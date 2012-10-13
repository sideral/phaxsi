<?php

/**
 * Driver for oci8. 
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

final class OracleDriver implements IDatabaseDriver{

	private $config;
	private $link;

	function __construct($config){
		$this->config = $config;
	}

	private function connect(){
		$this->link =  @oci_connect( $this->config['user'], $this->config['password'], $this->config['connection'])
						or trigger_error("Could not connect to database '{$this->config['driver']}'.", E_USER_ERROR);
		return $this->link != false;
	}

	function query($query){

		if(!$this->link) {
			$this->connect();
		}

		$matches = array();

		preg_match_all('/\' ?<<::(.*?)::>> *\'/', $query, $matches, PREG_SET_ORDER);

		$replacements = array();

		foreach($matches as $idx => $match){
			$query = str_replace($match[0], ':'.$idx, $query);
			$replacements[':'.$idx] = str_replace(array('<|<::', '::>|>'), array('<<::', '::>>'), $match[1]);
		}

		$result = oci_parse($this->link, $query);

		foreach($replacements as $key => $replacement){
			oci_bind_by_name($result, $key, $replacements[$key]);
		}

		if(!$result){
			return false;
		}

		$success = @oci_execute($result);

		if(!$success){
			return false;
		}

		return $result;
	}

	function quote($text){
		$text = str_replace(array('<<::', '::>>'), array('<|<::', '::>|>'), $text);
		return '<<::'.$text.'::>>';
	}

	function fetchAllRows($result){

		$output = array();

		$rows = oci_fetch_all($result, $output, 0, -1, OCI_FETCHSTATEMENT_BY_ROW);

		if($rows === false){
			return array();
		}

		return $output;
	}

	function fetchAllRowsNum($result){

		$output = array();

		$rows = oci_fetch_all($result, $output, 0, -1, OCI_FETCHSTATEMENT_BY_ROW + OCI_NUM);

		if($rows === false){
			return array();
		}

		return $output;
	}

	function fetchAssocArray($result){
		return oci_fetch_assoc($result);
	}

	function fetchNumArray($result){
		return oci_fetch_array($result, OCI_NUM);
	}

	function countRows($result){
		return oci_num_rows($result);
	}

	function lastInsertId(){
		trigger_error("Oracle databases don't support last inserted id. Zero will be returned.", E_USER_WARNING);
		return 0;
	}

	function lastError(){
		$error =  oci_error($this->link);
		return $error['message'];
	}

	function isReady(){
		return true;
	}

	function execute($query, $params){

		if(!$this->link) {
			$this->connect();
		}

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

}
