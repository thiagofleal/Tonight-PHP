<?php

namespace Tonight\Data;

class MySQL extends DataBase {

	public function __construct($name, $host, ...$args) {
		$con = "mysql:dbname=$name;host=$host";
		parent::__construct($con, ...$args);
		$this->PKquery = 
			"SELECT `key_column_usage`.`column_name`
			 FROM `information_schema`.`key_column_usage`
			 WHERE `table_schema` = schema()
			 AND `constraint_name` = 'PRIMARY'
			 AND `table_name` = ";
		$this->FKquery = 
			"SELECT `key_column_usage`.`column_name`
			 FROM `information_schema`.`key_column_usage`
			 WHERE `table_schema` = schema()
			 AND `constraint_name` = 'FOREIGN'
			 AND `table_name` = ";
	}

	public function identifier(string $str) {
		return "`$str`";
	}
}