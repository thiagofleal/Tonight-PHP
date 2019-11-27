<?php

namespace Tonight\Data;

abstract class DataBase extends \PDO {

	protected $tableNames;

	public function __construct(...$args, $start = true) {
		parent::__construct(...$args);
		$this->tableNames = $this->getTables();
		if($start) {
			$this->start();
		}
	}

	public function start() {
		foreach ($this->tableNames as $value) {
			$this->{$value} = new Table($this, $value);
		}
	}

	public abstract function identifier(string $str);
	public abstract function tablesSelectQuery();
	public abstract function primaryKeysSelectQuery(string $table);
	public abstract function foreignKeysSelectQuery(string $table);

	public function tableNames() { return $this->tableNames; }

	public function getTables($mode = \PDO::FETCH_OBJ) {
		$sql = $this->tablesSelectQuery();
		$sql = $this->query($sql);
		if($sql->rowCount()) {
			return $sql->fetch($mode);
		}
		return array();
	}

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