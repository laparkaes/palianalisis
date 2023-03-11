<?php $i = 1; $form_attr = array("class" => "remove_plan"); foreach($plans as $item){ ?>
<tr class="row_plan" style="height: 65px;">
	<td class="text-left"><?= $i ?></td>
	<td><?= $item->mp_plan_id ?></td>
	<td><?= $item->description ?></td>
	<td><?= number_format($item->price, 2) ?></td>
	<td>
		<?php echo form_open('#', $form_attr);  ?>
		<input type="hidden" name="id" value="<?= $item->id ?>">
		<button type="submit" class="btn btn-danger light sharp">
			<i class="fa fa-trash"></i>
		</button>
		<?php echo form_close(); ?>
	</td>
</tr>
<?php $i++; } ?>