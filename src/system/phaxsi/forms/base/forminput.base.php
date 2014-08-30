<?php

/**
 * The base class for all form inputs.
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

abstract class FormInput extends FormElement {

	protected $type;

	function __construct($type, $initial_value, $name){
		parent::__construct("input", $initial_value, $name);
		$this->setAttribute("type", $type);
		$this->setAttribute("class", 'form_input_'.$type);
	}

	function __toString(){
		//Allows children to write this attribute first
		if($this->getAttribute('value') === false){
			$this->setAttribute("value", $this->getRawValue());
		}
		return parent::__toString();
	}

}
