<?php
/**
 * ------------------------------------------------------------------------
 * JA ACM Module
 * ------------------------------------------------------------------------
 * Copyright (C) 2004-2018 J.O.O.M Solutions Co., Ltd. All Rights Reserved.
 * @license - GNU/GPL, http://www.gnu.org/licenses/gpl.html
 * Author: J.O.O.M Solutions Co., Ltd
 * Websites: http://www.joomlart.com - http://www.joomlancers.com
 * ------------------------------------------------------------------------
 */
// add css
$configform     = $displayData['config-form'];
$layoutform     = $displayData['layout-form'];
$description    = $displayData['description'];
$activetype     = $displayData['activetype'];
$activetypename = $displayData['activetypename'];
$activelayout   = $displayData['activelayout'];

// add button to toolbar
$bar = JToolbar::getInstance('toolbar');
// Add an apply button
$bar->appendButton('Standard', 'advanced', 'Advanced', 'advanced', false);
?>

<div id="ja-acm-admin" class="ja-acm-admin joomla<?php echo substr(JVERSION, 0, 1) ?>" data-activetype="<?php echo $activetype ?>" data-activelayout="<?php echo $activelayout ?>">

	<?php echo $layoutform ?>

	<div id="jatools-<?php echo $activetypename ?>" class="jatools-layout-config">

		<?php if ($description): ?>
			<p class="jatools-layout-desc">
                <?php echo $description ?>
            </p>
		<?php endif ?>

		<?php echo $configform ?>

	</div>

</div>

<div id="acm-advanced-form" class="hide" title="<?php echo JText::_('MOD_JA_ACM_ADVANCED_FORM_TITLE') ?>">
	<textarea id="acm-advanced-input"></textarea>
</div>


<script>
	jaToolsInit(jQuery);
</script>
