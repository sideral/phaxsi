<?php

/**
 * View for controller that process things and redirect.
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

class ProcessView extends View {

	protected $next;
	protected $flash;

	function __construct($context){
		$referer = UrlHelper::referer();
		$this->next = $referer? $referer : $context->getModule();
		parent::__construct($context);
	}

	public function render(){
		if(!$this->flash){
			RedirectHelper::to($this->next);
		}
		else{
			RedirectHelper::flash($this->next, $this->flash['key'], $this->flash['value']);
		}
	}

	function getCache(){
		trigger_error('Cache is disabled on views of type process.', E_USER_ERROR);
	}

	function setRedirect($path, $flash_key = '', $flash_value = null){
		if($path && $path[0] != '/' && !preg_match("/^http:/", $path)){
			$path = '/'.$this->context->getModule().'/'.$path;
		}
		$this->next = $path;

		if(!empty($flash_key) && !is_null($flash_value)){
			$this->flash = array('key' => $flash_key, 'value' => $flash_value);
		}

	}

}
