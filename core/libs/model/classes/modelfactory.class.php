<?php
class myModelFactory extends myRegistry {
	protected $_converter = null;
	function __construct($properties = array ()) {
		parent::__construct($properties);
		$this->_converter = myCMS::gI()->load->myclass($this->properties['converter'], array ());
	}

	public function load($name, $properties = array (), $path = null) {
		return myCMS::gI()->load->model($name, $properties, $path);
	}

	public function loadData($model_name, $where = array (), $offset = 0, $limit = 0, $path = null) {
		$metadata = $this->load($model_name, array(), $path);
		return $metadata ? call_user_func(array($metadata['class'], '_load'), $where, $offset, $limit) : array ();
	}

	public function loadOneData($model_name, $where = array (), $path = null) {
		$data = $this->loadData($model_name, $where, 0, 1, $path);
		return count($data) ? $data[0] : null;
	}

	public function convertToSave($fields, $metadata) {
		return $this->_converter->convertToSave($fields, $metadata);
	}

	public function convertFromSave($fields, $metadata) {
		return $this->_converter->convertFromSave($fields, $metadata);
	}
	public function getTableName($model_name, $sql_format = true) {
		$metadata = $this->getMetadata($model_name);
		$prefix = $postfix = $sql_format ? '`' : '';
		$prefix .= myCMS::gI()->db->tablePrefix();
		return is_array($metadata) && array_key_exists('table', $metadata) ? $prefix.$metadata['table'].$postfix : null;
	}

	public function getMetadata($model_name, $path = null) {
		return myCMS::gI()->load->model($model_name, array (), $path);
	}

	public function getOne($model_name, $where = array (), $path = null) {
		$metadata = $this->load($model_name, array(), $path);
		return call_user_func(array($metadata['class'], 'one'), $where);
	}

	public function getList($model_name, $where = array (), $offset = 0, $limit = 0, $path = null) {
		$metadata = $this->load($model_name, array(), $path);
		return call_user_func(array($metadata['class'], 'many'), $where, $offset, $limit);
	}

	public function create($model_name, $properties = array (), $path = null) {
		$metadata = $this->load($model_name, array(), $path);
		return call_user_func(array($metadata['class'], 'create'), $properties);
	}

	public function remove($model_name, $where = array (), $path = null) {
		return $this->remove($model_name, $where, $path);
	}

	public function removeOne($model_name, $where = array (), $path = null) {
		if($model = $this->getOne($model_name, $where, $path)) {
			return $model->remove();
		} else {
			return false;
		}
	}

	public function removeList($model_name, $where = array (), $offset = 0, $limit = 0, $path = null) {
		foreach($this->getList($model_name, $where, $offset, $limit, $path) as $model) {
			$model->remove();
		}
	}
}

return 'myModelFactory';