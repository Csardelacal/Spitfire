
<a href="<?= $this->app->url("/create/$bean") ?>" class="new-record">Insert a new record</a>

<div class="table-wrapper">
	<?php

	echo CoffeeBean::getBean($bean)->makeList($records, Array('edit' => $this->app->url('/edit/'. $bean . '/%s') ));
	echo '<div class="pagination">' . $paging . '</div>';

	?>
</div>