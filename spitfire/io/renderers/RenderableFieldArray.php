<?php namespace spitfire\io\renderers;

/**
 * Allows classes to indicate that data they manage is an array of the basic data
 * type. This allows the renderer to generate several HTML elements / one that
 * allows several values.
 * 
 * @author César de la Cal <cesar@magic3w.com> 
 */
Interface RenderableFieldArray extends RenderableField
{
	/**
	 * Get's the data the field currently contains. The field is responsible for 
	 * mixing existing data with post data and generating the value it needs to have
	 * shown. For field arrays this is an array containing elements of the correct type.
	 * 
	 * @return mixed[]
	 */
	//This will cause errors in PHP5.3 function getValue();
}