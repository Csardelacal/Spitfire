<?php

trigger_error('Using the deprecated schema in model.php. Please use storage\database\Schema', E_USER_DEPRECATED);

/**
 * A Model is a class used to define how Spitfire stores data into a DBMS. We
 * usually consider a DBMS as relational database engine, but Spitfire can
 * be connected to virtually any engine that stores data. Including No-SQL
 * databases and directly on the file system. You should even be able to use
 * tapes, although that would be extra slow.
 * 
 * Every model contains fields and references. Fields are direct data-types, they
 * allow storing things directly into them, while references are pointers to 
 * other models allowing you to store more complex data into them.
 * 
 * @author César de la Cal <cesar@magic3w.com>
 */
class Schema extends \spitfire\storage\database\Schema {}
