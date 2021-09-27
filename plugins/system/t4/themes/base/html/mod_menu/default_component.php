<?php
// check if need separated layout for Joomla 3!
if (($j3 = \T4\Helper\Layout::j3(__FILE__))) {
	include $j3;
	return;
}
defined('_JEXEC') or die;

use Joomla\CMS\Filter\OutputFilter;
use Joomla\CMS\HTML\HTMLHelper;

$attributes = array();

if ($item->anchor_title)
{
	$attributes['title'] = $item->anchor_title;
}

// T4: add class nav-link
if ($item->anchor_css)
{
	if(($item->level > 1)) {
		$attributes['class'] = $item->anchor_css . ' dropdown-item';
	} else {
		$attributes['class'] = $item->anchor_css . ' nav-link';
	}
}else{
	if(($item->level > 1)) {
		$attributes['class'] = 'dropdown-item';
	} else {
		$attributes['class'] = 'nav-link';
	}
}

if ($item->anchor_rel)
{
	$attributes['rel'] = $item->anchor_rel;
}

if ($item->id == $active_id)
{
	$attributes['aria-current'] = 'page';
}
if($item->deeper){
	if(!$item->mega_sub){
		$attributes['class'] .= ' dropdown-toggle';
	}
	$attributes['role'] = 'button';
	$attributes['aria-haspopup'] = 'true';
	$attributes['aria-expanded'] = 'false';
	$attributes['data-toggle'] = 'dropdown';
	// $attributes['id'] = 'navbarDropdown';
}
$linktype = $item->title;

if ($item->menu_image)
{
	if ($item->menu_image_css)
	{
		$image_attributes['class'] = $item->menu_image_css;
		$linktype = HTMLHelper::_('image', $item->menu_image, $item->title, $image_attributes);
	}
	else
	{
		$linktype = HTMLHelper::_('image', $item->menu_image, $item->title);
	}

	if ($itemParams->get('menu_text', 1))
	{
		$linktype .= '<span class="image-title">' . $item->title . '</span>';
	}
}

if ($item->browserNav == 1)
{
	$attributes['target'] = '_blank';
}
elseif ($item->browserNav == 2)
{
	$options = 'toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=yes';

	$attributes['onclick'] = "window.open(this.href, 'targetWindow', '" . $options . "'); return false;";
}

$caret = !empty($item->caret) ?  $item->caret : "";
$linktype = $item->icon . $linktype . $caret . $item->caption;
echo HTMLHelper::_('link', OutputFilter::ampReplace(htmlspecialchars($item->flink, ENT_COMPAT, 'UTF-8', false)), $linktype, $attributes);
