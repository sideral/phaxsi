<?php

/**
 * This class finds the files according to its type, includes them and create the objects.
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
 * @package       Phaxsi.Core
 * @since         Phaxsi v 0.1
 */

spl_autoload_register(array('Loader', 'autoload'));

final class Loader{

	private $context;
	private $models = array();

	final function __construct(Context $context = null){
		if(!$context){
			$context = new Context('');
		}
		$this->context = $context;
	}

	final function block($path, $args = array()){

		$context = $this->context->deriveContext('block', $path);

		$class_name = self::includeApplicationClass($context);

		if($class_name){
			$context->setArguments($args);
			return new $class_name($context, $this->context);
		}

		trigger_error("Block '$path' does not exist.", E_USER_ERROR);

	}

	final function form($path = '/phaxsi/form', $values = null, $form_params = null, $errors = null){

		$context = $this->context->deriveContext('form', $path);
		$class_name = self::includeApplicationClass($context);

		if($class_name){
			return new $class_name($context, $values, $form_params, $errors);
		}

		trigger_error("Form '$path' does not exist.", E_USER_ERROR);

	}

	final function layout($path){

		$context = $this->context->deriveContext('layout', $path);
		$class_name = self::includeApplicationClass($context);

		if($class_name){
			return new $class_name($context, $this->context);
		}

		return null;

	}

	final function utility($path){

		$context = $this->context->deriveContext('utility', $path, false);

		$class_name = self::includeApplicationClass($context);

		if($class_name){
			return new $class_name($context);
		}

		trigger_error("Utility class '$path' does not exist.", E_USER_WARNING);

	}

	final function installer($path){

		$context = $this->context->deriveContext('installer', $path);
		$class_name = self::includeApplicationClass($context);

		if($class_name){
			return new $class_name($context);
		}

		trigger_error("Installer '$path' does not exist.", E_USER_ERROR);

	}

	final function lang($path = null){

		/*
		 * Avoid looking again for a class that was not found before
		 */
		static $failed = array();

		if(!$path){
			$path = $this->context->getPath();
		}

		if(isset($failed[$path])){
			return null;
		}

		$current_lang = Lang::getCurrent();
		$context = $this->context->deriveContext('lang', $path);

		$class_name = self::includeApplicationClass($context, $current_lang);
		if($class_name){
			return new $class_name($context, $this);
		}

		/**
		 * Load default language file if the one for the current language is not found
		 */
		if($current_lang != AppConfig::DEFAULT_LANGUAGE){
			$class_name = self::includeApplicationClass($context, AppConfig::DEFAULT_LANGUAGE);
			if($class_name){
				return new $class_name($context, $this);
			}
		}

		$failed[$path] = 1;

		return null;

	}

	final function model($path){

		if(isset($this->models[$path])){
			$params = $this->models[$path];
			$name = $params[0];
			return new $name($params[1]);
		}

		$try_paths = array($path, '/'.$path.'/'.$path);

		foreach($try_paths as $path){
			$context = $this->context->deriveContext('model', $path, false);
			if(!$context) break;

			$class_name = self::includeApplicationClass($context);

			if($class_name){
				$this->models[$path] = array($class_name, $context);
				return new $class_name($context);
			}
		}

		trigger_error("Model '$path' does not exist.", E_USER_ERROR);

	}

	final function validator($path = '/phaxsi/validator'){

		$context = $this->context->deriveContext('validator', $path);
		$class_name = self::includeApplicationClass($context);

		if($class_name){
			return new $class_name($context);
		}

		trigger_error("Validator '$path' does not exist.", E_USER_ERROR);

	}

	/**
	 * Searchs a template file, includes it if found, and returns its name
	 *
	 * @param array $path
	 * @return mixed
	 */
	function template(){

		$type_info = PhaxsiConfig::$templates[$this->context->getType()];

		$template_file = self::getFileName($type_info, $this->context->getModule(), $this->context->getAction());

		if(file_exists($template_file)){
			return $template_file;
		}

		trigger_error("Template '$template_file' does not exist.", E_USER_ERROR);

		return false;

	}
	
	function helper($name){
		
		$context = $this->context->deriveContext('helper', $name, false);

		$name = strtolower($name);
		$class_name = $name.'Helper';
		$file_name = PHAXSID_HELPERS . DS . $name . '.helper.php';
		
		if(self::includeClass($file_name, $class_name)){
			return new $class_name($context);
		}
		
		$class_name = self::includeApplicationClass($context);
		
		if($class_name){
			return new $class_name($context);
		}
		
		trigger_error("Helper '$name' couldn't be loaded.", E_USER_ERROR);
		
	}

	function service($name){
		$obj = null;
		switch($name){
			case 'db':
				$obj = new DatabaseProxy($this);
				break;
			case 'plugin':
				$obj = PluginManager::getInstance()->getPluginList();
				break;
			case 'lang':
				$obj = $this->lang();
				break;
			case 'session':
				$obj = new Session($this->context->getModule(false));
				break;
			case 'helper':
				$obj = new HelperLoader($this);
				break;
			default:
				trigger_error("Trying to get invalid property '$name'", E_USER_WARNING);
				break;
		}
		return $obj;
	}

	function customFormComponent($path, $initial_value=''){

		$context = $this->context->deriveContext('input', $path, false);

		$class_name = self::includeApplicationClass($context);

		if($class_name){
			return new $class_name($initial_value);
		}

	}

	/**
	 * Loads a class that belongs to the application,
	 * returning its name
	 *
	 * @param array $path
	 * @return mixed
	 */
	static function includeApplicationClass(Context $context, $lang = ""){

		$base_module_name = $context->getBaseModuleName();

		$type_info = PhaxsiConfig::$type_info[$context->getType()];
		$class_name = $base_module_name . $type_info['suffix'];

		if(class_exists($class_name, false)){
			if(!$type_info['parent'] || is_subclass_of($class_name, $type_info['parent']))
				return $class_name;
			else
				return false;
		}

		$file_name = self::getFileName($type_info, $context->getModule(), $base_module_name, $lang);

		if(self::includeClass($file_name, $class_name, $type_info['parent'])){
			return $class_name;
		}

		return false;

	}

	/**
	 * Includes a class file within the application,
	 * performing optional inheritance tests if the actual class is found 
	 *
	 * @param string $file_name
	 * @param string $class_name
	 * @param string $parent_class
	 * @return bool
	 */
	static function includeClass($file_name, $class_name, $parent_class = ''){

		if(class_exists($class_name, false)){
			return true;
		}

		if(file_exists($file_name)){
			
			require_once($file_name);
			
			if(class_exists($class_name)){
				if((!$parent_class || is_subclass_of($class_name, $parent_class))){
					return true;
				}
				else{
					trigger_error("Class '$class_name' is not a child of '$parent_class', as expected.", E_USER_ERROR);
				}
			}
			else{
				trigger_error("File '$file_name' was included but it didn't have the expected class '$class_name'", E_USER_ERROR);
			}
		}

		return false;

	}

	private static function getFileName($type_info, $base_dir, $base_file, $lang = ""){
		return  APPD_APPLICATION . DS .
				$base_dir . DS .
				$type_info['basedir'] . ($type_info['basedir']? DS : "") .
				$base_file . '.' .
				($lang? $lang . '.' : "" ) .
				$type_info['ext'];
	}

	static function includeLibrary($file, $separator = DS){
		if($file[0] != $separator){
			$file = $separator.$file;
		}
		if(file_exists(PHAXSID_LIBRARIES.$file)){
			return require_once(PHAXSID_LIBRARIES.$file);
		}
	}

	static function formComponent($type, $initial_value = ''){
		$type = strtolower($type);	
		$class_name = 'input' . $type;
		$file_name = PHAXSID_FORMCOMPONENTS . DS . $class_name . '.class.php';
		if(self::includeClass($file_name, $class_name)){
			return new $class_name($initial_value);
		}
		return null;
	}

	static function autoload($class_name){

		$up_class = strtoupper($class_name);

		//Load any class defined in the framework
		if(defined('PHAXSIC_' . $up_class)){
			require_once(constant('PHAXSIC_' . $up_class));
			return true;
		}

		//Load registered class within application domain
		if(defined('APPC_' . $up_class)){
			require_once(constant('APPC_' . $up_class));
			return true;
		}

		return false;

	}

}
