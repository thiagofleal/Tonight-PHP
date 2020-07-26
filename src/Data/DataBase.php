<?php

namespace Tonight\Data;

use PDO;

class DataBase
{
	private $dbms;
	private $dsn;
	private $args;
	private $attributes;

	protected $dbName;
	protected $tableNames;

	public function __construct($class, $dsn, ...$args)
	{
		$this->dbms = new $class();
		$this->dsn = $dsn;
		$this->args = $args;
		$this->attributes = array();
	}

	public function getConnection()
	{
		$con = new PDO($this->dsn, ...$this->args);
		$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$con->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
		foreach ($this->attributes as $attribute) {
			$con->setAttribute($attribute['key'], $attribute['value']);
		}
		return $con;
	}

	public function setAttribute($key, $value)
	{
		$this->attributes[] = array('key' => $key, 'value' => $value);
	}

	public function load(...$tables)
	{
		foreach ($tables as $table) {
			$this->tableNames[] = $table;
			$this->{$table} = new Table($this, $table);
		}
	}

	public function getDBMS() { return $this->dbms; }
	public function dbName() { return $this->dbName; }
	public function tableNames() { return $this->tableNames; }

	public function getPrimaryKeys($table)
	{
		$db = $this->getConnection();
		$sql = $this->dbms->primaryKeysSelectQuery($table);
		$sql = $db->query($sql);
		$ret = array();
		if ($sql !== false) {
			foreach ($sql->fetchAll() as $value) {
			 	$ret[] = $value->column;
			}
		}
		return $ret;
	}

	public function getForeignKeys($table)
	{
		$db = $this->getConnection();
		$sql = $this->dbms->foreignKeysSelectQuery($table);
		$sql = $db->query($sql);
		$ret = array();
		if ($sql !== false) {
			foreach ($sql->fetchAll() as $value) {
			 	$ret[] = $value->column;
			}
		}
		return $ret;
	}
}