<?php 
class myBaseController extends myClass {
	private $_dynamicVariables = array ();
	private $_data = array ();
	private $_actions = array ();

	public function render($template = null) {

	}

	public function callAction($action = null, $args = array ()) {
		if
	}

	public function getRequiredParameters($action = null) {
		return array ();
	}

	public function setDynamicVariable($name, $value) {

	}

	public function getDynamicVariable($name) {

	}

	public function renderDynamicVariable($name) {

	}

	public static function load($controller_name, $properties = array (), $path = null) {
		myCMS::gI()->load->controller($controller_name, $properties, $path);
	}
}

return 'myBaseController';