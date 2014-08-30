<?php
	
/**
 * InputText
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


class InputText extends FormInput{ 

	public $trim = true;

	function __construct($value = '', $name = null){
		parent::__construct('text', $value, $name);
	}

	function getValue($filtered = true){
		if($this->trim)
			$this->_value = trim($this->_value);

		return parent::getValue($filtered);
	}

	function __toString(){

		$maxlength = $this->_validator != null ? $this->_validator->getOption('max_length') : null;

		if($maxlength !== null){
			$this->setAttribute('maxlength', $maxlength);
		}

		return parent::__toString();

	}

	protected function getClientValidationConfig(){
		return array('trim' => $this->trim);
	}

}
