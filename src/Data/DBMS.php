<?php

namespace Tonight\Data;

abstract class DBMS
{
	public abstract function identifier(string $str);

	public abstract function getPrimaryKeyField($table);
	public abstract function getPrimaryKeyFrom($table);
	public abstract function getPrimaryKeyWhere($table);

	public abstract function getForeignKeyField($table);
	public abstract function getForeignKeyFrom($table);
	public abstract function getForeignKeyWhere($table);
	
	public function primaryKeysSelectQuery($table)
	{
		$column = $this->identifier('column');

		$field = $this->getPrimaryKeyField($column);
		$from = $this->getPrimaryKeyFrom($column);
		$where = $this->getPrimaryKeyWhere($column);

		return "SELECT {$field} AS {$column} FROM {$from} WHERE {$where}";
	}

	public function foreignKeysSelectQuery($table)
	{
		$column = $this->identifier('column');
		
		$field = $this->getForeignKeyField($column);
		$from = $this->getForeignKeyFrom($column);
		$where = $this->getForeignKeyWhere($column);

		return "SELECT {$field} AS {$column} FROM {$from} WHERE {$where}";
	}
}