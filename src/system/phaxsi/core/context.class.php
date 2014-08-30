<?php

/**
 * This class is the 'glue' of Phaxsi. Gives coherence to modules by providing a module 
 * context to all classes that use it.
 * 
 * Copyright 2008-2014, Alejandro Zuleta (http://phplab.co)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2008-2014, Alejandro Zuleta (http://phplab.co)
 * @link          http://phaxsi.net Phaxsi PHP Framework
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @package       Phaxsi.Core
 * @since         Phaxsi v 0.1
 */

class Context{

	private $type;
	private $module, $complete_module;
	private $action;
	private $arguments;
	private $use_module_name;

	function __construct($type, $module = null, $action = null, $arguments = array(), $use_module_name = true){

		$this->type = $type;
		$this->complete_module = $module ? $module : DEFAULT_MODULE;
		$parts = explode('/',$this->complete_module);
		$this->module = array_shift($parts);
		/**
		 * If no action is provided, select the action with the same name as
		 * the module by default.
		 */
		$this->action = $action ? $action : $this->module;
		$this->arguments = (array)$arguments;
		$this->use_module_name = $use_module_name;

	}

	function getType(){
		return $this->type;
	}

	function deriveContext($type, $path = "", $use_module_name = true){

		//Should this be here?
		if(empty($path)){
			return new Context($type, $this->complete_module, $this->action, $this->arguments);
		}

		$parts = explode("/", strtolower($path));
		$count = count($parts);

		if($parts[0] == ''){
			$module = implode('/',array_slice($parts, 1, $count-2));
			$action = isset($parts[$count-1]) ? $parts[$count-1] : $parts[1];
		}
		else{
			$subdir = implode('/',array_slice($parts, 0, $count-1));
			$module = $this->module . ($subdir? '/'.$subdir : '');
			$action = $parts[$count-1];
		}

		return new Context($type, $module, $action, array(), $use_module_name);

	}

	function getModule($complete = true){
		if($complete){
			return $this->complete_module;
		}
		return $this->module;			
	}

	function getAction(){
		return $this->action;
	}

	function getViewType(){
		/**
		 * Select a View for the action based on its name.
		 * If the action name ends with _json, _process, etc,
		 * it means that a view other than HtmlView will be
		 * used by the controller.
		 */
		$parts = explode('_', $this->action);
		$count = count($parts);
		if($count==1)
			return 'html';

		$views = array('process','json','feed','mail', 'xml', 'file');
		if(in_array($parts[$count-1], $views)){
			return $parts[$count-1];
		}

		return 'html';

	}

	function getPath(){
		return '/'.$this->module.'/'.$this->action;
	}

	function getBaseModuleName(){
		return $this->use_module_name? $this->module : $this->action;
	}

	function getArguments(){
		return $this->arguments;
	}

	function setArguments($args){
		$this->arguments = (array)$args;
	}

	function addArgument($name, $value){
		$this->arguments[$name] = $value;
	}


}
