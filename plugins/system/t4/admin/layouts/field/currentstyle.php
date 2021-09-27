<?php
$content = $displayData['data'];
$cid = $displayData['cid'];
if (!$content) return '';
?>
<span class="dropdown-toggle" type="button" data-toggle="dropdown">
  <i class="fal fa-angle-down"></i>
</span>

<div class="dropdown-menu">

  <span class="dropdown-title"><?php echo JText::_("T4_ADMIN_SWITCH_STYLE"); ?></span>
  <ul>
	<?php foreach ($content as $temp): ?>
		<?php
		$images = '';
		$cls = [];
		if ($cid == $temp->value) $cls[] = 'current';
		// if (T4AMIN_DEFAULT_ID == $temp->value) $cls[] = 'master';
		if ($temp->home == 1) $cls[] = 'master';
		if($temp->home && $temp->image) $images = JHtml::_('image', 'mod_languages/' . $temp->image . '.gif', '', null, true);
		$cls = count($cls) ? ' class="' . implode(' ', $cls) . '"' : '';
		?>
		<li<?php echo $cls ?>>
			<?php if ($cid == $temp->value): ?>
				<span><?php echo $temp->title; ?></span><?php echo $images;?>
			<?php else: ?>
			<a href='index.php?option=com_templates&task=style.edit&id=<?php echo $temp->value; ?>'>
				<span><?php echo $temp->title; ?></span><?php echo $images;?>
			</a>
			<?php endif ?>
		</li>
	<?php endforeach ?>
	</ul>
</div>
