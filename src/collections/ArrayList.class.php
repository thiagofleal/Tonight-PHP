<?php

namespace Tonight\Collections;

class ArrayList {

	private $data;
	private $size;

	private function updateSize() {
		$this->size = count($this->data);
	}

	/* Public interface */
	public function __construct(array $data = array()) {
		$this->setData($data);
	}

	public function setData(array $data) {
		$this->data = $data;
		$this->updateSize();
	}

	public function get($key = NULL) {
		return $key ? $this->data[$key] : $this->data;
	}

	public function set($key, $value) {
		$this->data[$key] = $value;
		$this->updateSize();
	}

	public function remove($key) {
		unset($this->data[$key]);
		$this->updateSize();
	}

	public function removeFirst($value) {
		if(($key = array_search($value, $this->data)) != -1) {
			$this->remove($key);
		}
	}

	public function removeAll($value) {
		while(($key = array_search($value, $this->data)) != -1) {
			$this->remove($key);
		}
	}

	public function append($value) {
		$this->data[] = $value;
	}

	public function size() {
		return $this->size;
	}

	public function copy() {
		$arr = array();
		foreach ($this->data as $key => $value) {
			$arr[$key] = $value;
		}
		return new self($arr);
	}

	public function select(callable $sel) {
		$ret = array();
		foreach($this->data as $value) {
			$ret[] = $sel( $value );
		}
		$this->setData($ret);
		return $this;
	}

	public function where(callable $cond) {
		$ret = array();
		foreach($this->data as $value) {
			if($cond( $value )) {
				$ret[] = $value;
			}
		}
		$this->setData($ret);
		return $this;
	}
}