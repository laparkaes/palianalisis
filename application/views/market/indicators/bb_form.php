<div class="row indicator_forms d-none" id="bb_form">
	<div class="col">
		<div class="form-group">
			<small class="text-muted"><?= $this->lang->line('text_bb') ?></small>
		</div>
		<div class="form-group">
			<label><?= $this->lang->line('period') ?></label>
			<input type="number" class="form-control input-default text-center" id="bb_period" value="20" min="2">
			<div class="sys_msg" id="bb_period_msg"></div>
		</div>
		<div class="form-group">
			<label><?= $this->lang->line('multi_bottom_top') ?></label>
			<div class="input-group">
				<input type="number" step="0.1" class="form-control input-default text-center" id="bb_mlower" value="2.0">
				<input type="number" step="0.1" class="form-control input-default text-center" id="bb_mupper" value="2.0">
			</div>
		</div>
		<div class="form-group">
			<label><?= $this->lang->line('avg_type') ?></label>
			<select id="bb_avgtype" class="form-control default-select text-center">
				<option value="sma"><?= $this->lang->line('sma_full') ?></option>
				<option value="ema"><?= $this->lang->line('ema_full') ?></option>
			</select>
		</div>
	</div>
</div>