<?php

/**
 * Prebuilt test controller. Use this to test all the components built into
 * for right operation. This should be deleted whe using Spitfire.
 */

class HomeController extends Controller
{
	public function index() {
		$this->view->set('message', 'Hi! I\'m spitfire');
	}
}