<?php

/**
 * Class for building read queries.
 * 

 * Copyright 2008-2012, Alejandro Zuleta (http://phplab.co)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2008-2012, Alejandro Zuleta (http://phplab.co)
 * @link          http://phaxsi.net Phaxsi PHP Framework
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @package       Phaxsi.Database.QueryBuilder
 * @since         Phaxsi v 0.1
 */

require_once(PHAXSIC_TABLEQUERYBUILDER);

class TableReader extends TableQueryBuilder{

	public $selected_fields = array();
	public $order_by_fields = array();
	public $group_by_fields = array();
	public $limit_option = null;
	protected $from_tables;

	function getSql($type = 'select', $values = array()){
		return $this->_setupSelectStatement();
	}

	function select(){
		$field_names = func_get_args();
		$this->selected_fields = array_merge($this->selected_fields, $field_names);
		return $this;
	}

	function selectFromArray(array $select){
		$this->selected_fields = array_merge($this->selected_fields, $select);
		return $this;
	}

	function orderby($column, $orientation = 'asc'){
		$this->order_by_fields[] = array($column, $orientation);
		return $this;
	}

	function orderFromArray(array $order){
		$this->order_by_fields[] = $order;
		return $this;
	}

	function groupby(){
		$group_criteria = func_get_args();
		$this->group_by_fields = array_merge($this->group_by_fields, $group_criteria);
		return $this;
	}

	function groupFromArray(array $group){
		$this->group_by_fields = array_merge($this->group_by_fields, $group);
		return $this;
	}

	function limit($limit, $offset = 0){
		$this->limit_option = array($limit, $offset);
		return $this;
	}

	function join($table_name, $primary_key, $foreign_key = ''){
		if(!$foreign_key) $foreign_key = $primary_key;
		return new TableJoinReader($this, $table_name, $primary_key, $foreign_key, TableJoinReader::INNER);
	}

	function joinLeft($table_name, $primary_key, $foreign_key = ''){
		if(!$foreign_key) $foreign_key = $primary_key;
		return new TableJoinReader($this, $table_name, $primary_key, $foreign_key, TableJoinReader::LEFT);
	}

	function joinRight($table_name, $primary_key, $foreign_key = ''){
		if(!$foreign_key) $foreign_key = $primary_key;
		return new TableJoinReader($this, $table_name, $primary_key, $foreign_key, TableJoinReader::RIGHT);
	}

	function previous(){
		return trigger_error("There is no previous table", E_USER_ERROR);
	}

	function hasPrevious(){
		return false;
	}

	function from($table_name){
		if($table_name == $this->table_name){
			return $this;
		}
		$table = $this;
		while($table->hasPrevious()){
			$previous = $table->previous();
			if($table_name == $previous->getTableName()){
				return $previous;
			}
			$table = $previous;
		}
		trigger_error("There is no previous table called '$table_name'", E_USER_ERROR);
		return false;
	}

	/*** Select Query builder ***/

	protected function _setupSelectStatement(){
		return $this->_setupSelectString() . "\r\n" .
			   $this->_setupFromString() . "\r\n" .
			   $this->_setupWhereString() . "\r\n".
			   $this->_setupGroupByString() . "\r\n".
			   $this->_setupOrderByString(). "\r\n" .
			   $this->_setupLimitString();
	}

	function _setupSelectString($all = true, $comma = false){

		$select_statement = 'SELECT ' . implode(' ',$this->sql_options) .' ';

		$table_name = $this->table_alias? $this->table_alias : $this->table_name;

		if(!$this->selected_fields){
			if($all)
				return $select_statement. "`$table_name`.*" . ($comma?", ":" ");
			else
				return $select_statement;
		}
		else{
			$select_string = $this->_getSelectString($table_name);
			return $select_statement . $select_string . ($comma?", ":" ");
		}

	}

	protected function _getSelectString($table_name){

		$parts = array();

		foreach ($this->selected_fields as $field){
			if($field == '*'){
				$parts[] = "`$table_name`.*";
			}
			else if(!is_array($field)){
				if($field[0] != '"' && $field[0] != "'"){
					$parts[] = "`$table_name`.$field";
				}
				else{
					$parts[] = "$field";
				}
			}
			else{
				if(!is_array($field[1])){
					if($field[1] != '*' && $field[1][0] != '"' && $field[1][0] != "'"){
						$args = "`$table_name`.{$field[1]}";
					}
					else{
						$args = $field[1];
					}
				}
				else{
					foreach($field[1] as &$fld){
						if($fld != '*' && $fld[0] != '"' && $fld[0] != "'"){
							$fld = "`$table_name`.$fld";
						}
					}

					$args = implode(',', $field[1]);
				}

				$parts[] = $field[0] . '(' . $args . ')' . (isset($field[2])? " AS {$field[2]}": "");
			}
		}

		return implode(", ", $parts);

	}

	function _setupFromString(){
		return "FROM `$this->table_name` ".($this->table_alias?"AS $this->table_alias":"");
	}

	function _setupOrderByString($comma = false){

		$table_name = $this->table_alias? $this->table_alias : $this->table_name;

		$parts = array();

		foreach($this->order_by_fields as $order_by){
			$parts[] = "`$table_name`.`{$order_by[0]}` {$order_by[1]}";
		}
		if($parts)
			return "ORDER BY " . implode(", ", $parts) . ($comma? ", " : "");
		else{
			return $comma ? "ORDER BY " : "";
		}

	}

	function _setupGroupByString($comma = false){

		$table_name = $this->table_alias? $this->table_alias : $this->table_name;

		$parts = array();

		foreach($this->group_by_fields as $group_by){
			$parts[] = "`$table_name`.`$group_by` ";
		}
		if($parts)
			return "GROUP BY " . implode(", ", $parts) . ($comma? ", " : "");
		else{
			return $comma ? "GROUP BY " : "";
		}

	}

	function _setupLimitString(){
		if(is_null($this->limit_option))
			return "";
		return " LIMIT {$this->limit_option[1]}, {$this->limit_option[0]} ";
	}

	final function __call($procedure, $arguments){

		$result = $this->query($this->_setupSelectStatement());
		if($result){
			return call_user_func_array(array(&$result, $procedure), $arguments);
		}
		return false;
	}

}
