<?php namespace spitfire\router;

use privateException;

/**
 * This special exception type is only thrown when two routes being compared do
 * not match and are therefore different.
 * 
 * @author César de la Cal <cesar@magic3w.com>
 * @last-revision 2013-10-18
 */
class RouteMismatchException extends privateException {}