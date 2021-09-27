<?php
defined('_JEXEC') or die();
use T4Admin\RowColumnSettings AS RowColumnSettings;
$settings = $displayData;
if(!isset($settings->name)){
    $settings->name = '';
}
if(!isset($settings->type)){
    $settings->type = 'block';
}
if(!isset($settings->col)){
    $settings->col = 'auto';
}

$colSettings = 'data-type="'.$settings->type.'" data-col="12" data-name="none"';
if(isset($settings->col) && $settings->col){
    $colSettings = RowColumnSettings::getSettings($settings);
}
if((isset($settings->col) && $settings->col != 'auto')){
    $cls = 'col-md-'.$settings->col;
}else{
    $cls = 'col-md';
}
$output = '<div '.((isset($row->id) && $row->id)?'id="t4-mega-col"':'').' class="t4-col t4-mega-col ' . $cls .'" ' . $colSettings .'>';
$output .= '<div   class="col-inner item-build' . ((isset($settings->type) && $settings->type == 'component') ? ' t4-column-component' : '') . ' clearfix">';

if (isset($settings->type) && $settings->type == 'component')
{
    $output .= '<span class="t4-column-title">'.\JText::_("JCOMPONENT").'</span>';
}
else
{
    if (isset($settings->name) && $settings->name != '')
    {
        $output .= '<span class="t4-column-title">'. $settings->name .'</span>';
    }
    else
    {
        $output .= '<span class="t4-column-title">'.\JText::_("JNONE").'</span>';
    }
    $output .= '<span class="t4-col-remove hidden" title="Remove column" data-content="Remove column"><i class="fal fa-minus"></i> </span>';
}

$output .= '<a class="t4-item-options" href="#" ><i class="fal fa-cog fa-fw"></i></a>';
$output .= '</div>';
$output .= '</div>';

echo $output;