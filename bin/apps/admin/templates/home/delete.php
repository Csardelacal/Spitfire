
<div class="confirm">
	<div class="caption">
		Are you sure you want to delete "<strong><?= $record ?></strong>"?
	</div>
	
	<div class="form-buttons">
		<a class="button primary" href="<?= new URL($this->app, 'lst', $bean->getName()) ?>">Cancel</a>
		<a class="button warning" href="<?= new URL($this->app, 'delete', $bean->getName(), implode(':', $record->getPrimaryData()), Array('confirmed' => 1)) ?>">Delete</a>
	</div>
</div>