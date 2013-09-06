<?php

use spitfire\Request;
use spitfire\path\AppParser;
use spitfire\path\ControllerParser;
use spitfire\path\ActionParser;

/*
 * This defines how routes are parsed. This helps developing apps that use special
 * settings in the URL to generate their content.
 */

Request::get()->addHandler(new AppParser());
Request::get()->addHandler(new ControllerParser());
Request::get()->addHandler(new ActionParser());
