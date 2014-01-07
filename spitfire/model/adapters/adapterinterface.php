<?php namespace spitfire\model\adapters;

/**
 * An adapter is basically a simple block that contains receives and delivers
 * data both to the user and the database. This turns it into a great tool to 
 * sanitize user data and prepare information from the DB in a way that it's user
 * friendly.
 * 
 * The adapter can also set and receive virtual data, allowing it to hold information
 * that is not present / stored to the database. I.e. This is useful for arrays,
 * which are not contained as that in the database.
 * 
 * An array can either be stored in a serialized manner (which would require the
 * adapter to serialize / unserialize it every time) or as a child field, in which
 * case you can store several records that 'belong' to the parent record but are
 * stored in different tables.
 */
interface AdapterInterface
{
	
	function dbSetData($data);
	function dbGetData();
	
	function usrSetData($data);
	function usrGetData();
	
	function getModel();
	function getField();
	
	function isSynced();
}