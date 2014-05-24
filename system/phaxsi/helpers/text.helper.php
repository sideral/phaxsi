<?php

/**
 * Helps with text.
 * 

 * Copyright 2008-2012, Alejandro Zuleta (http://phplab.co)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2008-2012, Alejandro Zuleta (http://phplab.co)
 * @link          http://phaxsi.net Phaxsi PHP Framework
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @package       Phaxsi.Helpers
 * @since         Phaxsi v 0.1
 */

class TextHelper{
	/**
	 * Cuts a string to an specified number of words
	 * 
	 * @param string $text
	 * @param int $word_count
	 * @param bool $strip_tags
	 * @param string $dots
	 * @return string
	 */
	static function cut($text, $word_count, $strip_tags = true, $dots = '...'){
		if($strip_tags) $text = strip_tags($text);
		$words = preg_split('/\s+/u', $text);
		if(count($words) <= $word_count){
			return implode(' ', $words);
		}
		return implode(' ', array_slice($words, 0, $word_count)) . $dots;
	}

	static function titleCap($text, $limit = -1){
		return preg_replace_callback('/(\b[a-z])/iu',array('TextHelper', 'titleCapCallback'), $text, $limit);
	}

	private static function titleCapCallback($matches){
		return mb_strtoupper($matches[1]);
	}

	static function possessive($text){
		$last_character = mb_substr($text, mb_strlen($text)-1);
		return $text."'" . ($last_character != "s" ? 's' : '');
	}

	static function nonBreakingSpace($text){
		return str_replace(' ', '&nbsp;', $text);
	}
	
	/**
	 * From Wordpress
	 * @param string $title
	 * @return string 
	 */
	static function urlize($title){

		$normalizeChars = array(
			'Š'=>'S', 'š'=>'s', 'Ð'=>'Dj','Ž'=>'Z', 'ž'=>'z', 'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A',
			'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E', 'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I',
			'Ï'=>'I', 'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O', 'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U', 'Ú'=>'U',
			'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss','à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a',
			'å'=>'a', 'æ'=>'a', 'ç'=>'c', 'è'=>'e', 'é'=>'e', 'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i',
			'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ò'=>'o', 'ó'=>'o', 'ô'=>'o', 'õ'=>'o', 'ö'=>'o', 'ø'=>'o', 'ù'=>'u',
			'ú'=>'u', 'û'=>'u', 'ü'=>'u', 'ý'=>'y', 'ý'=>'y', 'þ'=>'b', 'ÿ'=>'y', 'ƒ'=>'f', 'ä'=>'a', 'ë'=>'e'
		);

		$url_title = strtr($title, $normalizeChars);
		$url_title = strtolower(str_replace('&', '-and-', $url_title));
		$url_title = trim(preg_replace('/[^\w\d_ -]/si', '', $url_title));//remove all illegal chars
		$url_title = str_replace(' ', '-', $url_title);
		$url_title = preg_replace('/-{2,}/', '-', $url_title);
		$url_title = trim($url_title, '-');

		if(!$url_title){
			$url_title = substr(md5($title), 0, 15);
		}

		return $url_title;

	}

	static function urlizeUnique($title, $table, $url_field, $extra = null){

		$url_title = self::urlize($title);

		$count = self::urlCountMatches($url_title, $table, $url_field, $extra);
		if($count){
			$count = self::urlCountMatches($url_title.'-%', $table, $url_field, $extra);
			$url_title .= '-'.$count;
			$count = self::urlCountMatches($url_title, $table, $url_field, $extra);
			if($count){
				$url_title .= '-'.substr(md5(mt_rand()),0,2);
				$count = self::urlCountMatches($url_title, $table, $url_field, $extra);
				if($count){
					$url_title .= '-'.md5(mt_rand());
				}
			}
		}

		return $url_title;

	}

	private static function urlCountMatches($url_title, $table, $url_field, $extra){
		$query = new TableReader($table);
		$query->where($url_field, $url_title, 'LIKE');
		if($extra) $query->where($extra[0], $extra[1]);
		return $query->count();
	}

	static function paragraph($text, $attributes = ""){
		$att = HtmlHelper::formatAttributes($attributes);
		return  preg_replace("/\b.+/", '<p '.$att.'>$0</p>', $text);
	}

	static function replaceFirst($search, $replace, $subject){
		$pos = strpos($subject, $search);
		if ($pos !== false) {
			$subject = substr_replace($subject, $replace, $pos, strlen($search));
		}
		return $subject;
	}

}
