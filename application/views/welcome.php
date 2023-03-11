<section id="hero" class="d-flex align-items-top">
	<div class="container" data-aos="zoom-out" data-aos-delay="100">
		<div class="row">
			<div class="col-xl-7 pt-5 mt-5">
				<h1><?= $this->lang->line("banner_msg1") ?></h1>
				<h2><?= $this->lang->line("banner_msg2") ?></h2>
				<a href="#about" class="btn-get-started scrollto"><?= $this->lang->line("learn_more") ?></a>
			</div>
		</div>
	</div>
</section>
<section id="counts" class="counts pt-5">
	<div class="container" data-aos="fade-up">
		<div class="section-title">
			<h2><?= $this->lang->line('now_bvl') ?></h2>
			<p><?= $this->lang->line('resume_title') ?></p>
		</div>
		<div class="row pt-3">
			<div class="col-lg-3 col-md-6">
				<div class="count-box">
					<i class="bi bi-arrow-left-right"></i>
					<span data-purecounter-start="0" data-purecounter-end="<?= $resume->opNumber ?>" data-purecounter-duration="1" class="purecounter"></span>
					<p><?= $this->lang->line('today_trades') ?></p>
				</div>
			</div>
			<div class="col-lg-3 col-md-6 mt-5 mt-md-0">
				<div class="count-box">
					<i class="bi bi-box-arrow-up"></i>
					<span data-purecounter-start="0" data-purecounter-end="<?= $resume->up ?>" data-purecounter-duration="1" class="purecounter"></span>
					<p><?= $this->lang->line('go_up') ?></p>
				</div>
			</div>
			<div class="col-lg-3 col-md-6 mt-5 mt-lg-0">
				<div class="count-box">
					<i class="bi bi-box-arrow-down"></i>
					<span data-purecounter-start="0" data-purecounter-end="<?= $resume->down ?>" data-purecounter-duration="1" class="purecounter"></span>
					<p><?= $this->lang->line('go_down') ?></p>
				</div>
			</div>
			<div class="col-lg-3 col-md-6 mt-5 mt-lg-0">
				<div class="count-box">
					<i class="bi bi-people"></i>
					<span><?= $resume->negociated_text ?></span>
					<p><?= $this->lang->line('negotiated_amount') ?></p>
				</div>
			</div>
		</div>
	</div>
</section>
<section id="about" class="about section-bg">
	<div class="container" data-aos="fade-up">
		<div class="row no-gutters">
			<div class="content col-xl-5 d-flex align-items-stretch">
				<div class="content">
					<h3><?= $this->lang->line('investment_tool') ?></h3>
					<p><?= $this->lang->line('presentation_title') ?></p>
					<a class="about-btn scrollto" href="#pricing">
						<span><?= $this->lang->line('see_plans') ?></span>
						<i class="bx bx-chevron-right"></i>
					</a>
				</div>
			</div>
			<div class="col-xl-7 d-flex align-items-stretch">
				<div class="icon-boxes d-flex flex-column justify-content-center">
					<div class="row">
						<div class="col-md-6 icon-box" data-aos="fade-up" data-aos-delay="100">
							<i class="bx bx-book-reader"></i>
							<h4><?= $this->lang->line('presentation_sub1') ?></h4>
							<p><?= $this->lang->line('presentation_sub1_msg') ?></p>
						</div>
						<div class="col-md-6 icon-box" data-aos="fade-up" data-aos-delay="200">
							<i class="bx bx-repost"></i>
							<h4><?= $this->lang->line('presentation_sub2') ?></h4>
							<p><?= $this->lang->line('presentation_sub2_msg') ?></p>
						</div>
						<div class="col-md-6 icon-box" data-aos="fade-up" data-aos-delay="300">
							<i class="bx bx-pencil"></i>
							<h4><?= $this->lang->line('presentation_sub3') ?></h4>
							<p><?= $this->lang->line('presentation_sub3_msg') ?></p>
						</div>
						<div class="col-md-6 icon-box" data-aos="fade-up" data-aos-delay="400">
							<i class="bx bx-trending-up"></i>
							<h4><?= $this->lang->line('presentation_sub4') ?></h4>
							<p><?= $this->lang->line('presentation_sub4_msg') ?></p>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
<section id="tabs" class="tabs">
	<div class="container aos-init aos-animate" data-aos="fade-up">
		<ul class="nav nav-tabs row d-flex">
			<li class="nav-item col-3">
				<a class="nav-link show active" data-bs-toggle="tab" data-bs-target="#tab-1">
					<i class="ri-bank-line"></i>
					<h4 class="d-none d-lg-block"><?= $this->lang->line('preparation') ?></h4>
				</a>
			</li>
			<li class="nav-item col-3">
				<a class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-2">
					<i class="ri-base-station-line"></i>
					<h4 class="d-none d-lg-block"><?= $this->lang->line('research') ?></h4>
				</a>
			</li>
			<li class="nav-item col-3">
				<a class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-3">
					<i class="ri-briefcase-line"></i>
					<h4 class="d-none d-lg-block"><?= $this->lang->line('acquisition') ?></h4>
				</a>
			</li>
			<li class="nav-item col-3">
				<a class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-4">
					<i class="ri-hand-coin-line"></i>
					<h4 class="d-none d-lg-block"><?= $this->lang->line('liquidation') ?></h4>
				</a>
			</li>
		</ul>
		<div class="tab-content">
			<div class="tab-pane show active" id="tab-1">
				<div class="row">
					<div class="col-lg-6 order-2 order-lg-1 mt-3 mt-lg-0 aos-init aos-animate" data-aos="fade-up" data-aos-delay="100">
						<h3><?= $this->lang->line('preparation_text1') ?></h3>
						<p class="fst-italic"><?= $this->lang->line('preparation_text2') ?></p>
						<ul>
							<li><i class="ri-check-double-line"></i> <?= $this->lang->line('preparation_text3') ?></li>
							<li><i class="ri-check-double-line"></i> <?= $this->lang->line('preparation_text4') ?></li>
							<li><i class="ri-check-double-line"></i> <?= $this->lang->line('preparation_text5') ?></li>
						</ul>
						<p><?= $this->lang->line('preparation_text6') ?></p>
						<p class="fw-bold"><?= $this->lang->line('preparation_text7') ?></p>
					</div>
					<div class="col-lg-6 order-1 order-lg-2 text-center aos-init aos-animate" data-aos="fade-up" data-aos-delay="200">
						<img src="/images/step/1.jpg" alt="" class="img-fluid">
					</div>
				</div>
			</div>
			<div class="tab-pane" id="tab-2">
				<div class="row">
					<div class="col-lg-6 order-2 order-lg-1 mt-3 mt-lg-0">
						<h3><?= $this->lang->line('research_text1') ?></h3>
						<p class="fst-italic"><?= $this->lang->line('research_text2') ?></p>
						<p><?= $this->lang->line('research_text3') ?></p>
						<p><?= $this->lang->line('research_text4') ?></p>
						<p class="fw-bold"><?= $this->lang->line('research_text5') ?></p>
					</div>
					<div class="col-lg-6 order-1 order-lg-2 text-center">
						<img src="/images/step/2.jpg" alt="" class="img-fluid">
					</div>
				</div>
			</div>
			<div class="tab-pane" id="tab-3">
				<div class="row">
					<div class="col-lg-6 order-2 order-lg-1 mt-3 mt-lg-0">
						<h3><?= $this->lang->line('acquisition_text1') ?></h3>
						<p class="fst-italic"><?= $this->lang->line('acquisition_text2') ?></p>
						<p><?= $this->lang->line('acquisition_text3') ?></p>
						<p><?= $this->lang->line('acquisition_text4') ?></p>
						<p class="fw-bold"><?= $this->lang->line('acquisition_text5') ?></p>
					</div>
					<div class="col-lg-6 order-1 order-lg-2 text-center">
						<img src="/images/step/3.jpg" alt="" class="img-fluid">
					</div>
				</div>
			</div>
			<div class="tab-pane" id="tab-4">
				<div class="row">
					<div class="col-lg-6 order-2 order-lg-1 mt-3 mt-lg-0">
						<h3><?= $this->lang->line('liquidation_text1') ?></h3>
						<p class="fst-italic"><?= $this->lang->line('liquidation_text2') ?></p>
						<p><?= $this->lang->line('liquidation_text3') ?></p>
						<p><?= $this->lang->line('liquidation_text4') ?></p>
						<p class="fw-bold"><?= $this->lang->line('liquidation_text5') ?></p>
					</div>
					<div class="col-lg-6 order-1 order-lg-2 text-center">
						<img src="/images/step/4.jpg" alt="" class="img-fluid">
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
<section id="pricing" class="pricing section-bg">
	<div class="container" data-aos="fade-up">
		<div class="section-title">
			<h2><?= $this->lang->line('plans') ?></h2>
		</div>
		<div class="row d-flex justify-content-center">
			<?php foreach($plans as $i => $p){ ?>
			<div class="col-lg-5 col-md-6">
				<div class="box <?= $p->featured ?>" data-aos="fade-up" data-aos-delay="100">
					<h3><?= $p->description ?></h3>
					<h4>
						<?php if ($p->price){ ?>
						<sup><?= $this->lang->line('sol_symbol') ?></sup>
						<?= $p->price ?>
						<span> / <?= $this->lang->line('month') ?></span>
						<?php }else echo $this->lang->line('free'); ?>
					</h4>
					<ul>
						<li><?= $p->indicator." ".$this->lang->line('statistical_indicators') ?></li>
						<?php $services = $p->services; foreach($services as $s){ ?>
						<li class="<?= $s["class"] ?>"><?= $s["desc"] ?></li>
						<?php } ?>
					</ul>
				</div>
			</div>				
			<?php } ?>
		</div>
	</div>
</section>
<section id="portfolio" class="portfolio">
	<div class="container" data-aos="fade-up">
		<div class="section-title">
			<h2><?= $this->lang->line('portfolios') ?></h2>
			<p><?= $this->lang->line('portfolios_text') ?></p>
		</div>
		<div class="row portfolio-container" data-aos="fade-up" data-aos-delay="200">
			<?php foreach($portafolios as $item){ ?>
			<div class="col-lg-4 col-md-6 portfolio-item filter-app">
				<div class="portfolio-wrap">
					<a href="/images/portafolio/<?= $item["file"] ?>" data-gallery="portfolioGallery" class="portfolio-lightbox" title="<?= $item["desc"] ?>">
						<img src="/images/portafolio/<?= $item["file"] ?>" class="img-fluid" alt="">
						<div class="portfolio-info">
							<p><?= $item["desc"] ?></p>
						</div>
					</a>
				</div>
			</div>
			<?php } ?>
		</div>
	</div>
</section>