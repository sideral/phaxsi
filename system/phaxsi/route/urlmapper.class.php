<?php

/**
 * Maps Urls
 * 

 * Copyright 2008-2012, Alejandro Zuleta (http://phplab.co)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2008-2012, Alejandro Zuleta (http://phplab.co)
 * @link          http://phaxsi.net Phaxsi PHP Framework
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @package       Phaxsi.Route
 * @since         Phaxsi v 0.1
 */

final class UrlMapper {

	function map($request_uri){

		$uri_parts = explode('?',$request_uri);
		$uri = $uri_parts[0];

		/**
		 * Checks if the url has two or more consecutive slashes
		 */
		if(preg_match('/\/{2,}/', $uri)){
			return false;
		}

		$uri = $this->preprocess($uri);
		$uri = $this->apply($uri);

		if(!$uri){
			return false;
		}

		$parts = explode('/', $uri);

		$module = array_shift($parts);
		$action = array_shift($parts);
		$args = array_merge($_GET, $parts);

		return new Context('controller',$module, $action, $args);

	}

	private function apply($uri){

		$fail_route = "";

		if(isset(AppConfig::$url_map['{failure}'])){
			$fail_route = AppConfig::$url_map['{failure}'];
			unset(AppConfig::$url_map['{failure}']);
		}

		$exp = array_merge(
			array(
				'module' =>  '[a-z](?:_?[a-z0-9]+)*',
				'action' => '[a-z](?:_?[a-z0-9]+)*(?:_(?:process|json|feed|mail|xml|file))?',
				'args'   => '[a-z0-9_-]+(?:/[a-z0-9_-]+)*'
			),
			AppConfig::$url_regexp
		);

		$new_uri = '';

		foreach(AppConfig::$url_map as $pattern => $replacement){
			$pattern = str_replace(
				array('{module}',	  '{action}',	  '{args}',		'/'),
				array($exp['module'], $exp['action'], $exp['args'], '\/'),
				$pattern
			);

			$pattern = '/^'.$pattern.'$/D';
			$replacement = str_replace('{default}', DEFAULT_MODULE, $replacement);

			$count = 0;
			$replaced = preg_replace($pattern, $replacement, $uri, 1, $count);
			if($count){
				$new_uri = $replaced;
				break;
			}
		}

		if(!$new_uri || strstr($new_uri, '/') === false){

			$default_routes = array(
				'' => DEFAULT_MODULE.'/'.DEFAULT_MODULE,
				$exp['module'] => '$0/$0',
				$exp['module'].'/'.$exp['action'] => '$0',
				$exp['module'] .'/'.$exp['action'].'/'.$exp['args'] => '$0'
			);

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