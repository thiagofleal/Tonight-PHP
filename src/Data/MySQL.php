<?php

namespace Tonight\Data;

class MySQL extends DataBase
{
	public function __construct($dsn, ...$args)
	{
		if (is_array($dsn)) {
			$dsn['driver'] = 'mysql';
		}
		parent::__construct($dsn, ...$args);
	}

	public function identifier(string $str)
	{
		return "`$str`";
	}

	public function primaryKeysSelectQuery(string $table)
	{
		return "SELECT `key_column_usage`.`column_name` 
			FROM `information_schema`.`key_column_usage` 
			WHERE `table_schema` = schema() 
			AND `constraint_name` = 'PRIMARY' 
			AND `table_name` = '$table'";
	}

	public function foreignKeysSelectQuery(string $table)
	{
		return "SELECT `key_column_usage`.`column_name` 
			FROM `information_schema`.`key_column_usage` 
			WHERE `table_schema` = schema() 
			AND `constraint_name` = 'FOREIGN' 
			AND `table_name` = '$table'";
	}
}