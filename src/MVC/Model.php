<?php

namespace Tonight\MVC;

use Tonight\Data\Table;
use Tonight\Collections\ArrayList;

abstract class Model
{
	private $table;

	protected abstract function loadData($from);
	protected abstract function selectValues(...$args);
	protected abstract function selectRow();
	protected abstract function insertData();
	protected abstract function assignData(&$data);
	
	public static function newInstance(...$args) {
		return new static(...$args);
	}

	public static function getTable() {
		$model = static::newInstance();
		return $model->table;
	}

	public function __construct(Table $table) {
		$this->table = $table;
	}

	public function load(...$args) {
		$this->table->find($this->selectValues(...$args));
		$result = $this->table->toArrayList();

		if ($result->count() > 0) {
			$this->loadData($result->get(0));
			return true;
		} else {
			return false;
		}
	}

	public function insert() {
		$this->table->insert($this->insertData());
		$ret = $this->table->commit();
		$lastInsertedRows = $this->table->getInsertedRows();
		$this->loadData($lastInsertedRows->last());
		return $ret;
	}

	public function update() {
		$this->table->find($this->selectRow());
		$result = $this->table->toArrayList();

		if ($result->count() > 0) {
			$data = $result->get(0);

			$this->assignData($data);
			$this->table->update($data);
			return $this->table->commit();
		}
		return 0;
	}

	public function delete() {
		$this->table->delete($this->selectRow());
		return $this->table->commit();
	}

	public static function get(...$args) {
		$ret = static::newInstance();
		$ret->load(...$args);
		return $ret;
	}

	public static function getFromValues(iterable $values) {
		$ret = array();

		foreach ($values as $value) {
			$new = static::newInstance();
			$new->loadData($value);
			$ret[] = $new;
		}
		return new ArrayList($ret);
	}

	public static function getAll() {
		$table = static::getTable();
		return self::getFromValues($table->selectAll()->toArray());
	}
}
