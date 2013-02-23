<?php

$b = CoffeeBean::getBean($bean);
$b->setDBRecord($record);
$nr = $b->makeDBRecord();

if (!$record->getTable()->validate($nr)) {
	print_r($record->getTable()->getErrors());
}

echo $b->makeForm();

?>
