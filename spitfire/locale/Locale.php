<?php namespace spitfire\locale;

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
 * The locale class provides a stub for applications to localize themselves
 * easier. It provides translation, and date / currency formatting that 
 * your application can implement to consistently localize components.
 * 
 * Currently we avoid number formatting since this would introduce more 
 * complexity than actually necessary. Formatting numbers is, in many cases,
 * context dependant and I feel that currency is the most common and glaring
 * example of number formatting issues.
 * 
 * I do personally avoid PHP's Locale system due to it doing a lot of magic, 
 * you're not always certain if echo, date or other functions are accidentally
 * localizing your strings and screwing up.
 * 
 * With this classes, you need to expressely request localization for a certain
 * string, which keeps you in control of the output.
 * 
 * @author César de la Cal Bretschneider <cesar@magic3w.com>
 */
abstract class Locale implements LocaleInterface
{
	
	/**
	 *
	 * @var type 
	 * @deprecated since version 0.1-dev 1607191244
	 */
	private $_current_output;
	
	/**
	 * Translates a string. In case the string id you provided was for a pluralizable
	 * string, then the second parameter will be used to identify the plural to be used.
	 * 
	 * <code>Say()</code> does internally use <code>sprintf</code> for string replacement,
	 * so any parameters you provide to this string will be passed to sprintf.
	 * 
	 * @param  string string The message to be translated, depending on your method 
	 *                       this may be a ID for a message or a complete string.
	 * 
	 * @param  int    $amt   If you pass a pluralized string, the second parameter
	 *                       will be used to determine the plural to be used.
	 * 
	 * @param  mixed  $_     All parameters (excluding $msgid and including $amt)
	 *                       will be passed to the sprintf that translates the text
	 * 
	 * @return string The translated string, including the replacements done by sprintf()
	 */
	public function say($string, $amt = null) {
		$args  = func_get_args();
		$msgid = array_shift($args);
		
		#This class allows you to work with both message ids and fully gettext style
		#translations. Your implementation choice.
		$msg   = $this->getMessage($msgid);
		
		#If no translation was available, return the string
		if (!$msg) { return $msgid; }
		
		#If a string is plural we do have a specialized behavior
		if ($this->hasPlural($msg)) {
			$amt = reset($args);
			$msg = $this->translatePlural($msg, $amt);
		}
		
		#Return message to the arguments for the function call
		array_unshift($args, $msg);
		
		#In PHP7 we should be able to sprintf($args...) and get rid of call_user_func
		return call_user_func_array('sprintf', $args);
	}
	
	/**
	 * Retrieves the message that translates the given ID. Please note, that while
	 * we encourage the usage of IDs (just like Symfony does on their locales) you
	 * can use an ID of any given length and format.
	 * 
	 * So, if you have a system that translates using translation strings, you can
	 * us it just as well. You just need to accomodate for the DB overhead (or 
	 * whatever you're using for localization).
	 * 
	 * @param string $msgid The id used to find the translation
	 * @return string The translated message for the string
	 */
	abstract public function getMessage($msgid);
	
	/**
	 * Returns a currency object, which will contain information about the currency
	 * and the way it's formatted when printed.
	 * 
	 * @return CurrencyLocalizer The currency object
	 */
	abstract public function getCurrency();
	
	/**
	 * Returns the date formatter, which allows your locale to output dates the
	 * same way that it's supposed to.
	 * 
	 * @return DateFormatter The object used to format the date for this locale
	 */
	abstract public function getDateFormatter();
	
	/**
	 * 
	 * @deprecated since version 0.1-dev 20160719
	 */
	public function start($lang) {
		if ($this->_current_output) $this->end();
		$this->_current_output = $lang;
		ob_start();
	}
	
	/**
	 * 
	 * @deprecated since version 0.1-dev 20160719
	 */
	public function end() {
		$msg = ob_get_clean();
		if ($this->_current_output == $this->getLangCode()) echo $msg;
		$this->_current_output = null;
	}
	
	/**
	 * Determines whether a locale string has a plural or not. This allows your 
	 * application to translate strings like "%d files deleted"
	 * 
	 * @param mixed $msg
	 * @return boolean
	 */
	protected function hasPlural($msg) {
		return is_array($msg);
	}
	
	/**
	 * Gets a message out of an array for the application to properly translate a 
	 * pluralizable string.
	 * 
	 * Please note that this method does not sort the array, therefore you MUST
	 * provide the array sorted by it's keys for it to work properly.
	 * 
	 * @param string[] $msg
	 * @param int      $amt
	 * @return string
	 */
	protected function translatePlural($msg, $amt) {
		$m   = null;
		
		#This loop checks for a key that is bigger than the amt provided.
		foreach ($msg as $key => $val) {
			if ($key > $amt) { return $m; }
			$m = $val;
		}

		return reset($msg);
	}
	
}