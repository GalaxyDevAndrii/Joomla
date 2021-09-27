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

	$ctaBg = '';

	if($helper->get('cta-bg')) {
		$ctaBg = 'style="background-image: url('.$helper->get('cta-bg').')"';
	};
?>

<div class="acm-cta style-1">
	<div class="container">
		<div class="cta-wrap bg-secondary" <?php echo $ctaBg ;?>>
			<?php if($helper->get('cta-decor')) :?>
				<div class="cta-decor">
					<img src="<?php echo $helper->get('cta-decor') ;?>" alt="" />
				</div>
			<?php endif ;?>

			<div class="cta-content">
				<?php if($helper->get('cta-title')) :?>
					<h2 class="text-white">
						<?php echo $helper->get('cta-title'); ?> 
					</h2>
				<?php endif ;?>

				<?php if($helper->get('cta-desc')) :?>
					<div class="cta-desc text-white">
						<?php echo $helper->get('cta-desc'); ?> 
					</div>
				<?php endif ;?>
			</div>

			<?php if($helper->get('btn-link')) :?>
			<div class="cta-action">
				<a href="<?php echo $helper->get('btn-link'); ?> " class="btn btn-primary">
					<?php echo $helper->get('btn-title'); ?>
				</a>
			</div>
			<?php endif ;?>
		</div>
	</div>
</div>
