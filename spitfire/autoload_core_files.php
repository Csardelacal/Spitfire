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
AutoLoad::registerClass('spitfire\router\Router',                               $cur_dir.'/core/router/router.php');
AutoLoad::registerClass('spitfire\router\Server',                               $cur_dir.'/core/router/server.php');
AutoLoad::registerClass('spitfire\router\Route',                                $cur_dir.'/core/router/route.php');
AutoLoad::registerClass('spitfire\router\Routable',                             $cur_dir.'/core/router/routable.php');
AutoLoad::registerClass('spitfire\router\Pattern',                              $cur_dir.'/core/router/routeparser.php');
AutoLoad::registerClass('spitfire\router\RouteMismatchException',               $cur_dir.'/core/router/mismatchexception.php');
AutoLoad::registerClass('spitfire\environment',  $cur_dir.'/environment.php');

AutoLoad::registerClass('_SF_MVC',               $cur_dir.'/mvc/mvc.php');
AutoLoad::registerClass('Controller',            $cur_dir.'/mvc/controller.php');
AutoLoad::registerClass('spitfire\View',                                        $cur_dir.'/mvc/view.php');
AutoLoad::registerClass('_SF_ViewElement',       $cur_dir.'/mvc/view_element.php');

AutoLoad::registerClass('spitfire\MemcachedAdapter',                            $cur_dir.'/cache/memcached.php');
AutoLoad::registerClass('FileCache',                                            $cur_dir.'/cache/filecache.php');
AutoLoad::registerClass('SimpleFileCache',                                      $cur_dir.'/cache/filecachesimple.php');
AutoLoad::registerClass('Image',                 $cur_dir.'/image.php');
AutoLoad::registerClass('browser',               $cur_dir.'/security.php');

#Database related imports
AutoLoad::registerClass('spitfire\storage\database\DB',                $cur_dir.'/db/db.php');
AutoLoad::registerClass('spitfire\storage\database\Queriable',         $cur_dir.'/db/queriable.php');
AutoLoad::registerClass('spitfire\storage\database\Table',             $cur_dir.'/db/table.php');
AutoLoad::registerClass('spitfire\storage\database\DBField',           $cur_dir.'/db/field.php');
AutoLoad::registerClass('spitfire\storage\database\Query',             $cur_dir.'/db/dbquery.php');
AutoLoad::registerClass('spitfire\storage\database\QueryTable',                 $cur_dir.'/db/querytable.php');
AutoLoad::registerClass('spitfire\storage\database\QueryField',                 $cur_dir.'/db/queryfield.php');
AutoLoad::registerClass('spitfire\storage\database\RestrictionGroup',  $cur_dir.'/db/restrictionGroup.php');
AutoLoad::registerClass('spitfire\storage\database\Restriction',       $cur_dir.'/db/restriction.php');
AutoLoad::registerClass('spitfire\storage\database\CompositeRestriction',       $cur_dir.'/db/restrictionComposite.php');
AutoLoad::registerClass('spitfire\storage\database\drivers\Driver',    $cur_dir.'/db/driver.php');
AutoLoad::registerClass('spitfire\storage\database\Uplink',                     $cur_dir.'/db/uplink.php');
AutoLoad::registerClass('spitfire\storage\database\Downlink',                   $cur_dir.'/db/downlink.php');
AutoLoad::registerClass('spitfire\storage\database\Ancestor',                   $cur_dir.'/db/ancestor.php');
AutoLoad::registerClass('spitfire\storage\database\QueryJoin',                  $cur_dir.'/db/join.php');
AutoLoad::registerClass('Pagination',            $cur_dir.'/db/pagination.php');

AutoLoad::registerClass('spitfire\storage\database\drivers\resultSetInterface', $cur_dir.'/db/resultset.php');
AutoLoad::registerClass('Model',                                                $cur_dir.'/db/databaseRecord.php');

AutoLoad::registerClass('Validatable',                                          $cur_dir.'/validatable.php');
AutoLoad::registerClass('Schema',                                               $cur_dir.'/model/model.php');
AutoLoad::registerClass('OTFModel',                                             $cur_dir.'/model/onthefly.php');
AutoLoad::registerClass('spitfire\model\Field',                                 $cur_dir.'/model/field.php');
AutoLoad::registerClass('IntegerField',                                         $cur_dir.'/model/fields/integer.php');
AutoLoad::registerClass('FileField',                                            $cur_dir.'/model/fields/file.php');
AutoLoad::registerClass('TextField',                                            $cur_dir.'/model/fields/text.php');
AutoLoad::registerClass('StringField',                                          $cur_dir.'/model/fields/string.php');
AutoLoad::registerClass('EnumField',                                            $cur_dir.'/model/fields/enum.php');
AutoLoad::registerClass('BooleanField',                                         $cur_dir.'/model/fields/boolean.php');
AutoLoad::registerClass('DatetimeField',                                        $cur_dir.'/model/fields/datetime.php');
AutoLoad::registerClass('ManyToManyField',                                      $cur_dir.'/model/fields/manytomany.php');
AutoLoad::registerClass('Reference',                                            $cur_dir.'/model/reference.php');
AutoLoad::registerClass('ChildrenField',                                        $cur_dir.'/model/children.php');

AutoLoad::registerClass('spitfire\model\adapters\ManyToManyAdapter',            $cur_dir.'/model/adapters/m2madapter.php');
AutoLoad::registerClass('spitfire\model\adapters\ChildrenAdapter',              $cur_dir.'/model/adapters/childrenadapter.php');

AutoLoad::registerClass('spitfire\model\defaults\userModel',                    $cur_dir.'/model/defaults/usermodel_default.php');


AutoLoad::registerClass('spitfire\storage\database\drivers\stdSQLDriver',       $cur_dir.'/db/drivers/stdSQL.php');
AutoLoad::registerClass('spitfire\storage\database\drivers\stdSQLTable',        $cur_dir.'/db/drivers/stdSQLTable.php');
AutoLoad::registerClass('spitfire\storage\database\drivers\mysqlPDODriver',     $cur_dir.'/db/drivers/mysqlPDO.php');
AutoLoad::registerClass('spitfire\storage\database\drivers\MysqlPDOTable',      $cur_dir.'/db/drivers/mysqlPDOTable.php');
AutoLoad::registerClass('spitfire\storage\database\drivers\mysqlPDOField',      $cur_dir.'/db/drivers/mysqlPDOField.php');
AutoLoad::registerClass('spitfire\storage\database\drivers\MysqlPDOQuery',      $cur_dir.'/db/drivers/mysqlPDOQuery.php');
AutoLoad::registerClass('spitfire\storage\database\drivers\MysqlPDOQueryTable', $cur_dir.'/db/drivers/mysqlPDOQueryTable.php');
AutoLoad::registerClass('spitfire\storage\database\drivers\MysqlPDOQueryField', $cur_dir.'/db/drivers/mysqlPDOQueryField.php');
AutoLoad::registerClass('spitfire\storage\database\drivers\MysqlPDOJoin',       $cur_dir.'/db/drivers/mysqlPDOJoin.php');
AutoLoad::registerClass('spitfire\storage\database\drivers\MysqlPDORecord',     $cur_dir.'/db/drivers/mysqlPDORecord.php');
AutoLoad::registerClass('spitfire\storage\database\drivers\MysqlPDORestriction',$cur_dir.'/db/drivers/mysqlPDORestriction.php');
AutoLoad::registerClass('spitfire\storage\database\drivers\MysqlPDOCompositeRestriction',$cur_dir.'/db/drivers/mysqlPDORestrictionComposite.php');
AutoLoad::registerClass('spitfire\storage\database\drivers\MysqlPDORestrictionGroup',$cur_dir.'/db/drivers/mysqlPDORestrictionGroup.php');
AutoLoad::registerClass('spitfire\storage\database\drivers\mysqlPDOResultSet',  $cur_dir.'/db/drivers/mysqlPDORes.php');

AutoLoad::registerClass('_SF_InputSanitizer',    $cur_dir.'/security_io_sanitization.php');
AutoLoad::registerClass('CoffeeBean',            $cur_dir.'/io/beans/coffeebean.php');
AutoLoad::registerClass('spitfire\io\beans\Field',                              $cur_dir.'/io/beans/field.php');
AutoLoad::registerClass('spitfire\io\beans\BasicField',                         $cur_dir.'/io/beans/basic_field.php');
AutoLoad::registerClass('spitfire\io\beans\TextField',                          $cur_dir.'/io/beans/text_field.php');
AutoLoad::registerClass('spitfire\io\beans\LongTextField',                      $cur_dir.'/io/beans/long_text_field.php');
AutoLoad::registerClass('spitfire\io\beans\EnumField',                          $cur_dir.'/io/beans/enum_field.php');
AutoLoad::registerClass('spitfire\io\beans\BooleanField',                       $cur_dir.'/io/beans/boolean_field.php');
AutoLoad::registerClass('spitfire\io\beans\ReferenceField',                     $cur_dir.'/io/beans/reference_field.php');
AutoLoad::registerClass('spitfire\io\beans\ManyToManyField',                    $cur_dir.'/io/beans/manytomany_field.php');
AutoLoad::registerClass('spitfire\io\beans\FileField',                          $cur_dir.'/io/beans/file_field.php');
AutoLoad::registerClass('spitfire\io\beans\DateTimeField',                      $cur_dir.'/io/beans/datetime_field.php');
AutoLoad::registerClass('spitfire\io\beans\ChildBean',                          $cur_dir.'/io/beans/childbean.php');
AutoLoad::registerClass('spitfire\io\beans\renderers\Renderer',                 $cur_dir.'/io/beans/renderers/renderer.php');
AutoLoad::registerClass('spitfire\io\beans\renderers\SimpleBeanRenderer',       $cur_dir.'/io/beans/renderers/simpleBeanRenderer.php');
AutoLoad::registerClass('spitfire\io\beans\renderers\SimpleFieldRenderer',      $cur_dir.'/io/beans/renderers/simpleFieldRenderer.php');
AutoLoad::registerClass('_SF_Invoke',            $cur_dir.'/mvc/invoke.php');
AutoLoad::registerClass('spitfire\ClassInfo',    $cur_dir.'/class.php');


AutoLoad::registerClass('spitfire\io\Upload',                                   $cur_dir.'/io/upload.php');
AutoLoad::registerClass('spitfire\io\html\HTMLElement',                         $cur_dir.'/io/html/element.php');
AutoLoad::registerClass('spitfire\io\html\HTMLUnclosedElement',                 $cur_dir.'/io/html/unclosed.php');
AutoLoad::registerClass('spitfire\io\html\HTMLDiv',                             $cur_dir.'/io/html/div.php');
AutoLoad::registerClass('spitfire\io\html\HTMLSpan',                            $cur_dir.'/io/html/span.php');
AutoLoad::registerClass('spitfire\io\html\HTMLInput',                           $cur_dir.'/io/html/input.php');
AutoLoad::registerClass('spitfire\io\html\HTMLTextArea',                        $cur_dir.'/io/html/textarea.php');
AutoLoad::registerClass('spitfire\io\html\HTMLSelect',                          $cur_dir.'/io/html/select.php');
AutoLoad::registerClass('spitfire\io\html\HTMLOption',                          $cur_dir.'/io/html/option.php');
AutoLoad::registerClass('spitfire\io\html\HTMLLabel',                           $cur_dir.'/io/html/label.php');
AutoLoad::registerClass('spitfire\io\html\HTMLForm',                            $cur_dir.'/io/html/form.php');
AutoLoad::registerClass('spitfire\io\html\HTMLTable',                           $cur_dir.'/io/html/table.php');
AutoLoad::registerClass('spitfire\io\html\HTMLTableRow',                        $cur_dir.'/io/html/table_row.php');
AutoLoad::registerClass('spitfire\io\html\dateTimePicker',                      $cur_dir.'/io/html/date_picker.php');

AutoLoad::registerClass('Strings',               $cur_dir.'/strings.php');
AutoLoad::registerClass('Headers',               $cur_dir.'/headers.php');

AutoLoad::registerClass('Locale',                $cur_dir.'/locale/locale.php');
AutoLoad::registerClass('spitfire\locales\langInfo',                            $cur_dir.'/locale/lang_info.php');

AutoLoad::registerClass('ComponentManager',                                     $cur_dir.'/components/componentManager.php');
AutoLoad::registerClass('Component',                                            $cur_dir.'/components/component.php');

AutoLoad::registerClass('spitfire\registry\Registry',                           $cur_dir.'/io/registry/registry.php');
AutoLoad::registerClass('spitfire\registry\JSRegistry',                         $cur_dir.'/io/registry/jsregistry.php');
AutoLoad::registerClass('spitfire\registry\CSSRegistry',                        $cur_dir.'/io/registry/cssregistry.php');

AutoLoad::registerClass('Pluggable',                                            $cur_dir.'/plugins/pluggable.php');

AutoLoad::registerClass('URL',                                                  $cur_dir.'/url.php');
AutoLoad::registerClass('absoluteURL',                                          $cur_dir.'/absoluteURL.php');
AutoLoad::registerClass('spitfire\Request',                                     $cur_dir.'/core/request.php');
AutoLoad::registerClass('spitfire\Intent',                                      $cur_dir.'/core/intent.php');
AutoLoad::registerClass('spitfire\Response',                                    $cur_dir.'/core/response.php');
AutoLoad::registerClass('spitfire\path\PathParser',                             $cur_dir.'/core/path/PathParser.php');
AutoLoad::registerClass('spitfire\path\AppParser',                              $cur_dir.'/core/path/AppParser.php');
AutoLoad::registerClass('spitfire\path\ControllerParser',                       $cur_dir.'/core/path/ControllerParser.php');
AutoLoad::registerClass('spitfire\path\ActionParser',                           $cur_dir.'/core/path/ActionParser.php');
AutoLoad::registerClass('spitfire\path\ObjectParser',                           $cur_dir.'/core/path/ObjectParser.php');
AutoLoad::registerClass('session',               $cur_dir.'/session.php');

AutoLoad::registerClass('Email',                 $cur_dir.'/mail.php');