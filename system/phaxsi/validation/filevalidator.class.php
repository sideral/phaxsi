<?php

/**
 * Validates files.
 * 

 * Copyright 2008-2012, Alejandro Zuleta (http://phplab.co)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2008-2012, Alejandro Zuleta (http://phplab.co)
 * @link          http://phaxsi.net Phaxsi PHP Framework
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @package       Phaxsi.Validation
 * @since         Phaxsi v 0.1
 */

class FileValidator extends Validator {
	
	protected $default_options = array(
		'required' => false, 				// Is the value required?
		 'extension' => array(),			// Array of allowed extensions
		 'max_size' => null,				// The max value for a number
		 'min_size' => null,				// The min value for a number
		 'callback' => null,				// A function reference to perform advanced validation. Must return true if the value is valid, anything else otherwise.
		 'comparisons' => array(),			// A list of booleans. If one of these is false, validation returns false
		 'validate_if' => true,			    // A list of bolleans for conditional validation. Only validate this value if all conditions are true
		 'partial'	   => null,
		 'no_tmp_dir'  => null,
		 'cant_write'  => null,
		 'mime_types'  => array(),
		 'client_side_validable' => null
	);
	
	/**
	 * Validates the passed value with respect to the options specified
	 *
	 * @param mixed $value The value that wants to be validated
	 * @return bool True if the value is valid, false if not.
	 */

	function validate($value){

		$valid = $this->doGeneralChecks($value);
		if($valid !== null){
			return $valid;
		}

		if($value['error'] == UPLOAD_ERR_NO_FILE){
			if($this->options['required']){
				$this->error = "required";
				return false;
			}
			else{
				return true;
			}
		}

		if($value['error'] == UPLOAD_ERR_FORM_SIZE 
			|| $value['error'] == UPLOAD_ERR_INI_SIZE){

			$this->error = 'max_size';
			return false;
		}

		if($this->options['partial'] && $value['error'] == UPLOAD_ERR_PARTIAL){
			$this->error = 'partial';
			return false;
		}

		if($this->options['no_tmp_dir'] && $value['error'] == UPLOAD_ERR_NO_TMP_DIR){
			$this->error = 'no_tmp_dir';
			return false;
		}

		if($this->options['cant_write'] && $value['error'] == UPLOAD_ERR_CANT_WRITE){
			$this->error = 'cant_write';
			return false;
		}

		if($value['error'] == UPLOAD_ERR_EXTENSION){
			$this->error = 'extension';
			return false;
		}

		$mime_types = $this->options['mime_types'];

		if($mime_types){
			if(!isset($mime_types[$value['type']])){
				$this->error = 'mime_types';
				return false;
			}
			else if(!in_array($this->getExtension($value['name']), (array)$mime_types[$value['type']])){
				$this->error = 'mime_types';
				return false;
			}
		}

		if($this->options['max_size'] !== null 
				&& $value['size'] > $this->options['max_size']){
			$this->error = 'max_size';
			return false;
		}

		if($this->options['min_size'] !== null 
				&& $value['size'] < $this->options['min_size']){
			$this->error = 'min_size';
			return false;
		}

		return true;

	}

	protected function getExtension($name){
		$pos = strrpos($name, '.');
		if($pos !== false){
			return strtolower(substr($name, $pos+1));
		}
		return "";
	}

	function getClientOptions(){

		$options = array();

		$allowed = array('required', 'extension');

		foreach($allowed as $key){
			if($this->options[$key]){
				$options[$key] = $this->options[$key];
			}
		}

		return $options;

	}

	function getClientErrorMessages(){

		$messages = array();

		$allowed = array('required', 'extension');

		foreach($allowed as $key){
			if(isset($this->messages[$key])){
				$messages[$key]= $this->messages[$key];
			}
		}

		return $messages;

	}

}