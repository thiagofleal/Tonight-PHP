<?php

namespace Tonight\Data;

abstract class DataBase extends \PDO {

	protected $dbName;
	protected $tableNames;

	public function __construct($dsn, ...$args) {
		$con = '';
		if(is_array($dsn)) {
			$con = $dsn["driver"].":".implode(";", array_map( function($key, $value) {
				return $key != 'driver' ? $key."=".$value : '';
			}, array_keys($dsn), $dsn));
			$this->dbName = $dsn["dbname"];
		}
		if(is_string($dsn)) {
			$con = $dsn;
		}
		parent::__construct($con, ...$args);
	}

	public function start($tables) {
		if(is_string($tables)) {
			$this->tableNames = array($tables);
		}
		if(is_array($tables)) {
			$this->tableNames = $tables;
		}
		foreach ($this->tableNames as $value) {
			$this->{$value} = new Table($this, $value);
		}
	}

	public abstract function identifier(string $str);
	public abstract function primaryKeysSelectQuery(string $table);
	public abstract function foreignKeysSelectQuery(string $table);

	public function dbName() { return $this->dbName; }
	public function tableNames() { return $this->tableNames; }

	public function getPrimaryKeys(string $table, $mode = \PDO::FETCH_OBJ) {
		$sql = $this->primaryKeysSelectQuery($table);
		$sql = $this->query($sql);
		if($sql->rowCount()) {
			return $sql->fetch($mode);
		}
		return array();
	}

	public function getForeignKeys(string $table, $mode = \PDO::FETCH_OBJ) {
		$sql = $this->foreignKeysSelectQuery($table);
		$sql = $this->query($sql);
		if($sql->rowCount()) {
			return $sql->fetch($mode);
		}
		return array();
	}
}