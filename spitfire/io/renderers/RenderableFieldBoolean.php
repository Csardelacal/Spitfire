<?php namespace spitfire\io\renderers;

/**
 * Classes that implement this interface will expect Booleans as return. The 
 * renderer should do it's best to provide a mechanism to allow only the booleans
 * but the class is responsible for validating that the posted data is received
 * correctly.
 * 
 * @author César de la Cal<cesar@magic3w.com>
 */
interface RenderableFieldBoolean extends RenderableField {}