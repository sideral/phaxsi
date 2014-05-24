<?php

/**
 * The base class for Blocks.
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

abstract class Block extends AbstractController{

	protected $parent_context = null;
	protected $allow_direct_access = false;
	protected $helper = null;

	function __construct(Context $context, $parent_context = null){
		$this->parent_context = $parent_context;
		$view = new HtmlView($context);
		$view->setBufferOutput(true);
		parent::__construct($context, $view);
		$this->helper = new HelperLoader($this->load);
		$this->_execute();
	}

	final public function isDirectAccessAllowed(){
		return $this->allow_direct_access;
	}

	final public function setArg($name, $value, $put_in_view = true){
		$this->args[$name] = $value;
		if($put_in_view){
			$this->view->set($name, $value);
		}
	}

	final public function getArg($name){
		return $this->args[$name];
	}

}
