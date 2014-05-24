<?php

/**
 * The base class for all controllers. 
 * 

 * Copyright 2008-2014, Alejandro Zuleta (http://phplab.co)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2008-2014, Alejandro Zuleta (http://phplab.co)
 * @link          http://phaxsi.net Phaxsi PHP Framework
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @package       Phaxsi.Controller
 * @since         Phaxsi v 0.1
 */

require_once(PHAXSIC_VIEW);

abstract class AbstractController{

	protected $load;

	protected $args;
	protected $view;
	protected $context;
	protected $config;

	/**
	 * Initializes controller
	 * @param Context $context
	 * @param View $view
	 */
	function __construct(Context $context, View $view){

		$this->load = new Loader($context);

		$this->context  = $context;
		$this->view = $view;
		$this->args = array_merge((array)$this->args, (array)$context->getArguments());

		$base_module = $context->getBaseModuleName();
		$this->config = isset(AppConfig::$modules[$base_module]) ? AppConfig::$modules[$base_module] : array();

	}

	final function __get($name){
		return $this->$name = $this->load->service($name);
	}

	protected function _execute(){
		$this->_setup();
		$this->_create();
	}

	protected function _setup(){
		$global_action_ptr = array(&$this, $this->context->getModule().'_setup');
		if(is_callable($global_action_ptr)){
			call_user_func($global_action_ptr);
		}
	}

	protected function _create(){
		$manager = PluginManager::getInstance();
		$manager->controllerStart($this->context);

		$action_ptr = array(&$this, $this->context->getAction());
		if(is_callable($action_ptr)){
			call_user_func($action_ptr);
		}
		else{
			trigger_error("Controller '".$this->context->getPath()."' of type '".$this->context->getType()."' does not exist.", E_USER_WARNING);
		}

		$manager->controllerEnd($this->context);

	}

	public function __toString(){
		return $this->_render();
	}

	protected function _render(){
		$manager  = PluginManager::getInstance();
		$manager->renderStart($this->context);
		$html = $this->view->render();
		$manager->renderEnd($this->context);
		return $html;
	}

	/**
	 *	Returns the Context of the current Controller
	 * @return Context
	 */
	final public function _getContext(){
		return $this->context;
	}

}
