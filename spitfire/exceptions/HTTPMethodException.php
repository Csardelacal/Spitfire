<?php namespace spitfire\exceptions;

/**
 * Applications can use this exception type to indicate that a certain process
 * they were initiating cannot be called from the given HTTP Method.
 */
class HTTPMethodException  extends PrivateException {}
