<?php
	
/**
 * The base class for radios and chekboxes.
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

abstract class InputCheckable extends FormInput{ 

	static $next_value = 0;

	protected $_checked = false;

	function __construct($type, $value, $name){
		if(is_null($value)){
			$value = self::generateValue();
		}
		parent::__construct($type, $value, $name);
	}

	function getRawValue(){
		if($this->_checked)
			return $this->_value;
		else
			return null;
	}

	function isChecked(){
		return $this->_checked;
	}

	function check(){
		$this->_checked = true;
	}

	function uncheck(){
		$this->_checked = false;
	}

	function setValidator($options, $messages = array()){
		//Why? This makes validation to fail!!
		if(!$this->_checked && isset($options['required']) && $options['required']){
			if(!isset($options['null_values'])){
				//$options['null_values'] = array($this->_value);
			}
			else{
				//$options['null_values'][] = $this->_value;
			}
		}
		parent::setValidator($options, $messages);
	}

	function __toString(){

		if($this->_checked){
			$this->setAttribute('checked', 'checked');
		}

		$this->setAttribute('value', $this->_value);

		return parent::__toString();

	}

	static function generateValue(){
		return 'inputcheckable_'.(++self::$next_value);
	}

}
