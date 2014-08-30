<?php

/**
 * InputMultipleDropDown
 * 

 * Copyright 2008-2014, Alejandro Zuleta (http://phplab.co)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2008-2014, Alejandro Zuleta (http://phplab.co)
 * @link          http://phaxsi.net Phaxsi PHP Framework
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @package       Phaxsi.Forms.Components
 * @since         Phaxsi v 0.1
 */

require_once('inputdropdown.class.php');

class InputMultipleDropDown extends InputDropDown{
	
	protected $_scalar = false;	
	protected $_value = array();
	
	function __construct($initial_value = array(), $name = null){	
		parent::__construct($initial_value, $name);
		$this->setAttribute('multiple', 'true');
		$this->setAttribute('size', 4);
	}
	
	function setRawValue($value){
		if(!is_null($value)){
			$this->_value = (array)$value;
		}
	}
	
	function getValue($filtered = true){

		$value = $this->_value;
		
		if($this->_filter && $filtered){
			foreach($value as &$val){
				$val = call_user_func($this->_filter, $val, $this->getName());
			}
		}	

		return $value;

	}
	
	protected function createHtmlOptions($items){
		$options = array();
		foreach($items as $value => $text){
			if(!in_array($value, $this->_value))
				$options[] = "<option value=\"$value\">".HtmlHelper::escape($text)."</option>";
			else
				$options[] = "<option value=\"$value\" selected='selected'>".HtmlHelper::escape($text)."</option>";
		}
		return $options;		
	}
	
	function validate(){

		$valid = true;
		if(!is_null($this->_validator)){
			$valid = $this->_validator->validateArray($this->getRawValue());
		}

		return $valid;

	}
	
	function __toString(){
		$this->setName($this->getName(), $this->getName().'[]');
		return parent::__toString();
	}
	
}
