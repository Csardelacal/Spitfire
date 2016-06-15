<?php

namespace spitfire\io\beans;

use CoffeeBean;
use Model;
use spitfire\exceptions\PrivateException;
use spitfire\io\renderers\RenderableFieldGroup;
use spitfire\validation\ValidationRule;
use Strings;

/**
 * This class allows a bean to receive data that belongs to this but is handled
 * by another bean. In this case the childbean will take care of handling the 
 * data and returning it to the parent bean.
 * 
 * Basically this generates what other apps call sub-forms, a form that allows 
 * you to handle data in a clean way.
 * 
 * @author César de la Cal <cesar@magic3w.com>
 * @since 0.1
 */
class ChildBean extends Field implements RenderableFieldGroup
{
	
	/**
	 * This variable indicates the minimum amount of subforms this child should 
	 * display when rendered. By default childbeans display the number of elements
	 * plus another empty spot to add a new one. If this is different than 0 it 
	 * will display at least as many as this requires.
	 * 
	 * @var int
	 */
	private $min_entries = 0;
	
	/**
	 * Contains a list of coffeebeans used to render this thing. They will also be
	 * responsible for generating and validating the data they receive.
	 *
	 * @var \CoffeeBean[]
	 */
	private $beans = Array();
	
	/**
	 * Returns the data posted to this childbean. This will be an array containing
	 * the data sent by the form as arrays.
	 * 
	 * @return mixed[]
	 */
	public function getRequestValue() {
		
		#Check if the request is done via POST. Otherwise return an empty array.
		if ($_SERVER['REQUEST_METHOD'] != 'POST') {
			throw new PrivateException("Invalid request method. Requires POST");
		}
		
		#Post will contain an array of subforms for this element.
		$data    = $this->getPostData();
		$_return = Array();
		
		#Test if the postdaa contains anything at all
		if (!is_array($data) || !count(array_filter($data))) {
			throw new PrivateException("Post was empty");
		}
		
		#Loop through the passed array and create the subforms to handle the data
		foreach ($data as $pk => $post) {
			if (!count(array_filter($post, function ($v) {
				return is_array($v)? !!array_filter($v) : !!$v;
			}))) { continue; }
			
			$table = $this->getField()->getTarget()->getTable();
			$child = $table->getBean();
			
			if (substr($pk, 0, 5) == '_new_')
				$r = $table->newRecord();
			else
				$r = $table->getById($pk);
			
			if ($r !== null) {
				$child->setParent($this);
				$child->setDBRecord($r);
				$child->setPostData($post);
				$child->updateDBRecord();

				$_return[] = $child->getRecord();
			}

		}

		return $_return;
	}
	
	/**
	 * Returns the minimum amount of elements that the element holds. This allows
	 * you to define the amount of empty forms displayed if no data has been set.
	 * 
	 * @return int
	 */
	public function getMinimumEntries() {
		return $this->min_entries;
	}
	
	/**
	 * Defines the minimum amount of elements that the element holds. This allows
	 * you to define the amount of empty forms displayed if no data has been set.
	 * 
	 * This method returns $this to allow method chaining.
	 * 
	 * @return ChildBean
	 */
	public function setMinimumEntries($amt) {
		$this->min_entries = $amt;
		return $this;
	}
	
	/**
	 * Returns the array of records that this field contains.
	 * 
	 * @return Model[]
	 */
	public function getDefaultValue() {
		if ( ($record = $this->getBean()->getRecord()) !== null)
			return $record->{$this->getField()->getName()};
		else
			return Array();
	}
	
	/**
	 * This method overrides the normal behavior of fields that allows to define
	 * their visibility to match the user's needs. Childbeans enforce only being
	 * displayed on the form and avoid being displayed on the listings.
	 * 
	 * @return int
	 */
	public function getVisibility() {
		if ($this->getBean()->getParent()) { return CoffeeBean::VISIBILITY_HIDDEN; }
		return CoffeeBean::VISIBILITY_FORM;
	}
	
	public function makeFields() {
		$val = $this->getValue();
		foreach ($val as $v) {
			#If the element is it's own child we avoid conflicts.
			if ($v->getPrimaryData() === $this->getBean()->getRecord()->getPrimaryData() && $v->getTable()->getModel() === $this->getBean()->getTable()->getModel()) {continue; }
			$pd = $v->getPrimaryData();
			if (count(array_filter($pd))) { $this->getPostTargetFor(implode(':', $pd)); }
		}
		
		do {
			$this->getPostTargetFor('_new_' . count($this->beans));
		} while (count($this->beans) < $this->getMinimumEntries());
	}
	
	public function clearPostData() {
		parent::clearPostData();
		$this->beans = Array();
	}

	public function getFields() {
		$this->makeFields();
		return $this->beans;
	}

	public function getPostTargetFor($name) {
		if (isset($this->beans[$name])) { return $this->beans[$name]; }
		$table = $this->getField()->getTarget()->getTable();
		$child = $table->getBean();
		
		if (Strings::startsWith($name, '_new_')) {
			$r = $table->newRecord();
		} else {
			$r = $table->getById($name);
		}
		
		$child->setParent($this);
		$child->setDBRecord($r);
		$child->setName($name);
		return $this->beans[$name] = $child;
	}

	public function validate() {
		//TODO: Add validation from database fields.
	}

	public function getEnforcedFieldRenderer() {
		return null;
	}

	public function addRule(ValidationRule $rule) {
		//TODO
	}

	public function getMessages() {
		//TODO
	}

	public function isOk() {
		//TODO
	}

}