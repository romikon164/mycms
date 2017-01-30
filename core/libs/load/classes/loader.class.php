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
	private $controllers = array ();

	private $namespace = null;
	private $namespace_path = null;

	function __construct($properties = array ()) {
		parent::__construct($properties);

		$this->namespace = 'core';
		$this->namespace_path = CMS_CORE_PATH;
	}

	private function load_required($required_file, $path = null) {
		if(is_array($required_file)) {
			$required = $required_file;
		} else {
			$required = $this->file($required_file, $path, myLoader::L_RETURNED);
			$path = null;
		}

		if(is_array($required)) {
			foreach($required as $type_required => $required_list) {
				switch($type_required) {
					case 'classes':
						if(is_array($required_list)) {
							foreach($required_list as $required_item) {
								$this->myclass($required_item, null, $path);
							}
						}
						break;
					case 'libs':
						if(is_array($required_list)) {
							foreach($required_list as $required_item) {
								$this->library($required_item, null, $path);
							}
						}
						break;
					case 'includes':
						if(is_array($required_list)) {
							foreach($required_list as $required_item) {
								$this->file($required_item, $path);
							}
						}
						break;
				}
			}

			return true;
		} else {
			return false;
		}
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
			$lib_required = $this->load_required('required.php', $path);

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

				if(array_key_exists('main_class', $lib_data)) {
					$class_name = $this->getComponentFullname($lib_data['main_class']);
					if(array_key_exists($class_name, $this->classes)) {
						$this->libs[$fullname] = $class_name = $this->classes[$class_name];
					}
					if(class_exists($class_name) && is_array($properties)) {
						$this->libs[$fullname] = $result = new $class_name($properties);
					}
				} else {
					$result = true;
				}
			}
		} else {
			if(is_array($properties) && is_string($this->libs[$fullname]) && class_exists($this->libs[$fullname])) {
				$this->libs[$fullname] = $result = new $class_name($properties);
			}
			
			$result = $this->libs[$fullname];
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
			return $class_name ? $class_name : false;
		}
	}

	public function component($name, $properties = array(), $path = null) {
		$fullname = $this->getComponentFullname($name);
		$shortname = $this->getComponentShortname($name);
	}

	public function controller($name, $properties = array (), $path = null) {
		$result = null;
		$_namespace = $this->namespace;
		$_namespace_path = $this->namespace_path;

		$fullname = $this->getComponentFullname($name);
		$shortname = $this->getComponentShortname($name);
		if(!array_key_exists($fullname, $this->controllers)) {
			$this->useComponentNamespace($fullname);
			$path = $this->getComponentPath($fullname, 'controllers', $path);

			$controller_config = $this->file('config.php', $path, myLoader::L_RETURNED);
			$controller_required = $this->load_required('required.php', $path);

			if(is_array($controller_config) && is_array($properties)) {
				$controller_config = array_merge($controller_config, $properties);
			}

			$controller_data = $this->file('main.php', $path, myLoader::L_RETURNED);
			$controller_config['actions'] = array ();
			$controller_config['shortname'] = $shortname;
			$controller_config['fullname'] = $fullname;

			if(is_array($controller_data)) {
				foreach($controller_data as $type_data => $data_list) {
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
						case 'actions':
							if(is_array($data_list)) {
								foreach($data_list as $data_item) {
									if($action_class = $this->file($data_item.'.action.php', $path.'actions/', myLoader::L_RETURNED)) {
										$controller_config['actions'][$data_item] = $action_class;
									}
								}
							}
							break;
					}
				}

				if( is_array($controller_config) && 
					array_key_exists('parent', $controller_config) && 
					$controller_config['parent']) {
					$parent_config = $this->controller($controller_config['parent'], null);
					if($parent_config) {
						if(is_array($parent_config) && array_key_exists('actions', $parent_config)) {
							$controller_config['actions'] = array_merge($parent_config['actions'], $controller_config['actions']);
						}
					} else {
						/*trigger_error('Не найден родительский контроллер: '.$controller_config['parent']);*/
					}
				}

				if(is_array($controller_config) && array_key_exists('type', $controller_config)) {
					$class_name = $this->getComponentFullname($controller_config['type']);
					if(array_key_exists($class_name, $this->classes)) {
						$controller_config['controller_object'] = $class_name = $this->classes[$class_name];
					}
					if(class_exists($class_name) && is_array($properties)) {
						$result = $controller_config['controller_object'] = new $class_name($controller_config);
						$this->controllers[$fullname] = $controller_config;
					} else {
						$result = class_exists($class_name) ? ($this->controllers[$fullname] = $controller_config) : null;
					}
				}
			}
		} else {
			$controller_config = $this->controllers[$fullname];
			if(is_array($properties)) {
				$controller_config = $properties = array_merge($controller_config, $properties);
			}
			if( is_array($properties) && 
				isset($this->controllers[$fullname]['controller_object']) &&
				is_string($this->controllers[$fullname]['controller_object']) && 
				class_exists($this->controllers[$fullname]['controller_object'])) {
				$this->controllers[$fullname]['controller_object'] = $result = new $class_name($properties);
			}
			
			$result = $this->controllers[$fullname]['controller_object'];
		}	

		$this->namespace = $_namespace;
		$this->namespace_path = $_namespace_path;
		return $result;
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

			$plugin_required = $this->load_required('required.php', $path);

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
			$result = $this->file($events[$event_name], null, myLoader::L_RETURNED);
			$properties['plugin_success'] = $result;
		} else {
			$properties['plugin_success'] = false;
		}

		$this->namespace = $_namespace;
		$this->namespace_path = $_namespace_path;

		return $properties;
	}

	public function template($controller = null, $action = null, $path = null, $is_included = true) {
		$root = myCMS::gI()->response->root();
		if($controller == null && $root != null) {
			$controller = $root->get('controller');
		}

		if($action == null && $root != null) {
			$action = $root->get('default_action');
		}

		if($path == null) {
			$path = $this->getControllerDefaultTemplateDir($controller);
		}

		if($is_included) {
			$this->file($action.'.tpl', $path);
		} else {
			$path = $path.$action.'.tpl';
			return file_exists($path) ? $path : null;
		}
	}

	public function getControllerDefaultTemplateDir($controller) {
		$controller = $this->getComponentFullname($controller);
		list($namespace, $controller) = explode('.', $controller);
		return $this->getComponentPath($controller, 'controllers').'templates/';
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
			case myLoader::L_ALL:
				return require_once $path;
			default:
				require $path;
		}

		return true;
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
			case 'controllers':
				$path = $this->getNamespacePath($namespace).'controllers/';
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
				case 'controllers':
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