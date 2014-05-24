<?php

/**
 * Plugin Manager.
 * 

 * Copyright 2008-2012, Alejandro Zuleta (http://phplab.co)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2008-2012, Alejandro Zuleta (http://phplab.co)
 * @link          http://phaxsi.net Phaxsi PHP Framework
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @package       Phaxsi.Plugins
 * @since         Phaxsi v 0.1
 */


require_once(PHAXSIC_PLUGIN);
require_once(PHAXSIC_PLUGINLIST);

#Singleton
final class PluginManager{

	private $plugins = array();
	private $list = array();
	private static $instance = null;

	static function getInstance(){
		if(!self::$instance){
			$plugin = self::$instance = new PluginManager();
			$plugin->loadPlugins();
		}
		return self::$instance;
	}

	private function __clone(){}

	private function __construct(){}

	private function loadPlugins(){
		foreach(AppConfig::$plugins as $plugin => $config){
			$parts = explode('/', $plugin);
			$context = new Context('plugin', $parts[0], $parts[1], array(), false);
			$plugin_name = Loader::includeApplicationClass($context);
			if($plugin_name){
				$plugin = new $plugin_name($context, $config);
				$this->plugins[$plugin->getName()] = $plugin;
			}
		}

		$this->list = new PluginList($this->plugins);

		foreach($this->plugins as $plugin){
			$plugin->initialize();
		}
	}

	function getPluginList(){
		return $this->list;
	}

	function requestStart($context){
		foreach($this->plugins as $plugin){
			$plugin->requestStart($context);
		}
	}

	function requestEnd($context){
		foreach($this->plugins as $plugin){
			$plugin->requestEnd($context);
		}
	}

	function controllerStart($context){
		foreach($this->plugins as $plugin){
			$plugin->controllerStart($context);
		}
	}

	function controllerEnd($context){
		foreach($this->plugins as $plugin){
			$plugin->controllerEnd($context);
		}
	}

	function renderStart($context){
		foreach($this->plugins as $plugin){
			$plugin->renderStart($context);
		}
	}

	function renderEnd($context){
		foreach($this->plugins as $plugin){
			$plugin->renderEnd($context);
		}
	}

	function queryStart($query){
		foreach($this->plugins as $plugin){
			$plugin->queryStart($query);
		}
	}

	function queryEnd($query){
		foreach($this->plugins as $plugin){
			$plugin->queryEnd($query);
		}
	}

	function onRedirect($url){
		foreach($this->plugins as $plugin){
			$plugin->onRedirect($url);
		}
	}


}