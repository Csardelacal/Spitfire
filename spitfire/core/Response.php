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
	 * The return state of a request allows the application to quickly define 
	 * special redirections if the application reached a certain request state.
	 * 
	 * This way, if the application finished successfully it can send the user
	 * while showing him an error page in case there was one.
	 *
	 * @var string|null
	 */
	private $returnState = null;
	
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
	
	/**
	 * Returns the content that is to be sent with the body. This is a string your
	 * application has to set beforehand.
	 * 
	 * In case you're using Spitfire's Context object to manage the context the
	 * response will get the view it contains and render that before returning it.
	 * 
	 * @return string
	 */
	public function getBody() {
		if ($this->body instanceof Context) {return $this->body->view->render();}
		return $this->body;
	}
	
	/**
	 * Changes the headers object. This allows your application to quickly change
	 * all headers and replace everything the way you want it.
	 * 
	 * @param \spitfire\core\Headers $headers
	 * @return \spitfire\core\Response
	 */
	public function setHeaders(Headers $headers) {
		$this->headers = $headers;
		return $this;
	}
	
	/**
	 * Defines the body of the response. This can be any string or any object that
	 * can be converted to string. It can also be a Context object which then 
	 * will be used to render it's view.
	 * 
	 * @param string|Context $body
	 * @return \spitfire\core\Response
	 */
	public function setBody($body) {
		$this->body = $body;
		return $this;
	}
	
	/**
	 * Defines a return state. The return state is just a string that provides 
	 * the application with a quick way of returning a redirection in certain 
	 * cases.
	 * 
	 * For example, if you application has a login form you can set a "success"
	 * return state when the user has properly logged in and redirect the user
	 * to the homepage.
	 * 
	 * In this case if a "onsuccess" _GET parameter was set (and it is a valid
	 * URL) the application will redirect the user to this URL instead of the
	 * homepage.
	 * 
	 * It just removes the need for an additional check before redirecting or 
	 * display a result message.
	 * 
	 * @param string $state
	 * @return \spitfire\core\Response
	 */
	public function setReturnState($state) {
		$this->returnState = $state;
		return $this;
	}
	
	/**
	 * Sends this response to the client computer. It will send both headers and 
	 * the body. Generating the body first and then sending the headers and body
	 * to make sure that any errors caused by generation of the body won't affect
	 * the headers.
	 */
	public function send() {
		
		#Check for a special return state
		$returnURL = filter_input(INPUT_GET, 'on' . $this->returnState);
		
		if ($returnURL && substr($returnURL, 0, 1) === '/') {
			$this->getHeaders()->redirect($returnURL);
		}
		
		ob_start();
		echo $this->getBody();
		$this->headers->send();
		ob_flush();
	}
	
}
