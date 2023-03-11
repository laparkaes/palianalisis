<div class="row page-titles d-md-none mx-0">
	<div class="col">
		<div class="welcome-text">
			<h4><?= $title ?></h4>
		</div>
	</div>
</div>
<div class="row">
	<div class="col-12 col-lg-4 pb-3 d-flex align-items-center">
		<span id="bd_update_canvas" class="badge light badge-info mr-1" style="cursor: pointer;">
			<i class="fa fa-refresh" id="ic_update"></i>
		</span>
		<span id="updated_at"><?= $updated_at ?></span>
	</div>
	<div class="col-12 col-lg-4 pb-3 d-flex align-items-center">
		<label class="radio-inline mr-3 mb-0">
			<input type="radio" name="f_type" class="f_type" value="all" checked> <?= $this->lang->line('all') ?>
		</label>
		<label class="radio-inline mr-3 mb-0">
			<input type="radio" name="f_type" class="f_type" value="national"> <?= $this->lang->line('nationals') ?>
		</label>
		<label class="radio-inline mb-0">
			<input type="radio" name="f_type" class="f_type" value="foreign"> <?= $this->lang->line('foreigns') ?>
		</label>
	</div>
	<div class="col-12 col-lg-4">
		<input type="text" class="form-control mb-3" id="f_text" placeholder="Ingrese filtros aqui..." value="">
	</div>
</div>
<div class="row d-none" id="no_result">
	<div class="col-12 text-center text-danger">
		<?= $this->lang->line('no_filter_result') ?>
	</div>
</div>
<div class="row" id="offer_canvas">
	<?php $this->load->view("market/offers_canvas"); ?>
</div>