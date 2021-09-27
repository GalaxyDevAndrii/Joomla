<?php
use Joomla\CMS\Form\Form as JForm;

$list = $displayData['list'];
$id = $displayData['id'];
$name = $displayData['name'];
$value = $displayData['value'];
$type = $displayData['type'];
$labelEl = $displayData['labelEl'];
$form = $displayData['form'];
$doc = JFactory::getDocument();
//$doc->addStypesheet(T4_ADMIN_URI . '/assets/css/typelist.css');
$doc->addScript(T4PATH_ADMIN_URI . '/assets/js/typelist.js');
if(file_exists(T4PATH_ADMIN . '/assets/js/typelist-' . $type . '.js')) $doc->addScript(T4PATH_ADMIN_URI . '/assets/js/typelist-' . $type . '.js');

?>
<div id="typelist-<?php echo $id ?>" class="typelist" data-type="<?php echo $type ?>">

	<div class="preset-chooser">
		<?php echo $labelEl; ?>
		<div class="typelist-control">
			<select id="<?php echo $id ?>" class="typelist-input" name="<?php echo $name ?>">
			<?php foreach ($list as $item => $status): ?>
				<option value="<?php echo $item ?>"<?php if ($item == $value): ?> selected <?php endif ?> data-status="<?php echo $status ?>"><?php echo $item ?></option>
			<?php endforeach ?>
			</select>
		</div>

		<div class="typelist-action top-actions">
			<span class="t4-btn btn-icon btn-action" data-action="edit" data-tooltip="Edit Selected <?php echo $type ?>"><i class="fal fa-edit"></i>Edit</span>
			<span class="t4-btn btn-icon btn-action" data-action="clone" data-tooltip="Clone" title="Clone Selected <?php echo $type ?>"><i class="fal fa-copy"></i></span>
			<span class="t4-btn btn-icon btn-action btn-last" data-status="loc" data-action="delete" data-tooltip="Delete" title="Delete the selected <?php echo $type ?>"><i class="fal fa-trash-alt"></i></span>
			<span class="t4-btn btn-icon btn-action" data-status="ovr" data-action="delete" data-tooltip="Restore" title="Restore the selected <?php echo $type ?>"><i class="fal fa-redo"></i></span>
		</div>

		<div class="form-action form-edit-action top-actions">
			<span class="t4-btn btn-action btn-success btn-save" data-action="save"><i class="fal fa-save"></i> Save</span>
			<span class="t4-btn btn-icon btn-action" data-action="cancel" data-tooltip="<?php echo JText::_('JCANCEL'); ?>"><i class="fal fa-times"></i></span>
		</div>
	</div>

	<div class="preset-content">
		<div class="typelist-form hide" data-name="edit">
			<div class="form-fields">
			<?php
			$fieldSets = $form->getFieldsets('typelist-' . $type);
			$sub_params_class = (count($fieldSets) == 1) ? 'sub-group-params-one' : "sub-group-params";
			$sub_group = (count($fieldSets) == 1) ? ' sub-group-open' : "";

			if($type == 'layout') {
				$sub_group = '';
				$sub_params_class = "sub-group-params";
			}

			foreach ($fieldSets as $name => $fieldset):
				// Start group
			?>
			<div class="sub-group<?php echo $sub_group;echo " ".$name;?>">
			    <div class="sub-group-inner">
			    	<?php if(count($fieldSets) !== 1):?>
			    	<div class="control-group sub-legend-group">
			            <div class="control-label">
			                <div class="legend has-icon sub-legend<?php echo " ".$name;?>">
			                	<span class="fal fa-<?php echo $fieldset->icon ?>"></span>
			                    <div class="item-content">
			                    	<span class="item-title"><?php echo JText::_($fieldset->label) ?></span>
			                    	<!--span class="item-desc"><?php echo JText::_($fieldset->description) ?></span-->
			                    </div>
			                </div>
			            </div>
		        </div>
						<?php endif?>
	        <div class="<?php echo $sub_params_class;?>">
					<?php foreach ($form->getFieldset($name) as $field) : ?>
						<?php $font_family_class = ($field->getAttribute('type') == "googlefonts") ? ' t4-font-family' : ""; ?>
						<?php $t4checkbox = ($field->getAttribute('type') == 't4radio') ? ' t4-checkbox' : '';?>
						<div class="control-group<?php echo $font_family_class;echo $t4checkbox;?>">
							<div class="control-label">
								<?php echo $field->label; ?>
							</div>
							<div class="controls">
								<?php echo $field->input; ?>
							</div>
						</div>
					<?php endforeach;?>
					</div>
				</div>
			</div>
			<?php endforeach ?>

			</div>

		</div>

		<div class="typelist-form clone-layout hide" data-name="clone">
			<div class="form-fields">
				<label for="layout-new-name"><?php echo \JText::_('T4_TYPE_LIST_ADD_NEW_'.strtoupper($type)); ?></label>
				<input id="layout-new-name" type="text" name="newname" placeholder="<?php echo \JText::_('T4_TYPE_LIST_ADD_PLACEHODER_'.strtoupper($type)); ?>" />
			</div>

			<div class="form-action">
				<span class="t4-btn btn-action btn-primary" data-action="saveclone"><i class="fal fa-save"></i>Save</span>
				<span class="t4-btn btn-action" data-action="cancel" data-type="clone"><i class="fal fa-times"></i>Cancel</span>
			</div>

		</div>

	</div> <!-- // Layout wrap -->

	<?php if($type == 'layout'): ?>
		<div class="t4-position-layout">
			<span class="t4-group-toggle">
			    <input id="t4-preview-layout" class="t4-input" type="checkbox" value="1">
			    <label for="t4-preview-layout"><?php echo JText::_('T4_LAYOUT_SHOW_LAYOUT_PREVIEW'); ?></label>
			</span>
		</div>
	<?php endif?>


</div>
