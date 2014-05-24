<?php

/**
 * Validates input.
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


/**
 * Validates a value or an array of values against a set of validation options
 *
 */
class Validator{

	protected $options;
	protected $error = '';
	protected $messages;
	
	protected $default_options = array(
		'required' => false, 				// Is the value required?
		 'expression' => null,				// Perl-compatible regular expression
		 'in' => array(),					// A white list of values
		 'not_in' => array(),				// A black list of values
		 'max_length' => null,				// The max length of the string value
		 'min_length' => null,				// The min length of the string value
		 'max_value' => null,				// The max value for a number
		 'min_value' => null,				// The min value for a number
		 'encoding'	=>	false,				// Wheter there are non valid characters
		 'callback' => null,				// A function reference to perform advanced validation. Must return true if the value is valid, anything else otherwise.
		 'null_values' => array('', null),  // A list of values that are to be considered null, for use in required
		 'comparisons' => array(),			// A list of booleans. If one of these is false, validation returns false
		 'validate_if' => true,			    // A booleans for conditional validation. Only validate this value if this is true
		 'array_required_values' => array(),// List of values that must be present
		 'array_required_keys' => array(), 	// List of arrray keys required
		 'array_count' => null,				// The exact number of items on the list
		 'array_min_count' => null,			// The minimum number of elements that have to be present
		 'array_max_count' => null,			// The maximum number of elements that must be present
		 'db_in_column' => null,			// A size 2 array containing the name of the table and the field where the value must be present
		 'db_not_in_column' => null,		// A size 2 array containing the name of the table and the field where the value must NOT be present
		 'array_allow_duplicates' => true,	// Every value in an array must be unique?
		 'client_side_validable' => null	// Allows or disallows the element to be validated on the client side
	);

	/**
	 * Creates a new Validator instance
	 *
	 * @param array $options An array of validation options
	 * @param array $messages An array of error messages corresponding to the options
	 */

	function __construct($options, $messages = array()){

		if(isset($options['validate_if'])){
			$options['client_side_validable'] = false;
		}

		$this->messages = $messages;
		
		if(count(array_diff(array_keys($options), array_keys($this->default_options))) > 0){
			trigger_error("Trying to set validation options that don't exist.", E_USER_ERROR);
			return;
		}
		
		$this->options  = array_merge($this->default_options, $options);
		
	}

	/**
	 * Validates the passed value with respect to the options specified
	 *
	 * @param mixed $value The value that wants to be validated
	 * @return bool True if the value is valid, false if not.
	 */

	function validate($value){

		if(is_array($value) || is_object($value)){
			$this->error = 'array_type';
			return false;
		}

		$valid = $this->doGeneralChecks($value);
		if($valid !== null){
			return $valid;
		}

		$is_null = in_array($value, $this->options['null_values'], true);

		if($this->options['required'] && $is_null){
			$this->error = "required";
			return false;
		}

		if($is_null){
			return true;
		}

		if($this->options['expression'] 
					&& !preg_match($this->options['expression'], $value)){
			$this->error = 'expression';
			return false;
		}

		/**
		 * WTF? in_array returns true if the array contains the number 0
		 * and the value searched is any string other than a number.
		 */
		if(!empty($this->options['in'])){
			foreach ($this->options['in'] as &$in){
				if($in === 0) $in = '0';
			}
		}

		if(!empty($this->options['in']) && !in_array($value, $this->options['in'])){
			$this->error = 'in';
			return false;
		}

		if(!empty($this->options['not_in']) && in_array($value, $this->options['not_in'])){
			$this->error = 'not_in';
			return false;
		}

		$str_value = (string)$value;

		//Assumes mbstring extension installed
		if($this->options['max_length'] !== null 
				&& mb_strlen($str_value) > $this->options['max_length']){
			$this->error = 'max_length';
			return false;
		}

		//Assumes mbstring extension installed
		if($this->options['min_length']  !== null
				&& mb_strlen($str_value) < $this->options['min_length']){
			$this->error = 'min_length';
			return false;
		}

		if($this->options['encoding']){
			$converted = @iconv(AppConfig::CHARSET, AppConfig::CHARSET, $str_value);
			if(mb_strlen($converted) != mb_strlen($str_value)){
				$this->error = 'encoding';
				return false;
			}
		}

		$float_value = (float)$value;

		if($this->options['max_value'] !== null
					&& $float_value > $this->options['max_value']){
			$this->error = 'max_value';
			return false;
		}

		if($this->options['min_value'] !== null
					&& $float_value < $this->options['min_value']){
			$this->error = 'min_value';
			return false;
		}

		if($this->options['db_in_column']){

			list($table_name, $column_name) = $this->options['db_in_column'];
			$query = new TableReader($table_name);
			$count = $query->select(array('count', '*'))
										->where($column_name, $value)->fetchScalar();
			if($count == 0){
				$this->error = 'db_in_column';
				return false;
			}
		}


		if($this->options['db_not_in_column']){
			list($table_name, $column_name) = $this->options['db_not_in_column'];
			$query = new TableReader($table_name);
			$count = $query->select(array('count','*'))
							->where($column_name, $value)->fetchScalar();
			if($count > 0){
				$this->error = 'db_not_in_column';
				return false;
			}
		}

		return true;

	}

	/**
	 * Validates an array with respect to the options specified
	 *
	 * @param array $values An array of values to be validated
	 * @return bool True if the valuea are valid, false if not.
	 */

	function validateArray($values){

		if(!is_array($values)){
			$this->error = 'array_type';
			return false;
		}

		$valid = $this->doGeneralChecks($values);
		if($valid !== null){
			return $valid;
		}

		$array_count = count($values);
		$null_count = 0;

		//Count null values
		foreach($values as $value){
			if(in_array($value, $this->options['null_values'], true)){
				$null_count++;
			}
		}

		if($this->options['required'] && ($null_count != 0 || $array_count == 0)){
			$this->error = "required";
			return false;
		}

		if($this->options['array_max_count'] !== null 
			&& ($array_count - $null_count) > $this->options['array_max_count']){
			$this->error = "array_max_count";
			return false;
		}

		if($this->options['array_min_count'] !== null
			&& ($array_count - $null_count) < $this->options['array_min_count']){
			$this->error = "array_min_count";
			return false;
		}

		if($this->options['array_required_keys']){
			//If array_diff_key returns a non empty array it means that 
			//not all required keys are present in $values
			if(count(array_diff_key(array_flip($this->options['array_required_keys']), $values))){
				$this->error = 'array_required_keys';
				return false;
			}
		}

		if($this->options['array_required_values']){
			if(count(array_diff($this->options['array_required_values'], $values))){
				$this->error = 'array_required_values';
				return false;
			}
		}

		if($this->options['array_count'] !== null
			&& $array_count != $this->options['array_count']){
				$this->error = 'array_count';
				return false;
		}

		if(!$this->options['array_allow_duplicates']){
			$unique = array_unique($values);
			if(count($unique) != $array_count){
				$this->error = 'array_allow_duplicates';
				return false;
			}
		}

		//All values are null and not required, so stop validating
		if($null_count == $array_count){
			return true;
		}

		//This allows to perform element by element validation on the remaining
		//options
		$new_options = array(
			'expression' 	=> $this->options['expression'],
			 'in' 			=> $this->options['in'],
			 'max_length' 	=> $this->options['max_length'],
			 'min_length' 	=> $this->options['min_length'],
			 'max_value' 	=> $this->options['max_value'],
			 'min_value' 	=> $this->options['min_value'],
			 'db_in_column' => $this->options['db_in_column'],
			 'db_not_in_column' => $this->options['db_not_in_column']
		);

		$validator = new Validator($new_options);

		foreach($values as $value){
			$valid = $validator->validate($value);
			if(!$valid){
				$this->error = $validator->getErrorCode();
				return false;
			}
		}

		return true;

	}

	/**
	 * Conditional validation, comparisons and callback, to be used by validate and validateArray 
	 *
	 * @return bool True, false or null.
	 */

	protected function doGeneralChecks($value){

		if($this->options['validate_if'] == false){
			return true;
		}

		if($this->options['comparisons']){

			$key = array_search(false, $this->options['comparisons'], true);

			if($key !== false){
				$this->error = 'comparisons/'.$key;
				return false;
			}

		}

		if($this->options['callback'] !== null){

			$return = call_user_func($this->options['callback'], $value);

			if($return !== true && $return !== ''){

				if(!$return){
					$this->error = 'callback';
					return false;
				}
				else{
					$this->error = 'callback/'.$return;
					return false;
				}

			}
		}

		return null;

	}

	function getErrorCode(){
		return $this->error;
	}

	function setErrorCode($error_code){
		$this->error = $error_code;
	}

	function getErrorMessage(){

		if(isset($this->messages[$this->error])){ 
			return $this->messages[$this->error];
		}

		if($this->error){

			$load = new Loader(new Context('lang', 'phaxsi'));
			$lang = $load->lang();

			if($lang && isset($lang->default_errors[$this->error])){
				return $lang->default_errors[$this->error];
			}

			$current = Lang::getCurrent();

			if(isset(AppConfig::$generic_error_message[$current])){
				return AppConfig::$generic_error_message[$current];
			}

			return AppConfig::$generic_error_message[AppConfig::DEFAULT_LANGUAGE];

		}

		return "";

	}

	function setOption($name, $value){
		
		if($name == 'validate_if')
			$this->options['client_side_validable'] = false;
		
		if(!isset($this->default_options[$name]) && !is_null($this->default_options[$name])){
			trigger_error("Trying to set invalid validation option '$name'.", E_USER_ERROR);
			return;
		}
		
		$this->options[$name] = $value;
		
	}

	function addOptions($options){
		if(isset($options['validate_if'])){
			$options['client_side_validable'] = false;
		}
		if(count(array_diff(array_keys($options), array_keys($this->default_options))) > 0){
			trigger_error("Trying to set validation options that don't exist.", E_USER_ERROR);
			return;
		}
		$this->options  = array_merge($this->options, $options);
	}

	function getOption($name){
		if(!isset($this->options[$name])){
			return null;
		}
		return $this->options[$name];
	}

	function getAllOptions(){
		return $this->options;
	}

	function setMessage($name, $value){
		$this->messages[$name] = $value;
	}

	function getMessage($name){
		if(!isset($this->messages[$name])){
			return null;
		}
		return $this->messages[$name];
	}

	function getClientOptions(){

		$options = array();

		$allowed = array('required', 'expression', 'in', 'max_length',
						 'min_length', 'max_value', 'min_value',
						 'null_values', 'array_required_values',
						 'array_required_keys', 'array_count',
						 'array_min_count', 'array_max_count',
						 'array_allow_duplicates');

		foreach($allowed as $key){
			if($this->options[$key]){
				$options[$key] = $this->options[$key];
			}
		}

		if($this->options['array_allow_duplicates'] == false)
			$options['array_allow_duplicates'] = false;
		else
			unset($options['array_allow_duplicates']);

		if(isset($options['expression'])){
			$options['expression'] = trim($options['expression'], '/uD');
		}

		return $options;

	}

	function getClientErrorMessages(){

		$messages = array();

		$allowed = array('required', 'expression', 'in', 'max_length',
						 'min_length', 'max_value', 'min_value',
						 'null_values', 'array_required_values',
						 'array_required_keys', 'array_count',
						 'array_min_count', 'array_max_count',
						 'array_allow_duplicates');

		foreach($allowed as $key){
			if(isset($this->messages[$key])){
				$messages[$key] = $this->messages[$key];
			}
		}

		return $messages;

	}

	static function getDefaultErrorMessages(){

		$load = new Loader(new Context('lang', 'phaxsi'));
		$lang = $load->lang();

		if($lang){
			return $lang->default_errors;
		}

		$current = Lang::getCurrent();

		if(isset(AppConfig::$generic_error_message[$current])){
			return array(AppConfig::$generic_error_message[$current]);
		}

		return array(AppConfig::$generic_error_message[AppConfig::DEFAULT_LANGUAGE]);

	}

}
