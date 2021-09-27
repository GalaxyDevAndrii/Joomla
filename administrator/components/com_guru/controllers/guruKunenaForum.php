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

class guruAdminControllerguruKunenaForum extends guruAdminController {
	var $_model = null;
	
	function __construct(){
		parent::__construct();
		$this->registerTask ("", "display");
		$this->registerTask ("kunenaoninstall", "kunenaoninstall");
		$this->registerTask ("kunenaoninstallno", "");
        $this->_model = $this->getModel("guruKunenaForum");
	}

    function display($cachable = false, $urlparams = Array()){
		$view = $this->getView("guruKunenaForum", "html");
        $view->setLayout('default');
		$view->setModel($this->_model, true);
		@$view->display();
    }
	function cancel(){
		 $msg = JText::_('GURU_MEDIACANCEL');	
		 $app = JFactory::getApplication('administrator');
		 $app->enqueueMessage($msg);
		 $app->redirect('index.php?option=com_guru');
	}
    function save(){
        $app = JFactory::getApplication('administrator');
        if($this->_model->savekunenadetails()){
            $msg = JText::_('GURU_MODIF_OK');
            $app->enqueueMessage($msg);
            $app->redirect('index.php?option=com_guru');
        } 
		else{
            $msg = JText::_('GURU_ERRORKUNENA').'<a href="http://www.ijoomla.com/redirect/guru/kunena.htm">'.' '. here.'</a>'.JText::_('GURU_ERRORKUNENA2') ;
            $app->enqueueMessage($msg);
            $app->redirect('index.php?option=com_guru');
        }
        $this->display();
    }
    
    function apply(){
        $app = JFactory::getApplication('administrator');
        if($id = $this->_model->savekunenadetails()){
            $msg = JText::_('GURU_MODIF_OK');
            $app->enqueueMessage($msg);
            $app->redirect('index.php?option=com_guru&controller=guruKunenaForum');
        }
		else{
            $msg = JText::_('GURU_ERRORKUNENA').'<a href="http://www.ijoomla.com/redirect/guru/kunena.htm">'.' '. here.'</a>'.JText::_('GURU_ERRORKUNENA2') ;
            $app->enqueueMessage($msg);
            $app->redirect('index.php?option=com_guru&controller=guruKunenaForum');
        }
        $this->display();
    }
	
	function kunenaoninstall(){
		$db = JFactory::getDBO();
		$sql = "UPDATE #__guru_kunena_forum set  forumboardlesson= '1', forumboardcourse='1'";
		$db->setQuery($sql);
		$db->execute();
		
		$db->setQuery("SELECT `kunena_category` FROM #__guru_kunena_forum WHERE id=1");
		$db->execute();	
		$kunena_category = $db->loadColumn();
		$kunena_category = @$kunena_category["0"];

		if(intval($kunena_category) == 0){
			$nameofmainforum = JText::_('GURU_TREECOURSE');
		}
		else{
			$sql = "SELECT `name` FROM #__kunena_categories WHERE id='".intval($kunena_category)."'";
			$db->setQuery($sql);
			$db->execute();
			$nameofmainforum = $db->loadResult();
		}

		$sql = "SELECT name FROM #__kunena_categories WHERE parent_id='0' and name='".$nameofmainforum."'";
		$db->setQuery($sql);
		$db->execute($sql);
		$result = $db->loadColumn();
		$result = $result["0"];

		if(count($result) == 0){
			$sql = "INSERT INTO #__kunena_categories (parent_id, name, alias, icon_id, locked, accesstype, access, pub_access, pub_recurse, admin_access, admin_recurse, ordering, published, channels, checked_out, checked_out_time, review, allow_anonymous, post_anonymous, hits, description, headerdesc, class_sfx, allow_polls, topic_ordering, numTopics, numPosts, last_topic_id, last_post_id, last_post_time, params) VALUES (".intval($kunena_category).", '".$db->escape($nameofmainforum)."', 'course', 0, 0, 'joomla.level', 1, 1, 1, 8, 1, 2, 1, NULL, 0, '0000-00-00 00:00:00', 0, 0, 0, 0, '', '', '', 0, 'lastpost', 0, 0, 0, 0, 0, '{}')";
			$db->setQuery($sql);
			$db->execute($sql);
		}
		$sql = "SELECT id FROM #__kunena_categories WHERE name='".$nameofmainforum."'";
		$db->setQuery($sql);
		$db->execute();
		$idmainforum = $db->loadColumn();
		$idmainforum = $idmainforum["0"];
		
		$sql = "SELECT alias FROM #__kunena_categories WHERE name='".$nameofmainforum."'";
		$db->setQuery($sql);
		$db->execute();
		$alias = $db->loadColumn();
		$alias = $alias["0"];
		
		if(trim($alias) != ""){
			$sql = "INSERT INTO #__kunena_aliases (alias, type, item, state) VALUES ( '".$alias."', 'catid', ".$idmainforum.", 0)";
			$db->setQuery($sql);
			$db->execute($sql);
		}
		
		$sql = "SELECT id, name, alias FROM #__guru_program order by id";
		$db->setQuery($sql);
		$db->execute();
		$coursenames = $db->loadAssocList();	
		
		for($i=0; $i < count($coursenames); $i++){	
			$sql = "SELECT name FROM #__kunena_categories WHERE alias='".$coursenames[$i]['alias']."'";
			$db->setQuery($sql);
			$db->execute($sql);
			$result1 = $db->loadColumn();
			$result1 = $result1["0"];
			
			$sql = "SELECT count(id) FROM #__kunena_categories WHERE alias='".$coursenames[$i]['alias']."'";
			$db->setQuery($sql);
			$db->execute($sql);
			$countcoursesb = $db->loadColumn();
			$countcoursesb = $countcoursesb["0"];
			
			if($countcoursesb == 0){
			
				if($result1 == 0){
					$sql = "INSERT INTO #__kunena_categories (parent_id, name, alias, icon_id, locked, accesstype, access, pub_access, pub_recurse, admin_access, admin_recurse, ordering, published, channels, checked_out, checked_out_time, review, allow_anonymous, post_anonymous, hits, description, headerdesc, class_sfx, allow_polls, topic_ordering, numTopics, numPosts, last_topic_id, last_post_id, last_post_time, params) VALUES ( '".$idmainforum."', '".$db->escape($coursenames[$i]["name"])."', '".$db->escape($coursenames[$i]['alias'])."', 0, 0, 'joomla.group', 1, 1, 1, 8, 1, 2, 1, NULL, 0, '0000-00-00 00:00:00', 0, 0, 0, 0, '', '', '', 0, 'lastpost', 0, 0, 0, 0, 0, '{}')";
					$db->setQuery($sql);
					$db->execute($sql);
				}
				
				$sql = "SELECT id FROM #__kunena_categories WHERE  name='".$coursenames[$i]['name']."'";
				$db->setQuery($sql);
				$db->execute($sql);
				$resultid = $db->loadColumn();
				$resultid = $resultid["0"];
				
				if(trim($coursenames[$i]['alias']) != ""){
					$sql = "INSERT INTO #__kunena_aliases (alias, type, item, state) VALUES (  '".$coursenames[$i]['alias']."', 'catid', ".$resultid.", 0)";
					$db->setQuery($sql);
					$db->execute($sql);
				}
	
				$sql = "INSERT INTO #__guru_kunena_courseslinkage (idcourse, coursename, catidkunena) VALUES (  '".$coursenames[$i]['id']."', '".$coursenames[$i]['name']."', '".$resultid."')";
				$db->setQuery($sql);
				$db->execute($sql);
			
			
				
				
				$sql = "SELECT id, title, alias from #__guru_days where pid =".intval($coursenames[$i]['id'])." order by ordering ";
				$db->setQuery($sql);
				$db->execute($sql);	
				$aliasmodules = $db->loadAssocList();
				
				for($k = 0; $k <count($aliasmodules); $k++ ){
						$sql = "SELECT name FROM #__kunena_categories WHERE parent_id='".$resultidmodule."' and alias='".$aliasmodules[$k]['alias']."'";
						$db->setQuery($sql);
						$db->execute($sql);
						$resultt = $db->loadColumn();
						$resultt = $resultt["0"];
						
						if(count($resultt) == 0){
							$sql = "INSERT INTO #__kunena_categories (parent_id, name, alias, icon_id, locked, accesstype, access, pub_access, pub_recurse, admin_access, admin_recurse, ordering, published, channels, checked_out, checked_out_time, review, allow_anonymous, post_anonymous, hits, description, headerdesc, class_sfx, allow_polls, topic_ordering, numTopics, numPosts, last_topic_id, last_post_id, last_post_time, params) VALUES ( ".$resultid.", '".$db->escape($aliasmodules[$k]['title'])."', '".$db->escape($aliasmodules[$k]['alias'])."', 0, 0, 'joomla.group', 1, 1, 1, 8, 1, '".($k+1)."', 1, NULL, 0, '0000-00-00 00:00:00', 0, 0, 0, 0, '', '', '', 0, 'lastpost', 0, 0, 0, 0, 0, '{}')";
							$db->setQuery($sql);
							$db->execute($sql);
	
						}
					$sql = "SELECT id FROM #__kunena_categories WHERE  name='".$aliasmodules[$k]['title']."'";
					$db->setQuery($sql);
					$db->execute($sql);
					$resultidmodule = $db->loadColumn();
					$resultidmodule = $resultidmodule["0"];	
						
					if(count($resultt) == 0){
						if(trim($aliasmodules[$k]['alias']) != ""){
							$sql = "INSERT INTO #__kunena_aliases (alias, type, item, state) VALUES ('".$aliasmodules[$k]['alias']."', 'catid', '".$resultidmodule."', 0)";
							$db->setQuery($sql);
							$db->execute($sql);
						}
					 }
				
					$sql = "SELECT id, name, alias from #__guru_task where id IN (SELECT media_id FROM #__guru_mediarel WHERE type = 'dtask' AND type_id in (".$aliasmodules[$k]['id'].")) order by ordering";
					$db->setQuery($sql);
					$db->execute($sql);	
					$aliaslesson = $db->loadAssocList();
	
					
					for($j = 0; $j<count($aliaslesson); $j++){
					
						$sql = "SELECT name FROM #__kunena_categories WHERE parent_id='".$resultidmodule."' and alias='".$aliaslesson[$j]['alias']."'";
						$db->setQuery($sql);
						$db->execute($sql);
						$result2 = $db->loadColumn();
						$result2 = $result2["0"];
						
						if(count($result2) == 0){
							$sql = "INSERT INTO #__kunena_categories (parent_id, name, alias, icon_id, locked, accesstype, access, pub_access, pub_recurse, admin_access, admin_recurse, ordering, published, channels, checked_out, checked_out_time, review, allow_anonymous, post_anonymous, hits, description, headerdesc, class_sfx, allow_polls, topic_ordering, numTopics, numPosts, last_topic_id, last_post_id, last_post_time, params) VALUES ( ".$resultidmodule.", '".$db->escape($aliaslesson[$j]['name'])."', '".$db->escape($aliaslesson[$j]['alias'])."', 0, 0, 'joomla.group', 1, 1, 1, 8, 1, '".($j+1)."', 1, NULL, 0, '0000-00-00 00:00:00', 0, 0, 0, 0, '', '', '', 0, 'lastpost', 0, 0, 0, 0, 0, '{}')";
							$db->setQuery($sql);
							$db->execute($sql);
						}
					  
					
					  $sql = "SELECT id FROM #__kunena_categories WHERE alias= '".$aliaslesson[$j]['alias']."'";
					  $db->setQuery($sql);
					  $db->execute($sql);
					  $resultidlesson = $db->loadColumn();
					  $resultidlesson = $resultidlesson["0"];
					  
					 if(count($result2) == 0){
					 	if(trim($aliaslesson[$j]['alias']) != ""){
							$sql = "INSERT INTO #__kunena_aliases (alias, type, item, state) VALUES ('".$aliaslesson[$j]['alias']."', 'catid', '".$resultidlesson."', 0)";
							$db->setQuery($sql);
							$db->execute($sql);
						 }
					 }
					 $sql = "INSERT INTO #__guru_kunena_lessonslinkage (idlesson, lessonname, catidkunena) VALUES (  '".$aliaslesson[$j]['id']."', '".addslashes($aliaslesson[$j]['name'])."', '".$resultidlesson."')";
					 $db->setQuery($sql);
					 $db->execute($sql);
					
					 $sql = "SELECT catidkunena FROM #__guru_kunena_lessonslinkage where idlesson=".$aliaslesson[$j]['id']." order by id desc limit 0,1";
					 $db->setQuery($sql);
					 $db->execute();
					 $catidkunena = $db->loadColumn(); 
					 $catidkunena = $catidkunena["0"];
					 
					 $sql = "UPDATE #__kunena_categories set name='".$db->escape($aliaslesson[$j]['name'])."' WHERE id=".intval($catidkunena);
					 $db->setQuery($sql);
					 $db->execute();
				}
			}
		}
	}
	
		  $sql = "UPDATE #__guru_task set forum_kunena_generatedt=1";
		  $db->setQuery($sql);
		  $db->execute($sql);

		$msg = JText::_('GURU_MODIF_OK');
        $this->setRedirect('index.php?option=com_guru&controller=guruKunenaForum', $msg);
	}
}

?>