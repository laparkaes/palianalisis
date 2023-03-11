<?php $i = 1; $form_attr = array("class" => "remove_indicator"); 
foreach($indicators as $item){ ?>
<tr style="height: 65px;">
	<td class="text-left"><?= $i ?></td>
	<td><?= $item->description ?></td>
	<td><?= $item->code ?></td>
	<td>
		<?php echo form_open('#', $form_attr); ?>
		<input type="hidden" name="id" value="<?= $item->id ?>">
		<button type="submit" class="btn btn-danger light sharp">
			<i class="fa fa-trash"></i>
		</button>
		<?php echo form_close(); ?>
	</td>
</tr>
<?php $i++;} ?>