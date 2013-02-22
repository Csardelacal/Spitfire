
<?php

echo CoffeeBean::getBean($bean)->makeList($records, Array('edit' => $this->app->url('/edit/'. $bean . '/%s') ));

?>
<a href="<?= $this->app->url("/create/$bean") ?>">New</a>