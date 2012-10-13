<?php

/**
 * InputPassword
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

class InputPassword extends FormInput{ 

	public $persist_password = false;

	function __construct($value = '', $name = null){
		parent::__construct('password', $value, $name);
	}

	function __toString(){

		$maxlength = $this->_validator != null ? $this->_validator->getOption('max_length') : null;

		if($maxlength !== null){
			$this->setAttribute('maxlength', $maxlength);
		}

		if(!$this->persist_password)
			$this->_value = '';

		return parent::__toString();
	}

}
