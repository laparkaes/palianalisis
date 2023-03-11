<div class="row page-titles d-md-none mx-0">
	<div class="col">
		<div class="welcome-text">
			<h4><?= $title ?></h4>
		</div>
	</div>
</div>
<?php if (!$has_subscription){ ?>
<div class="row">
	<div class="col-sm-12 col-md-8 mx-auto">
		<div class="card border border-success">
			<div class="card-body text-center">
				<h4 class="mb-2"><?= $this->lang->line('free_subscription_week') ?></h4>
				<button class="btn mt-2 btn-success btn-lg px-4" id="btn_activate_free">
					<i class="fa fa-user-plus"></i> <?= $this->lang->line('activate') ?>
				</button>
				<div class="text-center d-none" id="ic_activate_free"><i class="fa fa-spinner fa-spin"></i></div>
			</div>
		</div>
	</div>
</div>
<?php } if ($headlines){ ?>
<div class="row">
	<?php foreach($headlines as $item){ ?>
	<div class="col-sm-6 col-md-3">
		<input type="hidden" class="color" value="<?= $item["color"] ?>">
		<input type="hidden" class="nemonico" value="<?= $item["stock"]->nemonico ?>">
		<div id="chart_data_<?= str_replace("/", "", $item["stock"]->nemonico) ?>" class="chart_datas d-none">
			<?= $item["chart_datas"] ?>
		</div>
		<div class="card overflow-hidden">
			<div class="card-header d-flex align-items-start border-0 pb-0">
				<div>
					<p class="mb-2 fs-13 text-<?= $item["icon_color"] ?>">
						<i class="fa fa-caret-<?= $item["icon"] ?> scale5 mr-2"></i>
						<?= number_format(abs($item["stock"]->percentageChange), 2) ?>%
					</p>
					<a href="/market/company?n=<?= str_replace("/", "%2F", $item["stock"]->nemonico) ?>">
						<h3 class="text-black mb-0 font-w600" data-container="body" data-toggle="popover" data-trigger="hover" data-placement="top" data-content="<?= $item["stock"]->companyName ?>">
							<?= $item["stock"]->nemonico ?>
						</h3>
					</a>
				</div>
				<span class="dot" style="background-color: <?= $item["color"] ?>;" data-container="body" data-toggle="popover" data-trigger="hover" data-placement="top" data-content="<?= $item["stock"]->type ?>">
					<?= $item["type"] ?>
				</span>
			</div>
			<div class="card-body p-0">
				<div class="d-block" id="hl_chart_<?= $item["stock"]->nemonico ?>"></div>
			</div>
		</div>
	</div>
	<?php } ?>
</div>
<?php } if ($this->session->userdata('plan')->price) if ($checklist){ ?>
<div class="row">
	<div class="col">
		<div class="card">
			<div class="card-header justify-content-start border-0">
				<h4 class="card-title"><?= $this->lang->line('check_list') ?></h4>
				<span class="text-muted">
					<i class="fa fa-question-circle-o ml-2" data-container="body" data-toggle="popover" data-trigger="hover" data-placement="top" data-html="true" data-content="<?= $this->lang->line('check_list_text') ?><br/><i class='fa fa-square-o text-success mr-1'></i><?= $this->lang->line('evaluate_to_buy') ?><br/><i class='fa fa-square-o text-danger mr-1'></i><?= $this->lang->line('evaluate_to_sell') ?>" data-original-title="" title=""></i>
				</span>
			</div>
			<div class="card-body pt-0">
				<div class="row">
					<div class="col-sm-12 col-md-6">
						<div class="d-flex flex-wrap align-items-start justify-content-left">
							<?php $low = $checklist["low"]; foreach($low as $item){ ?>
							<a href="/market/company?n=<?= $item->nemonico ?>" class="mr-2">
								<div class="bd_progress border-success" data-container="body" data-toggle="popover" data-trigger="hover" data-placement="top" data-content="<?= $arr_companies[$item->nemonico] ?>" data-original-title="" title="">
									<div class="txt text-success"><?= $item->nemonico ?></div>
									<div class="progress border-top border-success rounded-0">
										<div class="progress-bar progress-animated bg-success rounded-0" style="width: <?= $item->last_year_per ?>%;"></div>
									</div>
								</div>
							</a>
							<?php } ?>
						</div>
					</div>
					<div class="col-sm-12 col-md-6">
						<div class="d-flex flex-wrap align-items-start justify-content-left">
							<?php $high = $checklist["high"]; foreach($high as $item){ ?>
							<a href="/market/company?n=<?= $item->nemonico ?>" class="mr-2">
								<div class="bd_progress border-danger" data-container="body" data-toggle="popover" data-trigger="hover" data-placement="top" data-content="<?= $arr_companies[$item->nemonico] ?>" data-original-title="" title="">
									<div class="txt text-danger"><?= $item->nemonico ?></div>
									<div class="progress border-top border-danger rounded-0">
										<div class="progress-bar progress-animated bg-danger rounded-0" style="width: <?= $item->last_year_per ?>%;"></div>
									</div>
								</div>
							</a>
							<?php } ?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php } ?>
<div class="row">
	<div class="col">
		<div class="card">
			<div class="card-header justify-content-start border-0">
				<h4 class="card-title"><?= $this->lang->line('favorites') ?></h4>
				<span id="fav_chaged_ic" class="text-warning d-none">
					<i class="fa fa-info-circle ml-2" data-container="body" data-toggle="popover" data-trigger="hover" data-placement="top" data-content="<?= $this->lang->line('favorite_changed') ?>" data-original-title="" title=""></i>
				</span>
			</div>
			<div class="card-body pt-0">
				<?php if ($favorites){ ?>
				<div id="bl_market_table" class="table-responsive">
					<table id="tb_favorites" class="table dataTable mb-0 display w-100 text-nowrap text-right">
						<thead>
							<tr>
								<th class="text-left"><?= $this->lang->line('type') ?></th>
								<th class="text-left"><?= $this->lang->line('company') ?></th>
								<th></th>
								<th><?= $this->lang->line('buy') ?></th>
								<th><?= $this->lang->line('sell') ?></th>
								<th><?= $this->lang->line('price') ?></th>
								<th><?= $this->lang->line('var')."%" ?></th>
								<th><?= $this->lang->line('num_negociated') ?></th>
								<th><?= $this->lang->line('volume') ?></th>
								<th><?= $this->lang->line('date') ?></th>
							</tr>
						</thead>
						<tbody>
							<?php foreach($favorites as $i => $item){ if ($i > 9) $row = "d-none"; else $row = ""; ?>
							<tr class="<?= $row ?>">
								<td class="text-left"><?= $item->type ?></td>
								<td class="text-left text-truncate" style="max-width: 150px;" data-container="body" data-toggle="popover" data-trigger="hover" data-placement="top" data-content="<?= $item->companyName ?>">
									<?= $item->companyName ?>
								</td>
								<td>
									<a href="/market/company?n=<?= $item->nemonico ?>" class="text-info">
										<?= $item->nemonico ?>
									</a>
									<button class="ic_favorite border-0 bg-transparent ml-1" value="<?= $item->nemonico ?>">
										<i class="fa <?= $item->ic_fav ?>"></i>
									</button>
								</td>
								<td class="text-nowrap"><?= $item->buy_t ?></td>
								<td class="text-nowrap"><?= $item->sell_t ?></td>
								<td class="text-nowrap"><?= $item->close_t ?></td>
								<td>
									<?php if ($item->percentageChange){ ?>
									<span class="text-<?= $item->color ?>">
										<?php if ($item->percentageChange > 0) $prefix = "+"; else $prefix = ""; ?>
										<?= $prefix.number_format($item->percentageChange, 2)."%" ?>
									</span>
									<?php } ?>
								</td>
								<td><?= $item->negotiated_t ?></td>
								<td><?= $item->volume_t ?></td>
								<td><?= $item->date ?></td>
							</tr>
							<?php } ?>
						</tbody>
					</table>
					<?php if ($row){ ?>
					<div class="text-center mt-3 mb-1">
						<button class="btn btn-rounded btn-primary" id="btn_favorite_view_all">
							<?= $this->lang->line('view_all') ?>
						</button>
					</div>
					<?php } ?>
				</div>
				<?php }else{ ?>
				<h5 class="text-danger"><?= $this->lang->line('no_favorite') ?></h5>
				<?php } ?>
			</div>
		</div>
	</div>
</div>
<div class="row">
	<div class="col">
		<div class="card">
			<div class="card-header border-0">
				<h4 class="card-title"><?= $this->lang->line('last_movements') ?></h4>
				<small class="text-muted" style="text-transform: initial;"><?= $updated_at ?></small>
			</div>
			<div class="card-body pt-0">
				<div id="bl_market_table" class="table-responsive">
					<table id="tb_market" class="table mb-0 display w-100 text-nowrap text-right">
						<thead>
							<tr>
								<th class="text-left"><?= $this->lang->line('type') ?></th>
								<th class="text-left"><?= $this->lang->line('company') ?></th>
								<th></th>
								<th><?= $this->lang->line('sector') ?></th>
								<th><?= $this->lang->line('buy') ?></th>
								<th><?= $this->lang->line('sell') ?></th>
								<th><?= $this->lang->line('price') ?></th>
								<th><?= $this->lang->line('var')."%" ?></th>
								<th><?= $this->lang->line('num_negociated') ?></th>
								<th><?= $this->lang->line('volume') ?></th>
							</tr>
						</thead>
						<tbody>
							<?php foreach($records as $item){ ?>
							<tr>
								<td class="text-left"><?= $item->type ?></td>
								<td class="text-left text-truncate" style="max-width: 150px;" data-container="body" data-toggle="popover" data-trigger="hover" data-placement="top" data-content="<?= $item->companyName ?>">
									<?= $item->companyName ?>
								</td>
								<td>
									<a href="/market/company?n=<?= $item->nemonico ?>" class="text-info">
										<?= $item->nemonico ?>
									</a>
									<button class="ic_favorite border-0 bg-transparent ml-1" value="<?= $item->nemonico ?>">
										<i class="fa <?= $item->ic_fav ?>"></i>
									</button>
								</td>
								<td data-container="body" data-toggle="popover" data-trigger="hover" data-placement="top" data-content="<?= $item->sectorDescription ?>">
									<?= $item->sectorCode ?>
								</td>
								<td class="text-nowrap"><?= $item->buy_t ?></td>
								<td class="text-nowrap"><?= $item->sell_t ?></td>
								<td class="text-nowrap"><?= $item->close_t ?></td>
								<td>
									<span class="text-<?= $item->color ?>">
										<?php if ($item->percentageChange > 0) $prefix = "+"; else $prefix = ""; ?>
										<?= $prefix.number_format($item->percentageChange, 2)."%" ?>
									</span>
								</td>
								<td><?= $item->negotiated_t ?></td>
								<td><?= $item->volume_t ?></td>
							</tr>
							<?php } ?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="d-none">
	<input type="hidden" id="error_no_result_movement" value="<?= $this->lang->line('error_no_result_movement') ?>">
	<input type="hidden" id="of" value="<?= $this->lang->line('of') ?>">
	<input type="hidden" id="companies" value="<?= $this->lang->line('companies') ?>">
	<input type="hidden" id="total" value="<?= $this->lang->line('total') ?>">
	<input type="hidden" id="filter" value="<?= $this->lang->line('filter') ?>">
</div>