<?php

/**
 * InputFile
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
 * @package       Phaxsi.Forms.Components
 * @since         Phaxsi v 0.1
 */

class InputFile extends FormInput{

	protected $_saved_to = '';
	protected $_saving_path = '';
	protected $_saving_value = '';

	function __construct($value = "", $name = null){
		parent::__construct('file', $value, $name);
	}

	function setRawValue($value){
		if(isset($_FILES[$this->_name])){
			$this->_value = $_FILES[$this->_name];
		}
		else{
			$this->_value = array('name' => '', 'type' => '', 
								  'tmp_name' => '', 'error' => UPLOAD_ERR_NO_FILE,
								  'size' => 0);
		}
	}

	function getValue($filtered = true){

		if($this->_saving_path == '' || $this->_saving_value == ''){
			return '';
		}

		$substitutions = array();
		$path = $this->saveFileAs($this->_saving_path, 0644, $substitutions);

		if(!$path){
			return '';
		}

		$value = str_replace(array_keys($substitutions),
							 array_values($substitutions),
							 PathHelper::replaceUploadsDir($this->_saving_value));

		if($this->_filter && $filtered){
			$value = call_user_func($this->_filter, $value, $this->getName());
		}

		return $value;

	}

	function setValidator($options, $messages = array()){

		if(!$this->_validator){
			$this->_validator = new FileValidator($options, $messages);
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

	function saveFileAs($target, $chmod = 0644, &$substitutions = array()){

		if(!$this->hasFile()){
			return false;
		}

		$info = pathinfo($this->_value['name']);
		$replacements = array('name' => $info['filename'], 'ext' => $info['extension']);

		$target = PathHelper::parse($target, $replacements, $substitutions);

		$dirname = dirname($target);
		if(!file_exists($dirname)){
			$old = umask(0);
			mkdir($dirname, 0777, true);
			umask($old);
		}

		if(!$this->_saved_to){
			$success = @move_uploaded_file($this->_value["tmp_name"], $target);
			$this->_saved_to = $target;
		}
		else{
			$success = @copy($this->_saved_to, $target);
		}

		if(!$success){
			return false;
		}

		@chmod($target, $chmod);

		return $target;

	}

	function setSavingTarget($path, $value){
		$this->_saving_path = $path;
		$this->_saving_value = $value;
	}

	function getSavingTarget(){
		return array($this->_saving_path, $this->_saving_value);
	}

	function getTempName(){
		if(isset($this->_value['tmp_name'])){
			return $this->_value['tmp_name'];
		}
		return false;
	}

	function getFilename(){
		return $this->_value['name'];
	}

	function hasFile(){
		return $this->_value['error'] != UPLOAD_ERR_NO_FILE
			   && isset($this->_value['tmp_name']);
	}

	function __toString(){
		$this->_value = '';
		return parent::__toString();
	}

	protected function getClientValidationConfig(){
		return array('file' => true);
	}

	function isFileUpload(){
		return true;
	}

	function returnsValue(){
		return $this->_saving_path != '' && $this->_saving_value != '';
	}

}
