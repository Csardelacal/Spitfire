<?php namespace spitfire\io\renderers;

/**
 * Allows the system to define a field that should allow a user to enter increased
 * amounts of text in a rich text format (HTML). This is the HTML equivalent of 
 * a contenteditable Frame or Div. Due to it's complex nature current versions
 * of Spitfire don't implement this in the system and render them as Textareas.
 */
interface RenderableFieldRTF extends RenderableFieldString {}