<?php

/**
 * InputSubmit
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

class InputSubmit extends FormInput{ 

	function __construct($value = "Submit", $name = null){
		parent::__construct('submit', $value, $name);
	}

	function setValue($value){
		parent::setValue($value);
		return $this;
	}

	function returnsValue(){
		return false;
	}

}
