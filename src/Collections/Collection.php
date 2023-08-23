<?php

namespace Tonight\Collections;

use Iterator;

abstract class Collection implements Iterator
{
	public abstract function current();
	public abstract function key();
	public abstract function next();
	public abstract function rewind();
	public abstract function valid();

	public abstract function newInstance($data);

	public function forEach(callable $func) {
		$this->rewind();
		while ($this->valid()) {
			$key = $this->key();
			$value = $this->current();
			$func($key, $value);
			$this->next();
		}
	}
}
