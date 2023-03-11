<div class="row page-titles mx-0">
	<div class="col-sm-6 p-md-0">
		<div class="welcome-text">
			<h4><?= $this->lang->line('plan_administration') ?></h4>
		</div>
	</div>
	<div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
		<ol class="breadcrumb">
			<li class="breadcrumb-item"><span><?= $title1 ?></span></li>
			<li class="breadcrumb-item active"><span><?= $title2 ?></span></li>
		</ol>
	</div>
</div>
<div class="row">
	<div class="col-lg-12">
		<div class="card">
			<div class="card-header">
				<h4 class="card-title"><?= $this->lang->line('plans') ?></h4>
			</div>
			<div class="card-body">
				<div class="table-responsive">
					<table class="table table-responsive-md text-nowrap border-bottom text-right">
						<thead>
							<tr class="text-uppercase">
								<th class="text-left"><strong>#<strong></th>
								<th><strong><?= $this->lang->line('mercadopago_id') ?><strong></th>
								<th><strong><?= $this->lang->line('description') ?></strong></th>
								<th style="width: 300px;"><strong><?= $this->lang->line('monthly_payment') ?></strong></th>
								<th style="width: 50px;"></th>
							</tr>
						</thead>
						<tbody id="plan_list">
							<?php echo form_open('#', array("id" => "add_plan")); ?>
							<tr class="table-info">
								<td class="text-left"></td>
								<td>
									<a href="https://www.mercadopago.com.pe" target="_blank">
										<img style="height: 25px;" src="/images/logo_mercadopago.png">
									</a>
								</td>
								<td><input type="input" class="form-control" name="description" style="height: 40px; border: 1px solid #c8c8c8; font-size: 1rem; float: right;"></td>
								<td>
									<div class="input-group">
										<div class="input-group-prepend">
											<div class="input-group-text"><?= $this->lang->line('sol_symbol') ?></div>
										</div>
										<input type="text" class="form-control" name="price" style="height: 40px; border: 1px solid #c8c8c8; font-size: 1rem; float: right;">
									</div>
								</td>
								<td>
									<button type="submit" class="btn btn-success light sharp">
										<i class="fa fa-plus"></i>
									</button>
								</td>
							</tr>
							<?php echo form_close(); ?>
							<?php $this->load->view("admin/plan/rows"); ?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="row">
	<div class="col-lg-7">
		<div class="card">
			<div class="card-header">
				<h4 class="card-title"><?= $this->lang->line('services') ?></h4>
				<small class="text-danger"><?= $this->lang->line('page_refresh_msg') ?></small>
			</div>
			<div class="card-body">
				<table class="table border-bottom w-100">
					<thead>
						<tr class="text-uppercase">
							<th class="text-left">#</th>
							<th><strong><?= $this->lang->line('description') ?></strong></th>
							<th style="width: 50px;"></th>
						</tr>
					</thead>
					<tbody id="service_list">
						<?php $this->load->view("admin/plan/srv_rows"); ?>
					</tbody>
				</table>
				<?php echo form_open('#', array("id" => "add_service")); ?>
				<div class="form-row mt-1">
					<div class="col d-flex justify-content-center">
						<input type="input" class="form-control" name="description" placeholder="<?= $this->lang->line('description') ?>">
						<button type="submit" class="btn btn-success light sharp ml-1">
							<i class="fa fa-plus"></i>
						</button>
					</div>
				</div>
				<?php echo form_close(); ?>
			</div>
		</div>
	</div>
	<div class="col-lg-5">
		<div class="card">
			<div class="card-header">
				<h4 class="card-title"><?= $this->lang->line('services_plan') ?></h4>
			</div>
			<div class="card-body">
				<div class="table-responsive">
					<table class="table border-bottom text-right">
						<thead>
							<tr>
								<?php foreach($plans as $m){ ?>
								<th><strong><?= $m->description ?></strong></th>
								<?php } ?>
							</tr>
						</thead>
						<tbody id="service_plan_list">
							<?php $this->load->view("admin/plan/srv_match_rows"); ?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="row">
	<div class="col-lg-7">
		<div class="card">
			<div class="card-header">
				<h4 class="card-title"><?= $this->lang->line('indicators') ?></h4>
			</div>
			<div class="card-body">
				<div class="table-responsive">
					<table class="table text-truncate border-bottom">
						<thead>
							<tr class="text-uppercase">
								<th class="text-left">#</th>
								<th style="max-width: 400px"><strong><?= $this->lang->line('description') ?></strong></th>
								<th><strong><?= $this->lang->line('code') ?></strong></th>
								<th style="width: 50px;"></th>
							</tr>
						</thead>
						<tbody id="indicator_list">
							<?php $this->load->view("admin/plan/ind_rows"); ?>
						</tbody>
					</table>
				</div>
				<?php echo form_open('#', array("id" => "add_indicator")); ?>
				<div class="form-row mt-1">
					<div class="col d-flex justify-content-center">
						<input type="input" class="form-control" name="description" placeholder="<?= $this->lang->line('description') ?>">
						<input type="input" class="form-control ml-1" name="code" placeholder="<?= $this->lang->line('code') ?>">
						<button type="submit" class="btn btn-success light sharp ml-1">
							<i class="fa fa-plus"></i>
						</button>
					</div>
				</div>
				<?php echo form_close(); ?>
			</div>
		</div>
	</div>
	<div class="col-lg-5">
		<div class="card">
			<div class="card-header">
				<h4 class="card-title"><?= $this->lang->line('indicators_plan') ?></h4>
			</div>
			<div class="card-body">
				<div class="table-responsive">
					<table class="table border-bottom text-right">
						<thead>
							<tr>
								<?php foreach($plans as $m){ ?>
								<th><strong><?= $m->description ?></strong></th>
								<?php } ?>
							</tr>
						</thead>
						<tbody id="indicator_plan_list">
							<?php $this->load->view("admin/plan/ind_match_rows"); ?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="d-none">
	<input type="hidden" id="confirm_add_plan" value="<?= $this->lang->line('confirm_add_plan') ?>">
	<input type="hidden" id="confirm_remove_plan" value="<?= $this->lang->line('confirm_remove_plan') ?>">
	<input type="hidden" id="confirm_add_service" value="<?= $this->lang->line('confirm_add_service') ?>">
	<input type="hidden" id="confirm_remove_service" value="<?= $this->lang->line('confirm_remove_service') ?>">
	<input type="hidden" id="confirm_add_indicator" value="<?= $this->lang->line('confirm_add_indicator') ?>">
	<input type="hidden" id="confirm_remove_indicator" value="<?= $this->lang->line('confirm_remove_service') ?>">
</div>
<script src="/utility/js/init/admin/plan_init.js"></script>