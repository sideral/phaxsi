<?php

class PhaxsiModel extends Model{

	function execute(){
		$args = func_get_args();
		return call_user_func_array(array(&$this, 'query'), $args);
	}

	function getDriver(){
		return $this->driver;
	}

}
