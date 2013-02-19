<?php

app('admin', 'admin')
	->putBean('user')
	->setuserModel('userModel')
	->enable();
