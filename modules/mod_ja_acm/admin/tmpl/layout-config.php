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
$form           = $displayData['form'];
$fieldsets      = $displayData['fieldsets'];
//$fieldsets_html = $displayData['fieldsets_html'];
$description    = $displayData['description'];
$layouts        = $displayData['layouts'];
$type           = $displayData['type'];
$sampledata     = $displayData['sample-data'];
?>

<input name="jatools-sample-data" type="hidden" value="<?php echo htmlspecialchars($sampledata, ENT_COMPAT, 'UTF-8') ?>" data-ignoresave="1" />

<!-- Layout field -->
<div class="control-group jatools-subheader">
	<div class="control-label">
        <label id="jatools-layout-<?php echo $type ?>-lbl" for="jatools-layout-<?php echo $type ?>" class="hasTip" title="<?php echo JText::_('MOD_JA_ACM_LAYOUT_DESC') ?>">
            <?php echo JText::_('MOD_JA_ACM_LAYOUT_LABEL') ?>
        </label>
    </div>
	<div class="controls">
		<select id="jatools-layout-<?php echo $type ?>" class="jatools-layouts" name="jatools-layout-<?php echo $type ?>">
			<?php foreach ($layouts as $layout): ?>
				<option value="<?php echo $layout ?>"><?php echo $layout ?></option>
			<?php endforeach ?>
		</select>
	</div>
</div>

<?php if ($description): ?>
    <p class="jatools-layout-desc">
        <?php echo $description ?>
    </p>
<?php endif ?>

<?php
if (!is_array($fieldsets)) return;
foreach ($fieldsets as $name => $fieldset) :
	$multiple        = isset($fieldset->multiple) ? $fieldset->multiple : false;
	$support_layouts = isset($fieldset->layouts) ? ' data-layouts="' . $fieldset->layouts . '"' : '';
	$horizontal      = isset($fieldset->horizontal) ? $fieldset->horizontal : false;
?>

<div class="jatools-group clearfix<?php if ($multiple): ?> jatools-multiple<?php endif ?><?php if ($horizontal): ?> jatools-hoz<?php endif ?>"<?php echo $support_layouts ?>>

	<div class="jatools-group-header clearfix">
		<h3 class="fieldset-title">
            <?php echo JText::_($fieldset->label) ?>
        </h3>
		<p class="fieldset-desc">
            <?php echo JText::_($fieldset->description) ?>
        </p>
	</div>

	<?php /* ?>
	<div class="jatools-row clearfix">
		<?php echo $fieldsets_html [$name] ?>
	</div>
 <?php */ ?>

	<?php include dirname(__FILE__) . '/layout-fieldset' . ($horizontal ? '-hoz' : '') . '.php' ?>

	<?php if ($multiple): ?>
        <div class="jatools-row-actions clearfix">
            <div class="btn btn-primary jatools-btn-add">
                <?php echo JText::_('MOD_JA_ACM_BTN_ADD') ?>
            </div>
        </div>

        <div class="btn btn-danger jatools-btn-del">
            <?php echo JText::_('MOD_JA_ACM_BTN_DEL') ?>
        </div>
	<?php endif ?>

</div>

<?php endforeach ?>