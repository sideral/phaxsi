<?php
	
/**
 * InputCheckbox
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


class InputCheckbox extends InputCheckable{ 

	protected $checked_value = '1';
	protected $unchecked_value = '0';

	function __construct($value = null, $name = null){
		parent::__construct('checkbox', $value, $name);
		$this->_value = $this->checked_value;
	}

	function setValue($value){
		if(!is_array($value)){
			if($value == $this->checked_value){
				$this->_checked = true;
			}
			else{
				$this->_checked = false;
			}
		}
	}

	function getValue($filtered = true){

		if($this->_checked){
			$value =  $this->checked_value;
		}
		else{
			$value =  $this->unchecked_value !== null? $this->unchecked_value : '';
		}

		if($this->_filter && $filtered){
			$value = call_user_func($this->_filter, $value, $this->getName());
		}

		return $value;

	}

	function setCheckedValues($checked, $unchecked){
		$this->checked_value = $checked;
		$this->unchecked_value = $unchecked;
	}

	function setRawValue($value){

		if(!is_scalar($value) && !is_null($value)){
			return;
		}

		if($value == $this->checked_value){
			$this->_checked = true;
		}
		else{
			$this->_checked = false;
		}

	}

}
