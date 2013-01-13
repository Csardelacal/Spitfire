<?php

class _SF_DBTable
{

	protected $tablename = false;
	protected $primaryK  = false;
	/** @var _SF_DBInterface  */
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
		
		$this->fields = $this->db->fetchFields($this);
		
	}
	
	public function getFields() {
		if (!is_array($this->fields)) $this->fetchFields();
		return array_map(Array($this->db, 'escapeFieldName'), $this->fields);
	}
	
	public function getTablename() {
		return $this->tablename;
	}
	
	/**
	 * Returns the database the table belongs to.
	 * @return _SF_DBInterface
	 */
	public function getDb() {
		return $this->db;
	}
	
	public function getErrors() {
		if (empty ($this->errors)) return false;
		return $this->errors;
	}
	
	/**
	 * Converts data from the encoding the database has TO the encoding the
	 * system uses.
	 * @param String $str
	 * @return String
	 */
	public function convertIn($str) {
		return iconv(environment::get('database_encoding'), environment::get('system_encoding'), $str);
	}
	
	
	/**
	 * Converts data from the encoding the system has TO the encoding the
	 * database uses.
	 * @param String $str
	 * @return Strng
	 */
	public function convertOut($str) {
		return iconv(environment::get('system_encoding'), environment::get('database_encoding'), $str);
	}

	public function set ($data) {
		return $this->db->set($this, $data);
	}
	
	public function validate($data) {
		
		$this->errors = Array();
		$newdata = Array();
		foreach ($this->fields as $field) {
			if (method_exists ($this, 'validate_' . $field)) {
				$function = Array($this, 'validate_' . $field);
				$error = call_user_func_array($function, Array($data[$field]));
				if ($error) {
					$this->errors[] = $error;
				}
			}
			$newdata[$field] = $data[$field];
		}
		
		return $newdata;
	}

}