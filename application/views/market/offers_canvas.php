<?php foreach($records as $item){ ?>
<div class="col-6 col-md-4 col-lg-3 all <?= $item->type_class ?>" id="card_<?= $item->nemonico ?>">
	<div class="card">
		<div class="card-header flex-column border-0 pb-0">
			<a href="/market/company?n=<?= $item->nemonico ?>">
				<h5 class="card-title nemonico"><?= $item->nemonico ?></h5>
			</a>
			<small class="text-center name"><?= $item->companyName ?></small>
		</div>
		<div class="card-body d-flex align-items-end justify-content-center p-0">
			<div class="row d-block">
				<div class="col-12">
					<div class="pt-3 pb-3 pl-0 pr-0 text-center">
						<h4 class="m-1 close_t"><?= $item->close_t ?></h4>
					</div>
				</div>
			</div>
		</div>
		<div class="card-footer border-0 p-0">
			<div class="row">
				<div class="col-6 border-right pr-0">
					<div class="pt-3 pb-3 pl-0 pr-0 text-center">
						<h4 class="m-1 buy_t"><?= $item->buy_t ?></h4>
						<p class="text-success m-0"><?= $this->lang->line('buy') ?></p>
					</div>
				</div>
				<div class="col-6 pl-0">
					<div class="pt-3 pb-3 pl-0 pr-0 text-center">
						<h4 class="m-1 sell_t"><?= $item->sell_t ?></h4>
						<p class="text-danger m-0"><?= $this->lang->line('sell') ?></p>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php } ?>