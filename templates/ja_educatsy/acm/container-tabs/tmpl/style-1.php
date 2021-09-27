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

	$items_position = $helper->get('position');
	$mods = JModuleHelper::getModules($items_position);
?>
<div class="acm-container-tabs" id="mod-<?php echo $module->id ?>">
	<div class="container-tabs-nav">
		<div class="container">

			<!-- BEGIN: TAB NAV -->
			<ul class="nav nav-tabs" role="tablist">
				<?php
				$i = 0;
				foreach ($mods as $mod):
					?>
					<li class="nav-item">
						<a class="nav-link <?php if ($i < 1) echo "active"; ?>" href="#mod-<?php echo $mod->id ?>" role="tab"
							 data-toggle="tab"><?php echo $mod->title ?></a>
					</li>
					<?php
					$i++;
				endforeach
				?>

			</ul>
			<!-- END: TAB NAV -->
		</div>
	</div>

	<!-- BEGIN: TAB PANES -->
	<div class="tab-content">
		<?php
		echo $helper->renderModules($items_position,
			array(
				'style'=>'ACMContainerItems',
				'active'=>0,
				'tag'=>'div',
				'class'=>'tab-pane fade show'
			))
		?>
	</div>
	<!-- END: TAB PANES -->
</div>

<script>
	jQuery(document).ready(function(){
		jQuery('#mod-<?php echo $module->id; ?>').find('li a').each(function(){
			$realid = jQuery(this).attr('href');
			$_realid = $realid+'_<?php echo $module->id ?>';
			jQuery('#mod-<?php echo $module->id; ?>').find('div'+$realid).attr('id', $_realid.replace('#',''));
			jQuery(this).attr('href', $_realid);
		});
	});
</script>