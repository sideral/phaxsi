<?php

class LogModel extends Model{

	protected $driver_name = 'generic_log';

	function addLine($message){

		if(!$this->driver->isReady())
			$this->createTable();

		$datetime = date('Y-m-d H:i:s');
		$this->query("INSERT INTO log (message,datetime)
							VALUES(?,?)", $message, $datetime);

	}

	function getAllLines(){
		if(!$this->driver->isReady())
			$this->createTable();

		return $this->query("SELECT * FROM log")->fetchAllRows();
	}

	private function createTable(){
		$query = 'CREATE TABLE log 
					(log_id INTEGER PRIMARY KEY NOT NULL ,
					message TEXT NOT NULL ,
					datetime DATETIME)';

		$this->query($query);

	}

}
