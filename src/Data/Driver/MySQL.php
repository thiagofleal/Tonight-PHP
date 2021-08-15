<?php

namespace Tonight\Data\Driver;

use Tonight\Data\DBMS;

class MySQL extends DBMS
{
	public function identifier($str)
	{
		return "`$str`";
	}

	public function getPrimaryKeyField($table)
	{
		return "`key_column_usage`.`column_name`";
	}

	public function getPrimaryKeyFrom($table)
	{
		return "`information_schema`.`key_column_usage`";
	}

	public function getPrimaryKeyWhere($table)
	{
		return "`table_schema` = schema() AND `constraint_name` = 'PRIMARY' AND `table_name` = '$table'";
	}

	public function getForeignKeyField($table)
	{
		return "`key_column_usage`.`column_name`";
	}

	public function getForeignKeyFrom($table)
	{
		return "`information_schema`.`key_column_usage`";
	}

	public function getForeignKeyWhere($table)
	{
		return "`table_schema` = schema() AND `constraint_name` = 'FOREIGN' AND `table_name` = '$table'";
	}	
}