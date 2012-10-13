<?php

/**
 * InputRadioList
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
 * @package       Phaxsi.Forms.Components
 * @since         Phaxsi v 0.1
 */


class InputRadioList extends FormElementList {

	protected $_label;

	function add($value, $label = ''){

		$new_input = $this->createComponent('radio', $value);

		$new_input->setName($this->_name);
		$new_input->setLabel($label);

		$this->_elements[$value] = $new_input;

		if($this->_raw_value == (string)$value){
			$new_input->check();
		}

		return $new_input;

	}

	function setRawValue($value){
		$this->_raw_value = (string)$value;
	}

	function setValue($value){
		foreach($this->_elements as $element){
			$element->uncheck();
		}

		if(isset($this->_elements[$value])){
			$this->_elements[$value]->check();
		}
	}

	function getRawValue(){
		$value = '';
		foreach($this->_elements as $input){
			if($input->isChecked()){
				$value = $input->getRawValue();
				break;
			}
		}
		return $value;
	}

	function getValue($filtered = true){

		$value = $this->getRawValue();

		if($this->_filter && $filtered){
			$value = call_user_func($this->_filter, $value, $this->getName());
		}

		return $value;

	}

	function setDataSource($data_source){
		$this->_data_source = $data_source;

		if(is_object($data_source)){
			$values = $data_source->fetchKeyValue();
		}
		else{
			$values = $data_source;
		}		

		foreach($values as $id =>$label){
			$this->add($id, $label);
		}
	}

	function validate(){

		$valid = true;
		if(!is_null($this->_validator)){
			$valid = $this->_validator->validate($this->getRawValue());
		}

		return $valid;

	}

	protected function getClientValidationConfig(){
		return array();
	}

	function setLabel($label_text, $attributes = array()){
		$label = new HtmlElement('label', null, true);
		$label->setAttribute('class', 'phaxsi_label');
		$label->setAttributes($attributes);
		$label->innerHTML = $label_text;
		$this->_label = $label;
	}

	function getLabel($label_text = '', $attributes = array()){
		if(!$this->_label || $label_text){
			$this->setLabel($label_text);
		}
		if($this->_label){
			$this->_label->setAttributes($attributes);
		}
		return $this->_label;
	}

	function __toString(){
		$html = "<div class='form_input_radiolist'>\r\n";
		$html .= $this->toString();
		$html .= '</div>';
		return $html;
	}

}
