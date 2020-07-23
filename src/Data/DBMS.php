<?php

namespace Tonight\Data;

abstract class DBMS
{
	public abstract function identifier($str);

	public abstract function getPrimaryKeyField($table);
	public abstract function getPrimaryKeyFrom($table);
	public abstract function getPrimaryKeyWhere($table);

	public abstract function getForeignKeyField($table);
	public abstract function getForeignKeyFrom($table);
	public abstract function getForeignKeyWhere($table);
	
	public function primaryKeysSelectQuery($table)
	{
		$column = $this->identifier('column');

		$field = $this->getPrimaryKeyField($table);
		$from = $this->getPrimaryKeyFrom($table);
		$where = $this->getPrimaryKeyWhere($table);

		return "SELECT {$field} AS {$column} FROM {$from} WHERE {$where}";
	}

	public function foreignKeysSelectQuery($table)
	{
		$column = $this->identifier('column');
		
		$field = $this->getForeignKeyField($table);
		$from = $this->getForeignKeyFrom($table);
		$where = $this->getForeignKeyWhere($table);

		return "SELECT {$field} AS {$column} FROM {$from} WHERE {$where}";
	}
}