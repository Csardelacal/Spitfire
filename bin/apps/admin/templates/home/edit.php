<?php

$b = $bean;
$b->setDBRecord($record);
$b->updateDBRecord();

?>
<div class="edit">
	<?= $b->makeForm(new \M3W\admin\Renderer(), $errors); ?>
</div>