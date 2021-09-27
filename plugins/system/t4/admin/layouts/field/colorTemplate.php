<?php 

/**
 *------------------------------------------------------------------------------
 * @package       T4 Framewrork for Joomla!
 *------------------------------------------------------------------------------
 * @copyright     Copyright (C) 2004-2020 JoomlArt.com. All Rights Reserved.
 * @license       GNU General Public License version 2 or later; see LICENSE.txt
 * @authors       JoomlArt, JoomlaBamboo, (contribute to this project at github
 *                & Google group to become co-author)
 *------------------------------------------------------------------------------
 */
defined('JPATH_BASE') or die;

$params = $displayData;



?>
<div class="choose-color-pattern hidden">
	<ul>
		<?php foreach ($params as $name_color => $group_color) :?>
			<li class="group-title"><span><?php echo str_replace("_"," ",$name_color);?></span></li>
			<?php 
				if($name_color == "user_color"){
					$type = 'user-color';
				}else if($name_color == "template_color"){
					$type = 'template-color';
				}else if($name_color == "custom_color"){
					$type = 'custom-color';
				}else{
					$type = '';
				}
			foreach ($group_color as $name => $field_color) : ?>
				<?php 
					switch ($type) {
						case 'user-color':
							$field_name = str_replace("_"," ", str_replace('user_', "", $name));
							break;
						case 'template-color':
							$field_name = str_replace("_"," ", str_replace('template_', "", $name));
							break;
						
						case 'custom-color':
							$field_name = str_replace("_"," ", str_replace('custom_', "", $name));
							break;
						
						default:
							$field_name = str_replace("_"," ",str_replace('color_', "", $name));
							break;
					}
				?>
				<li class="t4-select-pattern" data-val="<?php echo $name;?>" data-color="<?php echo $field_color;?>" data-name="<?php echo $field_name;?>">
					<span class="preview-icon" style="background-color: <?php echo $field_color; ?>;"></span>
					<span class="color-label"><?php echo $field_name;?></span>
				</li>
			<?php endforeach;?>
		<?php endforeach;?>
	</ul>
</div>