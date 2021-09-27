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

jimport ('joomla.application.component.controller');
$document = JFactory::getDocument();

class guruAdminController extends JControllerLegacy {

	function __construct() {
		parent::__construct();
		
		$ajax_req = JFactory::getApplication()->input->get("no_html", 0, "raw");
		$squeeze = JFactory::getApplication()->input->get("sbox", 0, "raw");
		$squeeze2 = JFactory::getApplication()->input->get("tmpl", 0, "raw");
		$task = JFactory::getApplication()->input->get("task", "", "raw");
		$export = JFactory::getApplication()->input->get("export", "", "raw");
		$export1 = JFactory::getApplication()->input->get("export1", "", "raw");
		$controller = JFactory::getApplication()->input->get("controller", "", "raw");
		
		if($export != "" || $export1 != ""){
			// do nothing
		}
		elseif(!$ajax_req && $task != "savesbox"&& $task != "save2" && $task != "export_button" && $task != "export" && $task != "savequizzes" && $task !="savequestionedit" && $task != "savequestion" && $task != "savequestionandclose" && $task != "getTeacherCoursesSelect"){
			$document = JFactory::getDocument();
			$document->addStyleSheet("components/com_guru/css/general.css");
			$document->addStyleSheet("components/com_guru/css/tmploverride.css");
			
			$document->addStyleSheet( 'components/com_guru/css/bootstrap.min.css' );
			$document->addStyleSheet( 'components/com_guru/css/font-awesome.min.css' );
			$document->addStyleSheet( 'components/com_guru/css/ace-fonts.css' );
			if($controller != "guruInstall"){
				$document->addStyleSheet( 'components/com_guru/css/ace.min.css' );
			}
			$document->addStyleSheet( 'components/com_guru/css/fullcalendar.css' );
			$document->addStyleSheet( 'components/com_guru/css/g_admin_modal.css' );
			
			require_once (JPATH_COMPONENT_ADMINISTRATOR.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'chtmlinput.php');

  			$view = $this->getView('guruDtree', 'html');
  			if (!$squeeze2 && !$squeeze){
	  		?>
	  		
			<?php
				$view->showDtree();
				?>
					
			<?php
  			}
		}
		$dir_name = JPATH_SITE."/media/files/guru/";
		$file_name = "guru_user_custom.css";
		if(file_exists($dir_name.$file_name)){
			//echo '<link rel="stylesheet" href="'.JURI::root().'media/files/guru/'.$file_name.'" type="text/css" media="all">';
			$document = JFactory::getDocument();
			$document->addStyleSheet( JURI::root().'media/files/guru/'.$file_name );
		}

	}

	function display ($cachable = false, $urlparams = array()) {
		parent::display($cachable, $urlparams);	
	}

	function debugStop($msg = ''){
       	$app = JFactory::getApplication('administrator');
	  	echo $msg;
		$app->close();
	}
	public static function checkJoomla4orGreater(){
		return !class_exists('JDispatcher');
	}
	
	public static function getModalClass($checkJoomla4orGreater){
		return $modalClass = ($checkJoomla4orGreater) ? 'openModal' : 'modal';
	}

	public static function getModalData($checkJoomla4orGreater){
		return $modalData = ($checkJoomla4orGreater) ? ' data-toggle="modal" data-target="#GuruModal"' : '';
	}
};

?>
