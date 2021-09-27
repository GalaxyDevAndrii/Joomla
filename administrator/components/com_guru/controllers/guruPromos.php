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

class guruAdminControllerguruPromos extends guruAdminController{
	var $_model = null;
	
	function __construct(){
		parent::__construct();
		$this->registerTask ("", "listPromos");
		$this->registerTask ("show_courses", "showCourses");
		$this->_model = $this->getModel("guruPromos");
	}

	function listPromos(){
		$view = $this->getView("guruPromos", "html");
		$view->setModel($this->_model, true);
		$view->display();
	}

	function edit(){
		$view = $this->getView("guruPromos", "html");
		$view->setLayout("editForm");
		$view->setModel($this->_model, true);
		$view->editForm();
	}

	function save(){ 
		if($this->_model->store()){
			$msg = JText::_('GURU_PROMSAVED');
		}
		else{
			$msg = JText::_('GURU_PROMSAVEFAIL');
		}
		$link = "index.php?option=com_guru&controller=guruPromos";
		$this->setRedirect($link, $msg);
	}
	
	function apply(){ 
		$id = JFactory::getApplication()->input->get("id", "0");
		$result = $this->_model->store(); 
		if(isset($result) && $result != false){
			$id = $result; 
			$msg = JText::_('GURU_PROMAPPLY');
		}
		else{
			$msg = JText::_('GURU_PROMAPPLYFAILED');
		}

		$link = "index.php?option=com_guru&controller=guruPromos&task=edit&cid[]=".$id;
		$this->setRedirect($link, $msg);
	}	

	function remove(){
		if(!$this->_model->delete()){
			$msg = JText::_('GURU_PROMREMERR');
		}
		else{
		 	$msg = JText::_('GURU_PROMREMSUCC');
		}
		$link = "index.php?option=com_guru&controller=guruPromos";
		$this->setRedirect($link, $msg);
	}

	function cancel(){
	 	$msg = JText::_('GURU_PROMCANCEL');
		$link = "index.php?option=com_guru&controller=guruPromos";
		$this->setRedirect($link, $msg);
	}

	function publish(){
		$res = $this->_model->publish();
		if(!$res){
			$msg = JText::_('GURU_PROMPUBERR');
		} 
		elseif($res == -1){
		 	$msg = JText::_('GURU_PROMUNPUB');
		}
		elseif($res == 1){
			$msg = JText::_('GURU_PROMPUB');
		} 
		else{
            $msg = JText::_('GURU_PROMPUBUNSP');
		}
		$link = "index.php?option=com_guru&controller=guruPromos";
		$this->setRedirect($link, $msg);
	}
	
	function unpublish(){
		$res = $this->_model->unpublish();
		if(!$res){
			$msg = JText::_('GURU_PROMPUBERR');
		}
		elseif($res == -1){
		 	$msg = JText::_('GURU_PROMUNPUB');
		} 
		elseif ($res == 1){
			$msg = JText::_('GURU_PROMPUB');
		}
		else{
            $msg = JText::_('GURU_PROMPUBUNSP');
		}		
		$link = "index.php?option=com_guru&controller=guruPromos";
		$this->setRedirect($link, $msg);
	}
	
	function showCourses(){
		$view = $this->getView("guruPromos", "html"); 
		$view->setLayout("courses_list");
		$view->setModel($this->_model, true);
		$view->showCourses();
	}
};
?>