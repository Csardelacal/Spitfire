<?php

use spitfire\AutoLoad;

#Set the base directory
$cur_dir = dirname(__FILE__);

#Define default classes and their locations
AutoLoad::registerClass('fileNotFoundException', $cur_dir.'/exceptions.php');
AutoLoad::registerClass('publicException',       $cur_dir.'/exceptions.php');
AutoLoad::registerClass('privateException',      $cur_dir.'/exceptions.php');
AutoLoad::registerClass('spitfire\exceptions\ExceptionHandler',  $cur_dir.'/exceptionHandler.php');
//AutoLoad::registerClass('spitfire\Path',         $cur_dir.'/core/path.php');

AutoLoad::registerClass('router',                $cur_dir.'/router.php');
AutoLoad::registerClass('spitfire\environment',  $cur_dir.'/environment.php');

AutoLoad::registerClass('_SF_MVC',               $cur_dir.'/mvc/mvc.php');
AutoLoad::registerClass('Controller',            $cur_dir.'/mvc/controller.php');
AutoLoad::registerClass('spitfire\View',                                        $cur_dir.'/mvc/view.php');
AutoLoad::registerClass('_SF_ViewElement',       $cur_dir.'/mvc/view_element.php');

AutoLoad::registerClass('_SF_Memcached',         $cur_dir.'/storage.php');
AutoLoad::registerClass('Image',                 $cur_dir.'/image.php');
AutoLoad::registerClass('browser',               $cur_dir.'/security.php');

#Database related imports
AutoLoad::registerClass('spitfire\storage\database\DB',                $cur_dir.'/db/db.php');
AutoLoad::registerClass('spitfire\storage\database\Queriable',         $cur_dir.'/db/queriable.php');
AutoLoad::registerClass('spitfire\storage\database\Table',             $cur_dir.'/db/table.php');
AutoLoad::registerClass('spitfire\storage\database\DBField',           $cur_dir.'/db/field.php');
AutoLoad::registerClass('spitfire\storage\database\Query',             $cur_dir.'/db/dbquery.php');
AutoLoad::registerClass('spitfire\storage\database\RestrictionGroup',  $cur_dir.'/db/restrictionGroup.php');
AutoLoad::registerClass('spitfire\storage\database\Restriction',       $cur_dir.'/db/restriction.php');
AutoLoad::registerClass('spitfire\storage\database\drivers\Driver',    $cur_dir.'/db/driver.php');
AutoLoad::registerClass('spitfire\storage\database\Ancestor',                   $cur_dir.'/db/ancestor.php');
AutoLoad::registerClass('Pagination',            $cur_dir.'/db/pagination.php');

AutoLoad::registerClass('spitfire\storage\database\drivers\resultSetInterface', $cur_dir.'/db/resultset.php');
AutoLoad::registerClass('databaseRecord',                                       $cur_dir.'/db/databaseRecord.php');

AutoLoad::registerClass('Validatable',                                          $cur_dir.'/validatable.php');
AutoLoad::registerClass('Model',                                                $cur_dir.'/model/model.php');
AutoLoad::registerClass('OTFModel',                                             $cur_dir.'/model/onthefly.php');
AutoLoad::registerClass('spitfire\model\Field',                                 $cur_dir.'/model/field.php');
AutoLoad::registerClass('IntegerField',                                         $cur_dir.'/model/fields/integer.php');
AutoLoad::registerClass('FileField',                                            $cur_dir.'/model/fields/file.php');
AutoLoad::registerClass('TextField',                                            $cur_dir.'/model/fields/text.php');
AutoLoad::registerClass('StringField',                                          $cur_dir.'/model/fields/string.php');
AutoLoad::registerClass('DatetimeField',                                        $cur_dir.'/model/fields/datetime.php');
AutoLoad::registerClass('spitfire\model\Reference',                             $cur_dir.'/model/reference.php');


AutoLoad::registerClass('spitfire\model\defaults\userModel',                    $cur_dir.'/model/defaults/usermodel_default.php');


AutoLoad::registerClass('spitfire\storage\database\drivers\stdSQLDriver',       $cur_dir.'/db/drivers/stdSQL.php');
AutoLoad::registerClass('spitfire\storage\database\drivers\stdSQLTable',        $cur_dir.'/db/drivers/stdSQLTable.php');
AutoLoad::registerClass('spitfire\storage\database\drivers\mysqlPDODriver',     $cur_dir.'/db/drivers/mysqlPDO.php');
AutoLoad::registerClass('spitfire\storage\database\drivers\MysqlPDOTable',      $cur_dir.'/db/drivers/mysqlPDOTable.php');
AutoLoad::registerClass('spitfire\storage\database\drivers\mysqlPDOField',      $cur_dir.'/db/drivers/mysqlPDOField.php');
AutoLoad::registerClass('spitfire\storage\database\drivers\MysqlPDOQuery',      $cur_dir.'/db/drivers/mysqlPDOQuery.php');
AutoLoad::registerClass('spitfire\storage\database\drivers\MysqlPDORecord',     $cur_dir.'/db/drivers/mysqlPDORecord.php');
AutoLoad::registerClass('spitfire\storage\database\drivers\MysqlPDORestriction',$cur_dir.'/db/drivers/mysqlPDORestriction.php');
AutoLoad::registerClass('spitfire\storage\database\drivers\MysqlPDORestrictionGroup',$cur_dir.'/db/drivers/mysqlPDORestrictionGroup.php');
AutoLoad::registerClass('spitfire\storage\database\drivers\mysqlPDOResultSet',  $cur_dir.'/db/drivers/mysqlPDORes.php');

AutoLoad::registerClass('_SF_InputSanitizer',    $cur_dir.'/security_io_sanitization.php');
AutoLoad::registerClass('CoffeeBean',            $cur_dir.'/io/beans/coffeebean.php');
AutoLoad::registerClass('spitfire\io\beans\Field',                              $cur_dir.'/io/beans/field.php');
AutoLoad::registerClass('spitfire\io\beans\TextField',                          $cur_dir.'/io/beans/text_field.php');
AutoLoad::registerClass('spitfire\io\beans\LongTextField',                      $cur_dir.'/io/beans/long_text_field.php');
AutoLoad::registerClass('spitfire\io\beans\ReferenceField',                     $cur_dir.'/io/beans/reference_field.php');
AutoLoad::registerClass('spitfire\io\beans\FileField',                          $cur_dir.'/io/beans/file_field.php');
AutoLoad::registerClass('_SF_Invoke',            $cur_dir.'/mvc/invoke.php');
AutoLoad::registerClass('spitfire\ClassInfo',    $cur_dir.'/class.php');


AutoLoad::registerClass('spitfire\io\html\HTMLElement',                         $cur_dir.'/io/html/element.php');
AutoLoad::registerClass('spitfire\io\html\HTMLForm',                            $cur_dir.'/io/html/form.php');
AutoLoad::registerClass('spitfire\io\html\HTMLTable',                           $cur_dir.'/io/html/table.php');
AutoLoad::registerClass('spitfire\io\html\HTMLTableRow',                        $cur_dir.'/io/html/table_row.php');

AutoLoad::registerClass('Strings',               $cur_dir.'/strings.php');
AutoLoad::registerClass('Headers',               $cur_dir.'/headers.php');

AutoLoad::registerClass('Locale',                $cur_dir.'/locale/locale.php');
AutoLoad::registerClass('spitfire\locales\langInfo',                            $cur_dir.'/locale/lang_info.php');

AutoLoad::registerClass('ComponentManager',                                     $cur_dir.'/components/componentManager.php');
AutoLoad::registerClass('Component',                                            $cur_dir.'/components/component.php');

AutoLoad::registerClass('spitfire\registry\Registry',                           $cur_dir.'/io/registry/registry.php');
AutoLoad::registerClass('spitfire\registry\JSRegistry',                         $cur_dir.'/io/registry/jsregistry.php');
AutoLoad::registerClass('spitfire\registry\CSSRegistry',                        $cur_dir.'/io/registry/cssregistry.php');

AutoLoad::registerClass('URL',                                                  $cur_dir.'/url.php');
AutoLoad::registerClass('absoluteURL',                                          $cur_dir.'/absoluteURL.php');
AutoLoad::registerClass('spitfire\Request',                                     $cur_dir.'/core/request.php');
AutoLoad::registerClass('session',               $cur_dir.'/session.php');

AutoLoad::registerClass('Email',                 $cur_dir.'/mail.php');