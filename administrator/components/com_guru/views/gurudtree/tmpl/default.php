<?php


/*------------------------------------------------------------------------
# com_guru
# ------------------------------------------------------------------------
# author    iJoomla
# copyright Copyright (C) 2013 ijoomla.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.ijoomla.com
# Technical Support:  Forum - http://www.ijoomla.com/forum/index/

-------------------------------------------------------------------------*/

defined( '_JEXEC' ) or die( 'Restricted access' );

function getGuruVersion(){			
	$component = "com_guru";
	$xml_file = "guru.xml";
	
	$version = '';
	$path = JPATH_ROOT.DIRECTORY_SEPARATOR.'administrator'.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.$component.DIRECTORY_SEPARATOR.$xml_file;
	if(file_exists($path)){
		$data = implode("", file($path));
		$pos1 = strpos($data,"<version>");
		$pos2 = strpos($data,"</version>");
		$version = substr($data, $pos1+strlen("<version>"), $pos2-$pos1-strlen("<version>"));
		return $version;
	}
	else{
		return "";
	}
}

function getGuruCurrentVersionData(){
    return;
    $cacheFile = JPATH_ADMINISTRATOR . '/components/com_guru/version_cache.txt';
    if (file_exists($cacheFile)) {
        $cache = file_get_contents($cacheFile);
    } else {
        $cache = array();
    }

    $cacheData = new JRegistry($cache);
    $lastUpdate = $cacheData->get('last_update');
    $version = $cacheData->get('version');
    if ($version 
        && $lastUpdate
        && $lastUpdate >= date('Y-m-d')) {
        return $version;
    }

    $component = "com_guru";
    $version = "";
    $data = 'https://www.ijoomla.com/ijoomla_latest_version.txt';
    $uri = JURI::root();
    
    if(strpos(" ".$uri, "localhost") !== FALSE){
        $data = 'http://www.ijoomla.com/ijoomla_latest_version.txt';
    }

    $extensions = get_loaded_extensions();
    $text = "";
    
    if(in_array("curl", $extensions)){
        $ch = @curl_init($data);
        @curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        @curl_setopt($ch, CURLOPT_TIMEOUT, 10);                             
        
        $version = @curl_exec($ch);
        if(isset($version) && trim($version) != ""){                    
            $pattern = "/3.0_".$component."=(.*);/msU"; 
            preg_match($pattern, $version, $result);
            if(is_array($result) && count($result) > 0){
                $version = trim($result["1"]);
            }
        }
    }
    else{
        $version = file_get_contents('www.ijoomla.com/ijoomla_latest_version.txt');
    }   

    $cacheData->set('last_update', date('Y-m-d'));
    $cacheData->set('version', $version);

    JFile::write($cacheFile, $cacheData->toString());

    return $version;
}

$latest_version = getGuruCurrentVersionData();
$installed_version = getGuruVersion();

$db = JFactory::getDBO();
$sql = "select * from #__guru_config";
$db->setQuery($sql);
$db->execute();
$configs = $db->loadAssocList();

$allow_teacher_action = json_decode($configs["0"]["st_authorpage"]);//take all the allowed action from administator settings

$teacher_aprove = @$allow_teacher_action->teacher_aprove; //allow or not aprove teacher
$params = JComponentHelper::getParams('com_users');
$useractivation = $params->get('useractivation');
		
?>

<div class="g_admin_top_wrap">
<div class="ui-app">

    <div class="navbar">
        <div class="navbar-inner">
            <div class="container-fluid">
                <div class="nav-collapse collapse">
                    <div class="pull-left">
  						<?php // Alkis - replaced Guru logo with Pre-Licensing logo, removed link to iJoomla
  						/* 
                        <a target="_blank" href="http://guru.ijoomla.com/"><img src="components/com_guru/images/guru-logo.png" /></a>
  						<span class="badge badge-important" id="guru-version">V <?php echo $installed_version; ?></span>
                        */
  						?>                        
  						<img src="components/com_guru/images/logo.png" />  						                        
                        <?php  // Alkis - removed new version check and notification
                          /*
                        <?php
                        	if(false){
								echo '&nbsp;&nbsp;<span class="white-color">'.JText::_("GURU_NEW_VERSION_AVAILABLE").": V ".$latest_version.'&nbsp; (<a href="http://www.ijoomla.com/redirect/guru/changelog.htm" target="_blank">'.JText::_("GURU_CHANGE_LOG").'</a>)  (<a href="http://www.ijoomla.com/redirect/general/latestversion.htm" target="_blank">'.JText::_("GURU_DOWNLOAD").'</a>) </span>';
							} ?>
                        */
                        ?>
                    </div>
                    <div class="pull-right">
                        <div class="ui-app">
                            <div class="navbar2">
                                <div class="g_navbar-inner">
                                    <div class="container-fluid">
                                        <div class="nav-collapse collapse">
                                            <div class="span12">
                                                <div id="g_rating">
                          							<?php // Alkis - hid socials                          							
                          							/*
                                                    <ul class="pull-right">                          							
                                                        <li class="pull-right"><a href="http://twitter.com/ijoomla" target="_blank" />
                                                            <?php
                                                            echo '<span class="small-text">'.JText::_("GURU_TWITTER").'</span>';
                                                            ?>
                                                            <img src="components/com_guru/images/icons/twitter.png" />
                                                            </a></li>
                                                        <li class="pull-right"><a href="https://www.facebook.com/ijoomla" target="_blank" />
                                                            <?php
                                                            echo '<span class="small-text">'.JText::_("GURU_FACEBOOK").'</span>';
                                                            ?>
                                                            <img src="components/com_guru/images/icons/facebook.png" />
                                                            </a></li>
                                                     </ul>
                                                     */
  													 ?>
                                                      <?php
														$pending_authors = $this->getPendingAuthors();
														$new_authors = "";
														$link ="";
														if($teacher_aprove == 0){
															if($useractivation == 1){
																$new_authors = $this->getNewAuthorsYesSelf();
																$link = 'index.php?option=com_guru&controller=guruAuthor&task=list&filter_alert=4';	
															}
															else{
																$new_authors = $this->getNewAuthorsYes();
																$link = 'index.php?option=com_guru&controller=guruAuthor&task=list';
															}
														}
														else{
															if($useractivation == 1){
																$new_authors = $this->getNewAuthorsNoSelf();
																$link = 'index.php?option=com_guru&controller=guruAuthor&task=list&filter_alert=1';	
															}
															else{
																$new_authors = $this->getNewAuthorsNoNoneAdmin();
																$link = 'index.php?option=com_guru&controller=guruAuthor&task=list&filter_alert=2';	
															}
														
														}
														
														$pending_orders = $this->getPendingOrders();
														
														$sum = intval($pending_authors)+ intval($new_authors)+ $pending_orders;
														
														
                                                     ?>                                                     
                                                         <ul class="nav ace-nav pull-right">
                                                            <li class="" id="guru-not-content">
                                                                <a href="#" class="dropdown-toggle" id="guru-dropdown-toggle" onclick="javascript:alertNotification();">
                                                                    <?php echo JText::_("GURU_NOTIFICATIONS"); ?>
                                                                    <i class="js-icon-bell-alt" id="icon-bell"></i>
                                                                    <div class="badge badge-important" id="badge-important"><?php if($sum >0){echo intval($sum);} ?></div>
                                                                </a>
                                                                <?php
																if(intval($pending_authors) > 0 || intval($new_authors) > 0 || intval($pending_orders) > 0){?>
                                                                    <ul class="pull-right dropdown-navbar navbar-js dropdown-menu dropdown-caret dropdown-closer">
                                                                        <li class="nav-header">
                                                                            <i class="js-icon-warning-sign"></i>
                                                                            <?php echo intval($sum)." ".JText::_("GURU_NOTIFICATIONS"); ?>
                                                                        </li>
                                                                        <?php
                                                                            if(intval($new_authors) > 0){
                                                                        ?>
                                                                                <li>
                                                                                    <a href="<?php echo $link; ?>">
                                                                                        <div class="clearfix">
                                                                                            <span class="pull-left">
                                                                                                <?php echo JText::_("GURU_AUTHORS_NEW"); ?>
                                                                                            </span>
                                                                                            <span class="pull-right orange">
                                                                                                <?php echo intval(intval($new_authors)); ?>
                                                                                            </span>
                                                                                        </div>
                                                                                    </a>
                                                                                </li>
                                                                        <?php
                                                                            }
                                                                        ?>
                                                                        <?php
                                                                            if(intval($pending_authors) > 0){
                                                                        ?>
                                                                                <li>
                                                                                    <a href="index.php?option=com_guru&controller=guruAuthor&task=list&filter_status=2">
                                                                                        <div class="clearfix">
                                                                                            <span class="pull-left">
                                                                                                <?php echo JText::_("GURU_PENDING_AUTHORS"); ?>
                                                                                            </span>
                                                                                            <span class="pull-right red">
                                                                                                <?php echo intval(intval($pending_authors)); ?>
                                                                                            </span>
                                                                                        </div>
                                                                                    </a>
                                                                                </li>
                                                                        <?php
                                                                            }
                                                                        ?>
                                                                        <?php
                                                                            if(intval($pending_orders) > 0){
                                                                        ?>
                                                                                <li>
                                                                                    <a href="index.php?option=com_guru&controller=guruOrders&filter_status=Pending">
                                                                                        <div class="clearfix">
                                                                                            <span class="pull-left">
                                                                                                <?php echo JText::_("GURU_PENDING_ORDERS"); ?>
                                                                                            </span>
                                                                                            <span class="pull-right red">
                                                                                                <?php echo intval(intval($pending_orders)); ?>
                                                                                            </span>
                                                                                        </div>
                                                                                    </a>
                                                                                </li>
                                                                        <?php
                                                                            }
                                                                        ?>
                                                                    </ul>
                                                               <?php
																}
																else{
																?>
                                                                     <ul class="pull-right dropdown-navbar navbar-js dropdown-menu dropdown-caret dropdown-closer">
                                                                        <li class="nav-header">
                                                                            <i class="js-icon-warning-sign"></i>
                                                                            <?php echo intval($sum)." ".JText::_("GURU_NOTIFICATIONS"); ?>
                                                                        </li>
                                                                        <li>
                                                                            <a>
                                                                                <div class="clearfix">
                                                                                    <span class="pull-left">
                                                                                        <?php echo JText::_("GURU_NO_NOTIFICATION"); ?>
                                                                                    </span>
                                                                                </div>
                                                                            </a>
                                                                        </li>
                                                                     </ul>  
																<?php
																}
															   ?>    
                                                            </li>
                                                        </ul>
                                                 </div>    
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div><!--end nav bar-->
</div>
<div class="clearfix"></div>


<div class="clearfix"></div>
</div>