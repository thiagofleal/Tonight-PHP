<?php

namespace Tonight\Data\Driver;

use Tonight\Data\DBMS;

class SQLite extends DBMS
{
	public function identifier($str)
	{
		return "\"$str\"";
	}

	public function getPrimaryKeyField($table)
	{
		return "\"name\"";
	}

	public function getPrimaryKeyFrom($table)
	{
		return "pragma_table_info('$table')";
	}

	public function getPrimaryKeyWhere($table)
	{
		return "\"pk\"=1";
	}

	public function getForeignKeyField($table)
	{
		return "\"name\"";
	}

	public function getForeignKeyFrom($table)
	{
		return "pragma_table_info('$table')";
	}

	public function getForeignKeyWhere($table)
	{
		return "1=1";
	}	
}