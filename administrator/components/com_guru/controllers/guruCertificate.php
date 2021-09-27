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

class guruAdminControllerguruCertificate extends guruAdminController {
	var $_model = null;
	
	function __construct(){
		parent::__construct();
		$this->registerTask ("", "display");
		$this->registerTask ("previewcertificate", "previewcertificate");
		$this->registerTask ("delete_image_ajax", "delete_image_ajax");
		$this->registerTask ("list", "listcertificates");
		
        $this->_model = $this->getModel("guruCertificate");
	}

    function display($cachable = false, $urlparams = Array()){
		$view = $this->getView("guruCertificate", "html");
        $view->setLayout('default');
		$view->setModel($this->_model, true);
		@$view->display();
    }

    function listcertificates($cachable = false, $urlparams = Array()){
		$view = $this->getView("guruCertificate", "html");
        $view->setLayout('list');
		$view->setModel($this->_model, true);
		@$view->listcertificates();
    }

	function cancel(){
		 $msg = JText::_('GURU_MEDIACANCEL');	
		 $app = JFactory::getApplication('administrator');
		 $app->enqueueMessage($msg);
		 $app->redirect('index.php?option=com_guru');
	}

    function save(){
        $app = JFactory::getApplication('administrator');
        if($this->_model->savedesigncert('s')){
            $msg = JText::_('GURU_MODIF_OK');
            $app->enqueueMessage($msg);
            $app->redirect('index.php?option=com_guru&controller=guruCertificate');
        } 
		else{
            $msg = JText::_('GURU_ERROR');
            $app->enqueueMessage($msg);
            $app->redirect('index.php?option=com_guru');
        }
        $this->display();
    }
    
    function apply(){
        $app = JFactory::getApplication('administrator');
        if($id = $this->_model->savedesigncert('a')){
            $msg = JText::_('GURU_MODIF_OK');
            $app->enqueueMessage($msg);
            $app->redirect('index.php?option=com_guru&controller=guruCertificate');
        }
		else{
            $msg = JText::_('GURU_ERROR');
            $app->enqueueMessage($msg);
            $app->redirect('index.php?option=com_guru&controller=guruCertificate');
        }
        $this->display();
    }
    
	function previewcertificate(){
		$db = JFactory::getDBO();
		$imagename = "SELECT * FROM #__guru_certificates WHERE id=1";
		$db->setQuery($imagename);
		$db->execute();
		$imagename = $db->loadAssocList();
		
		if($imagename[0]["design_background"] !=""){
			$image_theme = explode("/", $imagename[0]["design_background"]);
			if(trim($image_theme[4]) =='thumbs'){
				$image_theme = $image_theme[5];
			}
			else{
				$image_theme = $image_theme[4];
			}
		}	
		else{
			$background_color= "background-color:"."#".$imagename[0]["design_background_color"];
		}	
		?>
		<div style="width:800px; font-family:<?php echo $imagename[0]["font_certificate"]; ?>; height:600px; <?php echo $background_color;?>; background-repeat:no-repeat; background-image:url(<?php echo JUri::root()."images/stories/guru/certificates/".$image_theme; ?>);">
		<table>
			<tr>
				<td style="padding-left:150px; padding-top:180px; font-size:36px; color:<?php echo "#".$imagename[0]["design_text_color"]; ?>"><?php echo JText::_("GURU_CERTIFICATE_OF_COMPLETION"); ?></td>
			</tr>
			<tr>
				<td  style="padding:40px; padding-left:70px; color:<?php echo "#".$imagename[0]["design_text_color"]; ?>">
				<?php echo $imagename[0]["templates1"]; ?>
		   </td>
			</tr>
		 </table>
		</div>
	<?php
	}
	
	function delete_image_ajax(){
		$db = JFactory::getDBO();
		$course_id = JFactory::getApplication()->input->get("course_id", "0", "raw");

		$query = "update #__guru_certificates set `design_background`='' where `id`='1'";

		if(intval($course_id) > 0){
			$query = "update #__guru_certificates set `design_background`='' where `course_id`=".intval($course_id);
		}

		$db->setQuery($query);
		if($db->execute()){
			$image_selected = JFactory::getApplication()->input->get("image_selected", "", "raw");
			
			if(trim($image_selected) != ""){
				$image_selected_array = explode("/", $image_selected);
				$image_selected = $image_selected_array[count($image_selected_array) - 1];
				
				@unlink(JPATH_SITE.DIRECTORY_SEPARATOR."images".DIRECTORY_SEPARATOR."stories".DIRECTORY_SEPARATOR."guru".DIRECTORY_SEPARATOR."certificates".DIRECTORY_SEPARATOR.$image_selected);
				@unlink(JPATH_SITE.DIRECTORY_SEPARATOR."images".DIRECTORY_SEPARATOR."stories".DIRECTORY_SEPARATOR."guru".DIRECTORY_SEPARATOR."certificates".DIRECTORY_SEPARATOR."thumbs".DIRECTORY_SEPARATOR.$image_selected);
			}
			echo "2";
			die();
		}
		else{
			echo $query;
			die();
		}
	}
}

?>