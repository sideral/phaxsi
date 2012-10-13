<?php

/**
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
 */


/**
 * Let's starts with a nice shortcut to save time.
 * This value is diferent in Linux '/' and in Windows '\'.
 */
define('DS', DIRECTORY_SEPARATOR);

/**
 * Gets the directory path to this file and puts it in a
 * constant. This constant will serve as the base path
 * for the application directories.
 */
define('APPD', dirname(__FILE__));

define('APPD_APPLICATION',	APPD . DS . 'application');
define('APPD_SYSTEM',	APPD . DS . 'system');
	define('PHAXSID',	APPD_SYSTEM . DS . 'phaxsi');
define('APPD_PUBLIC',	APPD . DS . 'public');

/**
 * Loads the application configuration file.
 * 
 */
require_once(APPD_SYSTEM.DS.'app.config.php');

/**
 * Starts the framework by including its main file.
 */

if(php_sapi_name() == 'cli' && empty($_SERVER['REMOTE_ADDR']) && isset($_SERVER['argv'])){
	require_once(PHAXSID . DS . 'shell.php');
}
else{
	require_once(PHAXSID . DS . 'main.php');
}
