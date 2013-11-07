<?php namespace spitfire\io\renderers;

/**
 * Classes implementing this interface indicate that they expect to receive a file
 * upload as POST response to this form. The renderer can hint the browser that
 * there is a filesize and a list of supported file types.
 * 
 * Please not that the client browser may or may not respect this, so the implementing 
 * class is responsible for enforcing this setting.
 * 
 * @author César de la Cal<cesar@magic3w.com>
 */
interface RenderableFieldFile extends RenderableField 
{
	/**
	 * Here the field can indicate whether it's requires a certain set of filetypes
	 * to be admitted or if it accepts any file.
	 * 
	 * @return string[]|boolean Returns an array of strings with the supported formats
	 *		or returns boolean false if any filetype is admitted.
	 */
	function getSupportedFileFormats();
	
	/**
	 * The maximum filesize an upload should be (in bytes). Your webserver may enforce
	 * an upload policy that spitfire cannot control and therefore may cause
	 * upload aborts even when the filesize is ok for this script.
	 * 
	 * @return int|boolean Returns the filesize in bytes or a boolean false if this
	 *		is not restricted.
	 */
	function getMaxFileSize();
}