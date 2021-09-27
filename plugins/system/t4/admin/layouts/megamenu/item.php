<?php

defined('_JEXEC') or die();
use T4Admin\RowColumnSettings AS RowColumnSettings;

$row = $displayData;

$layout_path  = JPATH_ROOT .'/plugins/system/t4/admin/layouts';
$layout_column = new JLayoutFile('megamenu.layoutItem', $layout_path );

if(!isset($row->layout)){
    $row->layout =  12;
}
if(!isset($row->cols)){
    $row->cols =  1;
}
$rowSettings = '';
if(isset($row->contents))
{
    $rowSettings = RowColumnSettings::getSettings($row);
}

$output = '';
$outputs = '';
$output .= '<div '.((isset($row->id) && $row->id) ? 'id="t4-mega-section"' : '').' class="t4-mega-section" '.$rowSettings.'>';
$output .= '<div class="t4-meganeu-settings clearfix">';
$output .= '<div class="pull-right">';
$output .= '<ul class="t4-row-option-list">';
$outputs .= '<li>';
$outputs .= '<ul class="t4-column-list">';
$output .= '<li><a class="t4-move-row" href="#"><i class="fal fa-arrows-alt"></i></a></li>';
$output .= '<li><a class="t4-meganeu-row-options" href="#"><i class="fal fa-cog fa-fw"></i></a></li>';
$output .= '<li><a class="t4-remove-row-mega" href="#"><i class="fal fa-trash-alt fa-fw"></i></a></li>';
$output .= '</ul>';
$output .= '</div>';
$output .= '</div>';

$output .= '<div class="t4-row-container ui-sortable">';
$output .= '<div class="row ui-sortable">';

if(isset($row->contents) && $row->contents)
{
    foreach ($row->contents as $contents)
    {
        $output .= $layout_column->render($contents);
    }
}
else
{
    $output .= $layout_column->render(new stdClass);
}
$output .= '</div>';
$output .= '</div>';
$output .= '</div>';

echo $output;