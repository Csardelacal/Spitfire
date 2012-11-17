<?php

/**
 * Class used to send a email with plain/HTML content.
 */

class Email
{
	private $from;
	private $to;
	private $subject;
	private $HTML;
	private $plain = ".";
	private $data = Array();
	private $boundary = "==Spitfire==";
	private $carriage = "\r\n";
	
	public function __construct() {
		$this->boundary.=  uniqid('np');
	}
	
	public function setHTML($file) {
		$this->HTML = "bin/email/" . $file;
	}
	
	public function setFrom ($email) {
		$this->from = $email;
	}
	
	public function setTo ($email) {
		$this->to = $email;
	}
	
	public function setSubject ($subject) {
		$this->subject = $subject;
	}
	
	public function bind($var, $content) {
		$this->data[$var] = $content;
	}
	
	protected function makeHeaders() {
		$headers = "MIME-Version: 1.0$this->carriage";
		$headers.= "From: $this->from <$this->from>$this->carriage";
		$headers.= "Subject: $this->subject$this->carriage";
		$headers.= "Content-Type: multipart/alternative;boundary=" . $this->boundary . $this->carriage;
		
		return $headers;
	}
	
	protected function makeMessage() {
		
		$msg .= "$this->carriage$this->carriage--" . $this->boundary . "$this->carriage";
		$msg .= "Content-type: text/plain;charset=utf-8$this->carriage$this->carriage";
		$msg .= $this->plain;

		$msg .= "$this->carriage$this->carriage--" . $this->boundary . "$this->carriage";
		$msg .= "Content-type: text/html;charset=utf-8$this->carriage$this->carriage";
		
		if (file_exists($this->HTML)) {
			ob_start();
			foreach($this->data as $k => $v) $$k = $v;
			include $this->HTML;
			$msg.= ob_get_clean();
		}
		

		$msg .= "$this->carriage$this->carriage--" . $this->boundary . "--";
		
		return $msg;
	}
	
	public function send() {
		return mail($this->to, '', $this->makeMessage(), $this->makeHeaders());
	}
}