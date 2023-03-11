<div class="row indicator_forms d-none" id="sto_form">
	<div class="col">
		<div class="form-group">
			<small class="text-muted"><?= $this->lang->line('text_sto') ?></small>
		</div>
		<div class="form-group">
			<label><?= $this->lang->line('period_short_long_k') ?></label>
			<div class="input-group">
				<input type="number" class="form-control input-default text-center" id="sto_fk_period" value="6" min="2">
				<input type="number" class="form-control input-default text-center" id="sto_sk_period" value="10" min="2">
			</div>
			<div class="sys_msg" id="sto_f_period_msg"></div>
		</div>
		<div class="form-group">
			<label><?= $this->lang->line('period_d') ?></label>
			<input type="number" class="form-control input-default text-center" id="sto_d_period" value="6" min="2">
			<div class="sys_msg" id="sto_d_period_msg"></div>
		</div>
		<div class="form-group">
			<label><?= $this->lang->line('avg_type_d_k') ?></label>
			<div class="input-group">
				<select id="sto_k_avgtype" class="form-control default-select text-center">
					<option value="sma"><?= $this->lang->line('sma_full') ?></option>
					<option value="ema"><?= $this->lang->line('ema_full') ?></option>
				</select>
				<select id="sto_d_avgtype" class="form-control default-select text-center">
					<option value="sma"><?= $this->lang->line('sma_full') ?></option>
					<option value="ema"><?= $this->lang->line('ema_full') ?></option>
				</select>
			</div>
		</div>
	</div>
</div>