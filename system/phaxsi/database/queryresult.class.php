<?php

/**
 * Base class for query results.
 * 

 * Copyright 2008-2014, Alejandro Zuleta (http://phplab.co)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2008-2014, Alejandro Zuleta (http://phplab.co)
 * @link          http://phaxsi.net Phaxsi PHP Framework
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @package       Phaxsi.Database
 * @since         Phaxsi v 0.1
 */


class QueryResult{

	private $result = null;
	private $driver = null;

	function __construct($result, $driver){
		$this->result = $result;
		$this->driver = $driver;
	}

	function isError(){
		return $this->result === false;
	}

	function isSuccess(){
		return $this->result != false;
	}

	function fetchRow() {
		return $this->driver->fetchAssocArray($this->result);
	}

	function fetchAllRows() {
		return $this->driver->fetchAllRows($this->result);
	}

	function fetchAllRowsNum() {
		return $this->driver->fetchAllRowsNum($this->result);
	}

	function fetchScalar($index = 0) {

		$i = 0;
		do {
			$row = $this->driver->fetchNumArray($this->result);
		}
		while($i++ < $index );

		if(!$row)
			return false;

		return $row[0];
	}

	function fetchArray() {

		$fetched_array = array();

		while($row = $this->driver->fetchNumArray($this->result)) {
			$fetched_array[] = $row[0];
		}

		return $fetched_array;

	}

	function fetchNumMatrix() {

		$multi = array();
		while($row = $this->driver->fetchNumArray($this->result)) {
			$multi[] = $row;
		}

		return $multi;

	}

	function fetchKeyValue() {

		$assoc = array();
		while($row = $this->driver->fetchNumArray($this->result)) {
			$assoc[$row[0]] = $row[1];
		}

		return $assoc;
	}

	function fetchTransposedRows() {

		$multi = array();

		while($row = $this->driver->fetchAssocArray($this->result)) {
			foreach($row as $key => $value) {
				if(!isset($multi[$key])) {
					$multi[$key] = array();
				}

				$multi[$key][] = $value;
			}
		}

		return $multi;
	}

	function fetchTree($id_field, $parent_field, $children_name) {

		$tree = array();

		while($row = $this->driver->fetchAssocArray($this->result)) {

			$element_id = $row[$id_field];
			$parent_id = $row[$parent_field];

			if($parent_id == '0') {
				$tree[$element_id] = $row;
			}
			else {

				$parent =& $this->findNode($tree, $parent_id, $children_name);

				if(!$parent) {
					continue;
				}

				if(!isset($parent[$children_name])) {
					$parent[$children_name] = array();
				}

				$parent[$children_name][$element_id] = $row;

			}

		}

		return $tree;

	}

	protected function &findNode(&$tree, $node_id, $children_name) {


		if(isset($tree[$node_id])) {
			return $tree[$node_id];
		}

		foreach($tree as &$node) {
			if(isset($node[$children_name])) {

				$found =& $this->findNode($node[$children_name], $node_id, $children_name);

				if($found) {
					return $found;
				}
			}
		}

		// Avoids Notice
		$null = null;
		return $null;

	}

	function fetchResult() {
		return $this->result;
	}

	function count() {
		return $this->driver->countRows($this->result);
	}

	function lastInsertId() {
		return $this->driver->lastInsertId();
	}

}

