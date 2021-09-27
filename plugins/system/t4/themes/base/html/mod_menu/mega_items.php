<?php
$items = isset($col['items']) ? $col['items'] : 'all';
if ($items == 'all') {
	$items = array_keys($sub_items);
} else {
	$items = explode(',', $items);
}
?>

<ul class="mega-nav level1<?php echo $nextItem->level;?>">
<?php
	foreach ($items as $iid) {
		if (!isset($sub_items[$iid])) continue;
		// store current item
		$_item = $item;
		// set current item
		$item = $sub_items[$iid];
		$class = 'nav-item';
		if (in_array($item->id, $path))
		{
			$class .= ' active';
		}
		//remove data dropdown of menu
		$item->mega_sub = 1;
		$item->level = 1;
		$item->deeper = false;
		echo '<li class="' . $class . '" data-id="'.$item->id.'">';
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
		echo '</li>';

		// switch back current item
		$item = $_item;
	}
?>
</ul>
