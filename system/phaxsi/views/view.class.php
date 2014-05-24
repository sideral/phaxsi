<?php

/**
 * Base class for all views.
 * 

 * Copyright 2008-2014, Alejandro Zuleta (http://phplab.co)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2008-2014, Alejandro Zuleta (http://phplab.co)
 * @link          http://phaxsi.net Phaxsi PHP Framework
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @package       Phaxsi.Views
 * @since         Phaxsi v 0.1
 */


abstract class View {

	protected $context;
	protected $load;

	/**
	 * The PhaxsiCache object used for caching this view
	 *
	 * @var PhaxsiCache
	 */
	protected $cache = null;
	protected $buffer_output = false;

	protected $_template_vars = array();

	protected $use_layout = false;
	
	protected $helpers = array();

	function __construct($context){
		$this->context = $context;
		$this->load = new Loader($context);
		$this->helper = new HelperLoader($this->load);
	}

	final function __get($name){
		return $this->$name = $this->load->service($name);
	}

	function setContext($context){
		$this->context = $context;
	}

	abstract public function render();

	function set($name, $value){
		return $this->_template_vars[$name] = $value;
	}

	function get($name){
		if(isset($this->_template_vars[$name]))
			return $this->_template_vars[$name];
		else{
			trigger_error("Variable '$name' is not defined", E_USER_WARNING);
			return null;
		}
	}

	function setArray($array){
		$array = (array)$array;
		foreach($array as $name => $value){
			$this->set($name, $value);
		}
		return $array;
	}

	function delete($name){
		unset($this->_template_vars[$name]);
	}

	function getCache(){
		if(!$this->cache){
			$this->cache = new PhaxsiCache($this->context);
		}
		return $this->cache;
	}

	function setBufferOutput($true_or_false){
		$this->buffer_output = $true_or_false;
	}

	function useLayout(){
		return $this->use_layout;
	}

	function addHelper($name, $helper = ''){
		
		if(!$helper){
			$helper = $name;
		}
		
		$this->helpers[$name] = $this->load->helper($helper);
		
	}

}
