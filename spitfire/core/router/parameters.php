<?php namespace spitfire\core\router;

/**
 * The parameters a route reads out of a server or URI. This allows the application
 * to gracefully manage the parameters and give them over to the callback function
 * that should replace it.
 */
class Parameters
{
	
	/**
	 * This parameters are used to handle named parameters that a URL has handled
	 * from a Server or URI.
	 * 
	 * @var string[]
	 */
	private $parameters = Array();
	
	/**
	 * Some components are greedy (this means, they accept incomplete routes or 
	 * patterns as target for a string), the leftovers are stored in this array
	 * for the application to use at it's own discretion.
	 * 
	 * For example, Spitfire's default rules make heavy use of this tool as a way
	 * to retrieve the controller, action and object from a request.
	 *
	 * @var string[]
	 */
	private $unparsed   = Array();
	
	/**
	 * Imports a set of parameters parsed by the router. Usually, this will be a
	 * single element provided by the route.
	 * 
	 * @param string[] $params
	 */
	public function addParameters($params) {
		$this->parameters = array_merge($this->parameters, $params);
	}
	
	/**
	 * Returns the parameter for the given parameter name. Please note that this 
	 * function may return boolean false and empty strings alike. You can use the
	 * === operator to compare the values.
	 * 
	 * @param string $name
	 * @return string
	 */
	public function getParameter($name) {
		return (isset($this->parameters[$name]))? $this->parameters[$name] : false;
	}
	
	public function getParameters() {
		return $this->parameters;
	}

	public function getUnparsed() {
		return $this->unparsed;
	}

	public function setParameters($parameters) {
		$this->parameters = $parameters;
	}

	public function setUnparsed($unparsed) {
		$this->unparsed = $unparsed;
	}


}