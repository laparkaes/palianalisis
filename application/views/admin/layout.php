<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title><?= $this->lang->line("title")." - ".$title_ctrl ?></title>
    <link rel="icon" href="/images/favicon.ico">
	<link rel="stylesheet" href="/utility/tmp_b/vendor/chartist/css/chartist.min.css">
	<link rel="stylesheet" href="/utility/tmp_b/vendor/datatables/css/jquery.dataTables.min.css">
	<link rel="stylesheet" href="/utility/tmp_b/vendor/select2/css/select2.min.css">
	<link rel="stylesheet" href="/utility/tmp_b/vendor/bootstrap-icons/bootstrap-icons.css">
    <link rel="stylesheet" href="/utility/tmp_b/vendor/bootstrap-select/dist/css/bootstrap-select.min.css">
	<link rel="stylesheet" href="/utility/tmp_b/vendor/bootstrap-daterangepicker/daterangepicker.css">
	<link rel="stylesheet" href="/utility/tmp_b/vendor/bootstrap-material-datetimepicker/css/bootstrap-material-datetimepicker.css">
    <link rel="stylesheet" href="/utility/tmp_b/vendor/pickadate/themes/default.css">
    <link rel="stylesheet" href="/utility/tmp_b/vendor/pickadate/themes/default.date.css">
	<link rel="stylesheet" href="/utility/tmp_b/vendor/toastr/css/toastr.min.css">
	<link rel="stylesheet" href="/utility/tmp_b/vendor/sweetalert2/dist/sweetalert2.min.css">
	<link rel="stylesheet" href="/utility/tmp_b/vendor/jquery-smartwizard/dist/css/smart_wizard.min.css">
	<link rel="stylesheet" href="/utility/tmp_b/vendor/material-icons/material-icons.css">
    <link rel="stylesheet" href="/utility/tmp_b/css/style.css">
	<link rel="stylesheet" href="/utility/tmp_b/css/setting.css">
	
	<script src="/utility/jquery-3.3.1.min.js"></script>
	<script src="/utility/js/apexchartsFunc.js"></script>
	<script src="/utility/js/pali.js"></script>
	<script src="/utility/apexcharts.3.34.0/apexcharts.min.js"></script>
</head>
<body>
	<?php if ($this->session->userdata('subscription_msg')){$msg = $this->session->userdata('subscription_msg'); ?>
	<div class="alert alert-<?= $msg[0] ?> alert-dismissible fixed-bottom text-center rounded-0 py-2 mb-0">
		<?= $msg[1] ?>	
	</div>
	<?php } ?>
    <div id="main-wrapper" style="opacity: 1;">
        <div class="nav-header">
            <a href="/admin/dashboard" class="brand-logo">
				<img class="logo-abbr" src="/images/logo.svg" alt="">
				<img class="brand-title mt-0" src="/images/logo_text.svg" alt="" style="height: 45px;">
            </a>
            <div class="nav-control">
                <div class="hamburger">
                    <span class="line"></span><span class="line"></span><span class="line"></span>
                </div>
            </div>
        </div>
        <div class="header">
            <div class="header-content">
                <nav class="navbar navbar-expand">
                    <div class="collapse navbar-collapse justify-content-between">
                        <div class="header-left">
                            <div class="dashboard_bar">
                                <?= $title1 ?>
                            </div>
                        </div>
						<ul class="navbar-nav header-right">
                            <li class="nav-item dropdown header-profile">
                                <a class="nav-link" href="#" role="button" data-toggle="dropdown">
									<img src="/images/ic-account-96.png" alt="" width="20">
									<div class="header-info">
										<span id="logged_name"><?= $this->session->userdata('name'); ?></span>
										<small><?= $this->lang->line('admin') ?></small>
									</div>
                                </a>
                                <div class="dropdown-menu dropdown-menu-right">
                                    <a href="/admin/access/logout" class="dropdown-item ai-icon">
                                        <i class="fa fa-sign-out text-danger fa-lg mr-2" style="width: 14px"></i>
                                        <span class="ml-2"><?= $this->lang->line('logout') ?></span>
                                    </a>
                                </div>
                            </li>
                        </ul>
                    </div>
                </nav>
            </div>
        </div>
        <div class="deznav">
            <div class="deznav-scroll">
				<ul class="metismenu" id="menu">
                    <li>
						<a class="ai-icon" href="/admin/dashboard" aria-expanded="false">
							<i class="flaticon-381-home-2"></i>
							<span class="nav-text"><?= $this->lang->line('dashboard') ?></span>
						</a>
					</li>
					<li>
						<a class="ai-icon" href="/admin/client" aria-expanded="false">
							<i class="flaticon-381-user-9"></i>
							<span class="nav-text"><?= $this->lang->line('client') ?></span>
						</a>
                    </li>
					<li>
						<a class="ai-icon" href="/admin/company" aria-expanded="false">
							<i class="flaticon-381-folder"></i>
							<span class="nav-text"><?= $this->lang->line('company') ?></span>
						</a>
                    </li>
					<li>
						<a class="ai-icon" href="/admin/plan" aria-expanded="false">
							<i class="flaticon-381-key"></i>
							<span class="nav-text"><?= $this->lang->line('plan') ?></span>
						</a>
                    </li>
                </ul>
				<div class="copyright mt-5">
					<p><strong><?= $this->lang->line('title') ?></strong> © 2022 <?= $this->lang->line('all_rights_reserved') ?></p>
				</div>
			</div>
        </div>
		<div id="bl_main">
			<div class="content-body">
				<div class="container-fluid">
					<?php $this->load->view($main); ?>
				</div>
			</div>
		</div>
	<div class="d-none">
		<input type="hidden" id="lang_accept" value="<?= $this->lang->line('accept'); ?>">
		<input type="hidden" id="lang_cancel" value="<?= $this->lang->line('cancel'); ?>">
		<input type="hidden" id="lang_warning_no_result" value="<?= $this->lang->line('warning_no_result'); ?>">
		<input type="hidden" id="lang_sweet_success" value="<?= $this->lang->line('sweet_success'); ?>">
		<input type="hidden" id="lang_sweet_error" value="<?= $this->lang->line('sweet_error'); ?>">
		<input type="hidden" id="lang_sweet_warning" value="<?= $this->lang->line('sweet_warning'); ?>">
	</div>
    <script src="/utility/tmp_b/vendor/global/global.min.js"></script>
	<script src="/utility/tmp_b/vendor/bootstrap-select/dist/js/bootstrap-select.min.js"></script>
    <script src="/utility/tmp_b/vendor/select2/js/select2.full.min.js"></script>
	<script src="/utility/tmp_b/vendor/chart.js/Chart.bundle.min.js"></script>
    <script src="/utility/tmp_b/vendor/peity/jquery.peity.min.js"></script>
    <script src="/utility/tmp_b/vendor/datatables/js/jquery.dataTables.min.js"></script>
	<script src="/utility/tmp_b/vendor/moment/moment.min.js"></script>
	<script src="/utility/tmp_b/vendor/moment/moment-with-locales.min.js"></script>
    <script src="/utility/tmp_b/vendor/bootstrap-daterangepicker/daterangepicker.js"></script>
	<script src="/utility/tmp_b/vendor/bootstrap-material-datetimepicker/js/bootstrap-material-datetimepicker.js"></script>
	<script src="/utility/tmp_b/vendor/toastr/js/toastr.min.js"></script>
	<script src="/utility/tmp_b/vendor/sweetalert2/dist/sweetalert2.min.js"></script>
	<script src="/utility/tmp_b/vendor/jquery-smartwizard/dist/js/jquery.smartWizard.js"></script>
    <script src="/utility/tmp_b/js/custom.min.js"></script>
	<script src="/utility/tmp_b/js/deznav-init.js"></script>
</body>
</html>