<?php

class testController extends Controller
{
	public function index ($object, $params) {
		echo '<html><head></head><body><form method="post" action="' 
				. new url('test', 'index','').'"><textarea name="evaling" rows="17" cols="100">'
				. ((isset($_POST['evaling']))?$_POST['evaling']:'') 
				.'</textarea><input type="submit"></form>';
		
		if (isset($_POST['evaling'])) {
			echo '<h1>Evals:</h1><pre>'; 
			eval($_POST['evaling'] . ';');
			echo '</pre>';
			$t = print_r($ret, true); 
			echo '<h1>Returns:</h1><pre>' . $t . '</pre>';
		}
		echo ('</body></html>');
	}
	
	public static function _resolve_index($object) {
		return 'Awesome test class!!!';
	}

	public function detail ($object, $params) {
		echo '<html><head><link rel="stylesheet" type="text/css" href="/assets/css/synced.css"/></head><body><script type="text/javascript" src="/assets/js/synced.js"></script></body></html>';
	}
	
	public function syncedBrowsing($object, $params) {
		
		
		$data = memcached::get('syncedBrowsing');
		
		$data[$params['pcid']] = Array(
			'id' => $params['pcid'],
			'mouse' => $params['mouse'],
			'scroll' => $params['scroll']
		);
		
		memcached::set('syncedBrowsing', $data, false, 3600);
		
		foreach($data as $pc) if ($pc['id']) echo "displayPointer({$pc['mouse']['x']}, {$pc['mouse']['y']}, {$pc['scroll']['y']}, {$pc['scroll']['x']}, '{$pc['id']}');";
	}
	
	public function contentEditable($object, $params) {
		
		echo '<div contentEditable="true" id="contentEditable">
					<menu type="context" id="mymenu">
						
					</menu>
					Some text <ul><li>A list</li></ul></div>';
		
	}
	
	public function regex($object, $params) {
		
		
		
		function safe_rebuild_html($html) {
			$regex = '/\&lt\;(\/?(div|a|img|span)\s?((href|src)=\"http(s)?:\/\/([^\"]+)\"\s?){0,4}((style)=\"([^\"]+)\"\s?){0,4}\s?\/?)\&gt\;/';
			$result = preg_replace($regex, "<$1>", $html);
			return $result;
		}

		$html = 'Hello <a href="javascript:alert(\'Hello\')" >this</a> is a <img src="http://www.google.com" style="display: block; width: 100px; height: 100px" />';
		//$html = 'Hello is a <img src="http://www.google.com" style="display: block; width: 100px; height: 100px" />';
		$html = htmlspecialchars($html, ENT_NOQUOTES);
		echo $html;
		echo safe_rebuild_html($html);
	}
}

/*class dummy
 {                 *
   public $t = 'xxx';
 }
 
 class test
 {
	 private $x = Array();
	   public function __construct(dummy &$v) {$this->x[0] = &$v;}
	     public function getv () {return $this->x[0];}
	     }
	     
	     $f = new dummy();
			 $t = new test($f);
			 $f = 'Piedra';
			 unset ($f);
			 $f = Array(1,2,3);
			 echo 'Printing $f: ';
			 print_r($f);
			 $ret = $t->getv();*/