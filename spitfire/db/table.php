<?php

class _SF_DBTable
{

	protected $tablename = false;
	protected $primaryK  = false;
	protected $db        = false;
	protected $fields    = false;
	
	protected $errors    = Array();
	
	protected $rpp    = 20;

	public function __construct ($database, $tablename = false) {
		$this->db = $database;

		if ($tablename)
			$this->tablename = environment::get('db_table_prefix') . $tablename;
	}

	public function get($field, $value) {
		
		if (!is_array($this->fields)) $this->fetchFields();
		
		$query = new _SF_DBQuery($this);
		$query->addRestriction(new _SF_Restriction($field, $value));
		return $query;
	}

	public function getAll() {
		
		if (!is_array($this->fields)) $this->fetchFields();
		
		$query = new _SF_DBQuery($this);
		return $query;
	}

	public function like($field, $value) {
		
		if (!is_array($this->fields)) $this->fetchFields();
		
		$query = new _SF_DBQuery($this);
		$query->addRestriction(new _SF_Restriction($field, $value, _SF_Restriction::LIKE_OPERATOR));
		return $query;
	}

	public function isNull($field) {
		
		if (!is_array($this->fields)) $this->fetchFields();
		
		$query = new _SF_DBQuery($this);
		$query->addRestriction(new _SF_Restriction($field, NULL, ' is '));
		return $query;
	}
	
	public function fetchFields() {
		$statement = "DESCRIBE `$this->tablename` ";
		
		$con = $this->db->getConnection();
		$stt = $con->prepare($statement);
		$stt->execute();
		
		$error = $stt->errorInfo();
		if ($error[1]) throw new privateException($error[1], $error[0]);
		
		$this->fields = Array();
		while($row = $stt->fetch()) {
			$this->fields[] = $row['Field'];
			if ( isset($row['Key']) && $row['Key'] == 'PRIMARY') 
				$this->primaryK = $row['Field'];
		}
		
		
	}
	
	public function getFields() {
		if (!is_array($this->fields)) $this->fetchFields();
		return $this->fields;
	}
	
	public function getTablename() {
		return $this->tablename;
	}
	
	public function getDb() {
		return $this->db;
	}
	
	public function getErrors() {
		if (empty ($this->errors)) return false;
		return $this->errors;
	}

	public function set ($data) {
		$this->errors = Array();
		if ($this->fields) {
			$newdata = Array();
			foreach ($this->fields as $field) 
				if (method_exists ($this, 'validate_' . $field)) {
					if ($error = call_user_func_array(Array($this, 'validate_' . $field), Array($data[$field]))) {
						$this->errors[] = $error;
					}
				}
				else $newdata[$field] = $data[$field];
			$data = $newdata;
		}

		if (empty($data['id'])) unset ($data['id']);

		$fields = array_keys($data);
		$famt   = count($fields);

		$statement = "INSERT INTO `$this->tablename` (".implode(', ', $fields) .") VALUES (:" . implode(', :', $fields) . ") ON DUPLICATE KEY UPDATE ";
		for ($i = 0; $i < $famt; $i++) {
			$statement.= $fields[$i] . " = :" . $fields[$i];
			if ($i < $famt-1) $statement.= ',';
			$statement.= ' ';
		}

		$con = $this->db->getConnection();
		$stt = $con->prepare($statement);
		$stt->execute($data);
		
		if ($stt->rowCount() == 1) return $con->lastInsertId();
		else return $data['id'];

	}

}