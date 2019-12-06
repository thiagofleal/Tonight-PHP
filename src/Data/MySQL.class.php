<?php

namespace Tonight\Data;

class MySQL extends DataBase {

	private $dbName;

	public function __construct($name, $host, ...$args) {
		$con = "mysql:dbname=$name;host=$host";
		$this->dbName = $name;
		parent::__construct($con, ...$args);
	}

	public function identifier(string $str) {
		return "`$str`";
	}

	public function tablesSelectQuery() {
		return "SELECT `table_name` 
			FROM `information_schema`.`tables` 
			WHERE `table_schema`='".$this->dbName."'";
	}

	public function primaryKeysSelectQuery(string $table) {
		return "SELECT `key_column_usage`.`column_name` 
			FROM `information_schema`.`key_column_usage` 
			WHERE `table_schema` = schema() 
			AND `constraint_name` = 'PRIMARY' 
			AND `table_name` = '$table'";
	}

	public function foreignKeysSelectQuery(string $table) {
		return "SELECT `key_column_usage`.`column_name` 
			FROM `information_schema`.`key_column_usage` 
			WHERE `table_schema` = schema() 
			AND `constraint_name` = 'FOREIGN' 
			AND `table_name` = '$table'";
	}
}