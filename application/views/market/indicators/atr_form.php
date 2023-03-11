<div class="row indicator_forms d-none" id="atr_form">
	<div class="col">
		<div class="form-group">
			<small class="text-muted"><?= $this->lang->line('text_atr') ?></small>
		</div>
		<div class="form-group">
			<label><?= $this->lang->line('period') ?></label>
			<input type="number" class="form-control input-default text-center" id="atr_period" value="14" min="2">
			<div class="sys_msg" id="atr_period_msg"></div>
		</div>
	</div>
</div>