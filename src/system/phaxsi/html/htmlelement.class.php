<?php

/**
 * Base class for programatic html element creation.
 * 

 * Copyright 2008-2014, Alejandro Zuleta (http://phplab.co)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2008-2014, Alejandro Zuleta (http://phplab.co)
 * @link          http://phaxsi.net Phaxsi PHP Framework
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @package       Phaxsi.Html
 * @since         Phaxsi v 0.1
 */

class HtmlElement{

	public $beforeHTML = '';
	public $innerHTML = '';
	public $afterHTML = '';

	protected $_tag_name;
	protected $_can_have_children;
	protected $_attributes = array();
	protected $_inner_string;

	function __construct($tag_name, $id = null, $can_have_children = true){
		$this->_tag_name = $tag_name;
		$this->_attributes["id"] = !is_null($id) ? $id : HtmlHelper::generateId();
		$this->_can_have_children = $can_have_children;
	}

	function getId(){
		return $this->_attributes["id"];
	}

	function setId($id){
		$this->_attributes['id'] = $id;
		return $this;
	}

	function setAttribute($name, $value){
		$this->_attributes[$name] = $value;
		return $this;
	}

	function setClass($name){
		if(isset($this->_attributes['class'])){
			$name .= ' '.$this->_attributes['class'];
		}
		$this->_attributes['class'] = $name;
		return $this;
	}

	function removeClass($name){
		unset($this->_attributes['class']);
		return $this;
	}

	function setAttributes(array $attributes){
		foreach($attributes as $name => $value){
			 $this->_attributes[$name] = $value;
		}
		return $this;
	}

	function getAttribute($name){
		if(isset($this->_attributes[$name])){
			return $this->_attributes[$name];
		}
		else{
			return false;
		}
	}

	function setInnerString($string){
		$this->_inner_string = $string;
		return $this;
	}

	function __toString(){

		$html = $this->beforeHTML;

		$html .= "<$this->_tag_name $this->_inner_string ";

		foreach($this->_attributes as $name => $value){
			$html .= $name . '="' . HtmlHelper::escape($value) . '" ';
		}

		if($this->_can_have_children){
			$html .= ">{$this->innerHTML}</{$this->_tag_name}>\r\n";
		}
		else{
			$html .= "/>\r\n";
		}

		$html .= $this->afterHTML;

		return $html;
	}


}
