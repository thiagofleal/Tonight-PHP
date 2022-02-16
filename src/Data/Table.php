<?php

namespace Tonight\Data;

use stdClass;
use PDO;
use Tonight\Collections\ArrayList;

class Table extends ArrayList
{
	private $db;
	private $name;
	private $idName;
	private $pk;
	private $fk;
	private $sets;
	private $deletes;
	private $inserts;
	private $rowInsert;
	private $defaultQuery;
	private $autoUpdate;
	
	private function updateData()
	{
		if ($this->autoUpdate) {
			$this->defaultQuery->execute();
		}
	}

	public function newInstance($data)
	{
		$new = clone $this;
		$new->setData($data);
		return $new;
	}

	public function __construct(DataBase $db, $name, $load = true)
	{
		parent::__construct();
		$this->db = $db;
		$this->name = $name;
		$this->idName = $db->getDBMS()->identifier($name);
		$this->pk = $db->getPrimaryKeys($name);
		$this->fk = $db->getForeignKeys($name);
		$this->sets = array();
		$this->deletes = array();
		$this->inserts = array();
		$this->defaultQuery = $this->query();
		$this->autoUpdate = $load;
		$this->updateData();
		$this->rowInsert = array();
	}

	public function setAutoUpdate($autoUpdate)
	{
		$this->autoUpdate = $autoUpdate;
	}

	public function getDB()
	{
		return $this->db;
	}

	public function getConnection()
	{
		return $this->db->getConnection();
	}

	public function getName()
	{
		return $this->name;
	}

	public function getIdName()
	{
		return $this->idName;
	}

	public function getPrimaryKeys()
	{
		return $this->pk;
	}

	public function getForeignKeys()
	{
		return $this->fk;
	}

	public function setValue($value)
	{
		if (($key = array_search($value, $this->get())) !== false) {
			$this->sets[] = $key;
		}
	}

	public function setValues($sets)
	{
		$ret = parent::setValues($sets);
		foreach ($this as $value) {
			$this->setValue($value);
		}
		return $ret;
	}

	public function remove($key)
	{
		if ($key !== false) {
			$this->deletes[] = $this->pkValues($key);
			parent::remove($key);
		}
	}

	public function append($value)
	{
		parent::append($value);
		$this->inserts[] = $this->getLastInsertedKey();
	}

	public function getRowsInsert()
	{
		return new ArrayList($this->rowInsert);
	}

	public function pkValuesArray($key)
	{
		$ret = array();
		$dbms = $this->db->getDBMS();
		foreach ($this->pk as $value) {
			$ret[] = $dbms->identifier($value)."='".$this->get($key)->{$value}."'";
		}
		return $ret;
	}

	public function pkValues($key)
	{
		$array = $this->pkValuesArray($key);
		return implode(" AND ", $array);
	}

	private function formatValue($value)
	{
		if($value === NULL || (is_string($value) && strlen($value) == 0)) {
			return 'NULL';
		} else {
			return "'".addslashes($value)."'";
		}
	}

	public function commit()
	{
		$dbms = $this->db->getDBMS();
		$insert = false;
		$count_deletes = count($this->deletes);
		$ret = 0;
		
		if (count($this->sets)) {
			$pdo = $this->getConnection();
			$sql = '';
			foreach ($this->sets as $item) {
				$sql .= "UPDATE ".$this->idName." SET ".implode(",", array_map( function($key, $value) use($dbms) {
					return $dbms->identifier($key)."=".$this->formatValue($value);
				}, array_keys((array)$this->get($item)), (array)$this->get($item))).
				" WHERE ".$this->pkValues($item).";";
			}
			$sql = substr($sql, 0, -1);
			$ret += $pdo->exec($sql);
		}
		if ($count_deletes) {
			$pdo = $this->getConnection();
			$sql = "DELETE FROM ".$this->idName." WHERE ".implode(" OR ", $this->deletes);
			$ret += $pdo->exec($sql);
		}
		if (count($this->inserts)) {
			$pdo = $this->getConnection();
			$sql = '';
			foreach ($this->inserts as $key) {
				$sql .= "INSERT INTO ".$this->idName
				."(".implode(",", array_map( function($key) use($dbms) {
					return $dbms->identifier($key);
				}, array_keys((array)$this->get($key)))).")VALUES";
				$sql .= "(".implode(",", array_map( function($value) {
					return $this->formatValue($value);
				}, (array)$this->get($key))).");";
			}
			$sql = substr($sql, 0, -1);
			$ret += $pdo->exec($sql);
			$insert = true;
		}
		
		$this->updateData();

		if ($insert) {
			$data = array();
			foreach ($this->inserts as $key) {
				$data[] = $this->get($key - $count_deletes);
			}
			$this->rowInsert = $data;
		}
		
		$this->sets = array();
		$this->deletes = array();
		$this->inserts = array();
		
		return $ret;
	}

	public function getDefaultQuery()
	{
		return $this->defaultQuery;
	}

	public function query()
	{
		return new Query($this);
	}
}