<?php
use T4Admin\Settings AS Settings;
use T4Admin\MegaSettings AS MegaSettings;
use T4Admin\RowColumnSettings AS RowColumnSettings;

$data = $displayData['data'];
$inputId = $displayData['id'];
$inputName = $displayData['name'];
$inputVal = $displayData['value'];
$Megamenu = MegaSettings::getMenu();
$option = [''=> JText::_('T4_SELECT_MENU')];
$menuOpt = MegaSettings::getMenuType();
$option = array_merge($option, $menuOpt);
$default = (isset($data)) ? key($data) : '';

echo Settings::getMegaRowConfig();
echo Settings::getMegaItemSettings();
?>
<div class='select-menu-type'>
	<label for="typelist-navigationmenu_type"><?php echo \JText::_('T4_NAVIGATION_MENU_TYPE');?></label>
	<?php echo $html = \JHTML::_('select.genericlist',$option,'typelist-navigation[menu_type]','class="megamenu menu_type"',$value = '',$text = '',$default);?>
</div>
<div class="t4-megamenu-builder">
	<div class="menu_items">
		<?php echo MegaSettings::getMenuItems(); ?>
	</div>
	<div class="item-config">
		<div class="menu-item-extra">
			<label for="extra"><?php echo JText::_('T4_MENU_EXTRA_CLASS');?></label>
			<input id="extra" type="text" name="extra_class" value="" class="t4-item t4-extra-class" />
		</div>
		<div class="menu-item-icon">
			<label for="icons"><?php echo JText::_('T4_MENU_ITEM_ICON');?></label>
			<input id="icons" type="text" name="t4_item_icon" value="" class="t4-item  t4-item-icon" />
		</div>
		<div class="menu-item-caption">
			<label for="caption"><?php echo JText::_('T4_MENU_ITEM_CAPTION');?></label>
			<input id="caption" type="text" name="t4_item_caption" value="" class="t4-item t4-item-caption" />
		</div>
	</div>
	<div class="t4-menu-layout">
		<!-- Layout Builder Section -->
		<div class="t4-menu-layout-builder" >
			<div style="display: none">
			    <div id="t4-mega-section" class="t4-mega-section">
				    <div class="t4-meganeu-settings clearfix">
				        <div class="pull-right">
				            <ul class="t4-row-option-list">
				                <li><a class="t4-move-row" href="#"><i class="fal fa-arrows-alt"></i></a></li>
				                <li><a class="t4-meganeu-row-options" href="#"><i class="fal fa-cog fa-fw"></i></a></li>
				                <li><a class="t4-remove-row-mega" href="#"><i class="fal fa-trash-alt fa-fw"></i></a></li>
				            </ul>
				        </div>
				    </div>
				    <div class="t4-row-container">
				        <div class="row ui-sortable">
				            <div class="t4-col t4-mega-col col-md" data-name="" data-type="block" data-col="auto">
				                <div class="col-inner item-build clearfix"><span class="t4-column-title">None</span><span class="t4-col-remove hidden" title="Remove column" data-content="Remove column"><i class="fal fa-minus"></i> </span><a class="t4-item-options" href="#"><i class="fal fa-cog fa-fw"></i></a></div>
				            </div>
				        </div>
				    </div>
				</div>
			</div>
			<?php if(isset($data)):?>
			<?php foreach ($Megamenu as $type =>  $Items):?>
		   	<div class="t4-megamenu t4-<?php echo $type;?>" data-type="<?php echo $type;?>" >
		    	<?php foreach ($Items as $item):
		    		if(!empty($data->{$type}->{$item->id})){
		    			$dataItem = $data->{$type}->{$item->id};
			    	}else{
			    		 $obj = new stdClass;
			        	$obj->id = true;
			        	$obj->type = '';
			        	$obj->col = 'auto';
			        	$obj->name = \JText::_('JNONE');
			    		$dataItem = $obj;
			    	}
		    		$configItem = RowColumnSettings::getSettings($dataItem);
					$checked = (isset($dataItem->megabuild) && $dataItem->megabuild == '1') ? 'checked="true"' : "";
					$value 	 = (isset($dataItem->megabuild) && $dataItem->megabuild == '1') ? "1" : "0";
		    		$style = "style='display:none;'"; ?>
			    	<div class="t4-menu-items itemid-<?php echo $item->id; ?>" data-itemid="<?php echo $item->id;?>" <?php echo $style;?>>
			    		<div class="enablemega <?php echo $item->id; ?> t4-<?php echo $item->id; ?>">
							<label for="megabuild-<?php echo $item->id; ?>"><?php echo \JText::_('T4_NAVIGATION_BUILD_MEGA');?></label>
							<input id="megabuild-<?php echo $item->id; ?>" class="t4-item t4-input t4-input-check-mega" type="checkbox" name="megabuild" data-attrname="megabuild" value="<?php echo $value; ?>" <?php echo $checked; ?> />
						</div>
						<div class="item-mega-config">
							<div class="item-mega-width">
								<label class="item-width" for="width">Submenu Width (px)</label>
								<input id ="width" type="text" placeholder="300px" class="t4-item t4-item-width" name="item-width" value="" />
							</div>
							<div id="mega-extra" class="mega-extra-class">
								<label for="megaextra">Extra Class mega Item</label>
								<input id="mega_extra" type="text" name="mega_extra" value="" class="t4-item t4-mega-extra-class" aria-invalid="false">
							</div>
							<div class="item-mega-align">
								<label class="item-align">Alignment</label>
								<div class="t4-item btn-group">
									<a class="btn t4-item-align-left t4-item-action active" href="#" data-action="alignment" data-align="left" title="Left"><i class="fal fa-align-left"></i></a>
									<a class="btn t4-item-align-right t4-item-action" href="#" data-action="alignment" data-align="right" title="Right"><i class="fal fa-align-right"></i></a>
									<a class="btn t4-item-align-center t4-item-action" href="#" data-action="alignment" data-align="center" title="Center"><i class="fal fa-align-center"></i></a>
									<a class="btn t4-item-align-justify t4-item-action" href="#" data-action="alignment" data-align="justify" title="Justify"><i class="fal fa-align-justify"></i></a>
								</div>
							</div>
						</div>
					<div class="t4-menu-item" <?php echo $configItem;?> style='display:none;' >
						<?php if(empty($dataItem->settings)) $dataItem->settings[] = new stdClass() ;?>
						<?php foreach($dataItem->settings as $settings):?>
						<?php $rowSettings = RowColumnSettings::getSettings($settings); ?>
						<?php if(empty($settings->contents)) $settings->contents[] = new stdClass() ;?>
						<div class="t4-mega-section" <?php echo $rowSettings;?> >
							<div class="t4-meganeu-settings clearfix">
								<div class="pull-right">
									<ul class="t4-row-option-list">
										<li><a class="t4-move-row" href="#"><i class="fal fa-arrows-alt"></i></a></li>
										<li><a class="t4-meganeu-row-options" href="#"><i class="fal fa-cog fa-fw"></i></a></li>
										<li><a class="t4-remove-row-mega" href="#"><i class="fal fa-trash-alt fa-fw"></i></a></li>
									</ul>
								</div>
							</div>
							<div class="t4-row-container">
								<div class="row">
								<?php if(isset($settings->contents)): ?>
								<?php foreach($settings->contents as $contents):?>
									<?php $colSettings = RowColumnSettings::getSettings($contents);
									$cls = (isset($contents->col) && $contents->col != 'auto') ? 'col-md-'.$contents->col : 'col-md';
									$clsComp = (isset($contents->type) && $contents->type == 'component') ? ' t4-column-component' : '';
									if(isset($contents->type) && $contents->type == 'component'){
										$contentHtml = 'Component';
									}else{
										if(isset($contents->name) && $contents->name != ''){
											$contentHtml = $contents->name;
										}else{
											$contentHtml = \JText::_("JNONE");
										}
									}
									?>
									<div class="t4-col t4-mega-col <?php echo  $cls; ?>" <?php echo $colSettings;?>>
										<div class="col-inner item-build <?php echo $clsComp;?> clearfix">
											<span class="t4-column-title">None</span>
											<?php if(!$clsComp):?>
												<span class="t4-col-remove hidden" title="Remove column" data-content="Remove column"><i class="fal fa-minus"></i> </span>
											<?php endif;?>
											<a class="t4-item-options" href="#" ><i class="fal fa-cog fa-fw"></i></a>
										</div>
									</div>
								<?php endforeach?>
							<?php endif?>
								</div>
							</div>
						</div>
					<?php endforeach?>
					</div>
					<div class="t4-menu-add-row" style="display:none;"><a class="" href="#"><i class="fal fa-plus-circle"></i><span>Add Row</span></a></div>
				</div>
    			<?php endforeach ?>
		    </div>
	  		<?php endforeach ?>
	  		<?php endif ?>
		</div>
	</div>
</div>
<div class="clearfix"></div>
<input type="hidden" id="<?php echo $inputId; ?>" name="<?php echo $inputName; ?>" class="t4-navigation" value="<?php echo htmlspecialchars($inputVal); ?>">