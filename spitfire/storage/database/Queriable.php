<?php namespace spitfire\storage\database;

/**
 * Convenience class meant to separate tables and the table related actions from
 * their query related tasks. It will only return different query short-hand methods
 * that help speeding simple queries.
 * 
 * @package Spitfire.storage.database
 * @author César de la Cal <cesar@magic3w.com>
 * @abstract
 */
abstract class Queriable {
	
	
	/**
	 * Creates a simple query with a simple restriction applied to it. This
	 * is especially useful for id related queries.
	 * 
	 * @param String $field
	 * @param String $value
	 * @return Query
	 */
	public function get($field, $value, $operator = null) {
		#Create the query
		$query = $this->getQueryInstance();
		$query->addRestriction($field, $value, $operator);
		#Return it
		return $query;
	}
	
	/**
	 * Creates an empty query that would return all data. This is a syntax
	 * friendliness oriented method as it does exactly the same as startQuery
	 * 
	 * @see Queriable::getQueryInstance()
	 * @return Query
	 */
	public function getAll() {
		
		$query = $this->getQueryInstance();
		return $query;
	}
	
	/**
	 * 
	 * @param String $field Name of the database field to be queried.
	 * @param String $value Value we're looking for
	 * @param Boolean $fuzzy Defines whether the clause should automatically
	 *                       add %'s and replace spaces with %
	 * @return Query
	 */
	public function like($field, $value, $fuzzy = false) {
		
		if ($fuzzy) {
			$value = '%' . 
				str_replace(Array('%', ' '), Array('[%]', '%'), $value) . 
				'%';
		}
		
		$query = $this->getQueryInstance();
		$query->addRestriction($field, $value, Restriction::LIKE_OPERATOR);
		return $query;
	}
	
	/**
	 * Allows the driver to specify a class for the queries it needs to
	 * generate.
	 * 
	 * @return Query The query object
	 */
	public abstract function getQueryInstance();
}