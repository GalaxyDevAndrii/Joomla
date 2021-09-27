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
	$modTitle       = $module->title;
	$moduleSub 			= $params->get('sub-heading');
	$featuresIntro 	= $helper->get('block-intro');
	$count 					= $helper->getRows('data.title');
	$column 				= $helper->get('columns');
	$slide 					= $helper->get('slide');
?>

<div class="acm-features style-4">
	<?php if($helper->get('block-intro')) :?>
	<div class="container features-intro">
		<?php echo $helper->get('block-intro') ;?>
	</div>
	<?php endif ;?>

	<div id="acm-feature-<?php echo $module->id; ?>">
		<?php
			for ($i=0; $i<$count; $i++) :

			if ($i%$column==0) echo '<div class="row">'; 
		?>
			<div class="features-item col-lg-<?php echo (12/$column) ;?>">
				<div class="features-item-inner">
					<div class="features-num bg-light-primary">
						<span><?php if($i<=9) echo '0';?><?php echo $i+1 ; ?></span>
					</div>

					<?php if($helper->get('data.title', $i)) : ?>
						<h3>
							<?php echo $helper->get('data.title', $i) ?>
						</h3>
					<?php endif ; ?>

					<?php if($helper->get('data.sub-title', $i)) : ?>
					<div class="sub-title">
						<?php echo $helper->get('data.sub-title', $i) ?>
					</div>
					<?php endif ; ?>

					<?php if($helper->get('data.description', $i)) : ?>
						<div class="features-desc"><?php echo $helper->get('data.description', $i) ?></div>
					<?php endif ; ?>
				</div>
			</div>

			<?php if ( ($i%$column==($column-1)) || $i==($count-1) )  echo '</div>'; ?>
		<?php endfor ?>
	</div>
</div>
