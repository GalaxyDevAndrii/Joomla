<?php
use T4Admin\RowColumnSettings;

$palettes = $displayData['palettes'];
$id = $displayData['input']['id'];
$name = $displayData['input']['name'];
$value = $displayData['input']['value'];
$fields = $displayData['fields'];
$paletteDef = $displayData['palettesDef'];
$userColor_class = '';
$layout = $displayData['layout'];
$layoutCls = " t4-layout-palettes";
if($layout == 'theme') $layoutCls = ' t4-theme-palettes';
?>
<div class='pattern-list t4-color-pattern<?php echo $layoutCls;?>'>
	<?php if($layout == 'palettes'):?>
	<div class="pattern none" <?php echo RowColumnSettings::getSettings((object)$paletteDef); ?>>
		<div class="pattern-inner">
			<div class="pattern-header">
				<h4 class="pattern-title"><?php echo JText::_('T4_PATTERN_TITLE_NONE');?></h4>
			</div>
			<p><?php echo JText::_('T4_PATTERN_DEFAULT');?></p>
		</div>
	</div>
<?php endif;?>
<?php if(!empty($palettes)):?>
	<?php foreach ($palettes as $pname => $palette) : ?>

		<div class="pattern <?php echo $pname;?>" <?php echo $dataSeting = RowColumnSettings::getSettings((object)$palette); ?> >
			<div class="pattern-inner"<?php if($layout == 'palettes') { echo ' style ="background-color : '. $palette['background_color'] .';"';} ?>>
				<div class="pattern-header">
					<h4 class="pattern-title" <?php if($layout == 'palettes') echo 'style ="color: '. $palette['text_color'] .';"'; ?>><?php echo $palette['title'];?></h4>
					<?php if($layout == 'theme'):?>
					<div class="pattern-actions">
						<ul class="pattern-actions-list">
							<li><a class="pt-color-edit" data-action="edit" href="#" data-tooltip="<?php echo JText::_('JACTION_EDIT');?>"><i class="fal fa-edit"></i></a></li>
							<li><a class="pt-color-clone" href="#" data-action="clone"  data-tooltip="<?php echo JText::_('COLOR_PATTERN_CLONE');?>"><i class="fal fa-copy fa-fw"></i></a></li>
							<?php $orgHidden = ($palette['status'] == 'org') ? ' class="hidden"' : ''; ?>
							<?php if ($palette['status'] == 'loc') : ?>
							<li><a class="pt-color-del" href="#" data-action="remove" data-tooltip="<?php echo JText::_('JACTION_DELETE');?>"><i class="fal fa-trash-alt fa-fw"></i></a></li>
							<?php else: ?>
							<li<?php echo $orgHidden;?>><a class="pt-color-del" data-action="restore" href="#" data-tooltip="<?php echo JText::_('JACTION_RESTORE');?>"><i class="fal fa-redo"></i></a></li>
							<?php endif ?>
						</ul>
					</div>
					<?php endif;?>
				</div>
				<ul class="color-list" <?php if($layout == 'palettes') echo 'style ="display: none;"'; ?>>
					<?php foreach ($fields as $field): ?>
					<?php
						$dataVal = $field['value']; 
						if (isset($palette[$field['name']])){
							$dataVal = $palette[$field['name']];
						}
					?>
						<li><span class="<?php echo $field['name']; ?>" data-title="<?php echo str_replace('_', ' ', $field['title']);?>" style="background: <?php echo $dataVal;?>;">&nbsp;</span></li>
					<?php endforeach ?>
				</ul>
			</div>
		</div>
	<?php endforeach;?>
<?php endif; ?>
</div>
<?php if($layout == 'palettes'): ?>
<div class="t4-layout-pl-preview">
	<?php echo JText::_('COLOR_PATTERN_EXAMPLE'); ?>
	<div class="pl-preview__input">
	<?php foreach ($fields as $field) : ?>
		<div class="control-group <?php echo $field['name']; ?>">
			<div class="control-label">
				<label><?php echo str_replace(" color", "", $field['title']); ?></label>
			</div>
			<div class="controls">
				<input 	type="text" name="<?php echo $field['name']; ?>" class="t4-custom-color-spec t4-palette-color-spec t4-pattern <?php echo $field['class']; ?>" data-attrname="<?php echo $field['name']; ?>" value="<?php echo $field['value']; ?>" data-val="" readonly="true" />
			</div><!-- // Controls -->

		</div>
	<?php endforeach;?>
	</div>
</div>
<?php endif; ?>
<?php if($layout == 'theme'):?>

<div class="add-more-palettes">
	<span class="t4-btn btn-action pt-color-create" data-action="palette.add"><i class="fal fa-plus"></i><?php echo JText::_('T4_THEM_PALETTE_ADD_NEW');?></span>
	<div class="pattern pattern-clone hidden" data-class="" data-status="loc" data-title="" data-link_hover_color="#ffffff" data-link_color="#1a61ab" data-text_color="#ffff" data-heading_hover_color="#1a61ab" data-heading_color="#ff0000" data-background_color="#000">
		<div class="pattern-inner">
			<div class="pattern-header">
				<h4 class="pattern-title"><?php echo $palette['title'];?></h4>
				<?php if($layout == 'theme'):?>
				<div class="pattern-actions">
					<ul class="pattern-actions-list">
						<li><a class="pt-color-edit" href="#" data-tooltip="<?php echo JText::_('JACTION_EDIT');?>"><i class="fal fa-edit"></i></a></li>
						<li><a class="pt-color-clone" href="#" data-tooltip="<?php echo JText::_('COLOR_PATTERN_CLONE');?>"><i class="fal fa-copy fa-fw"></i></a></li>
						<li><a class="pt-color-del" href="#" data-tooltip="<?php echo JText::_('JACTION_DELETE');?>"><i class="fal fa-trash-alt fa-fw"></i></a></li>
					</ul>
				</div>
				<?php endif;?>
			</div>
			<ul class="color-list">
				<?php foreach ($fields as $field): ?>
				<?php
					$dataVal = $field['value']; 
					if (isset($palette[$field['name']])){
						$dataVal = $palette[$field['name']];
					}
				?>
					<li><span class="<?php echo $field['name']; ?>" data-title="<?php echo str_replace('_', ' ', $field['title']);?>" style="background: <?php echo $dataVal;?>;">&nbsp;</span></li>
				<?php endforeach ?>
			</ul>
		</div>
	</div>
</div>
<!-- action-t4-modal-close -->
<div class="t4-palettes-modal" style="display: none;">
	<div class="t4-modal-overlay"></div>
	<div class="t4-modal t4-pattern-row" data-target="#">
		<div class="t4-modal-header">
	    <span class="t4-modal-header-title"><i class="fal fa-cog"></i>Palettes Settings</span>
	    <a href="#" class="action-t4-modal-close"><span class="fal fa-times"></span></a>
	  </div>
		  <div class="t4-modal-inner t4-patterns-inner">
		  	<div class="t4-modal-content">
		  		<div class="row">
						<div class="config_pattern col-7">
							<div class="control-group title">
								<div class="control-label">
									<label><?php echo JText::_('T4_PATTERN_TITLE'); ?></label>
								</div>
								<div class="controls">
									<input 	type="text" name="title" class="t4-pattern title" data-attrname="title" value="" data-val="" placeholder="<?php echo JText::_('T4_PATTERN_TITLE_PLACEHOLDER'); ?>" />
								</div>
								<span class="t4-palette-error error-title-null alert alert-warning" style="display: none"><?php echo JText::_('T4_THEME_PALETTE_TITLE_NULL');?></span>
								<span class="t4-palette-error error-name-exist alert alert-warning" style="display: none"><?php echo JText::_('T4_THEME_PALETTE_NAME_EXIST');?></span>
								
							</div>
						<?php foreach ($fields as $field) : ?>
							<div class="control-group <?php echo $field['name']; ?>">
								<div class="control-label">
									<label><?php echo $field['title']; ?></label>
								</div>
								<div class="controls">
									<input 	type="text" name="<?php echo $field['name']; ?>" class="t4-custom-color-spec t4-pattern <?php echo $field['class']; ?>" data-attrname="<?php echo $field['name']; ?>" value="<?php echo $field['value']; ?>" data-val="" readonly="" />
								</div><!-- // Controls -->

							</div>
						<?php endforeach;?>
						</div>
						<div class="t4-pattern-preview col-5">
							<?php echo JText::_('COLOR_PATTERN_EXAMPLE'); ?>
						</div>
					</div>
		  	</div>
		  </div>
      <div class="t4-modal-footer">
        <a href="#" class="btn btn-secondary btn-xs t4-patterns-cancel"><span class="fal fa-times"></span> Cancel</a>
        <a href="#" class="btn btn-success btn-xs t4-patterns-apply" data-flag="mega-setting"><span class="fal fa-check"></span> Apply</a>
      </div>
	</div>
</div>

<?php endif;?>
<input id="<?php echo $id?>" class="t4-layout t4-input-color_pattern" name="<?php echo $displayData['input']['name'] ?>" value="<?php echo htmlspecialchars($value);?>" data-attrname="color_pattern" type="hidden" hidden="hidden" />
