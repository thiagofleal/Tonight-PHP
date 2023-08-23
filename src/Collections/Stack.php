<?php

namespace Tonight\Collections;

class Stack extends Collection
{
	private $current = -1;
	private $data;
	private $size;

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
		$this->current--;
	}

	public function rewind()
	{
		$this->current = $this->size - 1;
	}

	public function valid()
	{
		return $this->current >= 0;
	}

	public function setData(array $data)
	{
		$this->data = $data;
		$this->updateSize();
	}

	public function get()
	{
		return array_pop($this->data);
	}

	public function add($value)
	{
		$this->data[] = $value;
		$this->updateSize();
	}

	public function size()
	{
		return $this->size;
	}
}