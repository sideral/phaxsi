<?php

/**
 * InputCheckboxList
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


class InputCheckboxList extends FormElementList{

	protected $_initial_values;
	protected $_label;
	protected $checked_value = '1';

	function __construct($initial_values = null){
		$this->_initial_values = $initial_values;			
	}

	function add($list_id, $label = ''){

		$new_input = $this->createAndAdd('checkbox', null, $label, $list_id);

		//Values were received
		if(is_array($this->_raw_value)){
			if(isset($this->_raw_value[$list_id])
				&& $this->_raw_value[$list_id] == $this->checked_value){
				$new_input->setRawValue($this->checked_value);
			}
		}
		//setValue was not called
		else if(!is_null($this->_initial_values)){
			if(in_array((string)$list_id, $this->_initial_values)){
				$new_input->setRawValue($this->checked_value);
			}
		}

		return $new_input;

	}

	function setValue($value){
		if(is_array($value)){
			foreach($this->_elements as $list_id => $input){
				$input->uncheck();
				if(in_array($list_id, $value)){
					$input->check();
				}
			}
		}
	}

	function getRawValue(){
		$values = array();

		foreach($this->_elements as $list_id => $input){
			if($input->isChecked()){
				$values[$list_id] = $input->getRawValue();
			}
		}

		return $values;
	}

	function getValue($filtered = true){

		$values = array();

		foreach($this->_elements as $index => $input){
			if($input->isChecked()){
				$value = $input->getValue($filtered);
				if($this->_filter && $filtered){
					$value = call_user_func($this->_filter, $value, $this->getName());
				}
				$values[] = $index;
			}
		}

		return $values;

	}


	function setDataSource($data_source){
		$this->_data_source = $data_source;

		$values = is_array($data_source)? $data_source : $data_source->fetchKeyValue();
		foreach($values as $value =>$label){
			$this->add($value, $label);
		}
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
			$this->setLabel($label_text, $attributes);
		}
		return $this->_label;
	}

	function __toString(){
		$html = "<div class='form_input_checkboxlist'>\r\n";
		$html .= $this->toString();
		$html .= '</div>';
		return $html;
	}

}
