<?php foreach($indicators as $i){ ?>
<tr style="height: 65px;">
	<?php foreach($plans as $p){ ?>
	<td>
		<?php $value = $i->id."/".$p->id;
		if (in_array($value, $arr_ind_plan)) $checked = "checked"; else $checked = ""; ?>
		<div class="custom-control custom-checkbox checkbox-primary">
			<input type="checkbox" class="custom-control-input d-none chk_ind_plan" id="<?= $i->id ?>_<?= $p->id ?>" value="<?= $value ?>" <?= $checked ?>>
			<label class="custom-control-label" for="<?= $i->id ?>_<?= $p->id ?>"></label>
		</div>
	</td>
	<?php } ?>
</tr>
<?php } ?>