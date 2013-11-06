<?php namespace spitfire\io\renderers;

/**
 * A renderable element is anything that can be translated into an HTML form control.
 * Like inputs, textareas and combo boxes. This makes it fast to create forms
 * to populate data in databases or whatever place you need it.
 * 
 * Renderables can also be translated into an entire form. As forms are also form 
 * controls somewhow.
 * 
 * @see http://www.spitfirephp.com/wiki/index.php/Renderers
 * @author César de la Cal <cesar@magic3w.com>
 */
Interface Renderable
{
	/**
	 * Returns the post id this control wants to read the data from at a later 
	 * point.
	 */
	abstract public function getPostId();
	
	/**
	 * This function allows a renderable item to enforce a renderer it needs. In case
	 * you created your own field type (for eaxmple for tags) you may need a javascript
	 * enabled tool that allows users to enter tags the way you planned it, even 
	 * if they're using the control outside of your planned environment.
	 */
	abstract public function getEnforcedRenderer();
}