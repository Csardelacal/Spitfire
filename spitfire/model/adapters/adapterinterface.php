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
	/**
	 * This receives data from the database and stores it into the adapter. The 
	 * reason there are two methods for setting data is the different nature of the
	 * contents they set to the app.
	 * 
	 * @param mixed $data Data to be stored to the adapter.
	 */
	function dbSetData($data);
	
	/**
	 * This method returns the data the adapter wants to store to the database. The
	 * DBMS controller should use this endpoint to collect the data it will store 
	 * to the driver.
	 * 
	 * @return mixed Data to be contained in the database.
	 */
	function dbGetData();
	
	/**
	 * This method sets the data from the 'user' end. This means the opposite one
	 * to the database, the one being moved into the database.
	 * You can and should use this method to allow verifying that the data sent 
	 * by the user is correct here, there is no need to check it's database 
	 * friendliness here (this could even be bad for performance). 
	 * 
	 * @param mixed $data Data to be contained in the database.
	 */
	function usrSetData($data);
	
	/**
	 * Returns the data that is meant to reach the 'user space'. Objects here should 
	 * be programmer friendly and can be objects if needed.
	 * 
	 * You should avoid making verifications / parsing the data in this function,
	 * stuff you think should go here probably belongs in dbSetData()
	 * 
	 * @return mixed Data to be delivered to the programmer
	 */
	function usrGetData();
	
	/**
	 * This method should return the model / record this belongs to. This allows
	 * for checking against other database data (in case this is needed) and 
	 * several operations that are critical for the systems correct behavior.
	 * 
	 * @return \Model The model this belongs to.
	 */
	function getModel();
	
	/**
	 * Returns the field this adapter holds data for. This is important for the 
	 * database driver as it will have to locate the field where it has to place 
	 * this data.
	 * 
	 * @return \spitfire\model\Field The field this represents
	 */
	function getField();
	
	/**
	 * Allows the adapter to tell the database driver whether the data inside this
	 * is synced (has not been modified) and should be stored to the database to 
	 * avoid being lost.
	 * If data considers itself non-DB friendly (like arrays / subrecords /etc) 
	 * these should return false always and use the commit method to store the data.
	 * 
	 * @return boolean Indicates whether the data is already in sync. Returns false
	 * if it should be stored.
	 */
	function isSynced();
	
	/**
	 * Settles the content inside the adapter. This method is usually called when
	 * the model is being stored. This means the data is considered final and the 
	 * data inside the adapter is no longer out of sync with the database.
	 * 
	 * In case the data is not stored inside the database you can use the commit 
	 * function to do so, as this is called even if the adapter is in sync.
	 */
	function commit();
	
	/**
	 * Resets the content inside the adapter. In case you don't want to keep the 
	 * altered data and want to modify or read the original source data instead
	 * of the altered one.
	 */
	function rollback();
}