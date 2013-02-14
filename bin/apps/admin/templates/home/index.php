<h1>Admin</h1>
<pre><?= $_SF_DEBUG_OUTPUT ?></pre>
<?= $this->element('test'); ?>


<a href="<?= $this->app->url('/home/index.php'); ?>">Admin Home</a><br>
<a href="<?= $this->app->url('www.google.es'); ?>">Google</a><br>
<a href="<?= spitfire()->url('/home/index.php'); ?>">Site Home</a><br>