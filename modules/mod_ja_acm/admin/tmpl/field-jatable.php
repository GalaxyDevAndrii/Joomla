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
$field = $displayData['field'];
$items = $displayData['items'];
$value = htmlspecialchars($field->value, ENT_COMPAT, 'UTF-8');
$id = $field->id;
$name = $field->name;
$label = JText::_($field->element['label']);
$desc = JText::_($field->element['description']);

$width = 90/count ($items);

$doc = JFactory::getDocument();
$doc->addScript(JURI::root(true) . '/modules/mod_ja_acm/admin/assets/jatable.js');
$doc->addStyleSheet(JURI::root(true) . '/modules/mod_ja_acm/admin/assets/jatable.css');
?>
<div class="jaacm-table <?php echo $id ?>">
	<h4><?php echo $label ?></h4>
	<p><?php echo $desc ?></p>
	<table class="jatable" width="100%">
		<?php if (count($items)): ?>
		<thead>
			<tr class="title">
				<th><?php echo $items[0]->getLabel() ?></th>
				<th class="first">
					<div class="jatable-cell-container">
						<?php echo $items[0]->getInput() ?>
						<div class="actions">
							<span class="btn action btn-delete-col" data-action="delete_col" title="Delete Column" data-confirm="<?php echo JText::_('MOD_JA_ACM_CONFIRM_DELETE_MSG') ?>"><i class="fa fa-minus"></i></span>
							<span class="btn action btn-clone-col" data-action="clone_col" title="Clone Column""><i class="fa fa-plus"></i></span>
						</div>
					</div>
				</th>
				<th width="10%">&nbsp;</th>
			</tr>

			<?php for ($i=1; $i<count($items); $i++) : ?>
			<tr class="">
				<td>
					<?php echo $items[$i]->getLabel() ?>
				</td>
				<td>
					<?php echo $items[$i]->getInput() ?>
				</td>
				<td>
				</td>
			</tr>
			<?php endfor ?>
		</thead>
		<?php endif ?>
		<tbody>
			<tr class="first">
				<td>
					<textarea class="input"></textarea>
				</td>
				<td valign="top" data-type="text">
					<div class="jatable-cell-container">
						<input type="hidden" value="" />
						<span class="jatable-cell"></span>
						<textarea class="jatable-cell-text" autoheight="true" placeholder="Enter Text"></textarea>
						<div class="jatable-cell-tools navbar">
							<ul class="nav">
								<li class="jatable-cell-type action" data-action="change_type" data-type="text"><i class="fa fa-font"></i></li>
								<li class="jatable-cell-type action" data-action="change_type" data-type="b1"><i class="fa fa-check"></i></li>
								<li class="jatable-cell-type action" data-action="change_type" data-type="b0"><i class="fa fa-times"></i></li>
								<li class="jatable-cell-type action" data-action="change_type" data-type="b-1"><i class="fa fa-exclamation-triangle"></i></li>
								<li class="dropdown">
									<span class="dropdown-toggle" data-toggle="dropdown">Rating</span>
									<ul class="dropdown-menu" role="menu" aria-labelledby="dLabel">
										<li class=" action" data-action="change_type" data-type="r5"><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i></li>
										<li class=" action" data-action="change_type" data-type="r45"><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star-half"></i></li>
										<li class=" action" data-action="change_type" data-type="r4"><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i></li>
										<li class=" action" data-action="change_type" data-type="r35"><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star-half"></i></li>
										<li class=" action" data-action="change_type" data-type="r3"><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i></li>
										<li class=" action" data-action="change_type" data-type="r25"><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star-half"></i></li>
										<li class=" action" data-action="change_type" data-type="r2"><i class="fa fa-star"></i><i class="fa fa-star"></i></li>
										<li class=" action" data-action="change_type" data-type="r15"><i class="fa fa-star"></i><i class="fa fa-star-half"></i></li>
										<li class=" action" data-action="change_type" data-type="r1"><i class="fa fa-star"></i></li>
									</ul>
								</li>
							</ul>
						</div>
					</div>
				</td>
				<td>
					<div class="actions">
						<span class="btn action btn-clone-row" data-action="clone_row" title="Clone Row"><i class="fa fa-plus"></i></span>
						<span class="btn action btn-delete-row" data-action="delete_row" title="Delete Row" data-confirm="<?php echo JText::_('MOD_JA_ACM_CONFIRM_DELETE_MSG') ?>"><i class="fa fa-minus"></i></span>
					</div>
				</td>
			</tr>
		</tbody>
	</table>

	<input type="hidden" name="<?php echo $name ?>" value="<?php echo $value ?>" class="acm-object" />
</div>
<script>
	jQuery ('.<?php echo $id ?>').jatable();
</script>