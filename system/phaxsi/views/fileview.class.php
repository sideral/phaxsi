<?php

/**
 * View for controllers that return files.
 * 

 * Copyright 2008-2012, Alejandro Zuleta (http://phplab.co)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2008-2012, Alejandro Zuleta (http://phplab.co)
 * @link          http://phaxsi.net Phaxsi PHP Framework
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @package       Phaxsi.Views
 * @since         Phaxsi v 0.1
 */

class FileView extends View {

	protected $mime = 'text/plain';
	protected $filename = '';

	public function render(){

		if(!headers_sent()){
			header('Content-type: '. $this->mime);
			header("Content-Length: " . filesize($this->filename));
		}

		$fh = fopen($this->filename, 'r');
		fpassthru($fh);

	}

	function setFile($filename, $type = null){

		if(!file_exists($filename)){
			return false;
		}

		$this->filename = $filename;

		if($type){
			$this->mime = $type;
		}
		else{
			$finfo = finfo_open(FILEINFO_MIME, "/usr/share/misc/magic"); // return mime type ala mimetype extension
			if (!$finfo) {
				return false;
			}
			$this->mime = finfo_file($finfo, $filename);
			finfo_close($finfo);
		}

		return $this->mime != '';

	}

	function setClientCache($days){
		//header("Cache-Control: must-revalidate");
		$offset = 60 * 60 * 24 * $days ;
		$ExpStr = "Expires: " .	gmdate("D, d M Y H:i:s", time() + $offset) . " GMT";
		header($ExpStr);
	}

	function getType(){
		return $this->mime;
	}

	function getCache(){
		trigger_error('Cache is disabled on views of type file.', E_USER_ERROR);
	}

}
