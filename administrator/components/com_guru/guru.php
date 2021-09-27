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
if (!defined('DS')) define('DS', DIRECTORY_SEPARATOR);

require_once (JPATH_COMPONENT.DIRECTORY_SEPARATOR.'controller.php');
require_once( JPATH_COMPONENT.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'helper.php' );

//------------------------------------------------------------
$order = JFactory::getApplication()->input->get("order", array(), "raw");
$cid = JFactory::getApplication()->input->get("cid", array(), "raw");
$task = JFactory::getApplication()->input->get("task", "", "raw");


if(is_array($order) && count($order) > 0 && is_array($cid) && count($cid) > 0 && trim($task) == ""){
	JFactory::getApplication()->input->set("task", "saveOrderAjax");
}
//------------------------------------------------------------

JHTML::_('behavior.modal');

$controller = JFactory::getApplication()->input->get('controller');

if($controller){
	$path = JPATH_COMPONENT.DIRECTORY_SEPARATOR.'controllers'.DIRECTORY_SEPARATOR.$controller.'.php';
	if(file_exists($path)){
		require_once($path);
	} 
	else{
	 	$controller = '';
	}
}

$ajax_req = JFactory::getApplication()->input->get("no_html", 0, "raw");
$tmpl = JFactory::getApplication()->input->get("tmpl", "", "raw");
$squeeze = JFactory::getApplication()->input->get("sbox", 0, "raw");
$task = JFactory::getApplication()->input->get("task", "", "raw");
$p = JFactory::getApplication()->input->get("p", "0", "raw");

$export = JFactory::getApplication()->input->get("export", "", "raw");
$export1 = JFactory::getApplication()->input->get("export1", "", "raw");

$lang = JFactory::getLanguage();
$dir = $lang->get('rtl');

if(intval($dir) == 1 && $tmpl != "component"){
?>
	<style>
		#js-cpanel .main-content {
			margin-right: 190px !important;
			margin-left: 0px !important;
		}
		
		#js-cpanel .nav-list > li .submenu > li > a{
			padding: 7px 37px 8px 0px !important;
		}
		
		#js-cpanel .nav-list > li a > .arrow{
			right: auto !important;
			left:9px !important;
		}
	</style>
<?php
}

if(($controller == "guruProjects" || $controller == "guruScorm") && $task == "edit" && $tmpl == "component"){
?>
	<script type="text/javascript" language="javascript">
		var choose_file_lang = "<?php echo JText::_("GURU_CHOOSE_FILE"); ?>";
	</script>
<?php
}

if($export != "" || $export1 != "" ){
	$classname = "guruAdminController".$controller;
    $ajax_req = JFactory::getApplication()->input->get("no_html", 0);
	$tmpl = JFactory::getApplication()->input->get("tmpl", "");
    $squeeze = JFactory::getApplication()->input->get("sbox", 0);
    
    $controller = new $classname();
	$controller->execute ($task);
	$controller->redirect();
}
elseif(!$ajax_req && $tmpl !="component" && $task != "savesbox"&& $task != "save2" && $task != "export_button" && $task != "export" && $task != "savequizzes" && $task !="savequestionedit" && $task != "savequestion" && $task != "list_of_modules" && $task !="guru_file_uploader" && $task != "getcoursecost" && $task != "checkExistingUser" && $task != "checkCommissionPlan" && $task != "addcourse_ajax" && $task != "publish_un_ajax" && $task != "delete_image_ajax" && $task != "saveOrderG" && $task != "saveOrderS" && $task != "deleteFinalQuiz" && $task != "deleteGroup" && $task != "deleteScreen" && $task != "delete_categ_image" && $task != "check_values" && $task != "delete_course_image" && $task != "ajax_request" && $task != "ajax_request2" && $task != "ajax_request3" && $task != "saveOrderQuestions" && $task != "savequestionandclose" && $task != "getTeacherCoursesSelect" && $task != "check_lesson_title"){
?>
<div id="js-cpanel">
	<script type="text/javascript" language="javascript">
		var choose_file_lang = "<?php echo JText::_("GURU_CHOOSE_FILE"); ?>";
	</script>
	<?php
        // start add jQuery script ----------------------------------
	 //$doc = JFactory::getDocument();
	 //echo'<pre> Head DATA:';print_r(get_class_methods($doc));echo'</pre>'
	 $openInModal = JFactory::getApplication()->input->get("openModal", "0", "raw");
	 if($openInModal){
    ?>
    	<script type="text/javascript"> var openInModal = true;</script>
    <?php
    }

    ?>
        <script type="text/javascript" language="javascript">
			jQuery = jQuery.noConflict(true);
			$ = jQuery;
		</script>
		<script type="text/javascript" id ="load-jquery-migrate">
			var element = document.getElementById('load-jquery-migrate');
			if (typeof jQuery.migrateWarnings == 'undefined' && typeof openInModal == 'undefined' ) {
				document.write('<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-migrate/1.4.1/jquery-migrate.min.js" async="async"><\/script>');
			}
			element.parentNode.removeChild(element);
		</script>
		
		<script type="text/javascript" src="components/com_guru/js/jquery-dropdown.js"></script>
	<?php
        	if($controller == ""){
	?>
				<script type="text/javascript" src="components/com_guru/js/jquery.flot.js"></script>
				<script type="text/javascript" src="components/com_guru/js/jquery.flot.time.js"></script>
    <?php
				include_once(JPATH_SITE.DIRECTORY_SEPARATOR."administrator".DIRECTORY_SEPARATOR."components".DIRECTORY_SEPARATOR."com_guru".DIRECTORY_SEPARATOR."helpers".DIRECTORY_SEPARATOR."amcharts".DIRECTORY_SEPARATOR."daily_chart.php");
			}
	?>	
    	
    	<script type="text/javascript" src="components/com_guru/js/ace-elements.min.js"></script>
        <script type="text/javascript" src="components/com_guru/js/typeahead.jquery.min.js"></script>
		<script type="text/javascript" src="components/com_guru/js/ace.min.js"></script>
        
        <script type="text/javascript" src="components/com_guru/js/jquery.DOMWindow.js"></script>
        <script type="text/javascript" src="components/com_guru/js/removeJoomlaCMSObjectText.js"></script>
		
	<?php
		// stop add jQuery script ----------------------------------
	
    $classname = "guruAdminController".$controller;
    $ajax_req = JFactory::getApplication()->input->get("no_html", 0);
    $tmpl = JFactory::getApplication()->input->get("tmpl", "");
    $squeeze = JFactory::getApplication()->input->get("sbox", 0);
    $task = JFactory::getApplication()->input->get("task", "");
    
    $controller = new $classname();
    $task = JFactory::getApplication()->input->get('task');

    ?>
	<div id="admin_content_wrapper">
		<?php
        if (!$ajax_req && $tmpl !="component" && $task != "savesbox"&& $task != "save2" && $task != "export_button" && $task != "export" && $task != "savequizzes" && $task !="savequestionedit" && $task != "savequestion"&& $task !="guru_file_uploader" && $task != "getplans" && $task != "setrenew" && $task != "setpromo" && $task != "savequestionandclose"){
        require_once (JPATH_COMPONENT_ADMINISTRATOR.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'gurusidebar.php');
        }
        ?>
        <!--end sidebar-->
        <div class="main-content">
        	
           
        	<div class="page-content">
            <div class="page-header clearfix no-padding">
            	<?php 

					$pageTitle = "";
					$class_page_title = "";
					$image_guru_top = "";
					$tab = JFactory::getApplication()->input->get("tab", "");
					$e = JFactory::getApplication()->input->get("e", "");
					
					switch ($classname) {
						case "guruAdminController":
							$pageTitle = JText::_('GURU_DASHBOARD');	
							$image_guru_top = '<a href="http://www.ijoomla.com" target="_blank"><img src="components/com_guru/images/ijoomla-logo.png"></a>';
							break;
						case "guruAdminControllerguruConfigs":
							$pageTitle = JText::_('GURU_SETTINGS');
							$class_page_title = "";

							if($tab == 0){
								$pageTitle .=" ".">"." ".JText::_('GURU_GENERAL');
							}
							if($tab == 2){
								$pageTitle .=" ".">"." ".JText::_('GURU_LAYOUT');
							}
							if($tab == 5){
								$pageTitle .=" ".">"." ".JText::_('GURU_EMAIL');
							}
							if($tab == 6){
								$pageTitle .=" ".">"." ".JText::_('GURU_CONTENT');
							}
							if($tab == 1){
								$pageTitle = JText::_('GURU_MEDIA');
							}
							if($tab == 4){
								$pageTitle = JText::_('GURU_PROGRESS_BAR');
							}
							if($tab == 10){
								$pageTitle .=" ".">"." ".JText::_('GURU_SEO_URL_PATH');
							}
							if($tab == 11){
								$pageTitle = JText::_('GURU_SEO_LICENSE_INFO');
								$class_page_title = "red-info";
							}
							break;
						case "guruAdminControllerguruLanguages":
							$pageTitle = JText::_('GURU_SETTINGS')." ".">"." ".JText::_('GURU_TREELANGUAGES');
							break;
						case "guruAdminControllerguruAuthor":
							if($task == "list"){
								$pageTitle = JText::_('GURU_TREEAUTHOR');
							}
							if($task == "edit" || $task == "next"){
								$pageTitle = JText::_('GURU_AU_AUTHOR_DETAILS');
							}
							break;
						case "guruAdminControllerguruCustomers":
							if($task == ""){
								$pageTitle = JText::_('GURU_TREECUSTOMERS');
							}
							if($task == "edit"){
								$pageTitle = JText::_('GURU_STUDENT_DETAILS');
							}
							break;
						case "guruAdminControllerguruPrograms":
							if($task != "selectCourse" && $task != "edit"  ){
								$pageTitle = JText::_('GURU_COURSEMAN');
							}
							if($task == "edit"){
								$pageTitle = JText::_('GURU_EDITCOURSE');
							}
							break;
						case "guruAdminControllerguruPcategs":
							if($task == "add"){
								$pageTitle = JText::_('GURU_NEWCATEGORY');
							}
							if($task == "edit"){
								$pageTitle = JText::_('GURU_EDITCATEGORY');
							}
							if($task == ""){
								$pageTitle = JText::_('GURU_CSCAT_MANAGER');
							}
							break;
						case "guruAdminControllerguruKunenaForum":
							$pageTitle = JText::_('GURU_KUNENA_FORUM1');
							break;	
						case "guruAdminControllerguruMedia":
							if($task != "mass"){
								$pageTitle = JText::_('GURU_TREEMEDIA');
							}
							elseif($task == "mass"){
								$pageTitle = JText::_('GURU_MEDIA_MASS_TITLE');
							}
							break;	
						case "guruAdminControllerguruMediacategs":
							$pageTitle = JText::_('GURU_MEDIACATEGS');
							break;	
						case "guruAdminControllerguruQuiz":
							if($task == ""){
								$pageTitle = JText::_('GURU_Q_QUIZ_MANAGER');
							}
							if($task == "editZ"){
								$pageTitle = JText::_('GURU_NEWQUIZ');
							}
							if($task == "edit" && $e == 1){
								$pageTitle = JText::_('GURU_EDITQUIZ');
							}
							break;
						case "guruAdminControllerguruProjects":
							if($task == ""){
								$pageTitle = JText::_('GURU_Q_PROJECTS_MANAGER');
							}
							if($task == "edit"){
								$pageTitle = JText::_('GURU_DAY_EDIT_PROJECT');
							}
							if($task == "resultProject"){
								$pageTitle = JText::_('GURU_PROJECT_RESULT');
							}
							break;
						case "guruAdminControllerguruScorm":
							if($task == ""){
								$pageTitle = JText::_('GURU_Q_SCORM_MANAGER');
							}
							if($task == "edit"){
								$pageTitle = JText::_('GURU_DAY_EDIT_SCORM');
							}
							break;	
						case "guruAdminControllerguruQuizCountdown":
							$pageTitle = JText::_('GURU_Q_QUIZ_MANAGER');
							break;
						case "guruAdminControllerguruCertificate":
							$pageTitle = JText::_('GURU_CERTIFICATES_MANAGER');
							break;	
						case "guruAdminControllerguruOrders":
							if($task == ""){
								$pageTitle = JText::_('GURU_ORDER_MANAGER_TITLE');
							}
							
							break;	
						case "guruAdminControllerguruPromos":
							if($task == ""){
								$pageTitle = JText::_('GURU_PROMO_CODES_MANAGER');
							}
							if($task == "edit"){
								if(JFactory::getApplication()->input->get("cid", "", "raw") > 0){
									$pageTitle = JText::_('GURU_EDITPROMO');
								}
								else{
									$pageTitle = JText::_('GURU_ADDPROMO');
								}
							}
							break;	
						case "guruAdminControllerguruPlugins":
							$pageTitle = JText::_('GURU_PLUGINS_MANAGER');
							break;	
						case "guruAdminControllerguruSubplan":
							if($task == ""){
								$pageTitle = JText::_('GURU_PLANS_MANAGER');
							}
							if($task == "edit"){
								$pageTitle = JText::_('GURU_EDIT_SUBPL');
							}
							break;
						case "guruAdminControllerguruSubremind":
							if($task == ""){
								$pageTitle = JText::_('GURU_REMINDS_MANAGER');
							}
							if($task == "edit"){
								$pageTitle = JText::_('GURU_EDIT')." ".JText::_('GURU_EMAIL_REMIND');
							}
							break;	
						case "guruAdminControllerguruAbout":
							$pageTitle = JText::_('GURU_ABOUT_TITLE');
							break;
						case "guruAdminControllerguruDays":
							$pageTitle = JText::_('GURU_EDITCOURSE');
							break;
						case "guruAdminControllerguruLogs":
							if($task == "emails"){
								$pageTitle = JText::_('GURU_LOGS')." ".">"." ".JText::_('GURU_SYSTEM_EMAILS');
							}
							if($task == "purchases"){
								$pageTitle = JText::_('GURU_LOGS')." ".">"." ".JText::_('GURU_PURCHASES');
							}
							break;
						case "guruAdminControllerguruCommissions":
							if($task == "edit"){
								if(JFactory::getApplication()->input->get("cid", "", "raw") > 0){
									$pageTitle = JText::_('GURU_COMMISSION_PLAN')." ".">"." ".JText::_('GURU_EDIT');
								}
								else{
									$pageTitle = JText::_('GURU_COMMISSION_PLAN')." ".">"." ".JText::_('GURU_NEW');
								}
							}
							if($task == "add"){
								$pageTitle = JText::_('GURU_COMMISSION_PLAN')." ".">"." ".JText::_('GURU_NEW');
							}
							if($task == "list"){
								$pageTitle = JText::_('GURU_COMMISSION_PLAN');
							}
							if($task == "history"){
								$pageTitle = JText::_('GURU_COMMISSIONS')." ".">"." ".JText::_('GURU_PAID_SUMMARY');
							}
							if($task == "pending"){
								$pageTitle = JText::_('GURU_COMMISSIONS')." ".">"." ".JText::_('GURU_AU_PENDING');
							}
							if($task == "paid"){
								$pageTitle = JText::_('GURU_COMMISSIONS')." ".">"." ".JText::_('GURU_PAID_SUMMARY_P');
							}
							break;
						case "guruAdminControllerguruFields":
							$pageTitle = JText::_('GURU_FIELDSMAN');
							break;			
							 
						default:
						   ;
					}
?>
            	<h2 class="pull-left <?php echo $class_page_title; ?>"><?php echo $pageTitle; ?></h2>
                <div class="pull-right"><?php
                  // Alkis - hid ijoomla logo
                  // echo $image_guru_top; ?></div>
			</div>
			<?php
            $controller->execute ($task);
            $controller->redirect();
			
			$doc = JFactory::getDocument();
			if(isset($doc->_script["text/javascript"])){
				$all_script = $doc->_script["text/javascript"];
				$all_script = preg_replace("/.tooltip(.*);/msU", ";", $all_script);
				$doc->_script["text/javascript"] = $all_script;
			}
            ?>
            </div>
            
            <!-- -------------------------------------------------------------- -->
            <?php
				if($classname == "guruAdminControllerguruAuthor" && $task == "list"){
			?>
                    <div id="new-options" style="position:relative;">
                        <input type="button" id="new-options-button" onclick="editOptions();" class="btn btn-success g_toggle_button" value="<?php echo JText::_('GURU_NEW_TEACHER').'&nbsp; &#9660;'; ?>">
                        <div id="button-options" class="button-options" style="display:none;">
                            <ul style="font-size: 14px;">
                                <li>
                                    <a href="index.php?option=com_guru&controller=guruAuthor&task=edit&author_type=0">
										<?php echo JText::_("GURU_NEW_TECHER_DROP"); ?>
                                    </a>
								</li>
                                <li>
                                    <a href="#" onclick="Joomla.submitbutton('new');">
										<?php echo JText::_("GURU_EXISTING"); ?>
                                    </a>
								</li>
                            </ul>
                        </div>
                    </div>
			<?php
            	}
				elseif($classname == "guruAdminControllerguruCustomers" && $task == ""){
			?>
            		<div id="new-options"  style="position:relative;">
                        <input type="button" id="new-options-button" onclick="editOptions();" class="btn btn-success g_toggle_button" value="<?php echo JText::_('GURU_NEW_STUDENT').'&nbsp; &#9660;'; ?>">
                        <div id="button-options" class="button-options" style="display:none;">
                            <ul style="font-size: 14px;">
                                <li>
                                    <a href="index.php?option=com_guru&controller=guruCustomers&task=edit">
										<?php echo JText::_("GURU_NEW_TECHER_DROP"); ?>
                                    </a>
								</li>
                                <li>
                                    <a href="index.php?option=com_guru&controller=guruCustomers&task=add">
										<?php echo JText::_("GURU_EXISTING"); ?>
                                    </a>
								</li>
                            </ul>
                        </div>
                    </div>
            <?php
				}
				elseif($classname == "guruAdminControllerguruQuiz" && $task == ""){
			?>
            		<div id="new-options"  style="position:relative;">
                        <input type="button" id="new-options-button" onclick="editOptions();" class="btn btn-success g_toggle_button" value="<?php echo JText::_('GURU_NEWQUIZ').'&nbsp; &#9660;'; ?>">
                        <div id="button-options" class="button-options" style="display:none;">
                            <ul style="font-size: 14px;">
                                <li>
                                    <a href="index.php?option=com_guru&controller=guruQuiz&task=edit&v=0">
										<?php echo JText::_("GURU_REGULAR_QUIZ"); ?>
                                    </a>
								</li>
                                <li>
                                    <a href="index.php?option=com_guru&controller=guruQuiz&task=edit&v=1">
										<?php echo JText::_("GURU_FINAL_EXAM_QUIZ"); ?>
                                    </a>
								</li>
                            </ul>
                        </div>
                    </div>
            <?php
				}
				elseif($classname == "guruAdminControllerguruMedia" && $task == ""){
			?>
            		<div id="new-options"  style="position:relative;">
                        <input type="button" id="new-options-button" onclick="editOptions();" class="btn btn-success g_toggle_button" value="<?php echo JText::_('GURU_TREE_NEW_MEDIA').'&nbsp; &#9660;'; ?>">
                        <div id="button-options" class="button-options" style="display:none;">
                            <ul style="font-size: 14px;">
                                <li>
                                    <a href="index.php?option=com_guru&controller=guruMedia&task=edit&type=video">
										<?php echo JText::_("GURU_VIDEO"); ?>
                                    </a>
								</li>
                                <li>
                                    <a href="index.php?option=com_guru&controller=guruMedia&task=edit&type=audio">
										<?php echo JText::_("GURU_AUDIO"); ?>
                                    </a>
								</li>
                                <li>
                                    <a href="index.php?option=com_guru&controller=guruMedia&task=edit&type=docs">
										<?php echo JText::_("GURU_DOCS"); ?>
                                    </a>
								</li>
                                <li>
                                    <a href="index.php?option=com_guru&controller=guruMedia&task=edit&type=url">
										<?php echo JText::_("GURU_URL"); ?>
                                    </a>
								</li>
                                <li>
                                    <a href="index.php?option=com_guru&controller=guruMedia&task=edit&type=Article">
										<?php echo JText::_("GURU_ARTICLE"); ?>
                                    </a>
								</li>
                                <li>
                                    <a href="index.php?option=com_guru&controller=guruMedia&task=edit&type=image">
										<?php echo JText::_("GURU_IMAGE"); ?>
                                    </a>
								</li>
								<li>
                                    <a href="index.php?option=com_guru&controller=guruMedia&task=edit&type=zoom">
										<?php echo JText::_("GURU_ZOOM_MEETING"); ?>
                                    </a>
								</li>
                                <li>
                                    <a href="index.php?option=com_guru&controller=guruMedia&task=edit&type=text">
										<?php echo JText::_("GURU_TEXT"); ?>
                                    </a>
								</li>
                                <li>
                                    <a href="index.php?option=com_guru&controller=guruMedia&task=edit&type=file">
										<?php echo JText::_("GURU_FILE_LOWER"); ?>
                                    </a>
								</li>
                            </ul>
                        </div>
                    </div>
            <?php
				}
				elseif($classname == "guruAdminControllerguruCommissions" && $task == "history"){
			?>
            		<div id="new-options"  style="position:relative;">
                        <input type="button" id="new-options-button" onclick="editOptions();" class="btn btn-success g_toggle_button" value="<?php echo JText::_('GURU_EXPORT').'&nbsp; &#9660;'; ?>">
                        <div id="button-options" class="button-options" style="display:none;">
                            <ul style="font-size: 14px;">
                                <li>
                                    <a href="#" onclick="document.getElementById('export').value='csv'; document.adminForm.submit();">
										<?php echo JText::_("GURU_CSV"); ?>
                                    </a>
								</li>
                                 <?php
							?>    
                            </ul>
                        </div>
                    </div>
            <?php
				}
				elseif($classname == "guruAdminControllerguruCommissions" && $task == "pending"){
			?>
            		<div id="new-options"  style="position:relative;">
                        <input type="button" id="new-options-button" onclick="editOptions();" class="btn btn-success g_toggle_button" value="<?php echo JText::_('GURU_EXPORT').'&nbsp; &#9660;'; ?>">
                        <div id="button-options" class="button-options" style="display:none;">
                            <ul style="font-size: 14px;">
                                 <li>
                                    <a href="#" onclick="document.getElementById('export').value='csv'; document.adminForm.submit();">
										<?php echo JText::_("GURU_CSV"); ?>
                                    </a>
								</li>
                                 <?php
							?>    
                            </ul>
                        </div>
                    </div>
            <?php
				}
            	elseif($classname == "guruAdminControllerguruCommissions" && $task == "paid"){
			?>
            		<div id="new-options"  style="position:relative;">
                        <input type="button" id="new-options-button" onclick="editOptions();" class="btn btn-success g_toggle_button" value="<?php echo JText::_('GURU_EXPORT').'&nbsp; &#9660;'; ?>">
                        <div id="button-options" class="button-options" style="display:none;">
                            <ul style="font-size: 14px;">
                                <li>
                                    <a href="#" onclick="document.getElementById('export').value='csv'; document.adminForm.submit();">
										<?php echo JText::_("GURU_CSV"); ?>
                                    </a>
								</li>    
                            </ul>
                        </div>
                    </div>
            <?php
				}
			?>
            <!-- -------------------------------------------------------------- -->
            
			<script>
				function editOptions(){
					display = document.getElementById("button-options").style.display;
					
					if(display == "none"){
						document.getElementById("button-options").style.display = "";
						document.getElementById("new-options").value = "<?php echo JText::_('GURU_NEW'); ?> \u2227";
					}
					else{
						document.getElementById("button-options").style.display = "none";
						document.getElementById("new-options").value = "<?php echo JText::_('GURU_NEW'); ?> \u2228";
					}
				}
			
				// move the Joomla button toolbar to the layout
				jQuery("#toolbar").addClass("pull-right no-margin").prependTo(".page-header");
				
				jQuery("#new-options").addClass("pull-left no-margin").prependTo("#toolbar");
            </script>
    	</div> <!--end main content-->
    </div>
</div><!-- end js-cpanel -->
<?php 
}
else{
		if($task !="guru_file_uploader" && $task != "getcoursecost" && $task != "export_button" && $task != "getplans" && $task != "setrenew" && $task != "setpromo" && $task != "checkExistingUser" && $task != "checkCommissionPlan" && $task != "addcourse_ajax" && $task != "publish_un_ajax" && $task != "delete_image_ajax" && $task != "saveOrderG" && $task != "saveOrderS" && $task != "deleteFinalQuiz" && $task != "deleteGroup" && $task != "deleteScreen" && $task != "delete_categ_image" && $task != "check_values" && $task != "delete_course_image" && $task != "ajax_request" && $task != "ajax_request2" && $task != "ajax_request3" && $task != "export" && $task != "saveOrderQuestions" && $task != "savequestionandclose" && $task != "getTeacherCoursesSelect" && $task != "list_of_modules" && $task != "check_lesson_title"){
			echo '<div id="js-cpanel">';
		}
		
		$classname = "guruAdminController".$controller;
		$ajax_req = JFactory::getApplication()->input->get("no_html", 0);
		$tmpl = JFactory::getApplication()->input->get("tmpl", "");
		$squeeze = JFactory::getApplication()->input->get("sbox", 0);
		
		$controller = new $classname();
		$controller->execute ($task);
		$controller->redirect();
		
		if($task !="guru_file_uploader" && $task != "getcoursecost" && $task != "getplans" && $task != "setrenew" && $task != "setpromo" && $task != "checkExistingUser" && $task != "checkCommissionPlan" && $task != "addcourse_ajax" && $task != "publish_un_ajax" && $task != "delete_image_ajax" && $task != "saveOrderG" && $task != "saveOrderS" && $task != "deleteFinalQuiz" && $task != "deleteGroup" && $task != "deleteScreen" && $task != "delete_categ_image" && $task != "check_values" && $task != "delete_course_image" && $task != "ajax_request" && $task != "ajax_request2" && $task != "ajax_request3" && $task != "export" && $task != "saveOrderQuestions" && $task != "savequestionandclose" && $task != "getTeacherCoursesSelect" && $task != "list_of_modules" && $task != "check_lesson_title" && $task != "check_lesson_title"){
			echo '</div>';
		}
}?>