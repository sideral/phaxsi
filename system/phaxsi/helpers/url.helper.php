<?php

/**
 * Helps with urls.
 * 

 * Copyright 2008-2014, Alejandro Zuleta (http://phplab.co)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2008-2014, Alejandro Zuleta (http://phplab.co)
 * @link          http://phaxsi.net Phaxsi PHP Framework
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @package       Phaxsi.Helpers
 * @since         Phaxsi v 0.1
 */

class UrlHelper{


	/**
	 * Receives a fk path and transforms it into an absolute url
	 *
	 * @param string $path
	 * @return string
	 *
	 * @todo: Manage query strings to replace, append or remove parameters.
	 */
	static function get($path, $with_host = true){
		if($path != '' && $path[0] =='/'){
			$path = substr($path, 1);
		}

		if($with_host){
			return "http://" . AppConfig::HTTP_HOST . AppConfig::BASE_URL . $path;
		}
		else{
			return AppConfig::BASE_URL . $path;
		}

	}

	static function localized($path, $lang = '', $with_host = true){
		if(!$lang){
			$lang = Lang::getCurrent();
		}			

		if($path != ''){
			if($path[0] != '/'){
				$path = '/'.$path;
			}
			if(substr($path, 0, 4) == '/'. $lang.'/'){
				return self::get($path, $with_host);
			}
		}

		if($lang != AppConfig::DEFAULT_LANGUAGE || AppConfig::$language_redirect){
			$path = $lang . $path;
		}
		return self::get($path, $with_host);
	}

	static function referer($remove_host = true){
		$ref = '';
		if(isset($_SERVER['HTTP_REFERER'])){
			if($remove_host){
				$url = parse_url($_SERVER['HTTP_REFERER']);
				if($url['path']){
					$query = isset($url['query'])?'?'.$url['query']:"";
					$ref = substr_replace(strtolower($url['path']), '', 0, strlen(AppConfig::BASE_URL));
					$ref .= $query;
				}
			}
			else{
				$ref = $_SERVER['HTTP_REFERER'];
			}
		}
		return $ref;
	}

	static function current($lang = '', $include_host = true){

		if($include_host){
			$host = "http://" . AppConfig::HTTP_HOST . AppConfig::BASE_URL;
		}
		else{
			$host = '/';
		}

		$uri = preg_replace('/^'.str_replace('/','\/',AppConfig::BASE_URL).'/', '', $_SERVER['REQUEST_URI']);

		if(!$lang){
			return  $host. $uri;
		}
		else if($lang == AppConfig::DEFAULT_LANGUAGE && !AppConfig::$language_redirect){
			if(Lang::getCurrent() != $lang){
				$uri = preg_replace('/^'.Lang::getCurrent().'\//', '', $uri);
				return  $host . $uri;
			}
			else{
				return  $host . $uri;
			}
		}
		else{
			if(Lang::getCurrent() != $lang || AppConfig::$language_redirect){
				$uri = preg_replace('/^'.Lang::getCurrent().'\//', '', $uri);
				return  $host . $lang .'/'. $uri;
			}
			else{
				return  $host . $uri;
			}
		}
	}

	static function currentPath($with_query = true, $extra_query = array()){
		$uri = substr($_SERVER['REQUEST_URI'], strlen(AppConfig::BASE_URL));

		if($with_query){
			if(!$extra_query){
				return '/'.$uri;
			}
			else{
				$values = array();
				parse_str($_SERVER['QUERY_STRING'], $values);
				$values = array_merge($values, $extra_query);
				$new_query = http_build_query($values);
				$parts = explode('?',$uri);
				return '/'.$parts[0].'?'.$new_query;
			}
		}

		$parts = explode('?',$uri);
		return '/'.$parts[0];

	}

	static function resource($path, $module = DEFAULT_MODULE){
		if($path && $path[0] != '/'){
			$path = '/'.$module . '/'.  $path;
		}
		return self::get(APPU_PUBLIC . $path, false);
	}
	
}
