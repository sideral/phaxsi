<?php

/**
 * Aux class for doing joins.
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
 * @package       Phaxsi.Database.QueryBuilder
 * @since         Phaxsi v 0.1
 */


class TableJoinReader extends TableReader{

	const INNER = "INNER";
	const LEFT = "LEFT";
	const RIGHT = "RIGHT";

	protected $base;
	protected $join_type;
	protected $self_key;
	protected $base_key;

	function __construct(TableQueryBuilder $entity, $table_name, $self_key, $base_key, $join_type){
		$this->base = $entity;
		$this->table_name = is_array($table_name) ? $table_name[0] : $table_name;
		$this->join_type = $join_type;
		$this->self_key = $self_key;
		$this->base_key = $base_key;
		$this->table_alias = is_array($table_name) ? $table_name[1] : '';
		$this->driver = $this->base->driver;
	}

	function previous(){
		return new TableParentReader($this, $this->base);
	}

	function hasPrevious(){
		return true;
	}

	function limit($limit, $offset = 0){
		$this->base->limit($limit, $offset);
		return $this;
	}

	function option($option){
		$this->base->option($option);
		return $this;
	}

	function _setupSelectString($all = true, $comma = false){

		$table_name = $this->table_alias? $this->table_alias : $this->table_name;

		if(!$this->selected_fields){
			if($all)
				return $this->base->_setupSelectString(true, true) . "`$table_name`.*". ($comma?", ":" ");
			else 
				return $this->base->_setupSelectString(false, true);
		}
		else{
			$select_string = $this->_getSelectString($table_name);
			return $this->base->_setupSelectString(false, true) . "$select_string". ($comma?", ":" ");
		}

	}

	function _setupFromString(){
		$base_table = $this->base->getTableName();
		$base_string = $this->base->_setupFromString();
		$table_name = $this->table_alias ? $this->table_alias : $this->table_name;
		return "$base_string\r\n$this->join_type JOIN `$this->table_name` ". ($this->table_alias? "AS $this->table_alias ": "") ."ON `$base_table`.`$this->base_key` = `$table_name`.`$this->self_key`";
	}

	function _setupWhereString($and = false){

		if ($this->where_fields){
			$conditions = $this->_getWhereConditions();
			return $this->base->_setupWhereString(true) . implode("\t\r\nAND ", $conditions) . ($and? "\r\n\tAND ": "");
		}
		else{
			return $this->base->_setupWhereString($and);
		}
	}

	function _setupOrderByString($comma = false){

		$parts = array();
		foreach($this->order_by_fields as $order_by){
			$parts[] = "$this->table_name.{$order_by[0]} {$order_by[1]}";
		}
		if($parts)
			return $this->base->_setupOrderByString(true) . implode(", ", $parts);
		else{
			return $this->base->_setupOrderByString($comma);
		}

	}

	function _setupGroupByString($comma = false){

		$parts = array();
		foreach($this->group_by_fields as $group_by){
			$parts[] = "$this->table_name.$group_by ";
		}
		if($parts)
			return $this->base->_setupGroupByString(true) . implode(", ", $parts);
		else{
			return $this->base->_setupGroupByString($comma);
		}

	}

	function _setupLimitString(){
		return $this->base->_setupLimitString();
	}


}