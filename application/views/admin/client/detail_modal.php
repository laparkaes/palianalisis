<?php if ($subscription) $lg_div = 6; else $lg_div = 12; ?>
<div class="row">
	<div class="col-lg-<?= $lg_div ?> col-sm-12">
		<div class="card">
			<div class="card-header">
				<h4 class="card-title"><?= $this->lang->line('account') ?></h4>
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
				<div>
					<h4 class="text-dark mb-0"><?= $this->lang->line('name') ?></h4>
					<p class="mb-3 text-right"><?= $account->name ?></p>
				</div>
				<div>
					<h4 class="text-dark mb-0"><?= $this->lang->line('from') ?></h4>
					<p class="mb-3 text-right"><?= $account->registed_at ?></p>
				</div>
				<div>
					<h4 class="text-dark mb-0"><?= $this->lang->line('last_access') ?></h4>
					<p class="mb-0 text-right"><?= $account->last_logged_at ?></p>
				</div>
			</div>
		</div>
	</div>
	<?php if ($subscription){ ?>
	<div class="col-lg-<?= $lg_div ?> col-sm-12">
		<div class="card">
			<div class="card-header">
				<h4 class="card-title"><?= $this->lang->line('subscription') ?></h4>
			</div>
			<div class="card-body">
				<div>
					<h4 class="text-dark mb-0"><?= $this->lang->line('type') ?></h4>
					<p class="mb-3 text-right">
						<?php if ($subscription->mp) echo $this->lang->line('paid'); else echo $this->lang->line('gift'); ?>
					</p>
				</div>
				<div>
					<h4 class="text-dark mb-0"><?= $this->lang->line('from') ?></h4>
					<p class="mb-3 text-right"><?= $subscription->registed_at ?></p>
				</div>
				<?php if ($subscription->mp){ ?>
				<div>
					<h4 class="text-dark mb-0"><?= $this->lang->line('charge_qty') ?></h4>
					<p class="mb-3 text-right"><?= number_format($subscription->mp->summarized->charged_quantity) ?></p>
				</div>
				<div>
					<h4 class="text-dark mb-0"><?= $this->lang->line('recent_payment') ?></h4>
					<p class="mb-3 text-right"><?= mdate("%Y-%m-%d %H:%i:%s", strtotime($subscription->mp->summarized->last_charged_date)) ?></p>
				</div>
				<div>
					<h4 class="text-dark mb-0"><?= $this->lang->line('next_payment') ?></h4>
					<p class="mb-0 text-right"><?= mdate("%Y-%m-%d %H:%i:%s", strtotime($subscription->mp->next_payment_date)) ?></p>
				</div>
				<?php }else{ ?>
				<div>
					<h4 class="text-dark mb-0"><?= $this->lang->line('to') ?></h4>
					<p class="mb-0 text-right"><?= $subscription->valid_to ?></p>
				</div>
				<?php } ?>
			</div>
		</div>
	</div>
	<?php } ?>
</div>