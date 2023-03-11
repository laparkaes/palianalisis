<div class="row indicator_forms d-none" id="pbsar_form">
	<div class="col">
		<div class="form-group">
			<small class="text-muted"><?= $this->lang->line('text_pbsar') ?></small>
		</div>
		<div class="form-group">
			<label><?= $this->lang->line('acceleration') ?></label>
			<input type="number" step="0.01" class="form-control input-default text-center" id="pbsar_acceleration" value="0.02",  min="0.01">
			<div class="sys_msg" id="pbsar_acceleration_msg"></div>
		</div>
		<div class="form-group">
			<label><?= $this->lang->line('maximum') ?></label>
			<input type="number" step="0.1" class="form-control input-default text-center" id="pbsar_maximum" value="0.2" min="0.1">
			<div class="sys_msg" id="pbsar_maximum_msg"></div>
		</div>
	</div>
</div>