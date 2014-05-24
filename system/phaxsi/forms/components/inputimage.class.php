<?php

/**
 * InputImage
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

class InputImage extends FormInput{ 

	protected $src;

	function __construct($value = "Submit", $name = null){
		parent::__construct('image', $value, $name);
	}

	function setSource($src){
		$this->src = $src;
		return $this;
	}

	function __toString(){
		$this->setAttribute('src', UrlHelper::resource($this->src));
		return parent::__toString();
	}

	function returnsValue(){
		return false;
	}

}
