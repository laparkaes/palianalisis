<div class="row page-titles d-md-none mx-0">
	<div class="col">
		<div class="welcome-text">
			<h4><?= $title ?></h4>
		</div>
	</div>
</div>
<div class="row">
	<div class="col-sm-12 col-md-6">
		<div class="widget-stat card">
			<div class="card-body p-4">
		<i class="fa fa-question-circle-o" data-container="body" data-toggle="popover" data-trigger="hover" data-placement="top" data-content="<?= $this->lang->line('resume_text') ?>" data-original-title="" title="" style="position: absolute; top: 10px; right: 10px;"></i>
				<div class="media ai-icon">
					<span class="mr-3 bgl-<?= $my_stocks->sol_balance_color ?> text-<?= $my_stocks->sol_balance_color ?>">
						<?= $this->lang->line('sol_symbol') ?>
					</span>
					<div class="media-body">
						<p class="mb-1"><?= $this->lang->line('peruvian_sol') ?></p>
						<h4 class="mb-0 text-dark"><?= $my_stocks->sol_value ?></h4>
						<span class="badge badge-<?= $my_stocks->sol_balance_color ?> text-white">
							<i class="fa fa-caret-<?= $my_stocks->sol_balance_icon ?> mr-1"></i><?= $my_stocks->sol_balance ?>
						</span>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="col-sm-12 col-md-6">
		<div class="widget-stat card">
			<div class="card-body p-4">
				<div class="media ai-icon">
					<span class="mr-3 bgl-<?= $my_stocks->dol_balance_color ?> text-<?= $my_stocks->dol_balance_color ?>">
						<?= $this->lang->line('usd_symbol') ?>
					</span>
					<div class="media-body">
						<p class="mb-1"><?= $this->lang->line('american_dollar') ?></p>
						<h4 class="mb-0 text-dark"><?= $my_stocks->dol_value ?></h4>
						<span class="badge badge-<?= $my_stocks->dol_balance_color ?> text-white">
							<i class="fa fa-caret-<?= $my_stocks->dol_balance_icon ?> mr-1"></i><?= $my_stocks->dol_balance ?>
						</span>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="row">
	<div class="col-sm-12 col-md-6 col-lg-4">
		<div class="card">
			<div class="card-body">
				<div class="basic-form">
					<?php echo form_open('#', array("id" => "add_wallet")); ?>
						<div class="form-row">
							<div class="form-group col-md-12">
								<label><?= $this->lang->line('company') ?></label>
								<input type="text" class="form-control" id="nw_nemonico" name="nemonico">
								<div class="sys_msg" id="nw_nemonico_msg"></div>
							</div>
							<div class="form-group col-md-6">
								<label><?= $this->lang->line('operation') ?></label>
								<select name="type" class="form-control default-select">
									<option value="buy"><?= $this->lang->line('buy') ?></option>
									<option value="sell"><?= $this->lang->line('sell') ?></option>
								</select>
							</div>
							<div class="form-group col-md-6">
								<label><?= $this->lang->line('date') ?></label>
								<input type="text" class="form-control" id="nw_date" name="date">
								<div class="sys_msg" id="nw_date_msg"></div>
							</div>
							<div class="form-group col-md-6">
								<label><?= $this->lang->line('unit_price') ?></label>
								<input type="text" class="form-control" id="nw_price" name="price">
								<div class="sys_msg" id="nw_price_msg"></div>
							</div>
							<div class="form-group col-md-6">
								<label><?= $this->lang->line('quantity') ?></label>
								<input type="text" class="form-control" id="nw_quantity" name="quantity">
								<div class="sys_msg" id="nw_quantity_msg"></div>
							</div>
						</div>
						<button type="submit" class="btn btn-primary w-100"><?= $this->lang->line('add') ?></button>
						<div class="sys_msg mt-1" id="nw_result_msg"></div>
					<?php echo form_close(); ?>
				</div>
			</div>
		</div>
	</div>
	<div class="col-sm-12 col-md-6 col-lg-8">
		<div class="card">
			<div class="card-body">
				<div class="text-right">
					<div id="btns_visible" class="btn-group">
						<button type="button" class="btn op btn-primary" value="active">
							<?= $this->lang->line('active') ?>
						</button>
						<button type="button" class="btn op btn-outline-primary" value="all">
							<?= $this->lang->line('all') ?>
						</button>
					</div>
				</div>
				<div class="table-responsive">
					<table class="table text-nowrap border-bottom text-right mb-0">
						<thead>
							<tr class="text-uppercase">
								<th class="text-left"><strong>#</strong></th>
								<th><strong><?= $this->lang->line('mnemonic') ?></strong></th>
								<th><strong><?= $this->lang->line('date') ?></strong></th>
								<th><strong><?= $this->lang->line('quantity') ?></strong></th>
								<th><strong><?= $this->lang->line('price') ?></strong></th>
								<th><strong><?= $this->lang->line('value') ?></strong></th>
								<th><strong><?= $this->lang->line('balance') ?></strong></th>
								<th></th>
							</tr>
						</thead>
						<tbody>
							<?php 
							foreach($wallets as $i => $item){ 
								$resume = $item["resume"];
								if ($resume->quantity) $row_visible = "row_resume";
								else $row_visible = "row_resume inactive d-none";
							?>
							<tr class="<?= $row_visible ?>">
								<td class="text-left"><strong><?= $i + 1 ?><strong></td>
								<td data-container="body" data-toggle="popover" data-trigger="hover" data-placement="bottom" data-content="<?= $item["name"] ?>" data-original-title="" title=""><?= $item["nemonico"] ?></td>
								<td><?= $resume->date ?></td>
								<td><?= number_format($resume->quantity) ?></td>
								<td><?= $resume->price_t ?></td>
								<td><?= $resume->value_t ?></td>
								<td>
									<span class="text-<?= $resume->balance_color ?>">
										<i class="fa fa-caret-<?= $resume->balance_icon ?>"></i>
										<?= $resume->balance_t ?>
									</span>
								</td>
								<td>
									<button type="button" class="btn btn-primary shadow btn-xs sharp btn_dcontrol" value="<?= $item["nemonico"] ?>">
										<i class="fa fa-caret-down"></i>
									</button>
								</td>
							</tr>
							<?php $operations = $item["operations"]; foreach($operations as $j => $o){ ?>
							<tr class="table-active detail detail_<?= $item["nemonico"] ?> d-none">
								<td></td>
								<td>
									<?php if ($o->is_buy){ ?>
										<span class="text-success"><?= $this->lang->line('buy') ?></span>
									<?php }else{ ?>
										<span class="text-danger"><?= $this->lang->line('sell') ?></span>
									<?php } ?>
								</td>
								<td><?= $o->date ?></td>
								<td><?= number_format($o->quantity) ?></td>
								<td><?= $o->price_t ?></td>
								<td><?= $o->value_t ?></td>
								<td>
									<span class="text-<?= $o->balance_color ?>">
										<i class="fa fa-caret-<?= $o->balance_icon ?>"></i>
										<?= $o->balance_t ?>
									</span>
								</td>
								<td>
									<button type="button" class="btn btn-light shadow btn-xs sharp btn_roperation" value="<?= $o->id ?>">
										<i class="fa fa-trash"></i>
									</button>
								</td>
							</tr>
							<?php }} ?>
						</tbody>
					</table>
				</div>
				<?php if (!$wallets){ ?>
				<div class="text-center mt-3"><?= $this->lang->line('no_operations') ?></div>
				<?php } ?>
			</div>
		</div>
	</div>
</div>
<div class="row">
	<div class="col">
		<div class="card">
			<div class="card-header border-0 pb-0">
				<h5 class="card-title"><?= $this->lang->line('operations') ?></h5>
			</div>
			<div class="card-body">
				<div class="table-responsive">
					<table class="table text-nowrap border-bottom text-right mb-0" id="tb_all_operations">
						<thead>
							<tr class="text-uppercase">
								<th class="text-left"><strong>#</strong></th>
								<th><strong><?= $this->lang->line('mnemonic') ?></strong></th>
								<th><strong><?= $this->lang->line('date') ?></strong></th>
								<th><strong><?= $this->lang->line('type') ?></strong></th>
								<th><strong><?= $this->lang->line('investment') ?></strong></th>
								<th><strong><?= $this->lang->line('income') ?></strong></th>
								<th><strong><?= $this->lang->line('balance') ?></strong></th>
								<th><strong><?= $this->lang->line('investment') ?></strong></th>
								<th><strong><?= $this->lang->line('income') ?></strong></th>
								<th><strong><?= $this->lang->line('balance') ?></strong></th>
							</tr>
						</thead>
						<tbody>
							<?php foreach($all_operations as $i => $item){ ?>
							<tr>
								<td class="text-left"><strong><?= $i + 1 ?><strong></td>
								<td data-container="body" data-toggle="popover" data-trigger="hover" data-placement="bottom" data-content="<?= $item->name ?>" data-original-title="" title=""><?= $item->nemonico ?></td>
								<td><?= $item->date ?></td>
								<td>
									<?php if ($item->is_buy){ ?>
									<span class="text-success"><?= $this->lang->line('buy') ?></span>
									<?php }else{ ?>
									<span class="text-danger"><?= $this->lang->line('sell') ?></span>	
									<?php } ?>
								</td>
								<td><?= $item->sol_invest ?></td>
								<td><?= $item->sol_income ?></td>
								<td class="text-<?= $item->sol_balance_color ?>">
									<i class="fa fa-caret-<?= $item->sol_balance_icon ?> ml-1"></i>
									<?= $item->sol_balance ?>
								</td>
								<td><?= $item->dol_invest ?></td>
								<td><?= $item->dol_income ?></td>
								<td class="text-<?= $item->dol_balance_color ?>">
									<i class="fa fa-caret-<?= $item->dol_balance_icon ?> ml-1"></i>
									<?= $item->dol_balance ?>
								</td>
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
	<div class="d-none" id="companies_list"><?= $companies ?></div>
	<input type="hidden" id="error_no_result" value="<?= $this->lang->line('error_no_result') ?>">
	<input type="hidden" id="of" value="<?= $this->lang->line('of') ?>">
	<input type="hidden" id="operations" value="<?= $this->lang->line('operations') ?>">
	<input type="hidden" id="no_operations" value="<?= $this->lang->line('no_operations') ?>">
	<input type="hidden" id="warning_remove_operation" value="<?= $this->lang->line('warning_remove_operation') ?>">
	<input type="hidden" id="lang_cancel" value="<?= $this->lang->line('cancel') ?>">
	<input type="hidden" id="lang_remove" value="<?= $this->lang->line('remove') ?>">
</div>