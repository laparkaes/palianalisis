<div class="row page-titles d-md-none mx-0">
	<div class="col">
		<div class="welcome-text">
			<h4><?= $title ?></h4>
		</div>
	</div>
</div>
<div class="row">
	<div class="col-sm-12 col-md-6">
		<div class="card">
			<div class="card-body">
				<div class="row">
					<div class="col-lg-12">
						<h4 class="text-primary mb-3"><?= $this->lang->line('user_data') ?></h4>
						<?php echo form_open('#', array("id" => "update_account_form")); ?>
						<div class="form-group">
							<label><?= $this->lang->line('email') ?></label>
							<div class="input-group mb-3 input-success">
								<input type="text" class="form-control" value="<?= $account->email ?>" readonly>
								<div class="input-group-append">
									<span class="input-group-text"><i class="fa fa-check"></i></span>
								</div>
							</div>
						</div>
						<div class="form-group">
							<label><?= $this->lang->line('name') ?></label>
							<input type="text" class="form-control" name="name" value="<?= $account->name ?>">
						</div>
						<div id="account_data_btns" class="text-right">
							<button class="btn tp-btn btn-muted" type="button" id="btn_remove_account"><?= $this->lang->line('remove_account') ?></button>
							<button class="btn btn-primary" type="submit"><?= $this->lang->line('save') ?></button>
						</div>
						<div class="text-center py-2 d-none" id="ic_loading_account_data">
							<i class="fa fa-spinner fa-spin"></i>
						</div>
						<?php echo form_close(); ?>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="col-sm-12 col-md-6">
		<div class="card">
			<div class="card-body">
				<div class="row">
					<div class="col-lg-12">
						<h4 class="text-primary mb-3"><?= $this->lang->line('change_password') ?></h4>
						<?php echo form_open('#', array("id" => "update_pass_form")); ?>
						<div class="form-group">
							<label><?= $this->lang->line('current') ?></label>
							<input type="password" class="form-control" name="password">
						</div>
						<div class="form-row">
							<div class="form-group col-md-6">
								<label><?= $this->lang->line('new') ?></label>
								<input type="password" class="form-control" name="password_new">
							</div>
							<div class="form-group col-md-6">
								<label><?= $this->lang->line('confirmation') ?></label>
								<input type="password" class="form-control" name="password_confirm">
							</div>
						</div>
						<div class="text-right">
							<button type="submit" class="btn btn-primary"><?= $this->lang->line('save') ?></button>
						</div>
						<?php echo form_close(); ?>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php if ($subscription){ ?>
	<div class="col">
		<div class="card">
			<div class="card-body">
				<div class="row">
					<div class="col">
						<h4 class="text-primary mb-3"><?= $this->lang->line('subscription') ?></h4>
					</div>
				</div>
				<div class="row">
					<div class="col-sm-12 col-md-6 col-lg-3">
						<div class="mb-3">
							<p class="mb-1"><?= $this->lang->line('plan') ?></p>
							<h4 class="text-black"><?= $subscription->plan->description ?></h4>
						</div>
					</div>
					<div class="col-sm-12 col-md-6 col-lg-3">
						<div class="mb-3">
							<p class="mb-1"><?= $this->lang->line('monthly_payment') ?></p>
							<h4 class="text-black">
								<?php if ($subscription->is_paid) echo $this->lang->line('sol_symbol')." ".number_format($subscription->plan->price, 2); else echo "-"; ?>
							</h4>
						</div>
					</div>
					<div class="col-sm-12 col-md-6 col-lg-3">
						<div class="mb-3">
							<p class="mb-1">
								<?= $subscription->limit_text ?>
							</p>
							<h4 class="text-black"><?= $subscription->to ?></h4>
						</div>
					</div>
					<div class="col-sm-12 col-md-6 col-lg-3">
						<div class="mb-3">
							<p class="mb-1"><?= $this->lang->line('status') ?></p>
							<h4 class="text-<?= $subscription->status_color ?>"><?= $subscription->status ?></h4>
						</div>
					</div>
					<div class="col-12 text-right">
						<?php if (in_array("cancel", $actions)){ ?>
						<button type="button" class="btn tp-btn btn-muted" id="btn_cancel_subs">
							<?= $this->lang->line('cancel') ?>
						</button>
						<?php } if (in_array("pay", $actions)){ ?>
						<a type="button" class="btn btn-success" href="<?= $subscription->init_point ?>">
							<?= $this->lang->line('make_payment') ?>
						</a>
						<?php } ?>
						<div class="text-center py-2 d-none" id="ic_loading_subs"><i class="fa fa-spinner fa-spin"></i></div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php } ?>
</div>
<div class="d-none">
	<?php if ($subscription){ ?>
	<input type="hidden" id="warning_pause_subs" value="<?= $this->lang->line('msg_subscription_valid_until')." ".$subscription->to."<br/>".$this->lang->line('warning_subscription_deactivate') ?>">
	<input type="hidden" id="warning_cancel_subs" value="<?= $this->lang->line('msg_subscription_valid_until')." ".$subscription->to."<br/>".$this->lang->line('warning_subscription_cancel') ?>">
	<?php } ?>
	<input type="hidden" id="warning_reactivate_subs" value="<?= $this->lang->line('warning_subscription_activate') ?>">
	<input type="hidden" id="warning_session_end" value="<?= $this->lang->line('warning_session_end'); ?>">
	<input type="hidden" id="warning_remove_account" value="<?= $this->lang->line('warning_remove_account'); ?>">
</div>