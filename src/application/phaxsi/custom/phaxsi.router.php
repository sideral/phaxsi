<?php

class PhaxsiRouter extends Router{

	protected function getErrorContext(){
		return new Context('controller', 'phaxsi', 'error', array(404));
	}

}