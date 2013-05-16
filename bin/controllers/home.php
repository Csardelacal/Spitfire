<?php

/**
 * Prebuilt test controller. Use this to test all the components built into
 * for right operation. This should be deleted whe using Spitfire.
 */

class homeController extends Controller
{
	public function index() {
		$t = TestManager::getInstance();
		
		#URL Tests
		$t->assertEquals(new URL(),    spitfire()->baseURL() . '/');
		$t->assertEquals(new URL('/'), spitfire()->baseURL() . '/');
		
		#DB Tests
		$t->test(new tests\dbCreateTest());
		
		$result = $t->getResult();
		$this->view->set('result', $result);
	}
}