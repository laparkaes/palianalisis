<div class="row page-titles d-md-none mx-0">
	<div class="col">
		<div class="welcome-text">
			<h4><?= $title ?></h4>
		</div>
	</div>
</div>
<div class="row">
	<div class="col">
		<div class="text-right mb-3">
			<button class="ic_favorite border-0 bg-transparent ml-1" value="<?= $company->nemonico ?>">
				<i class="fa <?= $company->ic_fav ?>"></i>
			</button>
			<?php
			if ($company->is_national) $tx1 = $this->lang->line('national'); 
			else $tx1 = $this->lang->line('foreign');
			if ($company->sectorCode) $tx2 = $company->sectorDescription;
			else $tx2 = $company->broker;
			?>
			<span class="badge badge-sm light badge-primary mb-1"><?= $company->nemonico ?></span>
			<span class="badge badge-sm light badge-secondary mb-1"><?= $tx1 ?></span>
			<span class="badge badge-sm light badge-secondary mb-1"><?= $tx2 ?></span>
			<span class="badge badge-sm light badge-rounded badge-outline-dark mb-1"><?= $updated_at ?></span>
		</div>
	</div>
</div>
<div class="row">
	<div class="col">
		<div class="card">
			<div class="card-body pt-3 pb-1">
				<div class="row sp20">
					<div class="col-6 col-lg-2 info-group">
						<p class="fs-14 mb-1"><?= $this->lang->line('price') ?></p>
						<h2 class="fs-20 font-w600 text-black"><?= $last->price_t ?></h2>
					</div>
					<div class="col-6 col-lg-2 info-group">
						<p class="fs-14 mb-1"><?= $this->lang->line('variation') ?></p>
						<h3 class="fs-20 font-w600 text-<?= $last->var_color ?>"><?= $last->var_t ?>
							<?php if ($last->var_ic){ ?>
							<i class="fa fa-<?= $last->var_ic ?>"></i>
							<?php } ?>
						</h3>
					</div>
					<div class="col-6 col-lg-2 info-group">
						<p class="fs-14 mb-1"><?= $this->lang->line('volume') ?></p>
						<h3 class="fs-20 font-w600 text-black"><?= $last->amountNegotiated_t ?></h3>
					</div>
					<div class="col-6 col-lg-2 info-group">
						<p class="fs-14 mb-1"><?= $this->lang->line('num_negociated') ?></p>
						<h3 class="fs-20 font-w600 text-black"><?= $last->quantityNegotiated_t ?></h3>
					</div>
					<div class="col-6 col-lg-2 info-group">
						<p class="fs-14 text-success mb-1"><?= $this->lang->line('buy_upper') ?></p>
						<h3 class="fs-20 font-w600 text-black"><?= $last->buy_t ?></h3>
					</div>
					<div class="col-6 col-lg-2 info-group">
						<p class="fs-14 text-danger mb-1"><?= $this->lang->line('sell_upper') ?></p>
						<h3 class="fs-20 font-w600 text-black"><?= $last->sell_t ?></h3>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php $dates = $opens = $highs = $lows = $closes = $volumes = array(); if ($records){ ?>
<div class="row">
	<div class="col-sm-12 col-md-7 col-lg-8 col-xxxl-10">
		<div class="card">
			<div class="card-header d-flex justify-content-right pb-0 border-0">
				<div class="text-left">
					<i class="fa fa-spinner fa-spin d-none" id="ic_price_chart_loading"></i>
				</div>
				<div id="btns_daterange" class="btn-group btn-group-sm mb-1">
					<?php foreach($btns_daterange as $item){ ?>
					<button type="button" class="btn btn-outline-primary op py-1 px-2" value="<?= $item[1] ?>" <?= $item[2] ?>>
						<?= $item[0] ?>
					</button>
					<?php } ?>
				</div>
			</div>
			<div id="cht_block" class="card-body py-0">
				<div class="sys_msg text-right" id="result_chart_msg"></div>
				<div id="cht_price"></div>
				<div id="cht_indicator"></div>
				<div class="text-center my-3 d-none ic_indicator_chart_loading"><i class="fa fa-spinner fa-spin"></i></div>
			</div>
		</div>
	</div>
	<div class="col">
		<div class="card">
			<div class="card-header d-sm-flex d-block pb-0 border-0">
				<h4 class="fs-20 text-black"><?= $this->lang->line('technical_analysis') ?></h4>
			</div>
			<div class="card-body pt-0">
				<div class="form-group">
					<select id="sl_indicator" class="form-control default-select">
						<?php foreach($indicators as $item){ ?>
						<option value="<?= $item->code ?>"><?= $item->description ?></option>
						<?php } ?>
					</select>
				</div>				
				<div id="indicator_form_block" class="mt-3">
					<?php foreach($indicators as $item) $this->load->view("market/indicators/".$item->code."_form"); ?>
				</div>
				<?php if ($this->session->userdata('plan')->price){ ?>
				<div id="itp_block" class="mt-3">
					<div class="custom-control custom-checkbox">
						<input type="checkbox" class="custom-control-input" id="chk_itp" checked>
						<label class="custom-control-label" for="chk_itp">
							<span><?= $this->lang->line('show_interpretation') ?></span>
							<i class="fa fa-question-circle-o ml-1" data-container="body" data-toggle="popover" data-trigger="hover" data-placement="top" data-html="true" data-content="<?= $this->lang->line('reading') ?>:<br/><i class='fa fa-minus text-success mr-1'></i><?= $this->lang->line('buy_signal') ?><br/><i class='fa fa-minus text-danger mr-1'></i><?= $this->lang->line('sell_signal') ?>" data-original-title="" title=""></i>
						</label>
					</div>
				</div>
				<?php } ?>
				<div class="text-right" id="form_btns">
					<button type="button" id="btn_reset_forms" class="btn tp-btn btn-primary btn-sm">
						<?= $this->lang->line('initialize') ?>
					</button>
					<button type="button" id="btn_indicator" class="btn btn-primary btn-sm">
						<?= $this->lang->line('apply') ?>
					</button>
				</div>
				<div class="text-center my-3 d-none ic_indicator_chart_loading"><i class="fa fa-spinner fa-spin"></i></div>
			</div>
		</div>
	</div>
</div>
<div class="row">
	<div class="col">
		<div class="card">
			<div class="card-header pb-0 border-0">
				<h4 class="fs-20 text-black"><?= $this->lang->line('movement_history') ?></h4>
			</div>
			<div class="card-body pt-0">
				<div id="bl_market_table" class="table-responsive">
					<table id="tb_records" class="table mb-0 display w-100 text-nowrap text-right">
						<thead>
							<tr>
								<th class="text-left"><?= $this->lang->line('date') ?></th>
								<th><?= $this->lang->line('price') ?></th>
								<th><?= $this->lang->line('variation') ?></th>
								<th><?= $this->lang->line('volume') ?></th>
								<th><?= $this->lang->line('num_negociated') ?></th>
								<th><?= $this->lang->line('prev_price') ?></th>
								<th><?= $this->lang->line('prev_date') ?></th>
							</tr>
						</thead>
						<tbody>
							<?php 
							foreach($records as $item){
								if ($item->close){
									array_unshift($dates, strtotime($item->date) * 1000);
									array_unshift($opens, floatval($item->open));
									array_unshift($highs, floatval($item->high));
									array_unshift($lows, floatval($item->low));
									array_unshift($closes, floatval($item->close));
									array_unshift($volumes, floatval($item->quantityNegotiated));
								}
								
								if ($item->close){
									$d = strlen(substr(strrchr($item->close, "."), 1)); if ($d < 2) $d = 2;
									$close_t = $item->currencySymbol." ".number_format($item->close, $d);
									
									if ($item->yesterdayClose) $variation = ($item->close - $item->yesterdayClose) * 100 / $item->yesterdayClose; else $variation = 0;
								}else{
									$close_t = "-";
									$variation = 0;
								}
								
								if ($variation == 0){$var_t = "-"; $var_ic = ""; $var_color = "muted";}
								else{
									$var_t = number_format(abs($variation), 2)."%";
									if ($variation > 0){$var_ic = "up"; $var_color = "success";}
									else{$var_ic = "down"; $var_color = "danger";}
								}
								
								$aNego = $item->currencySymbol." ".$this->utility_lib->shortNumber($item->amountNegotiated);
								$volume = $this->utility_lib->shortNumber($item->quantityNegotiated, false);
								
								$d = strlen(substr(strrchr($item->yesterdayClose, "."), 1)); if ($d < 2) $d = 2;
								$ydClose = $item->currencySymbol." ".number_format($item->yesterdayClose, $d);
							?>
							<tr>
								<td class="text-left"><?= $item->date ?></td>
								<td><?= $close_t ?></td>
								<td class="text-<?= $var_color ?>">
									<?php if ($var_ic){ ?><i class="fa fa-caret-<?= $var_ic ?>"></i><?php } ?>
									<?= $var_t ?>
								</td>
								<td><?= $aNego ?></td>
								<td><?= $volume ?></td>
								<td><?= $ydClose ?></td>
								<td><?= $item->yesterday ?></td>
							</tr>
							<?php } ?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>
<?php }else{ ?>
<div class="row">
	<div class="col">
		<h5 class="text-muted text-center"><?= $this->lang->line('no_history') ?></h3>
	</div>
</div>
<?php } ?>
<div class="d-none">
	<input type="hidden" id="nemonico" value="<?= $company->nemonico ?>">
	<div id="ddate"><?= json_encode($dates) ?></div>
	<div id="dopen"><?= json_encode($opens) ?></div>
	<div id="dhigh"><?= json_encode($highs) ?></div>
	<div id="dlow"><?= json_encode($lows) ?></div>
	<div id="dclose"><?= json_encode($closes) ?></div>
	<div id="dvolumes"><?= json_encode($volumes) ?></div>
</div>