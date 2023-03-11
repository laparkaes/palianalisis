<script>
$(document).ready(function() {
	$(".tb_rmcd").unbind('click').on('click',(function(e) {
		remove_rtcd($(this).parent().children(".nemonico").val());
	}));
	
	$(".tb_adcd").unbind('click').on('click',(function(e) {
		add_rtcd($(this).parent().children(".nemonico").val());
	}));
});
</script>

<?php 
if ($show_qty == 999){ $text_currency = ""; $add_realtime = true; }
else{ $text_currency = "text-right"; $add_realtime = false; }
?>
<table class="table bg-info-hover tr-rounded text-right">
	<thead>
		<tr>
			<th class="text-left">EMPRESA</th>
			<th></th>
			<th class="text-success">COMPRA</th>
			<th class="text-danger">VENTA</th>
			<?php if ($add_realtime){ ?>
			<th></th>
			<?php } ?>
		</tr>
	</thead>
	<tbody>
		<?php foreach($stocks as $item){
				if (!$show_qty) break;
				if ($item->buy or $item->sell){$show_qty--; $row_class = "";}else $row_class = "d-none"; ?>
		<tr class="<?= $row_class ?>">
			<td class="text-left">
				<a href="/company/detail?n=<?= $item->nemonico ?>" class="text-info">
					<?= $item->nemonico ?>
				</a>
			</td>
			<td class="<?= $text_currency ?>"><?= $item->currencySymbol ?></td>
			<td><?php if ($item->buy) echo $item->buy_t; else echo "-"; ?></td>
			<td><?php if ($item->sell) echo $item->sell_t; else echo "-"; ?></td>
			<?php if ($add_realtime){ ?>
			<td id="actions_<?= $item->nemonico ?>" class="actions text-right">
				<?php if (in_array($item->nemonico, $realtimes)){$btn_add = "d-none"; $btn_remove = "";}
				else {$btn_add = ""; $btn_remove = "d-none";} ?>
				<input type="hidden" class="nemonico" value="<?= $item->nemonico ?>">
				<button class="tb_adcd btn tp-btn btn-primary btn-xs <?= $btn_add ?>"><i class="fa fa-desktop"></i></button>
				<button class="tb_rmcd btn tp-btn btn-danger btn-xs <?= $btn_remove ?>"><i class="fa fa-times"></i></button>
			</td>
			<?php } ?>
		</tr>
		<?php } ?>
	</tbody>
</table>