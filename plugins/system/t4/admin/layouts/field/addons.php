<?php 
$assets = $displayData['assets'];
$id = $displayData['id'];
$name = $displayData['name'];
$value = (array) $displayData['value'];

JFactory::getDocument()->addScript(T4PATH_ADMIN_URI . '/assets/js/addons.js');
$script = "\nvar T4Admin = window.T4Admin || {}; ";
$script .= "\nT4Admin.addons = ". json_encode($assets) . "; ";
JFactory::getDocument()->addScriptDeclaration($script);

// add placeholder one, for adding new addon js
$assets[''] = ['name' => '', 'title' => '', 'assets' => '', 'local' => 1];
?>
<div class="t4-addons-wrap">
	<p class="description"><?php echo JText::_('T4_FIELD_ADDONS_DESC') ?></p>
	
	<div class="addons-items">
		<ul class="addons-list">
		<?php 
		$i = 1;
		foreach ($assets as $aname => $asset): 
			if (!empty($asset['name'])) $aname = $asset['name'];
			$checked = $aname && in_array($aname, $value) ? ' checked' : '';
			$eid = $aname ? ' id="' . $id . $i . '"' : ' data-id="' . $id . '"';
			$liattr = $aname ? (!empty($asset['local']) ? ' class="addon-local"' : '') : ' id="addons-ghost" class="hide addon-local"';
			?>
		<li<?php echo $liattr ?> data-name="<?php echo $aname ?>">
			<label for="<?php echo $id . $i ;?>"><?php echo $aname ?></label>
				<?php if (!empty($asset['local'])): ?>
					<span class="t4-btn btn-action btn-delete" data-action="addons.remove" data-tooltip="<?php echo JText::_('T4_FIELD_ADDONS_REMOVE') ?>"><i class="fal fa-trash-alt"></i></span>
					<span class="t4-btn btn-action btn-edit" data-action="addons.edit" data-tooltip="<?php echo JText::_('T4_FIELD_ADDONS_EDIT') ?>"><i class="fal fa-edit"></i></span>
				<?php endif ?>

				<input<?php echo $eid?> class="t4-input" type="checkbox" name="<?php echo $name ?>[]" value="<?php echo $aname ?>"<?php echo $checked ?>>
			
		</li>
			
		<?php $i++;?>
		<?php endforeach ?>
	</div>
	
	<div class="add-more-addons">
		<span class="t4-btn btn-action" data-action="addons.addasset"><i class="fal fa-plus"></i><?php echo JText::_('T4_FIELD_ADDONS_ADD_ASSET') ?></span>		
	</div>

	<div class="addons-form hide">
		<div class="control-group">
			<div class="control-label"><label><?php echo JText::_('T4_FIELD_ADDONS_ADD_NAME_LABEL') ?></label></div>
			<div class="controls"><input id="addons-name" class="addons-input" name="addons-name" type="text" /></div>
		</div>

		<div class="control-group">
			<div class="control-label"><label><?php echo JText::_('T4_FIELD_ADDONS_CSS_URLS_LABEL') ?></label></div>
			<div class="controls"><textarea id="addons-css" class="addons-input" name="addons-css" rows="3"></textarea></div>
			<div class="control-helper"><?php echo JText::_('T4_FIELD_ADDONS_CSS_URLS_DESC') ?></div>
		</div>

		<div class="control-group">
			<div class="control-label"><label><?php echo JText::_('T4_FIELD_ADDONS_JS_URLS_LABEL') ?></label></div>
			<div class="controls"><textarea id="addons-js" class="addons-input" name="addons-js" rows="3"></textarea></div>
			<div class="control-helper"><?php echo JText::_('T4_FIELD_ADDONS_JS_URLS_DESC') ?></div>
		</div>

		<div class="addon-actions">
			<span class="t4-btn btn-action btn-primary" data-action="addons.save"><?php echo JText::_('T4_FIELD_ADDONS_SAVE') ?></span>
			<span class="t4-btn btn-action" data-action="addons.cancel"><?php echo JText::_('T4_FIELD_ADDONS_CANCEL') ?></span>
		</div>
	</div>
</div>