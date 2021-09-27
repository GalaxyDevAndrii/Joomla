<?php
/**
T4 Overide
 */
defined('_JEXEC') or die;

use Joomla\CMS\Factory as JFactory;
$doc = \JFactory::getDocument();
$app =  JFactory::getApplication();
$input = $app->input;
$template = $app->getTemplate();
$tplParams = $doc->params;
$navigation_settings = $tplParams->get('navigation-settings');
// $menuType = $navigation_settings->get('menu_type', 'mainmenu');
$menuType = $params->get('menutype', 'mainmenu');
$mega_settings = $navigation_settings->get('mega_settings');
if (is_string($mega_settings)) $mega_settings = json_decode($mega_settings, true);

$megaSetting = !empty($mega_settings[$menuType]) ? $mega_settings[$menuType] : [];

$id = '';

if ($tagId = $params->get('tag_id', ''))
{
	$id = ' id="' . $tagId . '"';
}

$navid = 't4-megamenu-' . $menuType;

$megacheck = '';

// The menu class is deprecated. Use nav instead
$show_in_sm = filter_var($navigation_settings->get('mega_showsm'), FILTER_VALIDATE_BOOLEAN);

// dropdown animation effect
$mega_effect = $navigation_settings->get('mega_effect','');
$mm_hover = $navigation_settings->get('mega_dropdown_effect','') == 'hover' ? ' mm_hover' : " mm_click";

$data_duration = '';
if($mega_effect)  {
	$mega_effect = ' ' . $mega_effect . ' animate';
	$data_duration = ' data-duration="' . $navigation_settings->get('mega_duration', 400) . '"';
}

// Add body class
\T4\T4::getInstance()->addBodyClass('nav-breakpoint-' . $navigation_settings->get('option_breakpoint', 'lg'));
?>

<nav class="navbar navbar-expand-<?php echo $navigation_settings->get('option_breakpoint', 'lg')?>">
<?php if ($show_in_sm): ?>
<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#<?php echo $navid ?>" aria-expanded="false" aria-label="Toggle navigation" style="display: none;">
    <i class="fa fa-bars toggle-bars"></i>
</button>
	<?php 
	else:
		// Add body class
		\T4\T4::getInstance()->addBodyClass('navigation-hide');
	?>
<?php endif ?>
<div id="<?php echo $navid ?>" class="t4-megamenu collapse navbar-collapse<?php  echo $mega_effect ?>"<?php echo $data_duration; ?>>

<ul<?php echo $id; ?> class="nav navbar-nav level0"  itemscope="itemscope" itemtype="http://www.schema.org/SiteNavigationElement">
<?php
$idx = 0;
$ids = array_keys($list);
while ($idx < count($list)) {
	if(isset($ids[$idx])) $item = $list[$ids[$idx]];
	if(isset($ids[$idx+1])) $nextItem = $list[$ids[$idx+1]];

	// check if is megamenu enable for this item
	$item->icon = $item->caption = '';
	if (!empty($megaSetting[$item->id]) && !empty($megaSetting[$item->id]['settings'])) {
		if (!empty($megaSetting[$item->id]['extra'])) $item->extra_class = $megaSetting[$item->id]['extra'];
		if (!empty($megaSetting[$item->id]['icons'])) {
			if (preg_match('/^fa(s|r|l|b)?\s/', $megaSetting[$item->id]['icons'])) {
				// font awesome
				$item->icon = '<i class="' . $megaSetting[$item->id]['icons'] . '" aria-hidden="true"></i>';
			}elseif (preg_match('/^icon-/', $megaSetting[$item->id]['icons'],$matches)) {
				// font IcoMoon
				$item->icon = '<i class="' . $megaSetting[$item->id]['icons'] . '" aria-hidden="true"></i>';
			} else {
				// font material design
				$item->icon = '<i class="material-icons" aria-hidden="true">' . $megaSetting[$item->id]['icons'] . '</i>';
			}
		}
		if (!empty($megaSetting[$item->id]['caption'])) {
			$item->caption = '<small class="mega-caption">' . $megaSetting[$item->id]['caption'] . '</small>';
		}


		if (!empty($megaSetting[$item->id]['megabuild'])) {
			$item->mega = $megaSetting[$item->id];
			$item->deeper = true;
		}
		//$item->type = 'heading';
	}
	$class = 'nav-item';

	if ($item->id == $default_id)
	{
		$class .= ' default';
	}

	if ($item->id == $active_id || ($item->type === 'alias' && $item->params->get('aliasoptions') == $active_id))
	{
		$class .= ' current';
	}

	if (in_array($item->id, $path))
	{
		$class .= ' active';
	}
	elseif ($item->type === 'alias')
	{
		$aliasToId = $item->params->get('aliasoptions');

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

	$align = '';
	if ($item->deeper)
	{
		$class .= ' dropdown';

		if ($item->level>1) {
			$class .= ' dropright dropend';
		}
		if(isset($item->mega)){
			$class .= ' mega';
			if(isset($item->mega['align'])){
				$align = ' data-align="' . $item->mega['align'] . '"';
				// $class .= ' mega-align-'.$item->mega['align'];
			}
		}
	}

	if ($item->parent)
	{
		$class .= ' parent';
	}

	// For caption, icon, extra class - extended by Megamneu
	if (!empty($item->icon))
	{
		$class .= ' has-icon';
	}
	if (!empty($item->caption))
	{
		$class .= ' has-caption';
	}
	if (!empty($item->extra_class))
	{
		$class .= ' ' . $item->extra_class;
	}

	// Add caret
	if ($item->deeper) {
		$item->caret = '<i class="item-caret"></i>';
	}

	echo '<li class="' . $class . '" data-id="'.$item->id.'" itemprop="name" data-level="'.$item->level.'"'. $align . '>';

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


	// Check and render megamenu
	if (!empty($item->mega)) {
		$item->mega = $megaSetting[$item->id];
		require JModuleHelper::getLayoutPath('mod_menu', 'mega_sub');
		echo "</li>\n";
	} else {
		// The next item is deeper.
		if ($item->deeper)
		{
			echo '<div class="dropdown-menu level'.$item->level.'"><div class="dropdown-menu-inner"><ul>';
		}
		// The next item is shallower.
		elseif ($item->shallower)
		{
			echo '</li>';
			echo str_repeat('</ul></div></div></li>', $item->level_diff);
		}
		// The next item is on the same level.
		else
		{
			echo '</li>';
		}
	}
	$idx++;
}
?></ul></div>
</nav>
