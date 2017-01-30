<?php 
class myControllerAction extends myClass {
	protected $_controller = null;
	protected $_errors = array ();

	function __construct($controller) {
		$this->_controller = $controller;
	}

	public function initialize($properties = array ()) {
		$this->properties = $properties;
		return true;
	}

	public function checkPermission () {
		$root = myCMS::gI()->response->root();
		return $root != null && $root->checkActionPermission($this->__ACTION_NAME__);
	}

	public function run () {
		if(is_object($this->_controller)) {
			return $this->process();
		};
	}

	public function process () {
		return $this->success();
	}

	public function success($code = 0, $data = array ()) {
		return array (
			'success' => true,
			'code' => $code,
			'data' => $data,
		);
	}

	public function failure($code = 0, $data = array ()) {
		return array (
			'success' => false,
			'code' => $code,
			'data' => $data,
		);
	}
}

return 'myControllerAction';