<?php namespace spitfire\locale;

use spitfire\exceptions\PrivateException;

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
 * The domain group class allows Spitfire to provide a clean API to the translation
 * subsystem when using the helper function <code>_t()</code> to manage locales.
 * 
 * Since the whole purpose of this class is to provide access to either the default
 * domain for the application or to the alternative domains it does not contain
 * barely any logic of it's own.
 * 
 * @author César de la Cal Bretschneider <cesar@magic3w.com>
 */
class DomainGroup
{
	
	const DEFAULT_DOMAIN = '_default_';
	
	/**
	 * The array of domains registered to be used with this application. A domain
	 * should uniquely identify your application / component unless it's the final
	 * user space.
	 * 
	 * Recommended is the android / java style naming like com.magic3w.myapp or a
	 * short name if your application is known uniquely by it.
	 *
	 * @var Domain[]
	 */
	private $domains;
	
	/**
	 * Gets the default domain for the user space application. This is the namespace
	 * you should use when you're programming outside an application / module 
	 * space, since it's the most convenient.
	 * 
	 * If you did not register a domain beforehand you will be returned an exception.
	 * You will have to register a proper Locale for the application first.
	 * 
	 * @throws PrivateException If there is no default domain registered
	 * @return Domain
	 */
	public function getDefault() {
		return $this->domain(self::DEFAULT_DOMAIN);
	}
	
	/**
	 * The domain method provides a comfortable access method to the different
	 * domains when translating a certain application.
	 * 
	 * This method is what allows a user-space application to inject translations
	 * into prebuilt components to ensure that a user has control over the string
	 * output of a third party inside his application without editing the vendor's
	 * source code.
	 * 
	 * By using domains we can construct modules which interact with Spitfire apps
	 * while maintaining their original functionality by providing a proper fallback
	 * in the event of the framwork not being available.
	 * 
	 * @param string $name
	 * @return Domain
	 * @throws PrivateException If there is no domain for the given name
	 */
	public function domain($name) {
		
		#If the domain is not available, we terminate the search. No fallbacks
		if (!isset($this->domains[$name])) { 
			throw new PrivateException('Tried localizing without an appropriate locale', 1607191627);
		}
		
		return $this->domains[$name];
	}
	
	/**
	 * Adds a domain to the already available list of domains. This method is 
	 * intended to be provided by the _t() helper function.
	 * 
	 * @param string $name
	 * @param Domain $domain
	 * @return Domain
	 */
	public function putDomain($name, Domain$domain) {
		if (empty($name)) { $name = self::DEFAULT_DOMAIN; }
		$this->domains[$name] = $domain;
		
		return $domain;
	}
	
	/**
	 * Translates a string using the default locale. 
	 * 
	 * While this function is definitely functional it's way slower than using _t()
	 * directly since it has an additional call_user_fun_array() call in it that
	 * (when PHP7 is ready for widespread adoption) will be replaceable by the 
	 * splatter operator.
	 * 
	 * @throws PrivateException If there is no locale registered
	 * @return string
	 */
	public function say() {
		return call_user_func_array(Array($this->getDefault(), 'say'), func_get_args());
	}
	
	/**
	 * Returns the currency object that allows localizing the currency for the 
	 * default locale.
	 * 
	 * @return Currency
	 */
	public function currency() {
		return $this->getDefault()->currency();
	}
	
	/**
	 * The date formatter object will provide access to date formatting according
	 * to the default locale.
	 * 
	 * @return DateFormatter
	 */
	public function date() {
		return $this->getDefault()->date();
	}
	
}
