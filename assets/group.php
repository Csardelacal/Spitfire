<?php

/** This script combines several static (JS and CSS) files into a single one reducing the ammount of requests required 
 * to transfer the contents to the client.
 * 
 * @package Xobs
 * @author César de la Cal Bretschneider <cesar@magic3w.com>
 */

define ('WHITELIST_FILE', '.whitelist', true);
define ('BLACKLIST_FILE', '.blacklist', true);

$filetypes = Array('css', 'js');
$content_types = Array('css' => 'text/css', 'js' => 'text/javascript');

/** Reads a file, splits it into lines and removes comment lines (those with a trailing # character)
 * 
 * @param String $file Name of the file to be used.
 * @return mixed Containing the lines of the parsed file
 */

function fileReadSimpleList ($file) {
	@$ls = file_get_contents($file);
	$ret = Array();
	if ($ls) { //File was read correctly
		$ls = explode("\n", $ls);
		foreach ($ls as $e) {
			@list($file, $comment) = explode('#', $e); //We already know some lines may not have any comments at all
			$file = trim($file);
			if (!empty($file)) $ret[] = $file;
		}
	}
	return $ret;
}

/** Detects whether a file has been marked to allow being grouped (this is indicated by the whitelist and blacklist 
 * constants)
 * 
 * @param String $file Indicates what file is to be checked
 * @return Boolean If the file can safely be included into the grouping
 */

function fileIsGroupable ($file) {
	global $whitelist, $blacklist;
	
	if ($whitelist && !in_array ($file, $whitelist) ) {
		return false;
	}
	else {
		if ($blacklist && in_array($file, $blacklist) ) {
			return false;
		}
		else return file_exists($file);
	}
}

//MAIN-----------------------------------------------------------------------------------------------------------------
$whitelist = file_exists(WHITELIST_FILE)? fileReadSimpleList( WHITELIST_FILE ) : false;
$blacklist = file_exists(BLACKLIST_FILE)? fileReadSimpleList( BLACKLIST_FILE ) : false;

if ( in_array($_GET['type'], $filetypes) ) {
	$filetype = $_GET['type'];
	header ('Content-type: ' . $content_types[$filetype] );
}
else {
	die ('No valid filetype selected');
}

foreach ($_GET['f'] as $file) {
	$filename = $filetype . '/' . $file['name'] . '.' . $filetype;
	if (fileIsGroupable ($filename)) {
		echo file_get_contents($filename);
	}
	else {
		echo '/* File ' . $filename . ' not found, skipped */';
	}
}