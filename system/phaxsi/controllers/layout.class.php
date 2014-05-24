<?php

/**
 * The base class for Layout controllers. 
 * 

 * Copyright 2008-2012, Alejandro Zuleta (http://phplab.co)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2008-2012, Alejandro Zuleta (http://phplab.co)
 * @link          http://phaxsi.net Phaxsi PHP Framework
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @package       Phaxsi.Controller
 * @since         Phaxsi v 0.1
 */


abstract class Layout extends AbstractController{

	protected $title = "";
	protected $charset = AppConfig::CHARSET;
	protected $description;
	protected $keywords;
	protected $styles = array();
	protected $scripts = array();
	protected $metas = array();
	protected $feeds = array();
	protected $page_context;
	protected $js_library = AppConfig::JS_LIBRARY;
	protected $doctype;
	protected $is_xhtml;
	protected $favico;

	const XHTML_1_0_STRICT		 = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">';
	const XHTML_1_0_TRANSITIONAL = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
	const XHTML_1_0_FRAMESET	 = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD XHTML 1.0 Frameset//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd">';
	const HTML_4_01_STRICT		 = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">';
	const HTML_4_01_TRANSITIONAL = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">';
	const HTML_4_01_FRAMESET	 =  '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN" "http://www.w3.org/TR/html4/frameset.dtd">';
	const HTML_5_0				 = '<!DOCTYPE HTML>';
	
	function __construct($context, $page_context){
		$this->doctype = self::HTML_5_0;
		$this->is_xhtml = false;
		$this->page_context = $page_context;
		parent::__construct($context, new HtmlView($context));
		$this->view->setBufferOutput(AppConfig::OUTPUT_BUFFERING_ENABLED);
		$this->addMetaTag('','text/html; charset='.$this->charset, 'Content-Type');
		$this->addMetaTag('language', Lang::getCurrent(), '');
		$this->addMetaTag('', 'no', 'imagetoolbar');
		$this->_execute();
	}

	function _display($page){

		$this->view->set('requested_page', $page);
		$this->view->set('head_html', $this->getHeadHtml());
		
		$lang = Lang::getCurrent();
		
		$document_opening = $this->doctype."\r\n";
		
		if($this->is_xhtml)
			$document_opening .= '<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="'.$lang.'" lang="'.$lang.'">';
		else
			$document_opening .= '<html lang="'.$lang.'">';
		
		$document_closing = '</html>';

		$this->view->set('document_opening', $document_opening);
		$this->view->set('document_closing', $document_closing);

		return $this->_render();

	}

	function addMetaTag($name = '', $content = '', $equiv = ''){
		$this->metas[] = array('name'=>$name, 'content'=>$content, 'http-equiv'=>$equiv);
	}

	function setTitle($title, $append_site_name = true, $append_meta = false){
		$this->title = $append_site_name?  $title . ' | ' . AppConfig::TITLE: $title;
		if($append_meta){
			$this->addMetaTag('title', $title);
			$this->addMetaTag('DC.Title', $title);
			$this->addMetaTag('', $title, 'title');
		}
	}

	function setDescription($description){
		$this->description = $description;
	}

	function setKeywords($keywords){
		$this->keywords = $keywords;
	}

	function addScript($path){
		$this->scripts[] = $path;
	}

	function addStyle($path, $media = 'screen'){
		$this->styles[] = array($path, $media);
	}

	function setJsLibrary($library){
		$this->js_library = $library;
	}

	function addFeed($path){
		$this->feeds[] = $path;
	}

	function setFavicon($path, $type='image/ico'){
		$this->favico = array($path, $type);
	} 

	function setDocType($type){
		$this->doctype = $type;
	}

	protected function getHeadHtml(){

		$html = new HtmlHelper($this->context);

		$output = '';

		foreach($this->metas as $meta){
			if($meta['name']){
				$output .= "<meta name=\"{$meta['name']}\" content=\"".HtmlHelper::escape($meta['content'])."\" />\r\n";
			}
			elseif($meta['http-equiv']){
				$output .= "<meta http-equiv=\"{$meta['http-equiv']}\" content=\"".HtmlHelper::escape($meta['content'])."\" />\r\n";
			}
		}

		$output .= "<title>".HtmlHelper::escape($this->title)."</title>\r\n";

		$output .= '<base href="'.UrlHelper::get('/').'"/>'."\r\n";

		if($this->favico){
			$output .= '<link rel="icon" type="'.$this->favico[1]."\" href=\"".UrlHelper::resource($this->favico[0])."\">\r\n";
		}

		if($this->description){
			$output .= '<meta name="description" content="'.HtmlHelper::escape($this->description)."\" />\r\n";
		}

		if($this->keywords){
			$output .= '<meta name="keywords" content="'.HtmlHelper::escape($this->keywords)."\" />\r\n";
		}

		foreach($this->feeds as $feed){
			$output .= '<link rel="alternate" type="application/rss+xml" title="RSS" href="'.UrlHelper::get($feed)."\" />\r\n";
		}

		foreach($this->styles as $style){
			$output .= $html->css($style[0], $style[1])."\r\n";
		}

		if($this->js_library){
			$output .= "<script type=\"text/javascript\" src=\"{$this->js_library}\"></script>\r\n";
			$output .= $html->javascript('/'.APPU_PHAXSI.'/'.'phaxsi-'.PhaxsiConfig::FRAMEWORK_VERSION.(AppConfig::DEBUG_MODE?'':'.min').'.js')."\r\n";
			$output .= HtmlHelper::inlineJavascript("Phaxsi.path = {".
				"base: '".UrlHelper::get('')."',".
				"local: '".UrlHelper::localized('/')."',".
				"'public': '".APPU_PUBLIC."',".
				"lang: '".Lang::getCurrent()."'}"
			);
		}

		foreach($this->scripts as $script){
			$output .= $html->javascript($script)."\r\n";
		}

		$this->styles = $this->scripts = array();

		return $output;

	}

}
