<?php 
class myDBQuery extends myClass {
	private $_table_name = null;
	private $_table_alias = null;
	private $_select_list = array ();
	private $_where_list = array ();
	private $_placeholders = array ();
	private $_join_list = array ();
	private $_sort_list = array ();
	private $_sort_by = array ();
	private $_limit_start = 0;
	private $_limit_count = null;
	private $_group_by = null;
	private $_having_list = array ();
	private $_sql = null;
	private $_where_concat_list = array ('AND', 'OR');
	private $_where_operator_list = array (
		'=' => '#OPERAND_1# = #OPERAND_2#', 
		'!=' => '#OPERAND_1# <> #OPERAND_2#', 
		'>' => '#OPERAND_1# > #OPERAND_2#', 
		'<' => '#OPERAND_1# < #OPERAND_2#', 
		'>=' => '#OPERAND_1# >= #OPERAND_2#',
		'<=' => '#OPERAND_1# <= #OPERAND_2#',
		'NOT' => '#OPERAND_1# IS NOT #OPERAND_2#',
		'IS' => '#OPERAND_1# IS #OPERAND_2#',
		'LIKE' => '#OPERAND_1# LIKE #OPERAND_2#',
		'IN' => '#OPERAND_1# IN(#OPERAND_2#)',
		'NOTIN' => '#OPERAND_1# NOT IN(#OPERAND_2#)',
	);

	function __construct($properties = array ()) {
		if(!array_key_exists('db', $properties)) {
			$properties['db'] = myCMS::gI()->db;
		}

		parent::__construct($properties);
		$this->clean();
	}

	public function clean() {
		$this->_table_name = null;
		$this->_table_alias = null;
		$this->cleanSelects();
		$this->cleanWheres();
		$this->cleanJoins();
		$this->cleanSorts();
		$this->_limit_start = 0;
		$this->_limit_count = null;
		$this->_group_by = null;
		$this->cleanHavings();
		$this->_sql = null;
		return $this;
	}

	public function cleanSelects() {
		$this->_select_list = array ();
	}

	public function cleanWheres() {
		$this->_where_list = array ();
		$this->_placeholders = array ();
	}

	public function cleanJoins() {
		$this->_join_list = array ();
	}

	public function cleanSorts() {
		$this->_sort_list = array ();
		$this->_sort_by = array ();
	}

	public function cleanHavings() {
		$this->_having_list = array ();
	}

	public function select($columns = array (), $table_alias = '') {
		if(!array_key_exists($table_alias, $this->_select_list)) {
			$this->_select_list[$table_alias] = array ();
		}
		foreach($columns as $name => $alias) {
			if(is_integer($name)) {
				$name = $alias;
			}

			$this->_select_list[$table_alias][$name] = $alias;
		}

		return $this;
	}

	public function from($table_name, $table_alias = null) {
		if($table_alias == null) {
			$table_alias = $table_name;
		}

		$this->_table_name = $table_name;
		$this->_table_alias = $table_alias;

		return $this;
	}

	public function join($table_name, $table_alias = null, $on, $join_type = 'left') {
		$join_type = strtoupper($join_type);
		if(!in_array($join_type, array('LEFT', 'RIGHT'))) {
			$join_type = 'LEFT';
		}

		if($table_alias == null) {
			$table_alias = $table_name;
		}

		$this->_join_list[$table_alias] = array (
			'name' => $table_name,
			'alias' => $table_alias,
			'on' => $on,
			'type' => $join_type,
		);

		return $this;
	}

	public function where($wheres = array ()) {
		list($escape, $placeholders) = $this->getWhereEscape($wheres);

		if(count($this->_where_list) == 0) {
			$this->_where_list = $escape;
		} else {
			$this->_where_list[] = $escape;
		}

		$this->_placeholders = array_merge($this->_placeholders, $placeholders);
		return $this;
	}

	public function limit($start, $count = null) {
		$this->_limit_start = $start;
		$this->_limit_count = $count;

		return $this;
	}

	public function group($column) {
		$this->_group_by = $column;

		return $this;
	}

	public function having() {

		return $this;
	}

	public function sort($by, $dir = 'ASC') {
		if(is_array($by)) {
			$this->_sort_by = array_merge($this->_sort_by, $by);
		} else {
			$this->_sort_by[$by] = $dir;
		}

		return $this;
	}

	public function compile() {
		$sql = "";
		$sql .= $this->_getColumns();
		$sql .= $this->_getTable();
		$sql .= $this->_getJoins();
		$sql .= $this->_getWhere();
		$sql .= $this->_getGroups();
		$sql .= $this->_getHaving();
		$sql .= $this->_getSort();
		$sql .= $this->_getLimit();

		$this->_sql = $sql;
		return $this;
	}

	public function execute($placeholders = array ()) {
		if($this->_sql == null) {
			$this->compile();
		}

		return $this->properties['db']->query($this->_sql, array_merge($this->_placeholders, $placeholders));
	}

	public function toSQL() {
		if($this->_sql == null) {
			$this->compile();
		}

		return $this->_sql;
	}

	private function _getColumns() {
		$columns = array ();
		foreach($this->_select_list as $table_alias => $table_columns) {
			if($table_alias == '') {
				$table_alias = $this->_table_alias;
			}
			foreach($table_columns as $column => $alias) {
				$column_sql = $this->columnPrepareToSQL($table_alias.'.'.$column);
				if(!$column_sql) { $column_sql = $column; }
				$alias_sql = $this->columnPrepareToSQL($alias);
				$alias_sql = $alias_sql ? " AS ".$alias_sql : "";
				$columns[] = $column_sql.$alias_sql;
			}
		}

		if(count($columns)) {
			$columns = implode(',', $columns);
		} else {
			$columns = "*";
		}
		
		return "SELECT ".$columns." ";
	}

	private function _getTable() {
		$table_name = $this->columnPrepareToSQL($this->_table_name);
		if(!$table_name) { $table_name = $this->_table_name; }
		$table_alias = $this->columnPrepareToSQL($this->_table_alias);
		$table_alias = $table_alias ? " AS ".$table_alias : "";
		return "FROM ".$table_name.$table_alias." ";
	}

	private function _getJoins() {
		$sql = "";
		foreach($this->_join_list as $join) {
			switch (strtoupper($join['type'])) {
				case 'RIGHT':
					$sql .= "RIGHT JOIN ";
					break;
				default:
					$sql .= "LEFT JOIN ";
			}

			$table_name = $this->columnPrepareToSQL($join['name']);
			if(!$table_name) { $table_name = $join['name']; }
			$table_alias = $this->columnPrepareToSQL($join['alias']);
			$table_alias = $table_alias ? " AS ".$table_alias : "";

			$sql .= $table_name.$table_alias.' ON '.$join['on'].' ';
		}
		return $sql;
	}

	private function _getWhere() {
		if(count($this->_where_list) == 0) {
			$where = "1";
		} else {
			$where = $this->parseWhereOnce(0, $this->_where_list);
			$where = $where['sql'];
			/*while(strpos($where, '(') === 0) {
				$where = substr($where, 1, strlen($where) - 2);
			}*/
			$where = substr($where, 1, strlen($where) - 2);
		}
		return "WHERE ".$where." ";
	}

	private function getWhereEscape($wheres = array ()) {
		$escape = array ();
		$placeholders = array ();
		foreach($wheres as $key => $value) {
			if(!is_integer($key)) {
				$key_metadata = $this->parseWhereKey($key);
				$key_placeholder = str_replace('.', '_', $key_metadata['sql']);
				if(is_array($value)) {
					list($_escape, $_placeholders) = $this->getWhereEscape($value);
					$placeholders = array_merge($placeholders, $_placeholders);
					$escape[$key] = $_escape;
				} else {
					$placeholders[$key_placeholder] = $value;
					$escape[$key] = ':'.$key_placeholder;
				}
			} else {
				if(is_array($value)) {
					list($_escape, $_placeholders) = $this->getWhereEscape($value);
					$escape[] = $_escape;
					$placeholders = array_merge($placeholders, $_placeholders);
				} else {
					$escape[] = $value;
				}
			}
		}

		return array($escape, $placeholders);
	}

	private function _getGroups() {
		return "";
	}

	private function _getHaving() {
		return "";
	}

	private function _getSort() {
		$sort = array ();
		foreach($this->_sort_by as $by => $dir) {
			$pattern = '/^[a-zA-Z0-9.]+$/';
			if(preg_match($pattern, $by, $matches)) {
				$by_sql = $this->columnPrepareToSQL($by);
				if(!$by_sql) { $by_sql = $by; }
				$by = $by_sql;
			}
			$sort[] = $by.' '.$dir;
		}
		$sort = implode(',', $sort);
		if($sort != "") {
			$sort = 'ORDER BY '.$sort.' ';
		}
		return $sort;
	}

	private function _getLimit() {
		$sql = "";
		if($this->_limit_start != null) {
			$sql .= "LIMIT ".$this->_limit_start;
		}

		if($this->_limit_count != null && $sql != "") {
			$sql .= ",".$this->_limit_count." ";
		}

		return $sql;
	}

	private function aliasModified($alias) {
		$alias_list = explode(',', $alias);
		foreach($alias_list as $key => $alias) {
			$alias_list[$key] = '`'.$alias.'`';
		}

		return implode('.', $alias_list);
	}

	private function parseWhereOnce($key, $value) {
		if(is_array($value) && is_integer($key)) {
			$sql = "";
			$sql_concat = "AND";
			foreach($value as $k => $v) {
				$where_data = $this->parseWhereOnce($k, $v);

				if($sql != "") {
					$sql .= ' '.$where_data['prefix'].' ';
				} else {
					$sql_concat = $where_data['prefix'];
				}

				$sql .= $where_data['sql'];

				$sql = '('.$sql.')';
			}
		} else {
			if(is_integer($key) && is_string($value)) {
				$key = $value;
			}

			$key_metadata = $this->parseWhereKey($key);
			$sql = $this->whereMetadataToSQL($key, $value, $key_metadata);
			$sql_concat = $key_metadata['prefix'];
		}

		return array (
			'sql' => $sql,
			'prefix' => $sql_concat,
		);
	}

	private function parseWhereKey($key) {
		$result = array (
			'sql' => '',
			'prefix' => 'AND',
			'operator' => '=',
		);

		foreach($this->_where_concat_list as $concat_item) {
			if(strpos($key, $concat_item.':') === 0) {
				$key = substr($key, strlen($concat_item) + 1);
				$result['prefix'] = $concat_item;
				break ;
			}
		}

		$key_length = strlen($key);
		foreach($this->_where_operator_list as $operator => $sql_operator) {
			$operator_length = strlen($operator);
			if(strpos($key, ':'.$operator) === $key_length - $operator_length - 1) {
				$key = substr($key, 0, $key_length - $operator_length - 1);
				$result['operator'] = $operator;
				break ;
			}
		}

		$result['sql'] = $key;

		return $result;
	}

	private function whereMetadataToSQL($key, $value, $key_metadata) {
		if($key != $value) {
			if(is_integer($value) || is_string($value)) {
				if(strpos($value, ':') !== 0) {
					$value = "'".$value."'";
				}
			} elseif(is_bool($value)) {
				$value = $value ? "'1'" : "'0'";
			} elseif(is_array($value)) {
				$value = implode(',', $value);
			}

			$sql_operator = $this->_where_operator_list[$key_metadata['operator']];
			$key_sql = $this->columnPrepareToSQL($key_metadata['sql']);
			if(!$key_sql) { $key_sql = $key_metadata['sql']; }
			$sql_operator = str_replace('#OPERAND_1#', $key_sql, $sql_operator);
			$sql_operator = str_replace('#OPERAND_2#', $value, $sql_operator);
		} else {
			$sql_operator = $key;
		}

		return $sql_operator;
	}

	private function columnPrepareToSQL($column) {
		if(preg_match('/^([a-zA-Z0-9_\-.]+)$/', $column)) {
			$column_items = explode('.', $column);
			foreach($column_items as $key => $column) {
				$column_items[$key] = '`'.$column.'`';
			}

			$column = implode('.', $column_items);
		} else {
			return null;
		}

		return $column;
	}

}