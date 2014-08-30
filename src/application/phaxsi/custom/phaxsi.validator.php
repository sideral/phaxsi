<?php

class PhaxsiValidator{
	
	function get(){
		$args = func_get_args();
		$validator = array();
		foreach($args as $arg){
			if(!isset($this->$arg)){
				trigger_error('Validation rule "'.$arg.'" does not exist.', E_USER_ERROR);
			}
			$validator = array_merge($validator, $this->$arg);
		}
		return $validator;
	}
	
	public $required = array('required' => true);
	public $not_required = array('required' => false);
	
	public $min_2 = array('min_length' => 2);
	public $min_5 = array('min_length' => 5);
	public $min_10 = array('min_length' => 10);
	
	public $email = array('expression' => '/^[0-9a-zA-Z_+-]{1,50}(?:[.][0-9a-zA-Z_+-]+)*@[0-9a-zA-Z-]{2,64}(?:[.][0-9a-zA-Z_-]{2,6}){1,4}$/D');

	public $complete_url = array('expression' => '/^https?:\/\/[0-9A-Za-z_-]+(?:[\.][0-9A-Za-z_-]+)+(?:[\/\?][0-9A-Za-z_~:\.,#\!?@%&+=-]*)*$/D');
	public $complete_local_url = array('expression' => '/^https?:\/\/[0-9A-Za-z_-]+(?:[\.][0-9A-Za-z_-]+)*(?:[\/\?][0-9A-Za-z_~:\.,#\?@%&+=-]*)*$/D');
	
	public $lossy_url = array('expression' => '/^(?:https?:\/\/)?[0-9A-Za-z_-]+(?:[\.][0-9A-Za-z_-]+)+(?:[\/\?][0-9A-Za-z_~:\.,#\?@%&+=-]*)*$/D');
	
	public $hostless_url = array('expression' => '/^(?:[\/\?][0-9A-Za-z_~:\.,#\?@%&+=-]*)*$/D');
	
	public $url_identifier = array('expression' => '/^[a-zA-Z0-9_-]+$/D');
	
	public $no_html = array('expression' => '/^[^<>]*$/uD');
	
	public $us_zipcode = array('expression' => '/^[0-9]{5}$/D', 'max_length' => 5);
	
	public $phone = array('expression' => '/^[+0-9() -]*$/D');

	public $numeric = array('callback' => array('PhaxsiValidator', 'isNumeric'));

	public $unsigned_integer = array('expression' => '/^\d+$/D', 'max_value' => PHP_INT_MAX);
	
	public $integer = array('expression' => '/^-?[0-9]+$/D', 'max_value' => PHP_INT_MAX);

	public $positive_integer = array('expression' => '/^[1-9][0-9]*$/D', 'max_value' => PHP_INT_MAX);
	
	public $alphanumeric = array('expression' => '/^[a-zA-z 0-9_-]*$/D');
	
	public $text_with_punctuation = array('required' => true, '');
	
	public $comma_separated_text = array();
	
	public $recent_year = array('expression' => '/^(19|20)[0-9]{2}$/D');
	public $numeric_month = array('expression' => '/^$/D');

	public $us_date = array('expression' => '/^(?:[0][1-9]|[1][012])\/(?:[0][1-9]|[12][0-9]|[3][0-1])\/(?:19|20)[0-9]{2}$/');
	public $european_date = array('expression' => '/^(?:[0][1-9]|[12][0-9]|[3][0-1])\/(?:[0][1-9]|[1][012])\/(?:19|20)[0-9]{2}$/');
	public $mysql_date = array('expression' => '/^(?:19|20)[0-9]{2}-(?:[0]?[1-9]|[1][012])-(?:[0]?[1-9]|[12][0-9]|[3][0-1])$/');
	
	public $price = array('expression' => '/^\d{1,15}(?:\.\d{0,2})?$/D');

	public $percent = array('expression' => '/^\d+$/D', 'max_value' => '100', 'min_value' => '0');
	
	public $mp3_file = array('mime_types' => array('audio/mpeg' => 'mp3','audio/x-mpeg' => 'mp3', 'audio/mp3' => 'mp3'), 'extension' => array('mp3'));
	
	static function isNumeric($number){
		if($number === '' || is_null($number) ){
			return true;
		}
		return is_numeric($number);
	}
	
}
