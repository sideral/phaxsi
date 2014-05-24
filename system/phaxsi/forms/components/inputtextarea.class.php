<?php

/**
 * InputTextArea
 * 

 * Copyright 2008-2012, Alejandro Zuleta (http://phplab.co)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2008-2012, Alejandro Zuleta (http://phplab.co)
 * @link          http://phaxsi.net Phaxsi PHP Framework
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @package       Phaxsi.Forms.Components
 * @since         Phaxsi v 0.1
 */

class InputTextArea extends FormElement{

	public $trim = true;

	function __construct($initial_value = '', $name = null){
		parent::__construct('textarea', $initial_value, $name, true);
	}

	function setValue($value){
		$this->_value = $value;
	}

	function getValue($filtered = true){
		if($this->trim){
			$this->_value = trim($this->_value);
		}
		return parent::getValue($filtered);
	}

	function __toString(){
		if(!$this->getAttribute('cols'))
			$this->setAttribute('cols', 30);

		if(!$this->getAttribute('rows'))
			$this->setAttribute('rows',5);

		$this->innerHTML = HtmlHelper::escape($this->_value);
		return parent::__toString();
	}

	protected function getClientValidationConfig(){
		return array('trim' => $this->trim);
	}

}
