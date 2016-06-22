<?php namespace spitfire\core\annotations;

use Strings;

/**
 * Description of AnnotationParser
 *
 * @author César de la Cal Bretschneider <cesar@magic3w.com>
 */
class AnnotationParser 
{
	
	public function parse($doc) {
		
		$raw = $doc instanceof \Reflector? $doc->getDocComment() : $doc;

		#Retrieve the docblock information
		$pieces   = explode(PHP_EOL, $raw);
		$docblock = Array();
		
		#Remove unrelated data
		$clean    = array_filter(array_map(function ($e) {
			$trimmed = trim($e, "\t */");
			return Strings::startsWith($trimmed, '@')? trim($trimmed, '@') : null;
		}, $pieces));
		
		#Sort the data
		foreach ($clean as $line) {
			$segments = array_filter(explode(' ', $line));
			$name     = array_shift($segments);
			
			#If uninitialized, initialize the array for the docblock
			if (!isset($docblock[$name])) { $docblock[$name] = Array(); }
			
			#Add the value we parsed
			$docblock[$name][] = $segments;
		}
		
		return $docblock;
	}
}
