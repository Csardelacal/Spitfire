<?php namespace spitfire\storage\database;

/**
 * The database object factory is a class that allows a driver to provide SF's 
 * ORM with all the required bits and pieces to operate. Usually a driver needs
 * to provide it's own Table, Query, Field... objects that implement / extend
 * the behavior required for the ORM to work.
 * 
 * Historically, a query would provide only the pieces it needed, as well as the
 * table would. But for consistency, and to avoid generating classes that only 
 * need to extend in order to provide factories we're merging those behaviors
 * in this single factory.
 */
abstract class DatabaseObjectFactory
{
	
	/**
	 * Returns an instance of the class the child tables of this class have
	 * this is used to create them when requested by the table() method.
	 * 
	 * @abstract
	 * @return Table Instance of the table class the driver wants the system to use
	 */
	abstract public function getTableInstance(DB$db, $tablename);
	
	/**
	 * Creates a new On The Fly Model. These allow the system to interact with a 
	 * database that was not modeled after Spitfire's models or that was not 
	 * reverse engineered previously.
	 * 
	 * @abstract
	 * @return Table Instance of the table class the driver wants the system to use
	 */
	abstract public function getOTFModel($tablename);
}
