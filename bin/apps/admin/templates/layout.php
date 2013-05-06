<html>
	<head>
		<title>Spitfire Administration</title>
		<link rel="stylesheet" type="text/css" href="<?= URL::asset('css/admin.css', $this->app) ?>" />
	</head>
	<body>
		<div class="header">
			<a class="logout" href="<?= $this->app->url('/auth/logout') ?>">Logout</a>
			<h1>Spitfire administration area</h1>
		</div>
		<div class="wrapper">
			<div class="spacer" style="height: 50px;"></div>
			<?php if (isset($beans)): ?>
				<div class="beans-list">
					<span class="beans-help">Select an element on this list to administrate it's contents </span>
				<?php foreach ($beans as $bean_name): ?>
					<a href="<?= $this->app->url('/lst/' . $bean_name) ?>" class="bean <?= ($bean_name == $bean)?'active':'' ?>"><?= CoffeeBean::getBean($bean_name)->getName() ?></a>
				<?php endforeach; ?>
				</div>
			<?php endif; ?>

			<div class="main">
				<?= $content_for_layout ?>
			</div>
		</div>
	</body>
</html>