<div class="row indicator_forms d-none" id="trix_form">
	<div class="col">
		<div class="form-group">
			<small class="text-muted"><?= $this->lang->line('text_trix') ?></small>
		</div>
		<div class="form-group">
			<label><?= $this->lang->line('period') ?></label>
			<input type="number" class="form-control input-default text-center" id="trix_period" value="12" min="2">
			<div class="sys_msg" id="trix_period_msg"></div>
		</div>
		<div class="form-group">
			<label><?= $this->lang->line('period_signal') ?></label>
			<input type="number" class="form-control input-default text-center" id="trix_sig_period" value="9" min="2">
			<div class="sys_msg" id="trix_sig_period_msg"></div>
		</div>
	</div>
</div>