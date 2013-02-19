<form method="POST" action="<?= $this->app->url('/auth/login') ?>">
	<input type="text" name="username">
	<input type="password" name="password">
	<input type="submit">
</form>