<?php namespace spitfire\locale\sys;

use spitfire\locale\Locale;

class En extends Locale
{
	
	public $comment_count   = Array('No comments', 'One comment', '%s comments');
	
	public $select_pick     = 'Pick'; 
	
	public $secondsago      = Array('Right now', 'One second ago', '%s seconds ago');
	public $minutesago      = Array('Less than a minute ago', 'One minute ago', '%s minutes ago');
	public $hoursago        = Array('Less than an hour ago', 'One hour ago', '%s hours ago');
	public $daysago         = Array('Less than a day ago', 'One day ago', '%s days ago');
	public $weeksago        = Array('Less than a week ago', 'One week ago', '%s weeks ago');
	public $monthssago      = Array('Less than a month ago', 'One month ago', '%s months ago');
	public $yearsago        = Array('Less than a year ago', 'One year ago', '%s years ago');
	
	public $str_too_long    = Array('', 'String should be shorter than one character', 'String should be shorter than %s characters');
	public $str_too_short   = Array('', 'String should be longer than one character', 'String should be longer than %s characters');
	public $err_not_numeric = Array('Requires a numeric value');
	public $err_field_null  = Array('Field cannot be null');
}