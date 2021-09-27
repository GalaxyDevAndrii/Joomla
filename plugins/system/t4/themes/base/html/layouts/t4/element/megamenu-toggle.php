<?php
	$paramsTpl = JFactory::getApplication()->getTemplate(true)->params;

    $navigation_settings = $paramsTpl->get('navigation-settings');

    if (!filter_var($navigation_settings->get('mega_showsm'), FILTER_VALIDATE_BOOLEAN)) return;

    $menuType = $navigation_settings->get('menu_type', 'mainmenu');
    $navid = 't4-megamenu-' . $menuType;
?>
<nav class="navbar-expand-<?php echo $navigation_settings->get('option_breakpoint', 'lg')?>">
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#<?php echo $navid ?>" aria-expanded="false" aria-label="Toggle navigation">
        <i class="fa fa-bars toggle-bars"></i>
    </button>
</nav>
