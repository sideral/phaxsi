<?php

class PhaxsiLang extends Lang{

	/**
	 * Default error messages to display on forms. Useful for testing.
	 */
	public $default_errors = array(
		'required'			=> 'This field is required',
		'expression'		=> 'Invalid value entered',
		'max_size'			=> 'The file size is greater than allowed',
		'min_size'			=> 'The file size is less than allowed',
		'max_length'		=> 'This field exceeded the maximum length allowed',
		'min_length'		=> 'This field has a value shorter than allowed',
		'max_value'			=> 'This number is too large',
		'min_value'			=> 'This number is too small',
		'callback'			=> 'Invalid value entered',
		'database_column'	=> 'This field is not valid',
		'summary'			=> 'There were errors in the form submission',
		'array_min_count' => 'There are less filled in fields than required',
		'array_max_count' => 'There are more filled in fields than allowed',
		'array_count'		=> 'You must fill in a different number of fields',
		'array_required_values' => 'One or more required values are not present',
		'array_required_keys' => 'One or more required fields were not filled',
		'extension'			=> 'This file format isn\'t accepted',
		'mime_types'		=> 'This file format isn\'t accepted'
	);

	public $http_messages = array(
		400 => array('Bad Request', 'Your browser sent a request that this server could not understand.'),
		401 => array('Authorization Required', 'This server could not verify that you are authorized to access the document requested.'),
		404 => array('Page Not Found', 'The requested URL was not found on this server.'),
		500 => array('Internal Server Error', 'Please try again later')
	);


}
