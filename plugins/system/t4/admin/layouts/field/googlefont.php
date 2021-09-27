<?php
$classes = $displayData['class'];
$id = $displayData['id'];
$value = $displayData['value'];
$name = $displayData['name'];
$attrName = $displayData['attrName'];
$fontType = $displayData['fontType'];
?>
<input id="<?php echo $id;?>" class="google-font-input <?php echo $classes; ?>" type="text" name="<?php echo $name; ?>" value="<?php echo htmlentities($value); ?>" placeholder="PT Sans" readonly="readonly" data-fontType="<?php echo $fontType;?>" data-classfontweight="<?php echo str_replace('_',"-",str_replace("_family","",$attrName)); ?>-weight">
<button type="button" id="get-<?php echo str_replace("_","-",$attrName);?>" class="btn-fonts" data-name="<?php echo $id;?>"><span class="fal fa-ellipsis-h"></span></button>