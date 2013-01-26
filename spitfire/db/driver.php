<?php

interface _SF_DBDriver
{
	#DB Specific functions
	function getConnection();
	
	#Table specific functions
	function fetchFields(_SF_DBTable $table);
	
	#Query Specific functions
	function query (_SF_DBTable $table, _SF_DBQuery $query, $fields = false);
	function insert(_SF_DBTable $table, databaseRecord $data );
	function update(_SF_DBTable $table, databaseRecord $data, $id );
	function inc   (_SF_DBTable $table, databaseRecord $data, $field, $value ); //Stands for increment
	function delete(_SF_DBTable $table, databaseRecord $id );
	
	#Data Escaping
	function escapeFieldNames(_SF_DBTable$table, $fields);
}
