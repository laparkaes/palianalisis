<div class="row indicator_forms d-none" id="rsi_form">
	<div class="col">
		<div class="form-group">
			<small class="text-muted"><?= $this->lang->line('text_rsi') ?></small>
		</div>
		<div class="form-group">
			<label><?= $this->lang->line('period') ?></label>
			<input type="number" class="form-control input-default text-center" id="rsi_period" value="20" min="2">
			<div class="sys_msg" id="rsi_period_msg"></div>
		</div>
	</div>
</div>