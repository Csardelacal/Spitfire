<div class="edit">
	<?= CoffeeBean::getBean($bean)->makeForm($this->app->url('/insert/' . $bean)) ?>
</div>