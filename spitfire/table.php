<?php

class table
{

	protected $tablename = false;
	protected $db     = false;
	protected $fields = false;

	public function __construct ($database, $tablename = false) {
		$this->db = $database;

		if ($tablename)
			$this->tablename = $tablename;
	}

	public function get($field, $value) {

		if (is_array($this->fields))
			$statement = "SELECT " . implode(array_keys($this->fields), ', ') . " WHERE  $field = :value";
		else
			$statement = "SELECT * FROM  WHERE  $field = :value";

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

}