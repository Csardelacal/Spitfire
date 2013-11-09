<?php namespace spitfire\validation;

interface ValidationRule
{
	function test($value, $source = null);
}