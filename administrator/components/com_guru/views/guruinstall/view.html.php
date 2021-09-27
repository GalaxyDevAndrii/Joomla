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

jimport ("joomla.application.component.view");

class guruAdminViewguruinstall extends JViewLegacy {

	function display($tpl = null){
		parent::display($tpl);
	}
	
	function startAction(){
		$model = $this->getModel();
		$step = JFactory::getApplication()->input->get("step", "start");
		
		if($step == "database"){
			$model->startDatabaseInstall();
		}
		elseif ($step === 'index') {
			$model->indexDatabase();
		}
		elseif($step == "default"){
			$model->startDefaultValues();
		}
		elseif($step == "folders"){
			$model->startCreateFolders();
		}
		elseif($step == "menu"){
			$model->startMenuItems();
		}
		elseif($step == "plugins"){
			$model->startInstallPlugins();
		}
		elseif($step == "questions"){
			$model->startInstallQuestions();
		}
		elseif($step == "quiz"){
			$model->startInstallQuiz();
		}
	}
	
	function createDiagram($type){
		$step = JFactory::getApplication()->input->get("step", "start");
		$steps = array("database", "index", "default", "folders", "menu", "plugins", "questions", "quiz", "stop");
		
		$poz_type = array_search($type, $steps);
		$poz_step = array_search($step, $steps);
		
		$return = '<div class="install-loading">
						<div class="progress progress-striped active">
							<div style="width: 100%;" class="bar"></div>
						</div>
					</div>';
		
		if($poz_type < $poz_step || $step == "stop"){
			$return = '<img class="install-checked" src="components/com_guru/images/checked.png" />';
		}
				
		return $return;
	}
}

?>