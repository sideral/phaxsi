<?php

/**
 * The base class for all form element lists.
 * 

 * Copyright 2008-2014, Alejandro Zuleta (http://phplab.co)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2008-2014, Alejandro Zuleta (http://phplab.co)
 * @link          http://phaxsi.net Phaxsi PHP Framework
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @package       Phaxsi.Forms.Base
 * @since         Phaxsi v 0.1
 */

require_once(PHAXSIC_HTMLELEMENTLIST);
require_once(PHAXSIC_FORMELEMENT);
require_once(PHAXSIC_FORMINPUT);
require_once(PHAXSIC_VALIDATOR);

/**
 * Base class for lists of form elements. Inherits from
 * HtmlElementList, giving the class array-like behavior.
 * 
 * @abstract 
 */

abstract class FormElementList extends HtmlElementList implements IFormComponent {

	static $current_element_id = 0;

	/**
	 * Name of the collection
	 * 
	 * @var string
	 */
	protected $_name;
	/**
	 * Array of values
	 *
	 * @var array
	 */
	protected $_raw_value;
	protected $_errors;

	protected $_validator;
	protected $_error_code;

	protected $_filter;
	protected $_save_as;

	protected $_data;
	protected $_scalar = false;

	protected $_error_summary;

	protected $enabled = true;

	function __construct($initial_value = null){
		$this->_raw_value = $initial_value;
	}

	/**
	 * Adds a new component to the element list
	 *
	 * @param string $component_name
	 * @param IFormComponent $component
	 * @return IFormComponent
	 */
	final function set( $component, $component_name, $html_name = null){

		if(isset($this->_elements[$component_name])){
			trigger_error("There is already a form element with the name '$component_name'", E_USER_WARNING);
		}

		$component->setName($component_name, $html_name);

		if(isset($this->_raw_value[$component_name])){ // Value was sent
			$component->setRawValue($this->_raw_value[$component_name]);
		}
		else if($this->_raw_value){ // Put null value if sent but not present
			$component->setRawValue(null);
		}

		if(isset($this->_errors[$component_name])){
			$component->setErrorCode($this->_errors[$component_name]);
		}

		$this->_elements[$component_name] = $component;

		return $component;

	}

	/**
	 * Sets the values of each element, leaves the element
	 * untouched if no value is passed. Receives 
	 *
	 * @param array $values An associative array of values forming name/value pairs.
	 */
	function setValue($values){
		$this->setRawValue($values);
	}

	/**
	 * Returns an associative array with the name/value pairs
	 * of the elements in the collection
	 *
	 * @return array An associative array with the name/value pairs of the elements in the collection
	 */

	function getValue($filtered = true){

		$values = array();

		foreach($this->_elements as $name => $element){
			if($element->returnsValue()){
				$values[$name] = $element->getValue($filtered);
			}
		}

		return $values;

	}

	function getDefaultValue(){
		$values = array();

		foreach($this->_elements as $name => $element){
			$values[$name] = $element->getDefaultValue();
		}

		return $values;	
	}

	function getValues(){
		return $this->getValue();
	}

	function getRawValue(){
		$values = array();
		foreach($this->_elements as $name => $element){
			$value = $element->getRawValue();
			if(!is_null($value)){
				$values[$name] = $value;
			}				
		}
		return $values;
	}

	function setRawValue($values){
		if(is_array($values)){
			$this->_raw_value = $values;
		}			
	}

	function returnsValue(){
		return true;
	}

	/**
	 * Sets the value of every element in the collection
	 * to their default value
	 *
	 */

	function resetValue(){
		foreach($this->_elements as $element){
			$element->resetValue();
		}
	}

	/**
	 * IFormComponent implementation. Returns the name of this collection,
	 * if any.
	 *
	 * @return string
	 */

	function getName(){    
		return $this->_name;				
	}

	/**
	 * IFormComponent implementation. Sets the name of the collection,
	 * although this name is not used in this context.
	 *
	 * @param string $name
	 */

	function setName($name){
		$this->_name = $name;
	}

	/**
	 * Sets the label of the elements whose name is found in the input arrays
	 *
	 * @param array $labels Associative array with name/label pairs
	 * @return bool False if failed, true if succeeded.
	 */

	function setLabel($labels, $attributes = array()){

		if(is_array($labels)){
			foreach($this->_elements as $name => $element){
				if(isset($labels[$name])){
					$element->setLabel($labels[$name], $attributes);
				}
			}
		}

	}

	function getLabel($label_text = '', $attributes = array()){
		return null;
	}

	function setData($name, $value){
		if(!$this->_data) $this->_data = array();
		$this->_data[$name] = $value;
	}

	function getData($name){
		return isset($this->_data[$name])? $this->_data[$name] : null;
	}

	/**
	 * Validates all the elements and returns true if all of them are valid.
	 *
	 * @return bool True if every element validates or if a validator is not defined.
	 */

	function validate(){

		$valid = true;
		if(!is_null($this->_validator)){
			$valid = $this->_validator->validateArray($this->getRawValue());
		}

		foreach($this->_elements as $element){
			$valid = $element->validate() && $valid;
		}

		return $valid;

	}

	/**
	 * Returns the list of validators of the elements in the collection
	 *
	 * @return ListValidator
	 */

	function getValidator(){
		return $this->_validator;
	}

	/**
	 * Creates a validator for the collection
	 * 
	 * @param array $options
	 * @param array $messages
	 */

	function setValidator($options, $messages = array()){

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

	/**
	 * Returns a list of error messages
	 * @final 
	 * @return unknown
	 */

	function getErrorMessage(){
		if($this->_validator){
			return $this->_validator->getErrorMessage();
		}
		return "";
	}

	/**
	 * Sets children's error codes
	 *
	 * @param string $error_code
	 * @final
	 */

	final function setErrorCode($error_codes){
		if(!$this->_validator){
			$this->_error_code = $error_codes;		
		}
		else{
			$this->_validator->setErrorCode($error_codes);
		}
	}

	/**
	 * Gets children's error codes
	 *
	 * @return Array
	 */
	final function getErrorCode(){
		if(!$this->_validator){
			return $this->_error_code;		
		}
		else{
			return $this->_validator->getErrorCode();
		}
	}

	function hasError(){
		return $this->getErrorCode() != '';
	}

	final function getChildrenErrorCodes(){
		$error_codes = array();

		foreach($this->_elements as $name => $element){
			$error_code = $element->getErrorCode();
			if($error_code){
				$error_codes[$name] = $error_code;
			}
		}

		return $error_codes;

	}

	final function getChildrenErrorMessages(){
		$error_codes = array();

		foreach($this->_elements as $name => $element){
			$error_code = $element->getErrorMessage();
			if($error_code){
				$error_codes[$name] = $error_code;
			}
		}

		return $error_codes;

	}

	function getErrorSummary(){
		//The existence of sent values assumes that errors exist
		if($this->_raw_value){
			if($this->_error_summary){
				return HtmlHelper::escape($this->_error_summary);//Be aware of this
			}
			else{
				return AppConfig::$default_errors['summary'];
			}
		}
		return '';
	}

	function setErrorSummary($message){
		$this->_error_summary = $message;
	}

	function getValidValues($filtered = true){

		$valid = $this->validate();

		if($valid){
			return $this->getValue($filtered);
		}

		$values = array();

		foreach($this->_elements as $name => $element){

			if($element->getErrorCode() == "")
				$values[$name] =  $element->getValue($filtered);
			else 
				$values[$name] = $element->getDefaultValue();
		}

		return $values;

	}

	function setFilter($filter){
		$this->_filter = $filter;		
	}

	function getFilter(){		
		return $this->_filter;		
	}

	function setTarget($table, $column = null){
		if($column !== null)
			$this->_save_as = array($table, $column);
		else 
			$this->_save_as = array($table, $this->_name);
	}

	function getTarget(){
		return $this->_save_as;
	}

	/**
	 * Returns the html string with all the elements in the collection 
	 *
	 * @return string
	 */

	function toString($vertical = true){
		$html = '';

		foreach($this->_elements as $element){
			if($element instanceof InputHidden){
				$html .= $element->__toString();
			}
			else{
				if($label = $element->getLabel()){
					$html .= $element->__toString() . $label->__toString() . ($vertical? "<br/>":"") . "\r\n";
				}
				else{
					$html .= $element->__toString() . ($vertical? "<br/>":""). "\r\n";
				}
			}
		}

		$html .= $this->getClientValidationHtml();

		return $html;
	}

	function __toString(){
		return $this->toString();			
	}

	function getClientValidationHtml(){
		if($this->_validator
			&& $this->_validator->getOption('client_side_validable')){
			return HtmlHelper::inlineJavascript(
						"Phaxsi.Validator.Current.addValidator('$this->_name',".
							JsonHelper::encode($this->_validator->getClientOptions()).",".
							JsonHelper::encode($this->_validator->getClientErrorMessages()).",".
							JsonHelper::encode($this->getClientValidationConfig()).");");
		}
		return '';
	}

	protected function getClientValidationConfig(){
		return array('array' => true);
	}

	function isFileUpload(){
		return false;
	}
	
	function isScalar(){
		return $this->_scalar;
	}

	/**
	 * Creates a form component of the specified type
	 *
	 * @param string $type
	 * @param string $initial_value
	 * @return FormElement
	 */
	function createComponent($type, $initial_value = ''){

		$component = Loader::formComponent($type, $initial_value);

		if(!$component){
			trigger_error("Form Component of type '$type' not found", E_USER_ERROR);
			return false;
		}

		return $component;

	}

	final protected function createAndAdd($type, $value = '', $label = '', $list_id = null){
		$input = $this->createComponent($type, $value);
		$list_id = $list_id ? $list_id : count($this->_elements);
		$html_name = $this->_name . "[$list_id]";
		$this->set($input, $list_id, $html_name);
		$input->setLabel($label);
		return $input;
	}

	final function enable(){
		foreach($this->_elements as $element){
			$element->enable();
		}
	}

	final function disable(){
		foreach($this->_elements as $element){
			$element->disable();
		}
	}

}
