<?php

/**
 * InputFileImage
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

class InputFileImage extends InputFile{

	protected $_thumbs_target = array();
	protected $_saved_thumbs = array();

	function __construct($value = "", $name = null){
		parent::__construct($value, $name);
		$this->setValidator(array());
	}

	function setValidator($options, $messages = array()){

		$mime = array('image/jpeg'  => array('jpg', 'jpeg'),
					  'image/gif'	=> 'gif',
					  'image/png'	=> 'png',
					  'image/jpg'	=> array('jpg', 'jpeg'),
					  'image/pjpeg'	=> array('jpg', 'jpeg'));

		$extension = array('jpg', 'gif', 'png', 'jpeg');

		$options = array_merge(array('mime_types' => $mime, 'extension' => $extension), $options);
		parent::setValidator($options, $messages);

	}

	function validate(){

		if(!parent::validate()){
			return false;
		}

		if(!is_null($this->_validator) && $this->_validator->getOption('required') && !@getimagesize($this->_value['tmp_name'])){
			$this->_validator->setErrorCode('mime_types');
			return false;
		}

		return true;

	}


	/**
	 *
	 * @example
	 * 
	 * $this->createThumbs(
	 * 		array('best',	'[programa]/{random[10]}_{width}x{height}.{ext}'	300, 500),
	 *		array('width',	'[programa]/{random[10]}_width.{ext}',	110),
	 * 		array('square', '[programa]/{random[10]}_square.{ext}',	80),
	 * 		array('crop', 	'[programa]/{random[10]}_crop_{width}x{height}.{ext}',	300, 500),
	 * 		array('height',	'[programa]/{random[10]}_height.{ext}',	180),
	 * 		array('filled', '[programa]/{random[10]}_filled.{ext}',	365),
	 *		array('original','[programa]/{random[10]}.{ext}')
	 * 	);
	 *
	 * @return array
	 */

	function createThumbs(/* Variable arguments */){

		if(!$this->hasFile()){
			return false;
		}

		$thumbs = func_get_args();

		#No thumbs specified
		if(!count($thumbs)){
			return false;
		}

		#Save temporarily the original image in the tmp directory
		$path = APPD_TMP . DS . '{random}.{ext}';
		$source = $this->saveFileAs($path);

		if(!$source){
			trigger_error("Could not create temporary thumbnail.", E_USER_WARNING);
			return false;
		}

		Loader::includeLibrary('phaxsithumb/PhaxsiThumb.php');
		$fk_thumb = new PhaxsiThumb($source);

		if($fk_thumb->isError()){
			@unlink($source);
			return false;
		}

		#Generate filenames if needed. $names will be returned.
		$names = array();

		for($i = 0; $i < count($thumbs); $i++){

			$info = pathinfo($this->_value['name']);

			$replacements = array(
				'size' => isset($thumbs[$i][2])? $thumbs[$i][2] : '',
				'width' => isset($thumbs[$i][2])? $thumbs[$i][2] : '',
				'height' => isset($thumbs[$i][3])? $thumbs[$i][3] : '',
				'name' => $info['filename'],
				'ext' => $info['extension']
			);

			$thumbs[$i][1] = PathHelper::replaceUploadsDir($thumbs[$i][1]);
			$thumbs[$i][1] = PathHelper::parse($thumbs[$i][1], $replacements);

			$dir = dirname($thumbs[$i][1]);

			if(!file_exists($dir)){
				$old = umask(0);
				mkdir($dir, 0777, true);
				umask($old);
			}

			$names[$i] = $thumbs[$i][1];

		}

		$success = call_user_func_array(array(&$fk_thumb, 'batchCreateThumbs'), $thumbs);
		@unlink($source);

		if(!$success){
			return false;
		}

		return $names;

	}

	function setThumbsTarget(array $thumbs){
		$this->_thumbs_target = $thumbs;
	}

	function getThumbsTarget(){
		 return $this->_thumbs_target;
	}

	function getSavedThumbs(){
		return $this->_saved_thumbs;
	}

	function getValue($filtered = true){
		$value = parent::getValue($filtered);
		$this->_saved_thumbs = call_user_func_array(array(&$this, 'createThumbs'), $this->_thumbs_target);
		return $value;
	}

}
