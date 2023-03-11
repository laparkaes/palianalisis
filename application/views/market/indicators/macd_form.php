<div class="row indicator_forms d-none" id="macd_form">
	<div class="col">
		<div class="form-group">
			<small class="text-muted"><?= $this->lang->line('text_macd') ?></small>
		</div>
		<div class="form-group">
			<label><?= $this->lang->line('period_short_long') ?></label>
			<div class="input-group">
				<input type="number" class="form-control input-default text-center" id="macd_f_period" value="12" min="2">
				<input type="number" class="form-control input-default text-center" id="macd_s_period" value="26" min="2">
			</div>
			<div class="sys_msg" id="macd_period_msg"></div>
		</div>
		<div class="form-group">
			<label><?= $this->lang->line('period_signal') ?></label>
			<input type="number" class="form-control input-default text-center" id="macd_sig_period" value="9" min="2">
			<div class="sys_msg" id="macd_sig_period_msg"></div>
		</div>
	</div>
</div>