<?php

function dictionary_implode($array, $s1, $s2) {
	$ret = '';
	$first = true;
	foreach ($array as $key => $value) {
		if (!$first) $ret.= $s2;
		$ret.= $key . $s1 . $value;
		$first = false;
	}
	return $ret;
}

class html
{
	public static function input ($type, $name, $label = false, $placeholder = '', $other = Array(), $value = false) {
		$ret = '';
		
		if ($label) $ret.= "<label for=\"$id\">$label</label>\n";
		
		$ret.= "<input type=\"$type\" name=\"$name\" id=\"$name\" placeholder=\"$placeholder\" ". dictionary_implode($other, '=', ' ');
		
		if ($type != 'checkbox') {
			if (isset($_POST[$name]) && $type!='password') $ret.= " value=\"{$_POST[$name]}\" ";
			elseif ($value)                                $ret.= " value=\"{$value}\" ";
		} else {
			if ($value == 'on' || $_POST[$name] == 'on') $ret.= ' checked="checked" ';
		}
		$ret.= " />"; 
		
		return $ret;
	}
}
 
class controller_register 
{
	
	public function index($object, $params) {
		
		$v = new view ('bin/views/register.php');
		$v->render(Array($_POST));
		
	}
	
}