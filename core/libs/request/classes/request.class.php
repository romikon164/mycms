<?php 
class myRequest extends myClass {
	private $_vars = null;
	private $_content = null;
	private $_headers = null;
	private $_contentTypes = null;
	private $_request_var = '__route__';
	private $_request_value = '';

	function __construct($properties = array ()) {
		parent::__construct($properties);
		$this->_vars = $this->getVariables();
		$this->_headers = $this->getHttpHeaders();
	}

	public function handleRequest() {
		myCMS::gI()->invokeEvent('OnHandleRequest');
		$this->_request_var = myCMS::gI()->getOption('core.request_get_param', '__route__');
		$this->_request_value = trim($this->getVar($this->_request_var), '/');

		return myCMS::gI()->response->handleResponse();
	}

	public function getRequest() {
		return $this->_request_value;
	}

	public function getVar($name, $method = null) {
		if($method == null) {
			$method = $this->getHttpMethod();
		}

		switch(strtoupper($method)) {
			case 'GET':
				$result = isset($_GET[$name]) ? $_GET[$name] : null;
				break;
			case 'POST':
				$result = isset($_POST[$name]) ? $_POST[$name] : null;
				break;
			default:
				$result = isset($this->_vars[$name]) ? $this->_vars[$name] : null;
		}

		return $result;
	}

	public function getVariables() {
		if($this->_vars === null) {
			parse_str($this->getRequestContent(), $this->_vars);
		}
		return $this->_vars;
	}

	public function getRequestContent() {
		if($this->_content === null) {
			$this->_content = trim(file_get_contents('php://input'));
		}
		return $this->_content;
	}

	public function getHttpHeaders() {
		if($this->_headers === null) {
			$this->_headers = getallheaders();
		}

		return $this->_headers;
	}

	public function getHttpHeader($header) {
		return isset($this->_headers[$header]) ? $this->_headers[$header] : null;
	}

	public function getHttpMethod() {
		return isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : 'GET';
	}

	public function isAjax() {
		return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
	}

	public function getContentTypes() {
		if($this->_contentTypes === null) {
			$contentType = isset($_SERVER['HTTP_ACCEPT']) ? $_SERVER['HTTP_ACCEPT'] : 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8';

			$contentTypes = array_map('trim', explode(',', $contentType));
			$this->_contentTypes = array ();
			foreach($contentTypes as $contentType) {
				$contentMeta = array_map('trim', explode(';', $contentType));
				$contentType = $contentMeta[0];
				if(count($contentMeta) > 1) {
					$contentQ = trim(end(explode('=', $contentMeta[1]))) * 100;
				} else {
					$contentQ = 100;
				}

				$this->_contentTypes[$contentType] = $contentQ;
			}

			asort($this->_contentTypes, SORT_NUMERIC);
			$this->_contentTypes = array_reverse(array_keys($this->_contentTypes));
		}

		return $this->_contentTypes;
	}

	public function getContentType() {
		return reset($this->getContentTypes());
	}

	public function issetContentType($type) {
		return in_array($type, $this->getContentTypes());
	}

	public function get($name) {
		return $this->getVar($name, 'get');
	}

	public function post($name) {
		return $this->getVar($name, 'post');
	}

	public function session($name) {
		return isset($_SESSION[$name]) ? $_SESSION[$name] : null;
	}

	public function cookie($name) {
		return isset($_COOKIE[$name]) ? $_COOKIE[$name] : null;
	}
}

return 'myRequest';