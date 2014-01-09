<?php namespace spitfire\model\adapters;

/**
 * This base adapter allows overriding methods to let it handle the actual getting,
 * setting and sync tasks to this base adapter. Allowing them to reduce duplicate
 * code from the actual returning and storing data and focusing on their tasks.
 */
abstract class baseAdapter implements AdapterInterface
{
	private $src;
	private $data;
	private $sync;
	
	public function dbGetData() {
		
	}

	public function dbSetData($data) {
		
	}

	public function getField() {
		
	}

	public function getModel() {
		
	}

	public function isSynced() {
		
	}

	public function usrGetData() {
		
	}

	public function usrSetData($data) {
		
	}

}