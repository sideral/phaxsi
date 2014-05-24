<?php

/**
 * Validates html. This class will be changed to use a more secure method.
 * 

 * Copyright 2008-2014, Alejandro Zuleta (http://phplab.co)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2008-2014, Alejandro Zuleta (http://phplab.co)
 * @link          http://phaxsi.net Phaxsi PHP Framework
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @package       Phaxsi.Validation
 * @since         Phaxsi v 0.1
 */

class HtmlValidator extends Validator {

	public $forbidden = array('script', 'plaintext', 'style', 'title', 'body', 'head', 'iframe', 'meta', 'frameset',  'link', 'layer', 'bgsound', 'input', 'textarea', 'select', 'option', 'html', 'xml', 'frame', 'base', 'form', 'fieldset', 'table', 'th', 'tr', 'tbody','td','thead', 'tfoot');

	public function __contruct($options, $messages = array()){
		$options['client_side_validable'] = false;
		parent::__contruct($options, $messages);
	}

	public function filterHtml($value){

		$value = $this->trim($value);

		if(empty($value)){
			return $value;
		}

		$parts = preg_split("/(<|>)/mi", $value, null, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);

		$in_tag = false;
		$tree = array();
		$current_tag_name = "";

		for($i = 0; $i < count($parts); $i++){

			if($parts[$i] == "<"){
				if(!$in_tag){
					$in_tag = true;
				}
				else{
					break;
				}
				continue;
			}

			if($parts[$i] == ">"){
				if($in_tag){
					$in_tag = false;
				}
				continue;
			}

			if($in_tag){
				$current_tag_name = $this->appendTag($parts[$i], $tree);
			}
			else{
				$this->appendText($parts[$i], $tree, $current_tag_name);
			}

		}

		$value = trim($this->joinTree($tree));

		return $value;

	}

	private function appendTag($tag, &$tree){

		$tag_name = array();
		preg_match('/^[\/]?([a-z]+[1-6]*)/i', $tag, $tag_name);

		if(!isset($tag_name[0]) || !isset($tag_name[1])){
			return "";
		}

		$closing = $tag_name[0] != $tag_name[1];  

		$tag_name = $tag_name[1];

		if(in_array(strtolower($tag_name), $this->forbidden)){
			return strtolower($tag_name);
		}

		$tag = preg_replace('/ mce_[a-z]+ *= *"([^"])*"/ui', "", $tag);

		$attributes = array();
		preg_match_all('/[a-z]+ *= *"([^"]|=)*"/mi', $tag, $attributes);

		$attributes2 = array();
		preg_match_all('/[a-z]+ *= *[^"\' ]+/mi', $tag, $attributes2);

		if($attributes2[0]){
			$attributes[0]  = array_merge($attributes[0], $attributes2[0]);
		}

		$attributes3 = array();
		preg_match_all('/[a-z]+ *= *\'([^\']|=)*\'/mi', $tag, $attributes3);

		if($attributes3[0]){
			$attributes[0]  = array_merge($attributes[0], $attributes3[0]);
		}

		$remainder = trim(str_replace($attributes[0], "", $tag));

		$valid = (trim($remainder, "/ ") == $tag_name) && !($closing && $attributes[0]) && $this->validateAttributes($attributes[0]);  		

		if(strtolower($tag_name) == "embed"){
			$this->cleanFlashScript($attributes[0]);
		}

		if($valid){ 
			$tree[] =  array('tag_name' => strtolower($tag_name), 'attributes' => $attributes[0], 'closing' => $closing);
		}


		return strtolower($tag_name);

	}

	private function cleanFlashScript(&$attributes){
		$put = false;

		foreach($attributes as &$att){
			if(preg_match('/allowscriptaccess/im', $att)){
				$att = "AllowScriptAccess=\"never\"";
				$put = true;
			}
		}

		if(!$put){
			$attributes[] = "AllowScriptAccess=\"never\"";
		}

	}

	private function joinTree($tree){

		$html = "";

		foreach($tree as $data){

			if($data['tag_name']){	

				if($data['closing']){			
					$html .= "</".$data['tag_name'] .">";
					continue;	
				}

				$html .= "<".$data['tag_name'];

				foreach($data['attributes'] as $attr){
					$html .= " $attr ";
				}

				$html .= ">";

			}
			else{
				$html .= $data['text'];
			}

		}

		return $html;

	}

	private function appendText($part, &$tree, $current_tag_name){

		if($current_tag_name == "script" || $current_tag_name == "style"){
			return;
		}

		$tree[] = array('tag_name' => "",
						'into_tag' => $current_tag_name,
						'text' => preg_replace("(<|>)", "", $part));
	}

	private function validateAttributes(&$attrs){

		foreach($attrs as &$att){

			if(preg_match("/&#/m", $att)){
				return false;
			}

			if(preg_match('/\\00/m', $att)){
				return false;
			}

			if(preg_match('/(j[^a-z]*a[^a-z]*v[^a-z]*a[^a-z]*|v[^a-z]*b[^a-z]*)s[^a-z]*c[^a-z]*r[^a-z]*i[^a-z]*p[^a-z]*t/mi', $att)){
				return false;
			}

			if(preg_match('/e[^a-z]*x[^a-z]*p[^a-z]*r[^a-z]*e[^a-z]*s[^a-z]*s[^a-z]*i[^a-z]*o[^a-z]*n/mi', $att)){
				return false;
			}

			if(preg_match('/[^a-z]+on.{1,15}=/mi', $att)){
				$att = "";
			}

			if(preg_match('/position *: *fixed/mi', $att)){
				$att = "";
			}

			if(preg_match('/data *: */mi', $att)){
				$att = "";
			}

			if(preg_match('/^on.{0,15}=/mi', $att)){
				$att = "";
			}

			if(preg_match('/^id *=/mi', $att)){
				$att = "";
			}

			if(preg_match('/class *=/mi', $att)){
				$att = "";
			}

			if(preg_match('/float *: *(left|right)/mi', $att)){
				$att = "";
			}

		}

		return true;

	}

	private function trim($value){

		$value = trim($value);

		$content = strip_tags($value);

		$content_wo_spaces = preg_replace('/(\s|&nbsp;)/', "", $content);

		if(!$content_wo_spaces){
			if(!preg_match("/(<img |<embed |<object )/i", $value)){
				return "";
			}
			else{
				$value = strip_tags($value, "<img><br><embed><object>");
			}
		}

		$value = preg_replace('/(\r\n|\r|\n)+/mi', "", $value);

		$value = preg_replace('/^(<br[\s]*>|<br[\s]*\/>)+/mi', "", $value);
		$value = preg_replace('/(<br[\s]*>|<br[\s]*\/>)+$/mi', "", $value);
		$value = preg_replace('/(<br[\s]*>|<br[\s]*\/>){4,}/i', " ", $value);

		$value = preg_replace('/\s|&nbsp;/', " ", $value);
		$value = preg_replace('/ {2,}/', " ", $value);

		return $value;

	}

}
