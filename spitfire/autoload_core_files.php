<?php

use spitfire\AutoLoad;

#Set the base directory
$cur_dir = dirname(__FILE__);

#Define default classes and their locations
AutoLoad::registerClass('fileNotFoundException', $cur_dir.'/exceptions.php');
AutoLoad::registerClass('publicException',       $cur_dir.'/exceptions.php');
AutoLoad::registerClass('privateException',      $cur_dir.'/exceptions.php');
AutoLoad::registerClass('spitfire\exceptions\ExceptionHandler',  $cur_dir.'/exceptionHandler.php');
AutoLoad::registerClass('spitfire\Path',         $cur_dir.'/core/path.php');

AutoLoad::registerClass('router',                $cur_dir.'/router.php');
AutoLoad::registerClass('spitfire\environment',  $cur_dir.'/environment.php');

AutoLoad::registerClass('_SF_MVC',               $cur_dir.'/mvc/mvc.php');
AutoLoad::registerClass('Controller',            $cur_dir.'/mvc/controller.php');
AutoLoad::registerClass('View',                  $cur_dir.'/mvc/view.php');
AutoLoad::registerClass('_SF_ViewElement',       $cur_dir.'/mvc/view_element.php');

AutoLoad::registerClass('_SF_Memcached',         $cur_dir.'/storage.php');
AutoLoad::registerClass('Image',                 $cur_dir.'/image.php');

#Database related imports
AutoLoad::registerClass('spitfire\storage\database\Model',             $cur_dir.'/db/db.php');
AutoLoad::registerClass('spitfire\storage\database\Queriable',         $cur_dir.'/db/queriable.php');
AutoLoad::registerClass('spitfire\storage\database\Table',             $cur_dir.'/db/table.php');
AutoLoad::registerClass('spitfire\storage\database\Query',             $cur_dir.'/db/dbquery.php');
AutoLoad::registerClass('spitfire\storage\database\Field',             $cur_dir.'/db/field.php');
AutoLoad::registerClass('spitfire\storage\database\RestrictionGroup',  $cur_dir.'/db/restrictionGroup.php');
AutoLoad::registerClass('spitfire\storage\database\Restriction',       $cur_dir.'/db/restriction.php');
AutoLoad::registerClass('spitfire\storage\database\drivers\Driver',    $cur_dir.'/db/driver.php');
AutoLoad::registerClass('Pagination',            $cur_dir.'/db/pagination.php');

AutoLoad::registerClass('spitfire\storage\database\drivers\resultSetInterface',    $cur_dir.'/db/resultset.php');
AutoLoad::registerClass('databaseRecord',        $cur_dir.'/db/databaseRecord.php');

AutoLoad::registerClass('spitfire\storage\database\drivers\stdSQLDriver',     $cur_dir.'/db/drivers/stdSQL.php');
AutoLoad::registerClass('spitfire\storage\database\drivers\mysqlPDODriver',   $cur_dir.'/db/drivers/mysqlPDO.php');
AutoLoad::registerClass('spitfire\storage\database\drivers\mysqlPDOResultSet', $cur_dir.'/db/drivers/mysqlPDORes.php');

AutoLoad::registerClass('_SF_InputSanitizer',    $cur_dir.'/security_io_sanitization.php');
AutoLoad::registerClass('CoffeeBean',            $cur_dir.'/coffeebean.php');
AutoLoad::registerClass('_SF_Invoke',            $cur_dir.'/mvc/invoke.php');
AutoLoad::registerClass('spitfire\ClassInfo',    $cur_dir.'/class.php');

AutoLoad::registerClass('Strings',               $cur_dir.'/strings.php');
AutoLoad::registerClass('Headers',               $cur_dir.'/headers.php');

AutoLoad::registerClass('ComponentManager',      $cur_dir.'/components/componentManager.php');
AutoLoad::registerClass('Component',             $cur_dir.'/components/component.php');
AutoLoad::registerClass('assetsController',      $cur_dir.'/components/assets.php');

AutoLoad::registerClass('URL',                   $cur_dir.'/url.php');
AutoLoad::registerClass('session',               $cur_dir.'/session.php');

AutoLoad::registerClass('Email',                 $cur_dir.'/mail.php');