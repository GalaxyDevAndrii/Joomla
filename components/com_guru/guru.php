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

defined('_JEXEC') or die('Restricted access');
JHtml::_('jquery.framework');
JHtml::_('behavior.core');
JHtml::_('behavior.keepalive');
//global $mainframe;
$app = JFactory::getApplication();
if (!defined('DS')) define('DS', DIRECTORY_SEPARATOR);
//check for access
$my =  JFactory::getUser();

$database =  JFactory::getDBO();
$meniu = 0;
$task = JFactory::getApplication()->input->get('task', "");
$control = JFactory::getApplication()->input->get('controller', "");
$view = JFactory::getApplication()->input->get('view', "");
$export = JFactory::getApplication()->input->get('export', "");
$ajax = JFactory::getApplication()->input->get("ajax", 0);
$tmpl = JFactory::getApplication()->input->get("tmpl", "");

require_once(JPATH_COMPONENT . DIRECTORY_SEPARATOR . 'controller.php');
require_once(JPATH_COMPONENT . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'helper.php');
$controller = JFactory::getApplication()->input->get('controller', "");

if (trim($controller) == "") {
	$controller = JFactory::getApplication()->input->get('view', "");
}

$guruHelperclass = new guruHelper();
$guruHelperclass->createBreacrumbs();

$menuParams = new JRegistry;
$app = JFactory::getApplication("site");
$menu = $app->getMenu()->getActive();

if (isset($menu)) {
	@$menuParams->loadString($menu->params);

	$show_page_heading = $menuParams->get("show_page_heading");
	$page_heading = $menuParams->get("page_heading");

	/*if($ajax == 0 && $show_page_heading == 1 && $tmpl != "component"){
		if($page_heading == ""){
			$page_heading = $menuParams->get("page_title");
		}
	?>
	    <header class="page-header">
	        <h1 class="page-title">
	            <?php echo trim($page_heading); ?>
	        </h1>
	    </header>
	<?php
	}*/
}

if ($controller == "guruProfile" || $controller == "guruBuy") {
	JFactory::getApplication()->input->set("view", "");
	JFactory::getApplication()->input->set("layout", "");
	JFactory::getApplication()->input->set("cid", "");
}

if ($controller && $controller != "featured") {
	switch ($controller) {
		case "guruauthor":
			$controller = 'guruAuthor';
			break;
		case "guruAuthor":
			$controller = 'guruAuthor';
			break;
		case "guruprograms":
			$controller = 'guruPrograms';
			break;
		case "guruPrograms":
			$controller = 'guruPrograms';
			break;
		case "guruorders":
			$controller = 'guruOrders';
			break;
		case "guruOrders":
			$controller = 'guruOrders';
			break;
		case "gurutasks":
			$controller = 'guruTasks';
			break;
		case "guruTasks":
			$controller = 'guruTasks';
			break;
		case "gurulogin":
			$controller = 'guruLogin';
			break;
		case "guruLogin":
			$controller = 'guruLogin';
			break;
		case "gurubuy":
			$controller = 'guruBuy';
		case "guruBuy":
			$controller = 'guruBuy';
			break;
		case "guruprofile":
			$controller = 'guruProfile';
		case "guruProfile":
			$controller = 'guruProfile';
			break;
		case "gurucustomers":
			$controller = 'guruCustomers';
		case "guruCustomers":
			$controller = 'guruCustomers';
			break;
		case "gurueditplans":
			$controller = 'guruEditplans';
		case "guruEditplans":
			$controller = 'guruEditplans';
			break;
		case "guruProjects":
			$controller = 'guruProjects';
			break;
		case "guruScorm":
			$controller = 'guruScorm';
			break;
		case "guruMedia":
			$controller = 'guruMedia';
			break;
		default:
			$controller = 'guruPcategs';
			break;
	}

	$path = JPATH_COMPONENT . DIRECTORY_SEPARATOR . 'controllers' . DIRECTORY_SEPARATOR . $controller . '.php';

	if (file_exists($path)) {
		require_once($path);
	}
} else {
	switch ($view) {
		case "guruauthor":
			$controller = 'guruAuthor';
			break;
		case "guruAuthor":
			$controller = 'guruAuthor';
			break;
		case "guruprograms":
			$controller = 'guruPrograms';
			break;
		case "guruPrograms":
			$controller = 'guruPrograms';
			break;
		case "guruorders":
			$controller = 'guruOrders';
			break;
		case "guruOrders":
			$controller = 'guruOrders';
			break;
		case "gurutasks":
			$controller = 'guruTasks';
			break;
		case "guruTasks":
			$controller = 'guruTasks';
			break;
		case "gurulogin":
			$controller = 'guruLogin';
			break;
		case "guruLogin":
			$controller = 'guruLogin';
			break;
		case "gurubuy":
			$controller = 'guruBuy';
		case "guruBuy":
			$controller = 'guruBuy';
			break;
		case "guruprofile":
			$controller = 'guruProfile';
		case "guruProfile":
			$controller = 'guruProfile';
			break;
		case "gurucustomers":
			$controller = 'guruCustomers';
		case "guruCustomers":
			$controller = 'guruCustomers';
			break;
		case "gurueditplans":
			$controller = 'guruEditplans';
		case "guruEditplans":
			$controller = 'guruEditplans';
			break;
		case "guruProjects":
			$controller = 'guruProjects';
			break;
		case "guruScorm":
			$controller = 'guruScorm';
			break;
		case "guruMedia":
			$controller = 'guruMedia';
		default:
			$controller = 'guruPcategs';
			break;
	}

	$path = JPATH_COMPONENT . DIRECTORY_SEPARATOR . 'controllers' . DIRECTORY_SEPARATOR . $controller . '.php';

	if (file_exists($path)) {
		require_once($path);
	}
}
JHtml::_('behavior.framework', true);

$task = JFactory::getApplication()->input->get("task", "");

if ($ajax == 0 && $task != "saveInDbQuiz" && $task != "showCertificateFr" && $task != "ajax_add_video" && $task != "savesbox" && $task != "lessonmessage" && $task != "editgurucomment" && $task != "editformgurupost" && $export != "csv" && $task != "upload_ajax_image" && $task != "checkExistingUserU" && $task != "checkExistingUserE" && $task != "pub_unpub_ajax" && $task != "publish_quiz_ajax" && $task != "unpublish_quiz_ajax" && $task != "delete_quiz_ajax" && $task != "add_quizz_ajax" && $task != "add_text_ajax" && $task != "delete_final_quizz_ajax" && $task != "delete_group_ajax" && $task != "delete_screen_ajax" && $task != "saveOrderG" && $task != "saveOrderS" && $task != "delete_image_ajax" && $task != "check_values" && $task != "add_media_ajax" && $task != "export_pdf" && $task != "export_csv" && $task != "saveMark" && $task != "upload_project_file" && $task != "upload_ajax_scorm" && $task != "check_lesson_title" && $task != "download" && $task != "loading_student_certificates") {
?>
	<div class="guru-content" id="guru-component">
		<script type="text/javascript" language="javascript">
			var choose_file_lang = "<?php echo JText::_("GURU_CHOOSE_FILE"); ?>";

			var quiz_lesson_lang = "<?php echo JText::_("GURU_QUIZ_LESSON_LANG"); ?>";
			var video_lesson_lang = "<?php echo JText::_("GURU_VIDEO_LESSON_LANG"); ?>";
			var audio_lesson_lang = "<?php echo JText::_("GURU_AUDIO_LESSON_LANG"); ?>";
			var document_lesson_lang = "<?php echo JText::_("GURU_DOCUMENT_LESSON_LANG"); ?>";
			var url_lesson_lang = "<?php echo JText::_("GURU_URL_LESSON_LANG"); ?>";
			var article_lesson_lang = "<?php echo JText::_("GURU_ARTICLE_LESSON_LANG"); ?>";
			var image_lesson_lang = "<?php echo JText::_("GURU_IMAGE_LESSON_LANG"); ?>";
			var text_lesson_lang = "<?php echo JText::_("GURU_TEXT_LESSON_LANG"); ?>";
			var file_lesson_lang = "<?php echo JText::_("GURU_FILE_LESSON_LANG"); ?>";
			var next_lang = "<?php echo JText::_("GURU_NEXT_LANG"); ?>";
			var prev_lang = "<?php echo JText::_("GURU_PREV_LANG"); ?>";
			var set_unit_completed = "<?php echo JText::_("GURU_UNIT_COMPLETED"); ?>";
			var set_unit_uncompleted = "<?php echo JText::_("GURU_UNIT_UNCOMPLETED"); ?>";
			var course_not_completed_lang = "<?php echo JText::_("GURU_COURSE_NOT_COMPLETED_LANG"); ?>";
			var you_have_lang = "<?php echo JText::_("GURU_YOU_HAVE_LANG"); ?>";
			var seconds_more_lang = "<?php echo JText::_("GURU_SECONDS_MORE_LANG"); ?>";
			var more_take_lang = "<?php echo JText::_("GURU_MORE_TAKE_LANG"); ?>";
			var minutes_lang = "<?php echo JText::_("GURU_MINUTES"); ?>";
			var minute_lang = "<?php echo JText::_("GURU_MINUTE"); ?>";
			var lesson_view_confirm = 0;

			<?php
			$view = JFactory::getApplication()->input->get("view", "", "raw");
			$task = JFactory::getApplication()->input->get("task", "", "raw");
			$cid = JFactory::getApplication()->input->get("cid", "", "raw");

			if (trim($view) == "guruPrograms" && $task == "view" && intval($cid) > 0) {
				$sql = "select `lesson_view_confirm` from #__guru_program where `id`=" . intval($cid);
				$db->setQuery($sql);
				$db->execute();
				$lesson_view_confirm = $db->loadColumn();
				$lesson_view_confirm = @$lesson_view_confirm["0"];

				if (intval($lesson_view_confirm) == 1) {
					echo 'lesson_view_confirm = 1';
				}
			}
			?>
		</script>


		<?php
	}

	$db = JFactory::getDBO();
	$sql = "SELECT * FROM #__guru_config WHERE id = '1' ";
	$db->setQuery($sql);
	$configs = $db->loadObject();
	$document	= JFactory::getDocument();

	$view = JFactory::getApplication()->input->get("view", "");
	$layout = JFactory::getApplication()->input->get("layout", "");
	$cid = JFactory::getApplication()->input->get("cid", "");

	if (intval($cid) == 0) {
		$menu	= $app->getMenu();
		$item	= $menu->getActive();
		if (isset($item)) {
			$cid = $item->params->get("cid");
		}
	}

	if ($view == "guruauthor" && $layout == "view" && $controller == "guruAuthor" && intval($cid) == "0") {
		$Itemid = JFactory::getApplication()->input->get("Itemid", "0", "raw");

		$helper = new guruHelper();
		$itemid_seo = $helper->getSeoItemid();
		$itemid_seo = @$itemid_seo["gurulogin"];

		if (intval($itemid_seo) > 0) {
			$Itemid = intval($itemid_seo);
		}

		$redirect = JRoute::_("index.php?option=com_guru&view=guruLogin&returnpage=authorprofile&Itemid=" . $Itemid, false);
		$app = JFactory::getApplication();
		$app->redirect($redirect);
	}

	$classname = "guruController" . $controller;
	$ajax_req = JFactory::getApplication()->input->get("no_html", 0);
	$controller = new $classname();
	$layout = JFactory::getApplication()->input->get('layout', "", "raw");

	if ($layout && $task != "renew") {
		if ($classname == "guruControllerguruEditplans") {
			$layout = $task;
		}

		if (trim($task) == "") {
			$controller->execute($layout);
		} else {
			$controller->execute($task);
		}
	} else {
		$task = JFactory::getApplication()->input->get('task');
		$controller->execute($task);
	}

	$controller->redirect();

	$view = JFactory::getApplication()->input->get("view", "");
	$controller = JFactory::getApplication()->input->get("controller", "");

	if (trim($controller) == "") {
		$controller = JFactory::getApplication()->input->get("view", "");
	}

	if (trim($view) == "") {
		$view = $controller;
	}

	if ($view == 'gurutasks') {
		// do nothing
	} elseif ($view == "guruPcategs" || $view == "gurupcategs" || $view == "gurubuy" || $view == "guruPrograms") {
		$db = JFactory::getDBO();
		$sql = "select show_powerd from #__guru_config";
		$db->setQuery($sql);
		$db->execute();
		$result = $db->loadColumn();
		$result = $result["0"];
		if ($result == 1) {
			if ($task != "savesbox" && $task != "saveLesson") {
		?>
				<div class="guru-powered">
					<span class="power_by">Powered by: Guru: </span>
					<a target="_blank" href="http://guru.ijoomla.com/" class="power_link" title="joomla lms">Joomla LMS</a>
				</div>
		<?php
			}
		} else {
		}
	}

	if ($ajax == 0 && $task != "saveInDbQuiz" && $task != "savesbox" && $task != "saveLesson" && $task != "lessonmessage" && $task != "editgurucomment" && $task != "editformgurupost" && $export != "csv" && $task != "upload_ajax_image" && $task != "checkExistingUserU" && $task != "checkExistingUserE" && $task != "pub_unpub_ajax" && $task != "publish_quiz_ajax" && $task != "unpublish_quiz_ajax" && $task != "delete_quiz_ajax" && $task != "add_quizz_ajax" && $task != "add_text_ajax" && $task != "delete_final_quizz_ajax" && $task != "delete_group_ajax" && $task != "delete_screen_ajax" && $task != "saveOrderG" && $task != "saveOrderS" && $task != "delete_image_ajax" && $task != "check_values" && $task != "add_media_ajax" && $task != "export_pdf" && $task != "export_csv" && $task != "saveMark" && $task != "upload_project_file" && $task != "upload_ajax_scorm" && $task != "check_lesson_title" && $task != "download" && $task != "loading_student_certificates") {
		?>
	</div>
	<div class="clearfix"></div>

<?php
	}
?>
<div id="js-cpanel">
	<?php
	$openInModal = JFactory::getApplication()->input->get("openModal", "0", "raw");
	if ($openInModal) {
	?>
		<script type="text/javascript">
			var openInModal = true;
		</script>
	<?php
	}

	?>
	<?php 
	JHtml::script('components/com_guru/js/redactor.min.js');
	JHtml::script('components/com_guru/js/fileuploader.js');
	JHtml::script('components/com_guru/js/accordion.js');
	JHtml::script('components/com_guru/js/guru.js');
	JHtml::script('components/com_guru/js/programs.js');
	JHtml::script('components/com_guru/js/guru_modal_commissions.js');
	JHtml::script('components/com_guru/js/guru_modal.js');
	JHtml::script('components/com_guru/js/js.js');
	?>
	<script type="text/javascript">
		var matched, browser;

		jQuery.uaMatch = function(ua) {
			ua = ua.toLowerCase();

			var match = /(chrome)[ \/]([\w.]+)/.exec(ua) ||
				/(webkit)[ \/]([\w.]+)/.exec(ua) ||
				/(opera)(?:.*version|)[ \/]([\w.]+)/.exec(ua) ||
				/(msie) ([\w.]+)/.exec(ua) ||
				ua.indexOf("compatible") < 0 && /(mozilla)(?:.*? rv:([\w.]+)|)/.exec(ua) || [];

			return {
				browser: match[1] || "",
				version: match[2] || "0"
			};
		};

		matched = jQuery.uaMatch(navigator.userAgent);
		browser = {};

		if (matched.browser) {
			browser[matched.browser] = true;
			browser.version = matched.version;
		}

		// Chrome is Webkit, but Webkit is also Safari.
		if (browser.chrome) {
			browser.webkit = true;
		} else if (browser.webkit) {
			browser.safari = true;
		}

		jQuery.browser = browser;
	</script>
</div>