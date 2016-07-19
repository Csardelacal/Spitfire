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
 * The language accept class retrieves information from a user's browser and 
 * returns it in an organized manner so that the application can properly localize
 * itself to serve the user properly.
 * 
 * Please note that, Spitfire will not automatically localize your application,
 * this is a responsibility you have to carry yourself. It will provide you with
 * localization stub classes that you can extend, and a helper function that
 * should ease the process of localizing. But the routing and, concrete implementation
 * is your task.
 */
class LanguageAccept
{
	
	/**
	 * The language part of the locale as described by ISO-638. This is a 2 character
	 * string containing the information about which language the user is requesting
	 *
	 * @var string 
	 */
	private $language;
	
	/**
	 * The locale of the language. This is relevant for languages that are spoken
	 * in different areas of the world, causing them to have minor variations between
	 * them.
	 * 
	 * E.g. fr-fr and fr-ca which is the french spoken in France and Canada respectively.
	 *
	 * @var string 
	 */
	private $locale;
	
	/**
	 * The priority is just a float indicating how much precedence this locale has.
	 * This can be any value in the [0,1) range. We do not validate this, the 
	 * browser should do it, but disregard of the rule is not dangerous.
	 *
	 * @var float
	 */
	private $priority;
	
	/**
	 * Creates a new AcceptLanguage header segment object. This object contains 
	 * information about the language the browser requested from the application
	 * to construct pages that are properly localized.
	 * 
	 * @param string $str
	 * @return mixed
	 */
	public function __construct($str = null) {
		
		/*
		 * If the string had no valid format, which can happen since this is user
		 * provided data and may contain as much bogus as he wants, we just ignore
		 * the string and default back to english.
		 */
		if ($str === null || !$this->validateFormat(trim($str))) {
			$this->locale = null;
			$this->language = 'en';
			return;
		}
		
		$pieces = explode(';', trim($str));
		
		#Get the locale string and priority separated
		#We use array_shift instead of list since it handles missing offsets way better
		$locale   = array_shift($pieces);
		$priority = array_shift($pieces);
		
		#Parse the priority, language and locale and we're done here.
		$this->priority = $this->makePriority($priority);
		list($this->language, $this->locale) = $this->makeLocales($locale);
	}
	
	/**
	 * Returns the ISO-639 code of the language. Although the RFC allows for some
	 * strange tags, we enforce a 2 character language code for the sake of simplicity
	 * and coherence.
	 * 
	 * @return string
	 */
	public function getLanguage() {
		return $this->language;
	}
	
	/**
	 * The "subtag", as called by the RFC, specifies which variation of the language
	 * we're localizing for. This for example will be "GB" in the event of "en-gb"
	 * or "us" in the case of using a "en-us" locale.
	 * 
	 * @return string
	 */
	public function getLocale() {
		return $this->locale;
	}
	
	/**
	 * The priority of the language. This is only used for sorting the locales 
	 * when received from the user-agent since, in theory, you could have them 
	 * sent in an unsorted manner.
	 * 
	 * Spitfire automatically sorts the locales by priority, so there's no need
	 * for the application to recheck this.
	 * 
	 * @return float
	 */
	public function getPriority() {
		return $this->priority;
	}
	
	/**
	 * Validates the format of a accep-language segment. This ensures that the 
	 * request was sent with a proper format and can be parsed by us.
	 * 
	 * It'd be interesting to move this to the AcceptLanguageParser class, which
	 * could evaluate the regular expression just once and validate the entire
	 * header in one fell swoop.
	 * 
	 * @param string $str
	 * @return boolean
	 */
	protected function validateFormat($str) {
		return !!preg_match('/^[A-Za-z]{2}(\-[A-Za-z]{2})?(;q\=[\d\.]+)?$/', $str);
	}
	
	/**
	 * Reads the priority assigned to the language accept header and translates it
	 * into a numeric value that we can use to properly sort.
	 * 
	 * It would be an interesting consideration to leave the value unparsed, since
	 * the spec states that the leading 0 is required, and therefore string sorting
	 * would be effectively as efficient as float parsing.
	 * 
	 * @param string $priority
	 * @return float
	 */
	protected function makePriority($priority) {
		return $priority && preg_match('/q\=([\d\.]+)/', $priority, $m)? ((float)$m[1]) : 1.0;
	}
	
	/**
	 * Receives a string that contains a locale string (like 'en' or 'en-gb') and 
	 * then returns the language and locale in an array like [en] or [en, gb] that
	 * we can then use to construct the locale.
	 * 
	 * @param string $localeStr
	 * @return string[]
	 */
	protected function makeLocales($localeStr) {
		/*
		 * The language needs to always be defined, due to the nature of the format
		 * that these headers need to comply with.
		 * 
		 * http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html#sec14.4
		 */
		list($lang) = explode('-', $localeStr);
		
		/*
		 * Check if a locale is defined, this allows the application to assign further
		 * settings than just the basic language or find a dialect that the user
		 * may speak.
		 */
		if (strstr($localeStr, '-')) { $locale = explode('-', $localeStr)[1]; }
		else                         { $locale = null; }
		
		return Array(
			strtolower($lang),    //Language
			strtolower($locale)   //Locale
		);
	}
}