<?php 
class myResponse extends myClass {
	private $_root = null;
	private $_controller = null;
	private $_dynamicVariables = array ();
	private $_dynamicVariablesContent = array ();
	public  $_output = "";

	public function handleResponse() {
		$this->_output = "";
		if($this->detectRoot() && 
		$this->detectController() && 
		$this->detectAction()) {
			$this->renderStart();
			echo $this->_controller->process();
			$this->_output = $this->renderStopAll();
		} else {
			/* access denied handler */
			return "";
		}

		return $this->_output;
	}

	public function getPermission() {
		return $this->_root ? $this->_root->get('access_level') : 0;
	}

	public function root() {
		return $this->_root;
	}

	public function controller () {
		return $this->_controller;
	}

	public function action () {
		return $this->_controller !== null ? $this->_controller->action() : null;
	}

	public function setDynamicVariable($name, $value) {
		$this->_dynamicVariables[$name] = $value;
	}

	public function getDynamicVariable($name, $default = null) {
		return array_key_exists($name, $this->_dynamicVariables) ? $this->_dynamicVariables[$name] : $default;
	}

	public function renderDynamicVariable($name, $is_dynamic = true) {
		if($is_dynamic) {
			if(!array_key_exists($name, $this->_dynamicVariables)) {
				$this->_dynamicVariables[$name] = '';
			}

			$ob_level = ob_get_level();
			if(!array_key_exists($ob_level, $this->_dynamicVariablesContent)) {
				$this->_dynamicVariablesContent[$ob_level] = array ();
			}

			$this->_dynamicVariablesContent[$ob_level][] = array (
				'variable' => $name,
				'content' => ob_get_contents(),
			);
			ob_clean();
		} else {
			return $this->getDynamicVariable($name, '');
		}
	}

	public function renderDynamicVariables($level = null, $is_returned = false, $is_remove = false) {
		$output = "";
		if($level == null) {
			$level = ob_get_level();
		}
		if($level && array_key_exists($level, $this->_dynamicVariablesContent)) {
			foreach($this->_dynamicVariablesContent[$level] as $_dynamicVariableContent) {
				$_dynamicVariable = $this->getDynamicVariable($_dynamicVariableContent['variable'], '');
				$output .= $_dynamicVariableContent['content'].$_dynamicVariable;
			}

			if($is_remove) {
				unset($this->_dynamicVariablesContent[$level]);
			}

			if($is_returned) {
				return $output;
			} else {
				$this->render($output);
			}
		} else {
			return $output;
		}
	}

	private function detectRoot() {
		$query = myCMS::gI()->db->newQuery('core.root');
		$query->sort(array('LENGTH(`path`)' => 'DESC'));
		$roots = $query->execute();
		while($root = $roots->fetch(PDO::FETCH_ASSOC)) {
			if($root['path'] == '' || myCMS::gI()->request->getRequest() == $root['path'] || preg_match('%^'.$root['path'].'/.*%', myCMS::gI()->request->getRequest())) {
				$this->_root = myCMS::gI()->model->create('core.root', $root);
				break;
			}
		}

		return $this->_root !== null && myCMS::gI()->user->checkPermission($this->_root->get('access_level') * 100);
	}

	private function detectController() {
		if($this->_root !== null) {
			$this->_controller = myCMS::gI()->load->controller($this->_root->get('controller'));
			return $this->_controller !== null && $this->_controller->checkPermission();
		} else {
			return false;
		}
	}

	private function detectAction() {
		if($this->_root !== null && $this->_controller !== null) {
			$actionVar = myCMS::gI()->getOption('request_action_var', '__action__');
			$actionValue = myCMS::gI()->request->get($actionVar);
			$this->_controller->loadAction($actionValue !== null ? $actionValue : $this->_root->get('default_action'));
			return $this->_controller->action() !== null && $this->_controller->action()->checkPermission();
		} else {
			return false;
		}
	}

	public function render($content) {
		echo $content;
	}

	public function setHttpResponseCode($code = 200) {
		http_response_code($code);
	}

	public function setContentType($contentType = 'text/html', $charset = 'utf-8') {
		$this->setHttpHeader('Content-type: '.$contentType.'; charset='.$charset);
	}

	public function setHttpHeader($header) {
		header($header);
	}

	public function setTitle($title) {
		$this->setDynamicVariable('html_meta_title', $title);
	}

	public function setDescription($description) {
		$this->setDynamicVariable('html_meta_description', $description);
	}

	public function setKeywords($keywords) {
		$this->setDynamicVariable('html_meta_keywords', $keywords);
	}

	public function getTitle() {
		return $this->getDynamicVariable('html_meta_title');
	}

	public function getDescription() {
		return $this->getDynamicVariable('html_meta_description');
	}

	public function getKeywords() {
		return $this->getDynamicVariable('html_meta_keywords');
	}

	public function renderTitle($is_dynamic = true) {
		return $this->renderDynamicVariable('html_meta_title', $is_dynamic);
	}

	public function renderDescription($is_dynamic = true) {
		return $this->renderDynamicVariable('html_meta_description', $is_dynamic);
	}

	public function renderKeywords($is_dynamic = true) {
		return $this->renderDynamicVariable('html_meta_keywords', $is_dynamic);
	}

	public function addHtml($html, $toBody = true) {
		$this->setDynamicVariable($toBody ? 'html_scripts' : 'html_startup_scripts', $html);
	}

	public function addScript($script, $type = 'js', $toBody = true) {
		switch($type) {
			case 'js':
			case 'javascript':
			case 'text/javascript':
			case 'script':
				$script = '<script type="text/javascript">'.$script.'</script>';
				break;
			case 'css':
			case 'style':
			case 'text/css':
			case 'stylesheet':
				$script = '<style>'.$script.'</style>';
				break;
			default:
				$script = '<script>'.$script.'</script>';
		}

		$this->addHtml($script);
	}

	public function includeScript($script, $type = 'js', $toBody = true) {
		switch($type) {
			case 'js':
			case 'javascript':
			case 'text/javascript':
			case 'script':
				$script = '<script type="text/javascript" src="'.$script.'"></script>';
				break;
			case 'css':
			case 'style':
			case 'text/css':
			case 'stylesheet':
				$script = '<link rel="stylesheet" href="'.$script.'">';
				break;
			default:
				$script = '<script src="'.$script.'"></script>';
		}

		$this->addHtml($script);
	}

	public function addJS($js, $toBody = true) {
		$this->addScript($js, 'js', $toBody);
	}

	public function includeJS($js, $toBody = true) {
		$this->includeScript($js, 'js', $toBody);
	}

	public function addCSS($css, $toBody = true) {
		$this->addScript($css, 'css', $toBody);
	}

	public function includeCSS($css, $toBody = true) {
		$this->includeScript($css, 'css', $toBody);
	}

	public function renderBodyScripts() {
		$this->renderDynamicVariable('html_scripts');
	}

	public function renderHeadScripts() {
		$this->renderDynamicVariable('html_startup_scripts');
	}

	public function renderStart() {
		if($ob_level = ob_get_level()) {
			if(!array_key_exists($ob_level, $this->_dynamicVariablesContent)) {
				$this->_dynamicVariablesContent[$ob_level] = array ();
			}
		}
		ob_start();
	}

	public function renderStop() {
		return $this->renderClean(true);
	}

	public function renderStopAll() {
		$content = "";
		while($ob_level = ob_get_level()) {
			$content .= $this->renderDynamicVariables($ob_level, true, true);
			$content .= ob_get_contents();
			ob_end_clean();
		}

		return $content;
	}

	public function renderClean($is_end = false) {
		if($ob_level = ob_get_level()) {
			$content = $this->renderDynamicVariables($ob_level, true, true);
			$content .= ob_get_contents();
			$is_end ? ob_end_clean() : ob_clean();
		} else {
			$content = '';
		}

		return $content;
	}

	public function renderContents() {
		return ob_get_level() ? ob_get_contents() : '';
	}
}

return 'myResponse';