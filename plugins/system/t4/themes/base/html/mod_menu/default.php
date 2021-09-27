<?php
/**
T4 Overide
 */

defined('_JEXEC') or die;

$id = '';

if ($tagId = $params->get('tag_id', ''))
{
	$id = ' id="' . $tagId . '"';
}

// The menu class is deprecated. Use nav instead
?>
<nav class="navbar">
<ul<?php echo $id; ?> class="nav navbar-nav <?php echo $class_sfx; ?>">
<?php foreach ($list as $i => &$item)
{
	$class = 'nav-item item-'.$item->id;
	if(version_compare(JVERSION, '4','ge')){
		$itemParams = $item->getParams();
	}else{
		$itemParams = $item->params;
	}
	if ($item->id == $default_id)
	{
		$class .= ' default';
	}

	if ($item->id == $active_id || ($item->type === 'alias' && $itemParams->get('aliasoptions') == $active_id))
	{
		$class .= ' current';
	}

	if (in_array($item->id, $path))
	{
		$class .= ' active';
	}
	elseif ($item->type === 'alias')
	{
		$aliasToId = $itemParams->get('aliasoptions');

		if (count($path) > 0 && $aliasToId == $path[count($path) - 1])
		{
			$class .= ' active';
		}
		elseif (in_array($aliasToId, $path))
		{
			$class .= ' alias-parent-active';
		}
	}

	if ($item->type === 'separator')
	{
		$class .= ' divider';
	}


	$caret = '';
	$level_cls = '';
	if ($item->deeper)
	{
		$class .= ' deeper dropdown';
 		$level_cls = " data-level=".$item->level;
		if ($item->level>1) {
			$class .= ' dropright dropend';
		}
	}

	if ($item->parent)
	{
		$class .= ' parent';
	}

	echo '<li class="' . $class . '"'.$level_cls.'>';
	$item->mega_sub = false;
	$item->icon = "";
	$item->caret = '';
	$item->caption = '';
	switch ($item->type) :
		case 'separator':
		case 'component':
		case 'heading':
		case 'url':
			require JModuleHelper::getLayoutPath('mod_menu', 'default_' . $item->type);
			break;

		default:
			require JModuleHelper::getLayoutPath('mod_menu', 'default_url');
			break;
	endswitch;

	// The next item is deeper.
	if ($item->deeper)
	{
		echo '<ul class="dropdown-menu">';
	}
	// The next item is shallower.
	elseif ($item->shallower)
	{
		echo '</li>';
		echo str_repeat('</ul></li>', $item->level_diff);
	}
	// The next item is on the same level.
	else
	{
		echo '</li>';
	}
}
?></ul></nav>