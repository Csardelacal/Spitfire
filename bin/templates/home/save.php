<?php 
header('Content-type: text/html; charset=utf-8');
?><!DOCTYPE html>
<html>
	<head>
		<title><?= $FW_NAME ?> - test</title>
		<link rel="stylesheet" type="text/css" href="http://twitter.github.com/bootstrap/assets/css/bootstrap.css" />
	</head>
	<body>
		<h1>It works!</h1>
		<p>Spitfire is working as it should.</p>
		<p><?=$this->element('test'); ?></p>
		<p><?php $t = $this->element('test'); $t->set('test_text', ' with extra text'); echo $t;?></p>
		<p>This was the data you typed:</p>
		<ul>
			<li> <b>Name: </b> <?= $name ?></li>
			<li> <b>Age:  </b> <?= $age  ?></li>
			<li> <b>Pass: </b> <?= $pass ?></li>
		</ul>
		<pre>
		<?php print_r($test); ?>
		<?php echo floor(memory_get_peak_usage() / 1024), 'KB' ?>
		</pre>
		<div class="pagination"><?= $pagination ?></div>
	</body>
</html>