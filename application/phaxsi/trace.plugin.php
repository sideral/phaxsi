<?php

	class TracePlugin extends Plugin {

		protected $name = 'Trace';

		function initialize(){

			$default_config = array(
				'enabled' => true,
				'trace_queries' => true,
				'show_args' => false
			);

			$this->config = array_merge($default_config, $this->config);
			
		}

		function controllerStart($context){

			if(!$this->config['enabled']){
				return;
			}
			$args_str = '';
			if($this->config['show_args']){
				$args = ConsolePlugin::formatArguments($context->getArguments());
				$args_str = ' ('. implode(',',$args).')';
			}
			
			ConsolePlugin::Log('Start '.ucfirst($context->getType()) .$args_str, 'no-important', $context);

		}

		function controllerEnd($context){
			if(!$this->config['enabled']){
				return;
			}
			
			ConsolePlugin::Log('End '.ucfirst($context->getType()), 'no-important', $context);

		}

		function renderStart($context){
			
			if(!$this->config['enabled']){
				return;
			}
			
			ConsolePlugin::Log('Start Render '.ucfirst($context->getType()), 'no-important', $context);

		}

		function renderEnd($context){
			if(!$this->config['enabled']){
				return;
			}
			
			ConsolePlugin::Log('End Render '.ucfirst($context->getType()), 'no-important', $context);

		}

		function queryStart($query){
			if(!$this->config['enabled'] || !$this->config['trace_queries']){
				return;
			}
			
			$backtrace = array_slice(debug_backtrace(), 4, 3);
			$location = '';

			foreach($backtrace as $back){
				if(!isset($back['class']) || in_array($back['class'], array('TableReader'))){
					continue;
				}
				$location = ucfirst($back['class']).'::'.ucfirst($back['function']);
				break;
			}
			
			ConsolePlugin::Log($query, 'no-important', $location);
			
		}

		function queryEnd($query){
			if(!$this->config['enabled'] || !$this->config['trace_queries']){
				return;
			}
			
			ConsolePlugin::Log('Query End', 'no-important', '-');

		}

	}
