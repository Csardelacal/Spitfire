<!DOCTYPE html>
<html>
	<head>
		<title><?= $FW_NAME ?> - test</title>
	</head>
	<body>
		<h1>It works!</h1>
		<p>Spitfire is working as it should.</p>
		<p>This task was completed by <?=$controller?></p>
		<script src ="<?= _t('helloworld') ?>"></script>
		<p><?= _t('helloworld') ?>, To test GETing we want you to enter some info about you:</p>
		<p><?= _t('comment_count', 0) ?></p>
		<p><?= _t('comment_count', 1) ?></p>
		<p><?= _t('comment_count', 2) ?></p>
		<p><?= _t('comment_count', 45) ?></p>
		<p><?= $_SERVER['HTTP_ACCEPT_LANGUAGE'] ?></p>
		<form action="<?php echo new URL('home', 'save'); ?>" method="POST">

			<label for="name">Name:</label><input type="text" name="name" id="name">
			<label for="age" >Age: </label><input type="text" name="age"  id="age" >
			<label for="pass">Pass:</label><input type="password" name="pass" id="pass">

			<input type="submit" value="Send">
		</form>
	</body>
</html>