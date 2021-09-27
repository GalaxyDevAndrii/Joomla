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
$fields = $form->getFieldset($name);
?>

<div class="jatools-row clearfix">
<?php foreach ($fields as $field) : ?>
	<?php $layouts = $field->element['layouts'] ? ' data-layouts="' . $field->element['layouts'] . '"' : ''; ?>
	<div class="control-group"<?php echo $layouts ?>>
		<div class="control-label"><?php echo $field->getLabel() ?></div>
		<div class="controls"><?php echo $field->getInput() ?></div>
	</div>
<?php endforeach ?>
</div>
