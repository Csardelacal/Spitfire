<?php

class assetsController extends Controller
{
	
	public function components () {
		$file = SpitFire::baseUrl() . str_replace('/assets/components', '/bin/components', $_SERVER['PATH_INFO']);
		die(header('location: ' . $file));
	}
	
}
