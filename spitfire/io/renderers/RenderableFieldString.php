<?php namespace spitfire\io\renderers;

/**
 * This field hlds a string. For renderers this is usually translated into inputs
 * with text type. For textarea and data with long text contents please refer to 
 * RenderableFieldText.
 * 
 * @author César de la Cal<cesar@magic3w.com>
 */
interface RenderableFieldString extends RenderableField
{
	/**
	 * Returns the recommended maximum length of the field. Remember that users can 
	 * simply disable this nd therefore it should not be taken for granted that
	 * the renderer will provide data accordingly.
	 * 
	 * @return int|boolean Returns the max length or a boolean false in case there
	 *		is no maximum length
	 */
	function getMaxLength();
}