<?php foreach($services as $s){ ?>
<tr style="height: 65px;">
	<?php foreach($plans as $p){ ?>
	<td>
		<?php $value = $s->id."/".$p->id;
		if (in_array($value, $arr_srv_plan)) $checked = "checked"; else $checked = ""; ?>
		<div class="custom-control custom-checkbox checkbox-primary">
			<input type="checkbox" class="custom-control-input d-none chk_srv_plan" id="<?= $s->id ?>_<?= $p->id ?>" value="<?= $value ?>" <?= $checked ?>>
			<label class="custom-control-label" for="<?= $s->id ?>_<?= $p->id ?>"></label>
		</div>
	</td>
	<?php } ?>
</tr>
<?php } ?>