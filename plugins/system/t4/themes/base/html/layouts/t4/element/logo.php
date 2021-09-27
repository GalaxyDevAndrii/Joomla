<?php
$doc = $displayData->doc;
$conf = JFactory::getConfig();

$site_settings = $doc->params->get('site-settings');

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

$hasLink = !empty($displayData->params) && !empty($displayData->params['nolink']) ? false : true;
?>
<div class="navbar-brand <?php echo $logo_cls; ?>">
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
</div>
