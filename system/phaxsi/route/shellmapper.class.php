<?php

/**
 * Maps Shell commands
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

final class ShellMapper{

	function map($argv){

		if(!isset($argv[1])){
			return false;
		}

		$parts = $this->preprocess($argv[1]);

		if(!isset($parts[0]) || !isset($parts[1]))
			return false;

		$argv_args = array_slice($argv, 2);

		$args = array();
		foreach($argv_args as $arg){
			if($arg[0] == '-' && strlen($arg)>=2){
				if($arg[1] == '-'){
					$arg = substr($arg, 1);
				}
				$split = explode('=', $arg);
				if(isset($split[1]) && $split[1] != ''){
					$args[substr($split[0],1)] = $split[1];
				}
				else{
					$args[] = $arg;
				}
			}
			else{
				$args[] = $arg;
			}
		}

		$args =  array_merge(array_slice($parts, 2), $args);

		return new Context('shell',$parts[0], $parts[1], $args);

	}

	protected function preprocess($uri){

		$parts = explode('/',$uri);

		setlocale(LC_ALL, Lang::getCurrent());

		/**
		 * Removes any trailing slash as efficiently as I could do it!
		 */
		if(end($parts)===''){
			array_pop($parts);
		}

		return $parts;

	}

}
