<?php

/**
 * Allows to move the 'cursor' to the parent table in a built query.
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


class TableParentReader extends TableJoinReader{

	protected $previous;

	function __construct(TableQueryBuilder $base, TableQueryBuilder $previous){
		$this->base = $base;
		$this->driver = $this->base->driver;
		$this->previous = $previous;
		$this->table_name = $this->previous->getTableName(true);
		$this->table_alias = $this->previous->getTableAlias();
	}

	function previous(){
		if(!$this->hasPrevious()){
			return trigger_error("There is no previous table", E_USER_ERROR);
		}
		return new TableParentReader($this, $this->previous->base);
	}

	function hasPrevious(){
		return isset($this->previous->base);
	}

	function select(){
		$field_names = func_get_args();
		$this->previous->selectFromArray($field_names);
		return $this;
	}

	function selectFromArray(array $select){
		$this->previous->selectFromArray($select);
		return $this;
	}

	function order(){
		$order_criteria = func_get_args();
		$this->previous->orderFromArray($order_criteria);
		return $this;
	}

	function orderby($column, $orientation = 'asc'){
		$this->previous->orderby($column, $orientation);
		return $this;
	}

	function orderFromArray(array $order){
		$this->previous->orderFromArray($order);
		return $this;
	}

	function group(){
		$group_criteria = func_get_args();
		$this->previous->groupFromArray($group_criteria);
		return $this;
	}

	function groupFromArray(array $group){
		$this->previous->groupFromArray($group);
		return $this;
	}

	function limit($limit, $offset = 0){
		$this->previous->limit($limit, $offset);
		return $this;
	}

	function option($option){
		$this->previous->option($option);
		return $this;
	}

	function _setupSelectString($all = true, $comma = false){
		return $this->base->_setupSelectString($all, $comma);
	}

	function _setupFromString(){
		return $this->base->_setupFromString();
	}

	function _setupOrderByString($comma = false){
		return $this->base->_setupOrderByString($comma);
	}

	function _setupGroupByString($comma = false){
		return $this->base->_setupGroupByString($comma);
	}

	function _setupLimitString(){
		return $this->base->_setupLimitString();
	}

}
