<?php namespace spitfire\io\renderers;

/**
 * Allows a form compatible object to indicate a render it can be used to generate
 * a HTML form.
 */
interface RenderableForm extends Renderable
{
	/**
	 * Returns the endpoint this should be sent to. This value is usually returned
	 * as '' or as a value set previously as many controls are unable to tell 
	 * where they should be submitted to.
	 */
	public abstract function getAction();
	
	/**
	 * Returns the list of fields that the form should contain. The elements of 
	 * this list must implement RenderableForm in order for a renderer to work 
	 * properly.
	 * 
	 * @return RenderableField[] The fields.
	 */	
	public abstract function getFields();
}