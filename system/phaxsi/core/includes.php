<?php

/**
 * Some files that are included on startup.
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


/**
 * All paths to the framework directories. This is done
 * to avoid hardcoding things. All the constants in the
 * following lines come from this file.
 */
require_once(PHAXSID . DS . 'config' . DS . 'path.config.php');

/**
 * Include Phaxsi's Interface definitions
 */
require_once(PHAXSIC_INTERFACES);

/**
 * The 'glue' of the framework, the Context class, is included here
 */
require_once(PHAXSIC_CONTEXT);

/**
 * Includes the class that will help us find the stuff we want
 */
require_once(PHAXSIC_LOADER);

/**
 * PluginManager will take care of our plugins
 */
require_once(PHAXSIC_PLUGINMANAGER);

/**
 * We will be working with Controllers all the time. This class, and
 * its descendants call the appropiate actions and their corresponding views.
 */
require_once(PHAXSIC_ABSTRACT_CONTROLLER);

/**
 * A controller specially tweaked for layouts
 */
require_once(PHAXSIC_LAYOUT);

/**
 * The main controller. All the application controllers will
 * inherit from this class.
 */
require_once(PHAXSIC_CONTROLLER);

/**
 * Class to handle i18n
 */
require_once(PHAXSIC_LANG);

/**
 * Includes the two classes that will find our controllers
 */
require_once(PHAXSIC_URLMAPPER);
require_once(PHAXSIC_ROUTER);
require_once(PHAXSIC_REDIRECTHELPER);


if(!function_exists('mb_strlen')){
	function mb_strlen($string){
		return strlen($string);
	}
}

if(!function_exists('mb_strtoupper')){
	function mb_strtoupper($string){
		return strtoupper($string);
	}
}

if(!function_exists('mb_substr')){
	function mb_substr($str, $start, $length = null){
		return substr($str, $start, $length);
	}
}
