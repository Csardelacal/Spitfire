<?php namespace spitfire\core\annotations;

use Strings;

/**
 * Reads a docblock and parses the information that it provides to extract the 
 * annotations that a programmer may use to modify an application's behavior.
 * 
 * To keep these as flexible as possible, the parser will just return an array
 * of information that it extracted from the docblock.
 *
 * @author César de la Cal Bretschneider <cesar@magic3w.com>
 */
class AnnotationParser 
{
	
	/**
	 * Filters the data from a docblock, extracting the lines that do contain 
	 * annotations and the information these provide.
	 * 
	 * The return for this will look like this:
	 * <code>Array('annotationA paramA paramB', 'annotationB paramA paramB')</code>
	 * 
	 * With this information, the parse function will be able to organize the data
	 * so it's easily accessible to the programmer using it.
	 * 
	 * Notice that this function will trim off asterisks and forward slashes of your
	 * annotation, so if you wish to use those you need to make sure to ask the user
	 * to quote them for the application to properly use them.
	 * 
	 * Annotations for this function may as well be numeric or contain special
	 * characters. You're though encouraged to use simple alphanumeric characters,
	 * since we're not testing for the operation with Unicode.
	 * 
	 * @param \Reflector|string $doc
	 * @return string[]
	 */
	protected function filter($doc) {
		
		#Raw contains the complete docblock comment, which will contain extra data
		#that may be uninteresting, we will filter it and return.
		$raw = $doc instanceof \Reflector? $doc->getDocComment() : $doc;

		#Individual lines make it easier to parse the data
		$pieces   = explode(PHP_EOL, $raw);
		
		#Remove unrelated data
		$clean    = array_filter(array_map(function ($e) {
			$trimmed = trim($e, "\t */");
			return Strings::startsWith($trimmed, '@')? ltrim($trimmed, '@') : null;
		}, $pieces));
		
		return $clean;
		
	}
	
	/**
	 * The parser will retrieve a DocComment, or any similar structure and read 
	 * the annotations, providing you with an array that is structured like this
	 * 
	 * <code>Array('annotation' => Array(Array('paramA'))</code>
	 * 
	 * Please note that there are three levels to the array:
	 * * Annotation type
	 * * Annotation disambiguation (you can have several &at;param for example)
	 * * Parameters
	 * 
	 * With the data structured, although inside a multi-layer array, it should be
	 * fairly simple to access the data that you need to make use of the annotation.
	 * 
	 * @todo The return of this parser is weird enough to justify a wiki page or 
	 *       a special return type
	 * 
	 * @param \Reflector|string $doc
	 * @return string[][][]
	 */
	public function parse($doc) {
		
		#Prepare the variables we need.
		$annotations = Array();
		$clean       = $this->filter($doc);
		
		#Sort the data
		foreach ($clean as $line) {
			$segments = array_filter(explode(' ', $line));
			$name     = array_shift($segments);
			
			#If uninitialized, initialize the array for the docblock
			if (!isset($annotations[$name])) { $annotations[$name] = Array(); }
			
			#Add the value we parsed
			$annotations[$name][] = $segments;
		}
		
		return $annotations;
	}
}
