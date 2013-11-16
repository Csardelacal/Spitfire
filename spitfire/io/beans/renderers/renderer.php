<?php

namespace spitfire\io\beans\renderers;

use CoffeeBean;

/**
 * @deprecated since version 0.1
 */
abstract class Renderer
{
	
	public abstract function renderForm(CoffeeBean$bean);
	public abstract function renderList(CoffeeBean$bean, $records);
	
}