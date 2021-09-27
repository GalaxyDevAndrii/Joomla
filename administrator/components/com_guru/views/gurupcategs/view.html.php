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
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
jimport ("joomla.application.component.view");
class guruAdminViewguruPcategs extends JViewLegacy {
	
	function display ($tpl =  null ) {
		$db = JFactory::getDBO();
		JToolBarHelper::title(JText::_('GURU_CSCAT_MANAGER'), 'generic.png');
		JToolBarHelper::publishList();
		JToolBarHelper::unpublishList();
		JToolBarHelper::addNew();
		JToolBarHelper::editList();
		JToolBarHelper::deleteList(JText::_('GURU_SURE_DELETE_CATEGORY'));
		$categs = $this->get('Items');
		$pagination = $this->get( 'Pagination' );
		$this->categs = $categs;	
		$this->pagination = $pagination;
		
		parent::display($tpl);
	}
	/*
	List categories
	*/
	function list_all($name, $category_id, $selected_categories=Array(), $size=1, $toplevel=true, $multiple=false) {
		$data_post = JFactory::getApplication()->input->post->getArray();
		$db = JFactory::getDBO();
		$q  = "SELECT parent_id FROM #__guru_categoryrel ";
		if( $category_id ){
			$q .= "WHERE child_id ='$category_id'";
		}	
		$db->setQuery($q);   
		$db->execute();
		$category_id=$db->loadResult();
		if(isset($data_post['parentcategory_id'])){
			$category_id=intval($data_post['parentcategory_id']);
		}	
		$pid = JFactory::getApplication()->input->get("pid", "0");
		echo "<select class=\"inputbox\" size=\"$size\" name=\"$name\">\n";
		if($toplevel){
			$selected = "";
			if($pid == "0"){
				$selected = 'selected="selected"';
			}
			echo "<option value=\"0\" ".$selected.">(0) Top</option>\n";
		}		
		$this->list_tree($category_id, '0', '0', $selected_categories);
		echo "</select>\n";
		}
	/*
	List categories
	*/
	function list_tree($category_id="", $cid='0', $level='0', $selected_categories=Array() ) {		
		$db = JFactory::getDBO();
		$level++;
		$q = "SELECT id, child_id, ordering, name FROM #__guru_category,#__guru_categoryrel ";
		$q .= "WHERE #__guru_categoryrel.parent_id='$cid' ";
		$q .= "AND #__guru_category.id=#__guru_categoryrel.child_id ";
		$q .= "ORDER BY #__guru_category.ordering ASC";
		$db->setQuery($q);
		$allresults = $db->loadObjectList();
		$pid = JFactory::getApplication()->input->get("pid", "0");
		
		foreach ($allresults as $child) {
			$child_id = $child->id;
			if ($child_id != $cid) {
				$selected = "";
				if($pid == $child->id){
					$selected = "selected=\"selected\"";
				}
				echo "<option ".$selected," value=\"$child_id\">\n";
			}
			for ($i=0;$i<$level;$i++) {
				echo "&#151;";
			}
			echo "($level)";
			echo "&nbsp;" . $child->name . "</option>";
			$this->list_tree($category_id, $child_id, $level, $selected_categories);
		}
	}
	
	function categlist($category_id="", $cid='0', $level='0', $selected_categories=Array()){		
		global $ids_array;
		
		if(!isset($ids_array))
			$ids_array=0;
		
		$db = JFactory::getDBO();
		$level++;				
		
		$q = "SELECT id, child_id, ordering, published, name, parent_id FROM #__guru_category, #__guru_categoryrel ";
		$q .= "WHERE #__guru_categoryrel.parent_id='$cid' ";
		$q .= "AND #__guru_category.id=#__guru_categoryrel.child_id ";
		$q .= "ORDER BY #__guru_category.ordering ASC ";
		
		if($category_id == ""){
			$app = JFactory::getApplication('administrator');
			$limitstart	= $app->getUserStateFromRequest('limitstart','limitstart','0');
			$limit		= $app->getUserStateFromRequest('limit','limit',$app->getCfg('list_limit'));
			
			if(intval($limitstart) != 0 || intval($limit) != 0){
				$q .= " LIMIT ".$limitstart.", ".$limit;
			}
		}
				
		$db->setQuery($q);
		$allresults = $db->loadObjectList();
		$nr_elemet = 0;
		if(isset($allresults)){
			$j = 0;
			foreach($allresults as $child) {
				$id = $child->id;			
				$checked = JHTML::_('grid.id', $ids_array, $id);
				$link = JRoute::_("index.php?option=com_guru&controller=guruPcategs&task=edit&cid[]=".$id."&pid=".$child->parent_id);		
				//$published = JHTML::_('grid.published', $child, $id );
				$published = JHTML::_('grid.published', $child, $ids_array );			
				
				$child_id = $child->id;
				if ($child_id != $cid) {
					echo "<tr class=\"row".($j%2)."\"><td>
                                    <span class=\"sortable-handler active\" style=\"cursor: move;\">
                                        <i class=\"icon-menu\"></i>
                                    </span>
                                    <input type=\"text\" class=\"width-20 text-area-order\" value=\"".$child->ordering."\" size=\"5\" name=\"order[]\" style=\"display:none;\">
                                </td><td>".$checked."<span class=\"lbl\"></span></td><td>".$child->id."</td><td><a class=\"a_guru\" href='".$link."'>";
				}
				for ($i=0;$i<$level;$i++) {
					echo "&#151;";
				}
				//echo "($level)";
				//echo "&nbsp;-";
				
				$q = "SELECT count(child_id) FROM #__guru_categoryrel WHERE parent_id = ".$child->id;
				$db->setQuery($q);
				$how_many_subcats = $db->loadResult();	
				
				$q = "SELECT count(id) FROM #__guru_program WHERE catid = ".$child->id;
				$db->setQuery($q);
				$how_many_programs = $db->loadResult();						
				
				//row with values
				echo "&nbsp;".$child->name."</a></td>
					<td align=\"center\">".$how_many_subcats."</td>
					<td align=\"center\">".$how_many_programs."</td>
					<td align=\"center\">".$published."</td></tr>";
				
				$ids_array ++;
				$this->categlist($category_id, $child_id, $level, $selected_categories);				
				$nr_elemet ++;
				$j++;
			}
		}	
	}
	
	function editForm($tpl = null) {
		$db = JFactory::getDBO();
		$categ = $this->get('categ');
		$lists = null;
		$cids = JFactory::getApplication()->input->get('cid', array(0), "raw");
		$isNew = ($categ->id < 1);
		$text = $isNew?JText::_('New'):JText::_('Edit');
		
		JToolBarHelper::title(JText::_('GURU_CATEGORY').":<small>[".$text."]</small>");
		JToolBarHelper::apply();
		JToolBarHelper::save();
		JToolBarHelper::cancel ('cancel', 'Close');
		
		$data_post = JFactory::getApplication()->input->post->getArray();
		
		//get post params
		if (isset($data_post['name'])) $categ->name = $data_post['name'];
		if (isset($data_post['description'])) $categ->description = $data_post['description'];
		if (isset($data_post['parentcategory_id'])) $categ->name = $data_post['name'];
		//get post params
		$this->categ = $categ;
		$javascript='';
		$this->lists = $lists;
		$this->isNew = $isNew;
		
		parent::display($tpl);
	}
}
?>