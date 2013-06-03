<?php

namespace spitfire\io\beans\renderers;

use CoffeeBean;

abstract class Renderer
{
	
	public abstract function renderForm(CoffeeBean$bean);
	public abstract function renderList(CoffeeBean$bean, $records);
	
}