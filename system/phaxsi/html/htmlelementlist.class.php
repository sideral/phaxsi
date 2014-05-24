<?php

/**
 * Base class for programatic html element lists creation.
 * 

 * Copyright 2008-2012, Alejandro Zuleta (http://phplab.co)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2008-2012, Alejandro Zuleta (http://phplab.co)
 * @link          http://phaxsi.net Phaxsi PHP Framework
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @package       Phaxsi.Html
 * @since         Phaxsi v 0.1
 */

/**
 * Base class for lists of html elements, this makes possible for all 
 * the classes that inherit, to use direct access to its elements by 
 * means of ArrayAccess, as well as Iterator behavior.
 *
 */

class HtmlElementList implements Iterator, ArrayAccess{

	protected $_elements = array();

	/**
	 * Adds an element to the collection
	 *
	 * @param HtmlElement $element
	 */
	function addElement(HtmlElement $element){
		$this->_elements[] = $element;		
	}

	function countElements(){
		return count($this->_elements);
	}

	/* Begin Iterator implementation */

	public function rewind() {
	   reset($this->_elements);
	}

	public function current() {
	   return current($this->_elements);
	}

	public function key() {
	   return key($this->_elements);
	}

	public function next() {
	   return next($this->_elements);
	}

	public function valid() {
	   return $this->current() !== false;
	}

	/* End Iterator implementation */

	/* Begin ArrayAccess implementation */ 

	public function offsetExists($offset){
		return isset($this->_elements[$offset]);
	}

	public function offsetGet($offset){
		return $this->_elements[$offset];
	}

	public function offsetSet($offset, $value){
		$this->_elements[$offset] = $value;
	}

	public function offsetUnset($offset){
		unset($this->_elements[$offset]);
	}

	/* End ArrayAccess implementation */

	/**
	 * Converts the elements to html and joins them on a <br> separated
	 * list
	 * @todo Make this method more powerful!
	 * @return string
	 */
	function __toString(){

		$html = '';

		foreach($this->_elements as $element){
			$html .= $element->__toString() . "<br/>\r\n";        		
		}

		return $html;

	}

}
