<?php 
$id = $displayData['id'];
$inputName = $displayData['name'];
$inputValue = $displayData['value'];
$data = $displayData['colors'];

$script = "\nvar T4Admin = window.T4Admin || {}; ";
$script .= "\nT4Admin.customcolors = ". json_encode($data) . "; ";
JFactory::getDocument()->addScriptDeclaration($script);
JFactory::getDocument()->addScript(T4PATH_ADMIN_URI . '/assets/js/custom-color.js');

$edit_class = " can-edit";
$decs = JText::_('T4_FIELD_CUSTOM_COLOR_MASTER_DESC');

?>
<div class="t4-custom-color-wrap">
	<?php if(!empty($data)):?>
		<ul class="custom-color-list">
		<?php foreach($data as $colorName => $colorData):?>
			<li class="t4-custom-color" data-name="<?php echo $colorData['name']; ?>" data-class="<?php echo $colorName; ?>"  data-color="<?php echo $colorData['color']; ?>">
				<input class="t4-color-picker" data-name="<?php echo $colorData['name']; ?>" data-class="<?php echo $colorName; ?>"  data-color="<?php echo $colorData['color']; ?>" name="t4_color_name" type="hidden" value="<?php echo $colorData['color']; ?>" />
			</li>
		<?php endforeach ?>
		<li class="t4-custom-color color-ghost" data-name="" data-class=""  data-color="">
			<span class="preview-icon t4-color"><i class="far fa-plus-square"></i></span>
		</li>
		</ul>
	<?php endif ?>
</div>
<input type="hidden" id="<?php echo $id; ?>" class="t4-custom-colors" name="<?php echo $inputName; ?>" value="<?php echo htmlspecialchars($inputValue); ?>">	
