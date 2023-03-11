<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?><!DOCTYPE html>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta content="width=device-width, initial-scale=1.0" name="viewport">
	<title><?= $this->lang->line("title")." - ".$title_ctrl ?></title>
	<link rel="icon" href="/images/favicon.ico">
	<link rel="stylesheet"href="/utility/tmp_f/vendor/aos/aos.css">
	<link rel="stylesheet"href="/utility/tmp_f/vendor/bootstrap/css/bootstrap.min.css">
	<link rel="stylesheet"href="/utility/tmp_f/vendor/bootstrap-icons/bootstrap-icons.css">
	<link rel="stylesheet"href="/utility/tmp_f/vendor/boxicons/css/boxicons.min.css">
	<link rel="stylesheet"href="/utility/tmp_f/vendor/glightbox/css/glightbox.min.css">
	<link rel="stylesheet"href="/utility/tmp_f/vendor/remixicon/remixicon.css">
	<link rel="stylesheet"href="/utility/tmp_f/vendor/swiper/swiper-bundle.min.css">
	<link rel="stylesheet"href="/utility/tmp_f/vendor/toastr/css/toastr.min.css">
	<link rel="stylesheet"href="/utility/tmp_f/vendor/sweetalert2/dist/sweetalert2.min.css">
	<link rel="stylesheet"href="/utility/tmp_f/vendor/fontawesome/css/font-awesome.min.css">
	<link rel="stylesheet"href="/utility/tmp_f/css/font.css">
	<link rel="stylesheet"href="/utility/tmp_f/css/style.css">
	<link rel="stylesheet" href="/utility/css/tmp_f_setting.css">
	<link rel="stylesheet" href="/utility/css/pali.css">
</head>
<body>
	<header id="header" class="fixed-top d-flex align-items-center">
		<div class="container d-flex align-items-center">
			<h1 class="logo me-auto">
				<a href="/" class="logo me-auto">
					<img src="/images/logo_f.svg" alt="">
				</a>
			</h1>
			<nav id="navbar" class="navbar order-last order-lg-0">
				<ul>
					<li><a class="nav-link scrollto" href="#hero"><?= $this->lang->line("home") ?></a></li>
					<li><a class="nav-link scrollto" href="#about"><?= $this->lang->line("presentation") ?></a></li>
					<li><a class="nav-link scrollto" href="#pricing"><?= $this->lang->line("plans") ?></a></li>
					<li><a class="nav-link scrollto" href="#portfolio"><?= $this->lang->line("portfolios") ?></a></li>
				</ul>
				<i class="bi bi-list mobile-nav-toggle"></i>
			</nav>
			<?php if ($this->session->userdata('logged_in')){
				if ($this->session->userdata('is_admin')){
					$link = "/admin/dashboard";
					$text = $this->lang->line("admin_area");
					$icon = "bi-pie-chart";
				}else{ 
					$link = "/dashboard";
					$text = $this->lang->line("client_area");
					$icon = "bi-bar-chart-line-fill";
				} ?>
				<a href="<?= $link ?>" class="get-started-btn">
					<?= $text ?>
					<i class="<?= $icon ?>"></i>
				</a>
			<?php }else{ ?>
			<a href="#" class="get-started-btn" id="btn_modal_login" data-bs-toggle="modal" data-bs-target="#modal_login">
				<?= $this->lang->line("login") ?>
			</a>
			<?php } ?>
		</div>
	</header>
	<div class="modal fade" id="modal_login" tabindex="-1">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-body mt-0">
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="position: absolute; right: 5px; top: 5px;"></button>
					<div class="row">
						<div class="col">
							<div class="text-center mt-1 mb-4">
								<img src="/images/logo_f.svg" alt="Logo">
							</div>
							<div class="auth-form" id="login_block">
								<?php echo form_open('#', array("id" => "login_form")); ?>
								<div class="form-group">
									<label class="mb-1"><strong><?= $this->lang->line('email') ?></strong></label>
									<input type="email" class="form-control" placeholder="hola@ejemplo.com" name="email_login">
									<div class="sys_msg" id="email_login_msg"></div>
								</div>
								<div class="form-group">
									<label class="mb-1"><strong><?= $this->lang->line('password') ?></strong></label>
									<input type="password" class="form-control" name="password_login">
									<div class="sys_msg" id="password_login_msg"></div>
								</div>
								<div class="form-row d-flex justify-content-end mt-4 mb-2">
									<div class="form-group">
										<a href="#" id="go_reset_pass"><?= $this->lang->line('forgot_password') ?></a>
									</div>
								</div>
								<div class="text-center">
									<button type="submit" class="btn btn-general btn-block"><?= $this->lang->line('login') ?></button>
									<div class="ic_loading text-center py-2 d-none"><i class="fa fa-spinner fa-spin"></i></div>
								</div>
								<div class="mt-3">
									<p><?= $this->lang->line('no_has_account') ?> <a href="#" id="go_register"><?= $this->lang->line('register_now') ?></a></p>
								</div>
								<?php echo form_close(); ?>
							</div>
							<div class="auth-form d-none" id="register_block">
								<?php echo form_open('#', array("id" => "register_form")); ?>
								<div class="form-group">
									<label class="mb-1"><strong><?= $this->lang->line('email') ?></strong></label>
									<input type="email" class="form-control" name="email_register" placeholder="hola@ejemplo.com">
									<div class="sys_msg" id="email_register_msg"></div>
								</div>
								<div class="form-group">
									<label class="mb-1"><strong><?= $this->lang->line('password') ?></strong></label>
									<input type="password" class="form-control" name="password_register">
									<div class="sys_msg" id="password_register_msg"></div>
								</div>
								<div class="form-group">
									<label class="mb-1"><strong><?= $this->lang->line('name') ?></strong></label>
									<input type="name" class="form-control" name="name_register" placeholder="<?= $this->lang->line('fullname') ?>">
									<div class="sys_msg" id="name_register_msg"></div>
								</div>
								<div class="form-group mb-3">
									<div class="custom-control custom-checkbox d-flex align-items-center">
										<input type="checkbox" class="custom-control-input" name="chk_terms">
										<?php 
										$terms_text = $this->lang->line('terms_and_privacy_text');
										$terms_text = str_replace("__aterm_", '<a href="/help/terms" target="_blank">', $terms_text);
										$terms_text = str_replace("__apriv_", '<a href="/help/privacy" target="_blank">', $terms_text);
										$terms_text = str_replace("__a_", '</a>', $terms_text);
										?>
										<small class="ml-2"><?= $terms_text ?></small>
									</div>
									<div class="sys_msg" id="terms_register_msg"></div>
								</div>
								<div class="mt-4">
									<button type="submit" class="btn btn-general btn-block"><?= $this->lang->line('register') ?></button>
									<div class="ic_loading text-center py-2 d-none"><i class="fa fa-spinner fa-spin"></i></div>
									<div class="sys_msg" id="result_register_msg"></div>
								</div>
								<div class="mt-3">
									<p><a class="go_login" href="#"><< <?= $this->lang->line('login') ?></a></p>
								</div>
								<?php echo form_close(); ?>
							</div>
							<div class="auth-form d-none" id="reset_pass_block">
								<?php echo form_open('#', array("id" => "reset_pass_form")); ?>
								<div class="form-group">
									<label class="mb-1"><strong><?= $this->lang->line('email') ?></strong></label>
									<input type="email" class="form-control" name="email_reset_pass" placeholder="hola@ejemplo.com">
									<div class="sys_msg" id="email_reset_pass_msg"></div>
								</div>
								<div class="text-center">
									<button type="submit" class="btn btn-general btn-block"><?= $this->lang->line('recover_password') ?></button>
									<div class="ic_loading text-center py-2 d-none"><i class="fa fa-spinner fa-spin"></i></div>
									<div class="sys_msg" id="result_reset_pass_msg"></div>
								</div>
								<div class="mt-3">
									<p><a class="go_login" href="#"><< <?= $this->lang->line('login') ?></a></p>
								</div>
								<?php echo form_close(); ?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php $this->load->view($main); ?>
	<footer id="footer">
		<div class="footer-top">
			<div class="container">
				<div class="row">
					<div class="col-lg-4 col-md-6 footer-contact">
						<h3><?= $this->lang->line('title') ?></h3>
						<p><?= $this->lang->line('banner_msg2') ?></p>
					</div>
					<div class="col-lg-2 col-md-6"></div>
					<div class="col-lg-3 col-md-6 footer-links">
						<h4><?= $this->lang->line('navigation') ?></h4>
						<ul>
							<li>
								<i class="bx bx-chevron-right"></i>
								<a href="#hero" class="scrollto"><?= $this->lang->line("home") ?></a>
							</li>
							<li>
								<i class="bx bx-chevron-right"></i>
								<a href="#about" class="scrollto"><?= $this->lang->line("presentation") ?></a>
							</li>
							<li>
								<i class="bx bx-chevron-right"></i>
								<a href="#pricing" class="scrollto"><?= $this->lang->line("plans") ?></a>
							</li>
							<li>
								<i class="bx bx-chevron-right"></i>
								<a href="#portfolio" class="scrollto"><?= $this->lang->line("portfolios") ?></a>
							</li>
						</ul>
					</div>
					<div class="col-lg-3 col-md-6 footer-links">
						<h4><?= $this->lang->line('help') ?></h4>
						<ul>
							<li>
								<i class="bx bx-chevron-right"></i>
								<a href="/help/terms">
									<?= $this->lang->line('terms_and_conditions') ?>
								</a>
							</li>
							<li>
								<i class="bx bx-chevron-right"></i>
								<a href="/help/privacy">
									<?= $this->lang->line('privacy_policy') ?>
								</a>
							</li>
						</ul>
					</div>
				</div>
			</div>
		</div>
		<div class="container d-md-flex py-4">
			<div class="me-md-auto text-center text-md-start">
				<div class="copyright">
					&copy; Copyright <strong><span><?= $this->lang->line('title') ?></span></strong>. 
					<?= $this->lang->line('all_rights_reserved') ?>
				</div>
				<div class="credits">
					<?= $this->lang->line('designed_by') ?> <a href="https://bootstrapmade.com/" target="_blank">BootstrapMade</a>
				</div>
			</div>
			<div class="social-links text-center text-md-end pt-3 pt-md-0">
				<a href="#" class="facebook"><i class="bx bxl-facebook"></i></a>
				<a href="#" class="instagram"><i class="bx bxl-instagram"></i></a>
			</div>
		</div>
	</footer>
	<a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>
	<script src="/utility/jquery-3.3.1.min.js"></script>
	<script src="/utility/tmp_f/vendor/purecounter/purecounter.js"></script>
	<script src="/utility/tmp_f/vendor/aos/aos.js"></script>
	<script src="/utility/tmp_f/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
	<script src="/utility/tmp_f/vendor/glightbox/js/glightbox.min.js"></script>
	<script src="/utility/tmp_f/vendor/isotope-layout/isotope.pkgd.min.js"></script>
	<script src="/utility/tmp_f/vendor/swiper/swiper-bundle.min.js"></script>
	<script src="/utility/tmp_f/vendor/php-email-form/validate.js"></script>
	<script src="/utility/tmp_f/vendor/toastr/js/toastr.min.js"></script>
	<script src="/utility/tmp_f/vendor/sweetalert2/dist/sweetalert2.min.js"></script>
	<script src="/utility/js/pali.js"></script>
	<script src="/utility/js/init/welcome_init.js"></script>
	<script src="/utility/tmp_f/js/main.js"></script>
	<div class="d-none">
		<input type="hidden" id="lang_accept" value="<?= $this->lang->line('accept'); ?>">
		<input type="hidden" id="lang_cancel" value="<?= $this->lang->line('cancel'); ?>">
		<input type="hidden" id="lang_sweet_success" value="<?= $this->lang->line('sweet_success'); ?>">
		<input type="hidden" id="lang_sweet_error" value="<?= $this->lang->line('sweet_error'); ?>">
		<input type="hidden" id="lang_sweet_warning" value="<?= $this->lang->line('sweet_warning'); ?>">
	</div>
</body>
</html>