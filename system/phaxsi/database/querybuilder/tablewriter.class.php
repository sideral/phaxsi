<?php

/**
 * Class for building write queries.
 * 

 * Copyright 2008-2014, Alejandro Zuleta (http://phplab.co)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2008-2014, Alejandro Zuleta (http://phplab.co)
 * @link          http://phaxsi.net Phaxsi PHP Framework
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @package       Phaxsi.Database.QueryBuilder
 * @since         Phaxsi v 0.1
 */

require_once(PHAXSIC_TABLEQUERYBUILDER);

class TableWriter extends TableQueryBuilder{

	function insert($values){
		$result = $this->query($this->_setupInsertStatement($values));
		if(!$result){
			return false;
		}
		return $result->lastInsertId();
	}

	function insertMultiple($values){
		$ids = array();
		foreach($values as $value){
			$result = $this->query($this->_setupInsertStatement($value));
			if(!$result){
				return false;
			}
			$ids[] =  $result->lastInsertId();
		}
		return $ids;
	}

	protected function _setupInsertStatement(&$values){
		return "INSERT INTO `$this->table_name` ". $this->_setupValueFields($values). "\r\n".
				$this->_setupValuesString($values)."\r\n";
	}

	protected function _setupValueFields(&$values){

		$fields = array();

		foreach($values as $field => $value){
			$fields[] =	'`'.$field.'`';
		}

		return "(".implode(", ", $fields).")";

	}

	protected function _setupValuesString(&$values){

		$field_values = array();

		foreach($values as $value){

			if(is_array($value)){
				trigger_error("Value cant be an array", E_USER_ERROR);
			}

			$value = $this->quote($value);
			$field_values[] = "$value";
		}

		return 'VALUES ('. implode(', ', $field_values). ")";

	}


	function update($values = array()){
		if(!$this->where_fields){
			trigger_error('Cannot update a whole table dynamically', E_USER_ERROR);
			return;
		}
		$result = $this->query($this->_setupUpdateStatement($values));
		return !$result->isError();
	}

	protected function _setupUpdateStatement(&$values){
		return "UPDATE `$this->table_name`\r\n".
				$this->_setupSetString($values)."\r\n".
				$this->_setupWhereString();
	}

	protected function _setupSetString(&$values){

		$set = array();

		foreach($values as $name => $value){
			$value = $this->quote($value);
			$set[] = "\t`$this->table_name`.`$name` = $value";
		}

		return 'SET '. implode(",\r\n", $set);

	}

	function delete(){
		if(!$this->where_fields){
			trigger_error('Cannot delete a whole table dynamically', E_USER_ERROR);
			return;
		}
		$result =  $this->query($this->_setupDeleteStatement());
		return $result && !$result->isError();
	}

	protected function _setupDeleteStatement(){
		return "DELETE FROM `$this->table_name`\r\n".
					$this->_setupWhereString();
	}

	function begin(){
		$result =  $this->query('START TRANSACTION');
		return !$result->isError();
	}

	function commit(){
		$result =  $this->query('COMMIT');
		return !$result->isError();
	}

	function rollback(){
		$result =  $this->query('ROLLBACK');
		return !$result->isError();
	}

	function getSql($type = 'insert', $values = array()){
		$statement = '';
		switch($type){
			case 'insert':
				$statement = $this->_setupInsertStatement($values);
				break;
			case 'update':
				$statement = $this->_setupUpdateStatement($values);
				break;
			case 'insert_multiple':
				$stats = array();
				foreach($values as $value){
					$stats[] = $this->_setupInsertStatement($value);
				}
				$statement = $stats;
				break;
			case 'delete':
				$statement = $this->_setupDeleteStatement();
				break;
		}
		return $statement;
	}

}
