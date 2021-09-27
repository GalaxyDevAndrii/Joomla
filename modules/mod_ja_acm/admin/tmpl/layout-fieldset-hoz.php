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
$width = 100 / count ($fields);
$width = 'width="' . $width . '%"';
?>

<table>
	<tr>
	<?php foreach ($fields as $field) : ?>
		<th <?php echo $width ?>><?php echo $field->getLabel() ?></th>
	<?php endforeach ?>
	</tr>
	<tr class="jatools-row clearfix">
		<?php foreach ($fields as $field) : ?>
			<td>
				<?php echo $field->getInput() ?>
			</td>
		<?php endforeach ?>
	</tr>
</table>
