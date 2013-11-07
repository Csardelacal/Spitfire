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
	 * This element should never be rendered. This is interesting for elements that
	 * are 'hidden' inside a Renderable. Please note that this won't generate a
	 * input type=hidden
	 */
	const VISIBILITY_HIDDEN = 0;
	
	/**
	 * Indicates that an element should be rendered only when generating a list of
	 * elements that are displayed. When doing so the default renderer behavior 
	 * is to strval() the field's value.
	 */
	const VISIBILITY_LIST   = 1;
	
	/**
	 * Displays the element when the system is rendering a form. This allows the 
	 * renderer to create the HTML necessary to populate this control with user 
	 * data.
	 */
	const VISIBILITY_FORM   = 2;
	
	/**
	 * A card is Spitfire's equivalent of a small element that contains a title,
	 * a description, an optional image and URL (and additional custom metadata).
	 * This allows your application to quickly present this 'card' to a user and
	 * generate quick visual connections with elements.
	 */
	const VISIBILITY_CARD   = 4;
	
	/**
	 * 
	 */
	const VISIBILITY_ALL    = 7;
	
	/**
	 * Returns the post id this control wants to read the data from at a later 
	 * point.
	 */
	function getPostId();
	
	/**
	 * This function allows a renderable item to enforce a renderer it needs. In case
	 * you created your own field type (for eaxmple for tags) you may need a javascript
	 * enabled tool that allows users to enter tags the way you planned it, even 
	 * if they're using the control outside of your planned environment.
	 */
	function getEnforcedRenderer();
	
	/**
	 * Defines the visibility of this renderable object. The scopes are either 
	 * form, card or list. Which indicate whether the renderable 'agrees' to be 
	 * rendered into any of those.
	 * 
	 * Please take note, that even if it should do so. The renderer may ignore the 
	 * setting and render the data it considers convenient.
	 */
	function getVisibility();
}