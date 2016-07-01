<?php

namespace spitfire\storage\database\drivers;

use spitfire\storage\database\Table;
use spitfire\storage\database\Query;
use Model;

/**
 * This class' behavior was moved to the tables, even though these could have been
 * global behaviors, they feel cleaner inside a proper scope.
 * 
 * @deprecated since version 0.1-dev 201607011052
 */
interface Driver
{
	
	#Table specific functions
	function fetchFields(Table $table);
	
	#Query Specific functions
	function query (Table $table, Query $query, $fields = false);
	function insert(Table $table, Model $data );
	function update(Table $table, Model $data );
	function inc   (Table $table, Model $data, $field, $value ); //Stands for increment
	function delete(Table $table, Model $id );
}
