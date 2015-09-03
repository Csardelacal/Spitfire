<?php namespace spitfire\core;

use spitfire\Context;

/**
 * Any HTTP response is built off a set of headers and a body that contains the
 * message being delivered. This class represents an HTTP response that can be
 * sent by the application.
 * 
 * I'd like to include the option to have in App return states. This way, if the
 * app provides a return URL for success or error the Response could automatically
 * handle that and send the user to the preferred location.
 * 
 * For this to work the application would have to define a onXXXXX GET parameter
 * that would be read. If the return state of the application is "success" the
 * Response should look for an "onsuccess" GET parameter and send the user to
 * that endpoint.
 */
class Response
{
	/**
	 * The headers this response should be sent with. This includes anything from
	 * the status code, to redirections and even debugging messages.
	 * 
	 * [Notice] You should not include debugging messages into your headers in
	 * production environments.
	 *
	 * @var Headers
	 */
	private $headers;
	
	/**
	 * Contains the Body of the response. Usually HTML. You can put any kind of 
	 * data in the response body as long as it can be encoded properly with the
	 * defined encoding.
	 *
	 * @var string 
	 */
	private $body;
	
	/**
	 * Instantiates a new Response element. This element allows you application to
	 * generate several potential responses to a certain request and then pick the
	 * one it desires to use.
	 * 
	 * It also provides the App with the ability to discard a certain response 
	 * before it was sent and generate a completely new one.
	 * 
	 * @param string $body
	 * @param int    $status
	 * @param mixed  $headers
	 */
	public function __construct($body, $status = 200, $headers = null) {
		$this->body    = $body;
		$this->headers = new Headers();
		$this->headers->status($status);
		
		if ($headers) {
			foreach ($headers as $header => $content) {
				$this->headers->set($header, $content);
			}
		}
	}
	
	/**
	 * Returns the headers object. This allows to manipulate the answer 
	 * headers for the current request.
	 * 
	 * @return Headers
	 */
	public function getHeaders() {
		return $this->headers;
	}

	public function getBody() {
		if ($this->body instanceof Context) {return $this->body->view->render();}
		return $this->body;
	}

	public function setHeaders(Headers $headers) {
		$this->headers = $headers;
		return $this;
	}

	public function setBody($body) {
		$this->body = $body;
		return $this;
	}
	
	public function send() {
		ob_start();
		echo $this->getBody();
		$this->headers->send();
		ob_flush();
	}
	
}
