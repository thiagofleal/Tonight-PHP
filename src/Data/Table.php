<?php

namespace Tonight\Data;

use Tonight\Collections\ArrayList;
use Tonight\Exceptions\DataBaseException;
use stdClass;
use PDO;
use strpos;
use is_string;

class Table
{
	private $db;
	private $name;
	private $idName;
	private $pk;
	private $fk;
	private $sets;
	private $deletes;
	private $inserts;
	private $rowsInserted;
    private $select;
    private $from;
    private $where;
    private $order;
    private $limit;

	public function __construct(DataBase $db, $name) {
		$this->db = $db;
		$this->name = $name;
		$this->idName = $db->getDBMS()->identifier($name);
		$this->pk = $db->getPrimaryKeys($name);
		$this->fk = $db->getForeignKeys($name);
		$this->sets = array();
		$this->deletes = array();
		$this->inserts = array();
		$this->rowsInserted = array();
        $this->restartQuery();
	}

	private function restartQuery() {
		$this->sql = "";
        $this->select = "SELECT *";
        $this->from = " FROM ".$this->getIdName();
        $this->where = array();
        $this->order = "";
        $this->limit = "";
	}

    private function identifier($str) {
        $parts = explode(".", $str);
        return implode(".", array_map( function($part) {
            if ($part === "*" || strpos($part, "(") !== FALSE) {
                return $part;
            }
            return $this->getDB()->getDBMS()->identifier($part);
        }, $parts));
    }

    private function formatValue($value) {
        if (!isset($value) || $value === NULL) {
            $value = "NULL";
        } elseif (is_string($value)) {
            $value = "'".addslashes($value)."'";
        } elseif (is_array($value)) {
			$value = "(".implode(",", $value).")";
		} elseif (is_bool($value)) {
			$value = $value ? "1" : "0";
		}
        return $value;
    }

    private function buildWhereEqual(iterable $fields) {
        $where = array();

        foreach ($fields as $field => $value) {
            $where[] = $this->identifier($field)."=".$this->formatValue($value);
        }
        return $where;
    }

    private function buildWhere($fields) {
        if (!count($fields)) {
            return "";
        }
        return " WHERE ".implode(" AND ", $fields);
    }

    private function join($type, $table, iterable $on) {
        $keys = array();

		if ($table instanceof Table) {
			$table = $table->getIdName();
		} else {
			$table = $this->identifier($table);
		}
        foreach ($on as $first => $second) {
            $firstField = $this->getIdName().".".$this->identifier($first);
            $secondField = $table.".".$this->identifier($second);
            $keys[] = $firstField."=".$secondField;
        }
        $this->from .= " {$type} JOIN ".$this->identifier($table)." ON ".implode(" AND ", $keys);
        return $this;
    }

	public function selectAll() {
		$this->restartQuery();
		return $this;
	}

	public function select($fields) {
        $select = array();

        if (!is_array($fields)) {
            $fields = array($fields);
        }
        foreach ($fields as $key => $field) {
            if ($field === "*" || strpos($field, "(") !== FALSE) {
                $select[] = $field;
            } elseif (is_string($key) && is_string($field)) {
                if (strpos($key, "(") === FALSE) {
                    $key = $this->identifier($key);
                }
                $select[] = $key." AS ".$this->identifier($field);
            } elseif (is_string($field)) {
                $select[] = $this->identifier($field);
            }
        }
        $select = implode(",", $select);
		$this->select = "SELECT {$select}";
        return $this;
	}

	private function buildWhereOperation($field, $operator, $value) {
		$value = $this->formatValue($value);
		return $this->identifier($field).$operator.$value;
	}

    public function where($field, $operator, $value) {
		if (is_string($field)) {
			$this->where[] = $this->buildWhereOperation($field, $operator, $value);
			return $this;
		}
		if (is_array($field) && is_array($value)) {
			$op0 = "";
			$op1 = "";
			if (count($field) === 3) {
				$op0 = $this->buildWhereOperation($field[0], $field[1], $field[2]);
			}
			if (count($value) === 3) {
				$op1 = $this->buildWhereOperation($value[0], $value[1], $value[2]);
			}
			if (!empty($op0) && !empty($op1)) {
				$this->where[] = "($op0 $operator $op1)";
				return $this;
			}
		}
		throw new DataBaseException("Impossible to create query from arguments");
    }

    public function find(iterable $fields) {
        $this->where = $this->buildWhereEqual($fields);
        return $this;
    }

    public function leftJoin($table, iterable $on) {
        return $this->join("LEFT", $table, $on);
    }

    public function rightJoin($table, iterable $on) {
        return $this->join("RIGHT", $table, $on);
    }

    public function innerJoin($table, iterable $on) {
        return $this->join("INNER", $table, $on);
    }

    public function orderBy($field, $mode) {
        if (strpos($field, "(") === FALSE) {
            $field = $this->identifier($field);
        }
        $this->order = " ORDER BY {$field} {$mode}";
        return $this;
    }

    public function limit($initial, $end = NULL) {
        $this->limit = " LIMIT {$initial}";

        if ($end !== NULL) {
            $this->limit .= ", {$end}";
        }
        return $this;
    }

	public function build() {
		$this->sql = $this->select.$this->from.$this->buildWhere($this->where).$this->order.$this->limit;
	}

    public function toArray() {
        $this->build();
		$db = $this->getConnection();
		$sql = $db->query($this->sql);
		$this->restartQuery();

		if ($sql === false) {
			$data = array();
		} else {
			$data = $sql->fetchAll();
		}
		return $data;
	}

	public function toArrayList() {
		return new ArrayList($this->toArray());
	}

	public function getDB() {
		return $this->db;
	}

	public function getConnection() {
		return $this->db->getConnection();
	}

	public function getName() {
		return $this->name;
	}

	public function getIdName() {
		return $this->idName;
	}

	public function getPrimaryKeys() {
		return $this->pk;
	}

	public function getForeignKeys() {
		return $this->fk;
	}

	public function pkValuesArray(array $fields) {
		$ret = array();
		$dbms = $this->db->getDBMS();
		foreach ($this->pk as $value) {
			if (!isset($fields[$value])) {
				return FALSE;
			}
			$ret[] = $dbms->identifier($value)."=".$this->formatValue($fields[$value]);
		}
		return $ret;
	}

	public function pkValues($value) {
		$array = $this->pkValuesArray((array)$value);
		if ($array === FALSE) {
			return FALSE;
		}
		return implode(" AND ", $array);
	}

	public function delete($fields) {
		$this->deletes[] = implode(" AND ", $this->buildWhereEqual($fields));
		return $this;
	}

	public function deleteWhere($field, $operator, $value) {
		$this->deletes[] = $this->identifier($field).$operator.$this->formatValue($value);
		return $this;
	}

	public function insert($value) {
		$this->inserts[] = $value;
		return $this;
	}

	public function update($value) {
		$this->sets[] = (array)$value;
		return $this;
	}

	public function getInsertedRows() {
		$this->select = "SELECT *";
		$this->from = " FROM {$this->idName}";
		$this->where = array(implode(" OR ", array_map( function ($item) {
			return implode(" AND ", $item);
		}, $this->rowsInserted)));
		$this->order = "";
		$this->limit = "";
		return $this->toArrayList();
	}

	public function commit() {
		$dbms = $this->db->getDBMS();
		$insert = false;
		$ret = 0;

		if (count($this->sets)) {
			$pdo = $this->getConnection();
			$sql = '';
			foreach ($this->sets as $item) {
				$sql .= "UPDATE ".$this->idName." SET ".implode(",", array_map( function($key, $value) use($dbms) {
					return $dbms->identifier($key)."=".$this->formatValue($value);
				}, array_keys((array)$item), (array)$item)).
				" WHERE ".$this->pkValues($item).";";
			}
			$sql = substr($sql, 0, -1);
			$ret += $pdo->exec($sql);
		}
		if (count($this->deletes)) {
			$pdo = $this->getConnection();
			$sql = "DELETE FROM ".$this->idName." WHERE ".implode(" OR ", array_map( function($item) {
				return "({$item})";
			}, $this->deletes));
			$ret += $pdo->exec($sql);
		}
		if (count($this->inserts)) {
			$pdo = $this->getConnection();
			$this->rowsInserted = array();
			foreach ($this->inserts as $key => $insert) {
				$sql = "INSERT INTO ".$this->idName
				."(".implode(",", array_map( function($key) use($dbms) {
					return $dbms->identifier($key);
				}, array_keys((array)$insert))).")VALUES";
				$sql .= "(".implode(",", array_map( function($value) {
					return $this->formatValue($value);
				}, (array)$insert)).")";
				$ret += $pdo->exec($sql);
				$keys = $this->pkValuesArray($insert);

				if ($keys !== FALSE) {
					$this->rowsInserted[] = $keys;
				} else {
					$keys = array();
					foreach ($this->pk as $field) {
						$keys[] = $this->identifier($field)."=".$pdo->lastInsertId($field);
					}
					$this->rowsInserted[] = $keys;
				}
			}
			$insert = true;
		}
		$this->sets = array();
		$this->deletes = array();
		$this->inserts = array();

		return $ret;
	}
}