<html>
	<head>
		<title>Spitfire Administration</title>
		<?php $this->css(URL::asset('css/admin.css', $this->app)) ?>
		<?= $this->css ?>
		<link href='http://fonts.googleapis.com/css?family=Roboto:400,400italic,700' rel='stylesheet' type='text/css'>
	</head>
	<body>
		<div class="header">
			<a class="logout" href="<?= $this->app->url('/auth/logout') ?>">Logout</a>
			<h1><a href="<?= $this->app->url('/') ?>">Spitfire administration area</a></h1>
			
			<?php if (isset($message)): ?>
			<div class="message-wrapper">
				<span class="message <?= $message_type ?>"><?= $message ?></span>
			</div>
			<?php endif; ?>
		</div>
		<div class="wrapper">
			<div class="spacer" style="height: 50px;"></div>
			<?php if (isset($beans)): ?>
				<div class="beans-list">
				<?php if (!isset($search_enabled) || !$search_enabled): ?>
					<span class="beans-help">Select an element on this list to administrate it's contents </span>
				<?php else: ?>
					<form class="filter" method="GET" action="">
						<input type="search" name="search" placeholder="<?= _t('search') ?>...">
						<input type="submit" value="Ok">
					</form>
				<?php endif; ?>
				<?php foreach ($beans as $b): ?>
					<a href="<?= $this->app->url('/lst/' . $b->getName()) ?>" class="bean <?= ($b->getName() == $bean)?'active':'' ?>"><?= $b->getName() ?></a>
				<?php endforeach; ?>
				</div>
			<?php endif; ?>

			<div class="main">
				<?= $content_for_layout ?>
				
				<?php if(spitfire\environment::get('debugging_mode')): ?>
				<div style="margin: 10px;">
					<pre class="debugmessages"><ul><?php 
								$messages = spitfire()->getMessages();
								foreach ($messages as $message) echo "<li>$message</li>";
							?></ul></pre>
				</div>
				<?php endif; ?>
			</div>
		</div>
		<?= $this->js(); ?>
	</body>
</html>