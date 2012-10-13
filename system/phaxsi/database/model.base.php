<?php

/**
 * Base class for models.
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
 * @package       Phaxsi.Database
 * @since         Phaxsi v 0.1
 */

abstract class Model extends DataSource{

	protected $load, $db, $session;
	protected $driver_name = 'default';
	protected $table_name, $id_column;
	protected $associations = array(
		'has_many'	=> array(),
		'has_one'	=> array(),
		'belongs_to'=> array()
	);

	final function __construct(Context $context){
		parent::__construct($this->driver_name);
		$this->load = new Loader($context);
		$this->db = new DatabaseProxy($this->load);
		$this->session = new Session($context->getModule());
		$this->plugin = $this->load->service('plugin');	
		$this->table_name = strtolower($context->getAction());
		$this->id_column = $this->table_name.'_id';
	}
	
	final function find($type, array $conditions = array()){
		
		if($type != 'first' && $type != 'all'){
			trigger_error('Invalid find type', E_USER_ERROR);
		}
		
		$query = $this->db->from($this->table_name);
		$query->where($conditions);
		
		$rows = $type == 'first'? $query->fetchRow() : $query->fetchAllRows();
		if(!$rows){
			return $rows;
		}
		
		if($type == 'first'){
			$rows = array($rows);
		}
		
		$results = array();
		
		foreach($rows as $row){
			$result = array($this->table_name => $row);
			if(isset($this->associations['has_many'])){
				foreach($this->associations['has_many'] as $table){
					//Not general enough
					$result[$table] = $this->db->from($table)->where($this->id_column, $row[$this->id_column])->fetchAllRows();
				}
			}
			if(isset($this->associations['has_one'])){
				foreach($this->associations['has_one'] as $table){
					//Not general enough
					$result[$table] = $this->db->from($table)->where($this->id_column, $row[$this->id_column])->fetchRow();
				}
			}
			if(isset($this->associations['belogns_to'])){
				foreach($this->associations['belogns_to'] as $table){
					//Not general enough
					$result[$table] = $this->db->from($table)->where($this->id_column, $row[$this->id_column])->fetchRow();
				}
			}
			$results[] = $result;
		}
		
		if($type == 'first'){
			return $results[0];
		}
		
		return $results;
		
	}

}
