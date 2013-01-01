<?php

interface _SF_DBDriver
{
	#DB Specific functions
	function getConnection();
	
	#Table specific functions
	function fetchFields(_SF_DBTable $table);
	
	#Query Specific functions
	function set   (_SF_DBTable $table, $data );
	function query (_SF_DBTable $table, _SF_DBQuery $query, $fields = false);
}
