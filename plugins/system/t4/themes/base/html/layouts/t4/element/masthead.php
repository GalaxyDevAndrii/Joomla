<?php

$app = JFactory::getApplication();
$active = $app->getMenu()->getActive();

if (!$active) return '';
if(version_compare(JVERSION, '4', 'ge')){
	$params = JFactory::getApplication()->getMenu()->getParams($active->id);
}else{
	$params = $active->params;
}
$title = null;
if (\T4\Helper\Layout::isSubpage()) {
    $title = $params ? $params->get('page_subheading') : null;
} else {
    $title = $params ? $params->get('page_heading') : null;
}
$page_desc = $params->get('page_heading_desc','');
$page_background_img = $params->get('page_heading_bg_img','');
$page_background_color = $params->get('page_heading_bg_color','');
$page_background = $page_background_img ? "background-image: url(".$page_background_img.");" : "";
$page_background .= $page_background_color ? "background-color:".$page_background_color.";" : "";

// if (!$title) $title = $active->title;
if (!$title) {
    return '';
}
?>
<div class="t4-masthead-inner<?php echo $page_background_img ? ' has-bg' : '';?>" <?php echo $page_background ? 'style="'.$page_background.'"' : "";?>>
	<div class="t4-masthead-detail">
		<h2 class="t4-masthead-title"><?php echo $title ?></h2>
	      <?php if ($page_desc != '') : ?>
		  <div class="t4-masthead-description"><?php echo $page_desc; ?></div>
	      <?php endif; ?>
	</div>
</div>
