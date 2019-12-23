<?php

namespace Tonight\MVC;

use Tonight\View\Template;

class Controller {

	private $template;

	public function __construct($template = NULL) {
		$this->setTemplate($template);
	}

	public function setTemplate(Template $template) {
		$this->template = $template ?? new Template;
	}

	public function setVariable(string $key, $value) {
		$this->template->setVariable($key, $value);
	}

	public function renderView(string $page) {
		$this->template->require('public/pages/'.$page.'.php');
	}
}