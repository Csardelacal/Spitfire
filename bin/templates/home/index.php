<html>
	<head>
		<title>Test Results</title>
	</head>
	<body>
		<div class="wrapper">
			<div class="results performed"><?= $result['performed'] ?> tests performed</div>
			<div class="results failed"><?= $result['failed'] ?> test failed</div>
			<div class="errors">
				<h1>Errors</h1>
				<ul>
					<?php foreach ($result['errors'] as $error): ?>
					<li><?= $error ?></li>
					<?php endforeach;?>
				</ul>
			</div>
		</div>
	</body>
</html>