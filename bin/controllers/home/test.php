<?php

namespace home;

use Controller;
use Strings;

class testController extends Controller
{
	
	public function index() {
		echo Strings::camel2underscores('someCamelCasedString');
		echo Strings::camel2underscores('SomeCamelCasedString');
		echo (isset($this->get->test))?'test is true': 'test is false';
		
		print_r(db()->table('people')->getAll()->fetchAll());
	}
	
}