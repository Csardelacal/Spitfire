<?php namespace spitfire\io\renderers;

interface RenderableFieldGroup extends RenderableField
{
	abstract public function getFields();
}