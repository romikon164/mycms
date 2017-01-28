<?php
class myCMS extends mySingleton {
	private $events = array ();
	private $namespaces = null;
	private $plugins_enabled = null;
	private $plugins_disabled = null;

	public function initialize($properties = array ()) {
		$this->load = $this->properties['loader_object'];
		$this->log = $this->load->library('core.log');
		$this->db = $this->load->library('core.db');
		$this->model = $this->load->library('core.model');
		session_start();
		$this->load_settings();
		$this->load_plugins();
		$this->load_user();
		$this->lang = $this->load->library('core.lang');
		$this->request = $this->load->library('core.request');
		$this->response = $this->load->library('core.response');
	}

	public function run() {
		$this->invokeEvent('OnInitialize');
		$this->request->handleRequest();
	}

	public function invokeEvent($event_name, $properties = array ()) {
		if(array_key_exists($event_name, $this->events)) {
			foreach($this->events[$event_name] as $plugin) {
				$properties = $this->load->event($plugin, $event_name, $properties);
			}
		}

		return $properties;
	}

	public function load_settings() {
		foreach($this->model->loadData('option') as $option) {
			$this->properties[$option['namespace'].'.'.$option['name']] = $option['value'];
		}
	}

	public function load_plugins($load_disabled = false) {
		foreach($this->getPlugins(true, $load_disabled) as $plugin_name) {
			$events = $this->load->plugin($plugin_name);
			if(is_array($events)){
				foreach($events as $event_name => $event_path) {
					if(!array_key_exists($event_name, $this->events)) {
						$this->events[$event_name] = array ();
					}

					$this->events[$event_name][] = $plugin_name;
				}
			}
		}
	}

	public function load_user() {
		$this->user = $this->model->create('user');
		if(!$this->user->authenticate()) {
			$this->user->fromArray(array (
				'id' => 0,
				'username' => 'anonymous',
				'active' => true,
			));
		}
	}

	public function getNamespaces() {
		if($this->namespaces === null) {
			$this->namespaces = array('core');
			foreach(scandir(CMS_NAMESPACE_PATH) as $dir) {
				if(is_dir(CMS_NAMESPACE_PATH.$dir) && $dir != '.' && $dir != '..') {
					$this->namespaces[] = $dir;
				}
			}
		}

		return $this->namespaces;
	}

	public function getPlugins($return_enabled = true, $return_disabled = true) {
		if($this->plugins_enabled === null || $this->plugins_disabled === null) {
			$this->plugins_enabled = array ();
			$this->plugins_disabled = array ();
			foreach($this->getNamespaces() as $namespace) {
				$path = $this->load->getPath('plugins', $namespace);
				foreach(scandir($path) as $dir) {
					if(is_dir($path.$dir) && $dir != '.' && $dir != '..') {
						$plugin_name = $namespace.'.'.$dir;
						if(strpos($dir, '_') !== 0) {
							$this->plugins_disabled[] = $plugin_name;
						} else {
							$this->plugins_enabled[] = $plugin_name;
						}
					}
				}
			}
		}

		$plugins = $return_enabled ? $this->plugins_enabled : array();
		if($return_disabled) {
			$plugins = array_merge($plugins, $this->plugins_disabled);
		}

		return $plugins;
	}

	public function getOption($name, $default = null) {
		$name = $this->load->getComponentFullname($name);
		return array_key_exists($name, $this->properties) ? $this->properties[$name] : $default;
	}

	public function signIn($username, $password) {
		$result = false;
		$this->logout();
		$date = date('Y-m-d H:i:s');
		$user = $this->model->getOne('user', array (
			'username' => $username,
		));

		if($user) {
			if($user->password == $user->generatePassword($password)) {
				$user->loggedon = $date;
				$user->token = $user->generateToken();
				$user->save();
				$user->rememberToSession();
				$this->load_user();
				$result = true;
			}
		}

		return $result;
	}

	public function logout() {
		$this->user->removeFromSession();
		$this->load_user();
	}

	public function signUp($data = array ()) {
		$data['createdon'] = date('Y-m-d H:i:s');
		$data['password'] = $this->user->generatePassword($data['password'], $data['createdon']);
		$user = $this->model->create('user', $data);
		$user->save();
	}
}

return 'myCMS';