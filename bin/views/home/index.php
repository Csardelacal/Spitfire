<!DOCTYPE html>
<html>
	<head>
		<title><?= $FW_NAME ?> - test</title>
	</head>
	<body>
		<h1>It works!</h1>
		<p>Spitfire is working as it should.</p>
		<p>This task was completed by <?=$controller?></p>
		<script src ="<?= $helloworld ?>"></script>
		<p>To test GETing we want you to enter some info about you:</p>
		<form action="<?php echo new URL('home', 'save'); ?>" method="POST">

			<label for="name">Name:</label><input type="text" name="name" id="name">
			<label for="age" >Age: </label><input type="text" name="age"  id="age" >
			<label for="pass">Pass:</label><input type="password" name="pass" id="pass">

			<input type="submit" value="Send">
		</form>
	</body>
</html>