<?php

app('admin', 'admin')
	->putBean('user')
	->putBean('dependant')
	->setuserModel('userModel')
	->enable();
