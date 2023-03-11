<?php if ($accounts){ ?>
<script>
$(document).ready(function() {
	$(".btn_acc_detail").on('click',(function(e) {load_account_detail(this);}));
	$(".btn_add_subscription").on('click',(function(e) {load_account_subscription(this);}));
});
</script>
<div class="table-responsive mt-3">
	<table class="table table-responsive-md border-bottom">
		<thead>
			<tr class="text-uppercase">
				<th><strong>#</strong></th>
				<th><strong><?= $this->lang->line('from') ?></strong></th>
				<th><strong><?= $this->lang->line('email') ?></strong></th>
				<th><strong><?= $this->lang->line('name') ?></strong></th>
				<th><strong><?= $this->lang->line('plan') ?></strong></th>
				<th></th>
			</tr>
		</thead>
		<tbody>
			<?php foreach($accounts as $i => $item){ ?>
			<tr>
				<td><strong><?= ($i + 1) ?></strong></td>
				<td><?= $item->registed_at ?></td>
				<td>
					<?= $item->email ?>
					<?php if ($item->is_validated){ ?>
					<i class="fa fa-check text-success ml-1"></i>
					<?php } ?>
				</td>
				<td><?= $item->name ?></td>
				<td><span class="text-<?= $item->plan->color ?>"><?= $item->plan->description ?></span></td>
				<td>
					<div class="dropdown">
						<button type="button" class="btn btn-info light sharp" data-toggle="dropdown">
							<i class="fa fa-ellipsis-h" aria-hidden="true"></i>
						</button>
						<div class="dropdown-menu">
							<input type="hidden" class="aid" value="<?= $item->id ?>">
							<button class="dropdown-item btn_acc_detail" data-toggle="modal" data-target="#modal_account_detail"><?= $this->lang->line('detail') ?></button>
							<button class="dropdown-item btn_add_subscription" data-toggle="modal" data-target="#modal_add_subscription"><?= $this->lang->line('subscription') ?></button>
						</div>
					</div>
				</td>
			</tr>
			<?php } ?>
		</tbody>
	</table>
</div>
<?php }else{ ?>
<p><?= $this->lang->line('msg_no_result') ?></p>
<?php } ?>