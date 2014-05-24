<?php

/**
 * Default Router.
 * 

 * Copyright 2008-2014, Alejandro Zuleta (http://phplab.co)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2008-2014, Alejandro Zuleta (http://phplab.co)
 * @link          http://phaxsi.net Phaxsi PHP Framework
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @package       Phaxsi.Plugins
 * @since         Phaxsi v 0.1
 */

class Router{

	/**
	 * Gets the Controller object related with the given Context.
	 * If no Controller is found, returns the default 404 Controller
	 * @param Context $context
	 * @return Controller
	 */
	public function getController($context){

		if(!$context)
			return false;

		$controller = $this->loadController($context);

		if(!$controller){
			$error_context = $this->getErrorContext();
			if($error_context != $context){
				return $this->getController($error_context);
			}
		}

		return $controller;

	}

	protected function getErrorContext(){
		return false;
	}

	/**
	 * Creates an instance of the Controller specified in the Context,
	 * and verifies that the requested action exists, returning
	 * the created instance if true, or false if not
	 * @param Context $context
	 * @return Controller
	 */
	final protected function loadController(Context $context){

		$page_name = Loader::includeApplicationClass($context);

		#Search the requested action, only return the page object
		#if the action exists
		if($page_name){
			$page = new $page_name($context);
			$action_method = array(&$page, $context->getAction());
			return is_callable($action_method) ? $page : false;
		}

		return false;

	}

}
