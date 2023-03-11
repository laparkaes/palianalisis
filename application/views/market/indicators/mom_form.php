<div class="row indicator_forms d-none" id="mom_form">
	<div class="col">
		<div class="form-group">
			<small class="text-muted"><?= $this->lang->line('text_mom') ?></small>
		</div>
		<div class="form-group">
			<label><?= $this->lang->line('period') ?></label>
			<input type="number" class="form-control input-default text-center" id="mom_period" value="10">
			<div class="sys_msg" id="mom_period_msg"></div>
		</div>
		<div class="form-group">
			<label><?= $this->lang->line('period_signal') ?></label>
			<input type="number" class="form-control input-default text-center" id="mom_sig_period" value="9">
			<div class="sys_msg" id="mom_sig_period_msg"></div>
		</div>
	</div>
</div>