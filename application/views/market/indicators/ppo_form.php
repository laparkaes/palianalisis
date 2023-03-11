<div class="row indicator_forms d-none" id="ppo_form">
	<div class="col">
		<div class="form-group">
			<small class="text-muted"><?= $this->lang->line('text_ppo') ?></small>
		</div>
		<div class="form-group">
			<label><?= $this->lang->line('period_short_long') ?></label>
			<div class="input-group">
				<input type="number" class="form-control input-default text-center" id="ppo_f_period" value="9" min="2">
				<input type="number" class="form-control input-default text-center" id="ppo_s_period" value="20" min="2">
			</div>
			<div class="sys_msg" id="ppo_period_msg"></div>
		</div>
		<div class="form-group">
			<label><?= $this->lang->line('avg_type') ?></label>
			<select id="ppo_avgtype" class="form-control default-select text-center">
				<option value="sma"><?= $this->lang->line('sma_full') ?></option>
				<option value="ema"><?= $this->lang->line('ema_full') ?></option>
			</select>
		</div>
	</div>
</div>