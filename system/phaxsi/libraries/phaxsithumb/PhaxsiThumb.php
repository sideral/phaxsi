<?php

/**
 * Thumbnail Generator
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
 * @package       Phaxsi.Library
 * @since         Phaxsi v 0.1
 */

class PhaxsiThumb{

	private $source;
	private $width;
	private $height;
	private $type;

	private $source_error = true;

	private $create_image_function;
	private $save_image_function;
	private $prepare_image_function;

	function __construct($source){
		 $this->setSourceFile($source);	
	}

	function isError(){
		return $this->source_error;
	}

	function setSourceFile($source){

		$info = @getimagesize($source);

		if(!$info || !$info[0]){
			$this->source_error = true;
			return false;
		}

		if(!$this->setupMethods($info[2])){
			$this->source_error = true;
			return false;
		}

		$this->source = $source;
		$this->width = $info[0];
		$this->height = $info[1];
		$this->source_error = false;

		return true;

	}

	function getSourceFile(){
		return $this->source;
	}

	function createFixedWidthThumb($target, $new_width){

		if($this->source_error) 
			return false;

		$ratio = $this->width/$this->height;
		$new_height = round($new_width/$ratio);
		return $this->createFixedSizeThumb($target, $new_width, $new_height);

	}

	function createFixedHeightThumb($target, $new_height){

		if($this->source_error) 
			return false;

		$ratio = $this->height/$this->width;
		$new_width = round($new_height/$ratio);
		return $this->createFixedSizeThumb($target, $new_width, $new_height);

	}

	function createFixedSizeThumb($target, $new_width, $new_height){

		if($this->source_error) 
			return false;

		$new_img = @imagecreatetruecolor($new_width, $new_height);
		return $this->createThumb($new_img,$target,0,0,0,0,$new_width, $new_height, $this->width, $this->height);
	}

	function createSquareThumb($target, $side){

		if($this->source_error) 
			return false;

		return $this->createCropThumb($target, $side, $side);

	}

	function createCropThumb($target, $new_width, $new_height){

		if($this->source_error)
			return false;

		if($new_height == 0 || $this->height == 0){
			return false;
		}

		if($new_width/$new_height>$this->width/$this->height) {
			$nh = ($this->height/$this->width)*$new_width;
			$nw = $new_width;
		} else {
			$nw = ($this->width/$this->height)*$new_height;
			$nh = $new_height;
		}
		$dx = ($new_width/2)-($nw/2);
		$dy = ($new_height/2)-($nh/2);

		$new_img = @imagecreatetruecolor($new_width, $new_height);

		return $this->createThumb($new_img,$target,$dx,$dy,0,0,$nw, $nh, $this->width, $this->height);

	}

	function createFilledThumb($target, $side){

		if($this->source_error) 
			return false;

		if($this->width <= $this->height){
			$ratio = $this->height/$this->width;
			$new_height = $side;
			$new_width = round($new_height/$ratio);
			$pos_y = 0;
			$pos_x = round(($side-$new_width)/2);
		}
		else{
			$ratio = $this->width/$this->height;
			$new_width = $side;
			$new_height = round($new_width/$ratio);
			$pos_y = round(($side-$new_height)/2);
			$pos_x = 0;
		}

		$new_img = @imagecreatetruecolor($side, $side);

		$white = imagecolorallocate($new_img, 255, 255, 255);
		@imagefill($new_img, 0, 0, $white);

		return $this->createThumb($new_img,$target, $pos_x, $pos_y, 0, 0, $new_width, $new_height, $this->width, $this->height);

	}

	function createBestFitThumb($target, $width, $height){

		if($this->source_error) 
			return false;

		$source_ratio = $this->height/$this->width;
		$target_ratio = $height/$width;

		if($target_ratio >= $source_ratio){
			$new_width = $width;
			$new_height = $new_width*$source_ratio;
		}
		else{
			$new_height = $height;
			$new_width = $new_height/$source_ratio;
		}			

		$new_img = @imagecreatetruecolor($new_width, $new_height);
		$success =  $this->createThumb($new_img,$target, 0, 0, 0, 0, $new_width, $new_height, $this->width, $this->height);

		return $success;		

	}

	function createMinMaxThumb($target, $side, $replace_source = false){

		if($this->source_error) 
			return false;

		if($this->width <= $this->height){
			$ratio = $this->height/$this->width;
			$new_width = $side;
			$new_height = $new_width*$ratio;

		}
		else{
			$ratio = $this->width/$this->height;
			$new_height = $side;
			$new_width = $new_height*$ratio;
		}

		$new_img = @imagecreatetruecolor($new_width, $new_height);
		$success =  $this->createThumb($new_img,$target, 0, 0, 0, 0, $new_width, $new_height, $this->width, $this->height);

		if($success && $replace_source){
			$this->setSourceFile($target);
		}

		return $success;			

	}

	function createBoundedThumb($target, $width, $height){

		$new_width = $this->width;
		$new_height = $this->height;

		if($width > 0){
			if($width < $this->width){

			}
		}

	}

	private function createThumb($new_img, $target, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h){

		$src_img = $this->createImage($this->source);

		if(!$src_img)
			return false;

		$this->prepareImage($src_img, $new_img);

		@imagecopyresampled($new_img, $src_img, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h);

		$success = $this->saveImage($new_img, $target);

		imagedestroy($src_img);
		imagedestroy($new_img);

		return $success;

	}

	private function createImage($source){
		$createImage = $this->create_image_function;
		return @$createImage($source);
	}

	private function saveImage($resource, $target){

		$quality = null;

		switch($this->type){
			case 'jpeg':
				$quality = 90;
				break;
			case 'png':
				$quality = 0;
				break;
		}

		$saveImage = $this->save_image_function;
		return @$saveImage($resource, $target, $quality);
	}

	private function prepareImage($image, $image_resized){
		return @call_user_func($this->prepare_image_function, $image, $image_resized);
	}

	private function prepareJpeg($image, $image_resized){
		return true;
	}

	private function preparePng($image, $image_resized){
		$trnprt_indx = imagecolortransparent($image);

		if ($trnprt_indx >= 0) {

			// Get the original image's transparent color's RGB values
			$trnprt_color    = imagecolorsforindex($image, $trnprt_indx);

			// Allocate the same color in the new image resource

			$trnprt_indx    = imagecolorallocate($image_resized, $trnprt_color['red'], $trnprt_color['green'], $trnprt_color['blue']);
			// Completely fill the background of the new image with allocated color.

			imagefill($image_resized, 0, 0, $trnprt_indx);
			// Set the background color for new image to transparent
			imagecolortransparent($image_resized, $trnprt_indx);
		  }
		  // Always make a transparent background color for PNGs that don't have one allocated already
		  else {

			// Turn off transparency blending (temporarily)
			imagealphablending($image_resized, false);

			// Create a new transparent color for image
			$color = imagecolorallocatealpha($image_resized, 0, 0, 0, 127);

			// Completely fill the background of the new image with allocated color.
			imagefill($image_resized, 0, 0, $color);

			// Restore transparency blending
			imagesavealpha($image_resized, true);
		 }

	}

	private function prepareGif($image, $image_resized){

	}

	private function setupMethods($mime){
		$valid = false;

		switch($mime){
			case IMAGETYPE_JPEG:
				$this->type = 'jpeg';
				$this->create_image_function = "imagecreatefromjpeg";
				$this->save_image_function = "imagejpeg";
				$this->prepare_image_function = array(&$this,'prepareJpeg');
				$valid = true;
				break;
			case IMAGETYPE_PNG:
				$this->type = 'png';
				$this->create_image_function = "imagecreatefrompng";
				$this->save_image_function = "imagepng";
				$this->prepare_image_function =  array(&$this,'preparePng');
				$valid = true;
				break;
			case IMAGETYPE_GIF:
				$this->type = 'gif';
				$this->create_image_function = "imagecreatefromgif";
				$this->save_image_function = "imagegif";
				$this->prepare_image_function =  array(&$this,'prepareGif');
				$valid = true;
				break;
			default:
				$valid = false;
		}

		return $valid;

	}

	function batchCreateThumbs(){

		#Contains the filenames of every thumb generated
		$names = array();

		$thumbs = func_get_args();
		$count = count($thumbs);

		#No thumbs specified
		if(!$count){
			return $names;
		}

		#Count the number of thumbs that are not original
		$non_original = 0;
		for($i=0; $i < $count; $i++){
			if($thumbs[$i][0] != 'original'){
				$non_original++;
			}
		}

		$old_source = $this->source;

		if($non_original > 2){

			$new_source = $old_source . '.'.mt_rand().'.tmp';

			$max_side = 0;

			#Get the maximum required size for all thumbs
			for($i=0; $i < $count; $i++){
				if($thumbs[$i][0] != 'original'){
					if(isset($thumbs[$i][2]) && $thumbs[$i][2] > $max_side){
						$max_side = $thumbs[$i][2];
					}
					if(isset($thumbs[$i][3]) && $thumbs[$i][3] > $max_side){
						$max_side = $thumbs[$i][3];
					}
				}
			}

			$success = $this->createMinMaxThumb($new_source, $max_side, true);

			if($success){
				$this->setSourceFile($new_source);
			}

		}

		$success = true;

		for($i=0; $i < $count; $i++){

			switch ($thumbs[$i][0]){
				case 'original':
					$success &= @copy($this->source,  $thumbs[$i][1]);
					break;
				case 'width':
					$success &= $this->createFixedWidthThumb($thumbs[$i][1], $thumbs[$i][2]);
					break;
				case 'height':
					if(!isset($thumbs[$i][3])){$thumbs[$i][3] = $thumbs[$i][2];}
					$success &= $this->createFixedHeightThumb($thumbs[$i][1], $thumbs[$i][3]);
					break;
				case 'square':
					$success &= $this->createSquareThumb($thumbs[$i][1], $thumbs[$i][2]);
					break;
				case 'filled':
					$success &= $this->createFilledThumb($thumbs[$i][1], $thumbs[$i][2]);
					break;
				case 'best':
					if(!isset($thumbs[$i][3])){$thumbs[$i][3] = $thumbs[$i][2];}
					$success &= $this->createBestFitThumb($thumbs[$i][1], $thumbs[$i][2], $thumbs[$i][3]);
					break;
				case 'crop':
					$success &= $this->createCropThumb($thumbs[$i][1], $thumbs[$i][2], $thumbs[$i][3]);
					break;
				case 'bounded':
					$success &= $this->createBoundedThumb($thumbs[$i][1], $thumbs[$i][2], $thumbs[$i][3]);
					break;
				default:
					trigger_error("Thumbnail of type '{$thumbs[$i][0]}' not recognized", E_USER_ERROR);
					$success = false;
					break;
			}
		}

		#Delete temporary thumbnail
		if($non_original > 2){
			@unlink($this->source);
			$this->setSourceFile($old_source);
		}

		if(!$success){
			for($i=0; $i < $count; $i++){
				@unlink($thumbs[$i][1]);
			}
		}

		return $success;

	}

}
