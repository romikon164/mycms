<?php 
class myLang extends myRegistry {
	private $current_namespace = '';
	private $default_path = '';
	private $currentLanguage = '';

	function __construct($properties = array ()) {
		parent::__construct($properties);
		$this->default_path = $this->properties['lang_path'];
		$this->currentLanguage = $this->properties['lang_default'];

		$this->load();
	}

	public function get($key) {
		$key = $this->current_namespace.$key;
		if(array_key_exists($key, $this->properties)) {
			return $this->properties[$key];
		} else {
			return $key;
		}
	}
	public function set($key, $value) {} 

	public function load($path = null, $namespace = '') {
		if(!$path) {
			$path = $this->default_path;
		}

		$namespace_path = $namespace;
		if($namespace_path == '') {
			$namespace_path = 'core';
		}

		if($namespace != '') {
			$namespace .= ':';
		} else {
			$namespace = $this->current_namespace;
		}

		$_lang = myCMS::gI()->load->file($namespace_path.'/'.$this->currentLanguage.'.php', $path, myLoader::L_RETURNED);
		if(is_array($_lang)) {
			foreach($_lang as $key => $value) {
				$this->properties[$namespace.$key] = $value;
			}
		}
	}

	public function use_namespace($namespace = '') {
		$this->$current_namespace = $namespace;
		if($this->$current_namespace != '') {
			$this->$current_namespace .= ':';
		}
	}

	public function switchLanguage($lang) {
		$this->currentLanguage = $lang;
	}
}

return 'myLang';