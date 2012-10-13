<?php

class LogUtility extends Utility{
	
	private $drivers = array();
	
	function __construct($context){
		parent::__construct($context);
		
		$this->drivers = array(
			'generic_log' => array(
				'driver' => 'sqlite',
				'name' => APPD_LOG . DS . 'generic.log.sqlite'
			)
		);
		
		AppConfig::$database['generic_log'] = $this->drivers['generic_log'];
		
	}
	
	function loadLog($name, $create = false){
		
		if(!isset($this->drivers[$name.'_log'])){
			if(!$create  && !file_exists(APPD_LOG . DS . $name.'.log.sqlite')){
				trigger_error("Log '$name' does not exist.", E_USER_ERROR);
			}
			
			$this->drivers[$name.'_log'] = array(
				'driver' => 'sqlite',
				'name' => APPD_LOG . DS . $name.'.log.sqlite'
			);
		}
		
		$this->db->Log->loadDriver($this->drivers[$name.'_log']);
		
	}

	function addLine($message){
		
		if(!$this->drivers){
			trigger_error('A log driver has to be loaded before using this utility.', E_USER_ERROR);
		}

		$this->db->Log->addLine($message);
	}

	function getAllLines(){

		if(!$this->drivers){
			trigger_error('A log driver has to be loaded before using this utility.', E_USER_ERROR);
		}
		
		return $this->db->Log->getAllLines();
	}

}
