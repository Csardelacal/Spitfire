<?php namespace spitfire\storage\database;

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

use spitfire\Model;

/**
 * The result set interface defines how the end user application and queries can
 * interact with result sets. The drivers can use this to provide a mechanism to 
 * access the result cursor of a database query.
 *
 * @author César de la Cal Bretschneider <cesar@magic3w.com>
 */

interface ResultSetInterface
{
	/**
	 * Fetches data from a driver's resultset. This returns a record and advances 
	 * the cursor in the database.
	 * 
	 * @return Model|null A record of a database, or null if the result is exhausted
	 */
	public function fetch();
	
	/**
	 * Returns a raw result, this will usually provide access to the raw data that
	 * the driver is reading from the Database to build models from.
	 * 
	 * This method is usually extremely valuable when retrieving aggregates, since
	 * these methods do not fit into models.
	 * 
	 * @return mixed[]
	 */
	public function fetchArray();
	
	/**
	 * Returns an array containing all the values from the database's result. This
	 * allows your application to loop over the records instead of while-ing over
	 * them.
	 * 
	 * @todo Maybe introduce a RecordCollection that also provides a set of functions 
	 *       on the records instead of a ol' and boring array.
	 * 
	 * @return Model[] All the models the driver read from the database.
	 */
	public function fetchAll();
}
