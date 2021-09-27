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

jimport('joomla.application.component.modellist');
jimport('joomla.utilities.date');


class guruAdminModelguruSubplan extends JModelLegacy 
{
	var $_id = null;
	var $_total = 0;
	var $total = 0;
	var $_pagination = null;
	protected $_context = 'com_guru.guruSubplan';
    var $_data = null;

	function __construct () 
    {
		parent::__construct();
		$mainframe =JFactory::getApplication();

		global $option;
		// Get the pagination request variables
		$limit = $mainframe->getUserStateFromRequest( 'global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int' );
		$limitstart = $mainframe->getUserStateFromRequest( $option.'limitstart', 'limitstart', 0, 'int' );
		
		if(JFactory::getApplication()->input->get("limitstart") == JFactory::getApplication()->input->get("old_limit")){
			JFactory::getApplication()->input->set("limitstart", "0");		
			$limit = $mainframe->getUserStateFromRequest('global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
			$limitstart = $mainframe->getUserStateFromRequest($option.'limitstart', 'limitstart', 0, 'int');
		}
		
		$this->setState('limit', $limit); // Set the limit variable for query later on
		$this->setState('limitstart', $limitstart);	
		
	}
	
	function getPagination() {
		// Lets load the content if it doesn't already exist
		if (empty($this->_pagination))	{
			jimport('joomla.html.pagination');
			if (!$this->_total) $this->getItems();
			$this->_pagination = new JPagination( $this->_total, $this->getState('limitstart'), $this->getState('limit') );
		}
		return $this->_pagination;
	}
   
	
	function getItems(){
		$config = new JConfig();	
		$app = JFactory::getApplication();
		$db = JFactory::getDBO();
		$sql = "SELECT * FROM #__guru_subplan ORDER BY ordering ASC, id DESC ";

		$limitstart=$this->getState('limitstart');
		$limit=$this->getState('limit');
			
		if($limit!=0){
			$limit_cond=" LIMIT ".$limitstart.",".$limit." ";
		} else {
			$limit_cond = NULL;
		}
		$result = $this->_getList($sql.$limit_cond);
		$this->_total = $this->_getListCount($sql);
		return $result;
	}
 
    public static function getAllPlans()
    {
        $db =JFactory::getDBO();
        $sql = "SELECT * FROM #__guru_subplan WHERE published<>'0' ORDER BY ordering ASC, id DESC ";
        $db->setQuery($sql);
        $res = $db->loadObjectList();		
        return $res;
    }
    
    function getCurrentPlan()
    {        
        $db =JFactory::getDBO();
        $data = JFactory::getApplication()->input->get->getArray();
        
        if (!isset($data['cid'][0])) {
            $sql = "SHOW FIELDS FROM #__guru_subplan";
            $db->setQuery($sql);
            $fields = $db->loadObjectList();
            
            $plan = new stdClass();
            
            foreach($fields as $element) {
                $cfield = $element->Field;
                $plan->$cfield = NULL;
            }
            return $plan;
        }
        
        $id = (int) $data['cid'][0];
        $sql = "SELECT * FROM #__guru_subplan WHERE id = " . $id;
        $db->setQuery($sql);
        $res = $db->loadObject();
        
        return $res;
    }
    
    function store()
    {
		$item = $this->getTable('guruSubplan');
        
		$data = JFactory::getApplication()->input->post->getArray();
        
        if(trim($data["id"]) == ""){
            $data["id"] = 0;
        }

		$res = true;
		if (!$item->bind($data)){
			$res = false;
		}
		if (!$item->check()) {
			$res = false;
		}
		if (!$item->store()) {
			$res = false;
		}
        
        if ($res == true && isset($item->id)) {
            return $item->id;
        } else {
            return false;
        }
    }
    
    function publish()
    {
		$db = JFactory::getDBO();
		$cids = JFactory::getApplication()->input->get('cid', array(0), "raw");
		$task = JFactory::getApplication()->input->get('task', '');	

        if ( $task == 'approve' || $task == 'publish' ) {
			$sql = "UPDATE #__guru_subplan SET published='1' WHERE id in ('" . implode("','", $cids) . "')";
		} elseif ( $task == 'unapprove' || $task == 'unpublish' ) {
			$sql = "UPDATE #__guru_subplan SET published='0' WHERE id in ('" . implode("','", $cids) . "')";
		} elseif ( $task == 'remove' ) {;
			if($cids["0"] == 1){
				$msg = JText::_('GURU_CANNOT_DELETE_PLAN');
				return $msg;
			}	
            $sql = "DELETE FROM #__guru_subplan WHERE id in ('" . implode("','", $cids) . "')";
        }
        
		$db->setQuery($sql);
		return $db->execute() ? true : false;
	
    }
    
    function updateOrder($elements) {
        $db =JFactory::getDBO();
        $order = 0;
        foreach ($elements as &$current) {
            $sql = "UPDATE #__guru_subplan SET ordering = '{$order}' 
                      WHERE id ={$current->id};";
            $current->ordering = $order;
            $db->setQuery($sql);
            $db->execute();
            $sqlz[] = $sql;
            $order++;
        }
        return $elements;
    }
    
    function saveorder() 
    {
        $db =JFactory::getDBO();
        $data = JFactory::getApplication()->input->post->getArray();
        $ok = true;
        
        if ($data['task'] == 'saveorder') {
            // Combine the ids with their ordering numbers
            $order = array_combine($data['cid'], $data['order']);
            // Sort ascending the order array
            asort($order);
            // The new value for each item [will be auto-incremented below]
            $new_val = 0;            
            foreach($order as $key => $value) {
                $sql = "UPDATE #__guru_subplan
                           SET ordering = '{$new_val}'
                           WHERE id={$key}";
                $sqlz[] = $sql;
                $db->setQuery($sql);
                if ( !$db->execute() ) {
                    $ok = false;
                }
                $new_val++;
            }
        } elseif ( $data['task'] == 'orderup' || $data['task'] == 'orderdown' ) {
            $current_item['id'] = (int) $data['cid'][0];
            
            $sql = "SELECT ordering FROM #__guru_subplan WHERE id = {$current_item['id']}";
            $db->setQuery($sql);
            $current_item['ordering'] = $db->loadResult();
            
            $compare = ($data['task'] == 'orderup') ? '<' : '>';
            $desc_or_asc = ($data['task'] == 'orderup') ? 'DESC' : 'ASC';
            
            $sql = "SELECT id, ordering FROM #__guru_subplan 
                       WHERE ordering " . $compare . " {$current_item['ordering']} 
                       ORDER BY ordering " . $desc_or_asc . " LIMIT 1";
            $sqlz[] = $sql;
            $db->setQuery($sql);
            $previous_item = $db->loadAssoc();
            
            // If we have a previous/next item, interchange the 2
            if ( !empty($previous_item) ) {

                // Update ordering for the current item
                $sql = "UPDATE #__guru_subplan 
                           SET ordering = '{$previous_item['ordering']}'
                           WHERE id = {$current_item['id']}";
                $sqlz[] = $sql;
                $db->setQuery($sql);
                if ( !$db->execute() ) {
                    $ok = false;
                }

                // Update ordering for the current item
                $sql = "UPDATE #__guru_subplan 
                           SET ordering = '{$current_item['ordering']}'
                           WHERE id = {$previous_item['id']}";
                $sqlz[] = $sql;
                $db->setQuery($sql);
                if ( !$db->execute() ) {
                    $ok = false;
                }
                
            }
        }
        
        return $ok;        
    }
    
};
?>