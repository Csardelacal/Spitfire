<?php

namespace spitfire\io\beans;

use \CoffeeBean;
use spitfire\io\renderers\RenderableFieldSelect;
use spitfire\io\renderers\RenderableFieldArray;

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
class ManyToManyField extends Field implements RenderableFieldSelect, RenderableFieldArray
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
	 * Returns the data posted to this childbean. This will be an array containing
	 * the data sent by the form as arrays.
	 * 
	 * @return Model[]
	 */
	public function getRequestValue() {
		
		#Check if the request is done via POST. Otherwise return an empty array.
		if ($_SERVER['REQUEST_METHOD'] != 'POST') throw new \privateException("Nothing posted");
		
		#Post will contain an array of subforms for this element.
		$postdata= $this->getPostData();
		$_return = Array();
		$table   = $this->getField()->getTarget()->getTable();
		
		if (empty($postdata)) {throw new \privateException("nothing sent");}
		
		#Loop through the passed array and create the subforms to handle the data
		foreach ($postdata as $pk) {
			
			$record = $table->getById($pk);
			if ($record) { $_return[] = $record; }
			
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
	 * @return spitfire\io\beans\ChildBean
	 */
	public function setMinimumEntries($amt) {
		$this->min_entries = $amt;
		return $this;
	}
	
	/**
	 * Returns the array of records that this field contains.
	 * 
	 * @return \Model[]
	 */
	public function getDefaultValue() {
		if ( ($record = $this->getBean()->getRecord()) !== null)
			return $record->{$this->getField()->getName()};
		else
			return null;
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
		else { return CoffeeBean::VISIBILITY_FORM; }
	}

	public function getPostTargetFor($name) {
		return null;
	}

	public function getEnforcedFieldRenderer() {
		return null;
	}
	
	public function getOptions() {
		$opts = $this->getField()->getTarget()->getTable()->getAll()->fetchAll();
		$_return = Array();
		
		foreach ($opts as $opt) {$_return[implode(':', $opt->getPrimaryData())] = strval($opt);}
		
		return $_return;
	}
	
	public function getPartial($str) {
		return $this->getField()->getTable()->get(null, $str)->fetchAll();
	}

	public function getSelectCaption($id) {
		return $this->getField()->getTarget()->getTable()->getById($id);
	}

	public function getSelectId($caption) {
		$values = $this->getValue();
		
		foreach ($values as $value) {
			if (strval($value) === $caption) {
				return implode(':', $value->getPrimaryData());
			}
		}
	}
	
	public function getPostId() {
		return parent::getPostId() . '[]';
	}

	public function addRule(\spitfire\validation\ValidationRule $rule) {
		//TODO
	}

	public function getMessages() {
		//TODO
	}

	public function isOk() {
		//TODO
	}

	public function validate() {
		//TODO
	}

}