<?php

namespace Tonight\View;

class Template {

	private $filename;
	private $data;
	private $required;

	public function __construct(string $filename = '') {
		$this->setFileName($filename);
		$this->data = array();
	}

	public function getFileName() { return $this->filename; }
	public function setFileName(string $filename) {
		if(file_exists($filename)) {
			$this->filename = $filename;
		}
	}
	
	public function getData() { return $this->data; }
	public function setData(array $data) { $this->data = $data; }
	
	public function getRequired() { return $this->required; }
	public function setRequired(string $required) { $this->required = $required; }

	public function setVariable(string $varName, $value) {
		$this->data[$varName] = $value;
	}

	public function getVariable(string $varName) {
		return $this->data[$varName];
	}

	private static function loadFile(string $filename, $data = array(), $content = NULL, $ignore = false) {
		if(file_exists($filename)) {
			$page = file_get_contents($filename);
			if($content) {
				$page = str_replace("%{content}%", self::loadFile($content, $data, NULL), $page);
			}
			foreach ($data as $key => $value) {
				$page = str_replace("{{".$key."}}", $value, $page);
			}
			if($ignore) {
				$page = str_replace("{\\{", "{{", $page);
			}
			return $page;
		}
		return "";
	}

	public function load(string $content) {
		return self::loadFile($this->filename, $this->data, $content, true);
	}

	public function render(string $content) {
		$page = $this->load($content);
		echo $page;
	}

	public function require(string $file) {
		$filename = $this->getFilename();
		if(!empty($file)) {
			$this->setRequired($file);
		}
		if(file_exists($filename)) {
			extract($this->data);
			return require $filename;
		}
		return false;
	}

	public function requireView() {
		if(file_exists($this->required)) {
			extract($this->data);
			return require $this->required;
		}
		return false;
	}
}