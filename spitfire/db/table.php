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
		
		$query = new _SF_DBQuery($this);
		$query->addRestriction(new _SF_Restriction($field, $value));
		
		return $query;
	}

	public function getAll() {
		
		$query = new _SF_DBQuery($this);
		return $query;
	}

	public function like($field, $value) {
		
		$query = new _SF_DBQuery($this);
		$query->addRestriction(new _SF_Restriction($field, $value, _SF_Restriction::LIKE_OPERATOR));
		return $query;
	}

	public function isNull($field) {
		
		$query = new _SF_DBQuery($this);
		$query->addRestriction(new _SF_Restriction($field, NULL, ' is '));
		return $query;
	}
	
	public function getFields() {
		if ($this->fields) return $this->fields;
		return $this->db->fetchFields($this);
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

	public function set ($data) {
		return $this->db->set($this, $data);
	}
	
	public function validate($data) {
		
		$this->errors = Array();
		$newdata = Array();
		foreach ($this->getFields() as $field) {
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