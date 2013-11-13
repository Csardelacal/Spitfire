<?php namespace spitfire\io\renderers;

/**
 * The data in this field is sent to the browser but not directly visible to the
 * user. This makes it especially useful if we're interested in tracking or 
 * verifying the user. Please note that a user can edit the content of this field,
 * it should therefore not be used for security relevant information.
 * 
 * @author César de la Cal<cesar@magic3w.com>
 */
interface RenderableFieldHidden extends RenderableField {}