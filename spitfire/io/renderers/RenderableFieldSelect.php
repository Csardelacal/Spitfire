<?php namespace spitfire\io\renderers;

/**
 * This class allows you to specify a field as a set of options to choose from.
 * Due to the fact that especially when handling data from a database the list
 * can be very big (causing performance issues on both client and server) it
 * supports the reading of partial data.
 */
interface RenderableFieldSelect extends RenderableField
{
	/**
	 * Gets the complete set of options for the current select. When using very
	 * big sets refer to getPartial()
	 * 
	 * @return mixed[]
	 */
	function getOptions();
	
	/**
	 * Fetches only a certain part of the list. This allows you to retrieve data
	 * in a fragmented way to reduce server / client load.
	 * 
	 * @parameter string $str Is used to filter data. If the HTML has a combo like 
	 *		control you can use AJAX/JSONP calls to retrieve that data.
	 * @return mixed[]
	 */
	function getPartial($str);
	
	
	/**
	 * Gets the caption for a certain ID. This is especially useful when the data 
	 * is fragmented and the value(s) are not in the option list.
	 */
	function getSelectCaption($id);
	
	/**
	 * Gets the id for a certain caption.
	 */
	function getSelectId($caption);
}