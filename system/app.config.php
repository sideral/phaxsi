<?php

/**
 * The main configuration class. All global settings of your application
 * will be done here
 */
class AppConfig{

	static $vars = array();

	/**
	 * The domain name, including www, i.e. www.example.com
	 * Phaxsi will put this host in all dinamically generated uris.
	 */
	const HTTP_HOST = 'localhost';

	/**
	 * If $_REQUEST['HTTP_HOST'] and AppConfig::HTTP_HOST don't coincide,
	 * should the url be redirected to the new domain?
	 *
	 * A website can often be visited from http://www.example.org but also
	 * from http://example.org. For SEO purposes it is often a good idea to
	 * have a unique url. Setting this to true, will force Phaxsi to make a
	 * permanent redirection to the one host specified in HTTP_HOST (see above)
	 */
	const REDIRECT_IF_DIFFERENT_HOST = true;

	/**
	 * The base path of your application. You need to modify this
	 * only if you intend to run your website from a subdirectory
	 * of the URL. All dinamically generated URIS will be appended
	 * this path.
	 */
	const BASE_URL = '/';

	/**
	 * The title of the website. It may be put in the browser's title bar.
	 */
	const TITLE = 'New Website';


	/**
	 * Default page character set.
	 */
	const CHARSET = 'utf-8';

	const JS_LIBRARY = 'http://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js';

	/**
	 * Enable global output buffering;
	 */
	const OUTPUT_BUFFERING_ENABLED = false;

	/**
	 * Globally enable or disable cache.
	 * If this is set to false, there will be no cache hits.
	 */
	const CACHE_ENABLED = true;

	/**
	 * 	The name of the cache provider that will be used by default.
	 *  Possible values are: file, memcache, sqlite*
	 */
	const DEFAULT_CACHE_PROVIDER = 'file';

	/**
	 * Set this to true to ease development.
	 * But don't forget to turn it back to false in production
	 *
	 */
	const DEBUG_MODE = true;

	/**
	 * The timezone used by the date function.
	 */
	const DEFAULT_TIMEZONE = "UTC";

	const DEFAULT_LANGUAGE = 'en';
	static $available_languages = array('en');
	static $generic_error_message = array('en' => 'This field is not valid');
	static $language_redirect = false;

	/**
	 * Plugin configuration. 'enabled' and 'name' are required for all plugins
	 * @var array
	 */
	static $plugins = array(
		'phaxsi/error'	=> array('enabled' => true),
		'phaxsi/console'=> array('enabled' => true),
		'phaxsi/trace'	=> array('enabled' => false)
	);

	static $modules = array();

	static $database = array(
		'default' => array(
			'driver' => 'pdo_mysql',
			'host' => 'localhost',
			'user' => 'db_user',
			'password' => 'db_password',
			'name' => 'db_name'
		)
	);

	
	const CUSTOM_ROUTER = 'phaxsi';
	
	/**
	 * This array contains pairs of patterns => replacements for urls.
	 * 
	 * For instance, if you want all requests to '/blog/345' to become '/blog/posts/345', then you 
	 * should add the map
	 * 
	 * 'blog/[0-9]+' => 'blog/posts/$0'
	 * 
	 * in which the key is a regular expression and the second is a string used for replacement 
	 * compatible with preg_match function.
	 * 
	 * @var array
	 */
	static $url_map = array();
	
	/**
	 * An array with validation expressions for the url parts. Leave blank for defaults.
	 * 
	 * This is used to validate the characters that are introduced in the url. For instance, you
	 * might not want to have hyphens (-) in the arguments of your site. The default regex for
	 * args is: /[a-z0-9_-]+(?:/[a-z0-9_-]+)* /. This expression accepts alphanumeric strings with 
	 * hyphens and underscores as valid argumenets. To disallow hyphens, You can change the default
	 * url by setting: 
	 * 
	 * $url_regexp = array(
	 *		'args' => '[a-z0-9_]+(?:/[a-z0-9_]+)*'
	 * );
	 * 
	 * Notice that we removed the hyphen from the expression. We also used the special key 'args'.
	 * 
	 * The keys that can be used are:
	 * 
	 * 'module': The url part that represents the module.
	 * 'action': The url part that represents the action.
	 * 'args': The url part that represents the arguments.
	 * 
	 * @see \Context for an extended explanation of these parts.
	 * 
	 * @var array 
	 */
	static $url_regexp = array();
	
}