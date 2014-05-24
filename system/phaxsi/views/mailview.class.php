<?php

/**
 * View for controller that sends mails.
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

require_once(PHAXSIC_PROCESSVIEW);

class MailView extends ProcessView {

	protected $headers = array();
	protected $_template_list_vars = array();
	protected $mailer = null; 

	function __construct($context){
		Loader::includeLibrary('phpmailer5.2/class.phpmailer.php');
		$this->mailer = new PHPMailer();	
		$this->mailer->CharSet = AppConfig::CHARSET;
		parent::__construct($context);
	}

	function getMailer(){
		return $this->mailer;
	}

	public function setList($to, $value){
		return $this->_template_list_vars[$to] = $value;
	}

	public function getList($name){
		if(isset($this->_template_list_vars[$name]))
			return $this->_template_list_vars[$name];
		else{
			trigger_error("Variable list '$name' is not defined", E_USER_WARNING);
			return null;
		}
	}

	public function send($recipient, $subject = AppConfig::TITLE, $from = ''){

		$recipient = (array) $recipient;

		$template_file = $this->getTemplate();

		$contents = array();

		if($template_file){

			$html = $this->load->helper('html');
			extract($this->helpers, EXTR_OVERWRITE);
			
			$this->mailer->Subject    = $subject;
			
			if(!empty($from)){
				if(!is_array($from)){
					$this->mailer->SetFrom($from, AppConfig::TITLE);
				}
				else{
					$this->mailer->SetFrom($from[0], $from[1]);
				}
			}			
			
			extract($this->_template_vars, EXTR_OVERWRITE);

			$debug_text = '';

			foreach($recipient as $to){

				$this->mailer->ClearAddresses();
				
				if(is_array($to)){
					$to_email = $to[0];
					$this->mailer->AddAddress($to[0], $to[1]);
				}
				else{
					$to_email = $to;
					$this->mailer->AddAddress($to);
				}

				if(isset($this->_template_list_vars[$to_email])){
					extract($this->_template_list_vars[$to_email], EXTR_OVERWRITE);
				}

				ob_start();
				require($template_file);
				$body = ob_get_clean();

				if(isset($this->_template_list_vars[$to_email])){
					foreach($this->_template_list_vars[$to_email] as $var_name => $var_value){
						unset($var_name);
					}
				}

				$contents[$to_email] = $body;

				if(!AppConfig::DEBUG_MODE){
					$this->mailer->MsgHTML($body);					
					$this->mailer->Send();		
					usleep(500000);
				}
				else{
					$debug_text.= "<strong>A</strong>: $to_email<br/><strong>Tema</strong>: $subject<br/><br/>";
					$debug_text.= $body;
					$debug_text.= "<hr/>";
				}

			}

			if(AppConfig::DEBUG_MODE){
				if(!is_dir(APPD_TMP.DS.'mails')){
					mkdir(APPD_TMP.DS.'mails', 0777);
				}
				file_put_contents(APPD_TMP.DS.'mails'.DS.microtime(true), $debug_text);
			}

			return $contents;

		}

		return false;
	}

	function setTemplate($template){
		$this->context = $this->context->deriveContext($this->context->getType(), $template);
		$this->load = new Loader($this->context);
	}

	function getTemplate(){
		return $this->load->template();
	}

}
