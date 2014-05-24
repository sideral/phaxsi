<?php

/**
 * Main framework configuration class.
 * 

 * Copyright 2008-2012, Alejandro Zuleta (http://phplab.co)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2008-2012, Alejandro Zuleta (http://phplab.co)
 * @link          http://phaxsi.net Phaxsi PHP Framework
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @package       Phaxsi.Config
 * @since         Phaxsi v 0.1
 */


class PhaxsiConfig{

	const FRAMEWORK_VERSION = '0.9';

	static $type_info = array(
		'block'		=> array(
			'suffix' 	=> 'Block',
			'basedir'	=> '',
			'parent'	=> 'Block',
			'ext'		=> 'block.php'
		),
		'controller'=>   array(
			'suffix' 	=> 'Controller',
			'basedir'	=> '',
			'parent'	=> 'Controller',
			'ext'		=> 'controller.php'
		),
		'layout'	=> array(
			'suffix' 	=> 'Layout',
			'basedir'	=> '',
			'parent'	=> 'Layout',
			'ext'		=> 'layout.php'
		),
		'form'		=> array(
			'suffix' 	=> 'Form',
			'basedir'	=> '',
			'parent'	=> 'Form',
			'ext'		=> 'form.php'
		),
		'plugin'	=> array(
			'suffix' 	=> 'Plugin',
			'basedir'	=> '',
			'parent'	=> 'Plugin',
			'ext'		=> 'plugin.php'
		),
		'model'		=>	array(
			'suffix'	=> 'Model',
			'basedir'	=> '',
			'parent'	=> 'Model',
			'ext'		=> 'model.php'
		),
		'lang'		=>  array(
			'suffix'	=> 'Lang',
			'basedir'	=> 'lang',
			'parent'	=> 'Lang',
			'ext'		=> 'lang.php'
		),
		'shell'		=>  array(
			'suffix'  => 'Shell',
			'basedir' => '',
			'parent'  => 'Shell',
			'ext'     => 'shell.php'
		),
		'utility'	=>	array(
			'suffix' 	=> 'Utility',
			'basedir'	=> '',
			'parent'	=> 'Utility',
			'ext'		=> 'utility.php'
		),
		'installer'	=>	array(
			'suffix' 	=> 'Installer',
			'basedir'	=> '',
			'parent'	=> 'Installer',
			'ext'		=> 'installer.php'
		),
		'input'		=> array(
			'suffix'	=> 'Input',
			'basedir'	=> 'custom',
			'parent'	=> 'IFormComponent',
			'ext'		=> 'input.php'
		),
		'validator'	=> array(
			'suffix'	=> 'Validator',
			'basedir'	=> 'custom',
			'parent'	=> '',
			'ext'		=> 'validator.php'
		),
		'router'	=> array(
			'suffix'	=> 'Router',
			'basedir'	=> 'custom',
			'parent'	=> 'Router',
			'ext'		=> 'router.php'
		),
		'helper'	=> array(
			'suffix'	=> 'Helper',
			'basedir'	=> 'custom',
			'parent'	=> '',
			'ext'		=> 'helper.php'
		)
	);

	 static $templates = array(
		'controller'=> array(
			'basedir' => 'html',
			'ext' => 'html.php'
		),
		'layout' 	=> array(
			'basedir' => 'html',
			'ext' => 'layout.html.php'
		),
		'block'  	=> array(
			'basedir' => 'html/blocks',
			'ext'	=> 'html.php'
		)
	 );

}

define('DEFAULT_MODULE', 'index');

define('APPD_VAR',	APPD_SYSTEM . DS . 'var');
	define('APPD_CACHE',	APPD_VAR . DS . 'cache');
	define('APPD_TMP',		APPD_VAR . DS . 'tmp');
	define('APPD_LOG',		APPD_VAR . DS . 'log');

define('APPU_PUBLIC', 'public');
	define('APPU_PHAXSI', 'phaxsi');

/**
 * Paths to the framework's classes
 *
 */

define('PHAXSID_CONTROLLERS',	PHAXSID . DS . 'controllers');
	define('PHAXSIC_ABSTRACT_CONTROLLER', PHAXSID_CONTROLLERS . DS . 'abstract_controller.class.php');
	define('PHAXSIC_CONTROLLER',		  PHAXSID_CONTROLLERS . DS . 'controller.class.php');
	define('PHAXSIC_LAYOUT',			  PHAXSID_CONTROLLERS . DS . 'layout.class.php');
	define('PHAXSIC_BLOCK',				  PHAXSID_CONTROLLERS . DS . 'block.class.php');
	define('PHAXSIC_SHELL',				  PHAXSID_CONTROLLERS . DS . 'shell.class.php');

define('PHAXSID_CORE', PHAXSID . DS . 'core');
	define('PHAXSIC_INTERFACES',	PHAXSID_CORE . DS . 'interfaces.base.php');
	define('PHAXSIC_LANG',			PHAXSID_CORE . DS . 'lang.class.php');
	define('PHAXSIC_UTILITY',		PHAXSID_CORE . DS . 'utility.class.php');
	define('PHAXSIC_SESSION',		PHAXSID_CORE . DS . 'session.class.php');
	define('PHAXSIC_CONTEXT',       PHAXSID_CORE . DS . 'context.class.php');
	define('PHAXSIC_LOADER',		PHAXSID_CORE . DS . 'loader.class.php');
	define('PHAXSIC_HELPERLOADER',	PHAXSID_CORE . DS . 'helperloader.class.php');
	

define('PHAXSID_ROUTE', PHAXSID . DS . 'route');
	define('PHAXSIC_URLMAPPER',		PHAXSID_ROUTE . DS . 'urlmapper.class.php');
	define('PHAXSIC_ROUTER',		PHAXSID_ROUTE . DS . 'router.class.php');
	define('PHAXSIC_SHELLMAPPER',	PHAXSID_ROUTE . DS . 'shellmapper.class.php');

define('PHAXSID_PLUGINS', PHAXSID . DS . 'plugins');
	define('PHAXSIC_PLUGINMANAGER', PHAXSID_PLUGINS . DS . 'pluginmanager.class.php');
	define('PHAXSIC_PLUGIN',        PHAXSID_PLUGINS . DS . 'plugin.class.php');
	define('PHAXSIC_PLUGINLIST',    PHAXSID_PLUGINS . DS . 'pluginlist.class.php');
	
define('PHAXSID_CACHE', PHAXSID . DS . 'cache');
	define('PHAXSIC_PHAXSICACHE',	PHAXSID_CACHE . DS . 'cache.class.php');
	define('PHAXSIC_CACHEFILEPROVIDER',	PHAXSID_CACHE . DS . 'cache.file.php');
	define('PHAXSIC_CACHEMEMCACHEPROVIDER',	PHAXSID_CACHE . DS . 'cache.memcache.php');
	define('PHAXSIC_CACHESQLITEPROVIDER',	PHAXSID_CACHE . DS . 'cache.sqlite.php');

define('PHAXSID_DATABASE',	PHAXSID . DS . 'database');
	define('PHAXSID_DRIVERS',		PHAXSID_DATABASE . DS . 'drivers');
		define('PHAXSIC_MYSQLDRIVER',		PHAXSID_DRIVERS . DS . 'mysql.driver.class.php');
		define('PHAXSIC_SQLITEDRIVER',		PHAXSID_DRIVERS . DS . 'sqlite.driver.class.php');
		define('PHAXSIC_ORACLEDRIVER',		PHAXSID_DRIVERS . DS . 'oracle.driver.class.php');
		define('PHAXSIC_PDO_MYSQLDRIVER',	PHAXSID_DRIVERS . DS . 'pdo_mysql.driver.class.php');
	define('PHAXSID_QUERYBUILDER',	PHAXSID_DATABASE . DS . 'querybuilder');
		define('PHAXSIC_TABLEQUERYBUILDER',	PHAXSID_QUERYBUILDER . DS . 'tablequerybuilder.base.php');
		define('PHAXSIC_TABLEREADER',		PHAXSID_QUERYBUILDER . DS . 'tablereader.class.php');
		define('PHAXSIC_TABLEWRITER',		PHAXSID_QUERYBUILDER . DS . 'tablewriter.class.php');
		define('PHAXSIC_TABLEJOINREADER',	PHAXSID_QUERYBUILDER . DS . 'tablejoinreader.class.php');
		define('PHAXSIC_TABLEPARENTREADER',	PHAXSID_QUERYBUILDER . DS . 'tableparentreader.class.php');
	define('PHAXSIC_DATASOURCE',	PHAXSID_DATABASE . DS . 'datasource.class.php');
	define('PHAXSIC_QUERYRESULT',	PHAXSID_DATABASE . DS . 'queryresult.class.php');
	define('PHAXSIC_DATABASEPROXY',	PHAXSID_DATABASE . DS . 'databaseproxy.class.php');
	define('PHAXSIC_MODEL',			PHAXSID_DATABASE . DS . 'model.base.php');

define('PHAXSID_HELPERS', PHAXSID . DS . 'helpers');
	define('PHAXSIC_HTMLHELPER', 	PHAXSID_HELPERS . DS . 'html.helper.php');
	define('PHAXSIC_TEXTHELPER', 	PHAXSID_HELPERS . DS . 'text.helper.php');
	define('PHAXSIC_FORMHELPER', 	PHAXSID_HELPERS . DS . 'form.helper.php');
	define('PHAXSIC_DATETIMEHELPER',PHAXSID_HELPERS . DS . 'datetime.helper.php');
	define('PHAXSIC_FILTERHELPER',	PHAXSID_HELPERS . DS . 'filter.helper.php');
	define('PHAXSIC_URLHELPER',		PHAXSID_HELPERS . DS . 'url.helper.php');
	define('PHAXSIC_JSONHELPER',	PHAXSID_HELPERS . DS . 'json.helper.php');
	define('PHAXSIC_PATHHELPER',	PHAXSID_HELPERS . DS . 'path.helper.php');
	define('PHAXSIC_REDIRECTHELPER',PHAXSID_HELPERS . DS . 'redirect.helper.php');

define('PHAXSID_LIBRARIES',	PHAXSID . DS . 'libraries');

define('PHAXSID_VALIDATION', PHAXSID . DS . 'validation');
		define('PHAXSIC_VALIDATOR',				PHAXSID_VALIDATION . DS . 'validator.class.php');
		define('PHAXSIC_FILEVALIDATOR',			PHAXSID_VALIDATION . DS . 'filevalidator.class.php');
		define('PHAXSIC_HTMLVALIDATOR', 		PHAXSID_VALIDATION . DS . 'htmlvalidator.class.php');

define('PHAXSID_VIEWS', PHAXSID . DS . 'views');
	define('PHAXSIC_VIEW',			PHAXSID_VIEWS . DS . 'view.class.php');
	define('PHAXSIC_JSONVIEW',		PHAXSID_VIEWS . DS . 'jsonview.class.php');
	define('PHAXSIC_HTMLVIEW',		PHAXSID_VIEWS . DS . 'htmlview.class.php');
	define('PHAXSIC_PROCESSVIEW',	PHAXSID_VIEWS . DS . 'processview.class.php');
	define('PHAXSIC_SHELLVIEW',     PHAXSID_VIEWS . DS . 'shellview.class.php');
	define('PHAXSIC_MAILVIEW',      PHAXSID_VIEWS . DS . 'mailview.class.php');
	define('PHAXSIC_XMLVIEW',		PHAXSID_VIEWS . DS . 'xmlview.class.php');
	define('PHAXSIC_FEEDVIEW',      PHAXSID_VIEWS . DS . 'feedview.class.php');
	define('PHAXSIC_FILEVIEW',      PHAXSID_VIEWS . DS . 'fileview.class.php');

define('PHAXSID_HTML', 	PHAXSID . DS . 'html');
	define('PHAXSIC_HTMLELEMENT',	PHAXSID_HTML . DS . 'htmlelement.class.php');
	define('PHAXSIC_HTMLELEMENTLIST',	PHAXSID_HTML . DS . 'htmlelementlist.class.php');

	define('PHAXSID_ERROR', PHAXSID_HTML . DS . 'errors');
		define('PHAXSI_ERROR_404', PHAXSID_ERROR .DS.'404.doc.php');
		define('PHAXSI_ERROR_400', PHAXSID_ERROR .DS.'400.doc.php');

define('PHAXSID_FORMS',	PHAXSID . DS . 'forms');

	define('PHAXSID_FORMSBASE', PHAXSID_FORMS . DS . 'base');
		define("PHAXSIC_FORM",			 	PHAXSID_FORMSBASE . DS . "form.base.php");
		define('PHAXSIC_FORMELEMENT',	 	PHAXSID_FORMSBASE . DS . "formelement.base.php");
		define("PHAXSIC_FORMINPUT",     	PHAXSID_FORMSBASE . DS . "forminput.base.php");
		define('PHAXSIC_FORMELEMENTLIST',	PHAXSID_FORMSBASE . DS . 'formelementlist.base.php');
		define("PHAXSIC_INPUTCHECKABLE",  	PHAXSID_FORMSBASE . DS . "inputcheckable.class.php");

	define('PHAXSID_FORMCOMPONENTS', 	PHAXSID_FORMS . DS . 'components');
		define('PHAXSIC_INPUTFILE',		PHAXSID_FORMCOMPONENTS . DS . 'inputfile.class.php');
		define('PHAXSIC_INPUTFILEIMAGE',PHAXSID_FORMCOMPONENTS . DS . 'inputfileimage.class.php');
		define('PHAXSIC_INPUTTEXT',		PHAXSID_FORMCOMPONENTS . DS . 'inputtext.class.php');
		define('PHAXSIC_INPUTHIDDEN',	PHAXSID_FORMCOMPONENTS . DS . 'inputhidden.class.php');
