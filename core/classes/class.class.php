<?php 
class myClass {
	protected $properties = array ();

	function __construct ($properties = array ()) {
		$this->properties = $properties;
	}
}

return 'myClass';