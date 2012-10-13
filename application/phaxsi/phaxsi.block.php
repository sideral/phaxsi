<?php

 class PhaxsiBlock extends Block{

	function console(){
		$this->helper->filter->defaults($this->args,
			array('lines' => null, 'popup' => false)
		);			
		$this->view->setArray($this->args);
	}

 }