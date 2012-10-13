<?php

function Console($content, $type = 'info', $context = null, $add_backtrace = false){
	ConsolePlugin::Log($content, $type, $context, $add_backtrace, 2);
}

class ConsolePlugin extends Plugin {

	protected $name = 'Console';

	private $lines = array();
	private $messages_added = false;
	private $last_time = 0;

	function initialize(){
		
		require_once(APPD_APPLICATION.DS.'phaxsi/phaxsi.block.php');
		
		$default_config = array(
			'enabled' => true,
			'show_redirect' => true,
			'log' => false,
			'print' => AppConfig::DEBUG_MODE
		);

		$this->config = array_merge($default_config, $this->config);
		
		$this->last_time = microtime(true);
		
		//Just to be sure ??
		Session::start();
		
	}

	function toString($popup = false){
		
		if(!AppConfig::DEBUG_MODE || !$this->config['enabled']){
			return '';
		}

		if(!$this->lines){
			return '';
		}

		return (string)$this->load->block('console', array('lines' => $this->lines, 'popup' => $popup));
		
	}
	
	protected function addRedirectLines(){
		if($this->config['show_redirect']){
			$lines = $this->session->getFlash('console');
			if($lines){
				$this->addLine('-- FROM PREVIOUS PAGE --', 'redirect', UrlHelper::referer(true), false);
				$this->lines = array_merge($this->lines, $lines);
			}
		}
	}

	function requestEnd($context){
		$this->finalize($context);
	}
	
	function finalize($context = null){

		if(!$this->config['enabled']){
			return;
		}

		if($this->config['print'] && (!$context || $context->getViewType() == 'html')){
			$this->addRedirectLines();
			print $this->toString(is_null($context));
		}
		
		if($this->config['log']){
			$log = $this->load->utility('Log');
			$log->loadLog('console', true);
			foreach($this->lines as $line){	
				if(is_array($line['name'])){
					$line['name'] = print_r($line['name'], true);
				}
				$log->addLine(implode(' | ',$line));
			}
		}
		
	}
	
	function isEnabled(){
		return $this->config['enabled'] && ($this->config['print'] || $this->config['log']);
	}

	function onRedirect($url){
		if(!$this->config['enabled']){
			return;
		}
		$this->addRedirectLines();
		$this->session->setFlash('console', $this->lines);
	}

	function addLine($content, $type = 'info', $location = '-', $put_time = true){
		
		$this->messages_added = true;
		
		$duration = '';
		if($put_time){
			$current_time = microtime(true);
			$duration = $current_time - $this->last_time;
			$this->last_time = $current_time;
		}
		
		$line = array(
			'location' => $location,
			'type' => $type,
			'name' => $content,
			'duration' => $duration
		);
		
		$this->lines[] = $line;
		
	}

	
	static function Log($content, $type = 'info', $context = null, $add_backtrace = false, $start_context = 1){
		
		$location = null;
		
		$backtrace = array();
		if(!$context){
			$backtrace = debug_backtrace();
			if(isset($backtrace[$start_context])){
				if(isset($backtrace[$start_context]['class'])){
					$location = $backtrace[$start_context]['class'].'::'.$backtrace[$start_context]['function'];
				}
				else{
					$location = $backtrace[$start_context]['function'];
				}
			}
			else{
				$add_backtrace = false;
			}
		}
		else if($context instanceof Context){
			$location =  ucfirst($context->getModule(false)).ucfirst($context->getType()).'::'.$context->getAction();
		}
		else{
			$location = (string)$context;
		}

		PluginManager::getInstance()->getPluginList()->Console->addLine($content, $type, $location);
		
		if($add_backtrace){
			$backtrace = $backtrace? $backtrace : debug_backtrace();
			self::addBacktrace(array_slice($backtrace, $start_context, count($backtrace)-4-$start_context));
		}
		
	}
	
	private static function addBacktrace($backtrace){
		
		$console = PluginManager::getInstance()->getPluginList()->Console;
		
		foreach($backtrace as $back){
			
			if(!isset($back['class'])){
				continue;
			}

			$args = self::formatArguments($back['args']);

			$content = $back['class'].'::'.$back['function'] .'('.implode(',',$args).')';
			
			if(isset($back['file']) && isset($back['line'])){
				$content.= " \r\nFile: " .str_replace(APPD, '', $back['file']).":".$back['line'].'';
			}
			
			$console->addLine($content, 'backtrace', '-', false);
		
		}

	}

	static function formatArguments($back, $level = 0){

		
		$args = array();
		
		foreach($back as $key=>$arg){
			
			$arg_key = is_string($key)? "\r\n".str_repeat(' ', ($level)*4)."'$key'" .' => ': "\r\n".str_repeat(' ', $level*4);
			
			if(is_object($arg)){
				$args[] = $arg_key.get_class($arg);
			}
			else if(is_array($arg)){
				
				$child_args = self::formatArguments($arg, $level+1);
				
				$child_str = '';
				foreach($child_args as $i => $child){
					$child_str .= $child .($i==count($child_args)-1?"":",");
				}
				
				$args[] = $arg_key."array(".$child_str."\r\n".str_repeat(' ', $level*4).')';
				
			}
			else if(is_string($arg)){
				$args[] = $arg_key."'$arg'";
			}
			elseif(is_null($arg)){
				$args[] = $arg_key.'null';
			}
			elseif(is_bool($arg)){
				$args[] = $arg_key.($arg?'true':'false');
			}
			else{
				$args[] = $arg_key.$arg;
			}

		}

		return $args;

	}
	
	

}
