<?php namespace spitfire\io\renderers;

/**
 * Classes that implement this interface will expect Dates in return. In order to 
 * provide a thing most similar to standard the class should use a structure 
 * similar to the one provided by the datepicker class.
 * 
 * @author César de la Cal<cesar@magic3w.com>
 * @link http://www.spitfirephp.com/wiki/index.php/IO/POST_DateTimeFormat
 */
interface RenderableFieldDateTime extends RenderableField {}