<?php

AppManager::register('M3W', 'testComponent');
AppManager::register('M3W', 'testingComponent');

$admin = AppManager::register('M3W', 'adminComponent');
$admin->putModel('user');
$admin->putModel('dependant');
$admin->putBean('user');