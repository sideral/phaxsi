<?php

/**
 * Manages the list of loaded plugins.
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
 * @package       Phaxsi.Plugins
 * @since         Phaxsi v 0.1
 */


class PluginList{

	private $plugins = array();

	function __construct($plugins){
		$this->plugins = $plugins;
	}

	function __get($name){
		if(isset($this->plugins[$name])){
			return $this->$name = $this->plugins[$name];
		}
		trigger_error("Plugin '$name' was not found", E_USER_WARNING);
		return null;
	}

	function __isset($name){
		return isset($this->plugins[$name]);
	}

	function isEnabled($name){
		return isset($this->plugins[$name]) && $this->plugins[$name]->isEnabled();
	}

}