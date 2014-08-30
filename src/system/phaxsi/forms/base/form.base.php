<?php

/**
 * The base class for all forms.
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

require_once(PHAXSIC_FORMELEMENTLIST);

abstract class Form extends FormElementList {

	static $id_count = array();

	protected $_action = '';
	protected $_javascript = '';
	protected $_method = 'post';
	protected $_target = '_self';
	protected $_enc_type = 'application/x-www-form-urlencoded';
	protected $_charset = AppConfig::CHARSET;
	protected $_id;
	protected $_context;
	protected $_client_validation_enabled = true;

	protected $args;
	protected $filter;

	protected $load, $db, $plugin, $lang, $session, $valid, $helper;

	function __construct($context, &$values = array(), &$args = array(), &$errors = array()){

		//Notice: There's no difference in sending an empty $values array
		//or sending no value at all. Should this be detected?
		$action = $context->getAction();
		if(!isset(self::$id_count[$action])){
			self::$id_count[$action] = 0;
		}
		$this->_id 	= strtolower(get_class($this).'_'.$action.'_'.++self::$id_count[$action]);

		$this->_raw_value 	= (array) $values;
		$this->_context = $context;

		$this->args	= (array) $args;
		$this->load = new Loader($context);
		$this->db = new DatabaseProxy($this->load);
		$this->plugin = PluginManager::getInstance()->getPluginList();
		$this->lang = $this->load->lang();
		$this->session = new Session($context->getModule());
		$this->valid = $this->load->validator('/phaxsi/validator');
		$this->helper = new HelperLoader($this->load);

		if(empty($this->_raw_value)){
			if(($flash = Session::getFlash($this->_id))){
				$this->_raw_value = $flash['values'];
				$this->_errors = $flash['errors'];	
			}
		}

		if($errors){
			$this->_errors = array_merge((array)$this->_errors, $errors);
		}

		$create_method_ptr = array(&$this, $this->_context->getAction());
		if(is_callable($create_method_ptr)){
			call_user_func($create_method_ptr);
		}
		else{
			trigger_error("Form '".$this->_context->getPath()."' does not exist.", E_USER_ERROR);
		}

	}

	final function getId(){
		return $this->_id;
	}

	final function receivedValues(){
		return !empty($this->_raw_value);
	}

	final function setAction($path){

		if(preg_match('/^https?:\/\//', $path)){
			$this->_action = $path;
			return;
		}

		if(isset($path[0]) && $path[0] != '/'){
			$path = $this->_context->getModule().'/'.$path;
		}

		$this->_action = UrlHelper::localized($path);

	}

	final function getAction(){
		return $this->_action;
	}

	final function setAjaxAction($action, $update_element_id, $wait_function = 'null', $success_function = 'null'){
		$action = UrlHelper::get($action);
		$this->_javascript .= HtmlHelper::inlineJavascript("Event.on('$this->_id', 'submit', Phaxsi.Form.submit.createDelegate(this, ['$this->_id','$action', '$update_element_id', $wait_function, $success_function], true));");
	}

	final function setMethod($method){
		$this->_method = $method;
	}

	final function getMethod(){
		return $this->_method;
	}

	final function setFormTarget($target){
		$this->_target = $target;			
	}

	final function getFormTarget(){
		return $this->_target;
	}

	final function setEncType($value){
		$this->_enc_type = $value;
	}

	final function getEncType(){
		return $this->_enc_type;
	}

	final function setCharset($charset){
		$this->_charset = $charset;
	}

	final function getCharset(){
		return $this->_charset;
	}

	final function open($attributes = array())
	{
		$html_id = $this->_id;
		if(isset($attributes['id'])){
			$html_id =  $attributes['id'];
			unset($attributes['id']);
		}

		$attributes = HtmlHelper::formatAttributes($attributes);
		$validation_script = '';

		if($this->_client_validation_enabled){
			$this->enableClientValidation();
			$validation_script = HtmlHelper::inlineJavascript("Phaxsi.Validator.Current = Phaxsi.Validator.List['$html_id'] = new Phaxsi.Validator.Manager('$html_id');\r\n".
															"Phaxsi.Validator.DefaultErrorMessages = ".JsonHelper::encode(Validator::getDefaultErrorMessages()) .";");
			$this->_javascript .= HtmlHelper::inlineJavascript("Phaxsi.Validator.Current.attachToSubmit();");
		}

		#For xhtml strict compliance
		$target_str = $this->_target == '_self'? '' : 'target="'.$this->_target.'"';

		return "<form id=\"$html_id\" accept-charset=\"".$this->_charset ."\" action=\"" . HtmlHelper::escape($this->_action) . "\" method=\"$this->_method\"  $attributes $target_str enctype=\"$this->_enc_type\">\r\n".$validation_script;

	}

	final function close(){
		return "</form>\r\n$this->_javascript";
	}

	final function __get($component_name){
		return $this->_elements[$component_name];
	}

	final function __isset($component_name){
		 return isset($this->_elements[$component_name]);
	}

	final function getArgument($name){
		if(isset($this->args[$name])){
			return $this->args[$name];
		}
	}

	final protected function setDefaultArgs($defaults){

		$new_args = array();

		foreach($defaults as $name => $value){
			if(isset($this->args[$name])){
				$new_args[$name] = $this->args[$name];
			} 
			else{
				$new_args[$name] = $value;
			}
		}

		$this->args = $new_args;

	}

	final function hasErrors(){
		return count($this->_errors) > 0;
	}

	final function hasError(){
		return count($this->_errors) > 0;
	}

	final protected function setErrors(array $errors){
		$this->_errors = $errors;

		foreach($this->_elements as $name => $element){
			if(isset($this->_errors[$name])){
				$element->setErrorCode($this->_errors[$name]);
			}
		}
	}

	final function setRawValue($values){
		if(is_array($values)){
			$this->_raw_value = $values;
			foreach($this->_elements as $name => $element){
				if(isset($this->_raw_value[$name])){
					$element->setRawValue($this->_raw_value[$name]);
				}
				else if($this->_raw_value){
					$element->setRawValue(null);
				}
			}
		}
	}

	final function setValue($values){
		if(is_array($values)){
			foreach($this->_elements as $name => $element){
				if(isset($values[$name])){
					$element->setValue($values[$name]);
				}
			}
		}
	}

	/**
	 * Creates and adds a new element to the form
	 *
	 * @param string $type
	 * @param string $input_name
	 * @param string $initial_value
	 * @return IFormComponent
	 */
	final function add($type, $input_name, $initial_value = null){
		$component = $this->set($this->createComponent($type, $initial_value), $input_name);
		if($component->isFileUpload()){
			$this->setEncType("multipart/form-data");
		}
		return $component;
	}

	final function remove($input_name){
		if(isset($this->_elements[$input_name])){
			$input = $this->_elements[$input_name];
			unset($this->_elements[$input_name]);
			return $input;
		}
		return false;
	}

	function createComponent($type, $initial_value = ''){
		$component = $this->load->formComponent($type, $initial_value);
		if(!$component){
			$component = $this->load->customFormComponent($type, $initial_value);
			if(!$component){
				trigger_error("Form Component of type '$type' not found", E_USER_ERROR);
			}
		}
		return $component;
	}

	final function getFlashParams(){
		return array('values' => $this->getRawValue(),
					 'errors' => $this->getChildrenErrorCodes());
	}

	final function validateOrRedirect($url = null){
		if(!$url) $url = UrlHelper::referer();
		if(!$url) $url = '/';
		if(!$this->validate()){
			RedirectHelper::flash($url, $this->getId(), $this->getFlashParams());
		}
	}

	final function __toString(){
		$html = $this->open();
		$html .= parent::__toString();
		$html .= $this->close();
		return $html;
	}

	final function extendForm($path, $args = array()){

		$form = $this->load->form($path, $this->_raw_value, $args, $this->_errors);

		if($form){
			foreach($form as $name => $element){
				$this->_elements[$name] = $element;
			}
			$this->setEncType($form->getEncType());
		}

	}

	final public function enableClientValidation(){
		$this->_client_validation_enabled = true;
		foreach($this->_elements as $component){
			$validator = $component->getValidator();
			if(!$validator)
				continue;
			if(is_null($validator->getOption('client_side_validable'))){
				$validator->setOption('client_side_validable', true);
			}
		}
	}

	final public function disableClientValidation(){
		$this->_client_validation_enabled = false;
		foreach($this->_elements as $component){
			$validator = $component->getValidator();
			if(!$validator)
				continue;
			$validator->setOption('client_side_validable', false);
		}
	}

	final function getTargetValues($table_name){

		$fields = array();

		$array_value = array();

		foreach($this->_elements as $component){

			$target = $component->getTarget();
			if(!$target) continue;

			list($target_table, $target_field) = $target;

			if($target_table == $table_name){
				$fields[$target_field] = $component->getValue();
			}

		}

		return $fields;

	}

}
