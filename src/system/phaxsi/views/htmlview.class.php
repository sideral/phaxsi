<?php

/**
 * View for controllers that return html.
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

class HtmlView extends View{

	protected $use_layout = true;

	function setTemplate($template){
		$this->context = $this->context->deriveContext($this->context->getType(), $template);
		$this->load = new Loader($this->context);
	}

	function getTemplate(){
		return $this->load->template();
	}

	/**
	 * Gets the page template and returns its contents or the cached template
	 * if is a hit. This method is only called by the controller
	 *
	 * @param AbstractController $controller, the controller that uses this view
	 * @return string The final html generated
	 */
	public function render(){
                //Set correct charset
                header('Content-Type:text/html; charset='.  strtoupper(AppConfig::CHARSET));
            
		if($this->cache && $this->cache->isHit()){
			if($_htmlview_contents = $this->cache->getContents()){
				return $_htmlview_contents;
			}
		}

		$_htmlview_template_file = $this->getTemplate();

		$_htmlview_contents = '';

		if($_htmlview_template_file){

			$html = $this->load->helper('html');
			
			extract($this->helpers, EXTR_OVERWRITE);

			extract($this->_template_vars, EXTR_OVERWRITE);

			$_htmlview_cache_enabled = $this->cache && $this->cache->isEnabled();

			if($this->buffer_output || $_htmlview_cache_enabled){

				ob_start();
				require($_htmlview_template_file);
				$_htmlview_contents = ob_get_clean();

				if($_htmlview_cache_enabled){
					$this->cache->setContents($_htmlview_contents);
				}

			}
			else{
				require($_htmlview_template_file);
			}

		}

		return $_htmlview_contents;

	}

}
