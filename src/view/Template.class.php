<?php

namespace Tonight\View;

class Template {

	private $filename;

	public function __construct(string $filename) {
		$this->setFileName($filename);
	}

	public function setFileName(string $filename) { $this->filename = $filename; }
	public function getFileName() { return $this->filename; }

	private static function loadFile(string $filename, $data = array(), $content = NULL) {
		if(file_exists($filename)) {
			$page = file_get_contents($filename);
			if($content) {
				$page = str_replace("%{content}%", self::loadFile($content, $data, NULL), $page);
			}
			foreach ($data as $key => $value) {
				$page = str_replace("{{".$key."}}", $value, $page);
			}
			$page = str_replace("\\{", "{", $page);
			$page = str_replace("\\\\", "\\", $page);
			return $page;
		}
		return "";
	}

	public function load($data = array(), string $content = NULL) {
		return self::loadFile($this->filename, $data, $content);
	}

	public function render($data = array(), string $content = NULL) {
		$page = $this->load($data, $content);
		echo $page;
	}
}