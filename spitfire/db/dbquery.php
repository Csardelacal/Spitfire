<?php

namespace spitfire\storage\database;

use Model;
use privateException;
use \spitfire\model\Field;

abstract class Query
{
	/** @var spitfire\storage\database\drivers\ResultSetInterface */
	protected $result;
	/** @var \spitfire\storage\database\QueryTable  */
	protected $table;
	
	protected $restrictions;
	protected $restrictionGroups;
	protected $page = 1;
	protected $rpp = -1;
	protected $order;
	
	private static $counter = 1;
	private $id;
	private $aliased = false;
	private $count = null;


	public function __construct($table) {
		$this->id = self::$counter++;
		$this->table = $this->queryTableInstance($table);
		$this->restrictions = Array();
	}
	
	/**
	 * Adds a restriction to the current query. Restraining the data a field
	 * in it can contain.
	 * 
	 * @param string $field
	 * @param mixed  $value
	 * @param string $operator
	 * @return spitfire\storage\database\Query
	 */
	public function addRestriction($fieldname, $value, $operator = '=') {
		try {
			#If the name of the field passed is a physical field we just use it to 
			#get a queryField
			$field = $this->table->getTable()->getField($fieldname);
			$restriction = $this->restrictionInstance($this->queryFieldInstance($field), $value, $operator);
			
		} catch (\Exception $e) {
			#Otherwise we create a complex restriction for a logical field.
			$field = $this->table->getTable()->getModel()->getField($fieldname);
			
			if ($fieldname instanceof \Reference && $fieldname->getTarget() === $this->table->getModel())
				$field = $fieldname;
			if ($field == null)
				throw new privateException("No field '$fieldname'");
			
			$restriction = $this->compositeRestrictionInstance($field, $value, $operator);
		}
		
		$this->restrictions[] = $restriction;
		$this->result = false;
		return $this;
	}
	
	public function setAliased($aliased) {
		$this->aliased = $aliased;
	}
	
	public function getAliased() {
		return $this->aliased;
	}
	
	public function getAlias() {
		if ($this->aliased)
			return $this->table->getTable()->getTablename() . '_' . $this->id;
		else
			return $this->table->getTable()->getTablename();
	}
	
	public function getJoins() {
		$_joins = Array();
		
		foreach($this->restrictions as $restriction) {
			$_joins = array_merge($_joins, $restriction->getJoins());
		}
		
		return $_joins;
	}
	
	/**
	 * Creates a new set of alternative restrictions for the current query.
	 * 
	 * @return RestrictionGroup
	 */
	public function group() {
		return $this->restrictions[] = $this->restrictionGroupInstance($this);
	}
	
	/**
	 * Sets the ammount of results returned by the query.
	 * @param int $amt
	 */
	public function setResultsPerPage($amt) {
		$this->rpp = $amt;
		return $this;
	}
	
	/**
	 * @return int The amount of results the query returns when executed.
	 */
	public function getResultsPerPage() {
		return $this->rpp;
	}
	
	/**
	 * @param int $page The page of results currently displayed.
	 * @return boolean Returns if the page se is valid.
	 */
	public function setPage ($page) {
		#The page can't be lower than 1
		if ($page < 1) return false;
		$this->page = $page;
		return true;
	}
	
	public function getPage() {
		return $this->page;
	}
	
	public function getErrors() {
		return $this->table->getErrors();
	}
	
	public function setOrder ($field, $mode) {
		$this->order['field'] = $field;
		$this->order['mode'] = $mode;
		return $this;
	}
	
	/**
	 * Returns a record from a databse that matches the query we sent.
	 * 
	 * @return Model
	 */
	public function fetch() {
		if (!$this->result) $this->query();
		$data = $this->result->fetch();
		return  $data;//array_map(Array($this->table->getDB(), 'convertIn'), $data) ;
	}
	
	public function fetchAll($parent = null) {
		if (!$this->result) $this->query();
		return $this->result->fetchAll($parent);
	}

	protected function query($fields = null, $returnresult = false) {
		$result = $this->execute($fields);
		if ($returnresult) return $result;
		else $this->result = $result;
		return $this;
	}

	/**
	 * Deletes the records matching this query. This will not retrieve the data and
	 * therefore is more efficient than fetching and later deleting.
	 * 
	 * @todo Currently does not support deleting of complex queries.
	 * @return int Number of affected records
	 */
	public abstract function delete();
	
	public function count() {
		if ($this->count !== null) return $this->count;
		$query = $this->query(Array('count(*)'), true)->fetchArray();
		$count = $query['count(*)'];//end($query);
		return $this->count = $count;
	}
	
	public function getRestrictions() {
		$this->table->getTable()->getModel()->getBaseRestrictions($this);
		return $this->restrictions;
	}
	
	public function getCompositeRestrictions() {
		$_return = Array();
		foreach ($this->restrictions as $restriction) {
			if ($restriction instanceof CompositeRestriction) {
				$_return[] = $restriction;
				if ($restriction->getValue() !== null) {
					$_return = array_merge($_return, $restriction->getValue()->getCompositeRestrictions());
				}
			} 
			if ($restriction instanceof RestrictionGroup) {
				$_return = array_merge($_return, $restriction->getCompositeRestrictions());
			}
		}
		return $_return;
	}
	
	public function getOrder() {
		return $this->order;
	}
	
	public function getQueryTable() {
		return $this->table;
	}
	
	public function getTable() {
		return $this->table->getTable();
	}
	
	public function __toString() {
		return $this->getTable() . implode(',', $this->getRestrictions());
	}
	
	/**
	 * This method is used to clean empty restriction groups and restrictions from
	 * a query. This allows to 'optimize' the speed of SQL due to removing potentially
	 * unnecessary joins and subqueries.
	 * 
	 * [Notice] This generates a special 'quirk' of the database engine built into SF,
	 * when you create a query with an empty subquery the database won't return
	 * the expected result from an SQL database (aka. all the elements who have
	 * a parent - including duplicates) but will ignore the subquery and return 
	 * all the data that matches the parent query.
	 * 
	 * @param Restriction|CompositeRestriction|RestrictionGroup $restriction
	 * @return boolean
	 */
	public static function restrictionFilter($restriction) {
		#In case the data contained is a restriction we consider it valid.
		#Restrictions can by default not be empty (they always have a field attached)
		if ($restriction instanceof Restriction) {
			return true;
		}
		
		#Composite restrictions are the most common source of possible empty elements
		#If they contain a query and it is empty it will not add any value to the query
		if ($restriction instanceof CompositeRestriction) {
			if ( ($query = $restriction->getValue()) instanceof Query && !count($query->getRestrictions())) {
				return false;
			}
			return true;
		}
		
		#Restriction groups that are empty will not do anything useful and maybe 
		#even generate invalid SQL like '() AND' so we clean them beforehand.
		if ($restriction instanceof RestrictionGroup) {
			$restrictions = array_filter($restriction->getRestrictions(), Array(self, __METHOD__));
			
			if (empty($restrictions)) {
				return false;
			}
			else {
				$restriction->setRestrictions($restrictions);
				return true;
			}
		}
	}
	
	public abstract function execute($fields = null);
	public abstract function restrictionInstance(QueryField$field, $value, $operator);
	public abstract function compositeRestrictionInstance(Field$field, $value, $operator);
	
	/**
	 * Creates a new instance of a restriction group for this query. The instance
	 * is already created with a reference to this element. This is just used in 
	 * a set of cases, when creatinbg a restriction (so it keeps the reference to
	 * the query) and when "ending the group" which basically returns the call flow
	 * over to the query.
	 * 
	 * @return spitfire\storage\database\RestrictionGroup
	 */
	public abstract function restrictionGroupInstance();
	public abstract function queryFieldInstance($field);
	public abstract function queryTableInstance(Table$table);
	
	/**
	 * @deprecated since version 0.1
	 */
	public abstract function aliasedTableName();
}
