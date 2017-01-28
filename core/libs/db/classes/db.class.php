<?php 
class myDB extends myClass {
	private $_pdo = null;
	function __construct ($properties = array ()) {
		parent::__construct($properties);
		$this->_pdo = $this->connect();
	}

	public function query($sql, $placeholders = array ()) {
		$stmt = $this->_pdo->prepare($sql);
		$stmt->execute($placeholders);
		return $stmt;
	}

	public function connect($connect_data = array ()) {
		$this->properties = array_merge($this->properties, $connect_data);

		$dsn = "mysql:host={$this->properties['host']};dbname={$this->properties['dbname']};charset={$this->properties['charset']}";
		$opt = array(
			PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
			PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
			PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES ".$this->properties['charset'],
		);
		$pdo = new PDO($dsn, $this->properties['username'], $this->properties['password'], $opt);

		return $pdo;
	}

	public function is_connect() {
		return (bool) $this->_pdo; 
	}

	public function newQuery($model_name, $model_alias = null) {
		$query = new myDBModelQuery();
		$query->from($model_name, $model_alias);
		return $query;
	}

	public function tablePrefix() {
		return $this->properties['table_prefix'];
	}

	public function lastInsertId() {
		return $this->_pdo->lastInsertId();
	}
}

return 'myDB';