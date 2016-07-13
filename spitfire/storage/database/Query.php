<?php namespace spitfire\storage\database;

use Model;
use Exception;
use \spitfire\model\Field;

abstract class Query extends RestrictionGroup
{
	/** @var spitfire\storage\database\ResultSetInterface */
	protected $result;
	/** @var \spitfire\storage\database\QueryTable  */
	protected $table;
	
	protected $page = 1;
	protected $rpp = -1;
	protected $order;
	protected $groupby = null;
	
	/**
	 *
	 * @deprecated since version 0.1-dev 20160406
	 * @var int|null
	 */
	private $count = null;


	public function __construct($table) {
		$this->table = $this->queryTableInstance($table);
		
		#Initialize the parent
		parent::__construct(null, Array());
		$this->setType(RestrictionGroup::TYPE_AND);
	}
	
	/**
	 * 
	 * @param string $fieldname
	 * @param string $value
	 * @param string $operator
	 * @return Query
	 */
	public function addRestriction($fieldname, $value, $operator = '=') {
		$this->result = false;
		return parent::addRestriction($fieldname, $value, $operator);
	}
	
	/**
	 * 
	 * @deprecated since version 0.1-dev 20160406
	 * @param boolean $aliased
	 */
	public function setAliased($aliased) {
		$this->table->setAliased($aliased);
	}
	
	/**
	 * 
	 * @deprecated since version 0.1-dev 20160406
	 * @return boolean
	 */
	public function getAliased() {
		return $this->table->isAliased();
	}
	
	/**
	 * 
	 * @deprecated since version 0.1-dev 20160406
	 * @return int
	 */
	public function getId() {
		return $this->table->getId();
	}
	
	/**
	 * 
	 * @param int $id
	 * @deprecated since version 0.1-dev 20160406
	 * @return \spitfire\storage\database\Query
	 */
	public function setId($id) {
		$this->table->setId($id);
		return $this;
	}
	
	/**
	 * Since a query is the top Level of any group we can no longer climb up the 
	 * ladder.
	 * 
	 * @throws \spitfire\exceptions\PrivateException
	 */
	public function endGroup() {
		throw new \spitfire\exceptions\PrivateException('Called endGroup on a query', 201604031547);
	}
	
	public function getQuery() {
		return $this;
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
	
	//@TODO: Add a decent way to sorting fields that doesn't resort to this awful thing.
	public function setOrder ($field, $mode) {
		try {
			$this->order['field'] = $this->table->getTable()->getField($field);
		} catch (Exception $ex) {
			$physical = $this->table->getTable()->getModel()->getField($field)->getPhysical();
			$this->order['field'] = reset($physical);
		}
		
		$this->order['mode'] = $mode;
		return $this;
	}
	
	/**
	 * Returns a record from a databse that matches the query we sent.
	 * 
	 * @return Model
	 */
	public function fetch() {
		if (!$this->result) { $this->query(); }
		
		$data = $this->result->fetch();
		return  $data;
	}
	
	public function fetchAll() {
		if (!$this->result) { $this->query(); }
		return $this->result->fetchAll();
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
	
	/**
	 * Counts the number of records a query would return. If there is a grouping
	 * defined it will count the number of records each group would return.
	 * 
	 * @return type
	 */
	public function count() {
		if (!$this->groupby) {
			$query = $this->query(Array('count(*)'), true)->fetchArray();
			$count = $query['count(*)'];
			return $this->count = (int)$count;
		}
		elseif(count($this->groupby) === 1) {
			$_ret   = Array();
			$cursor = $this->query(Array(reset($this->groupby), 'count(*)'), true);
			
			while ($row = $cursor->fetchArray()) { $_ret[reset($row)] = end($row); }
			return $_ret;
		}
		
	}
	
	/**
	 * Defines a column or array of columns the system will be using to group 
	 * data when generating aggregates.
	 * 
	 * @param Field|Field[]|null $column
	 * @return Query Description
	 */
	public function aggregateBy($column) {
		if (is_array($column))   { $this->groupby = $column; }
		elseif($column === null) { $this->groupby = null; }
		else                     { $this->groupby = Array($column); }
		
		return $this;
	}
	
	
	/**
	 * Creates the execution plan for this query. This is an array of queries that
	 * aid relational DBMSs' drivers when generating SQL for the database.
	 * 
	 * This basically generate the connecting queries between the tables and injects
	 * your restrictions in between so the system egenrates logical routes that 
	 * will be understood by the relational DB.
	 * 
	 * @return Query[]
	 */
	public function makeExecutionPlan() {
		$_ret = $this->getPhysicalSubqueries();
		array_push($_ret, $this);
		return $_ret;
	}
	
	public function importRestrictions(Query$query) {
		$restrictions = $query->getRestrictions();
		
		foreach($restrictions as $r) {
			$copy = clone $r;
			$copy->setQuery($this);
			$this->putRestriction($copy);
		}
	}
	
	public function getOrder() {
		return $this->order;
	}
	
	public function getQueryTable() {
		return $this->table;
	}
	
	/**
	 * Returns the current 'query table'. This is an object that allows the query
	 * to alias it's table if needed.
	 * 
	 * @return QueryTable
	 */
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
			$restrictions = array_filter($restriction->getRestrictions(), Array(get_class(), __METHOD__));
			
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
	public abstract function compositeRestrictionInstance(Field$field = null, $value, $operator);
	
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
}
