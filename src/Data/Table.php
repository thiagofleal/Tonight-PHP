<?php

namespace Tonight\Data;

use PDO;
use Tonight\Collections\ArrayList;

class Table extends ArrayList
{
	private $db;
	private $name;
	private $idName;
	private $pk;
	private $fk;
	private $selectMode;
	private $sets;
	private $deletes;
	private $inserts;

	private function updateData()
	{
		$sql = "SELECT * FROM ".$this->idName;
		$sql = $this->db->query($sql);

		if ($sql === false) {
			$data = array());
		} else {
			$data = $sql->fetchAll($this->selectMode);
		}
		$this->setData($data);
	}

	/* Public interface */
	public function __construct(DataBase $db, string $name, $selectMode = PDO::FETCH_OBJ)
	{
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

	public function setValue($value)
	{
		if (($key = array_search($value, $this->get())) !== false) {
			$this->sets[] = array(
				"key" => $key,
				"value" => $value
			);
		}
	}

	public function remove($key)
	{
		if ($key !== false) {
			$this->deletes[] = $this->pkValues($key);
			parent::remove($key);
		}
	}

	public function removeArray(array $arg)
	{
		foreach ($arg as $value) {
			$this->removeFirst($value);
		}
	}

	public function append($value)
	{
		$this->inserts[] = $this->size();
		parent::append($value);
	}

	public function pkValues($key)
	{
		$ret = array();
		foreach ($this->pk as $value) {
			$ret[] = $this->db->identifier($value)."='".$this->get($key)->$value."'";
		}
		return implode(" AND ", $ret);
	}

	private function formatValue($value)
	{
		if(empty($value)) {
			return 'NULL';
		} else {
			return "'".addslashes($value)."'";
		}
	}

	public function update()
	{
		if (count($this->sets)) {
			foreach ($this->sets as $item) {
				$sql = "UPDATE ".$this->idName." SET ".implode(", ", array_map( function($key, $value) {
					return $this->db->identifier($key)."=".$this->formatValue($value);
				}, array_keys((array)$this->get($item["key"])), (array)$this->get($item["key"]))).
				" WHERE ".$this->pkValues($item["key"]);
				$this->db->query($sql);
			}
			$this->sets = array();
		}
		if (count($this->deletes)) {
			$sql = "DELETE FROM ".$this->idName." WHERE ".implode(" OR ", $this->deletes);
			$this->db->query($sql);
			$this->deletes = array();
		}
		if (count($this->inserts)) {
			foreach ($this->inserts as $value) {
				$sql = "INSERT INTO ".$this->idName
				." (".implode(", ", array_map( function($key, $value) {
					return $this->db->identifier($key);
				}, array_keys($this->get($value)), $this->get($value)))
				.") VALUES(".implode(", ", array_map( function($value) {
					return $this->formatValue($value);
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