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
	$count 					= $helper->getRows('data.author');
	$column 				= 1;
?>

<div class="acm-testimonial style-1">
	<div class="container">
		<div class="testimonial-item-wrap">
			<div class="testimonial-content">
				<div id="acm-testimonial-<?php echo $module->id; ?>">
					<div class="owl-carousel owl-theme">
						<?php 
							for ($i=0; $i<$count; $i++) : 
						?>
							<div class="testimonial-item">
								<?php if($helper->get('data.avatar', $i)) : ?>
								<div class="testimonial-avatar">
									<img src="<?php echo $helper->get('data.avatar', $i) ;?>" alt="" />
								</div>
								<?php endif ; ?>

								<div class="testimonial-item-inner">
									<div class="testimonial-top">
										<!-- Description -->
										<?php if($helper->get('data.desc', $i)) : ?>
											<div class="t-desc">
												<?php echo $helper->get('data.desc', $i) ?>
											</div>
										<?php endif ; ?>

										<!-- Author -->
											<?php if($helper->get('data.author', $i)) : ?>
												<div class="t-author"><?php echo $helper->get('data.author', $i) ?> </div>
											<?php endif ; ?>
									</div>
								</div>
							</div>
						<?php endfor ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<script>
(function($){
  jQuery(document).ready(function($) {
    $("#acm-testimonial-<?php echo $module->id; ?> .owl-carousel").owlCarousel({
    	items: 1,
			addClassActive: true,
			itemsScaleUp : true,
			nav : true,
			navText : ["<i class='fas fa-chevron-left'></i>", "<i class='fas fa-chevron-right'></i>"],
			dots: false,
			loop: true,
			autoPlay: false
    });
  });
})(jQuery);
</script>