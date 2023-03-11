<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="en" class="h-100">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title><?= $this->lang->line("title")." - ".$title_ctrl ?></title>
    <link rel="icon" href="/images/favicon.ico">
	<link rel="stylesheet" href="/utility/tmp_b/vendor/bootstrap-select/dist/css/bootstrap-select.min.css">
	<link rel="stylesheet" href="/utility/tmp_b/vendor/sweetalert2/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="/utility/tmp_b/css/style.css">
	<link rel="stylesheet" href="/utility/tmp_b/css/setting.css">
</head>
<body class="vh-100">
    <div class="authincation h-100">
        <div class="container h-100">
            <div class="row justify-content-center h-100 align-items-center">
                <div class="col-md-6">
                    <div class="authincation-content">
                        <div class="row no-gutters">
                            <div class="col-xl-12">
                                <div class="auth-form">
									<div class="text-center mb-3">
										<a href="#"><img src="/images/logo_img.jpg" alt=""></a>
									</div>
                                    <h4 class="text-center mb-4">Panel de Administracion</h4>
                                    <?php echo form_open('#', array("id" => "login_form")); ?>
                                        <div class="form-group">
                                            <label class="mb-1"><strong>Correo electronico</strong></label>
                                            <input type="text" class="form-control" name="email" id="email">
                                        </div>
                                        <div class="form-group">
                                            <label class="mb-1"><strong>Contraseña</strong></label>
                                            <input type="password" class="form-control" name="password">
                                        </div>
                                        <div class="text-center">
                                            <button type="submit" class="btn btn-primary btn-block">Ingresar</button>
											<i class="fa fa-spinner fa-spin ic_loading mt-3 d-none"></i>
                                        </div>
                                    <?php echo form_close(); if (!$has_master){ ?>
									<div class="new-account mt-3">
										<a class="text-primary" href="/admin/access/generate_master" target="_blank">Crear admin maestro</a>
                                    </div>
									<?php } ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="/utility/tmp_b/vendor/global/global.min.js"></script>
	<script src="/utility/tmp_b/vendor/bootstrap-select/dist/js/bootstrap-select.min.js"></script>
	<script src="/utility/tmp_b/vendor/sweetalert2/dist/sweetalert2.min.js"></script>
    <script src="/utility/tmp_b/js/custom.min.js"></script>
	<script src="/utility/tmp_b/js/deznav-init.js"></script>
	<script src="/utility/js/myglobal.js"></script>
	<script src="/utility/js/init/admin/login_init.js"></script>
	<div class="d-none">
		<input type="hidden" id="lang_accept" value="<?= $this->lang->line('accept'); ?>">
		<input type="hidden" id="lang_cancel" value="<?= $this->lang->line('cancel'); ?>">
		<input type="hidden" id="lang_sweet_success" value="<?= $this->lang->line('sweet_success'); ?>">
		<input type="hidden" id="lang_sweet_error" value="<?= $this->lang->line('sweet_error'); ?>">
		<input type="hidden" id="lang_sweet_warning" value="<?= $this->lang->line('sweet_warning'); ?>">
	</div>
</body>
</html>