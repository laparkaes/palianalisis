<div class="row page-titles d-md-none mx-0">
	<div class="col">
		<div class="welcome-text">
			<h4><?= $title ?></h4>
		</div>
	</div>
</div>
<div class="row">
	<div class="col-sm-12 col-lg-8">
		<div class="card">
			<div class="card-body">
				<div class="row select_box d-flex justify-content-center">
					<?php foreach($plans as $item){ ?>
					<div class="col-sm-12 col-md-6 mb-2">
						<div class="box mbs op <?= $item->featured ?>" id="mbs_<?= $item->id ?>">
							<h3 class="pt-2"><?= $item->description ?></h3>
							<h4>
								<sup><?= $this->lang->line('sol_symbol') ?></sup>
								<?= $item->price ?>
								<span> / <?= $this->lang->line('month') ?></span>
							</h4>
							<ul class="pb-2">
								<li><strong class="text-success"><?= $item->indicator_qty ?></strong> <?= $this->lang->line('statistical_indicators') ?></li>
								<?php $services = $item->services; foreach($services as $s){ ?>
								<li class="<?= $s["class"] ?>"><?= $s["desc"] ?></li>
								<?php } ?>
							</ul>
						</div>
					</div>
					<?php } ?>
				</div>
			</div>
		</div>
	</div>
	<?php $premium_plan = $plans[count($plans)-1]; ?>
	<div class="col-sm-12 col-lg-4">
		<div class="card">
			<div class="card-body">
				<ul class="list-group mb-3">
					<li class="list-group-item d-flex justify-content-between lh-condensed">
						<div>
							<h6 class="my-0"><?= $this->lang->line('access')." ".$premium_plan->description ?></h6>
							<small class="text-muted"><?= $this->lang->line('monthly_payment') ?></small>
						</div>
						<span class="text-muted"><?= $this->lang->line('sol_symbol')." ".$premium_plan->price ?></span>
					</li>
					<li class="list-group-item d-flex justify-content-between">
						<span><?= $this->lang->line('total_pen') ?></span>
						<strong><?= $this->lang->line('sol_symbol')." ".$premium_plan->price ?></strong>
					</li>
				</ul>
				<a href="<?= $btn_detail["link"] ?>" class="btn btn-primary btn-lg w-100">
					<?= $btn_detail["text"] ?>
				</a>
				<?php if ($btn_detail["msg"]){ ?>
				<small class="text-danger mt-1"><?= $btn_detail["msg"] ?></small>
				<?php }else{ ?>
				<a href="<?= $weekly_payment_link ?>" class="btn tp-btn-light btn-primary w-100 mt-3">
					<?= $this->lang->line('pay_week_btn') ?>
				</a>
				<?php } ?>
			</div>
		</div>
	</div>
</div>