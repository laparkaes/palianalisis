<div class="row page-titles d-md-none mx-0">
	<div class="col">
		<div class="welcome-text">
			<h4><?= $title ?></h4>
		</div>
	</div>
</div>
<div class="row">
	<div class="col">
		<h5 class="mb-3"><?= $this->lang->line('main_msg') ?></h5>
	</div>
</div>
<div class="row">
	<?php foreach($plans as $item){ ?>
	<div class="col-sm-12 col-lg-4">
		<div class="select_box mb-3">
			<div class="box mbs op bg-white <?= $item->featured ?>">
				<h3 class="pt-2"><?= $item->description ?></h3>
				<h4 class="mb-0">
					<sup><?= $item->price_currency ?></sup>
					<?= $item->price ?>
					<span><?= $item->price_sub ?></span>
				</h4>
				<div class="my-4">
					<a href="<?= $item->payment ?>" class="<?= $item->payment_class ?> w-50"><?= $item->payment_btn ?></a>
				</div>
				<ul>
					<li><strong class="text-success"><?= $item->indicator_qty ?></strong> <?= $this->lang->line('statistical_indicators') ?></li>
					<?php $services = $item->services; foreach($services as $s){ ?>
					<li class="<?= $s["class"] ?>"><?= $s["desc"] ?></li>
					<?php } ?>
				</ul>
			</div>
		</div>
	</div>
	<?php } ?>
</div>