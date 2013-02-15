<h1>Admin</h1>
<pre><?= $_SF_DEBUG_OUTPUT ?></pre>
<?= $this->element('test'); ?>

<?php foreach($beans as $bean): ?>
	<a href="<?= $this->app->url('/lst/' . $bean)?>"><?=$bean ?></a><br/>
<?php endforeach; ?>
