<?php

/**
 * View for controllers that return json.
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

class JsonView extends View {

	protected $jsonp_callback = null;
	
	public function render(){

		//$_SERVER['HTTP_X_REQUESTED_WITH'] == 'XmlHttpRequest';

		if($this->cache && $this->cache->isHit()){
			if($contents = $this->cache->getContents()){
				return $contents;
			}
		}

		foreach($this->_template_vars as &$var){
			if($var instanceof AbstractController ){
				ob_start();
				print($var);
				$var = ob_get_clean();
			}
		}

		$json = JsonHelper::encode($this->_template_vars);

		if($this->cache && $this->cache->isEnabled()){
			$this->cache->setContents($json);
		}
		
		if($this->jsonp_callback){
			$json = $this->jsonp_callback.'('.$json.')';
		}

		return $json;

	}
	
	function setJsonPCallback($callback){
		$this->jsonp_callback = $callback;						
	}

}