<?php

$e = new spitfire\environment('test');

$e->set('db_table_prefix', 'test_');

$lang = reset(explode('.', $_SERVER['SERVER_NAME']));
$e->set('system_language', $lang);