<?php

class table
{

	protected $tablename = false;
	protected $db     = false;
	protected $fields = false;

	public function __construct ($database, $tablename = false) {
		$this->db = $database;

		if ($tablename)
			$this->tablename = environment::get('db_prefix') . $tablename;
	}

	public function get($field, $value) {

		if (is_array($this->fields))
			$statement = "SELECT " . implode(array_keys($this->fields), ', ') . " FROM $this->tablename WHERE  $field = :value";
		else
			$statement = "SELECT * FROM $this->tablename WHERE  $field = :value";

		$con = $this->db->getConnection();
		$stt = $con->prepare($statement);
		
		$stt->execute(Array(':value' => $value));

		$result = Array();
		while ($row = $stt->fetch()) {
			foreach($row as $field=>$value)
				if (is_callable(Array($this, 'parse_'.$field)))
					$row['field'] = call_user_func_array(Array($this, 'parse_'.$field), Array($value));
				$result[$row['id']] = $row; //Check
		}

		return $result;
	}

	public function set ($data) {

		if ($this->fields) {
			$newdata = Array();
			foreach ($fields as $field)  $newdata[$field] = $data[$field];
			$data = $newdata;
		}

		if (empty($data['id'])) unset ($data['id']);

		$fields = array_keys($data);
		$famt   = count($fields);

		$statement = "INSERT INTO $this->tablename (".implode(', ', $fields) .") VALUES :" . implode(', :', $fields) . "ON DUPLICATE KEY UPDATE ";
		for ($i = 0; $i < $famt; $i++) $statement.= $fields[$i] . " = :" . $fields[$i] . " ";

		$con = $this->db->getConnection();
		$stt = $con->prepare($statement);
		$stt->execute($data);

	}

}