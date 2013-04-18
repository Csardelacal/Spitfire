
<a href="<?= $this->app->url("/create/$bean") ?>" class="new-record">Insert a new record</a>

<?php

echo CoffeeBean::getBean($bean)->makeList($records, Array('edit' => $this->app->url('/edit/'. $bean . '/%s') ));
echo $paging;

?>