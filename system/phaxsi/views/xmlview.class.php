<?php

/**
 * View for controllers that return xml.
 * 

 * Copyright 2008-2014, Alejandro Zuleta (http://phplab.co)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2008-2014, Alejandro Zuleta (http://phplab.co)
 * @link          http://phaxsi.net Phaxsi PHP Framework
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @package       Phaxsi.Views
 * @since         Phaxsi v 0.1
 */

class XmlView extends View{

	private $xml;

	function __construct($context){
		$action_parts = explode('_', $context->getAction() );
		$root = $action_parts[0];
		$this->createXml($root);
		parent::__construct($context);
	}

	function render(){
		header('Content-Type: application/xml');
		//headers??
		return $this->xml->asXML();
	}

	function getXml(){
		return $this->xml;
	}

	function loadFile($path){
		return $this->xml = simplexml_load_file($path);
	}

	function createXml($root){
		$data = '<?xml version="1.0" encoding="'.AppConfig::CHARSET.'"?>';
		$data .= "<$root></$root>";
		return $this->xml = new SimpleXMLElement($data);
	}

}

