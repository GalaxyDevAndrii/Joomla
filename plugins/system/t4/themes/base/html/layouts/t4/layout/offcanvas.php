<?php

$doc = $displayData->doc;
$paramsTpl = $doc->params;
$conf = JFactory::getConfig();
//if(empty($paramsTpl->get('t4-navigation'))) $paramsTpl->set('t4-navigation','default');
//$params = json_decode(\T4\Helper\Path::getFileContent('etc/navigation/'.$paramsTpl->get('t4-navigation').'.json'));
//if(empty($params)) return;
//if (!$params->navigation_oc_enabled) return;
$navigation_settings = $paramsTpl->get('navigation-settings');
$site_settings = $paramsTpl->get('site-settings');
if (!$navigation_settings->get('oc_enabled')) return;

$site_name = $site_settings->get('site_name', $conf->get('sitename'));
$site_slogan = $site_settings->get('site_slogan', '');
$logo = $site_settings->get('site_logo');
$logo_small = $site_settings->get('site_logo_small');
$logo_cls = $logo ? 'logo-image' : 'logo-text';
$logo_sm_cls = '';
if ($logo_small) {
    $logo_cls .= ' logo-control';
    $logo_sm_cls = ' d-none d-sm-block';
}

$oc_effect = $navigation_settings->get('oc_effect', "left");

$oc_pos_name = $navigation_settings->get('oc_pos_name', 'offcanvas');
$oc_pos_style = $navigation_settings->get('oc_pos_style', 'xhtml');
$oc_rightside = filter_var($navigation_settings->get('oc_rightside'), FILTER_VALIDATE_BOOLEAN);
$oc_menu_effect = $navigation_settings->get('oc_menu_effect', 'def');

// add css & js
$doc->addStylesheet(T4\Helper\Path::findInTheme('vendors/js-offcanvas/_css/js-offcanvas.css', true));
$doc->addScript(T4\Helper\Path::findInTheme('vendors/js-offcanvas/_js/js-offcanvas.pkgd.js', true));
$doc->addScript(T4\Helper\Path::findInTheme('vendors/bodyscrolllock/bodyScrollLock.min.js', true));
$doc->addScript(T4\Helper\Path::findInTheme('js/offcanvas.js', true));
$oc_rightside = $navigation_settings->get('oc_rightside', '');
if($oc_rightside){
      $oc_effect = str_replace("left", 'right', $oc_effect);
  }
switch ($oc_effect) {
	case 'left':
	case 'right':
		$options = '{"modifiers":"'.$oc_effect.',overlay"}';
		break;
	
	default:
		$options = '{"modifiers":"'.str_replace("-",",",$oc_effect).'"}';
		break;
}
$hasLink = !empty($displayData->params) && !empty($displayData->params['nolink']) ? false : true;
?>
<div class="t4-offcanvas" data-offcanvas-options='<?php echo $options; ?>' id="off-canvas-<?php echo $oc_effect;?>" role="complementary" style="display:none;">
	<div class="t4-off-canvas-header">
	 	<?php if ($hasLink): ?>
	  <a href="<?php echo JUri::base(); ?>" title="<?php echo strip_tags($site_name); ?>">
	  <?php endif; ?>
	    <?php if ($logo_small) : ?>
	      <img class="logo-img-sm d-block d-sm-none" src="<?php echo $logo_small; ?>" alt="<?php echo strip_tags($site_name); ?>" />
	    <?php endif; ?>
	  	
	    <?php if ($logo) : ?>
	      <img class="logo-img<?php echo $logo_sm_cls; ?>" src="<?php echo $logo; ?>" alt="<?php echo strip_tags($site_name); ?>" />
	    <?php else : ?>
	      <?php if ($site_name) : ?><span class="site-name<?php echo $logo_sm_cls; ?>"><?php echo $site_name; ?></span><?php endif; ?>
	      <?php if ($site_slogan) : ?><small class="site-slogan<?php echo $logo_sm_cls; ?>"><?php echo $site_slogan; ?></small><?php endif; ?>
	    <?php endif; ?>

	  <?php if ($hasLink): ?>
	  </a>
	  <?php endif; ?>
		<button type="button" class="close js-offcanvas-close" data-dismiss="modal" aria-hidden="true">×</button>
	</div>

	<div class="t4-off-canvas-body" data-effect="<?php echo $oc_menu_effect;?>">
		<jdoc:include type="modules" name="<?php echo $oc_pos_name ?>" style="<?php echo $oc_pos_style ?>" />
	</div>
</div>