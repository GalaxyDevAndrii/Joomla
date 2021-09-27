<?php
$input_id = $displayData['id'];
$input_name = $displayData['name'];
$input_value = $displayData['value'];
$params = $displayData['colors'];
$class = $displayData['class'];

if(is_object($input_value)){
	$value_ip = $input_value->name;
	$data_value = $input_value->class;
}else{
	$value_ip = $data_value = $input_value;
}
$data_val = str_replace(" ", "_", $data_value);
if( array_key_exists($data_val, $params['brand_color'])) $dataVal =$params['brand_color'][$data_val];
if( array_key_exists($data_val, $params['base_color'])) $dataVal =$params['base_color'][$data_val];
if( array_key_exists($data_val, $params['user_color'])) $dataVal =$params['user_color'][$data_val];
if(empty($dataVal)) $dataVal = '';
?>

<div class="t4-select-color">
	<div class="color-preview">
		<span class="preview-icon" data-bgcolor="<?php echo $dataVal;?>" style="background-color: <?php echo $dataVal;?>;"></span>
		<input type="text" name="<?php echo $input_name;?>" id="<?php echo $input_id;?>" value="<?php echo str_replace('color','',$value_ip);?>" data-val="<?php echo str_replace("_", " ", $data_value);?>" data-color="<?php echo $dataVal;?>" class="t4-input t4-input-color <?php echo $class;?>" aria-invalid="false" readonly="" />
		<span class="toggle-icon"><i class="fal fa-angle-down"></i></span>
	</div>
	<?php
		echo \JLayoutHelper::render('field.colorTemplate', $params, T4PATH_ADMIN . '/layouts');
	?>
</div>