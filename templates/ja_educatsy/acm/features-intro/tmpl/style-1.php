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
		$moduleSub = '<h1 class="sub-heading text-primary text-'.$subColor.'">'.$params->get('sub-heading').'</h1>';
	}

	// Mod Title
	$modTitle = '';

	if($module->showtitle != 0) {
		$modTitle = '<h3 class="section-title title-style-'.$titleStyle.' text-'.$titleColor.'"><span>'.$module->title.'</span></h3>';
	}

	$mod            = $module->id;

	$count 					= $helper->getRows('data.ft-title');

	$items_position = $helper->get('position');
?>

<div id="acm-features-<?php echo $mod; ?>" class="acm-features style-1 bg-secondary <?php echo $helper->get('features-style'); ?>">
	<div class="container">
		<div class="acm-features-inner">
			<div class="row no-gutters">
				<div class="col-lg-7" >
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

							<div class="features-mod">
								<?php
									echo $helper->renderModules($items_position,
										array(
											'style'=>'none',
											'tag'=>'div',
											'class'=>''
										))
									?>
							</div>

							<?php if($count) :?>
								<ul class="features-list">
									<?php
										for ($i=0; $i<$count; $i++) :
									?>
										<li><?php echo $helper->get('data.ft-title', $i) ?></li>
									<?php endfor ?>
								</ul>
							<?php endif ;?>
							<!--- //Features Content -->
						</div>
					</div>
				</div>

				<div class="features-image">
					<img src="<?php echo $helper->get('img-features'); ?>" alt="" />
				</div>
			</div>
		</div>
	</div>
</div>