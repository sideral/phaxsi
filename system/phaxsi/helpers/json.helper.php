<?php

/**
 * Encodes and decodes Json
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
