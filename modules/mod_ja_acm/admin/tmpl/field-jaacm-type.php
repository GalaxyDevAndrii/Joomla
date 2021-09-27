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
$form       = $displayData['form'];
$fieldsets  = $displayData['fieldsets'];
$sampledata = $displayData['sample-data'];
$helper = $displayData['helper'];
?>

<?php
if (!is_array($fieldsets)) return;
foreach ($fieldsets as $name => $fieldset) :
	$multiple           = isset($fieldset->multiple) ? $fieldset->multiple : false;
	$support_layouts    = isset($fieldset->layouts) ? ' data-layouts="' . $fieldset->layouts . '"' : '';
	$horizontal         = isset($fieldset->horizontal) ? $fieldset->horizontal : false;
?>

<input name="jatools-sample-data" type="hidden" value="<?php echo htmlspecialchars($sampledata, ENT_COMPAT, 'UTF-8') ?>" data-ignoresave="1" />

<div class="jatools-group clearfix<?php if ($multiple): ?> jatools-multiple<?php endif ?><?php if ($horizontal): ?> jatools-hoz<?php endif ?>"<?php echo $support_layouts ?>>

    <!-- Fieldset Header-->
	<div class="jatools-group-header clearfix">
        <!-- Display Field Header-->
		<h3 class="fieldset-title">
            <?php echo JText::_($fieldset->label) ?>
        </h3>
        <!-- Display Field Description-->
		<p class="fieldset-desc">
            <?php echo JText::_($fieldset->description) ?>
        </p>
	</div>

	<?php
	$fields = $form->getFieldset($name);
	?>

    <!-- Fieldset Body-->

	<div class="jatools-row clearfix">
		<?php foreach ($fields as $field) : ?>
			<?php
			if (!version_compare(JVERSION, '4.0', 'ge'))
				if ($helper->get($field->name) !== NULL)
					$field->form->setValue($helper->get($field->name));
			$layouts = $field->element['layouts'] ? ' data-layouts="' . $field->element['layouts'] . '"' : '';
			$label = $field->getLabel();
			$input = $field->getInput();
			?>
			<div class="control-group"<?php echo $layouts ?>>
				<?php if ($label) : ?>
					<div class="control-label"><?php echo $label ?></div>
					<div class="controls"><?php echo $input ?></div>
				<?php else : ?>
					<?php echo $input ?>
				<?php endif ?>
			</div>
		<?php endforeach ?>
	</div>

	<?php if ($multiple): ?>
	<div class="jatools-row-actions clearfix">
		<div class="btn btn-primary jatools-btn-add"><?php echo JText::_('MOD_JA_ACM_BTN_ADD') ?></div>
	</div>

	<div class="btn btn-danger jatools-btn-del"><?php echo JText::_('MOD_JA_ACM_BTN_DEL') ?></div>
	<?php endif ?>

</div>

<?php endforeach ?>