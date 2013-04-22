<?php

app('admin', 'admin')
	->putBean('user')
	->putBean('dependant')
	->putBean('test')
	->setuserModel('userModel')
	->enable();
