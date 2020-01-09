<?php

namespace Tonight\Collections;

abstract class Collection implements \Iterator
{
	public abstract function current();
	public abstract function key();
	public abstract function next();
	public abstract function rewind();
	public abstract function valid();

	public function forEach(callable $func)
	{
		while ($this->valid()) {
			$key = $this->key();
			$value = $this->current();
			$func($key, $value);
			$this->next();
		}
		$this->rewind();
	}

	public function each()
	{
		return each($this);
	}
}