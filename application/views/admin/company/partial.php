<?php foreach($arr_update as $nemonico){ ?>
<button class="btn btn-primary mr-1 mb-1 btn_partial"><?= $nemonico ?></button>
<?php } ?>
<span id="partial_blank" class="text-danger d-none"><?= $this->lang->line('msg_no_update') ?></span>