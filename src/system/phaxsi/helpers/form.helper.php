<?php

/**
 * Creates some common form html elements. 
 * 

 * Copyright 2008-2014, Alejandro Zuleta (http://phplab.co)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2008-2014, Alejandro Zuleta (http://phplab.co)
 * @link          http://phaxsi.net Phaxsi PHP Framework
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @package       Phaxsi.Helpers
 * @since         Phaxsi v 0.1
 */

class FormHelper{

	static function label($text, $component = null, $attributes = array()){
		$id = $component ? $component->getId() : "";
		$attributes = HtmlHelper::formatAttributes($attributes);
		return "<label for=\"$id\" $attributes >$text</label>";
	}

	static function errorMessage($message, $name = '', $attributes = array()){

		if(!isset($attributes['class'])){
			$attributes['class'] = 'error-message';
		}

		if(!$message){
			if(!isset($attributes['style'])){
				$attributes['style'] = '';
			}
			$attributes['style'] .= ';display:none;';
		}

		if($name != '' && !isset($attributes['id'])){
			//Warning: This doesn't work with page with multiple forms
			$attributes['id'] = 'error-message-'.$name ;
		}

		$message = HtmlHelper::escape($message);

		$attributes = HtmlHelper::formatAttributes($attributes);
		return "<div $attributes>$message</div>";

	}

	static function componentErrorMessage(IFormComponent $component){
		return self::errorMessage($component->getErrorMessage(), $component->getName());
	}

}
