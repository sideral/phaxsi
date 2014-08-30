<?php

/**
 * Encodes and decodes Json
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

class JsonHelper{

	static function encode($object){
		Loader::includeLibrary('json/json.php');
		$json = new Services_Json();
		return $json->encode($object);
	}

	static function decode($string){
		Loader::includeLibrary('json/json.php');
		$json = new Services_Json();
		return $json->decode($string);
	}

}
