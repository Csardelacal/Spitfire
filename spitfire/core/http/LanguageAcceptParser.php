<?php namespace spitfire\core\http;

/*
 * The MIT License
 *
 * Copyright 2016 César de la Cal Bretschneider <cesar@magic3w.com>.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

/**
 * This parser allows an application to determine what language it should use to 
 * answer a request. To do so, it reads the Accept-Language header from a browser's
 * request.
 * 
 * Please note, that a browser may not be sending any accept-language header, or
 * requesting a language your application may not support.
 *
 * @author César de la Cal Bretschneider <cesar@magic3w.com>
 */
class LanguageAcceptParser 
{
	
	/**
	 * Contains the default locale to be included in the list of locales in the 
	 * event that the user did not specify any locale in the header.
	 *
	 * @var string
	 */
	private $default;
	
	/**
	 * Contains the header string with a valid "Accept-Language" header format
	 * that should be scanned for locales.
	 *
	 * @see http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html#sec14.4
	 * @var string 
	 */
	private $header;
	
	/**
	 * Creates a new Parser, which retrieves the language from either a string you
	 * provided or the system header.
	 * 
	 * The default locale will ensure that this function always returns data and 
	 * provide you with the option to always use fallbacks. Please note that, your
	 * application should provide those fallbacks.
	 * 
	 * @see http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html#sec14.4
	 * @param string $default Provides a default language in case there was none.
	 * @param string $header
	 */
	public function __construct($default, $header = null) {
		
		#Sometimes we wanna set a custom header (for testing, for example)
		if (!$header) { 
			$header = $_SERVER['HTTP_ACCEPT_LANGUAGE']; 
		}
		
		$this->default = $default;
		$this->header  = $header;
	}
		
	/**
	 * Parses the Accept Language header and returns a list of locales that it 
	 * extracted.
	 * 
	 * Your application can then check if, and how, it wishes to localize itself
	 * to provide a proper experience for the user.
	 * 
	 * @return LanguageAccept[]
	 */
	public function parse() {
		
		#We do do no validation at this point, even though it may be interesting to
		#do it here and not loop over the components later.
		$pieces = explode(',', $this->header);
		array_walk($pieces, function (&$e) { $e = new LanguageAccept($e); });
		
		#Data gets sorted by priority. Even if usually the browser will do this for us.
		usort($pieces, function ($a, $b) {
			$pa = $a->getPriority();
			$pb = $b->getPriority();
			
			if ($pa === $pb) { return 0; }
			return $pa > $pb? -1 : 1;
		});
		
		return array_merge($pieces, Array(new LanguageAccept($this->default)));
	}
}
