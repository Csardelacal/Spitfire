<?php

include $file = dirname(__FILE__).'/autoload.php';
include $file = dirname(__FILE__).'/storage.php';
include $file = dirname(__FILE__).'/plugins.php';
include $file = dirname(__FILE__).'/security.php';
include $file = dirname(__FILE__).'/exceptions.php';
include $file = dirname(__FILE__).'/url.php';
include $file = dirname(__FILE__).'/mvc.php';
include $file = dirname(__FILE__).'/validation.php';
include $file = dirname(__FILE__).'/html.php';

//For debugging purposes:
//echo '<!--' . $file . ' - ' . round(memory_get_usage()/(1024*1024), 2) . 'MB -->'."\n";
