<?php

namespace Tonight\Collections;

class ArrayList extends Collection
{
	private $data;
	private $size;
	private $current;
	private $lastInsertedKey;

	public function newInstance($data)
	{
		return new self($data);
	}

	private function updateSize()
	{
		$this->size = count($this->data);
	}

	public function __construct(array $data = array())
	{
		$this->setData($data);
		$this->rewind();
	}

	public function current()
	{
		return $this->data[array_keys($this->data)[$this->current]];
	}

	public function key()
	{
		return array_keys($this->data)[$this->current];
	}

	public function next()
	{
		$this->current++;
	}

	public function rewind()
	{
		$this->current = 0;
	}

	public function valid()
	{
		return $this->current < $this->size;
	}

	public function setData(array $data)
	{
		$this->data = $data;
		$this->updateSize();
	}

	public function get($key = false)
	{
		return ($key === false ? $this->data : $this->data[$key]);
	}

	public function first()
	{
		foreach ($this->data as $value) {
			return $value;
		}
		return NULL;
	}

	public function last()
	{
		$last = NULL;
		foreach ($this->data as $value) {
			$last = $value;
		}
		return $last;
	}

	public function firstKey()
	{
		foreach ($this->data as $key => $value) {
			return $key;
		}
		return NULL;
	}

	public function lastKey()
	{
		$last = NULL;
		foreach ($this->data as $key => $value) {
			$last = $key;
		}
		return $last;
	}

	public function getLastInsertedKey()
	{
		return $this->lastInsertedKey;
	}

	public function set($key, $value)
	{
		$this->data[$key] = $value;
		$this->updateSize();
	}

	public function setValues($sets)
	{
		foreach ($this->data as &$element) {
			if (is_array($sets)) {
				foreach ($sets as $key => $value) {
					if (is_array($element)) {
						$element[$key] = $value;
					}
					if (is_object($element)) {
						$element->{$key} = $value;
					}
				}
			} else {
				$element = $sets;
			}
		}
		return $this;
	}

	public function isset($key)
	{
		return isset($this->data[$key]);
	}

	public function remove($key)
	{
		if ($key !== false && isset($this->data[$key])) {
			unset($this->data[$key]);
			$this->updateSize();
			return $key;
		}
		return false;
	}

	public function removeFirst($value)
	{
		if (($key = array_search($value, $this->data)) !== false) {
			$this->remove($key);
			return true;
		}
		return false;
	}

	public function removeAll($value)
	{
		while ($this->removeFirst($value)) {}
	}

	public function removeArray(array $arg)
	{
		foreach ($arg as $value) {
			$this->removeFirst($value);
		}
	}

	public function removeWhere(callable $callback)
	{
		$where = $this->where($callback);
		$array = $where->get();
		$this->removeArray($array);
	}

	public function append($value)
	{
		$this->data[] = $value;
		$this->lastInsertedKey = $this->lastKey();
		$this->updateSize();
	}

	public function count()
	{
		return $this->size;
	}

	public function select(callable $sel)
	{
		return $this->newInstance(array_map($sel, $this->data));
	}

	public function where(callable $cond)
	{
		return $this->newInstance(array_values(array_filter($this->data, $cond)));
	}

	public function order(callable $func)
	{
		$ret = array_map( function($value) {
			return $value;
		}, $this->data);
		usort($ret, $func);
		return $this->newInstance($ret);
	}

	public function join(ArrayList $other, callable $on, $required = false)
	{
		$ret = array();
		$default = NULL;
		$mark = false;
		$other_data = $other->get();
		if ($required) {
			$model = $other->get(0);
			if (is_array($model)) {
				$default = array();
				foreach ($model as $key => $value) {
					$default[$key] = NULL;
				}
			}
		}
		foreach ($this->data as $left) {
			$mark = false;
			foreach ($other_data as $right) {
				if ( $on($left, $right) ) {
					if (is_array($left) && is_array($right)) {
						$ret[] = array_merge($left, $right);
					} elseif (is_object($left) && is_object($right)) {
						$ret[] = (object)array_merge((array)$left, (array)$right);
					} else {
						$ret[] = array($left, $right);
					}
					$mark = true;
					break;
				}
			}
			if (!$mark && $required) {
				if (is_array($left)) {
					$ret[] = array_merge($left, array_diff_key($default, $left));
				} else {
					$ret[] = array($left, NULL);
				}
			}
		}
		return $this->newInstance($ret);
	}

	public function interval($begin, $length=0)
	{
		$array = array();
		$current_end = $this->count() - 1;

		if ($begin < 0) {
			$begin += $current_end;
		}

		if ($length <= 0) {
			$length = $current_end + $length - $begin;
		}

		$end = $begin + $length;

		if ($end > $current_end) {
			$end = $current_end + 1;
		}

		for($i = $begin; $i < $end; $i++) {
			$array[] = $this->data[$i];
		}

		return $this->newInstance($array);
	}
}