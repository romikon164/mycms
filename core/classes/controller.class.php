<?php 
class myBaseController extends myClass {
	protected $_actions = array ();
	protected $_currentAction = null;

	public function initialize($properties = array ()) {
		if(!array_key_exists('template_path', $this->properties)) {
			$this->properties['template_path'] = $this->getTemplatePath();
		}

		if($this->_currentAction === null || myCMS::gI()->response->root() !== null) {
			$this->loadAction(myCMS::gI()->response->root()->get('action'), $properties);
		}
	}

	public function render($template = null, $data) {
		if(!array_key_exists('fullname', $this->properties) || $this->_currentAction == null) { return ; }
		$this->response = myCMS::gI()->response;
		extract($data);
		$template_path = myCMS::gI()->load->template($this->properties['fullname'], $this->_currentAction->__ACTION_NAME__, null, false);
		if($template_path) {
			$this->response->renderStart();
			require $template_path;
			return $this->response->renderStop();
		}
	}

	public function loadAction($action = null, $properties = array ()) {
		if(!array_key_exists($action, $this->_actions)) {
			if(array_key_exists($action, $this->properties['actions'])) {
				$action_class_name = $this->properties['actions'][$action];
				$this->_actions[$action] = new $action_class_name($this);

				$cmp = explode('.', $action);
				$this->_actions[$action]->__ACTION_NAME__ = end($cmp);
			} else {
				return false;
			}
		}

		$this->_currentAction = $this->_actions[$action];
		return $this->_actions[$action]->initialize($properties);
	}

	public function checkPermission () {
		return myCMS::gI()->user->checkPermission();
	}

	public function process ($properties = array ()) {
		$this->initialize($properties);
		if($this->checkPermission() && $this->_currentAction !== null && $this->_currentAction->checkPermission()) {
			$response = $this->_currentAction->run();
			return $this->prepareResponse($response);
		} else {
			/* permission denied handler */
			return "";
		}
	}

	public function prepareResponse($response) {
		myCMS::gI()->response->setHttpResponseCode($response['code']);
		return $this->render($this->properties['template_path'], $response['data']);
	}

	public function action () {
		return $this->_currentAction;
	}

	public function getTemplatePath() {
		return dirname(__FILE__).'/'.$this->getTemplate().'/';
	}

	public function getTemplate() {
		return 'default';
	}
}

return 'myBaseController';