<?php
class myRegistry extends myClass {
	function __construct($properties = array ()) {
		parent::__construct($properties);
		$this->fromArray($properties);
	}

	function __set($key, $value) {
		$method_name = 'set'.ucfirst($key);
		if(method_exists($this, $method_name)) {
			$result = call_user_func(array ($this, $method_name), $value);
			if($result !== null) {
				$this->properties[$key] = $result;
			}
		} else {
			$this->properties[$key] = $value;
		}
	}

	function __get($key) {
		if(array_key_exists($key, $this->properties)) {
			$result = $this->properties[$key];
		} else {
			$result = null;
		}

		$method_name = 'get'.ucfirst($key);
		if(method_exists($this, $method_name)) {
			$result = call_user_func(array ($this, $method_name), $result);
		}

		return $result;
	}

	public function toArray() {
		$data = array ();
		foreach($this->properties as $key => $value) {
			$data[$key] = $this->__get($key);
		}
		return $data;
	}

	public function fromArray($properties) {
		if(is_array($properties)) {
			foreach($properties as $key => $value) {
				$this->__set($key, $value);
			}
			$this->properties = $properties;
		}
	}

	public function toJSON() {
		return @json_encode($this->toArray());
	}

	public function fromJSON($json) {
		$this->fromArray(@json_decode($json, true));
	}

	public function get($key) {
		return $this->__get($key);
	}

	public function set($key, $value) {
		$this->__set($key, $value);
	}
}

return 'myRegistry';