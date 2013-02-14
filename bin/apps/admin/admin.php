<?php

namespace M3W\Admin;

use Controller;

class homeController extends Controller
{
	function index() {
		echo 'Hi! ';
		
		$p = $_SERVER['PATH_INFO'];
		echo $p;
		preg_match_all('/\/[a-zA-Z0-9-_]+/', $p, $m);
		print_r($m);
		
		preg_match('/\.([a-zA-Z0-9-_]+)$/', $p, $m);
		print_r($m);
		
	}
}