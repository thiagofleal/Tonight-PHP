<?php

namespace Tonight\Data;

abstract class DataBase extends \PDO {

	protected $PKquery;
	protected $FKquery;
	
	public function __construct(...$args) {
		parent::__construct(...$args);
	}

	public abstract function identifier(string $str);

	public function getPrimaryKeys(string $table, $mode = \PDO::FETCH_OBJ) {
		$sql = $this->PKquery."'".$table."'";
		$sql = $this->query($sql);
		if($sql->rowCount()) {
			return $sql->fetch($mode);
		}
		return array();
	}

	public function getForeignKeys(string $table, $mode = \PDO::FETCH_OBJ) {
		$sql = $this->FKquery."'".$table."'";
		$sql = $this->query($sql);
		if($sql->rowCount()) {
			return $sql->fetch($mode);
		}
		return array();
	}
}