<?php

/**
 * Reads the input url and creates a Context to route the request.
 * 
 * The URLs that Phaxsi expect are always of the form: /$module/$action/[$arguments].
 * 
 * $module and $action are always required, and $arguments is optional and there can be many of
 * them, separated by /.
 * 
 * Of course, actual URLs are of any form, including just '/' for the main page of a website. The 
 * UrlMapper's purpose is to convert the normal urls to complete urls accepted by phaxsi.
 * 
 * For instance, the '/' url will be converted to 'index/index' in the default settings. A url 
 * such as 'category' will be converted to 'category/category', as in Phaxsi the default action
 * name is the same as the module name.
 * 
 *
 * @copyright     Copyright 2008-2014, Alejandro Zuleta (http://phplab.co)
 * @link          http://phaxsi.net Phaxsi PHP Framework
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @package       Phaxsi.Route
 * @since         Phaxsi v 0.1
 */

final class UrlMapper {
	
	/**
	 * Receives the url of the request and creates a Context.
	 * 
	 * @see \Context
	 * @param string $request_uri The url of the request.
	 * @return boolean|\Context
	 */
	function map($request_uri){
		
		#Separate path from parameters.
		$uri_parts = explode('?',$request_uri);
		$uri = $uri_parts[0];

		#Checks if the url has two or more consecutive slashes.
		if(preg_match('/\/{2,}/', $uri)){
			return false;
		}
		
		$uri = $this->preprocess($uri);
		$uri = $this->apply($uri);

		if(!$uri){
			return false;
		}
		
		#Gets the different parts of the URL.
		$parts = explode('/', $uri);

		$module = array_shift($parts);
		$action = array_shift($parts);
		$args = array_merge($_GET, $parts);

		return new Context('controller',$module, $action, $args);

	}
	
	/**
	 * 
	 * 
	 * @param string $uri
	 * @return string
	 */
	private function apply($uri){

		$fail_route = "";
		#Gets the '{failure}' route in the AppConfig file. This route is used when it is not
		#possible to parse the url correctly.
		if(isset(AppConfig::$url_map['{failure}'])){
			$fail_route = AppConfig::$url_map['{failure}'];
			unset(AppConfig::$url_map['{failure}']);
		}
		
		#Merges the default expressions for url parts with the custom ones in AppConfig.
		$exp = array_merge(
			array(
				'module' =>  '[a-z](?:_?[a-z0-9]+)*',
				'action' => '[a-z](?:_?[a-z0-9]+)*(?:_(?:process|json|feed|mail|xml|file))?',
				'args'   => '[a-z0-9_-]+(?:/[a-z0-9_-]+)*'
			),
			AppConfig::$url_regexp
		);

		$new_uri = '';
		#Reads all maps and performs replacements.
		foreach(AppConfig::$url_map as $pattern => $replacement){
			#Replace the special values in the map with their corresponding regular expressions.
			$pattern = str_replace(
				array('{module}',	  '{action}',	  '{args}',		'/'),
				array($exp['module'], $exp['action'], $exp['args'], '\/'),
				$pattern
			);
			
			#Add the regex wrapper expected by PHP.
			$pattern = '/^'.$pattern.'$/D';
			#Replace the special value {default} with the name of the default module.
			$replacement = str_replace('{default}', DEFAULT_MODULE, $replacement);

			$count = 0;
			#Replace by regex one url by another according to the map.
			$replaced = preg_replace($pattern, $replacement, $uri, 1, $count);
			if($count){
				$new_uri = $replaced;
				break;
			}
		}
		
		#If no maps have been matched, do default replacements in the url.
		if(!$new_uri || strstr($new_uri, '/') === false){
			#These are the routes that Phaxsi assume.
			$default_routes = array(
				'' => DEFAULT_MODULE.'/'.DEFAULT_MODULE,
				$exp['module'] => '$0/$0',
				$exp['module'].'/'.$exp['action'] => '$0',
				$exp['module'] .'/'.$exp['action'].'/'.$exp['args'] => '$0'
			);
			
			#Replace the url with the one expected by Phaxsi.
			foreach($default_routes as $pattern => $replacement){
				$pattern = '/^'.str_replace('/','\/',$pattern).'$/D';
				$count = 0;

				$replaced = preg_replace($pattern, $replacement, $uri, 1, $count);
				if($count){
					$new_uri = $replaced;
					break;
				}
			}

		}

		if(!$new_uri){
			return $fail_route;
		}

		return $new_uri;
	}
	
	/**
	 * Removes url parts that may have special meaning to the framework.
	 * 
	 * @param string $uri
	 * @return string
	 */
	private function preprocess($uri){
		/**
		 * Removes BASE_URL from $uri, including the initial '/'.
		 */
		$uri = substr_replace(strtolower($uri), '', 0, strlen(AppConfig::BASE_URL));

		$parts = explode('/',$uri);

		/**
		 * Detects if there is a language specified in the url,
		 * and if so, sets the context language. $parts[0] always exist
		 * given the returning values of explode function.
		 */

		if(@in_array($parts[0], AppConfig::$available_languages, true)
			&& ($parts[0] != AppConfig::DEFAULT_LANGUAGE || AppConfig::$language_redirect)){
			Lang::setCurrent(array_shift($parts));
		}

		setlocale(LC_ALL, Lang::getCurrent());

		/**
		 * Removes any trailing slash as efficiently as I could do it!
		 */
		if(end($parts)===''){
			array_pop($parts);
		}

		return implode('/',$parts);

	}

}