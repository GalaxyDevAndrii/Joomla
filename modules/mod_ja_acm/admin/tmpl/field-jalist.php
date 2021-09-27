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
$field      = $displayData['field'];
$items      = $displayData['items'];
$value      = htmlspecialchars($field->value, ENT_COMPAT, 'UTF-8');
$id         = $field->id;
$name       = $field->name;
$showlabel  = (bool)$field->element['showlabel'];
$label      = JText::_($field->element['label']);
$desc       = JText::_($field->element['description']);

$width = 90/count ($items);

$doc = JFactory::getDocument();
if (version_compare(JVERSION, '4.0', 'ge')) {
	$doc->addScript(JURI::root(true) . '/modules/mod_ja_acm/admin/assets/script_j4.js');
	$doc->addScript(JURI::root(true) . '/modules/mod_ja_acm/admin/assets/jalist_j4.js');
} else {
	$doc->addScript(JURI::root(true) . '/modules/mod_ja_acm/admin/assets/script.js');
	$doc->addScript(JURI::root(true) . '/modules/mod_ja_acm/admin/assets/jalist.js');
}
$doc->addStyleSheet(JURI::root(true) . '/modules/mod_ja_acm/admin/assets/style.css');
$doc->addStyleSheet(JURI::root(true) . '/modules/mod_ja_acm/admin/assets/jalist.css');
?>
<div class="jaacm-list <?php echo $id ?>">
	<?php if (!$showlabel): ?>
	<h4><?php echo $label ?></h4>
	<p><?php echo $desc ?></p>
	<?php endif ?>
	<table class="jalist" width="100%">
		<thead>
			<tr>
				<?php foreach ($items as $item) :
					$title = (string) $item->element['title'];
					if (!$title) $title = (string) $item->element['label'];
					?>
					<th width="<?php echo $width ?>%">
						<?php echo JText::_($title) ?>
					</th>
				<?php endforeach ?>
				<th width="10%">&nbsp;</th>
			</tr>
		</thead>

		<tbody>
			<tr class="first">
				<?php foreach ($items as $item) : ?>
					<td>
						<?php echo $item->getInput() ?>
					</td>
				<?php endforeach ?>
				<td>
					<span class="btn action btn-clone" data-action="clone_row" title="Clone Row"><i class="icon-plus fa fa-plus"></i></span>
					<span class="btn action btn-delete" data-action="delete_row" title="Delete Row" data-confirm="<?php echo JText::_('MOD_JA_ACM_CONFIRM_DELETE_MSG') ?>"><i class="icon-minus fa fa-minus"></i></span>
				</td>
			</tr>
		</tbody>

	</table>

	<input type="hidden" name="<?php echo $name ?>" value="<?php echo $value ?>" class="acm-object" />
</div>
<script>
	// jaFieldList(jQuery, '.<?php echo $id ?>');
	function JAjSelectPosition_<?php echo $id; ?>__position(name) {
		if (hidden_position=='') {jModalClose();return;}
		document.getElementById(hidden_position).value = name;
		jModalClose();
	}
	var hidden_position='';
	jQuery('.<?php echo $id ?>').jalist();
</script>