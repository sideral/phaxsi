<?php

/**
 * DatabaseProxy class works as an entry point for the application to the two data models of Phaxsi.
 * 

 * Copyright 2008-2012, Alejandro Zuleta (http://phplab.co)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2008-2012, Alejandro Zuleta (http://phplab.co)
 * @link          http://phaxsi.net Phaxsi PHP Framework
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @package       Phaxsi.Database
 * @since         Phaxsi v 0.1
 */

final class DatabaseProxy{

	private $load, $models = array();

	function __construct($loader){
		$this->load = $loader;
	}

	function from($table, $driver = 'default'){
		return new TableReader($table, $driver);
	}

	function into($table, $driver = 'default'){
		return new TableWriter($table, $driver);
	}

	function model($name){
		$lower_name = strtolower($name);
		if(isset($this->models[$lower_name])){
			return $this->models[$lower_name];
		}
		return $this->models[$name] = $this->load->model($lower_name);
	}

	function __get($name){
		if(isset($this->models[$name])){
			return $this->models[$name];
		}
		return $this->model($name);
	}

}