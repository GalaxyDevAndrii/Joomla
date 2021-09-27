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

class guruAdminControllerguruAuthor extends guruAdminController {
	var $_model = null;
	
	function __construct () {
		parent::__construct();
		$this->registerTask ("", "AuthorList");
		$this->registerTask ("list", "AuthorList");
		$this->registerTask ("block", "block");
		$this->registerTask ("unblock", "block");
		$this->registerTask("orderup", "saveorder");
        $this->registerTask("orderdown", "saveorder");
		$this->registerTask ("new", "newAuthorStep1");
		$this->registerTask ("next", "newAuthorStep2");
		$this->registerTask ("edit", "editAuthor");
		$this->registerTask ("remove", "delete");
		$this->registerTask ("saveOrderAjax", "saveOrderAjax");		
		$this->_model = $this->getModel("guruauthor");
	}
	
	function newAuthorStep1(){
		JFactory::getApplication()->input->set("view", "guruauthor");
		$view = $this->getView("guruauthor", "html");
		$view->setLayout("settype");
		$view->setModel($this->_model, true);
		$username_value = JFactory::getApplication()->input->get("username", "", "raw");
		$view->settypeform();
	}

	function newAuthorStep2(){
		$author_type 	= JFactory::getApplication()->input->get("author_type", 0, "raw");
		$username_value = JFactory::getApplication()->input->get("username", "", "raw");
		//add an existent user as author
		if($author_type==1){
			if($this->_model->existUser($username_value)){				
				if($this->_model->existNewAuthor($username_value)){
					$msg = JText::_("GURU_AU_EXIST_AUTHOR_ERROR");
					$this->setRedirect( 'index.php?option=com_guru&controller=guruAuthor&task=new', $msg, "notice");
				}
				else{
					$userid = $this->_model->getUserId($username_value);
					if(!empty($userid)){
						JFactory::getApplication()->input->set("id", $userid);
					}
					JFactory::getApplication()->input->set("view", "guruauthor");
					$view = $this->getView("guruauthor", "html");
					$view->setLayout("editform");
					$view->setModel($this->_model, true);
					$view->editform();
				}
			}
			else{
				$msg = JText::_("GURU_AU_NO_AUTHOR");
				$this->setRedirect( 'index.php?option=com_guru&controller=guruAuthor&task=edit', $msg);
			}
		}
		//add new author
		else{
			JFactory::getApplication()->input->set("view", "guruauthor");
			$view = $this->getView("guruauthor", "html");
			$view->setLayout("editform");
			$view->setModel($this->_model, true);
			$view->editform();
		}		
	}
		
	function editAuthor(){
		$cids = JFactory::getApplication()->input->get("cid", "", "raw");
		$author_id = @$cids[0];
		JFactory::getApplication()->input->set ("view", "guruauthor");
		$view = $this->getView("guruauthor", "html");
		$view->setLayout("editform");
		$view->setModel($this->_model, true);
		$view->editform();
	}
	
	function AuthorList() {
       	JFactory::getApplication()->input->set("view", "guruauthor");
		$view = $this->getView("guruauthor", "html");		
		$view->setModel($this->_model, true);
		parent::display();
	}

	function delete(){
	 	$app = JFactory::getApplication('administrator');
		$result = $this->_model->delete();
		$msg = "";
		$type = "";
		
		if($result === true){
			$msg = JText::_('GURU_AU_DELETED');
			$type = "message";
		} 
		elseif($result === false){
			$msg = JText::_('GURU_AU_DELETED_ERROR');
			$type = "error";
		}
		elseif($result == "has courses"){
			$msg = JText::_("GURU_NOT_DELETE_HAS_COURSES");
			$type = "notice";
		}
		
		$app->enqueueMessage($msg, $type);
		$link = "index.php?option=com_guru&controller=guruAuthor&task=list";
		$app->redirect('index.php?option=com_guru&controller=guruAuthor');
	}

	function saveorder(){
        $app = JFactory::getApplication('administrator');
        if($this->_model->saveorder()){
            $msg = JText::_('GURU_MODIF_OK');
            $app->enqueueMessage($msg);
            $app->redirect('index.php?option=com_guru&controller=guruAuthor');
        }
		else{
            $msg = JText::_('GURU_ERROR');
            $app->enqueueMessage($msg);
            $app->redirect('index.php?option=com_guru&controller=guruAuthor');
        }
        $this->display();    
    }
	
	function display($cachable = false, $urlparams = Array()){
		JFactory::getApplication()->input->set("view", "guruauthor");
		$view = $this->getView("guruauthor", "html");		
		$view->setModel($this->_model, true);
		@parent::display();
    }

	function block(){
		$const = "";
		if(JFactory::getApplication()->input->get("task")=="block"){
			$const = "BLOCK";
		}
		else{
			$const = "UNBLOCK";
		}
		
		if ($this->_model->block() ){
			$msg = JText::_('GURU_AU_'.$const);
		} 
		else{
			$msg = JText::_('GURU_AU_'.$const."_ERROR");
		}
		$link = "index.php?option=com_guru&controller=guruAuthor&task=list";
		$this->setRedirect($link, $msg, "notice");
	}
	
	function cancel(){
		$msg = JText::_( 'GURU_AU_AUTHOR_CANCEL' );
		$this->setRedirect( 'index.php?option=com_guru&controller=guruAuthor&task=list', $msg );		
	}	
	
	function save(){
		$app = JFactory::getApplication('administrator');
		$type = JFactory::getApplication()->input->get('author_type', '0', "raw");
		$data = JFactory::getApplication()->input->post->getArray();
		
		$userid	= JFactory::getApplication()->input->get('userid', '0', "raw");
		$data['id'] = $userid;
		$url_valid = "1";
		
		$session = JFactory::getSession();
		$registry = $session->get('registry');
		$email_already = $registry->get('email_already', "0");
		
		if($url_valid == "0"){
			if($userid == 0){
				$msg = JText::_( 'GURU_AU_URL_ERROR2' );
			}
			else{
				$msg = JText::_( 'GURU_AU_URL_ERROR' );
			}
			$this->setRedirect( 'index.php?option=com_guru&controller=guruAuthor&task=list', $msg, 'warning' );
		}
		elseif($this->_model->save()){
			$msg = JText::_( 'GURU_AU_AUTHOR_DETAILS_SAVED' );
			$this->setRedirect( 'index.php?option=com_guru&controller=guruAuthor&task=list', $msg );
		}
		else if($email_already == 1){
			$msg = JText::_('GURU_AU_AUTHOR_DETAILS_SAVED_ERROR1');
			$registry->set('email_already', "0");
			$this->setRedirect( 'index.php?option=com_guru&controller=guruAuthor&task=list', $msg, "warning");
		}
		else{
			$msg = JText::_('GURU_AU_AUTHOR_DETAILS_SAVED_ERROR');
			$this->setRedirect( 'index.php?option=com_guru&controller=guruAuthor&task=list', $msg, "notice");
		}		
	}
	
	function apply(){
		$app = JFactory::getApplication('administrator');
		$db =  JFactory::getDBO();
		$type = JFactory::getApplication()->input->get('author_type', '0', "raw");
		$data = JFactory::getApplication()->input->post->getArray();
		$id	  = JFactory::getApplication()->input->get('id', '0', "raw");
		$userid	= JFactory::getApplication()->input->get('userid', '0', "raw");
		$data['id'] = $userid;
		
		$url_valid = "1";
		$result = $this->_model->save();
		if($userid == 0){
			$userid = $result;
		}
		
		$session = JFactory::getSession();
		$registry = $session->get('registry');
		$email_already = $registry->get('email_already', "0");
		
		if($url_valid == "0"){
			$msg = JText::_( 'GURU_AU_URL_ERROR' );
			$this->setRedirect( 'index.php?option=com_guru&controller=guruAuthor&task=edit&id='.$userid, $msg,'warning' );
		}
		elseif(isset($result) && $result != false){
			$msg = JText::_( 'GURU_AU_AUTHOR_DETAILS_SAVED' );
			$this->setRedirect( 'index.php?option=com_guru&controller=guruAuthor&task=edit&id='.$result, $msg );
		}
		else if($email_already == 1){
			$msg = JText::_('GURU_AU_AUTHOR_DETAILS_SAVED_ERROR1');
			$registry->set('email_already', "0");
			$this->setRedirect( 'index.php?option=com_guru&controller=guruAuthor&task=edit&id='.$result, $msg );
		}
		else{
			$author_type = JFactory::getApplication()->input->get("author_type", "0", "raw");
			return $this->setRedirect("index.php?option=com_guru&controller=guruAuthor&task=next&author_type=".intval($author_type), JText::_('GURU_AU_AUTHOR_DETAILS_SAVED_ERROR'));
		}				
	}
	
	public function saveOrderAjax(){
		// Get the arrays from the Request
		$originalOrder = explode(',', $this->input->getString('original_order_values'));
	
		$model = $this->getModel("guruAuthor");
		// Save the ordering
		$return = $model->saveOrder();
		if ($return){
			echo "1";
		}
		// Close the application
		JFactory::getApplication()->close();
	}

	function changefolder(){
		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
		header("Cache-Control: no-cache");
		header("Pragma: no-cache");
		jimport( 'joomla.filesystem.folder' );
		$jFolder = new JFolder();
		$data = JFactory::getApplication()->input->get->getArray();
		$data['imgfiles'] = $data['imagefiles'];
		$imageFiles=$jFolder->files(JPATH_SITE.DIRECTORY_SEPARATOR."images".$data['folder']);
		$url = '..';
		if(!empty($imageFiles)){
			$javascript	= " size=\"5\" onchange=\"".$data['function']."( '".$data['imgfiles']."', 'view_imagefiles', '".$url."/' )\"";
			$images="<select id='".$data['imgfiles']."' name='".$data['imgfiles']."' class='inputbox' multiple='multiple' ".$javascript." >";
			$images.="<option value=''>Select</option>";
			foreach ( $imageFiles as $file ) {
           		if ( preg_match( "/^bmp|gif|jpg|png/", $file ) ) {
                	$images.="<option value='".$data['folder']."/".$file."'>".$file."</option>";
           		}
    		}
		}
		else $images="No Images here...";
		echo ($images);		
	}
};
?>