<?php

use spitfire\Request;
use spitfire\environment;

/*
 * This defines how routes are parsed. This helps developing apps that use special
 * settings in the URL to generate their content.
 */

Request::get()->addHandler(function /*app*/($context, $element) {
	
	$app = spitfire()->getApp($element);
	$context->app = $app;
		
	if (spitfire()->appExists($element)) {
		return true;
	}else {
		return false;
	}
});

Request::get()->addHandler(function /*controller*/($context, $element) {
		
	$app = $context->app;

	try {
		$context->controller = $app->getController($element, $context);
		return true;
	} catch (Exception $e) {
		$controller = $app->getController(environment::get('default_controller'), $context);
		$context->controller = $controller;
		return false;
	}
});

Request::get()->addHandler(function /*action*/($context, $element) {
	$controller = $context->controller;

	if (empty($element)) {
		$context->action = environment::get('default_action');
		return false;
	}
	elseif (is_callable(Array($controller, $element))) {
		$context->action = $element;
		return true;
	}
	throw new \publicException('Action not Found', 404);

});
