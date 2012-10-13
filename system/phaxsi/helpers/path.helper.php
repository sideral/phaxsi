<?php

/**
 * Transform special path strings into real paths.
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
 * @package       Phaxsi.Helpers
 * @since         Phaxsi v 0.1
 */

class PathHelper{

	static function parse($path, $replacements = array(), &$substitutions = array()){

		$path = self::replaceUploadsDir($path);

		$matches1 = array();

		preg_match_all(
			'/{(appd|uploads|public|random|date|root)(?:\[([0-9a-zA-Z-_]+)\])?}/',
			$path, $matches1, PREG_SET_ORDER
		);

		$matches2 = array();
		$expression = implode('|', array_keys($replacements));
		preg_match_all(
				'/{('.$expression.')(?:\[([0-9a-zA-Z-_]+)\])?}/', $path, $matches2, PREG_SET_ORDER
		);

		$matches = array_merge($matches1, $matches2);

		foreach($matches as $match){

			if(isset($replacements[$match[1]])){

				$substitutions[$match[0]] = $replacements[$match[1]];

				if(isset($match[2])){

					if(is_numeric($match[2]))
						$substitutions[$match[0]] = substr($substitutions[$match[0]], 0, $match[2]);
					elseif(is_callable($match[2])){
						$substitutions[$match[0]] = call_user_func($match[2], $substitutions[$match[0]]);
					}
					elseif($match[2] == 'url'){
						$substitutions[$match[0]] = TextHelper::urlize($substitutions[$match[0]]);
					}
				}

			}
			else{
				$param = isset($match[2])? $match[2] : null;
				$substitutions[$match[0]] = self::replace($match[1], $param);
			}

		}

		return str_replace(array_keys($substitutions), array_values($substitutions), $path);

	}

	static function replaceUploadsDir($path){
		return preg_replace('/^\[([a-zA-Z0-9]+)\]$/', '{uploads[$1]}/{name}.{ext}', $path);
	}

	static function join(array $elements, $separator = DS){

		$new_elements = array();

		foreach($elements as $element){
			$el = trim($element, $separator);
			if($el != ''){
				$new_elements[] = $el;
			}
		}

		return join($separator, $new_elements);

	}

	private static function replace($id, $param){

		$replace = '';
		switch($id){
			case 'random':
				$replace = sha1(mt_rand());
				if(!is_null($param))
					$replace = substr($replace, 0, $param);
				break;
			case 'date':
				$replace = date('dmy');
				if(!is_null($param)){
					$replace = date($param);
				}
				break;
			case 'public':
				$replace = APPD_PUBLIC;
				break;
			case 'uploads':
				$replace = 'uploads';
				if(!is_null($param)){ 
					$replace = APPD_PUBLIC . DS . $param . DS . 'uploads';
				}
				break;
			case 'appd':
				$replace = APPD;
				break;
			case 'root':
				$replace = $_SERVER['DOCUMENT_ROOT'];
				break;
		}

		return $replace;


	}


}
