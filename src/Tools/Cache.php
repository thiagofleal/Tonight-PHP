<?php

namespace Tonight\Tools;

class Cache
{
	private $filename;
	private $data;
	private $type = 0;

	private function __construct(string $filename)
	{
		$this->filename = $filename;
	}

	public static function file(string $filename)
	{
		static $files;

		if (!isset($files)) {
			$files = array();
		}
		foreach ($files as $item) {
			if($item['name'] == $filename) {
				return $item['value'];
			}
		}
		$value = new self($filename);
		$files[] = [ 'name' => $filename, 'value' => $value ];
		return $value;
	}

	private function putContents($contents)
	{
		$dir = explode("/", $this->filename);
		array_pop($dir);
		$dir = implode("/", $dir);

		if (!is_dir($dir)) {
			mkdir($dir);
		}
		
		file_put_contents($this->filename, $contents);
	}

	public function set(string $id, $value)
	{
		$this->data[$id] = $value;
		if ($this->type != 1) {
			$this->data = array();
			$this->type = 1;
		}
		$this->putContents(json_encode($this->data));
	}

	public function get(string $id)
	{
		$value = NULL;
		if (file_exists($this->filename) && $this->type != 2) {
			$this->data = json_decode(file_get_contents($this->filename));

			if (isset($this->data[$id])) {
				$value = $this->data[$id];
			}
		}
		return $value;
	}

	public function requirePage(string $page, $time = 0)
	{
		if (!file_exists($this->filename) || $this->type == 1 || ($time && $time < time() - $this->time())) {
			$this->type = 2;
			ob_start();
			require $page;
			$this->data = ob_get_contents();
			ob_end_clean();
			$this->putContents($this->data);
		}
		require $this->filename;
	}

	public function time()
	{
		if (file_exists($this->filename)) {
			return filemtime($this->filename);
		}
	}
}