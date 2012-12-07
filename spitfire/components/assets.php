<?php

class assetsController extends Controller
{
	
	public function components () {
		$file = SpitFire::baseUrl() . 
			'/bin/components/' . 
			implode('/', func_get_args()) . 
			'.' . SpitFire::$extension;
		die (header('location: ' . $file));
	}
	
}
