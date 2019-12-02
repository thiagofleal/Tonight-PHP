<?php

namespace Tonight\View;

class Template {

	private $filename;

	public function __construct(string $filename) {
		$this->setFileName($filename);
	}

	public function setFileName(string $filename) { $this->filename = $filename; }
	public function getFileName() { return $this->filename; }

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
				$page = str_replace("\\{", "{", $page);
			}
			return $page;
		}
		return "";
	}

	public function load(string $content = NULL, $data = array()) {
		return self::loadFile($this->filename, $data, $content, true);
	}

	public function render(string $content = NULL, $data = array()) {
		$page = $this->load($content, $data);
		echo $page;
	}
}