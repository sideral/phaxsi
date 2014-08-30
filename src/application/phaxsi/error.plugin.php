<?php

	class ErrorPlugin extends Plugin{

		protected $name = 'Error';
		private $is_locked = false;
		
		public function initialize(){
			
			$default_config = array(
				'enabled' => true,
				'template' => '/phaxsi/error',
				'error_level' => E_ALL ^ E_STRICT,
				'admin_email' => '',
				'display_errors' => 0,
				'log' => false,
				'log_level' => E_ERROR
			);

			$this->config = array_merge($default_config, $this->config);
			
			AppConfig::$url_map['{failure}'] = 'phaxsi/error/400';
			
		}

		function requestStart($context){
			if(!$this->config['enabled']){
				return;
			}			
			error_reporting($this->config['error_level']);
			ini_set('display_errors', $this->config['display_errors']);
			set_error_handler(array(&$this, 'handleError'), E_ALL ^ E_STRICT );
			set_exception_handler(array(&$this, 'handleException'));
			register_shutdown_function(array(&$this, 'handleShutdown'));
		}

		function get404(){
			return '/phaxsi/error/404';
		}
		
		function getErrorPage($error_code){
			return '/phaxsi/error/'.$error_code;
		}

		function goto404(){
			RedirectHelper::to($this->get404());
		}

		function handleError($error_type, $message, $file, $line, $context = null){

			$message .= " ($file:$line)";

			//Prevents errors while handling errors!

			if(!$this->is_locked){
				
				$this->is_locked = true;
				
				switch($error_type){
					case E_NOTICE:
					case E_STRICT:
					case E_USER_NOTICE:
						$this->handleNotice($message, $file, $line);
						break;
					case E_USER_WARNING:
					case E_WARNING:
						$this->handleWarning($message, $file, $line);
						break;
					case E_PARSE:
						$this->handleParserError($message, $file, $line);
						break;
					case E_ERROR:
					case E_COMPILE_ERROR:
					case E_USER_ERROR:
					default:
						$this->handleFatalError($message, $file, $line);

				}

				$this->is_locked = false;
				
			}
			else{
				//???
				$this->show($message);
			}

		}

		function handleException($exception){
			$this->handleError(E_USER_ERROR, $exception->getMessage(), $exception->getFile(), $exception->getLine());
		}

		function handleShutdown(){
			$error = error_get_last();
			if($error){
				switch($error['type']){
					case E_ERROR:
					case E_PARSE;
					case E_CORE_ERROR:
					case E_COMPILE_ERROR:
					case E_USER_ERROR:
						$this->handleError($error['type'], $error['message'], $error['file'], $error['line']);
						break;
				}
			}
		}

		private function handleNotice($message, $file, $line){
			$this->handleGenericError($message, $file, $line, 'notice');
		}

		private function handleWarning($message, $file, $line){
			$this->handleGenericError($message, $file, $line, 'warning');
		}

		private function handleFatalError($message, $file, $line){
			
			$this->handleGenericError($message, $file, $line, 'error');
			
			if(!AppConfig::DEBUG_MODE){
				RedirectHelper::force('/phaxsi/error/500');
			}
			
			exit;
			
		}
		
		private function handleGenericError($message, $file, $line, $type){

			if($this->plugin->isEnabled('Console')){
				ConsolePlugin::Log($message, $type, null, $type != 'notice', $type == 'notice'?4:5);
				if($type == 'error'){
					$this->plugin->Console->finalize();
				}
			}
			elseif(AppConfig::DEBUG_MODE){
				$this->show("{$message} ({$file} on line {$line})");
			}
			else{
				$this->log("{$message} ({$file} on line {$line})", E_ERROR);
			}
			
		}

		private function handleParserError($message, $file, $line){
			
			if(AppConfig::DEBUG_MODE){
				$this->show("{$message} ({$file} on line {$line})");
			}
			else{
				$this->log("{$message} ({$file} on line {$line})", E_ERROR);
				RedirectHelper::force('/phaxsi/error/500');
			}

			exit;
		}

		private function show($output){
			print '<div style="color:red;background:#fff;padding:5px;font-size:13px;">'.$output.'</div>';
		}

		private function log($message, $error_level){
			if($this->config['log'] && $error_level <= $this->config['log_level']){
				$log = $this->load->utility('Log');
				$log->loadLog('error', true);
				$log->addLine($message);
			}
		}

	}
		