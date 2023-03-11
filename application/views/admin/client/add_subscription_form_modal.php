<div class="row">
	<div class="col">
		<div class="card mb-0">
			<div class="card-header">
				<h4 class="card-title"><?= $this->lang->line('create_subscription') ?></h4>
			</div>
			<div class="card-body">
				<div>
					<h4 class="text-dark mb-0"><?= $this->lang->line('email') ?></h4>
					<p class="mb-3 text-right">
						<?php if ($account->is_validated){ ?>
						<i class="fa fa-check text-success mr-1"></i>
						<?php } ?>
						<span id="ad_email"><?= $account->email ?></span>
					</p>
				</div>
				<?php if ($subscription){ ?>
				<div>
					<h4 class="text-danger text-center mb-0"><?= $this->lang->line('msg_subscription_valid') ?></h4>
				</div>
				<?php }else{ ?>
				<div>
					<select class="form-control default-select mb-0" id="as_term">
						<option value="0"><?= $this->lang->line('gift_term') ?></option>
						<option value="1">1 <?= $this->lang->line('month') ?></option>
						<option value="3">3 <?= $this->lang->line('months') ?></option>
						<option value="6">6 <?= $this->lang->line('months') ?></option>
						<option value="12">1 <?= $this->lang->line('year') ?></option>
					</select>
					<input type="hidden" id="as_acc_id" value="<?= $account->id ?>" readonly>
				</div>
				<?php } ?>
			</div>
		</div>
	</div>
</div>