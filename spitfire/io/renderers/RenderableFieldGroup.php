<?php namespace spitfire\io\renderers;

/**
 * Classes implementing this basically are sub-forms. This means they have a set
 * of fields and their value will be a mmixed array with all kinds of possible data.
 * 
 * @author César de la Cal <cesar@magic3w.com>
 */
interface RenderableFieldGroup extends RenderableField
{
	/**
	 * Returns the fields the sub-form contains so they can be rendered.
	 */
	function getFields();
}