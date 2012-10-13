<?php

/**
 * Base class for all sources of data.
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

abstract class DataSource{

	protected $driver = null;
	private static $drivers = array();

	function __construct($driver_name){
		$this->loadDriver($driver_name);		
	}

	final function loadDriver($driver_name){

		if(is_array($driver_name)){
			$class_name = $driver_name['driver'] . 'Driver';
			$this->driver = new $class_name($driver_name);
			return;
		}

		if(!isset(AppConfig::$database[$driver_name])){
			trigger_error("Database driver '$driver_name' is not defined", E_USER_ERROR);
			return;
		}

		if(isset(self::$drivers[$driver_name])){
			$this->driver = self::$drivers[$driver_name];
			return;
		}

		$config = AppConfig::$database[$driver_name];
		$class_name = $config['driver'] . 'Driver';
		$this->driver = self::$drivers[$driver_name] = new $class_name($config);

	}

	final protected function query($query){

		$args = func_get_args();

		if(!isset($query)){
			trigger_error('No query specified', E_USER_ERROR);
		}

		$params = array();
		if(isset($args[1])){
			$params = is_array($args[1]) ? $args[1] : array_slice($args, 1);
		}

		$result = $this->driver->execute($query, $params);

		if($result === false){
			return null;
		}

		return new QueryResult($result, $this->driver);

	}

	final protected function quote(&$text) {

		if(is_array($text)) {
			$new_text = array();
			foreach($text as $name => $value) {
				$new_text[$name] = $this->quote($value);
			}
			return $new_text;
		}
		else {
			return $this->driver->quote($text);
		}
	}

}

