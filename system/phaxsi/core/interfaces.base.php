<?php

/**
 * The interfaces used by different parts of Phaxsi.
 * 

 * Copyright 2008-2012, Alejandro Zuleta (http://phplab.co)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2008-2012, Alejandro Zuleta (http://phplab.co)
 * @link          http://phaxsi.net Phaxsi PHP Framework
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @package       Phaxsi.Core
 * @since         Phaxsi v 0.1
 */

/**
 * General interface for all the components
 * that perform any type of validation
 *
 */
interface IValidable{
	function validate();
	function setValidator($options, $messages = null);
	function getValidator();
	function getErrorCode();
	function setErrorCode($error_code);
	function getErrorMessage();
	function hasError();
}

interface IFormComponent extends IValidable{

	function getName();
	function setName($name);

	function setValue($value);
	function getValue($filtered = true);
	function returnsValue();

	function getRawValue();
	function setRawValue($value);

	function resetValue();
	function getDefaultValue();

	function setLabel($label_text, $attributes = array());
	function getLabel($label_text = '', $attributes = array());

	function setFilter($filter);
	function getFilter();

	function isFileUpload();

	function getTarget();
	function setTarget($table, $column = null);

	function disable();
	function enable();

	function setData($name, $value);
	function getData($name);
	
	function isScalar();

	function __toString();

}

interface ICacheProvider{
	function delete($key);
	function get($key);
	function set($key, $value, $duration);
	function isHit($key, $duration = 0);
}

interface IDatabaseDriver{

	function query($query);
	function quote($text);
	function execute($query, $params);

	function fetchAllRows($result);
	function fetchAllRowsNum($result);
	function fetchAssocArray($result);
	function fetchNumArray($result);

	function countRows($result);
	function lastInsertId();

	function lastError();

	function isReady();
	function getConnection();

}

interface Installer{
	function getVersion();
	function getDependencies();
}
