<div id="auth">
	<form method="POST" action="<?= $this->app->url('/auth/login') ?>">
		<div class="field">
			<label for ="username"><?= _t('username') ?></label>
			<input type="text" name="username">
		</div>
		<div class="field">
			<label for ="password"><?= _t('password') ?></label>
			<input type="password" name="password">
		</div>
		<input type="submit"   value="Login">
	</form>
</div>