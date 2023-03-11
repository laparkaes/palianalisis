<div class="row page-titles mx-0">
	<div class="col-sm-6 p-md-0">
		<div class="welcome-text">
			<h4><?= $this->lang->line('company_administration') ?></h4>
		</div>
	</div>
	<div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
		<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="/admin/dashboard"><?= $title1 ?></a></li>
			<li class="breadcrumb-item active"><a href="/admin/company"><?= $title2 ?></a></li>
		</ol>
	</div>
</div>
<div class="row">
	<div class="col-lg-7">
		<div class="card">
			<div class="card-header border-0 pb-0">
				<h4 class="card-title"><?= $this->lang->line('records') ?></h4>
			</div>
			<div class="card-body">
				<h5><?= $this->lang->line('partial_load') ?></h5>
				<div class="mb-3" id="bl_partial_update">
					<?php $this->load->view("admin/company/partial"); ?>
				</div>
				<h5><?= $this->lang->line('full_load') ?></h5>
				<div>
					<?php foreach($arr_new as $nemonico){ ?>
					<button class="btn btn-secondary mr-1 mb-1 btn_initial"><?= $nemonico ?></button>
					<?php } ?>
					<span id="initial_blank" class="text-danger d-none"><?= $this->lang->line('msg_no_update') ?></span>
				</div>
			</div>
		</div>
	</div>
	<div class="col-lg-5">
		<div class="card">
			<div class="card-header border-0 pb-0">
				<h4 class="card-title"><?= $this->lang->line('companies') ?></h4>
			</div>
			<div class="card-body">
				<a href="/admin/company/update_national_companies" class="btn btn-info w-100" target="_blank"><?= $this->lang->line('nationals') ?></a>
				<a href="/admin/company/update_foreign_companies" class="btn btn-dark w-100 mt-3" target="_blank"><?= $this->lang->line('foreign') ?></a>
			</div>
		</div>
	</div>
</div>
<script src="/utility/js/init/admin/company_init.js"></script>