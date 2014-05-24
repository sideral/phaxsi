<?php

/**
 * This class finds the helpers.
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


class HelperLoader{
	
	private $load = null;
	private $helpers = array();
	
	function __construct(Loader $load){
		$this->load = $load;
	}
	
	function __get($helper){
		
		if(isset($this->helpers[$helper])){
			return $this->helpers[$helper];
		}
		
		$this->helpers[$helper] = $this->load->helper($helper);
		return $this->helpers[$helper];
		
	}
	
}