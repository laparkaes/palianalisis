<div class="row page-titles d-md-none mx-0">
	<div class="col">
		<div class="welcome-text">
			<h4><?= $title ?></h4>
		</div>
	</div>
</div>
<div class="row">
	<div class="col">
		<div class="card">
			<div class="card-body table-responsive">
				<table id="tb_companies" class="table mb-0 display w-100 text-nowrap text-right">
					<thead>
						<tr class="text-center">
							<th><?= $this->lang->line('type') ?></th>
							<th><?= $this->lang->line('company') ?></th>
							<th><?= $this->lang->line('mnemonic') ?></th>
							<th><?= $this->lang->line('sector') ?></th>
							<th><?= $this->lang->line('market') ?></th>
							<th><?= $this->lang->line('broker') ?></th>
						</tr>
					</thead>
					<tbody>
						<?php foreach($companies as $item){ ?>
						<tr>
							<td><?php if ($item->is_national) echo $this->lang->line('national'); else echo $this->lang->line('foreign'); ?></td>
							<td class="text-left text-truncate" style="max-width: 400px;"><?= $item->name ?></td>
							<td>
								<a href="/market/company?n=<?= $item->nemonico ?>" class="text-info">
									<?= $item->nemonico ?>
								</a>
								<?php 
								$is_fav = in_array($item->nemonico, $favorites_nemonicos);
								if ($is_fav) $item->ic_fav = "fa-star text-warning"; else $item->ic_fav = "fa-star-o text-muted";
								?>
								<button class="ic_favorite border-0 bg-transparent ml-1" value="<?= $item->nemonico ?>">
									<i class="fa <?= $item->ic_fav ?>"></i>
								</button>
							</td>
							<td data-container="body" data-toggle="popover" data-trigger="hover" data-placement="top" data-content="<?= $item->sectorDescription ?>">
								<div class="text-truncate" style="max-width: 100px;">
									<?= $item->sectorDescription ?>
								</div>
							</td>
							<td data-container="body" data-toggle="popover" data-trigger="hover" data-placement="top" data-content="<?= $item->market ?>">
								<div class="text-truncate" style="max-width: 120px;">
									<?= $item->market ?>
								</div>
							</td>
							<td data-container="body" data-toggle="popover" data-trigger="hover" data-placement="top" data-content="<?= $item->broker ?>">
								<div class="text-truncate" style="max-width: 120px;">
									<?= $item->broker ?>
								</div>
							</td>
						</tr>
						<?php } ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>
<div class="d-none">
	<input type="hidden" id="msg_filter" value="<?= $this->lang->line('filter') ?>">
	<input type="hidden" id="msg_per_page" value="<?= $this->lang->line('per_page') ?>">
	<input type="hidden" id="msg_no_result" value="<?= $this->lang->line('no_result') ?>">
	<input type="hidden" id="msg_of" value="<?= $this->lang->line('of') ?>">
	<input type="hidden" id="msg_total" value="<?= $this->lang->line('total') ?>">
</div>