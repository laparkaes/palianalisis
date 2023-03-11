<div class="row indicator_forms d-none" id="env_form">
	<div class="col">
		<div class="form-group">
			<small class="text-muted"><?= $this->lang->line('text_env') ?></small>
		</div>
		<div class="form-group">
			<label><?= $this->lang->line('period_sma') ?></label>
			<input type="number" class="form-control input-default text-center" id="env_period" value="20" min="2">
			<div class="sys_msg" id="env_period_msg"></div>
		</div>
		<div class="form-group">
			<label><?= $this->lang->line('sep_per') ?></label>
			<input type="number" class="form-control input-default text-center" id="env_sep" value="15" step="0.1" min="0.1">
		</div>
	</div>
</div>