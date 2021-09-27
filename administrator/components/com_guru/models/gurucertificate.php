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
jimport ("joomla.aplication.component.model");


class guruAdminModelguruCertificate extends JModelLegacy {
	var $_packages;
	var $_package;
	var $_tid = null;
	var $_total = 0;
	var $_pagination = null;
	protected $_context = 'com_guru.guruCertificate';

	function __construct () {
		global $option;
		parent::__construct();
		$mainframe =JFactory::getApplication();

		$limit = $mainframe->getUserStateFromRequest( 'global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int' );
		$limitstart = $mainframe->getUserStateFromRequest( $option.'limitstart', 'limitstart', 0, 'int' );
		
		$this->setState('limit', $limit); // Set the limit variable for query later on
		$this->setState('limitstart', $limitstart);	
	}
	
	function savedesigncert($t) {
		$db = JFactory::getDBO();
		
		$library_pdf = JFactory::getApplication()->input->get("library_pdf", "", "raw");
		$certificate_sett = JFactory::getApplication()->input->get("certificate_sett", "", "raw");
		$image = JFactory::getApplication()->input->get("image", "", "raw");
		$st_donecolor1 = JFactory::getApplication()->input->get("st_donecolor1", "", "raw");
		$st_donecolor2 = JFactory::getApplication()->input->get("st_donecolor2", "", "raw");
		$avg_cert = JFactory::getApplication()->input->get("avg_cert", "70", "raw");
		$certificate = JFactory::getApplication()->input->get("certificate", "", "raw");
		$certificate_page = JFactory::getApplication()->input->get("certificate_page", "", "raw");
		$email_template = JFactory::getApplication()->input->get("email_template", "", "raw");
		$email_mycertificate = JFactory::getApplication()->input->get("email_mycertificate", "", "raw");
		$subjectt3 = JFactory::getApplication()->input->get("subjectt3", "", "raw");
		$subjectt4 = JFactory::getApplication()->input->get("subjectt4", "", "raw");
		$font = JFactory::getApplication()->input->get("font", "", "raw");
		$library_pdf = JFactory::getApplication()->input->get("library_pdf", "", "raw");
		$certificatepdf = JFactory::getApplication()->input->get("certificatepdf", "", "raw");
		
		if(!file_exists(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_guru'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'MPDF') && $post_value["library_pdf"] == 1){
			$app = JFactory::getApplication('administrator');
			$msg = JText::_('GURU_NO_MPDF_MSG');

			if($t == 'a'){
				$app->enqueueMessage($msg, "error");
				$app->redirect('index.php?option=com_guru&controller=guruCertificate');
			}
			elseif($t == 's'){
				$app->enqueueMessage($msg, "error");
				$app->redirect('index.php?option=com_guru');
			}
		}
		
		$sql = "UPDATE #__guru_certificates set general_settings='".addslashes(trim($certificate_sett))."', design_background= '".addslashes(trim($image))."',	design_background_color ='".addslashes(trim($st_donecolor1))."', design_text_color='".addslashes(trim($st_donecolor2))."', avg_cert='".addslashes(trim($avg_cert))."', templates1='".addslashes(trim($certificate))."', templates2='".addslashes(trim($certificate_page))."', templates3='".addslashes(trim($email_template))."', templates4='".addslashes(trim($email_mycertificate))."', subjectt3='".addslashes(trim($subjectt3))."', subjectt4='".addslashes(trim($subjectt4))."', font_certificate='".addslashes(trim($font))."' , library_pdf = '".addslashes(trim($library_pdf))."', templatespdf='".addslashes($certificatepdf)."' where `id`='1'";
		$db->setQuery($sql);
		$db->execute();
		return true;
	}	

   	public static function getCertificatesDetails(){
  		$db = JFactory::getDBO();
		$sql = "SELECT * from #__guru_certificates where id='1'";
		$db->setQuery($sql);
		$db->execute();
		$result = $db->loadObject();
		return $result;
   	}

   	function getPagination(){
		// Lets load the content if it doesn't already exist
		if (empty($this->_pagination))	{
			jimport('joomla.html.pagination');
			if (!$this->_total) $this->getItems();
			$this->_pagination = new JPagination( $this->_total, $this->getState('limitstart'), $this->getState('limit') );
		}

		return $this->_pagination;
	}

	function getTeachers(){
		$db = JFactory::getDbo();
		$sql = "select u.`id`, u.`name` from #__users u, #__guru_authors a where u.`id`=a.`userid`";
		$db->setQuery($sql);
		$db->execute();

		$authors = $db->loadAssocList();
		return $authors;
	}

   	function getItems(){
		$config = new JConfig(); 
		$app = JFactory::getApplication('administrator');
		$db = JFactory::getDBO();
		$sql = $this->getListQuery();
		$limitstart=$this->getState('limitstart');
		$limit=$this->getState('limit');
			
		if($limit!=0){
			$limit_cond=" LIMIT ".$limitstart.",".$limit." ";
		} else {
			$limit_cond = NULL;
		}
		
		$query = $this->getListQuery();
		$result = $this->_getList($query.$limit_cond);
		$this->_total = $this->_getListCount($query);

		return $result;
	}

	protected function getListQuery(){
		$db = JFactory::getDbo();
		$task = JFactory::getApplication()->input->get("task", "", "raw");
		$search_teacher = JFactory::getApplication()->input->get("search_teacher", "", "raw");
		$search_text = JFactory::getApplication()->input->get("search_text", "", "raw");
		
		$and = "";

		if(trim($search_text) != ""){
			$and .= ' AND (p.`name` like "%'.$db->escape(trim($search_text)).'%" OR c.`firstname` like "%'.$db->escape(trim($search_text)).'%" OR c.`lastname` like "%'.$db->escape(trim($search_text)).'%") ';
		}

		if(intval($search_teacher) != 0){
			$sql = "select `id` from #__guru_program where `author`=".intval($search_teacher)." OR `author` like '%|".intval($search_teacher)."|%'";
			$db->setQuery($sql);
			$db->execute();
			$course_ids = $db->loadColumn();

			if(is_array($course_ids) && count($course_ids) > 0){
				$and .= " AND mc.`course_id` in (".implode(",", $course_ids).")";
			}
			else{
				$and .= " AND mc.`author_id`=".intval($search_teacher);
			}
		}
		
		$sql = "SELECT mc.*, p.`id` as course_id, p.`name` as course_name, c.`firstname`, c.`lastname` FROM #__guru_mycertificates mc, #__guru_program p, #__guru_customer c WHERE mc.`completed`='1' AND p.`id`=mc.`course_id` AND c.`id`=mc.`user_id` ".$and." ORDER BY mc.`id` desc";
		
		return $sql;
	}

	function getCourseDetails($course_id){
		$db = JFactory::getDbo();

		$sql = "select * from #__guru_program where `id`=".intval($course_id);
		$db->setQuery($sql);
		$db->execute();
		$course = $db->loadAssocList();

		return $course;
	}

	function getCourseAuthor($author_ids){
		$db = JFactory::getDbo();
		$author_ids = explode("|", $author_ids);

		if(isset($author_ids) && count($author_ids) > 0){
			foreach ($author_ids as $key => $value) {
				$author_ids[$key] = intval($author_ids[$key]);
			}
		}
		else{
			$author_ids = array("0");
		}

		$sql = "select u.`id`, u.`name` from #__users u where u.`id` in (".implode(",", $author_ids).")";
		$db->setQuery($sql);
		$db->execute();
		$authors = $db->loadAssocList();

		return $authors;
	}
};
?>