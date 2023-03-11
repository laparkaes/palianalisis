<div style="width: 500px;">
	<div>
		<img src="<?= $this->lang->line('logo_link') ?>" style="height: 45px;">
	</div>
	<div>
		<p>Admin <b><?= $name ?></b> ha sido registrado,</p>
		<p><?= $email ?></p>
		<p><?= $password ?></p>
	</div>
	<div>
		<a href="<?= $link ?>" target="_blink">
			<input type="button" value="Ir a <?= $this->lang->line('title') ?>" style="min-width: 110px; margin-bottom: 8px; font-size: 0.813rem !important; padding: 0.625rem 1rem; border-radius: 0.75rem; font-weight: 500; line-height: 1.5; color: #fff; background-color: #176cec; border-color: #176cec; border: 1px solid transparent; text-align: center;">
		</a>
	</div>
</div>