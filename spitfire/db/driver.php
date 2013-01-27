<?php

namespace spitfire\storage\database\drivers;

use spitfire\storage\database\Table;
use spitfire\storage\database\Query;
use databaseRecord;

interface Driver
{
	#DB Specific functions
	function getConnection();
	
	#Table specific functions
	function fetchFields(Table $table);
	
	#Query Specific functions
	function query (Table $table, Query $query, $fields = false);
	function insert(Table $table, databaseRecord $data );
	function update(Table $table, databaseRecord $data );
	function inc   (Table $table, databaseRecord $data, $field, $value ); //Stands for increment
	function delete(Table $table, databaseRecord $id );
}
