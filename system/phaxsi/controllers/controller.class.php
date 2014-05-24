<?php

/**
 * The base class for page controllers. 
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

require_once(PHAXSIC_HELPERLOADER);

abstract class Controller extends AbstractController{

	protected $layout = null;
	protected $db, $helper;
	protected $_headers = array();

	/**
	 * Initializes the Controller instance and selects the appropiate View
	 * @param Context $context
	 */
	function __construct(Context $context){
		$type = $context->getViewType();
		$class_name = $type.'view';
		$view = new $class_name($context);
		parent::__construct($context, $view);
		$this->db = new DatabaseProxy($this->load);
		$this->helper = new HelperLoader($this->load);
	}

	/**
	 * Overrides AbstractController _setup to add a layout
	 */
	final protected function _setup(){
		/**
		 * Checks if the View uses a layout and load the default
		 */
		if($this->view->useLayout()){
			$default_layout = '/'.DEFAULT_MODULE.'/'.DEFAULT_MODULE;
			$this->layout = $this->load->layout($default_layout);
		}
		parent::_setup();
	}

	final public function _execute($display = true){
		parent::_execute();
		if($display){
			$this->_sendHeaders();
			$this->_display();
		}
		return $this->view;
	}
	
	final protected function _call($path, $args = array()){
		$parts = explode('/',$path);
		if($parts[0] == ''){
			array_shift($parts);
		}
		$router = new Router();
		$controller = $router->getController(new Context('controller', $parts[0], $parts[1], $args));
		if($controller){
			return $controller->_execute(false);
		}
		return false;
	}

	/**
	 * This is called after finishing all processing in order to return
	 * the output to the browser
	 */
	final protected function _display(){

		/**
		 * If there is a layout available, it will call _render(),
		 * so we only need to call display on the layout and
		 * print its output.
		 */
		if($this->layout && $this->view->useLayout()){
			echo $this->layout->_display($this);
		}
		else{
			echo parent::_render();
		}

	}

	/**
	 * Sends http cache headers before any output is sent
	 */
	final protected function _sendHeaders(){
		/**
		 * A better management for client side cache would be good.
		 */
		 $file = ''; $line = '';

		if(!headers_sent($file, $line)){
			header('Pragma: no-cache');
			header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
			header('Expires: Wed, 1 Dec 1982 06:52:00 GMT');
		}
		else{
			trigger_error("No headers were sent because output started at '$file' on line $line", E_USER_WARNING);
		}

	}

	/**
	 * This is a special action available to all Pages. It automatically
	 * returns the html of the requested block for viewing in the page.
	 * Only blocks that explicity allow this action will be returned.
	 */
	final function getBlock_json(){
		//Check?
		if(isset($this->args[0])){
			$block = $this->load->block($this->args[0], $_POST);
			if($block && $block->isDirectAccessAllowed()){
				$this->view->set('inner_html', $block);
			}
			else{
				$this->view->set('inner_html', '');
			}
		}
	}
}
