<?php 
class myLoader extends myClass {
	const L_DEFAULT = '0x00'; // require not return
	const L_RETURNED = '0x01'; // require with return 
	const L_ONCE = '0x02'; // require_once not return 
	const L_ALL = '0x04'; // require_once with return

	private $libs = array ();
	private $classes = array ();
	private $plugins = array ();
	private $components = array ();
	private $models = array ();

	private $namespace = null;
	private $namespace_path = null;

	function __construct($properties = array ()) {
		parent::__construct($properties);

		$this->namespace = 'core';
		$this->namespace_path = CMS_CORE_PATH;
	}

	public function library($name, $properties = array(), $path = null) {
		$result = null;
		$_namespace = $this->namespace;
		$_namespace_path = $this->namespace_path;

		$fullname = $this->getComponentFullname($name);
		$shortname = $this->getComponentShortname($name);
		if(!array_key_exists($fullname, $this->libs)) {
			$this->useComponentNamespace($fullname);
			$path = $this->getComponentPath($fullname, 'libs', $path);

			$lib_config = $this->file('config.php', $path, myLoader::L_RETURNED);
			$lib_required = $this->file('required.php', $path, myLoader::L_RETURNED);

			if(is_array($lib_required)) {
				foreach($lib_required as $type_required => $required_list) {
					switch($type_required) {
						case 'classes':
							if(is_array($required_list)) {
								foreach($required_list as $required_item) {
									$this->myclass($required_item);
								}
							}
							break;
						case 'libs':
							if(is_array($required_list)) {
								foreach($required_list as $required_item) {
									$this->library($required_item);
								}
							}
							break;
						case 'includes':
							if(is_array($required_list)) {
								foreach($required_list as $required_item) {
									$this->file($required_item);
								}
							}
							break;
					}
				}
			}

			if(is_array($lib_config) && is_array($properties)) {
				$properties = array_merge($lib_config, $properties);
			}

			$lib_data = $this->file('main.php', $path, myLoader::L_RETURNED);

			if(is_array($lib_data)) {
				foreach($lib_data as $type_data => $data_list) {
					switch($type_data) {
						case 'classes':
							if(is_array($data_list)) {
								foreach($data_list as $data_item) {
									$this->myclass($data_item, null, $path.'classes/');
								}
							}
							break;
						case 'includes':
							if(is_array($data_list)) {
								foreach($data_list as $data_item) {
									$this->file($data_item.'.inc.php', $path.'includes/');
								}
							}
							break;
					}
				}

				if(is_array($properties) && array_key_exists('main_class', $lib_data)) {
					$class_name = $this->getComponentFullname($lib_data['main_class']);
					if(array_key_exists($class_name, $this->classes)) {
						$class_name = $this->classes[$class_name];
					}
					if(class_exists($class_name)) {
						$result = new $class_name($properties);
					}
				}
			}
		}		

		$this->namespace = $_namespace;
		$this->namespace_path = $_namespace_path;

		return $result;
	}

	public function myclass($name, $properties = null, $path = null) {
		$fullname = $this->getComponentFullname($name);
		$shortname = $this->getComponentShortname($name);
		if(!array_key_exists($fullname, $this->classes)) {
			$path = $this->getComponentPath($fullname, 'classes', $path);

			$class_name = $this->file($shortname.'.class.php', $path, myLoader::L_ALL);

			$this->classes[$fullname] = $class_name;
		} else {
			$class_name = $this->classes[$fullname];
		}

		if($properties !== null && class_exists($class_name)) {
			return new $class_name($properties);
		} else {
			return $class_name;
		}
	}

	public function component($name, $properties = array(), $path = null) {

	}

	public function getModelNameByClassName($name) {
		foreach($this->models as $model_name => $map) {
			if(array_key_exists('class', $map) && $map['class'] == $name) {
				return $model_name;
			}
		}

		return false;
	}

	public function model($name, $properties = array(), $path = null) {
		$_namespace = $this->namespace;
		$_namespace_path = $this->namespace_path;

		$fullname = $this->getComponentFullname($name);
		if(!array_key_exists($fullname, $this->models)) {
			if($alias = $this->getModelNameByClassName($name)) {
				$fullname = $alias;
				$name = $this->getComponentShortname($alias);
			}
		}

		$shortname = $this->getComponentShortname($fullname);
		if(!array_key_exists($fullname, $this->models)) {
			$this->useComponentNamespace($fullname);
			$path = $this->getComponentPath($fullname, 'models', $path);

			$map = $this->file('map.php', $path, myLoader::L_RETURNED);

			if(is_array($map)) {
				if(array_key_exists('parent', $map)) {
					$map_parent = $this->model($map['parent']);
					if(!is_array($map_parent)) {
						$map_parent = array ();
					}
				} else {
					$map_parent = array ();
				}

				$fields = array_merge(  array_key_exists('fields', $map_parent) ? $map_parent['fields'] : array (), 
										array_key_exists('fields', $map) ? $map['fields'] : array ()  );
				$master = array_merge(  array_key_exists('master', $map_parent) ? $map_parent['master'] : array (), 
										array_key_exists('master', $map) ? $map['master'] : array ()  );
				$slave = array_merge(  array_key_exists('slave', $map_parent) ? $map_parent['slave'] : array (), 
										array_key_exists('slave', $map) ? $map['slave'] : array ()  );

				$map = array_merge($map_parent, $map);
				$map['fields'] = $fields;
				$map['master'] = $master;
				$map['slave'] = $slave;
				$map['class'] = $model_class = $this->getComponentShortname($this->file('model.php', $path, myLoader::L_RETURNED));

				$result = $this->models[$fullname] = $map;
			} else {
				$result = null;
			}
		} else {
			$result = $this->models[$fullname];
		}

		$this->namespace = $_namespace;
		$this->namespace_path = $_namespace_path;

		return $result;
	}

	public function plugin($name, $properties = array(), $path = null) {
		$result = null;

		$_namespace = $this->namespace;
		$_namespace_path = $this->namespace_path;

		$fullname = $this->getComponentFullname($name);
		$shortname = $this->getComponentShortname($name);
		if(!array_key_exists($fullname, $this->plugins)) {
			$this->useComponentNamespace($fullname);
			$path = $this->getComponentPath($fullname, 'plugins', $path);

			$plugin_config = $this->file('config.php', $path, myLoader::L_RETURNED);
			$plugin_required = $this->file('required.php', $path, myLoader::L_RETURNED);
			$plugin_events = array ();

			if(is_array($plugin_required)) {
				foreach($plugin_required as $type_required => $required_list) {
					switch($type_required) {
						case 'classes':
							if(is_array($required_list)) {
								foreach($required_list as $required_item) {
									$this->myclass($required_item);
								}
							}
							break;
						case 'libs':
							if(is_array($required_list)) {
								foreach($required_list as $required_item) {
									$this->library($required_item);
								}
							}
							break;
						case 'includes':
							if(is_array($required_list)) {
								foreach($required_list as $required_item) {
									$this->file($required_item);
								}
							}
							break;
					}
				}
			}

			if(is_array($plugin_config) && is_array($properties)) {
				$properties = array_merge($plugin_config, $properties);
			}

			$plugin_data = $this->file('main.php', $path, myLoader::L_RETURNED);

			if(is_array($plugin_data)) {
				foreach($plugin_data as $type_data => $data_list) {
					switch($type_data) {
						case 'classes':
							if(is_array($data_list)) {
								foreach($data_list as $data_item) {
									$this->myclass($data_item, null, $path.'classes/');
								}
							}
							break;
						case 'events':
							if(is_array($data_list)) {
								foreach($data_list as $data_item) {
									//$this->myclass($data_item, null, $path.'events/');
									$event_path = $path.'events/'.strtolower($data_item).'.event.php';
									if(file_exists($event_path)) {
										$plugin_events[$data_item] = $event_path;
									}
								}
							}
							break;
						case 'includes':
							if(is_array($data_list)) {
								foreach($data_list as $daa_item) {
									$this->file($data_item, $path.'includes/');
								}
							}
							break;
					}
				}

				$this->plugins[$fullname] = $plugin_events;

				$result = $plugin_events;
			}
		}

		$this->namespace = $_namespace;
		$this->namespace_path = $_namespace_path;

		return $result;
	}

	public function event($plugin_name, $event_name, $properties = array (), $path = null) {
		$_namespace = $this->namespace;
		$_namespace_path = $this->namespace_path;

		$plugin_fullname = $this->getComponentFullname($plugin_name);
		$plugin_shortname = $this->getComponentShortname($plugin_name);

		if(!array_key_exists($plugin_fullname, $this->plugins)) {
			$events = $this->plugin($plugin_fullname, array(), $path);

		} else {
			$events = $this->plugins[$plugin_fullname];
		}

		if(is_array($events) && array_key_exists($event_name, $events)) {
			$result = $this->file($events[$event_name], null);
			if($result === null) {
				$result = true;
			}
			$properties['plugin_success'] = $result;
		} else {
			$properties['plugin_success'] = false;
		}

		$this->namespace = $_namespace;
		$this->namespace_path = $_namespace_path;

		return $properties;
	}

	public function file($name, $path = null, $mode = myLoader::L_DEFAULT) {
		$path = $path == null ? $name : $path.$name;
		$path = realpath($path);
		
		if(empty($path) || !file_exists($path)) {
			return null;
		}

		switch($mode) {
			case myLoader::L_RETURNED:
				return require $path;
			case myLoader::L_ONCE:
				require_once $path;
				break;
			case myLoader::L_ALL:
				return require_once $path;
			default:
				require $path;
		}
	}

	public function setNamespace($namespace, $path = null) {
		$this->namespace = $namespace;
		if($path === null) {
			$path = $this->getNamespacePath($namespace);
		}

		$this->namespace_path = $path;
	}

	public function getNamespace() {
		return $this->namespace;
	}

	public function getNamespacePath($namespace = null) {
		if($namespace === null) {
			return $this->namespace_path;
		} else {
			if($namespace == 'core') {
				return $this->properties['core_path'];
			} else {
				return $this->properties['namespace_path'].$namespace.'/';
			}
		}
	}

	public function getPath($type = 'namespace', $namespace = null) {
		switch($type) {
			case 'classes':
				$path = $this->getNamespacePath($namespace).'classes/';
				break;
			case 'libs':
				$path = $this->getNamespacePath($namespace).'libs/';
				break;
			case 'models':
				$path = $this->getNamespacePath($namespace).'models/';
				break;
			case 'plugins':
				$path = $this->getNamespacePath($namespace).'plugins/';
				break;
			default:
				$path = $this->getNamespacePath($namespace);
		}

		return $path;
	}

	public function getComponentPath($name, $type, $path = null) {
		if($path === null) {
			$name = strtolower($name);
			$nameChunks = explode('.', $name);
			$name = array_pop($nameChunks);
			$namespace = array_pop($nameChunks);
			switch($type) {
				case 'classes':
						$path = $this->getPath($type, $namespace);
					break;
				case 'libs':
				case 'models':
				case 'plugins':
				default:
					$path = $this->getPath($type, $namespace).$name.'/';
			}
		}

		return $path;
	}

	public function getComponentFullname($name) {
		if(strpos($name, '.') === false) {
			$name = $this->getNamespace().'.'.$name;
		}

		return $name;
	}

	public function getComponentShortname($name) {
		return end(explode('.', $name));
	}

	public function useComponentNamespace($fullname) {
		$this->setNamespace(array_shift(explode('.', $fullname)));
	}
}

return 'myLoader';