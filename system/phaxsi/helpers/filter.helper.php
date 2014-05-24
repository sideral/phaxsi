<?php

/**
 * Filters input.
 * 

 * Copyright 2008-2012, Alejandro Zuleta (http://phplab.co)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2008-2012, Alejandro Zuleta (http://phplab.co)
 * @link          http://phaxsi.net Phaxsi PHP Framework
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @package       Phaxsi.Helpers
 * @since         Phaxsi v 0.1
 */

require_once(PHAXSIC_VALIDATOR);

class FilterHelper{

	static function validate(&$values, $validators, $defaults = array(), $names = array()){

		$filtered_values = array();

		foreach($validators as $name => $options){
			if(!isset($values[$name])){
				$values[$name] = null;
			}

			if(!$options)
				$options = array();

			$validator = new Validator($options);
			$validator->setOption('required', true);

			if(!isset($options['array']) || $options['array'] === false){
				$valid = !is_array($values[$name]) && $validator->validate($values[$name]);
			}
			else{
				$valid = is_array($values[$name]) && $validator->validateArray($values[$name]);
			}

			$key_name = isset($names[$name])? $names[$name] : $name;

			if($valid){
				$filtered_values[$key_name] = $values[$name];
			}
			else{
				if(isset($defaults[$name]))
					$filtered_values[$key_name] = $defaults[$name];
				else
					$filtered_values[$key_name] = null;
			}

		}

		return $values = $filtered_values;

	}

	static function defaults(&$array, $defaults, $names = array(), $replace_original = true){

		$new_args = array();
		$names = (array)$names;

		foreach($defaults as $name => $value){
			$key_name = isset($names[$name])? $names[$name] : $name;
			if(isset($array[$name])){
				$new_args[$key_name] = $array[$name];
			}
			else{
				$new_args[$key_name] = $value;
			}
		}

		if($replace_original){
			$array = $new_args;
		}

		return $new_args;

	}
	
	static function defaultsRecursive(&$array, $defaults, $names = array(), $replace_original = true){

		$new_args = array();
		$names = (array)$names;

		foreach($defaults as $name => $value){
			$key_name = isset($names[$name])? $names[$name] : $name;
			
			if(isset($array[$name])){
				if(is_array($value) && $value){
					$array[$name] = self::defaultsRecursive($array[$name], $value);
				}
				$new_args[$key_name] = $array[$name];
			}
			else{
				$new_args[$key_name] = $value;
			}
		}

		if($replace_original){
			$array = $new_args;
		}

		return $new_args;

	}

}
