<?php
	/**
	 * ------------------------------------------------------------------------
 * JA Educatsy Template
 * ------------------------------------------------------------------------
 * Copyright (C) 2004-2018 J.O.O.M Solutions Co., Ltd. All Rights Reserved.
 * @license - Copyrighted Commercial Software
 * Author: J.O.O.M Solutions Co., Ltd
 * Websites:  http://www.joomlart.com -  http://www.joomlancers.com
 * This file may not be redistributed in whole or significant part.
 * ------------------------------------------------------------------------
	*/
	defined('_JEXEC') or die;

	// Sub Color
	$subColor = $params->get('sub-color', 'normal');
	$titleColor = $params->get('title-color', 'normal');
	$titleStyle = $params->get('title-style', '1');

	// Sub Align
	$titleSpace = $params->get('title-space');
	// Sub Heading
	$moduleSub = '';
	if($params->get('sub-heading')) {
		$moduleSub = '<h2 class="sub-heading text-'.$subColor.'">'.$params->get('sub-heading').'</h2>';
	}

	// Mod Title
	$modTitle = '';
	if($module->showtitle != 0) {
		$modTitle = '<h3 class="section-title title-style-'.$titleStyle.' text-'.$titleColor.'"><span>'.$module->title.'</span></h3>';
	}
	$mod            = $module->id;
?>

<div id="acm-features-<?php echo $mod; ?>" class="acm-features style-2">
	<div class="container">
		<div class="acm-features-inner">
			<div class="row no-gutters">
				<div class="col-lg-6 order-lg-2 mb-4 mb-lg-0 features-image">
					<img src="<?php echo $helper->get('img-features'); ?>" alt="" />
				</div>

				<div class="col-lg-6" >
					<div class="features-content-wrap">
						<div class="features-content">
							<?php if($moduleSub || $modTitle) : ?>
							<div class="section-title-wrap space-<?php echo $titleSpace ;?>">
								<!-- Module Title -->
								<?php if($modTitle) : ?>
									<?php echo $modTitle ?>
								<?php endif; ?>

								<?php if ($moduleSub): ?>
									<?php echo $moduleSub; ?>
								<?php endif; ?>
								<!-- // Module Title -->
							</div>
							<?php endif ; ?>

							<?php if($helper->get('desc-features')) :?>
								<div class="desc-features">
									<?php echo $helper->get('desc-features') ;?>
								</div>
							<?php endif ;?>
							<!--- //Features Content -->

							<!-- Features Actions -->
							<?php if($helper->get('btn-link')) :?>
							<div class="actions">
								<a href="<?php echo $helper->get('btn-link') ;?>" class="btn btn-primary">
									<?php echo $helper->get('btn-title') ;?>
								</a>
							</div>
							<!-- // Features Actions -->
							<?php endif ;?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
