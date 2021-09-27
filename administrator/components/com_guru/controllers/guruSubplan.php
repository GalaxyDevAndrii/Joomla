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

class guruAdminControllerguruSubplan extends guruAdminController {
	var $_model = null;
	
	function __construct(){
		parent::__construct();
		$this->registerTask ("", "display");
        $this->registerTask ("remove", "publish");
        $this->registerTask ("unpublish", "publish");
        $this->registerTask ("unapprove", "publish");
        $this->registerTask ("approve", "publish");
        $this->registerTask("orderup", "saveorder");
        $this->registerTask("orderdown", "saveorder");
        $this->_model = $this->getModel("guruSubplan");
	}

    function display($cachable = false, $urlparams = Array()){
		$view = $this->getView("guruSubplan", "html");
        $view->setLayout('default');
		$view->setModel($this->_model, true);
		@$view->display();
    }
    
    function edit(){
		$view = $this->getView("guruSubplan", "html");
        $view->setLayout('editform');
		$view->setModel($this->_model, true);
		$view->edit();        
    }
    
    function save(){
        $app = JFactory::getApplication('administrator');
        if($this->_model->store()){
            $msg = JText::_('GURU_MODIF_OK');
            $app->enqueueMessage($msg);
            $app->redirect('index.php?option=com_guru&controller=guruSubplan');
        } 
		else{
            $msg = JText::_('GURU_ERROR');
            $app->enqueueMessage($msg);
            $app->redirect('index.php?option=com_guru&controller=guruSubplan');
        }
        $this->display();
    }
    
    function apply(){
        $app = JFactory::getApplication('administrator');
        if($id = $this->_model->store()){
            $msg = JText::_('GURU_MODIF_OK');
            $app->enqueueMessage($msg);
            $app->redirect('index.php?option=com_guru&controller=guruSubplan&task=edit&cid[]='.$id);
        }
		else{
            $msg = JText::_('GURU_ERROR');
            $app->enqueueMessage($msg);
            $app->redirect('index.php?option=com_guru&controller=guruSubplan');
        }
        $this->display();
    }
    
    function publish(){
        $app = JFactory::getApplication('administrator');
        $return = $this->_model->publish();
		if($return === TRUE){
            $msg = JText::_('GURU_MODIF_OK');
            $app->enqueueMessage($msg);
            $app->redirect('index.php?option=com_guru&controller=guruSubplan');
        }
		elseif($return === FALSE){
            $msg = JText::_('GURU_ERROR');
            $app->enqueueMessage($msg);
            $app->redirect('index.php?option=com_guru&controller=guruSubplan');
        }
		else{
			$app->enqueueMessage($return);
            $app->redirect('index.php?option=com_guru&controller=guruSubplan');
		}
        $this->display();
    }
    
    function saveorder(){
        $app = JFactory::getApplication('administrator');
        if($this->_model->saveorder()){
            $msg = JText::_('GURU_MODIF_OK');
            $app->enqueueMessage($msg);
            $app->redirect('index.php?option=com_guru&controller=guruSubplan');
        }
		else{
            $msg = JText::_('GURU_ERROR');
            $app->enqueueMessage($msg);
            $app->redirect('index.php?option=com_guru&controller=guruSubplan');
        }
        $this->display();    
    }
}

?>