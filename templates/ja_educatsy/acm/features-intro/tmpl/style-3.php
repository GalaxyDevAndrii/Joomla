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
	$textAlign 			= $params->get('sub-align');
	$featuresIntro 	= $helper->get('block-intro');
	$count 					= $helper->getRows('data.title');
	$column 				= $helper->get('columns');
	$slide 					= $helper->get('slide');
?>

<div class="acm-features style-3">
	<?php if($helper->get('block-intro')) :?>
	<div class="container features-intro text-<?php echo $textAlign ;?>">
		<?php echo $helper->get('block-intro') ;?>
	</div>
	<?php endif ;?>

	<div id="acm-feature-<?php echo $module->id; ?>">
		<div class="owl-carousel owl-theme">
			<?php
				for ($i=0; $i<$count; $i++) :
			?>
				<div class="features-item bg-primary-light">
					<div class="features-item-inner">
						<?php if($helper->get('data.intro-img', $i)) : ?>
						<div class="features-image">
							<img src="<?php echo $helper->get('data.intro-img', $i) ?>" alt="<?php echo $helper->get('data.title', $i) ?>" />
						</div>
						<?php endif ; ?>

						<?php if($helper->get('data.title', $i)) : ?>
							<h3>
								<?php if($helper->get('data.link', $i)) : ?>
									<a href="<?php echo $helper->get('data.link', $i) ?>" title="" class="heading-link">
								<?php endif ; ?>

								<?php echo $helper->get('data.title', $i) ?>

								<?php if($helper->get('data.link', $i)) : ?>
									</a>
								<?php endif ; ?>
							</h3>
						<?php endif ; ?>

						<?php if($helper->get('data.sub-title', $i)) : ?>
						<div class="sub-title text-primary">
							<?php echo $helper->get('data.sub-title', $i) ?>
						</div>
						<?php endif ; ?>

						<?php if($helper->get('data.description', $i)) : ?>
							<div class="features-desc"><?php echo $helper->get('data.description', $i) ?></div>
						<?php endif ; ?>
					</div>
				</div>
			<?php endfor ?>
		</div>
	</div>

	
</div>

<script>
(function($){
  jQuery(document).ready(function($) {
    $("#acm-feature-<?php echo $module->id; ?> .owl-carousel").owlCarousel({
      addClassActive: true,
      items: <?php echo $column; ?>,
      itemsScaleUp : true,
      nav : true,
      navText : ["<span class='fa fa-angle-left'></span>", "<span class='fa fa-angle-right'></span>"],
      dots: <?php echo ($count > $column) ? 'true' : 'false' ?>,
      autoPlay: false,
      margin: 36,
      responsive : {
				0 : {
			    items: 1,
			    autoHeight: true
				},
				767 : {
			    items: 2,
				},
				1200 : {
			    items: <?php echo $column; ?>
				}
			}
    });
  });
})(jQuery);
</script>