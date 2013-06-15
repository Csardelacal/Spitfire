<?php

use spitfire\model\Field;
use spitfire\model\Reference;
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
	 * @todo Document
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
		$fields = array_filter(array_merge($this->fields, $this->getReferencedFields()));
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
	 * Returns a list of the fields referenced by this model on a different
	 * model. Depending on the parameters you set you can change the behavior
	 * of this function.
	 * 
	 * With no parameters it returns all the fields that this model uses to
	 * reference remote models.
	 * 
	 * [NOTICE] This function is intended to aid the development of database
	 * drivers and functions and does not actually perform any task on the 
	 * model. All the fields are generated during runtime and not cached.
	 * 
	 * @param Model $target When setting this parameter you will receive only
	 *          the fields inside your target model.
	 * 
	 * @param string $role When several relations exist this parameter defines
	 *          which relation is used.
	 * 
	 * @return Field[]
	 * @throws privateException
	 */
	public function getReferencedFields(Model$target = null, $role = null) {
		#Init the fields array to be returned
		$fields = Array();
		
		#If a target model is set get the related fields
		if (is_null($target)) {
			$references = $this->references;
		}
		else {
			#Find a model which is referenced by this
			$references = Array($this->getReference($target, $role));
		}
		
		#Get the fields for the target model(s)
		foreach ($references as $reference) {
			$fields = array_merge($fields, $reference->getFields());
		}
		return $fields;
	}
	
	/**
	 * Return the list of references this model uses to refer data placed on
	 * remote models.
	 * 
	 * @return Reference[]
	 */
	public function getReferences() {
		return $this->references;
	}
	
	/**
	 * Returns the reference this model uses to connect to the target model
	 * you define in the parameters. In case a reference appears repeteadly
	 * you need to specify the role you want the reference for.
	 * 
	 * @param Model $target
	 * @param string $role
	 * @return Reference
	 * @throws privateException In case you use a model that is referenced 
	 *       several times but indicate no valid role
	 */
	public function getReference(Model$target, $role = null) {
		#Initialize an array to store references
		$references = Array();
		
		#Loop through the references to retrieve those which reference the target
		foreach ($this->references as /** @var Reference */ $r) {
			$model = $r->getTarget();
			if ($model == $target) {
				$references[$r->getRole()] = $r;
			}
		}

		#If the array is empty we need to inform that nothing was found
		if (count($references) == 0) {
			throw new privateException("Model {$target->getName()} not referenced by {$this->getName()}");
		}

		#If the array contains more than one detect this condition and get the right role.
		if (count($references) > 1) {
			if ($role) {
				if (isset ($references[$role]))
					return $references[$role];
				else
					throw new privateException('No valid role specified, role ' . $role . 'does not exist');
			} else {
				throw new privateException('Model ' . $this->getName() . ' has several roles for ' . $target->getName() );
			}
		}
		
		return reset($references);
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
	public function getName() {
		static $name;
		if ($name) return $name;
		
		$name = substr(get_class($this), 0, 0 - strlen('Model'));
		return strtolower($name);
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
	 * Creates a new relation between two tables. This allows us to 'walk'
	 * the relationships when in ORM mode. It greatly improves speed when
	 * coding.
	 * 
	 * @param string|Model $model
	 * @param string $alias
	 * @return Reference
	 * @throws privateException
	 */
	public function reference($model, $alias = null) {
		#If the alias is empty we give it a default alias.
		if (is_null($alias)) {
			$alias = ($model instanceof Model)? $model->getName() : $model;
		}
		
		#Check if the alias is already taken.
		if (isset($this->references[$alias])) {
			throw new privateException('Defined two references with the same alias');
		}
		
		#If the object refers to itself it could cause a loop
		if (is_string($model) && $model == $this->getName()) {
			$model = $this;
		}
		
		#If the reference is not a model try to fetch it.
		if (!$model instanceof Model) {
			$model = Model::getInstance($model);
		}
		
		$ref = new Reference($this, $model, $alias);
		return $this->references[$alias] = $ref;
	}
	
	/**
	 * This function SHOULD NOT be used. It serves the purpose of modifying 
	 * the model in case of extreme need. Also, does not destroy existing
	 * links in databases.
	 * 
	 * Deletes a reference by receiving the alias / role of the relationship
	 * and dropping it from the relationships list. Use it to avoid the use 
	 * of functions that rely on the connection between this model and a
	 * parent.+
	 * 
	 * @param string $alias
	 */
	public function unreference($alias) {
		unset($this->references[$model]);
	}
	
	/**
	 * Adds a new field to the model. Fields are the basic unit of storage
	 * on Models allowing you to store different basic types of data. You can
	 * use custom data types although this can provoke problems when connecting
	 * the model to the database that should handle this data.
	 * 
	 * @param string $name
	 * @param string $instanceof Accepts any valid classname
	 * @param int $length
	 * @return Field
	 */
	public function field($name, $instanceof, $length = false) {
		$this->fields[$name] = $field = new $instanceof($length);
		$field->setName($name);
		return $field;
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
	 * @param type $name
	 * @return Field
	 */
	public function __get($name) {
		if (isset($this->fields[$name]))
			return $this->fields[$name];
		else
			throw new privateException('No field ' . $name . ' found');
	}
	
}
