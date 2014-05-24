<?php

/**
 * The base class for Shell controllers. 
 * 

 * Copyright 2008-2012, Alejandro Zuleta (http://phplab.co)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2008-2012, Alejandro Zuleta (http://phplab.co)
 * @link          http://phaxsi.net Phaxsi PHP Framework
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @package       Phaxsi.Controller
 * @since         Phaxsi v 0.1
 */


abstract class Shell extends AbstractController{

	function __construct(Context $context){

		$this->load = new Loader($context);
		$this->db = new DatabaseProxy($this->load);

		$this->context  = $context;
		$this->view = new ShellView($context);
		$this->args = array_merge((array)$this->args, (array)$context->getArguments());

	}

	function _execute(){
		$this->_create();
		print $this;
	}

	protected function _create(){

		$action_ptr = array(&$this, $this->context->getAction());
		if(is_callable($action_ptr)){
			call_user_func($action_ptr);
		}
		else{
			trigger_error("Controller '".$this->context->getPath()."' of type '".$this->context->getType()."' does not exist.", E_USER_WARNING);
		}

	}

	protected function _render(){
		return $this->view->render();
	}

}