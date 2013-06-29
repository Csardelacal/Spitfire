<?php

use spitfire\model\Field;
use spitfire\storage\database\Table;
use spitfire\storage\database\Query;

/**
 * A Model is a class used to define how Spitfire stores data into a DBMS. We
 * usually consider a DBMS as relational database engine, but Spitfire can
 * be connected to virtually any engine that stores data. Including No-SQL
 * databases and directly on the file system. You should even be able to use
 * tapes, although that would be extra slow.
 * 
 * Every model contains fields and references. Fields are direct data-types, they
 * allow storing things directly into them, while references are pointers to 
 * other models allowing you to store more complex data into them.
 * 
 * @author César de la Cal <cesar@magic3w.com>
 */
abstract class Model
{

	/**
	 * Contains a list of the fields that this model uses t ostore data. 
	 * Fields are stored in a FILO way, so the earlier you register a field
	 * the further left will it be on a database table (if you look at it 
	 * in table mode).
	 * 
	 * @var Field[]
	 */
	private $fields;
	
	/**
	 * Contains a reference to the table this model is 'templating'. This 
	 * means that the current model is attached to said table and offers to 
	 * it information about the data that is stored to the DBMS and the format
	 * it should hold.
	 *
	 * @var spitfire\storage\database\Table 
	 */
	private $table;
	
	/**
	 * Creates a new instance of the Model. This allows Spitfire to create 
	 * and manage data accordingly to your wishes on a DB engine without 
	 * requiring you to integrate with any concrete engine but writing code
	 * that SF will translate.
	 * 
	 * @param Table $table
	 */
	public final function __construct(Table$table) {
		#Define the Model's table as the one just received
		$this->table = $table;
		#Create a field called ID that automatically identifies records 
		$this->_id = new IntegerField(true);
		#Define _id as primary key and auto_increment
		$this->_id->setPrimary(true)->setAutoIncrement(true);
		#Call definitions
		$this->definitions();
		#Make the physical counterparts of the fields
		foreach ($this->fields as $field) {
			$field->makePhysical();
		}
	}
	
	/**
	 * This method is used to generate the 'template' for the table that allows
	 * spitfire to automatically generate tables and allows it to check the types
	 * of data and fix tables.
	 * 
	 * @abstract
	 */
	public abstract function definitions();

	/**
	 * Imports a set of fields. This allows to back them up in case they're 
	 * needed. Please note that the parent setting for them will be rewritten.
	 * 
	 * @param Field[] $fields
	 */
	public function setFields($fields) {
		#Loop through the fields to import them
		foreach($fields as $field) {
			$this->{$field->getName()} = $field; #This triggers the setter
		}
	}
	
	/**
	 * Returns the list of fields Spitfire considers necessary to hold the 
	 * data it generates. This means, it returns a list of the fields for 
	 * this model and a list of referenced fields for models that are
	 * in some way connected to this one.
	 * 
	 * This function is intended to be used by DBMS engines to ease the 
	 * work of generating schemas to store the data.
	 * 
	 * @deprecated since version 0.1.Dev
	 * @todo Remove
	 * @return Field[]
	 */
	public function getDBFields() {
		$fields = array_filter($this->fields);
		#If the given type is a field return it.
		foreach($fields as $name => $field) 
			if (!$field instanceof Field) unset($fields[$name]);
		return $fields;
	}
        
	/**
	 * Returns a logical field for this model. Logical fields refers to fields
	 * that can also contain complex datatypes aka References. Instead of 
	 * resoltving them like a DBFields function would do this returns a 
	 * raw reference when requested.
	 * 
	 * @param string $name
	 * @return Field|null
	 */
	public function getField($name) {
		if (isset($this->fields[$name]))   return $this->fields[$name];
		else return null;
	}
	
	/**
	 * Returns the whole list of fields this model contains. This are logical fields
	 * and therefore can contain data that is too complex to be stored directly
	 * by a DB Engine, the table object is in charge of providing a list of 
	 * DB Friendly fields.
	 * 
	 * @return Field[]
	 */
	public function getFields() {
		return $this->fields;
	}
	
	/**
	 * Returns a list of primary DB Fields needed for this model to work.
	 * 
	 * Primary database fields are considered thoe who indicate to any 
	 * DBMS that they identify in a unique way a certain record and are 
	 * therefore those who are inherited by child models that reference this 
	 * one.
	 * 
	 * @last-revision 2013-05-17
	 * @return Field[]
	 */
	public function getPrimaryDBFields() {
		$fields = $this->getDBFields();
		
		foreach($fields as $name => $content) {
			if (!$content->isPrimary()) unset($fields[$name]);
		}
		
		return $fields;
	}
	
	/**
	 * Returns the 'name' of the model. The name of a model is obtained by 
	 * removing the Model part of tit's class name. It's best practice to 
	 * avoid the usage of this function for anything rather than logging.
	 * 
	 * This function has a special use case, it also defines the name of the
	 * future table. By changing this you change the table this model uses
	 * on DBMS, this is particularly useful when creating multiple models
	 * that refer to a single dataset like 'People' and 'Adults'.
	 * 
	 * @staticvar string $name
	 * @return string
	 */
	public final function getName() {
		static $name = null;
		
		if ($name !== null) 
			return $name;
		else
			return $name = strtolower(substr(get_class($this), 0, 0 - strlen('Model')));
	}
	
	/**
	 * Returns the tablename spitfire considers best for this Model. This 
	 * value is calculated by using the Model's name and replacing any 
	 * <b>\</b> with hyphens to make the name database friendly.
	 * 
	 * Hyphens are the only chars that DBMS tend to accept that class names
	 * do not. So this way we avoid any colissions in names that could be 
	 * concidentally similar.
	 * 
	 * @return string
	 */
	public function getTableName() {
		return str_replace('\\', '-', $this->getName());
	}
	
	public function getTable() {
		return $this->table;
	}
	
	/**
	 * Extending this function on models allows you to add restrictions to a 
	 * query when this is made for this model. This way you can generate a 
	 * behavior similar to a view where the records are always limited by 
	 * the restriction set in this function.
	 * 
	 * This is especially useful when fake deleting records from the database.
	 * You use a flag to indicate a record is deleted and use this function
	 * to hide any records that have that flag.
	 * 
	 * @param Query $query The query that is being prepared to be executed.
	 * @return type
	 */
	public function getBaseRestrictions(Query$query) {
		//Do nothing, this is meant for overriding
	}
	
	/**
	 * Returns a list of fields which compound the primary key of this model.
	 * The primary key is a set of records that identify a unique record.
	 * 
	 * @return spitfire\storage\database\DBField[]
	 */
	public function getPrimary() {
		#Fetch the field list
		$fields = $this->getFields();
		#Drop the fields which aren't primary
		foreach ($fields as $name => $field) {
			if (!$field->isPrimary()) unset($fields[$name]);
		}
		#Return the cleared array
		return $fields;
	}
	
	/**
	 * The getters and setters for this class allow us to create fields with
	 * a simplified syntax and access them just like they were properties
	 * of the object. Please note that some experts recommend avoiding magic
	 * methods for performance reasons. In this case you can use the field()
	 * method.
	 * 
	 * @param type $name
	 * @param \spitfire\model\Field $value
	 */
	public function __set($name, $value) {
		
		if ($value instanceof Field) {
			$value->setName($name);
			$value->setModel($this);
			$this->fields[$name] = $value;
		}
		
	}
	
	/**
	 * The getters and setters for this class allow us to create fields with
	 * a simplified syntax and access them just like they were properties
	 * of the object. Please note that some experts recommend avoiding magic
	 * methods for performance reasons. In this case you can use the field()
	 * method.
	 * 
	 * @param string $name
	 * @throws privateException
	 * @return Field
	 */
	public function __get($name) {
		if (isset($this->fields[$name]))
			return $this->fields[$name];
		else
			throw new privateException('No field ' . $name . ' found');
	}
	
	/**
	 * 
	 * @param string $name
	 * @throws privateException
	 */
	public function __unset($name) {
		if (isset($this->fields[$name]))
			unset($this->fields[$name]);
		else
			throw new privateException('No field ' . $name . ' found');
	}
	
}
