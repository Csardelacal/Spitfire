<?php


class home_testController extends Controller
{
	
	public function index() {
		echo Strings::camel2underscores('someCamelCasedString');
		echo Strings::camel2underscores('SomeCamelCasedString');
		echo (isset($this->get->test))?'test is true': 'test is false';
		die('Hi!');
	}
	
}