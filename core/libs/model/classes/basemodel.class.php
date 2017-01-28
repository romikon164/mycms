<?php
class myBaseModel extends myRegistry {
	protected $allowedFields = array ();
	protected $excludeFields = array ();
	protected $errors = array ();
	protected $_masters = array ();
	protected $_slaves = array ();
	protected static $_metadata = null;

	function __construct($properties = array ()) {
		$metadata = static::getMetadata();
		$this->allowedFields = array_keys($metadata['fields']);

		$defaultFields = array ();

		$properties = array_merge($this->getDefaultFields(), $properties);

		parent::__construct($properties);
	}

	function __set($key, $value) {
		if((empty($this->allowedFields) || in_array($key, $this->allowedFields)) && !in_array($key, $this->excludeFields)) {
			parent::__set($key, $value);
		}
	}

	function __get($key) {
		$metadata = $this->getMetadata();
		if(array_key_exists($key, $metadata['master'])) {
			$is_many = isset($metadata['master'][$key]['conformity']) && $metadata['master'][$key]['conformity'] == 'many';
			return $is_many ? $this->getMany($key) : $this->getOne($key);
		}
		if(array_key_exists($key, $metadata['slave'])) {
			$is_many = isset($metadata['slave'][$key]['conformity']) && $metadata['slave'][$key]['conformity'] == 'many';
			return $is_many ? $this->getMany($key) : $this->getOne($key);
		}
		return parent::__get($key);
	}

	public static function one($where) {
		if(!is_array($where) && !is_object($where)) {
			$primaryKey = $this->getPrimaryKey();
			if($primaryKey) {
				$primaryKeyValue = $where;
				$where = array ();
				$where[$primaryKey] = $primaryKeyValue;
			} else {
				return null;
			}
		}

		$models = static::many($where, 0, 1);
		return count($models) ? $models[0] : null;
	}

	public static function many($where, $offset = 0, $limit = 0) {
		$models = array ();
		foreach(static::_load($where, $offset, $limit) as $data) {
			$model_name = get_called_class();
			$model = new $model_name();
			$model->dataConvertFromSave($data);
			$models[] = $model;
		}
		return $models;
	}

	public static function _load($where, $offset = 0, $limit = 0) {
		return array ();
	}

	public static function create($fields = array ()) {
		$class_name = get_called_class();
		$object = new $class_name($fields);
		return $object;
	}

	public function beforeSave() {

	}

	public function save() {
		$this->cleanErrors();
		$this->beforeSave();
		if(!$this->hasErrors()) {
			$data = $this->dataConvertToSave();
			return $this->saveProcess($data);
		} else {
			return false;
		}
	}

	public function dataConvertToSave() {
		$fields = $this->toArray();
		return myCMS::gI()->model->convertToSave($fields, $this->getMetadata());
	}

	public function dataConvertFromSave($data = array ()) {
		$fields = myCMS::gI()->model->convertFromSave($data, $this->getMetadata());
		$this->fromArray($fields);
	}

	protected function saveProcess($data = array ()) {
		return true;
	}

	public function remove() {
		return true;
	}

	public function hasErrors() {
		return count($this->errors) > 0;
	}

	public function getError() {
		if($this->hasErrors()) {
			return reset(reset($this->errors));
		}
	}

	public function getErrors() {
		return $this->errors;
	}

	public function cleanErrors() {
		$this->errors = array ();
	}

	public static function getMetadata() {
		return static::$_metadata = myCMS::gI()->model->getMetadata(get_called_class());
	}

	public static function getPrimaryKey() {
		$metadata = static::getMetadata();
		return is_array($metadata) && array_key_exists('primaryKey', $metadata) ? $metadata['primaryKey'] : null;
	}

	public function getPrimaryKeyValue() {
		return $this->get($this->getPrimaryKey());
	}

	public static function getDefaultFields() {
		$metadata = static::getMetadata();
		$fields = array ();
		foreach ($metadata['fields'] as $field => $meta) {
			$fields[$field] = $meta['default'];
		}

		return $fields;
	}

	public static function getTableName() {
		return myCMS::gI()->model->getTableName(get_called_class());
	}

	public static function getModelAlias() {
		return myCMS::gI()->load->getModelNameByClassName(get_called_class());
	}

	public function addOne($model_alias) {
		echo get_class($this);
	}

	public function addMany($model_alias) {

	}

	public function getOne($model_alias, $where = array ()) {
		$many = $this->getMany($model_alias, $where);
		return count($many) ? $many[0] : null;
	}

	public function getMany($model_alias, $where = array (), $offset = 0, $limit = 0) {
		$metadata = $this->getMetadata();
		if(array_key_exists($model_alias, $metadata['master'])) {
			$model_name = $metadata['master'][$model_alias]['model'];
			$internal_key = $metadata['master'][$model_alias]['internal'];
			$external_key = $metadata['master'][$model_alias]['external'];
		} elseif(array_key_exists($model_alias, $metadata['slave'])) {
			$model_name = $metadata['slave'][$model_alias]['model'];
			$internal_key = $metadata['slave'][$model_alias]['internal'];
			$external_key = $metadata['slave'][$model_alias]['external'];
		} else {
			return null;
		}

		$self_alias = $this->getModelAlias();
		$model_name = myCMS::gI()->load->getComponentShortName($model_name);
		$self_alias = myCMS::gI()->load->getComponentShortName($self_alias);
		$query = myCMS::gI()->db
			->newQuery($model_name)
			->join($self_alias, null, $model_name.'.'.$external_key.'='.$self_alias.'.'.$internal_key)
			->where($where)
			->where(array($self_alias.'.'.$internal_key => $this->get($internal_key)));
		return myCMS::gI()->model->getList($model_name, $query, $offset, $limit);
	}
}

return 'myBaseModel';