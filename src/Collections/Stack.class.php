<?php

namespace Tonight\Collections;

class Stack {

	private $data;
	private $size;

	private function updateSize() {
		$this->size = count($this->data);
	}

	public function __construct(array $data = array()) {
		$this->setData($data);
	}

	public function setData(array $data) {
		$this->data = $data;
		$this->updateSize();
	}

	public function get() {
		return array_pop($this->data);
	}

	public function add($value) {
		$this->data[] = $value;
		$this->updateSize();
	}

	public function size() {
		return $this->size;
	}
}