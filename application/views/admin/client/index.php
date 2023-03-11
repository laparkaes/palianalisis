<div class="row page-titles mx-0">
	<div class="col-sm-6 p-md-0">
		<div class="welcome-text">
			<h4><?= $this->lang->line('clients') ?></h4>
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
	<div class="col-xl-3 col-xxl-3 col-lg-3 col-sm-6">
		<div class="widget-stat card bg-info">
			<div class="card-body p-4">
				<div class="media">
					<span class="mr-3">
						<i class="flaticon-381-folder-9"></i>
					</span>
					<div class="media-body text-white text-right">
						<p class="mb-1"><?= $free_plan->description ?></p>
						<h3 class="text-white"><?= number_format($free_plan->qty) ?></h3>
						<span class="fs-14 op7"><?= $free_plan->per ?></span>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="col-xl-3 col-xxl-3 col-lg-3 col-sm-6">
		<div class="widget-stat card bg-secondary">
			<div class="card-body p-4">
				<div class="media">
					<span class="mr-3">
						<i class="flaticon-381-folder-8"></i>
					</span>
					<div class="media-body text-white text-right">
						<p class="mb-1"><?= $this->lang->line('gift') ?></p>
						<h3 class="text-white"><?= $premium_plan->qty_gift ?></h3>
						<span class="fs-14 op7"><?= $premium_plan->per_gift ?></span>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="col-xl-3 col-xxl-3 col-lg-3 col-sm-6">
		<div class="widget-stat card bg-success">
			<div class="card-body p-4">
				<div class="media">
					<span class="mr-3">
						<i class="flaticon-381-folder-7"></i>
					</span>
					<div class="media-body text-white text-right">
						<p class="mb-1"><?= $premium_plan->description ?></p>
						<h3 class="text-white"><?= $premium_plan->qty_paid ?></h3>
						<span class="fs-14 op7"><?= $premium_plan->per_paid ?></span>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="col-xl-3 col-xxl-3 col-lg-3 col-sm-6">
		<div class="widget-stat card bg-warning">
			<div class="card-body p-4">
				<div class="media">
					<span class="mr-3">
						<i class="flaticon-381-user-7"></i>
					</span>
					<div class="media-body text-white text-right">
						<p class="mb-1"><?= $this->lang->line('total') ?></p>
						<h3 class="text-white"><?= number_format($free_plan->qty + $premium_plan->qty_gift + $premium_plan->qty_paid) ?></h3>
						<span class="fs-14 op7">100.00%</span>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="row">
	<div class="col-lg-12">
		<div class="card">
			<div class="card-header border-0 pb-0">
				<h4 class="card-title"><?= $this->lang->line('accounts') ?></h4>
			</div>
			<div class="card-body">
				<div class="row">
					<div class="col-sm-4">
						<span id="acc_filtered_qty"><?= number_format(count($accounts)) ?></span>
						<span><?= $account_qty_text ?></span>
					</div>
					<div class="col-sm-8">
						<?php echo form_open('#', array("id" => "account_filter_form", "class" => "w-100")); ?>
						<div class="input-group d-flex justify-content-end">
							<input type="text" class="form-control" style="max-width: 300px;" name="acc_filter">
							<div class="input-group-append">
								<button class="btn btn-primary" type="submit"><?= $this->lang->line('search') ?></button>
							</div>
						</div>
						<?php echo form_close(); ?>
					</div>
				</div>
				<div id="bl_account_table"><?php $this->load->view('admin/client/account_table'); ?></div>
			</div>
			<div class="card-footer text-danger border-0 pt-0">
				<span><?= $this->lang->line('msg_max_account_show') ?></span>
			</div>
		</div>
	</div>
</div>
<div class="modal fade show" id="modal_account_detail" aria-modal="true">
	<div class="modal-dialog modal-lg modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-body pb-0" id="detail_modal_body"></div>
			<div class="modal-footer">
				<button type="button" class="btn tp-btn btn-primary" data-dismiss="modal"><?= $this->lang->line('close') ?></button>
			</div>
		</div>
	</div>
</div>
<div class="modal fade show" id="modal_add_subscription" aria-modal="true">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-body pb-0" id="subscription_modal_body"></div>
			<div class="modal-footer">
				<button type="button" class="btn tp-btn btn-primary" data-dismiss="modal"><?= $this->lang->line('close') ?></button>
				<button type="button" class="btn btn-primary" id="btn_asc"><?= $this->lang->line('confirm') ?></button>
			</div>
		</div>
	</div>
</div>
<script src="/utility/js/init/admin/client_init.js"></script>