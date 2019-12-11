<?php

namespace Tonight\Collections;

class Queue extends Collection {

	private $data;
	private $size;
	private $current;

	private function updateSize() {
		$this->size = count($this->data);
	}

	public function __construct(array $data = array()) {
		$this->setData($data);
		$this->rewind();
	}

	public function current() {
		return $this->data[array_keys($this->data)[$this->current]];
	}

	public function key() {
		return array_keys($this->data)[$this->current];
	}

	public function next() {
		$this->current++;
	}

	public function rewind() {
		$this->current = 0;
	}

	public function valid() {
		return $this->current < $this->size;
	}

	public function setData(array $data) {
		$this->data = $data;
		$this->updateSize();
	}

	public function get() {
		return array_shift($this->data);
	}

	public function add($value) {
		$this->data[] = $value;
		$this->updateSize();
	}

	public function size() {
		return $this->size;
	}
}