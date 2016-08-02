<?php

use spitfire\AutoLoad;


#Define default classes and their locations
AutoLoad::registerClass('spitfire\environment',                                 SPITFIRE_BASEDIR.'/environment.php');

AutoLoad::registerClass('spitfire\MVC',                                         SPITFIRE_BASEDIR.'/mvc/mvc.php');
AutoLoad::registerClass('Controller',                                           SPITFIRE_BASEDIR.'/mvc/controller.php');
AutoLoad::registerClass('spitfire\View',                                        SPITFIRE_BASEDIR.'/mvc/view.php');
AutoLoad::registerClass('_SF_ViewElement',                                      SPITFIRE_BASEDIR.'/mvc/view_element.php');

AutoLoad::registerClass('Time',                                                 SPITFIRE_BASEDIR.'/time.php');
AutoLoad::registerClass('Image',                                                SPITFIRE_BASEDIR.'/image.php');
AutoLoad::registerClass('browser',                                              SPITFIRE_BASEDIR.'/security.php');

#Database related imports
AutoLoad::registerClass('spitfire\storage\database\Queriable',                  SPITFIRE_BASEDIR.'/db/queriable.php');
AutoLoad::registerClass('spitfire\storage\database\DBField',                    SPITFIRE_BASEDIR.'/db/field.php');
AutoLoad::registerClass('spitfire\storage\database\QueryTable',                 SPITFIRE_BASEDIR.'/db/querytable.php');
AutoLoad::registerClass('spitfire\storage\database\QueryField',                 SPITFIRE_BASEDIR.'/db/queryfield.php');
AutoLoad::registerClass('spitfire\storage\database\Restriction',                SPITFIRE_BASEDIR.'/db/restriction.php');
AutoLoad::registerClass('spitfire\storage\database\CompositeRestriction',       SPITFIRE_BASEDIR.'/db/restrictionComposite.php');
AutoLoad::registerClass('spitfire\storage\database\drivers\Driver',             SPITFIRE_BASEDIR.'/db/driver.php');
AutoLoad::registerClass('spitfire\storage\database\Uplink',                     SPITFIRE_BASEDIR.'/db/uplink.php');
AutoLoad::registerClass('spitfire\storage\database\Downlink',                   SPITFIRE_BASEDIR.'/db/downlink.php');
AutoLoad::registerClass('spitfire\storage\database\Ancestor',                   SPITFIRE_BASEDIR.'/db/ancestor.php');
AutoLoad::registerClass('spitfire\storage\database\QueryJoin',                  SPITFIRE_BASEDIR.'/db/join.php');
AutoLoad::registerClass('Pagination',                                           SPITFIRE_BASEDIR.'/db/pagination.php');

AutoLoad::registerClass('Model',                                                SPITFIRE_BASEDIR.'/db/databaseRecord.php');

AutoLoad::registerClass('Validatable',                                          SPITFIRE_BASEDIR.'/validatable.php');
AutoLoad::registerClass('ValidationException',                                  SPITFIRE_BASEDIR.'/validation/ValidationException.php');
AutoLoad::registerClass('Schema',                                               SPITFIRE_BASEDIR.'/model/model.php');
AutoLoad::registerClass('OTFModel',                                             SPITFIRE_BASEDIR.'/model/onthefly.php');
AutoLoad::registerClass('spitfire\model\Field',                                 SPITFIRE_BASEDIR.'/model/field.php');
AutoLoad::registerClass('IntegerField',                                         SPITFIRE_BASEDIR.'/model/fields/integer.php');
AutoLoad::registerClass('FileField',                                            SPITFIRE_BASEDIR.'/model/fields/file.php');
AutoLoad::registerClass('TextField',                                            SPITFIRE_BASEDIR.'/model/fields/text.php');
AutoLoad::registerClass('StringField',                                          SPITFIRE_BASEDIR.'/model/fields/string.php');
AutoLoad::registerClass('EnumField',                                            SPITFIRE_BASEDIR.'/model/fields/enum.php');
AutoLoad::registerClass('BooleanField',                                         SPITFIRE_BASEDIR.'/model/fields/boolean.php');
AutoLoad::registerClass('DatetimeField',                                        SPITFIRE_BASEDIR.'/model/fields/datetime.php');
AutoLoad::registerClass('ManyToManyField',                                      SPITFIRE_BASEDIR.'/model/fields/manytomany.php');
AutoLoad::registerClass('Reference',                                            SPITFIRE_BASEDIR.'/model/reference.php');
AutoLoad::registerClass('ChildrenField',                                        SPITFIRE_BASEDIR.'/model/children.php');

AutoLoad::registerClass('spitfire\model\adapters\ManyToManyAdapter',            SPITFIRE_BASEDIR.'/model/adapters/m2madapter.php');
AutoLoad::registerClass('spitfire\model\adapters\BridgeAdapter',                SPITFIRE_BASEDIR.'/model/adapters/bridgeadapter.php');
AutoLoad::registerClass('spitfire\model\adapters\ChildrenAdapter',              SPITFIRE_BASEDIR.'/model/adapters/childrenadapter.php');

AutoLoad::registerClass('spitfire\model\defaults\userModel',                    SPITFIRE_BASEDIR.'/model/defaults/usermodel_default.php');


AutoLoad::registerClass('spitfire\storage\database\drivers\stdSQLDriver',       SPITFIRE_BASEDIR.'/db/drivers/stdSQL.php');
AutoLoad::registerClass('spitfire\storage\database\drivers\stdSQLTable',        SPITFIRE_BASEDIR.'/db/drivers/stdSQLTable.php');
AutoLoad::registerClass('spitfire\storage\database\drivers\mysqlPDODriver',     SPITFIRE_BASEDIR.'/db/drivers/mysqlPDO.php');
AutoLoad::registerClass('spitfire\storage\database\drivers\MysqlPDOTable',      SPITFIRE_BASEDIR.'/db/drivers/mysqlPDOTable.php');
AutoLoad::registerClass('spitfire\storage\database\drivers\mysqlPDOField',      SPITFIRE_BASEDIR.'/db/drivers/mysqlPDOField.php');
AutoLoad::registerClass('spitfire\storage\database\drivers\MysqlPDOQuery',      SPITFIRE_BASEDIR.'/db/drivers/mysqlPDOQuery.php');
AutoLoad::registerClass('spitfire\storage\database\drivers\MysqlPDOQueryTable', SPITFIRE_BASEDIR.'/db/drivers/mysqlPDOQueryTable.php');
AutoLoad::registerClass('spitfire\storage\database\drivers\MysqlPDOQueryField', SPITFIRE_BASEDIR.'/db/drivers/mysqlPDOQueryField.php');
AutoLoad::registerClass('spitfire\storage\database\drivers\MysqlPDOJoin',       SPITFIRE_BASEDIR.'/db/drivers/mysqlPDOJoin.php');
AutoLoad::registerClass('spitfire\storage\database\drivers\MysqlPDORecord',     SPITFIRE_BASEDIR.'/db/drivers/mysqlPDORecord.php');
AutoLoad::registerClass('spitfire\storage\database\drivers\MysqlPDORestriction',SPITFIRE_BASEDIR.'/db/drivers/mysqlPDORestriction.php');
AutoLoad::registerClass('spitfire\storage\database\drivers\MysqlPDOCompositeRestriction',SPITFIRE_BASEDIR.'/db/drivers/mysqlPDORestrictionComposite.php');
AutoLoad::registerClass('spitfire\storage\database\drivers\MysqlPDORestrictionGroup',SPITFIRE_BASEDIR.'/db/drivers/mysqlPDORestrictionGroup.php');
AutoLoad::registerClass('spitfire\storage\database\drivers\mysqlPDOResultSet',  SPITFIRE_BASEDIR.'/db/drivers/mysqlPDORes.php');
AutoLoad::registerClass('spitfire\storage\database\drivers\mysqlPDOSelectStringifier',  SPITFIRE_BASEDIR.'/db/drivers/mysqlPDOSelectStringifier.php');

AutoLoad::registerClass('spitfire\InputSanitizer',                              SPITFIRE_BASEDIR.'/security_io_sanitization.php');
AutoLoad::registerClass('CoffeeBean',                                           SPITFIRE_BASEDIR.'/io/beans/coffeebean.php');
AutoLoad::registerClass('spitfire\io\beans\Field',                              SPITFIRE_BASEDIR.'/io/beans/field.php');
AutoLoad::registerClass('spitfire\io\beans\BasicField',                         SPITFIRE_BASEDIR.'/io/beans/basic_field.php');
AutoLoad::registerClass('spitfire\io\beans\TextField',                          SPITFIRE_BASEDIR.'/io/beans/text_field.php');
AutoLoad::registerClass('spitfire\io\beans\IntegerField',                       SPITFIRE_BASEDIR.'/io/beans/integer_field.php');
AutoLoad::registerClass('spitfire\io\beans\LongTextField',                      SPITFIRE_BASEDIR.'/io/beans/long_text_field.php');
AutoLoad::registerClass('spitfire\io\beans\EnumField',                          SPITFIRE_BASEDIR.'/io/beans/enum_field.php');
AutoLoad::registerClass('spitfire\io\beans\BooleanField',                       SPITFIRE_BASEDIR.'/io/beans/boolean_field.php');
AutoLoad::registerClass('spitfire\io\beans\ReferenceField',                     SPITFIRE_BASEDIR.'/io/beans/reference_field.php');
AutoLoad::registerClass('spitfire\io\beans\ManyToManyField',                    SPITFIRE_BASEDIR.'/io/beans/manytomany_field.php');
AutoLoad::registerClass('spitfire\io\beans\FileField',                          SPITFIRE_BASEDIR.'/io/beans/file_field.php');
AutoLoad::registerClass('spitfire\io\beans\DateTimeField',                      SPITFIRE_BASEDIR.'/io/beans/datetime_field.php');
AutoLoad::registerClass('spitfire\io\beans\ChildBean',                          SPITFIRE_BASEDIR.'/io/beans/childbean.php');
AutoLoad::registerClass('spitfire\io\beans\renderers\Renderer',                 SPITFIRE_BASEDIR.'/io/beans/renderers/renderer.php');
AutoLoad::registerClass('spitfire\io\beans\renderers\SimpleBeanRenderer',       SPITFIRE_BASEDIR.'/io/beans/renderers/simpleBeanRenderer.php');
AutoLoad::registerClass('spitfire\io\beans\renderers\SimpleFieldRenderer',      SPITFIRE_BASEDIR.'/io/beans/renderers/simpleFieldRenderer.php');
AutoLoad::registerClass('_SF_Invoke',                                           SPITFIRE_BASEDIR.'/mvc/invoke.php');


AutoLoad::registerClass('spitfire\io\html\HTMLElement',                         SPITFIRE_BASEDIR.'/io/html/element.php');
AutoLoad::registerClass('spitfire\io\html\HTMLUnclosedElement',                 SPITFIRE_BASEDIR.'/io/html/unclosed.php');
AutoLoad::registerClass('spitfire\io\html\HTMLDiv',                             SPITFIRE_BASEDIR.'/io/html/div.php');
AutoLoad::registerClass('spitfire\io\html\HTMLSpan',                            SPITFIRE_BASEDIR.'/io/html/span.php');
AutoLoad::registerClass('spitfire\io\html\HTMLInput',                           SPITFIRE_BASEDIR.'/io/html/input.php');
AutoLoad::registerClass('spitfire\io\html\HTMLTextArea',                        SPITFIRE_BASEDIR.'/io/html/textarea.php');
AutoLoad::registerClass('spitfire\io\html\HTMLSelect',                          SPITFIRE_BASEDIR.'/io/html/select.php');
AutoLoad::registerClass('spitfire\io\html\HTMLOption',                          SPITFIRE_BASEDIR.'/io/html/option.php');
AutoLoad::registerClass('spitfire\io\html\HTMLLabel',                           SPITFIRE_BASEDIR.'/io/html/label.php');
AutoLoad::registerClass('spitfire\io\html\HTMLForm',                            SPITFIRE_BASEDIR.'/io/html/form.php');
AutoLoad::registerClass('spitfire\io\html\HTMLTable',                           SPITFIRE_BASEDIR.'/io/html/table.php');
AutoLoad::registerClass('spitfire\io\html\HTMLTableRow',                        SPITFIRE_BASEDIR.'/io/html/table_row.php');
AutoLoad::registerClass('spitfire\io\html\dateTimePicker',                      SPITFIRE_BASEDIR.'/io/html/date_picker.php');

AutoLoad::registerClass('Strings',                                              SPITFIRE_BASEDIR.'/Strings.php');

AutoLoad::registerClass('Locale',                                               SPITFIRE_BASEDIR.'/locale/locale.php');
AutoLoad::registerClass('spitfire\locale\langInfo',                             SPITFIRE_BASEDIR.'/locale/lang_info.php');

AutoLoad::registerClass('ComponentManager',                                     SPITFIRE_BASEDIR.'/components/componentManager.php');
AutoLoad::registerClass('Component',                                            SPITFIRE_BASEDIR.'/components/component.php');

AutoLoad::registerClass('spitfire\registry\Registry',                           SPITFIRE_BASEDIR.'/io/registry/registry.php');
AutoLoad::registerClass('spitfire\registry\JSRegistry',                         SPITFIRE_BASEDIR.'/io/registry/jsregistry.php');
AutoLoad::registerClass('spitfire\registry\CSSRegistry',                        SPITFIRE_BASEDIR.'/io/registry/cssregistry.php');

AutoLoad::registerClass('Pluggable',                                            SPITFIRE_BASEDIR.'/plugins/pluggable.php');

AutoLoad::registerClass('URL',                                                  SPITFIRE_BASEDIR.'/url.php');
AutoLoad::registerClass('absoluteURL',                                          SPITFIRE_BASEDIR.'/absoluteURL.php');
AutoLoad::registerClass('spitfire\Context',                                     SPITFIRE_BASEDIR.'/core/context.php');
AutoLoad::registerClass('spitfire\path\PathParser',                             SPITFIRE_BASEDIR.'/core/path/PathParser.php');
AutoLoad::registerClass('spitfire\path\AppParser',                              SPITFIRE_BASEDIR.'/core/path/AppParser.php');
AutoLoad::registerClass('spitfire\path\ControllerParser',                       SPITFIRE_BASEDIR.'/core/path/ControllerParser.php');
AutoLoad::registerClass('spitfire\path\ActionParser',                           SPITFIRE_BASEDIR.'/core/path/ActionParser.php');
AutoLoad::registerClass('spitfire\path\ObjectParser',                           SPITFIRE_BASEDIR.'/core/path/ObjectParser.php');
AutoLoad::registerClass('session',                                              SPITFIRE_BASEDIR.'/session.php');

AutoLoad::registerClass('Email',                                                SPITFIRE_BASEDIR.'/mail.php');
