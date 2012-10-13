<?php

/**
 * The base class for all form elements.
 * 
 * Phaxsi PHP Framework (http://phaxsi.net)
 * Copyright 2008-2012, Alejandro Zuleta (http://slopeone.net)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2008-2012, Alejandro Zuleta (http://slopeone.net)
 * @link          http://phaxsi.net Phaxsi PHP Framework
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @package       Phaxsi.Forms.Base
 * @since         Phaxsi v 0.1
 */


require_once(PHAXSIC_HTMLELEMENT);

abstract class FormElement extends HtmlElement implements IFormComponent {

	protected $_name;
	protected $_html_name;
	protected $_value;
	protected $_initial_value;
	protected $_label;

	protected $_filter;
	protected $_target;

	protected $_data;
	protected $_scalar = true;

	protected $_validator;
	protected $_error_code;

	protected $enabled = true;

	function __construct($tag_name, $initial_value, $name, $can_have_children = false){
		parent::__construct($tag_name, null, $can_have_children);
		$this->_name = $name;
		if(!is_null($initial_value)){
			//The order of these two operations is significant
			$this->_initial_value = $initial_value;
			$this->setValue($initial_value);				
		}
	}

	function setRawValue($value){
		if(!is_null($value) && !is_array($value)){
			$this->_value = $value;
		}
	}

	function getRawValue(){
		return $this->_value;
	}

	function getValue($filtered = true){

		$value = $this->_value;

		if($this->_filter && $filtered){
			$value = call_user_func($this->_filter, $value, $this->getName());
		}

		return $value;

	}

	function setValue($value){
		$this->setRawValue($value);
	}

	function resetValue(){
		$this->_value = $this->_initial_value;			
	}

	function getDefaultValue(){
		return $this->_initial_value;
	}
	
	function returnsValue(){
		return true;
	}

	function getName(){    
		return $this->_name;				
	}

	function setName($name, $html_name = null){
		$this->_name = $name;
		$this->_html_name = is_null($html_name)? $name : $html_name;
	}

	function setLabel($label_text, $attributes = array()){
		$label = new HtmlElement('label', null, true);
		$label->setAttribute('for', $this->getId());
		$label->setAttribute('class', 'phaxsi_label');
		$label->setAttributes($attributes);
		$label->innerHTML = $label_text;
		$this->_label = $label;
	}

	function getLabel($label_text = '', $attributes = array()){
		if(!$this->_label || $label_text){
			$this->setLabel($label_text);
		}

		if($this->_label){
			$this->_label->setAttributes($attributes);
		}

		return $this->_label;
	}

	function setData($name, $value){
		if(!$this->_data) $this->_data = array();
		$this->_data[$name] = $value;
	}

	function getData($name){
		return isset($this->_data[$name])? $this->_data[$name] : null;
	}

	function validate(){
		$valid = true;
		if(!is_null($this->_validator)){
			$valid = $this->_validator->validate($this->getRawValue());
		}
		return $valid;
	}

	function getValidator(){
		return $this->_validator;			
	}

	function setValidator($options, $messages = array()){
		if(!is_array($options)){
			trigger_error('Validator must be an array', E_USER_ERROR);
		}
		
		if(!$this->_validator){
			$this->_validator = new Validator($options, $messages);
			if($this->_error_code){
				$this->_validator->setErrorCode($this->_error_code);
			}
		}
		else{
			foreach($options as $option =>$value){
				$this->_validator->setOption($option, $value);
			}
			foreach($messages as $option =>$message){
				$this->_validator->setMessage($option, $message);
			}
		}
	}

	function getErrorMessage(){
		if($this->_validator){
			return $this->_validator->getErrorMessage();
		}
		return "";
	}

	function setErrorCode($error_code){
		if(!$this->_validator){
			$this->_error_code = $error_code;		
		}
		else{
			$this->_validator->setErrorCode($error_code);
		}
	}

	function getErrorCode(){
		if(!$this->_validator){
			return $this->_error_code;		
		}
		else{
			return $this->_validator->getErrorCode();
		}
	}

	final function hasError(){
		return $this->getErrorCode() != '';
	}

	function setFilter($filter){
		$this->_filter = $filter;		
	}

	function getFilter(){		
		return $this->_filter;		
	}

	function setTarget($table, $column = null){
		if($column !== null)
			$this->_target = array($table, $column);
		else 
			$this->_target = array($table, $this->_name);
	}

	function getTarget(){
		return $this->_target;
	}

	function __toString(){
		
		$this->setAttribute("name", $this->_html_name);

		if(!$this->enabled)
			$this->setAttribute("disabled", 'disabled');

		$this->afterHTML .= $this->getClientValidationHtml();

		return parent::__toString();

	}

	function getClientValidationHtml(){
		if($this->_validator && $this->_validator->getOption('client_side_validable')){
			return  HtmlHelper::inlineJavascript(
				"Phaxsi.Validator.Current.addValidator('$this->_html_name',".
					JsonHelper::encode($this->_validator->getClientOptions()).",".
					JsonHelper::encode($this->_validator->getClientErrorMessages()).",".
					JsonHelper::encode($this->getClientValidationConfig()).");");
		}
		return '';
	}

	function disable(){
		$this->enabled = false;
	}

	function enable(){
		$this->enabled = true;
	}

	protected function getClientValidationConfig(){
		return array();
	}

	function isFileUpload(){
		return false;
	}
	
	function isScalar(){
		return $this->_scalar;
	}

}
