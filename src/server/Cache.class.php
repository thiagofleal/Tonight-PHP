<?php

namespace Tonight\Server;

class Cache {

	private $filename;
	private $data;
	private $page;
	private $type;

	private function __construct(string $filename) {
		$this->filename = $filename;
		$this->data = array();
		$this->type = 0;
	}

	public static function file(string $filename) {
		static $files;

		if(!isset($files)) {
			$files = array();
		}
		foreach ($files as $item) {
			if($item['name'] == $filename) {
				return $item['value']
			}
		}
		$value = new self($filename);
		$files[] = [ 'name' => $filename, 'value' => $value ];
		return $value;
	}

	public function set(string $id, $value) {
		$this->data[$id] = $value;
		$this->page = NULL;
		$this->type = 1;
		file_put_contents($this->filename, json_encode($this->data));
	}

	public function get(string $id) {
		$value = NULL;
		if(file_exists($this->filename) && $this->type == 1) {
			$this->data = json_decode(file_get_contents($this->filename));

			if(isset($this->data[$id])) {
				$value = $this->data[$id];
			}
		}
		return $value;
	}

	public function savePage(string $page) {
		if(file_exists($page)) {
			$this->data = array();
			$this->type = 2;
			ob_start();
			require $page;
			$this->page = ob_get_contents();
			ob_end_clean();
		}
	}

	public function getPageContent() {
		if(file_exists($this->filename) && $this->type == 2) {
			return $this->page;
		}
		return NULL;
	}

	public function loadPage() {
		$page = $this->getPageContent();
		if($page) {
			echo $page;
		}
	}

	public function time() {
		if(file_exists($this->filename)) {
			return filemtime($this->filename);
		}
	}
}