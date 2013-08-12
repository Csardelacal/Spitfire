<?php

namespace spitfire\storage\database;

use spitfire\storage\database\Query;
use spitfire\storage\database\Table;
use spitfire\model\Field;

abstract class QueryJoin
{
	/**
	 * The query that is referencing the target.
	 *
	 * @var spitfire\storage\database\Query
	 */
	private $srcQuery;
	
	/**
	 * The query referenced
	 *
	 * @var spitfire\storage\database\Query
	 */
	private $targetQuery;
	
	/**
	 *
	 * @var spitfire\model\Field
	 */
	private $field;
	
	/**
	 *
	 * @var spitfire\storage\database\Table 
	 */
	private $bridge;

	function __construct(Query $srcQuery, Query$targetQuery, Field $field) {
		$this->srcQuery = $srcQuery;
		$this->targetQuery = $targetQuery;
		$this->field = $field;
	}
	
	public function getSrcQuery() {
		return $this->srcQuery;
	}

	public function setSrcQuery(spitfire\storage\database\Query $srcQuery) {
		$this->srcQuery = $srcQuery;
	}

	public function getTargetQuery() {
		return $this->targetQuery;
	}

	public function setTargetQuery(spitfire\storage\database\Query $targetQuery) {
		$this->targetQuery = $targetQuery;
	}

	public function getField() {
		return $this->field;
	}

	public function setField(Field$field) {
		$this->field = $field;
	}

	public function getBridge() {
		return $this->bridge;
	}

	public function setBridge(Table $bridge) {
		$this->bridge = $bridge;
	}
	
	abstract public function __toString();

	
}