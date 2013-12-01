<?php $module_groups = $this->app->getModules(); ?>

<div>
	<?= implode("\n<div class='spacer' style='height: 20px'></div>\n", $module_groups); ?>
</div>