<?php

/**
 * Base class for building queries.
 * 

 * Copyright 2008-2013, Alejandro Zuleta (http://phplab.co)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2008-2013, Alejandro Zuleta (http://phplab.co)
 * @link          http://phaxsi.net Phaxsi PHP Framework
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @package       Phaxsi.Database.QueryBuilder
 * @since         Phaxsi v 0.1
 */


abstract class TableQueryBuilder extends DataSource{


	protected $table_name;
	protected $table_alias;

	protected $store = null;

	public $where_fields = array();
	public $sql_options = array();

	function __construct($table_name, $driver = 'default'){
		parent::__construct($driver);
		$this->table_name = is_array($table_name) ? $table_name[0] : $table_name;
		$this->table_alias = is_array($table_name) ? $table_name[1] : '';
	}

	/** Accessors **/

	final function getTableName($force_real_name = false){
		return ($this->table_alias && !$force_real_name)? $this->table_alias : $this->table_name;
	}

	final function getTableAlias(){
		return $this->table_alias;
	}

	abstract function getSql($type = 'select', $values=array());

	/***** Table Data ***/

	function option($option){
		$this->sql_options[] = $option;
		return $this;
	}

	function where($field_name, $value = null, $condition = "="){
		if(is_array($field_name)){
			foreach($field_name as $field => $value){
				$this->where_fields[] = array($field, $condition, $value);
			}
		}
		else{
			$this->where_fields[] = array($field_name, $condition, $value);
		}	
		return $this;
	}

	function _setupWhereString($and = false){

		if ($this->where_fields){
			$conditions = $this->_getWhereConditions();
			return "WHERE " . implode("\r\n\tAND ", $conditions) . ($and? "\r\n\tAND " : " ");
		}
		else{
			return ($and? "WHERE " : " ");
		}

	}

	final protected function _getWhereConditions(){
		$conditions = array();
		$table_name = $this->table_alias? $this->table_alias : $this->table_name;

		foreach($this->where_fields as $condition){

			if(!is_array($condition[0])){
				$row_name = "`$table_name`.`{$condition[0]}`";
			}
			else{
				if(!is_array($condition[0][1])){
					$args = "`$table_name`.`{$condition[0][1]}`";
				}
				else{
					foreach($condition[0][1] as &$field_condition){
						$field_condition = "`$table_name`.`$field_condition`";
					}
					$args = implode(',', $condition[0][1]);
				}
				
				$row_name = $condition[0][0] . '(' . $args . ')';
			}

			if(is_null($condition[2])){
				$conditions[] = "$row_name {$condition[1]} NULL";
			}
			else if(!is_array($condition[2])){
				$value = $this->quote($condition[2]);
				if(strtoupper($condition[1]) == 'AGAINST'){
					$value = "($value)";
				}
				$conditions[] = "$row_name {$condition[1]} $value";
			}
			else if(strtoupper($condition[1]) == 'IN'){
				$in_values = array();
				foreach($condition[2] as $value){
					$value = $this->quote($value);
					$in_values[] = "$value";
				}
				$in_string = '('. implode(', ', $in_values). ")";
				$conditions[] = "$row_name IN $in_string";
			}
			else if(isset($condition[2][1])){
				$conditions[] = "$row_name {$condition[1]} {$condition[2][0]}({$condition[2][1]})";
			}
		}
		return $conditions;
	}
	
	function whereMatch($fields, $value = null){
		if(!is_array($fields)){
			$fields = array($fields);
		}
		$this->where_fields[] = array(array('MATCH',$fields), 'AGAINST', $value);
		return $this;
	}

}
