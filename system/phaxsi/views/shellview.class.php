<?php

/**
 * View for controllers that sends mails.
 * 

 * Copyright 2008-2014, Alejandro Zuleta (http://phplab.co)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2008-2014, Alejandro Zuleta (http://phplab.co)
 * @link          http://phaxsi.net Phaxsi PHP Framework
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @package       Phaxsi.Views
 * @since         Phaxsi v 0.1
 */

class ShellView extends View{

	function __construct($context){
		$this->context = $context;
		$this->load = new Loader($context);
	}

	public function write($line){
		print $line."\r\n";
	}

	public function render(){
		if($this->_template_vars){
			return print_r($this->_template_vars, true);
		}
		return '';
	}

}