<?php namespace spitfire\model\adapters;

/**
 * This base adapter allows overriding methods to let it handle the actual getting,
 * setting and sync tasks to this base adapter. Allowing them to reduce duplicate
 * code from the actual returning and storing data and focusing on their tasks.
 */
abstract class baseAdapter implements AdapterInterface
{
	private $field;
	private $model;
	
	private $src;
	private $data;
	
	public function dbGetData() {
		return $this->data;
	}

	public function dbSetData($data) {
		$this->data = $data;
		$this->src  = $data;
	}

	public function getField() {
		return $this->field;
	}

	public function getModel() {
		return $this->model;
	}

	public function isSynced() {
		return $this->data == $this->src;
	}

	public function usrGetData() {
		return $this->data;
	}

	public function usrSetData($data) {
		$this->data = $data;
	}

	public function commit() {
		$this->src = $this->data;
	}

	public function rollback() {
		$this->data = $this->src;
	}

}