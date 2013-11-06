<?php namespace spitfire\io\renderers;

abstract class Renderer
{
	
	public abstract function renderForm(RenderableForm$form);
	public abstract function renderList(RenderableForm$form, $data);
	
}