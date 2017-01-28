<?php 
class mySingleton extends myClass {
	protected static $object = null;

	public static function getInstance($properties = array()) {
		if(static::$object == null) {
			$class_name = get_called_class();
			static::$object = new $class_name($properties);
			static::$object->initialize($properties);
		}

		return static::$object;
	}

	public function initialize($properties = array ()) {

	}

	public static function gI($properties = array ()) {
		return static::getInstance($properties);
	}
}

return 'mySingleton';