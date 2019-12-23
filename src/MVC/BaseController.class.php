<?php

namespace Tonight\MVC;

use Tonight\View\Template;

class BaseController {

	private $template;
	private $directory = '';
	private $extension = 'php';

	public function __construct($template = NULL) {
		$this->setTemplate($template);
	}

	public function getTemplate() { return $this->template; }
	public function setTemplate($template) {
		if($template instanceof Template) {
			$this->template = $template;
		}
		else {
			$this->template = new Template;
		}
	}

	public function getDirectory() { return $this->directory; }
	public function setDirectory(string $directory) { $this->directory = $directory; }

	public function getExtension() { return $this->extension; }
	public function setExtension(string $extendion) { $this->extendion = $extendion; }

	public function setVariable(string $key, $value) {
		$this->template->setVariable($key, $value);
	}

	public function renderView(string $page) {
		$this->template->require($this->getDirectory().'/'.$page.'.'.$this->getExtension());
	}
}