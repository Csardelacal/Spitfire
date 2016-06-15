<?php

namespace spitfire\io\beans;

use CoffeeBean;
use spitfire\exceptions\PrivateException;
use spitfire\io\PostTarget;
use spitfire\validation\ValidatorInterface;

/**
 * A bean field is an object that allows the bean to retrieve the data it needs
 * from a POST request. Additionally it provides the renderer classes with 
 * valuable data about the contents this field expects and how it should name
 * the fields of the form.
 * 
 * @author César de la Cal <cesar@magic3w.com>
 * @since  0.1
 * @last-revision 2013-07-01
 */
abstract class Field extends PostTarget implements ValidatorInterface
{
	/**
	 * The bean this field currently belongs to. This is used by the Field to 
	 * retrieve the data from the eban's current record, and information about it's
	 * parent.
	 * 
	 * @var \CoffeeBean
	 */
	private $bean;
	
	/**
	 * The name that identifies this field. This is used to alias a field and hide
	 * it's database column name, this is mainly useful if you're paranoic about
	 * people knowing the structure of your database. Otherwise it is pretty 
	 * useless.
	 *
	 * @var string
	 */
	private $name;
	
	/**
	 * This is the field this bean represents on the current Model, it allows the 
	 * field to retrieve information about the data-type it contains. It also
	 * tells the bean field where the data for the form is.
	 *
	 * @var \spitfire\model\Field
	 */
	private $field;
	
	/**
	 * The string that is displayed on the label next to the input for this field.
	 * Only intended to make the name more user-friendly for users.
	 *
	 * @var string
	 */
	private $caption;
	
	/**
	 * Sets the visibility of a field. By default the visibility of each field is
	 * to be visibile everywhere, this means you will se this field displayed on
	 * the listing and the form.
	 *
	 * @var int
	 */
	private $visibility = CoffeeBean::VISIBILITY_ALL;
	
	/**
	 * Creates a new Bean Field, this is a connector between the bean and a model's
	 * field meant to ease the creation of Forms or POST input sanitization.
	 * 
	 * @param CoffeeBean $bean
	 * @param \spitfire\model\Field $field
	 * @param string $caption
	 */
	public function __construct(CoffeeBean$bean, \spitfire\model\Field$field, $caption) {
		$this->bean = $bean;
		$this->field = $field;
		$this->caption = $caption;
	}
	
	public function setBean(CoffeeBean$bean) {
		$this->bean = $bean;
		return $this;
	}
	
	public function getBean() {
		return $this->bean;
	}
	
	public function setName($name) {
		$this->name = $name;
		return $this;
	}
	
	public function getName() {
		return (!$this->name)? $this->field->getName() : $this->name;
	}
	
	public function getPostId() {
		$name = $this->getName();
		$parent = $this->bean->getPostId();
		return "{$parent}[{$name}]";
	}
	
	public function setCaption($caption) {
		$this->caption = $caption;
		return $this;
	}
	
	/**
	 * Returns the field this one represents on the model. This provides the field
	 * and the renderers with information about the data it can contain.
	 * 
	 * @return \spitfire\model\Field
	 */
	public function getField() {
		return $this->field;
	}
	
	public function getFieldName() {
		return $this->field->getName();
	}
	
	public function getCaption() {
		return $this->caption;
	}
	
	public function getValue() {
		try {
			return $this->getRequestValue();
		}
		catch (PrivateException $e) {
			return $this->getDefaultValue();
		}
	}
	
	public function getRequestValue() {
		$postdata = $this->getPostData();
		
		if ($this->hasPostData()) {
			return $postdata;
		}
		else {
			throw new PrivateException(spitfire()->log ('Field ' . $this->getName() . ' was not sent with request'));
		}
	}
	
	abstract public function getDefaultValue();
	
	public function setVisibility($visibility) {
		if ($visibility >= 0 && $visibility <= 3) $this->visibility = $visibility;
		return $this;
	}
	
	public function getVisibility() {
		return $this->visibility;
	}
	
	public function __toString() {
		$id = "field_{$this->name}";
		return sprintf('<div class="field"><label for="%s">%s</label><input type="%s" id="%s" name="%s" value="%s" ></div>',
			$id, $this->caption, $this->type, $id, $this->name, $this->getValue() 
			);
	}
}
