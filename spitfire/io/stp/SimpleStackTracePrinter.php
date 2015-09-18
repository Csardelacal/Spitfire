<?php namespace spitfire\io\stp;

class SimpleStackTracePrinter extends StackTracePrinter
{
	public function printLine($line, $type = StackTracePrinter::LINE_TYPE_NORMAL){
		return sprintf('<li class="line %s">%s</li>', $type, $line);
	}
	
	public function makeCSS() {
		return file_get_contents(dirname(__FILE__) . '/stp.css');
	}

	public function wrapExcerpt($html, $startLine) {
		return sprintf('<pre class="excerpt"><ol start="%d">%s</ol></pre>', $startLine, $html);
	}
	
	public function printMethodSignature($function, $args) {
		return sprintf('<strong>%s</strong> ( %s )', $function, $this->stringifyArgs($args));
	}
	
	public function wrapMethodSignature($html) {
		return sprintf('<div class="signature">%s</div>', $html);
	}
	
	public function wrapStackTrace($html) {
		return sprintf('<div class="stacktrace"><h1>Stack trace</h1>%s</div>', $html);
	}

}