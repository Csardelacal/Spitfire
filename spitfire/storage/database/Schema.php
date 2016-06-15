<?php namespace spitfire\storage\database;

use spitfire\model\Field;
use spitfire\storage\database\Table;
use spitfire\storage\database\Query;
use spitfire\exceptions\PrivateException;
use IntegerField;

/**
 * A Schema is a class used to define how Spitfire stores data into a DBMS. We
 * usually consider a DBMS as relational database engine, but Spitfire can
 * be connected to virtually any engine that stores data. Including No-SQL
 * databases and directly on the file system. You should even be able to use
 * tapes, although that would be extra slow.
 * 
 * Every model contains fields and references. Fields are direct data-types, they
 * allow storing things directly into them, while references are pointers to 
 * other models allowing you to store more complex data into them.
 * 
 * @property IntegerField $_id This default primary key integer helps the system 
 * locating the records easily.
 * 
 * @author César de la Cal <cesar@magic3w.com>
 */
class Schema
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
	 * @var Table 
	 */
	private $table;
	
	/**
	 * The name of the table that represents this schema on DBMS' side. This will
	 * be automatically generated from the class name and will be replacing the 
	 * invalid inverted bar (\) with hyphens (-) that are not valid as a class name.
	 *
	 * @var string
	 */
	private $name;
	
	/**
	 * Creates a new instance of the Model. This allows Spitfire to create 
	 * and manage data accordingly to your wishes on a DB engine without 
	 * requiring you to integrate with any concrete engine but writing code
	 * that SF will translate.
	 * 
	 * @param Table $table
	 */
	public final function __construct($name, Table$table = null) {
		#Define the Model's table as the one just received
		$this->table = $table;
		$this->name  = $name;
		
		#Create a field called ID that automatically identifies records 
		$this->_id = new IntegerField(true);
		
		#Define _id as primary key and auto_increment
		$this->_id->setPrimary(true)->setAutoIncrement(true);
	}

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
	 * Returns a logical field for this model. "Logical field" refers to fields
	 * that can also contain complex datatypes aka References. 
	 * 
	 * You can use the Field::getPhysical() method to retrieve the physical fields
	 * the application uses to interact with the DBMS.
	 * 
	 * @param string $name
	 * @return Field|null
	 */
	public function getField($name) {
		if (isset($this->fields[$name])) { return $this->fields[$name]; }
		else                             { return null; }
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
		return $this->name;
	}
	
	/**
	 * Returns the tablename spitfire considers best for this Model. This 
	 * value is calculated by using the Model's name and replacing any 
	 * <b>\</b> with hyphens to make the name database friendly.
	 * 
	 * Hyphens are the only chars that DBMS tend to accept that class names
	 * do not. So this way we avoid any colissions in names that could be 
	 * coincidentally similar.
	 * 
	 * @return string
	 */
	public function getTableName() {
		return str_replace('\\', '-', $this->getName());
	}
	
	/**
	 * Returns the table the Schema represents. The schema is the logical representation
	 * of a Table. While the Schema will manage logical fields that the programmer
	 * can directly write data to, the Table will take that data and translate it
	 * so the database engine can use it.
	 * 
	 * @return Table
	 */
	public function getTable() {
		return $this->table;
	}
	
	/**
	 * Sets the table this schema manages. This connection is used to determine 
	 * what DBMS table it should address and to make correct data conversion.
	 * 
	 * @param Table $table
	 */
	public function setTable($table) {
		$this->table = $table;
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
	 * @todo Move to the model. It makes no sense having it here
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
	 * @return Field[]
	 */
	public function getPrimary() {
		#Fetch the field list
		$fields = $this->getFields();
		#Drop the fields which aren't primary
		foreach ($fields as $name => $field) {
			if (!$field->isPrimary()) { unset($fields[$name]); }
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
	 * @throws PrivateException
	 * @return Field
	 */
	public function __get($name) {
		if (isset($this->fields[$name])) { return $this->fields[$name]; }
		else { throw new PrivateException('Schema: No field ' . $name . ' found'); }
	}
	
	/**
	 * Removes a field from the Schema. This is a somewhat rare method, since you
	 * should avoid it's usage in production environments and you should REALLY 
	 * know what you're doing before using it.
	 * 
	 * @param string $name
	 * @throws PrivateException
	 */
	public function __unset($name) {
		if (isset($this->fields[$name])) { unset($this->fields[$name]); }
		else { throw new PrivateException('Schema: Could not delete. No field ' . $name . ' found'); }
	}
	
}
