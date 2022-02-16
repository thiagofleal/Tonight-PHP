<?php

namespace Tonight\Data;

class Query
{
    const EQUAL = '=';
    const DIFF = "<>";
    const GREATER = ">";
    const LESS = "<";
    const GREATER_EQUAL = ">=";
    const LESS_EQUAL = "<=";
    
    const ASC = "ASC";
    const DESC = "DESC";

    private $table;
    private $select;
    private $from;
    private $where;
    private $order;
    private $limit;

    public function __construct(Table $table)
    {
        $this->table = $table;
        $this->sql = "";
        $this->select = "SELECT *";
        $this->from = " FROM ".$this->table->getIdName();
        $this->where = array();
        $this->order = "";
        $this->limit = "";
        $this->build();
    }

    public function getSQL()
    {
        return $this->sql;
    }

    private function identifier($str)
    {
        $parts = explode(".", $str);
        return implode(".", array_map( function($part) {
            if ($part === "*" || str_contains($part, "(")) {
                return $part;
            }
            return $this->table->getDB()->getDBMS()->identifier($part);
        }, $parts));
    }

	public function select($fields)
	{
        $select = array();

        if (!is_array($fields)) {
            $fields = array($fields);
        }
        foreach ($fields as $key => $field) {
            if ($field === "*" || str_contains($field, "(")) {
                $select[] = $field;
            } elseif (is_string($key) && is_string($field)) {
                if (!str_contains($key, "(")) {
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

    private function sqlValue($value)
    {
        if (is_string($value)) {
            $value = "'{$value}'";
        }
        if ($value === NULL) {
            $value = "NULL";
        }
        return $value;
    }

    public function where($field, $operator, $value)
    {
        $value = $this->sqlValue($value);
        $this->where[] = $this->identifier($field)."{$operator}{$value}";
        return $this;
    }

    public function find(iterable $fields)
    {
        $where = array();

        foreach ($fields as $field => $value) {
            $where[] = $this->identifier($field)."=".$this->sqlValue($value);
        }
        $this->where = $where;
        return $this;
    }

    private function join($type, $table, iterable $on)
    {
        $keys = array();

        foreach ($on as $first => $second) {
            $firstField = $this->table->getIdName().".".$this->identifier($first);
            $secondField = $this->identifier($table).".".$this->identifier($second);
            $keys[] = $firstField."=".$secondField;
        }
        $this->from .= " {$type} JOIN ".$this->identifier($table)." ON ".implode(" AND ", $keys);
        return $this;
    }

    public function leftJoin($table, iterable $on)
    {
        return $this->join("LEFT", $table, $on);
    }

    public function rightJoin($table, iterable $on)
    {
        return $this->join("RIGHT", $table, $on);
    }

    public function innerJoin($table, iterable $on)
    {
        return $this->join("INNER", $table, $on);
    }

    public function orderBy($field, $mode)
    {
        if (!str_contains($field, "(")) {
            $field = $this->identifier($field);
        }
        $this->order = " ORDER BY {$field} {$mode}";
        return $this;
    }

    public function limit($initial, $end = NULL)
    {
        $this->limit = " LIMIT {$initial}";

        if ($end !== NULL) {
            $this->limit .= ", {$end}";
        }
        return $this;
    }

    private function buildWhere()
    {
        if (!count($this->where)) {
            return "";
        }
        return " WHERE ".implode(" AND ", $this->where);
    }

	public function build()
	{
		$this->sql = $this->select.$this->from.$this->buildWhere().$this->order.$this->limit;
        return $this;
	}

    public function execute()
	{
        $this->build();
		$db = $this->table->getConnection();
		$sql = $db->query($this->sql);

		if ($sql === false) {
			$data = array();
		} else {
			$data = $sql->fetchAll();
		}
		$this->table->setData($data);
	}
}