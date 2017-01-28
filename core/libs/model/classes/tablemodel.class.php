<?php
class myTableModel extends myBaseModel {
	public static function _load($where, $offset = 0, $limit = 0) {
		$dataList = array ();
		if(is_object($where)) {
			$query = $where;
			$query->cleanSelects();
			$query->select(array_keys(static::getDefaultFields()));
		} else {
			if(!is_array($where)) {
				$primaryKeyValue = $where;
				$where = array();
				$where[static::getPrimaryKey()] = $primaryKeyValue;
			}
			$query = myCMS::gI()->db->newQuery(static::getTableName());

			$query = $query->select()->where($where);
		}

		$query = $query->limit($offset, $limit)->execute();
		while($data = $query->fetch(PDO::FETCH_ASSOC)) {
			$dataList[] = $data;
		}

		return $dataList;
	}

	protected function saveProcess($data = array ()) {
		$primaryKey = $this->getPrimaryKey();
		if(!$data[$primaryKey]) {
			unset($data[$primaryKey]);
			$fields = array_keys($data);
			myCMS::gI()->db->query("INSERT INTO ".$this->getTableName()." (`".implode('`,`', $fields)."`) VALUES(:".implode(",:", $fields).")",$data);
			$id = myCMS::gI()->db->lastInsertId();

			$this->set($primaryKey, $id);
			$result = (bool) $id;
		} else {
			$fields = array_keys($data);
			$_data = array ();
			foreach($fields as $key) {
				$_data[] = "`".$key."` = :".$key;
			}
			myCMS::gI()->db->query("UPDATE ".$this->getTableName()." SET ".implode(',', $_data)." WHERE `".$primaryKey."` = :".$primaryKey, $data);
			$result = true;
		}
		return $result;
	}

	public function remove() {
		myCMS::gI()->db->query("DELETE FROM ".$this->getTableName()." WHERE `".$this->getPrimaryKey()."` = '".$this->getPrimaryKeyValue()."'");
		return parent::remove();
	}
}

return 'myTableModel';