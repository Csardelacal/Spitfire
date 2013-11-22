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
	function getAction();
	
	/**
	 * Returns the list of fields that the form should contain. The elements of 
	 * this list must implement RenderableForm in order for a renderer to work 
	 * properly.
	 * 
	 * @return RenderableField[] The fields.
	 */	
	function getFormFields();
	
	/**
	 * This function allows a renderable item to enforce a renderer it needs. In case
	 * you created your own field type (for example for tags) you may need a javascript
	 * enabled tool that allows users to enter tags the way you planned it, even 
	 * if they're using the control outside of your planned environment.
	 */
	function getEnforcedFormRenderer();
}