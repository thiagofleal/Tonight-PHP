<?php

namespace Tonight\MVC;

use Tonight\Collections\ArrayList;

abstract class Model
{
	private $table;

	protected abstract function loadData($from);
	protected abstract function selectFromArgs($row, ...$args);
	protected abstract function selectRow($row);
	protected abstract function insertData();
	protected abstract function assignData(&$data);
	
	public static function newInstance() {
		return new static();
	}

	public function __construct($table) {
		$this->table = $table;
	}

	public function load(...$args) {
		$result = $this->table->where( fn($row) => $this->selectFromArgs($row, ...$args) );

		if ($result->count() > 0) {
			$this->loadData($result->get(0));
			return true;
		} else {
			return false;
		}
	}

	public function insert() {
		$this->table->append($this->insertData());
		$ret = $this->table->commit();
		$lastInsertedKey = $this->table->getLastInsertedKey();
		$lastInsertedRow = $this->table->get($lastInsertedKey);
		$this->loadData($lastInsertedRow);
		return $ret;
	}

	public function update() {
		$result = $this->table->where( fn($row) => $this->selectRow($row) );

		if ($result->count() > 0) {
			$data = $result->get(0);

			$this->assignData($data);
			$this->table->setValue($data);
			return $this->table->commit();
		}
		return 0;
	}

	public function delete() {
		$this->table->removeWhere( fn($row) => $this->selectRow($row) );
		return $this->table->commit();
	}

	public static function get(...$args) {
		$ret = static::newInstance();
		$ret->load(...$args);
		return $ret;
	}

	public static function getAll() {
		$ret = array();
		$db = static::newInstance();

		foreach ($db->table as $value) {
			$new = static::newInstance();
			$new->loadData($value);
			$ret[] = $new;
		}
		return new ArrayList($ret);
	}
}