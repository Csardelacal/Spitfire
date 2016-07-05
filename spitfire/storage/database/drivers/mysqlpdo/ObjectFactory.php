<?php namespace storage\database\drivers\mysqlpdo;

use spitfire\storage\database\DB;
use spitfire\storage\database\drivers\MysqlPDOTable;
use spitfire\storage\database\ObjectFactoryInterface;

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
 * The object factory class allows a Database to centralize a point where the 
 * database objects can retrieve certain items from. As opposed to having this
 * algorithms in every class, as some classes would just be overriding one factory
 * method they needed in a completely standard class.
 * 
 * This allows Spitfire to define certain behaviors it expects from DB objects
 * and then have the driver provide this to not disturb Spitfire's logic.
 *
 * @author César de la Cal Bretschneider <cesar@magic3w.com>
 */
class ObjectFactory implements ObjectFactoryInterface
{
	public function getOTFModel($tablename) {
		$model = new \OTFModel();
		
		$fields = $this->execute("DESCRIBE `" . environment::get('db_table_prefix') . $tablename . "`", false);
		
		while ($row = $fields->fetch()) {
			$model->{$row['Field']} = new \TextField();
		}
		
		$model->setName($tablename);
		return $model;
		//TODO: As of writing this, the method does not use adequate types.
	}
	
	
	public function getTableInstance(DB $db, $tablename) {
		return new MysqlPDOTable($db, $tablename);
	}

}
