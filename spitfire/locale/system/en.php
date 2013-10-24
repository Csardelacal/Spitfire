<?php namespace system;

use Locale;

class enLocale extends Locale
{
	
	public $comment_count   = Array('No comments', 'One comment', '%s comments');
	
	public $secondsago      = Array('Right now', 'One second ago', '%s seconds ago');
	public $minutessago     = Array('Less than a minute ago', 'One minute ago', '%s minutes ago');
	public $hoursago        = Array('Less than an hour ago', 'One hour ago', '%s hours ago');
	public $daysago         = Array('Less than a day ago', 'One day ago', '%s days ago');
	public $weeksago        = Array('Less than a week ago', 'One week ago', '%s weeks ago');
	public $monthssago      = Array('Less than a month ago', 'One month ago', '%s months ago');
	public $yearsago        = Array('Less than a year ago', 'One year ago', '%s years ago');
}