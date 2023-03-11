<div class="row indicator_forms d-none" id="mar_form">
	<div class="col">
		<div class="form-group">
			<small class="text-muted"><?= $this->lang->line('text_mar') ?></small>
		</div>
		<div class="form-group">
			<label><?= $this->lang->line('avg_type') ?></label>
			<select id="mar_avgtype" class="form-control default-select text-center">
				<option value="sma"><?= $this->lang->line('sma_full') ?></option>
				<option value="ema"><?= $this->lang->line('ema_full') ?></option>
			</select>
		</div>
	</div>
</div>