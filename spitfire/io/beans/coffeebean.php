<?php

use spitfire\io\beans\ChildBean;
use spitfire\storage\database\Table;
use spitfire\model;

use spitfire\io\beans\TextField;
use spitfire\io\beans\LongTextField;
use spitfire\io\beans\FileField;
use spitfire\io\beans\ReferenceField;
use spitfire\io\beans\DateTimeField;

/**
 * A Bean is the equivalent to a Model for users. Instead of generating SQL and
 * reading resultsets a Bean generates forms and reads the POST data they 
 * generate. This allows to automate data i/o tasks with users and quickly 
 * produce applications that interact with the user.
 * 
 * @author César de la Cal <cesar@magic3w.com>
 */
abstract class CoffeeBean extends Validatable
{
	
	const VISIBILITY_HIDDEN = 0;
	const VISIBILITY_LIST   = 1;
	const VISIBILITY_FORM   = 2;
	const VISIBILITY_ALL    = 3;
	
	const STATUS_SUBMITTED_OK  = 2;
	const STATUS_SUBMITTED_ERR = 1;
	const STATUS_UNSUBMITTED   = 0;
	
	private static $counter = 0;
	private $id = null;
	
	private $fields = Array();
	private $record;
	private $parent;
	private $table;
	private $postdata = null;
	
	public $name;
	public $model;
	
	/**
	 * Create a new bean. This allows to generate forms to receive data from a 
	 * client, it requires a Table to know which model it shall work on.
	 * 
	 * @param \spitfire\storage\database\Table $table
	 */
	public final function __construct(Table$table) {
		$this->table = $table;
		$this->definitions();
	}
	
	/**
	 * Creates the fields for this bean. By doing so the bean knows which fields
	 * it can present to the user to input data.
	 */
	abstract public function definitions();

	/**
	 * This function informs you about the status of the bean. This status
	 * can take three different values.
	 * <ul>
	 * <li>STATUS_SUBMITTED_OK: If the bean did receive data and it is valid.</li>
	 * <li>STATUS_SUBMITTED_ERR: If the data was received but not valid</li>
	 * <li>STATUS_UNSUBMITTED: If the data wasn't received at all</li>
	 * </ul>
	 * 
	 * This function is meant to aid you taking the decision whether the bean
	 * should display a form or store the data. To do so you can compare the
	 * values or compare status to be less (&lt;) than OK.
	 * 
	 * @return int Status code of the submission
	 */
	public function getStatus() {
		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			if ($this->validate())
				return self::STATUS_SUBMITTED_OK;
			else
				return self::STATUS_SUBMITTED_ERR;
		}
		else {
			return self::STATUS_UNSUBMITTED;
		}
	}
	
	public function updateDBRecord() {
		
		$record = $this->record;
		
		if ($this->table) {
			$fields = $this->fields;
			foreach ($fields as $field) {
				if (null != $value = $field->getValue())
					$record->{$field->getFieldName()} = $value;
			}
		}
	}
	
	public function setDBRecord($record) {
		$this->id = self::$counter++;
		if ($record instanceof databaseRecord || is_null($record))
		$this->record = $record;
	}
	
	/**
	 * Returns the current record this bean is representing. This will be used to
	 * populate the form in case there is no data being sent to the form.
	 * 
	 * @return \databaseRecord
	 */
	public function getRecord() {
		return $this->record;
	}
	
	/**
	 * Creates a new field for the bean.
	 * 
	 * @param string $instanceof
	 * @param string $name
	 * @param string $caption
	 * @param string $method
	 * @return spitfire\io\beans\Field
	 */
	public function field($field, $caption) {
		$logical = $this->table->getModel()->getField($field);
		
		if (!$logical) throw new privateException('No field ' . $field);
		
		switch($logical->getDataType()) {
			case model\Field::TYPE_STRING:
			case model\Field::TYPE_INTEGER:
			case model\Field::TYPE_LONG:
				return $this->fields[$field] = new TextField($this, $logical, $caption);
				break;
			case model\Field::TYPE_DATETIME:
				return $this->fields[$field] = new DateTimeField($this, $logical, $caption);
				break;
			case model\Field::TYPE_TEXT:
				return $this->fields[$field] = new LongTextField($this, $logical, $caption);
				break;
			case model\Field::TYPE_FILE:
				return $this->fields[$field] = new FileField($this, $logical, $caption);
				break;
			case model\Field::TYPE_REFERENCE:
				return $this->fields[$field] = new ReferenceField($this, $logical, $caption);
				break;
			case model\Field::TYPE_CHILDREN:
				return $this->fields[$field] = new ChildBean($this, $logical, $caption);
				break;
		}
	}
	
	public function getFields() {
		return $this->fields;
	}
	
	public function getField($name) {
		return $this->fields[$name];
	}
	
	/**
	 * Returns the table using this bean to generate it's forms.
	 * 
	 * @return spitfire\storage\database\Table
	 */
	public function getTable() {
		return $this->table;
	}
	
	public function getName() {
		if ($this->name) return $this->name;
		else return substr( get_class ($this), 0, - strlen('Bean'));
	}

	public function makeForm($renderer) {
		return $renderer->renderForm($this);
	}
	
	public function makeList($renderer, $records) {
		return $renderer->renderList($this, $records);
	}
	
	public function setParent($field) {
		$this->parent = $field;
	}
	
	public function getParent() {
		return $this->parent;
	}
	
	public function setPostData($postdata = null) {
		$this->postdata = $postdata;
	}
	
	public function getPostData() {
		if ($this->postdata !== null) return $this->postdata;
		else return $_POST;
	}
	
	public function getId() {
		return $this->id;
	}
	
	/**
	 * Check if the contents of this bean are valid.
	 * 
	 * @param mixed $data Is ignored
	 * @return boolean
	 */
	public function validate($data = null) {
		foreach ($this->fields as $field => $content) {
			if (!($content instanceof ChildBean))
			$data[$field] = $content->getValue(); 
		}
		return parent::validate($data);
	}

	
	/**
	 * Returns an instance of a required bean.
	 * 
	 * @param type $name The classname of the bean without Bean at the end of
	 *                   the string.
	 * 
	 * @return CoffeeBean
	 */
	public static function getBean($name) {
		#Create a camel cased string for the class
		$class_name = ucfirst($name) . 'Bean';
		
		#Check if it exists and instance
		if (class_exists($class_name)) {
			return new $class_name();
		}
		else throw new privateException('Bean not found');
	}
	
}