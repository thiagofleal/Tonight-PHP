<?php

namespace Tonight\Data;

use \Tonight\Collections\ArrayList;

class Table extends ArrayList {

	private $db;
	private $name;
	private $idName;
	private $pk;
	private $fk;
	private $selectMode;
	private $sets;
	private $deletes;
	private $inserts;

	private function updateData() {
		$sql = "SELECT * FROM ".$this->idName;
		$sql = $this->db->query($sql);
		$this->setData($sql->fetchAll($this->selectMode));
	}

	/* Public interface */
	public function __construct(DataBase $db, string $name, $selectMode = \PDO::FETCH_OBJ) {
		parent::__construct();
		$this->db = $db;
		$this->name = $name;
		$this->idName = $db->identifier($name);
		$this->selectMode = $selectMode;
		$this->pk = $db->getPrimaryKeys($name);
		$this->fk = $db->getForeignKeys($name);
		$this->sets = array();
		$this->deletes = array();
		$this->inserts = array();
		$this->updateData();
	}

	public function setValue($value) {
		if(($key = array_search($value, $this->get())) !== false) {
			$this->sets[] =
				"UPDATE ".$this->idName.
				" SET ".implode(", ", array_map(function($key, $value) {
					return $this->db->identifier($key)."='".addslashes($value)."'";
				}, array_keys((array)$this->get($key)), (array)$this->get($key))).
				" WHERE ".$this->pkValues($key);
		}
	}

	public function remove($key) {
		if($key !== false) {
			$this->deletes[] = $this->pkValues($key);
			parent::remove($key);
		}
	}

	public function removeValue($arg) {
		if(is_array($arg)){
			foreach ($arg as $value) {
				$this->removeFirst($value);
			}
		} else {
			$this->removeFirst($arg);
		}
	}

	public function append($value) {
		$this->inserts[] = $this->size();
		parent::append($value);
	}

	public function pkValues($key) {
		$ret = array();
		foreach ($this->pk as $value) {
			$ret[] = $this->db->identifier($value)."='".$this->get($key)->$value."'";
		}
		return implode(" AND ", $ret);
	}

	public function update() {
		if(count($this->sets)) {
			foreach ($this->sets as $value) {
				$sql = $value;
				$this->db->query($sql);
			}
			$this->sets = array();
		}
		if(count($this->deletes)) {
			$sql = "DELETE FROM ".$this->idName." WHERE ".implode(" OR ", $this->deletes);
			$this->db->query($sql);
			$this->deletes = array();
		}
		if(count($this->inserts)) {
			foreach ($this->inserts as $value) {
				$sql = "INSERT INTO ".$this->idName
				." (".implode(", ", array_map( function($key) {
					return $this->db->identifier($key);
				}, array_keys($this->get($value))))
				.") VALUES(".implode(", ", array_map( function($value) {
					return "'".addslashes($value)."'";
				}, $this->get($value)))
				.")";
				$this->db->query($sql);
			}
			$this->inserts = array();
		}
		$this->updateData();
		return $this;
	}
}