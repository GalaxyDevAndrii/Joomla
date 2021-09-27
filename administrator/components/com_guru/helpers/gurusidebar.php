<?php
/*------------------------------------------------------------------------
# com_guru
# ------------------------------------------------------------------------
# author    iJoomla
# copyright Copyright (C) 2013 ijoomla.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.ijoomla.com
# Technical Support:  Forum - http://www.ijoomla.com.com/forum/index/
-------------------------------------------------------------------------*/

defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.application.component.modellist');
jimport('joomla.utilities.date');

$controller_req = JFactory::getApplication()->input->get("controller", "");
$task = JFactory::getApplication()->input->get("task", "");
$tab_req = JFactory::getApplication()->input->get("tab", "");
$display_settings = "none";
$display_courses = "none";
$display_media = "none";
$display_quizzes = "none";
$display_projects = "none";
$display_scorm = "none";
$display_finances = "none";
$display_subscription = "none";
$display_icon1 = "none";
$display_icon2 = "none";
$display_icon3 = "none";
$display_logs = "none";
$display_certificates = "none";
$display_fields = "none";

$db = JFactory::getDBO();
$sql = " SELECT count(a.id) FROM #__guru_authors a, #__users u WHERE a.enabled=2 and a.userid=u.id";
$db->setQuery($sql);
$db->execute();
$pending_authors = $db->loadColumn();
$pending_authors = $pending_authors[0];
												
$li_settings = "";
$li_courses = "";
$li_media = "";
$li_quizzes = "";
$li_projects = "";
$li_scorm = "";
$li_finances = "";
$li_subscription = "";
$li_logs = "";
$li_teachers = "";
$li_students = "";
$li_comments = "";
$li_quizzes = "";
$li_certificates = "";
$li_commissions = "";
$li_fields = "";

if(($controller_req == "guruConfigs" && $tab_req != 4 && $tab_req != 1 && $tab_req != 8&& $tab_req != 9) || $controller_req == "guruLanguages"){
	$display_settings = "block";
	$li_settings = 'class="open"';
}
elseif($controller_req == "guruPrograms" || $controller_req == "guruPcategs" || ($controller_req == "guruConfigs" && $tab_req == 4)){
	$display_courses = "block";
	$li_courses = 'class="open"';
}
elseif($controller_req == "guruMedia" || $controller_req == "guruMediacategs" || ($controller_req == "guruConfigs" && $tab_req == 1)){
	$display_media = "block";
	$li_edia = 'class="open"';
}
elseif($controller_req == "guruQuiz" || $controller_req == "guruQuizCountdown"){
	$display_quizzes = "block";
	$li_quizzes = 'class="open"';
}
elseif($controller_req == "guruProjects"){
    $display_projects = "block";
    $li_projects = 'class="open"';
}
elseif($controller_req == "guruScorm"){
    $display_scorm = "block";
    $li_scorm = 'class="open"';
}
elseif($controller_req == "guruOrders" || $controller_req == "guruPromos" || $controller_req == "guruPlugins"){
	$display_finances = "block";
	$li_finances = 'class="open"';
}
elseif($controller_req == "guruSubplan" || $controller_req == "guruSubremind"){
	$display_subscription = "block";
	$li_subscription = 'class="open"';
}
elseif($controller_req == "guruLogs"){
	$display_logs = "block";
	$li_logs = 'class="open"';
}
elseif($controller_req == "guruAuthor" || ($controller_req == "guruConfigs" && $tab_req == 8) ){
	$display_teachers = "block";
	$li_teachers = 'class="open"';
}
elseif($controller_req == "guruCustomers" || ($controller_req == "guruConfigs" && $tab_req == 9) ){
	$display_students = "block";
	$li_students = 'class="open"';
}
elseif($controller_req == "guruKunenaForum"){
	$display_comments = "block";
	$li_comments = 'class="open"';
}
elseif($controller_req == "guruQuiz"){
	$li_quizzes = 'class="open"';
}
elseif($controller_req == "guruCertificate"){
	$li_certificates = 'class="open"';
    $display_certificates = "block";
}
elseif($controller_req == "guruCommissions"){
	$display_commissions = "block";
	$display_teachers = "block";
	$li_commissions = 'class="open"';
	$li_teachers = 'class="open"';
	
	if($task == 'pending'){
		$display_icon1 = "inline-block";
	}
	elseif($task == 'paid'){
		$display_icon2 = "inline-block";
	}
	elseif($task == 'history'){
		$display_icon3 = "inline-block";
	}
}
elseif($controller_req == "guruFields"){
    $li_fields = 'class="open"';
    $display_fields = "block";
}

?>

<div id="sidebar" class="sidebar">
    <ul class="nav nav-list">
     <li <?php if($controller_req == ""){ echo 'class="active"';} ?>>
        <a href="index.php?option=com_guru">
            <i class="icon-home"></i>
            <span class="menu-text"> <?php echo JText::_("GURU_DASHBOARD"); ?></span>
            
        </a>
     </li>
     
     <li <?php echo $li_settings; ?>>
        <a class="dropdown-toggle" href="#">
            <i class="icon-wrench"></i>
            <span class="menu-text">  <?php echo JText::_("GURU_TREESETTINGS"); ?> </span>
            <b class="arrow js-icon-angle-down"></b>
        </a>
        
        <ul class="submenu" style="display:<?php echo $display_settings; ?>;">
            <!--
            <li <?php if($controller_req == "guruConfigs" && $tab_req == 11){ echo 'class="active"';} ?> >
                <a href="index.php?option=com_guru&controller=guruConfigs&tab=11">
                    <i class="js-icon-double-angle-right"></i>
                    <?php echo JText::_("GURU_REGISTER_LICENSE"); ?>
                </a>
            </li>
        	-->
            <li <?php if($controller_req == "guruConfigs" && $tab_req == 0){ echo 'class="active"';} ?> >
                <a href="index.php?option=com_guru&controller=guruConfigs&tab=0">
                    <i class="js-icon-double-angle-right"></i>
                    <?php echo JText::_("GURU_GENERAL"); ?>
                </a>
            </li>
            <li <?php if($controller_req == "guruConfigs" && $tab_req == 2){ echo 'class="active"';} ?>>
                <a href="index.php?option=com_guru&controller=guruConfigs&tab=2">
                    <i class="js-icon-double-angle-right"></i>
                    <?php echo JText::_("GURU_LAYOUT"); ?>
                </a>
            </li>
            <li <?php if($controller_req == "guruConfigs" && $tab_req == 5){ echo 'class="active"';} ?>>
                <a href="index.php?option=com_guru&controller=guruConfigs&tab=5">
                    <i class="js-icon-double-angle-right"></i>
                    <?php echo JText::_("GURU_TREEEMAILS"); ?>
                </a>
            </li>
            <li <?php if($controller_req == "guruConfigs" && $tab_req == 6){ echo 'class="active"';} ?>>
                <a href="index.php?option=com_guru&controller=guruConfigs&tab=6">
                    <i class="js-icon-double-angle-right"></i>
                    <?php echo JText::_("GURU_PROMOTION_BOX"); ?>
                </a>
            </li>
            <!--
            <li <?php if($controller_req == "guruLanguages"){ echo 'class="active"';} ?>>
                <a href="index.php?option=com_guru&controller=guruLanguages&task=edit">
                    <i class="js-icon-double-angle-right"></i>
                    <?php echo JText::_("GURU_TREELANGUAGES"); ?>
                </a>
            </li>
        	-->
            <li <?php if($controller_req == "guruConfigs" && $tab_req == 10){ echo 'class="active"';} ?>>
                <a href="index.php?option=com_guru&controller=guruConfigs&tab=10">
                    <i class="js-icon-double-angle-right"></i>
                    <?php echo JText::_("GURU_SEO_URL_PATH"); ?>
                </a>
            </li>
            <li <?php if($controller_req == "guruConfigs" && $tab_req == 12){ echo 'class="active"';} ?>>
                <a href="index.php?option=com_guru&controller=guruConfigs&tab=12">
                    <i class="js-icon-double-angle-right"></i>
                    <?php echo JText::_("GURU_API_ORDERS"); ?>
                </a>
            </li>
        </ul>
     </li>
     
     <li <?php echo $li_teachers; ?>>
         <a class="dropdown-toggle" href="#">
            <i class="icon-book"></i>
            <span class="menu-text">  <?php echo JText::_("GURU_TREEAUTHOR"); ?> </span>
            <b class="arrow js-icon-angle-down"></b>
        </a>
        
        <ul class="submenu" style="display:<?php echo @$display_teachers; ?>;">
            <li <?php if($controller_req == "guruAuthor"){ echo 'class="active"';} ?>>
                <a href="index.php?option=com_guru&controller=guruAuthor&task=list">
                    <i class="js-icon-double-angle-right"></i>
                    <?php echo JText::_("GURU_MANAGE_TEACHERS"); ?>
                    <?php 
					if(intval($pending_authors) > 0){?>
                    	<span class="badge badge-important"><?php echo $pending_authors;?></span>
                    <?php 
					}?>
                </a>
            </li>
            <li <?php if($controller_req == "guruConfigs" && $tab_req == 8){ echo 'class="active"';} ?>>
                <a href="index.php?option=com_guru&controller=guruConfigs&tab=8">
                    <i class="js-icon-double-angle-right"></i>
                    <?php echo JText::_("GURU_SETTINGS"); ?>
                </a>
            </li>
            
            <li <?php echo $li_commissions; ?>>
                <a class="dropdown-toggle" href="#">
                    <span class="menu-text">  <?php echo JText::_("GURU_COMMISSIONS"); ?> </span>
                    <b class="arrow js-icon-angle-down"></b>
                </a>
                <ul class="submenu submenu-commissions" style="display:<?php echo @$display_commissions; ?>;">
                    <li <?php if($controller_req == "guruCommissions" && $task == 'list'){ echo 'class="active"';} ?>>
                        <a href="index.php?option=com_guru&controller=guruCommissions&task=list">
                            <?php echo JText::_("GURU_COMMISSION_PLAN"); ?>
                        </a>
                    </li>
                    <li <?php if($controller_req == "guruCommissions" && $task == 'pending'){ echo 'class="active"';} ?>>
                        <a href="index.php?option=com_guru&controller=guruCommissions&task=pending" onclick="document.getElementById('iconar').style.display='inline-block';">
                            <?php echo JText::_("GURU_AU_PENDING"); ?>
                        </a>
                    </li>
                    <li <?php if($controller_req == "guruCommissions" && $task == 'paid'){ echo 'class="active"';} ?>>
                        <a href="index.php?option=com_guru&controller=guruCommissions&task=paid" onclick="document.getElementById('iconar2').style.display='inline-block';">
                            <?php echo JText::_("GURU_O_PAID"); ?>
                        </a>
                    </li>
                    <li <?php if($controller_req == "guruCommissions" && $task == 'history'){ echo 'class="active"';} ?>>
                        <a href="index.php?option=com_guru&controller=guruCommissions&task=history" onclick="document.getElementById('iconar3').style.display='inline-block';">
                            <?php echo JText::_("GURU_COMMISSIONS_HISTORY"); ?>
                        </a>
                    </li>
                </ul>
            </li>
            
        </ul>
     </li>
     
     <li <?php echo $li_students; ?>>
         <a class="dropdown-toggle" href="#">
            <i class="icon-users"></i>
            <span class="menu-text">  <?php echo JText::_("GURU_TREECUSTOMERS"); ?> </span>
            <b class="arrow js-icon-angle-down"></b>
        </a>
        
        <ul class="submenu" style="display:<?php echo @$display_students; ?>;">
            <li <?php if($controller_req == "guruCustomers"){ echo 'class="active"';} ?>>
                <a href="index.php?option=com_guru&controller=guruCustomers">
                    <i class="js-icon-double-angle-right"></i>
                    <?php echo JText::_("GURU_MANAGE_CUSTOMERS"); ?>
                </a>
            </li>
            <li <?php if($controller_req == "guruConfigs" && $tab_req == 9){ echo 'class="active"';} ?>>
                <a href="index.php?option=com_guru&controller=guruConfigs&tab=9">
                    <i class="js-icon-double-angle-right"></i>
                    <?php echo JText::_("GURU_REGISTRTION"); ?>
                </a>
            </li>
        </ul>
     </li>

     <li <?php echo $li_fields; ?>>
        <a href="index.php?option=com_guru&controller=guruFields">
            <i class="icon-list"></i>
            <?php echo JText::_("GURU_TREEFIELDS"); ?>
        </a>
    </li>
     
     <li <?php echo $li_courses; ?>>
       <a class="dropdown-toggle" href="#">
            <i class="icon-eye-open"></i>
             <span class="menu-text">  <?php echo JText::_("GURU_TREECOURSE"); ?> </span>
             <b class="arrow js-icon-angle-down"></b>
    
        </a>
        <ul class="submenu" style="display:<?php echo $display_courses; ?>;">
            <li <?php if($controller_req == "guruPrograms"){ echo 'class="active"';} ?>>
                <a href="index.php?option=com_guru&controller=guruPrograms">
                    <i class="js-icon-double-angle-right"></i>
                    <?php echo JText::_("GURU_TREECOURSE"); ?>
                </a>
            </li>
            <li <?php if($controller_req == "guruPcategs"){ echo 'class="active"';} ?>>
                <a href="index.php?option=com_guru&controller=guruPcategs">
                    <i class="js-icon-double-angle-right"></i>
                    <?php echo JText::_("GURU_TREECOURSECAT"); ?>
                </a>
            </li>
            <li <?php if($controller_req == "guruConfigs" && $tab_req == 4 ){ echo 'class="active"';} ?> style="display:none;">
                <a href="index.php?option=com_guru&controller=guruConfigs&tab=4">
                    <i class="js-icon-double-angle-right"></i>
                    <?php echo JText::_("GURU_PROGRESS_BAR"); ?>
                </a>
            </li>
        </ul>
    </li>
    
    <li <?php echo $li_comments; ?>>
		<a class="dropdown-toggle" href="#">
			<i class="icon-comments-2"></i>
			<span class="menu-text">  <?php echo JText::_("GURU_COMMENTS"); ?> </span>
			<b class="arrow js-icon-angle-down"></b>
		</a>
		<ul class="submenu" style="display:<?php echo @$display_comments; ?>;">
            <li <?php if($controller_req == "guruKunenaForum"){ echo 'class="active"';} ?>>
                <a href="index.php?option=com_guru&controller=guruKunenaForum">
                    <i class="js-icon-double-angle-right"></i>
                    <?php echo JText::_("GURU_KUNENA_FORUM1"); ?>
                </a>
            </li>
		</ul>
    </li>
    
    <li <?php echo $li_media; ?>>
        <a class="dropdown-toggle" href="#">
            <i class="icon-picture"></i>
             <span class="menu-text">  <?php echo JText::_("GURU_TASK_MEDIA"); ?> </span>
             <b class="arrow js-icon-angle-down"></b>
    
        </a>
        <ul class="submenu" style="display:<?php echo $display_media; ?>;">
            <li <?php if($controller_req == "guruMedia" && $task == "edit"){ echo 'class="active"';} ?>>
                <a href="index.php?option=com_guru&controller=guruMedia&task=edit&cid[]=0">
                    <i class="js-icon-double-angle-right"></i>
                    <?php echo JText::_("GURU_TREE_NEW_MEDIA"); ?>
                </a>
            </li>
			<li <?php if($controller_req == "guruMedia" && $task != "edit" && $task != "mass"){ echo 'class="active"';} ?>>
                <a href="index.php?option=com_guru&controller=guruMedia">
                    <i class="js-icon-double-angle-right"></i>
                    <?php echo JText::_("GURU_TREEMEDIA"); ?>
                </a>
            </li>
            <li <?php if($controller_req == "guruMediacategs"){ echo 'class="active"';} ?>>
                <a href="index.php?option=com_guru&controller=guruMediacategs">
                    <i class="js-icon-double-angle-right"></i>
                    <?php echo JText::_("GURU_TREEMEDIACAT"); ?>
                </a>
            </li>
            <li <?php if($controller_req == "guruConfigs" && $tab_req == 1){ echo 'class="active"';} ?>>
                <a href="index.php?option=com_guru&controller=guruConfigs&tab=1">
                    <i class="js-icon-double-angle-right"></i>
                    <?php echo JText::_("GURU_MEDIA"); ?>
                </a>
            </li>
            <li <?php if($controller_req == "guruMedia" && $task == "mass"){ echo 'class="active"';} ?>>
                <a href="index.php?option=com_guru&controller=guruMedia&task=mass">
                    <i class="js-icon-double-angle-right"></i>
                    <?php echo JText::_("GURU_TREE_NEW_MEDIA_MASS"); ?>
                </a>
            </li>
        </ul>
    </li>
    
    <li <?php echo $li_quizzes; ?>>
        <a href="index.php?option=com_guru&controller=guruQuiz">
            <i class="icon-checkmark-2"></i>
            <?php echo JText::_("GURU_TREEQUIZ"); ?>
        </a>
    </li>

    <li <?php echo $li_projects; ?>>
        <a href="index.php?option=com_guru&controller=guruProjects">
            <i class="icon-file-2"></i>
            <?php echo JText::_("GURU_TREEPROJECTS"); ?>
        </a>
    </li>

    <li <?php echo $li_scorm; ?>>
        <a href="index.php?option=com_guru&controller=guruScorm">
            <i class="icon-file-add"></i>
            <?php echo JText::_("GURU_TREESCORM"); ?>
        </a>
    </li>
    
    <li <?php echo $li_certificates; ?>>
        <a class="dropdown-toggle" href="#">
            <i class="icon-bookmark"></i>
            <span class="menu-text">  <?php echo JText::_("GURU_CERTIFICATE"); ?> </span>
            <b class="arrow js-icon-angle-down"></b>
        </a>

        <ul class="submenu" style="display:<?php echo $display_certificates; ?>;">
            <li <?php if($controller_req == "guruCertificate" && $task != "list"){ echo 'class="active"';} ?>>
                <a href="index.php?option=com_guru&controller=guruCertificate">
                    <i class="js-icon-double-angle-right"></i>
                    <span class="menu-text"><?php echo JText::_("GURU_CERTIFICATE"); ?> </span>
                </a>
            </li>
            <li <?php if($controller_req == "guruCertificate" && $task == "list"){ echo 'class="active"';} ?>>
                <a href="index.php?option=com_guru&controller=guruCertificate&task=list">
                    <i class="js-icon-double-angle-right"></i>
                    <span class="menu-text"><?php echo JText::_("GURU_CERTIFICATE_LIST"); ?> </span>
                </a>
            </li>
        </ul>
    </li>
    
    <li <?php echo $li_finances; ?>>
        <a class="dropdown-toggle" href="#">
            <i class="icon-cart"></i>
            <span class="menu-text">  <?php echo JText::_("GURU_FINANCES"); ?> </span>
            <b class="arrow js-icon-angle-down"></b>
    
        </a>
        <ul class="submenu" style="display:<?php echo $display_finances; ?>;">
            <li <?php if($controller_req == "guruOrders"){ echo 'class="active"';} ?>>
                <a href="index.php?option=com_guru&controller=guruOrders">
                    <i class="js-icon-double-angle-right"></i>
                    <?php echo JText::_("GURU_TREEORDERS"); ?>
                </a>
            </li>
            <li <?php if($controller_req == "guruPromos"){ echo 'class="active"';} ?>>
                <a href="index.php?option=com_guru&controller=guruPromos">
                    <i class="js-icon-double-angle-right"></i>
                    <?php echo JText::_("GURU_TREEPROMOS"); ?>
                </a>
            </li>
            <li <?php if($controller_req == "guruPlugins"){ echo 'class="active"';} ?>>
                <a href="index.php?option=com_guru&controller=guruPlugins">
                    <i class="js-icon-double-angle-right"></i>
                    <?php echo JText::_("GURU_PAYMENT_PLUGINS"); ?>
                </a>
            </li>
        </ul>
     </li>
     
     <li <?php echo $li_subscription; ?>>
        <a class="dropdown-toggle" href="#">
            <i class="icon-archive"></i>
            <span class="menu-text">  <?php echo JText::_("GURU_SUBSCRIPTIONS"); ?> </span>
            <b class="arrow js-icon-angle-down"></b>
        </a>
        <ul class="submenu" style="display:<?php echo $display_subscription; ?>;">
            <li <?php if($controller_req == "guruSubplan"){ echo 'class="active"';} ?>>
                <a href="index.php?option=com_guru&controller=guruSubplan">
                    <i class="js-icon-double-angle-right"></i>
                    <?php echo JText::_("GURU_SUBS_PLANS"); ?>
                </a>
            </li>
            <li <?php if($controller_req == "guruSubremind"){ echo 'class="active"';} ?>>
                <a href="index.php?option=com_guru&controller=guruSubremind">
                    <i class="js-icon-double-angle-right"></i>
                    <?php echo JText::_("GURU_EMAIL_REMIND"); ?>
                </a>
            </li>
        </ul>
    </li>
    
    <li <?php echo $li_logs; ?>>
        <a class="dropdown-toggle" href="#">
            <i class="icon-list"></i>
            <span class="menu-text">  <?php echo JText::_("GURU_LOGS"); ?> </span>
            <b class="arrow js-icon-angle-down"></b>
        </a>
        <ul class="submenu" style="display:<?php echo $display_logs; ?>;">
            <li <?php if($controller_req == "guruLogs" && $task == "emails"){ echo 'class="active"';} ?>>
                <a href="index.php?option=com_guru&controller=guruLogs&task=emails">
                    <i class="js-icon-double-angle-right"></i>
                    <?php echo JText::_("GURU_SYSTEM_EMAILS"); ?>
                </a>
            </li>
            <li <?php if($controller_req == "guruLogs" && $task == "purchases"){ echo 'class="active"';} ?>>
                <a href="index.php?option=com_guru&controller=guruLogs&task=purchases">
                    <i class="js-icon-double-angle-right"></i>
                    <?php echo JText::_("GURU_PURCHASES"); ?>
                </a>
            </li>
        </ul>
    </li>
    <?php
    // Alkis - hid Guru Pro left sidebar Help menu
    /*
    <li>
        <a class="dropdown-toggle" href="#">
            <i class="icon-question-sign"></i>
            <span class="menu-text">  <?php echo JText::_("GURU_HELP"); ?> </span>
            <b class="arrow js-icon-angle-down"></b>
        </a>
        <ul class="submenu">
            <li class="">
                <a target="_blank" href="https://member.joomlart.com/forums/ijoomla">
                    <i class="js-icon-double-angle-right"></i>
                    <?php echo JText::_("VIEWDSADMINSUPPORT"); ?>
                </a>
            </li>
            <li class="">
                <a target="_blank" href="http://tiny.cc/guru-videos">
                    <i class="js-icon-double-angle-right"></i>
                    <?php echo JText::_("VIEWDSADMINMANUAL"); ?>
                </a>
            </li>
            <li class="">
                <a target="_blank" href="https://js-socialize.demo.joomlart.com/">
                    <i class="js-icon-double-angle-right"></i>
                    <?php echo JText::_("VIEWTREETEMPLATES"); ?>
                </a>
            </li>
            <li class="">
                <a target="_blank" href="http://www.ijoomla.com">
                    <i class="js-icon-double-angle-right"></i>
                    <?php echo JText::_("VIEWDSADMINSITE"); ?>
                </a>
            </li>
            <li class="">
                <a target="_blank" href="https://member.joomlart.com/downloads/ijoomla/guru/guru-pro">
                    <i class="js-icon-double-angle-right"></i>
                    <?php echo JText::_("VIEWTREELV"); ?>
                </a>
            </li>
        </ul>
    </li><!--end help-->
    */
    ?>
    <?php
    // Alkis - hid Guru Pro left sidebar About menu item
    /*
    <li <?php if($controller_req == "guruAbout"){ echo 'class="active"';} ?>>
        <a href="index.php?option=com_guru&controller=guruAbout">
            <i class="icon-star"></i>
            <span class="menu-text"><?php echo JText::_("GURU_TREEABOUT"); ?> </span>
        </a>
    </li><!--end about-->
    */
    ?>
 </ul>
 	<div id="sidebar-collapse" class="sidebar-collapse">
    	<i class="js-icon-double-angle-left"></i>
	</div><!--end collapse button div -->
</div><!-- end the guru menu-->
<?php
?>