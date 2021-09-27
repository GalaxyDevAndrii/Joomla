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
$group_types        = $displayData['group_types'];
$activetype         = $displayData['activetype'];
$activetypename     = $displayData['activetypename'];
$activelayout       = $displayData['activelayout'];
$activetypetitle    = $displayData['activetypetitle'];
$activelayouttitle  = $displayData['activelayouttitle'];
$group_layouts      = $displayData['group_layouts'];
?>
<?php JHTML::_('behavior.modal'); ?>

<div class="control-group jatools-header">

	<div class="control-label">
		<label id="jatools-type-lbl"
               for="jatools-type"
               class="hasTip"
               title="<?php echo JText::_('MOD_JA_ACM_TYPE_DESC') ?>"><?php echo JText::_('MOD_JA_ACM_TYPE_LABEL') ?>
        </label>
	</div>

	<div class="controls">
		<p><?php echo $activetype && $activelayout ? $activetypetitle . ' : ' . $activelayouttitle : JText::_('MOD_JA_ACM_SELECT_LAYOUT') ?></p>
		<button type="button"
                id="jatools-select-layout-toggle"
                class="select-btn">
			<i class="fa fa-list icon-list"></i>
		</button>
	</div>

</div>


<div id="jatools-select-layout-form" class="hide" title="<?php echo JText::_('MOD_JA_ACM_SELECT_LAYOUT') ?>">
	<div class="jatools-select-layout-body form-horizontal" style="min-height: 200px;overflow-y: visible;">
		<div class="control-group">

			<div class="control-label">
				<label id="jatools-type-lbl"
                       for="jatools-type"
                       class="hasTip"
                       title="<?php echo JText::_('MOD_JA_ACM_TYPE_DESC') ?>"><?php echo JText::_('MOD_JA_ACM_TYPE_LABEL') ?>
                </label>
			</div>

			<div class="controls">
				<select id="jatools-type" name="jatools-type">
					<option value="" selected="selected"><?php echo JText::_('MOD_JA_ACM_LAYOUT_DEFAULT') ?></option>
					<?php foreach ($group_types as $tpl => $types) : ?>
						<optgroup id="jatools-type-<?php echo $tpl ?>" label="<?php if ($tpl == '_'): ?>---From Module---<?php else: ?>---From <?php echo $tpl ?> Template---<?php endif ?>">
							<?php foreach ($types as $type => $title): ?>
								<option	value="<?php echo $tpl ?>:<?php echo $type ?>"<?php if ($activetype == $tpl . ':' . $type): ?> selected="selected"<?php endif ?>><?php echo $title ?></option>
							<?php endforeach ?>
						</optgroup>
					<?php endforeach ?>
				</select>
			</div>
		</div>

		<?php foreach ($group_layouts as $type => $layouts) : ?>
			<div class="control-group jatools-layouts jatools-layouts-<?php echo $type ?><?php if ($activetypename != $type): ?> hide<?php endif ?>">

				<div class="control-label">
					<label id="jatools-layout-<?php echo $type ?>-lbl"
                           for="jatools-layout-<?php echo $type ?>"
                           class="hasTip"
                           title="<?php echo JText::_('MOD_JA_ACM_LAYOUT_DESC') ?>"><?php echo JText::_('MOD_JA_ACM_LAYOUT_LABEL') ?>
                    </label>
				</div>

				<div class="controls">
					<select id="jatools-layout-<?php echo $type ?>" name="jatools-layout-<?php echo $type ?>">
						<?php foreach ($layouts as $layout): ?>
							<option	value="<?php echo $layout ?>"<?php if ($activetypename == $type && $activelayout == $layout): ?> selected="selected"<?php endif ?>><?php echo $layout ?></option>
						<?php endforeach ?>
					</select>
				</div>
			</div>
		<?php endforeach ?>

	</div>

</div>


<script>
	(function ($) {
		var $form = $('#jatools-select-layout-form');
		new jBox('Confirm', {
			attach: $('#jatools-select-layout-toggle'),
			title: 'Select ACM Block!',
			content: $('#jatools-select-layout-form'),
			width: 600,
			height: 400,
			confirmButton: 'Update',
			cancelButton: 'Close',
			confirm: function() {
				layoutSelected();
			}
		});

		// switch type
		$('#jatools-type').on('change', function () {
			var tmp = $(this).val().split(':'),
				selectedType = tmp.length == 1 ? tmp[0].trim() : tmp[1].trim();

			// show layouts for selected type
			$form.find('.jatools-layouts').addClass('hide');
			$form.find('.jatools-layouts-' + selectedType).removeClass('hide');
		});

		// save selection
		var layoutSelected = function () {
			// dismiss modal
			var newType         = $form.find('#jatools-type').val(),
				tmp             = newType.split(':'),
				selectedType    = tmp.length == 1 ? tmp[0].trim() : tmp[1].trim(),
				newLayout       = $form.find('#jatools-layout-' + selectedType).val(),
				activeType      = '<?php echo $activetype ?>',
				activeLayout    = '<?php echo $activelayout ?>';

			// check if selected && new value
			if (newType && newLayout && (newType != activeType || newLayout != activeLayout)) {
				// store temporary value in cookie, and reload form
				var expire = new Date();
				expire.setTime(expire.getTime() + 3600000);
				document.cookie = "activetype=" + newType + '::' + newLayout + "; expires=" + expire.toGMTString() + "; path=/";
				window.location.reload(true);
			}
		};

	})(jQuery)
</script>