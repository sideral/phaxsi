<?php

class PhaxsiController extends Controller{

	function error(){

		$code = !isset($this->args[0]) ? 500 : $this->args[0];

		$headers = array(
			400 => 'HTTP/1.1 400 Bad Request',
			401 => 'HTTP/1.1 401 Unathorized',
			404 => 'HTTP/1.1 404 Not Found',
			500 => "HTTP/1.1 500 Internal Server Error"
		);

		if(!isset($headers[$code])){
			$code = 400;
		}

		header($headers[$code]);

		if($this->plugin->isEnabled('Error')){
			$this->view->setTemplate($this->plugin->Error->getConfig('template'));
		}

		$this->view->set('code', $code);

	}

	//TODO
	function install(){
		if(!AppConfig::DEBUG_MODE){
			$this->helper->redirect->to($this->plugin->Error->get404());
		}
	}
	
	function log(){

		if(!AppConfig::DEBUG_MODE){
			$this->helper->redirect->to($this->plugin->Error->get404());
		}
		
		$this->layout = null;
		
		$this->helper->filter->validate($this->args, array(0 => array('expression' => '/^[a-z]+$/')));
		
		if(!$this->args[0]){
			$this->helper->redirect->to($this->plugin->Error->get404());
		}
		
		$log = $this->load->utility('Log');
		
		$log->loadLog($this->args[0]);
		
		$this->view->set('data',
			$log->getAllLines()
		);

	}

}
