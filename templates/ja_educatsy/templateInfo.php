<?php
/**
 * ------------------------------------------------------------------------
 * JA Educatsy Template
 * ------------------------------------------------------------------------
 * Copyright (C) 2004-2018 J.O.O.M Solutions Co., Ltd. All Rights Reserved.
 * @license - Copyrighted Commercial Software
 * Author: J.O.O.M Solutions Co., Ltd
 * Websites:  http://www.joomlart.com -  http://www.joomlancers.com
 * This file may not be redistributed in whole or significant part.
 * ------------------------------------------------------------------------
*/
$info = $displayData['info'];
$tplName = $displayData['tplName'];
$telem = 't4_blank';
$felem = 't4';
$thasnew = false;
$fhasnew = false;
// no direct access
defined('_JEXEC') or die;
$pxml = simplexml_load_file(T4PATH .'/t4.xml');
$cfversion = $nfversion =  $pxml->version[0];
$txml = simplexml_load_file(JPATH_ROOT . '/templates/'.$tplName.'/templateDetails.xml');
$ctversion = $ntversion = $txml->version[0];


if(count($info)){
	if(isset($info[$telem]) && version_compare($info[$telem]->version, $ctversion, 'gt')){
    $thasnew = true;
    $ntversion = $info[$telem]->version;
  }
  
  if(isset($info[$felem]) && version_compare($info[$felem]->version, $cfversion, 'gt')){
    $fhasnew = true;
    $nfversion = $info[$felem]->version;
  }
}
$pxml->name = str_replace("_"," ",$pxml->name);
$txml->name = str_replace("_"," ",$txml->name);
?>

<div class="t4-template-info">
	<div class="tpl-preview">
		<img src="<?php echo JUri::root(true) . '/templates/'.$tplName;?>/template_preview.png" alt="Template Preview"/>
	</div>

	<div class="t4-admin-overview-header">
		<h2>
			<?php echo JText::_('T4_TPL_DESC_1') ?>
			<small><?php echo JText::_('T4_TPL_DESC_2') ?></small>
		</h2>
		<p><?php echo JText::_('T4_TPL_DESC_3') ?></p>
	</div>
	<div class="t4-admin-overview-body">
		<h4><?php echo JText::_('T4_TPL_DESC_4') ?></h4>
		<ul class="t4-admin-overview-features">
			<!-- <li><?php echo JText::_('T4_TPL_DESC_5') ?></li> -->
			<li><?php echo JText::_('T4_TPL_DESC_6') ?></li>
			<li><?php echo JText::_('T4_TPL_DESC_7') ?></li>
			<li><?php echo JText::_('T4_TPL_DESC_8') ?></li>
		</ul>
	</div>

	<div class="t4-template-more-info">
		<h4>Template Information</h4>
		<ul>
			<li><span>Name:</span> <?php echo $txml->name;?></li>
			<li><span>Version:</span> <?php echo $txml->version;?></li>
			<li><span>Released Date:</span> <?php echo $txml->creationDate;?></li>
			<li><span>Author:</span><a class="t4-author" href="https://<?php echo $txml->authorUrl; ?>" target="_Blank"><?php echo $txml->author;?></a></li>
		</ul>
	</div>
	<div class="t4-admin-overview-block updated">
	<?php echo empty($txml->updateservers) ? \JText::_('T4_OVERVIEW_TPL_VERSION_MSG') : ($thasnew ? \JText::sprintf('T4_OVERVIEW_TPL_NEW_MSG', $ctversion, $txml->name, $ntversion) : \JText::sprintf('T4_OVERVIEW_TPL_SAME_MSG', $txml->name)) ?>
	<?php echo $thasnew ? "<div class='t4-btn btn-update btn-primary'><a href='index.php?option=com_installer&view=update' title='Update template'>Update</a></div>" : "";?>
	</div>
</div>