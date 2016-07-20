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
 * This interface defines the basic methods that any "Locale" class that is 
 * supposed to work integrate into Spitfire's workflow needs to implement.
 * 
 * Otherwise, the _t() method, upon receiving that class as a parameter will
 * issue a Locale error.
 * 
 * You could also use the predefined Locale class in the spitfire\locale 
 * namespace that will implement some of these methods and allow your application
 * to work on the stuff it needs instead of working out the details.
 * 
 * @author César de la Cal Bretschneider <cesar@magic3w.com>
 */
interface LocaleInterface
{
	
	/**
	 * Methods implementing this should provide a mechanism for translating strings
	 * given a source string (either id or as a whole message) and translate the
	 * string, according to a certain number of parameters given that could replace
	 * placeholders in the string.
	 * 
	 * Recommended would be to implement a sprintf() like behavior that allows for
	 * flexible replacement and that is compatible with the native implementations
	 * of this interface.
	 * 
	 * @param string $string
	 * @param int    $amt
	 */
	function say($string, $amt = null);
	
	/**
	 * Returns a currency object, which will contain information about the currency
	 * and the way it's formatted when printed.
	 * 
	 * @return CurrencyLocalizer The currency object
	 */
	function getCurrency();
	
	/**
	 * Returns the date formatter, which allows your locale to output dates the
	 * same way that it's supposed to.
	 * 
	 * @return DateFormatter The object used to format the date for this locale
	 */
	function getDateFormatter();
}
