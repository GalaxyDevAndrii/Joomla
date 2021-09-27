<?php
	$paramsTpl = JFactory::getApplication()->getTemplate(true)->params;

    $navigation_settings = $paramsTpl->get('navigation-settings');
    if (!filter_var($navigation_settings->get('oc_enabled'), FILTER_VALIDATE_BOOLEAN)) return;

	$show_in_lg = filter_var($navigation_settings->get('oc_showlg'), FILTER_VALIDATE_BOOLEAN);

    $breakpoint = $navigation_settings->get('option_breakpoint', 'lg');
    $oc_effect = $navigation_settings->get('oc_effect', 'left');
    switch ($oc_effect) {
        case 'left-reveal':
        case 'left-push':
        case 'left':
            break;
        default:
           $oc_effect = "left";
            break;
    }
    $oc_rightside = $navigation_settings->get('oc_rightside', '');
    if($oc_rightside){
        $oc_effect = str_replace("left", 'right', $oc_effect);
    }
    $lgsc = $show_in_lg ? '' : ' d-' . $breakpoint . '-none';

    // add bodyclass
    if ($show_in_lg) {
        \T4\T4::getInstance()->addBodyClass('oc-desktop');
    }

?>
<span id="triggerButton" class="btn js-offcanvas-trigger t4-offcanvas-toggle<?php echo $lgsc ?>" data-offcanvas-trigger="off-canvas-<?php echo $oc_effect;?>"><i class="fa fa-bars toggle-bars"></i></span>
