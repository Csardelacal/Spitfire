<?php

abstract class appController extends Controller
{
	public function onload() {
		$this->view->set('content', '');
	}
}
