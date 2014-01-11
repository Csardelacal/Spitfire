<?php namespace spitfire\model\adapters;

use Validatable;
use spitfire\model\Field;
use Model;

/**
 * This base adapter allows overriding methods to let it handle the actual getting,
 * setting and sync tasks to this base adapter. Allowing them to reduce duplicate
 * code from the actual returning and storing data and focusing on their tasks.
 */
abstract class baseAdapter implements AdapterInterface, Validatable
{
	/**
	 * This is the logical field the adapter contains data for. Allowing the system
	 * to check the type of the field and several other tasks that are necessary.
	 * 
	 * @var \spitfire\model\Field
	 */
	private $field;
	
	/**
	 * This is the model this field belongs to. This makes it possible for this 
	 * object to act accordingly to a certain content (for example, you want to 
	 * deliver data about a relationship between two elements).
	 * 
	 * @var \Model 
	 */
	private $model;
	
	/**
	 * This property holds the data as it has been fetched from the database. This
	 * can cause issues when holding very highly concurrent data as the data could
	 * have been modified on the database and stored already. Rolling back does 
	 * not verify the data on the DBMS hasn't changed.
	 *
	 * @var mixed
	 */
	private $src;
	
	/**
	 * The current data stored in this adapter. The data can be validated before 
	 * being sent to the DBMS and parsed before being used in this system. This
	 * is the data the system usually handles as there is no use for the data that
	 * was originally placed inside the DB.
	 *
	 * @var mixed 
	 */
	private $data;
	
	/**
	 * Creates a new Adapter. The adapter creates a bridge between a field, a model
	 * and the data it contains, the model can use this to create a consistent data
	 * relation between the DB and the programmer's scope.
	 * 
	 * @param \spitfire\model\Field $field
	 * @param \Model $model
	 */
	public function __construct(Field$field, Model$model) {
		$this->field = $field;
		$this->model = $model;
	}
	
	/**
	 * Returns the data as it should be stored by the DBMS. Please note that most
	 * DBMS don't accept any complex types like Arrays and Objects directly, this
	 * method allows to prepare the data accordingly before storing it.
	 * 
	 * @return mixed
	 */
	public function dbGetData() {
		return $this->data;
	}
	
	/**
	 * When the database defines the data it is important that it also overrides 
	 * the src element, setting this element as in sync again. This is important
	 * as otherwise the element will find data that is not in sync even though
	 * it was.
	 * 
	 * @param mixed $data
	 */
	public function dbSetData($data) {
		$this->data = $data;
		$this->src  = $data;
	}
	
	/**
	 * Returns the field (database column) the adapter is holding data for. This 
	 * allows the driver to test for the field or getting it's name when needed.
	 * 
	 * @return \spitfire\model\Field
	 */
	public function getField() {
		return $this->field;
	}
	
	/**
	 * Returns the Model containing this adapter. Allowing to have objects which 
	 * do not have a reference to the model it belongs to to read the data.
	 * 
	 * @return \Model
	 */
	public function getModel() {
		return $this->model;
	}

	public function isSynced() {
		return $this->data == $this->src;
	}

	public function usrGetData() {
		return $this->data;
	}

	public function usrSetData($data) {
		$this->data = $data;
	}

	public function commit() {
		$this->src = $this->data;
	}

	public function rollback() {
		$this->data = $this->src;
	}
	
	public function validate() {
		return $this->field->validate($this->data);
	}

}