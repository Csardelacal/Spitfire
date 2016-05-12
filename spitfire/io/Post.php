<?php namespace spitfire\io;

use DOMDocument;
use spitfire\exceptions\PublicException;

/**
 * I currently have no use for this class beyond the idea of parsing several 
 * different POST input formats and that's it.
 * 
 * In future iterations I'm considering the possibility of allowing POST to accept
 * rules for validation, making it less awkward to validate your data when it's 
 * incoming.
 * 
 * @author César de la Cal Bretschneider<cesar@magic3w.com>
 */
class Post
{
	
	/**
	 * The init method reads the current post / the data it is receiving. We will
	 * read several different formats and parse them to make the integration across
	 * apps seamless.
	 */
	public static function init() {
		
		/*
		 * Switch the content type between the possible admitted options that
		 * Spitfire admits and properly parses.
		 */
		switch(_def($_SERVER['CONTENT_TYPE'], null)) {
			/*
			 * To parse JSON we check whether the proper type was set and parse it.
			 * In the event of a JSON parse failure it will return null, which would
			 * cause the POST array to be invalid.
			 */
			case 'application/json':
				$post = json_decode(file_get_contents('php://input'), 1);
				break;
			
			/*
			 * XML are somewhat more difficult to parse, and need to get through and 
			 * need to be translated to JSON before we can convert it to data that
			 * can be used.
			 */
			case 'application/xml':
				$doc  = new DOMDocument;
				
				#Load the XML file. If the data is not okay, we stop right there
				if (!$doc->loadXML(file_get_contents('php://input'))) 
					{ throw new PublicException('Request error', 400); }
				
				$post = json_decode(json_encode(simplexml_import_dom($doc)), 1);
				break;
				
			/*
			 * All other cases are handled by the standard post mechanism in PHP.
			 */
			default:
				$post = $_POST; //PHP will parse this by default
		}
		
		return $post?: Array();
	}
}
