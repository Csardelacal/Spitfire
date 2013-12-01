
<a href="<?= $this->app->url("/create/{$bean->getName()}") ?>" class="new-record">Insert a new record</a>

<div class="table-wrapper">
	<?php

	echo $bean->makeList(new M3W\admin\Renderer(), $records);
	echo '<div class="pagination">' . $paging . '</div>';

	?>
</div>