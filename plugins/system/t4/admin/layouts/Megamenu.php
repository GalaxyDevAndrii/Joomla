<?php
use T4Admin\Settings AS Settings;
use T4Admin\MegaSettings AS MegaSettings;
use T4Admin\RowColumnSettings AS RowColumnSettings;
defined ('_JEXEC') or die ();

$layout_path  = JPATH_ROOT .'/plugins/system/t4/admin/layouts';
echo Settings::getMegaRowConfig();
echo Settings::getMegaItemSettings();
$Megamenu = MegaSettings::getMenu();
$option = [''=> JText::_('T4_SELECT_MENU')];
$menuOpt = MegaSettings::getMenuType();
$option = array_merge($option, $menuOpt);
$default = $megamenu_data ? key($megamenu_data) : '';
echo "<div class='select-menu-type'>";
echo "<label for=\"menu_type\">Select Menu Type</label>";
echo $html = \JHTML::_('select.genericlist',$option,'menu_type','class="megamenu"',$value = '',$text = '',$default);
echo "</div>";
?>
<div class="t4-megamenu-builder">
	<div class="menu_items">
		<?php echo MegaSettings::getMenuItems(); ?>
	</div>
	<div class="item-config">
		<div class="menu-item-extra">
			<label for="extra"><?php echo JText::_('T4_MENU_EXTRA_CLASS');?></label>
			<input id="extra" type="text" name="extra_class" value="" class="t4-item t4-extra-class" />
			<!-- <span class="control-helper">Description here</span> -->
		</div>
		<div class="menu-item-icon">
			<label for="icons"><?php echo JText::_('T4_MENU_ITEM_ICON');?></label>
			<input id="icons" type="text" name="t4_item_icon" value="" class="t4-item  t4-item-icon" />
			<!-- <span class="control-helper">Description here</span> -->
		</div>
		<div class="menu-item-caption">
			<label for="caption"><?php echo JText::_('T4_MENU_ITEM_CAPTION');?></label>
			<input id="caption" type="text" name="t4_item_caption" value="" class="t4-item t4-item-caption" />
			<!-- <span class="control-helper">Description here</span> -->
		</div>
	</div>
	<div class="t4-menu-layout">
		<!-- Layout Builder Section -->
		<div class="t4-menu-layout-builder" >
			<div style="display: none">
			    <?php
			        $lt_section = new JLayoutFile('megamenu.item', $layout_path );
			        $obj = new stdClass;
			        $obj->id = true;
			        echo $lt_section->render($obj);
			    ?>
			</div>
		<?php
		    $output = '';
		    foreach ($Megamenu as $type =>  $Items) {
		    	$output .=  "<div class=\"t4-megamenu t4-{$type}\" data-type=\"{$type}\" >";
		    	$i = 0;
		    	foreach ($Items as $item) {
		    		$data = [$item];
		    		if(!empty($megamenu_data->{$type}->{$item->id})){
		    			$data[] = $megamenu_data->{$type}->{$item->id};
			    	}else{
			    		 $obj = new stdClass;
			        	$obj->id = true;
			    		$data[] = $obj;
			    	}
			    	$style = "style='display:none;'";
					$output .= "<div class='t4-menu-items itemid-".$item->id."' data-itemid='".$item->id."' $style >";
			    	$lt_section = new JLayoutFile('megamenu.items', $layout_path );
		        	$output .= $lt_section->render($data);
		        	$output .=  '</div>';
		        	$i++;
		    	}
		    	$output .=  '</div>';
		    }
		    echo $output;
	    ?>
		</div>
	</div>
</div>
<div class="clearfix"></div>
