<div class="row indicator_forms d-none" id="adx_form">
	<div class="col">
		<div class="form-group">
			<small class="text-muted"><?= $this->lang->line('text_adx') ?></small>
		</div>
		<div class="form-group">
			<label><?= $this->lang->line('period') ?></label>
			<input type="number" class="form-control input-default text-center" id="adx_period" value="14" min="2">
			<div class="sys_msg" id="adx_period_msg"></div>
		</div>
	</div>
</div>