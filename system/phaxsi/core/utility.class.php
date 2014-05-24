<?php

/**
 * The base class for the application's utilities.
 * 

 * Copyright 2008-2012, Alejandro Zuleta (http://phplab.co)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2008-2012, Alejandro Zuleta (http://phplab.co)
 * @link          http://phaxsi.net Phaxsi PHP Framework
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @package       Phaxsi.Core
 * @since         Phaxsi v 0.1
 */


abstract class Utility{

	protected $context;
	protected $load;

	function __construct($context){
		$this->context  = $context;
		$this->load = new Loader($context);
	}

	final function __get($name){
		return $this->$name = $this->load->service($name);
	}

}
