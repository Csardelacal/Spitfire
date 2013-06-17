<?php

namespace spitfire\storage\database;

use spitfire\model\Field;

/**
 * The 'database field' class is an adapter used to connect logical fields 
 * (advanced fields that can contain complex data) to simplified versions 
 * that common DBMSs can use to store this data.
 * 
 * This class should be extended by each driver to allow it to use them in an 
 * efficient manner for them.
 */
abstract class DBField
{
	/**
	 * Contains a reference to the logical field that contains information
	 * relevant to this field.
	 * 
	 * @var spitfire\model\Field 
	 */
	private $logical;
	
	/**
	 * Provides a name that the DBMS should use to name this field. Usually
	 * this will be exactly the same as for the logical field, except for 
	 * fields that reference others.
	 * 
	 * @var string
	 */
	private $name;
	
	/**
	 * This allows the field to provide information about a field it refers 
	 * to on another table on the same DBMS. This helps connecting the fields
	 * and generating links on the DBMS that allow the engine to optimize
	 * queries that Spitfire generates.
	 * 
	 * @var spitfire\storage\database\DBField 
	 */
	private $referenced;
	
	/**
	 * Creates a new Database field. This fields provide information about 
	 * how the DBMS should hadle one of Spitfire's Model Fields. The Model 
	 * Fields, also referred to as Logical ones can contain data that 
	 * requires several DBFields to store, this class creates an adapter
	 * to easily handle the different objects.
	 * 
	 * @param \spitfire\model\Field $logical
	 * @param string $name
	 * @param \spitfire\storage\database\DBField $references
	 */
	public function __construct(Field$logical, $name, DBField$references = null) {
		$this->logical = $logical;
		$this->name = $name;
		$this->referenced = $references;
	}
	
	/**
	 * Returns the fully qualified name for this column on the DBMS. Fields
	 * referring to others will return an already prefixed version of them
	 * like 'field_remote'.
	 * 
	 * In order to obtain the field name you can request it from the logical 
	 * field. And to obtain the remote name you can request it from the 
	 * referenced field.
	 * 
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}
	
	public function setName($name) {
		$this->name = $name;
	}
	
	/**
	 * Returns the field this one is referencing to. This allows the DBMS to 
	 * establish relations. In case this field does not reference any other
	 * it'll return null.
	 * 
	 * @return \spitfire\storage\database\DBField|null
	 */
	public function getReferencedField() {
		return $this->referenced;
	}
	
	/**
	 * Defines which field is referenced by this one, this allows the DBMS to set
	 * relations from one field to another and enforce referential integrity.
	 * 
	 * @param \spitfire\storage\database\DBField $referenced
	 */
	public function setReferencedField(DBField$referenced) {
		$this->referenced = $referenced;
	}


	/**
	 * Returns the logical field that contains this Physical field. The
	 * logical field contains relevant data about what data it contains
	 * and how it relates.
	 * 
	 * @return \spitfire\model\Field
	 */
	public function getLogicalField() {
		return $this->logical;
	}
	
	/**
	 * Defines which logical field this belongs to. The logical field is the one 
	 * in charge to provide data about the field and the data it contains.
	 * 
	 * @param Field $logical
	 */
	public function setLogicalField(Field$logical) {
		$this->logical = $logical;
	}
	
	/**
	 * This function returns the table this field belongs to. This function 
	 * is a convenience shortcut that allows retrieving the Table without 
	 * needing to read all the intermediate layers that compound the logical
	 * template of the Table.
	 * 
	 * @return \spitfire\storage\database\Table
	 */
	public function getTable() {
		return $this->getLogicalField()->getModel()->getTable();
	}
	
}