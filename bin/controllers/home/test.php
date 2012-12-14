<?php


class home_testController extends Controller
{
	
	public function index() {
		echo (isset($this->get->test))?'test is true': 'test is false';
		die('Hi!');
	}
	
}