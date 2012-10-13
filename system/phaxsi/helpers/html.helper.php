<?php

/**
 * Creates some common html elements.
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

require_once(PHAXSIC_URLHELPER);

class HtmlHelper{

	private $module;

	function __construct(Context $context = null){
		if(!$context){
			$context = new Context('helper', 'phaxsi');
		}
		$this->module = $context->getModule();
	}

	function css($path, $media = 'screen'){
		$url = UrlHelper::resource($path, $this->module);
		return "<link rel='stylesheet' type='text/css' href='$url' media='$media' />";
	}

	private static $_loaded_javascript = array();
	
	function javascript($path, $allow_duplicate = false){
		$url = UrlHelper::resource($path, $this->module);
		if(!$allow_duplicate && isset(self::$_loaded_javascript[$url])){
			return "";
		}
		self::$_loaded_javascript[$url] = true;
		return "<script type=\"text/javascript\" src=\"$url\"></script>";
	}

	function link($text, $path, $attributes = array(), $escape = true){
		$attributes = self::formatAttributes($attributes);
		if($escape) $text = self::escape($text);
		if($path && $path[0] != '/'){
			$path = $this->module . '/'. $path;
		}
		return "<a href=\"".UrlHelper::get($path)."\" $attributes >$text</a>";
	}

	function langLink($text, $path, $attributes = array(), $escape = true){
		$attributes = self::formatAttributes($attributes);
		if($path && $path[0] != '/')	$path = $this->module . '/'. $path;
		if($escape) $text = self::escape($text);
		return "<a href=\"".UrlHelper::localized($path)."\" $attributes >$text</a>";
	}

	/**
	 * Creates the markup required for a local image.
	 *
	 * @param string $text
	 * @param string $attributes
	 * @return string
	 * 
	 */
	function img($path, $alt = '', $attributes = array()){
		$attributes = self::formatAttributes($attributes);
		$alt = self::escape($alt);
		$url = UrlHelper::resource($path, $this->module);
		return "<img src=\"$url\" alt=\"$alt\" $attributes/>";
	}

	function langImg($path, $alt = '', $attributes = array()){
		$parts = explode('/', $path, 2);
		if(isset($parts[1])){
			$parts[2] = $parts[1];
			$parts[1] = Lang::getCurrent();
		}
		$path = implode('/',$parts);
		return $this->img($path, $alt, $attributes);
	}

	static function absoluteImg($src, $alt = '', $attributes = array()){
		$src = self::escape($src);
		$alt = self::escape($alt);
		$attributes = self::formatAttributes($attributes);
		return "<img src=\"$src\" alt=\"$alt\" $attributes/>";
	}

	static function absoluteLink($text, $href, $attributes = array(), $escape = true){
		$href = self::escape($href);
		if($escape) $text = self::escape($text);
		$attributes = self::formatAttributes($attributes);
		return "<a href=\"$href\" $attributes>$text</a>";
	}
	
	static function hashLink($text, $href, $attributes = array(), $escape = true){
		if($href && $href[0] != '#'){
			$href = '#'.$href;
		}
		$href = UrlHelper::current().$href;
		return self::absoluteLink($text, $href, $attributes, $escape);
	}

	static function publicLink($text, $href, $attributes = array(), $escape = true){
		return self::absoluteLink($text, UrlHelper::resource($href), $attributes, $escape);
	}

	/**
	 * Utility method, useful for testing purposes. 
	 * Prints an array within <pre> tags.
	 *
	 * @param unknown_type $array
	 */

	static function pre($array){
		$string =  "<pre>";
		$string .= self::escape(print_r($array, true));
		return $string . "</pre>";		
	}

	private static $id_count = 0;
	static function generateId(){
		$id_num = ++self::$id_count;
		return "element_{$id_num}";
	}

	static function inlineJavascript($code){
		return "<script type=\"text/javascript\">//<![CDATA[\r\n".$code."\r\n//]]></script>\r\n";
	}

	/**
	 * Shortcut to htmlentities
	 *
	 * @param string $text
	 * @return string
	 */
	static function escape($text, $quotes = ENT_QUOTES){
		return htmlspecialchars($text, $quotes, AppConfig::CHARSET);
	}

	static function formatAttributes($attributes){
		$str = "";
		if(is_array($attributes)){
			foreach ($attributes as $key => $value){
				$str .= " $key=\"".self::escape($value).'"';
			}
		}
		else{
			$str = (string)$attributes;
		}
		return $str;
	}


}
