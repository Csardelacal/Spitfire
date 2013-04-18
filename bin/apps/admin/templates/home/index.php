<h1>Admin</h1>
<pre><?= $_SF_DEBUG_OUTPUT ?></pre>
<?= $this->element('test'); ?>

<?php foreach($beans as $bean_name): ?>
	<a href="<?= $this->app->url('/lst/' . $bean_name)?>"><?= CoffeeBean::getBean($bean_name)->getName() ?></a><br/>
<?php endforeach; ?>
