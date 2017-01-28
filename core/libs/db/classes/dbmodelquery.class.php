<?php 
class myDBModelQuery extends myDBQuery {
	function __construct($properties = array ()) {
		parent::__construct($properties);
	}

	public function from($model_name, $model_alias = null) {
		$table_name = $this->getTableNameFromModelName($model_name);
		$table_alias = $this->getTableAliasFromModelName($model_name, $model_alias == $model_name ? null : $model_alias);

		return parent::from($table_name, $table_alias);
	}

	public function join($model_name, $model_alias = null, $on, $join_type = 'left') {
		$table_name = $this->getTableNameFromModelName($model_name);
		$table_alias = $this->getTableAliasFromModelName($model_name, $model_alias == $model_name ? null : $model_alias);

		return parent::join($table_name, $table_alias, $on, $join_type);
	}

	public function getTableNameFromModelName($model_name) {
		$table_name = myCMS::gI()->model->getTableName($model_name);
		return $table_name === null ? $model_name : $table_name;
	}

	public function getTableAliasFromModelName($model_name, $model_alias = null) {
		return $model_alias === null ? myCMS::gI()->load->getComponentShortName($model_name) : $model_alias;
	}
}

return 'myDBModelQuery';