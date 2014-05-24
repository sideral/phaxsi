<?php

/**
 * InputDropDown
 * 

 * Copyright 2008-2012, Alejandro Zuleta (http://phplab.co)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2008-2012, Alejandro Zuleta (http://phplab.co)
 * @link          http://phaxsi.net Phaxsi PHP Framework
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @package       Phaxsi.Forms.Components
 * @since         Phaxsi v 0.1
 */


class InputDropDown extends FormElement {

	protected $_options = array();
	protected $_data_source = array();
	protected $_opt_groups = array();

	function __construct($initial_value = '', $name = null){		
		parent::__construct('select', $initial_value, $name, true);
	}

	function add($value, $text){
		$this->_options[$value] = $text;
	}

	function addToGroup($value, $text, $group){
		if(!isset($this->_opt_groups[$group])){
			$this->_opt_groups[$group] = array();
		}
		$this->_opt_groups[$group][] = array($value, $text);
	}

	function countOptions(){
		return count($this->_options);
	}

	function __toString(){
		
		$options = $this->createHtmlOptions($this->_options);
		
		if(is_array($this->_data_source)){
			$data = $this->_data_source;
		}
		else{
			$data = $this->_data_source->fetchKeyValue();
		}
		
		$options = array_merge($options, $this->createHtmlOptions($data));

		$group_html = "";
		foreach($this->_opt_groups as $group => $items){
			$groups_items = $this->createHtmlOptions($items);
			$group_html .= "<optgroup label=\"$group\">" . join("\r\n", $groups_items). "</optgroup>";
		}

		$this->innerHTML = join("\r\n", $options) .$group_html;

		return parent::__toString();

	}
	
	protected function createHtmlOptions($items){
		$options = array();
		foreach($items as $value => $text){
			if($value != $this->_value)
				$options[] = "<option value=\"".HtmlHelper::escape($value)."\">".HtmlHelper::escape($text)."</option>";
			else
				$options[] = "<option value=\"".HtmlHelper::escape($value)."\" selected='selected'>".HtmlHelper::escape($text)."</option>";
		}
		return $options;		
	}

	function setDataSource($source){
		$this->_data_source = $source;
	}

	function bindDataSource(){

		if(is_array($this->_data_source)){
			$data = $this->_data_source;
		}
		else{
			$data = $this->_data_source->fetchKeyValue();
		}

		foreach($data as $value => $text){
			$this->_options[$value] = $text;
		}

		$this->_data_source = array();

	}

	function bindToGroup($group){

		if(!isset($this->_opt_groups[$group])){
			$this->_opt_groups[$group] = array();
		}

		if(is_array($this->_data_source)){
			$data = $this->_data_source;
		}
		else{
			$data = $this->_data_source->fetchKeyValue();
		}

		foreach($data as $value => $text){
			$this->_opt_groups[$group][] = array($value, $text);
		}

		$this->_data_source = array();
	}

	function setSize($size){
		$this->setAttribute('size', $size);
	}
	
	function getSize(){
		return $this->getAttribute('size');
	}
	
}
