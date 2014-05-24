<?php

/**
 * The base class for i18n.
 * 

 * Copyright 2008-2012, Alejandro Zuleta (http://phplab.co)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2008-2012, Alejandro Zuleta (http://phplab.co)
 * @link          http://phaxsi.net Phaxsi PHP Framework
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @package       Phaxsi.Core
 * @since         Phaxsi v 0.1
 */

class Lang{

	protected $context;
	protected $load;
	protected $translation_table = array();

	function __construct($context, $load){
		$this->context = $context;
		$this->load = $load;
	}

	private static $_current_language = AppConfig::DEFAULT_LANGUAGE;
	private static $_language_set = false;

	static function getCurrent(){
		return self::$_current_language;
	}

	static function setCurrent($lang){
		self::$_current_language = $lang;
		self::$_language_set = true;
	}

	static function wasSet(){
		return self::$_language_set;
	}

	final function get($path){
		return $this->load->lang($path);
	}

	final function tr($term){
		if(isset($this->translation_table[$term])){
			return $this->translation_table[$term];
		}
		return $term;
	}

	/*
	 * From: http://www.php.net/manual/en/function.http-negotiate-language.php#86787
	 */
	static function autoDetect() {

		$available_languages = AppConfig::$available_languages;
		$http_accept_language = $_SERVER['HTTP_ACCEPT_LANGUAGE'];

		// standard  for HTTP_ACCEPT_LANGUAGE is defined under
		// http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html#sec14.4
		// pattern to find is therefore something like this:
		//    1#( language-range [ ";" "q" "=" qvalue ] )
		// where:
		//    language-range  = ( ( 1*8ALPHA *( "-" 1*8ALPHA ) ) | "*" )
		//    qvalue         = ( "0" [ "." 0*3DIGIT ] )
		//            | ( "1" [ "." 0*3("0") ] )
		preg_match_all("/([[:alpha:]]{1,8})(-([[:alpha:]|-]{1,8}))?" .
					   "(\s*;\s*q\s*=\s*(1\.0{0,3}|0\.\d{0,3}))?\s*(,|$)/i",
					   $http_accept_language, $hits, PREG_SET_ORDER);

		// default language (in case of no hits) is the first in the array
		$bestlang = $available_languages[0];
		$bestqval = 0;

		foreach ($hits as $arr) {
			// read data from the array of this hit
			$langprefix = strtolower ($arr[1]);
			if (!empty($arr[3])) {
				$langrange = strtolower ($arr[3]);
				$language = $langprefix . "-" . $langrange;
			}
			else $language = $langprefix;
			$qvalue = 1.0;
			if (!empty($arr[5])) $qvalue = floatval($arr[5]);

			// find q-maximal language
			if (in_array($language,$available_languages) && ($qvalue > $bestqval)) {
				$bestlang = $language;
				$bestqval = $qvalue;
			}
			// if no direct hit, try the prefix only but decrease q-value by 10% (as http_negotiate_language does)
			else if (in_array($langprefix,$available_languages) && (($qvalue*0.9) > $bestqval)) {
				$bestlang = $langprefix;
				$bestqval = $qvalue*0.9;
			}
		}
		return $bestlang;
	}


}
