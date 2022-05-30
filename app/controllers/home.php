<?php namespace app\controllers;

use Controller;

use spitfire\exceptions\PublicException;
use spitfire\exceptions\PrivateException;
use m3w\IOException;

/**
 * Prebuilt test controller. Use this to test all the components built into
 * for right operation. This should be deleted whe using Spitfire.
 */

class HomeController extends Controller
{
	public function index()
	{
		$this->view->set('message', 'Hi! I\'m spitfire');
		throw new PrivateException('Not found', 403);
		throw new IOException('Not found', 403);
	}
}
