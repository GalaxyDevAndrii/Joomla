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

function guruBuildRoute(&$query){
	$segments = array();
	$database = JFactory::getDBO();
	$task = @$query['task'];
	$active_menu_id = @$query['Itemid'];
	unset($query['task']);
	
	$db = JFactory::getDbo();
	$sql = "select `seo` from #__guru_config";
	$db->setQuery($sql);
	$db->execute();
	$seo = $db->loadColumn();
	$seo = @$seo["0"];
	
	if(trim($seo) != ""){
		$seo_array = json_decode($seo, true);
		$seo = @$seo_array["seo"];
	}
	else{
		$seo = array();
	}
	
	// get a menu item based on Itemid or currently active
	$app		= JFactory::getApplication();
	$menu		= $app->getMenu();
	// we need a menu item.  
	//Either the one specified in the query, or the current active one if none specified
	if (empty($query['Itemid'])) {
		$menuItem = $menu->getActive();
		$menuItemGiven = false;
		unset($query['Itemid']);
	}
	else {
		$menuItem = $menu->getItem($query['Itemid']);
		$menuItemGiven = true;
	}

	$view = "";
	if(isset($query['view'])) {
		$view = $query['view'];
	}
	else{
		// we need to have a view in the query or it is an invalid URL
		@$view = $menuItem->query["view"];
		$query['view'] = $view;
	}

	if(isset($query['view']) && ($query['view'] == "guruPcategs" || $query['view'] == "gurupcategs")){
		if(isset($seo["gurupcategs"]) && trim($seo["gurupcategs"]) != ""){
			$segments[] = $seo["gurupcategs"];
		}
		else{
			$segments[] = $query['view'];
		}

		unset($query['view']);		
		unset($query['task']);
		
		if(isset($query['cid'])){
			$segments[] = $query['cid'];
		}

		unset($query['cid']);
		
		if(isset($query['layout'])){
			$segments[] = $query['layout'];
			unset($query['layout']);
		}
		else{
			$db = JFactory::getDBO();	
			$sql = "SELECT `params` from #__menu where id=".intval(@$query["Itemid"]);
			$db->setQuery($sql);
			$db->execute();
			$params = $db->loadColumn();
			$params = @$params["0"];
			$params = json_decode($params);
			
			if(isset($params->cid)){
				$segments[] = "view";
			}
		}
	}
	
	if(isset($query['view']) && ($query['view'] == "guruPrograms" || $query['view'] == "guruprograms")){
		if(isset($seo["guruprograms"]) && trim($seo["guruprograms"]) != ""){
			$segments[] = $seo["guruprograms"];
		}
		else{
			$segments[] = $query['view'];
		}
		unset($query['view']);

		if(isset($query['cid'])){
			$course_id = intval($query['cid']);
		}
		else{
			$course_id = "0";

			if(isset($query['Itemid'])){
				$db = JFactory::getDBO();
				$sql = "SELECT `params` from #__menu where id=".intval($query['Itemid']);
				$db->setQuery($sql);
				$db->execute();
				$params = $db->loadColumn();
				$params = @$params["0"];
				$params = json_decode($params);
				
				if(isset($params->cid) && ($params->cid != "")){
					$course_id = intval($params->cid);
				}
			}
		}
		
		if($course_id == "0"){
			$course_id = intval(@$query['course_id']);
		}
		
		if($course_id == "0"){
			if(isset($menuItem->params)){
				$course_id = intval($menuItem->params->get("cid", 0));
			}
		}
		
		//------------------------------------------------------------------------------------		
		$sql = "select catid from #__guru_program where id=".intval($course_id);		
		$database->setQuery($sql);
		$database->execute();
		$category_id = $database->loadResult();
		if(intval($category_id) != 0){
			$sql = "select name, alias from #__guru_category where id=".intval($category_id);
			$database->setQuery($sql);
			$database->execute();
			$result = $database->loadAssocList();			
			$alias = "";
			if(isset($result["0"]["alias"])){
				if(trim($result["0"]["alias"]) == ""){
					$alias = JFilterOutput::stringURLSafe($result["0"]["name"]);
				}
				else{
					$alias = trim($result["0"]["alias"]);
				}
			}
			$segments[] = $category_id."-".$alias;
		}
		//------------------------------------------------------------------------------------
		
		$sql = "select name, alias from #__guru_program where id=".intval($course_id);
		$database->setQuery($sql);
		$database->execute();
		$result = $database->loadAssocList();			
		$alias = "";
		
		if(trim(@$result["0"]["alias"]) == ""){
			$alias = JFilterOutput::stringURLSafe(@$result["0"]["name"]);
		}
		else{
			$alias = trim($result["0"]["alias"]);
		}
		
		$segments[] = intval($course_id)."-".$alias;
		unset($query['cid']);
		unset($query['layout']);
		
		if(isset($query['action'])){
			$segments[] = $query['action'];
			unset($query['action']);
		}
		
		unset($query['catid']);
		unset($query['controller']);
		
		if(isset($query['registered_user'])){
			$segments[] = $query['registered_user'];
			unset($query['registered_user']);
		}
	}
	
	if(isset($query['view']) && ($query['view'] == "gurutasks" || $query['view'] == "guruTasks")){
		if(isset($seo["gurutasks"]) && trim($seo["gurutasks"]) != ""){
			$segments[] = $seo["gurutasks"];
		}
		else{
			$segments[] = $query['view'];
		}
		unset($query['view']);
		//------------------------------------------------------------------------------------
		if(isset($query['catid'])){
			$sql = "select name, alias from #__guru_category where id=".intval($query['catid']);
			$database->setQuery($sql);
			$database->execute();
			$result = $database->loadAssocList();			
			$alias = "";
			if(trim(@$result["0"]["alias"]) == ""){
				$alias = JFilterOutput::stringURLSafe(@$result["0"]["name"]);
			}
			else{
				$alias = trim(@$result["0"]["alias"]);
			}
			$segments[] = intval($query['catid'])."-".$alias;
			unset($query['catid']);
		}
		//------------------------------------------------------------------------------------
		if(isset($query['module'])){
			$sql = "select title, alias from #__guru_days where id=".intval($query['module']);
			$database->setQuery($sql);
			$database->execute();
			$result = $database->loadAssocList();			
			$alias = "";
			if(trim(@$result["0"]["alias"]) == ""){
				$alias = JFilterOutput::stringURLSafe(@$result["0"]["title"]);
			}
			else{
				$alias = trim(@$result["0"]["alias"]);
			}
			$segments[] = intval($query['module'])."-".$alias;
			unset($query['module']);
		}
		//------------------------------------------------------------------------------------		
		if(isset($query['cid'])){
			$sql = "select name, alias from #__guru_task where id=".intval($query['cid']);
			$database->setQuery($sql);
			$database->execute();
			$result = $database->loadAssocList();		
			$alias = "";
			if(trim(@$result["0"]["alias"]) == ""){
				$alias = JFilterOutput::stringURLSafe(@$result["0"]["name"]);
			}
			else{
				$alias = trim($result["0"]["alias"]);
			}
			if(intval($query['cid']) != "0"){
				$segments[] = intval($query['cid'])."-".$alias;
				unset($query['cid']);
			}
		}
		//------------------------------------------------------------------------------------
		
		unset($query['task']);
		
		unset($query['action']);
		
		if(isset($query['certificate'])){
			$segments[] = $query['certificate'];
			unset($query['certificate']);
		}
		if(isset($query['pdf'])){
			$segments[] = $query['pdf'];
			unset($query['pdf']);
		}
		
		if(isset($query['dw'])){
			$segments[] = $query['dw'];
			unset($query['dw']);
		}
		
		if(isset($query['ci'])){
			$segments[] = $query['ci'];
			unset($query['ci']);
		}
		
		if(isset($query['prev_lesson_id'])){
			$segments[] = $query['prev_lesson_id'];
			unset($query['prev_lesson_id']);
		}
		
		if(isset($query['module_prev_lesson'])){
			$segments[] = $query['module_prev_lesson'];
			unset($query['module_prev_lesson']);
		}
		
		if(isset($query["course_id"])){
			$segments[] = $query["course_id"];
			unset($query['course_id']);
		}
		
		unset($query['view']);
		
	}

	if(isset($query['view']) && (($query['view'] == "guruorders") || ($query['view'] == "guruOrders"))){
		if(isset($seo["guruorders"]) && trim($seo["guruorders"]) != ""){
			$segments[] = $seo["guruorders"];
		}
		else{
			$segments[] = $query['view'];
		}
		unset($query['view']);
		if(isset($query['layout'])){
			$segments[] = $query['layout'];
			unset($query['layout']);
		}
		
		if(isset($query['back'])){
			$segments[] = $query['back'];
			unset($query['back']);
		}

		if(isset($query['course'])){
			$sql = "select name, alias from #__guru_program where id=".intval($query['course']);
			$database->setQuery($sql);
			$database->execute();
			$result = $database->loadAssocList();			
			$alias = "";
			if(trim($result["0"]["alias"]) == ""){
				$alias = JFilterOutput::stringURLSafe($result["0"]["name"]);
			}
			else{
				$alias = trim($result["0"]["alias"]);
			}
			if(intval($query['course']) != 0){
				$segments[] = intval($query['course'])."-".$alias;
				unset($query['course']);
			}
		}
	}
	
	if(isset($query['view']) && ($query['view'] == "guruauthor")){
		if(isset($seo["guruauthor"]) && trim($seo["guruauthor"]) != ""){
			$segments[] = $seo["guruauthor"];
		}
		else{
			$segments[] = $query['view'];
		}
		unset($query['view']);
		if(isset($query['controller'])){
			unset($query['controller']);
		}

		if(intval($active_menu_id) != 0){
			$menuItem = $menu->getItem($query['Itemid']);
			if(isset($menuItem)){
				$menu_vars = $menuItem->query;
				$id = @$query["id"];
				$cid = @$query["cid"];
				$pid = @$query["pid"];
				
				if(isset($menu_vars["layout"]) && !isset($query['layout']) && (intval($id) == 0 && intval($cid) == 0 && intval($pid) == 0)){
					$segments[] = $menu_vars["layout"];
				}
				else{
					if(isset($query['layout'])){
						$segments[] = $query['layout'];
						unset($query['layout']);
					}
				}
			}	
		}
		else{
			if(isset($query['layout'])){
				$segments[] = $query['layout'];
				unset($query['layout']);
			}
		}

		if($task == "projectForm"){
			$segments[] = "projectForm";
		}

		if($task == "projectResult"){
			$segments[] = "projectResult";
		}

		if($task != "projects" && $task != "projectForm" && $task != "projectResult"){
			$segments[] = @$query['cid'];
			unset($query['cid']);
		}
		
		if(isset($query['pid'])){
			$segments[] = $query['pid'];
			unset($query['pid']);
		}
		
		if(isset($query['id'])){
			$segments[] = $query['id'];
			unset($query['id']);
		}
		
		if(isset($query['userid'])){
			$segments[] = $query['userid'];
			unset($query['userid']);
		}
		
		if(isset($query['v'])){
			$segments[] = $query['v'];
			unset($query['v']);
		}
		
		if(isset($query['e'])){
			$segments[] = $query['e'];
			unset($query['e']);
		}
		if(isset($query['export'])){
			$segments[] = $query['export'];
			unset($query['export']);
		}
		
		if(isset($query['quiz'])){
			$db = JFactory::getDBO();
			$sql = "select `name` from #__guru_quiz where `id`=".intval($query['quiz']);
			$db->setQuery($sql);
			$db->execute();
			$q_name = $db->loadColumn();
			$q_name = @$q_name["0"];
			$q_alias = JFilterOutput::stringURLSafe($q_name);
			
			$segments[] = $query['quiz']."-".$q_alias;
			unset($query['quiz']);
		}
		
		if($task == "treeCourse"){
			$segments[] = "tree";
		}
		elseif($task == "addCourse"){
			$segments[] = "info";
		}
		elseif($task == "editMedia"){
			$segments[] = "infom";
		}
		elseif($task == "editQuizFE"){
			$segments[] = "infoq";
		}
		elseif($task == "authoraddeditmediacat"){
			$segments[] = "infomc";
		}
		elseif($task == "newStudent"){
			$segments[] = "infos";
		}
		elseif($task == "course_stats"){
			$segments[] = "stats";
		}
		elseif($task == "quizz_stats"){
			$segments[] = "qstats";
		}
		elseif($task == "studentdetails"){
			$segments[] = "ststats";
		}
		elseif($task == "studentquizes"){
			$segments[] = "squizes";
		}
		elseif($task == "quizdetails"){
			$segments[] = "qdetails";
		}
		elseif($task == "mark"){
			$segments[] = "mark";
		}
		elseif($task == "authoressays"){
			$segments[] = "authoressays";
		}
		elseif($task == "mystudents"){
		   $segments[] = "mystudents";
	  	}
	  	
		if(isset($query["tmpl"])){
			$segments[] = $query["tmpl"];
			unset($query['tmpl']);
		}
		if(isset($query["action"])){
			$segments[] = $query["action"];
			unset($query['action']);
		}
	}
	
	if(isset($query['view']) && ($query['view'] == "guruProfile")){
		if(isset($seo["guruprofile"]) && trim($seo["guruprofile"]) != ""){
			$segments[] = $seo["guruprofile"];
		}
		else{
			$segments[] = $query['view'];
		}
		unset($query['view']);
		
		if(isset($query["task"])){
			$segments[] = $query["task"];
			unset($query['task']);
		}
		
		if(isset($query["course_id"])){
			$segments[] = $query["course_id"];
			unset($query['course_id']);
		}
		
		if(isset($query["returnpage"])){
			$segments[] = $query["returnpage"];
			unset($query['returnpage']);
		}
		
		if(isset($query["graybox"])){
			$segments[] = $query["graybox"];
			unset($query['graybox']);
		}
		
		if(isset($query["tmpl"])){
			$segments[] = $query["tmpl"];
			unset($query['tmpl']);
		}
	}

	if(isset($query['view']) && ($query['view'] == "guruLogin")){
		if(isset($seo["gurulogin"]) && trim($seo["gurulogin"]) != ""){
			$segments[] = $seo["gurulogin"];
		}
		else{
			$segments[] = $query['view'];
		}
		unset($query['view']);
		
		$segments[] = @$query['returnpage'];
		unset($query['returnpage']);
		
		if(isset($query['cid'])){
			$sql = "select name, alias from #__guru_program where id=".intval($query['cid']);
			$database->setQuery($sql);
			$database->execute();
			$result = $database->loadAssocList();			
			$alias = "";
			if(trim($result["0"]["alias"]) == ""){
				$alias = JFilterOutput::stringURLSafe($result["0"]["name"]);
			}
			else{
				$alias = trim($result["0"]["alias"]);
			}
			if(intval($query['cid']) != 0){
				$segments[] = intval($query['cid'])."-".$alias;
				unset($query['cid']);
			}
		}
	}
	
	if(isset($query['view']) && ($query['view'] == "guruEditplans")){
		if(isset($seo["gurueditplans"]) && trim($seo["gurueditplans"]) != ""){
			$segments[] = $seo["gurueditplans"];
		}
		else{
			$segments[] = $query['view'];
		}
		unset($query['view']);
		
		unset($query['tmpl']);
		
		$segments[] = @$query['course_id'];
		unset($query['course_id']);
		
		if(isset($query['action'])){
			$segments[] = $query['action'];
			unset($query['action']);
		}
	}

	if(isset($query['view']) && ($query['view'] == "guruBuy" || $query['view'] == "gurubuy")){
		if(isset($seo["gurubuy"]) && trim($seo["gurubuy"]) != ""){
			$segments[] = $seo["gurubuy"];
		}
		else{
			$segments[] = $query['view'];
		}
		unset($query['view']);
	}

	if(isset($query['view']) && ($query['view'] == "guruSearch" || $query['view'] == "gurusearch")){
		$segments[] = $query['view'];
		unset($query['view']);

		$menuTable = JTable::getInstance('menu');
		$menuTable->load(array(
			'link' => 'index.php?option=com_guru&view=gurusearch',
			'published' => 1,
		));

		if ($menuTable->id) {
			$query['Itemid'] = $menuTable->id;
		}
	}
	
	if(isset($query['page_from'])){
		$segments[] = $query['page_from'];
		unset($query['page_from']);
	}

	return $segments;
}


function guruParseRoute($segments){
	$database = JFactory::getDBO();
	$user = JFactory::getUser();
	$vars = array();
	
	//Get the active menu item.
	$app	= JFactory::getApplication();
	$menu	= $app->getMenu();
	$item	= $menu->getActive();

	$db = JFactory::getDbo();
	$sql = "select `seo` from #__guru_config";
	$db->setQuery($sql);
	$db->execute();
	$seo = $db->loadColumn();
	$seo = @$seo["0"];
	
	if(trim($seo) != ""){
		$seo_array = json_decode($seo, true);
		$seo = $seo_array["seo"];
	}
	else{
		$seo = array();
	}
	
	$all_guru_controllers = array("gurupcategs", "guruprograms", "gurutasks", "guruorders", "guruauthor", "guruprofile", "gurulogin", "gurueditplans", "gurubuy");
	
	$segments["0"] = str_replace(":", "-", $segments["0"]);
	
	if(!in_array(mb_strtolower($segments["0"]), $all_guru_controllers)){
		foreach($seo as $key=>$value){
			if($segments["0"] == $value){
				$segments["0"] = $key;
				break;
			}
		}
	}

	// Count route segments
	$count = count($segments);
	if($segments["0"] == "guruPcategs" || $segments["0"] == "gurupcategs"){
		if($segments["0"] == "gurupcategs" && count($segments) == 1){
			$vars['view'] = "gurupcategs";
		}
		else{
			$vars['view'] = "guruPcategs";
			$vars['task'] = "view";
			
			if(isset($segments["1"]) && intval($segments["1"]) != 0){
				//------------------------------------------------
				$check_alias = $segments["1"];
				$check_alias = explode(":", trim($check_alias));

				if(isset($check_alias["1"])){
					$check_alias = $check_alias["1"];
					$sql = "select `id` from #__guru_category where `alias`='".$db->escape($check_alias)."'";
					$db->setQuery($sql);
					$db->execute();
					$check_id = $db->loadColumn();
					$check_id = @$check_id["0"];

					if(intval($check_id) != intval($segments["1"])){
						JError::raiseError(404, "");
						die();
					}
				}
				//------------------------------------------------

				$vars['cid'] = intval($segments["1"]);
			}
			else{
				$db = JFactory::getDBO();
				$sql = "SELECT `params` from #__menu where id=".intval(@$item->id);
				$db->setQuery($sql);
				$db->execute();
				$params = $db->loadColumn();
				$params = @$params["0"];
				$params = json_decode($params);
				
				if(isset($params->cid) && ($params->cid != "")){
					$vars['cid'] = $params->cid;
				}
			}
		}
	}
	
	if($segments["0"] == "guruPrograms" || $segments["0"] == "guruprograms"){
		if($segments["0"] == "guruPrograms"){ // when is comming from course link, from category page, or click on Buy Now
			$vars['view'] = "guruPrograms";

			//------------------------------------------------
			$check_alias = $segments["1"];
			$check_alias = explode(":", trim($check_alias));

			if(isset($check_alias["1"])){
				$check_alias = $check_alias["1"];
				$sql = "select `id` from #__guru_category where `alias`='".$db->escape($check_alias)."'";
				$db->setQuery($sql);
				$db->execute();
				$check_id = $db->loadColumn();
				$check_id = @$check_id["0"];

				if(intval($check_id) != intval($segments["1"])){
					JError::raiseError(404, "");
					die();
				}
			}
			//------------------------------------------------

			//------------------------------------------------
			if(isset($segments["2"])){
				$check_alias = $segments["2"];
				$check_alias = explode(":", trim($check_alias));

				if(isset($check_alias) && isset($check_alias["1"])){
					$check_alias = $check_alias["1"];
					$sql = "select `id` from #__guru_program where `alias`='".$db->escape($check_alias)."'";
					$db->setQuery($sql);
					$db->execute();
					$check_id = $db->loadColumn();
					$check_id = @$check_id["0"];

					if(intval($check_id) != intval($segments["2"])){
						JError::raiseError(404, "");
						die();
					}
				}
			}
			//------------------------------------------------

			$vars['catid'] = @intval($segments["1"]);
			$vars['cid'] = @intval($segments["2"]);
			
			$course_id = JFactory::getApplication()->input->get("course_id", "0");
			if(intval($course_id) == 0){// from course link
				$vars['task'] = 'view';
			}
			else{ //click on Buy Now
				$vars['task'] = 'buy_action';
			}
			
			if(isset($segments["3"])){
				$vars["registered_user"] = intval($segments["3"]);
				$vars['task'] = 'enroll';
			}
			
		}
		elseif($segments["0"] == "guruprograms"){ // when is comming from menu item - Course Layout
			$vars['view'] = "guruPrograms";

			if(isset($segments["1"])){
				//------------------------------------------------
				$check_alias = $segments["1"];
				$check_alias = explode(":", trim($check_alias));

				if(isset($check_alias["1"])){
					$check_alias = $check_alias["1"];
					$sql = "select `id` from #__guru_category where `alias`='".$db->escape($check_alias)."'";
					$db->setQuery($sql);
					$db->execute();
					$check_id = $db->loadColumn();
					$check_id = @$check_id["0"];

					if(intval($check_id) != intval($segments["1"])){
						JError::raiseError(404, "");
						die();
					}
				}
				//------------------------------------------------

				$vars['catid'] = intval($segments["1"]);
			}
			else{
				$vars['catid'] = "0";
			}

			if(isset($segments["2"])){
				//------------------------------------------------
				$check_alias = $segments["2"];
				$check_alias = explode(":", trim($check_alias));

				if(isset($check_alias["1"])){
					$check_alias = $check_alias["1"];
					$sql = "select `id` from #__guru_program where `alias`='".$db->escape($check_alias)."'";
					$db->setQuery($sql);
					$db->execute();
					$check_id = $db->loadColumn();
					$check_id = @$check_id["0"];

					if(intval($check_id) != intval($segments["2"])){
						JError::raiseError(404, "");
						die();
					}
				}
				//------------------------------------------------

				$vars['cid'] = intval($segments["2"]);
			}
			else{
				$vars['cid'] = "0";
			}

			if(isset($segments["3"]) && $segments["3"] == "enroll"){
				$vars['task'] = 'enroll';
			}
			else{
				$vars['layout'] = 'view';
			}
		}
	}
	
	if($segments["0"] == "gurutasks" || $segments["0"] == "guruTasks"){
		if(count($segments) == 5){
			$vars['view'] = $segments["0"];
			$vars['certificate'] = $segments["1"];
			$vars['pdf'] = $segments["2"];
			$vars['ci'] = $segments["3"];
			$vars['dw'] = $segments["4"];
			$vars['task'] = 'viewcertificate';
		}
		elseif(count($segments) == 9){
			$vars['view'] = $segments["0"];
			$vars['course_id'] = intval($segments["1"]);
			$vars['certificate'] = intval($segments["2"]);
			$vars['pdf'] = intval($segments["3"]);
			$vars['dw'] = intval($segments["4"]);
			$vars['ci'] = intval($segments["5"]);
			$vars['prev_lesson_id'] = intval($segments["6"]);
			$vars['module_prev_lesson'] = intval($segments["7"]);
			$vars['task'] = 'viewcertificate';
		}
		else{
			$vars['view'] = "gurutasks";
			$vars['catid'] = intval($segments["1"]);
			$vars['module'] = intval($segments["2"]);
			if(isset($segments["3"])){
				$vars['cid'] = intval($segments["3"]);
			}
			//$vars['task'] = 'view';
			if(!isset($segments["3"])){
				$vars['action'] = 'viewmodule';
			}
		}
	}
	
	if($segments["0"] == "guruorders" ||$segments["0"] == "guruOrders"){
		$vars['view'] = "guruorders";
		
		$app = JFactory::getApplication();
		$menu = $app->getMenu();
		$item = $menu->getActive();
		$task = JFactory::getApplication()->input->get("task", "");
		
		if(isset($item->query["layout"]) && trim($item->query["layout"]) != ""){
			$vars['layout'] = trim($item->query["layout"]);
		}
		
		if(isset($segments["1"]) && intval($segments["1"]) == 0){
			$vars['layout'] = $segments["1"];
		}
		
		if($task == "myorders"){
			$vars['layout'] = "myorders";
		}
		elseif($task == "mycourses"){
			$vars['layout'] = "mycourses";
		}
		
		if(intval(@$segments["1"]) == 1){
			$vars["back"] = 1;
			$vars["task"] = "printcertificate";
		}
		
		if(isset($segments["2"]) && intval($segments["2"]) != 0){
			$vars["course"] = intval($segments["2"]);
		}
	}


	if($segments["0"] == "guruauthor" || $segments["0"] == "guruAuthor"){
		$vars['view'] = "guruauthor";
		$task = JFactory::getApplication()->input->get("task", "");
		
		$app	= JFactory::getApplication();
		$menu	= $app->getMenu();
		$item	= $menu->getActive();

		if(count($segments) == 2 && $segments["1"] == "authorprofile"){
			$vars['layout'] = 'authorprofile';
		}
		elseif(count($segments) == 2 && $segments["1"] == "authorregistration"){
			$vars['layout'] = 'authorregistration';
		}
		elseif(count($segments) == 2 && $segments["1"] == "studentregistration"){
			$vars['layout'] = 'studentregistration';
		}
		elseif(count($segments) == 2 && $segments["1"] == "projects"){
			$vars['layout'] = 'projects';
		}
		elseif(count($segments) == 2 && $segments["1"] == "projectForm"){
			$vars['layout'] = 'projectForm';
		}
		elseif(count($segments) == 3 && $segments["1"] == "projectForm"){
			$vars['layout'] = 'projectForm';
			$vars['id'] = intval($segments["2"]);
		}
		elseif(count($segments) == 3 && $segments["1"] == "projectResult"){
			$vars['task'] = 'projectResult';
			$vars['id'] = intval($segments["2"]);
		}
		elseif(count($segments) == 4 && $segments["1"] == "projectForm" && $segments["2"] == "projectForm"){
			$vars['layout'] = 'projectForm';
			$vars['id'] = intval($segments["3"]);
		}
		elseif((count($segments) == 3 || count($segments) == 2) && $segments["1"] == "authoressays"){
			$vars['layout'] = 'authoressays';
			$vars['task'] = "authoressays";
		}
		elseif(count($segments) == 4 && $segments["1"] == "authoressays"){
			$vars['layout'] = 'authoressays';
			$vars['task'] = "authoressays";
		}
		elseif(count($segments) == 2 && $segments["1"] == "scorm"){
			$vars['layout'] = 'scorm';
		}
		elseif(count($segments) == 2 && $segments["1"] == "scormForm"){
			$vars['layout'] = 'scormForm';
		}
		elseif(count($segments) == 2 && intval($segments["1"]) > 0){
			$vars['layout'] = 'scormForm';
			$vars['id'] = intval($segments["1"]);
		}
		elseif(count($segments) == 3 && $segments["1"] == "scormForm" && intval($segments["2"]) > 0){
			$vars['layout'] = 'scormForm';
			$vars['id'] = intval($segments["2"]);
		}
		else{
			if(intval(@$segments["1"]) == 0){
				if(isset($segments["2"]) && $segments["2"] == "tree"){
					$vars['task'] = "treeCourse";
					$vars['pid'] = intval($segments["1"]);
				}
				elseif(isset($segments["2"]) && $segments["2"] == "info"){
					$vars['task'] = "addCourse";
					$vars['id'] = intval($segments["1"]);
				}
				elseif(isset($segments["2"]) && $segments["2"] == "infom"){
					$vars['task'] = "editMedia";
					$vars['cid'] = intval($segments["1"]);
				}
				elseif(isset($segments["2"]) && $segments["2"] == "infomc"){
					$vars['task'] = "authoraddeditmediacat";
					$vars['id'] = intval($segments["1"]);
				}
				elseif(isset($segments["2"]) && $segments["2"] == "stats"){
					$vars['task'] = "course_stats";
					$vars['id'] = intval($segments["1"]);
				}
				elseif(isset($segments["4"]) && $segments["4"] == "infoq"){
					$vars['task'] = "editQuizFE";
					$vars['cid'] = intval($segments["1"]);
					$vars['v'] = intval($segments["2"]);
					$vars['e'] = intval($segments["3"]);
				}
				elseif(isset($segments["2"]) && $segments["2"] == "infoq"){
					$vars['task'] = "editQuizFE";
					$vars['cid'] = intval($segments["1"]);
					$vars['e'] = "1";
				}
				elseif(isset($segments["3"]) && $segments["3"] == "infoq"){
					$vars['task'] = "editQuizFE";
					$vars['cid'] = intval($segments["1"]);
					$vars['e'] = "1";
				}
				elseif(isset($segments["2"]) && $segments["2"] == "qstats"){
					$vars['task'] = "quizz_stats";
					$vars['id'] = intval($segments["1"]);
				}
				elseif(isset($segments["2"]) && $segments["2"] == "ststats"){
					$vars['task'] = "studentdetails";
					$vars['id'] = intval($segments["1"]);
				}
				elseif(isset($segments["2"]) && $segments["2"] == "mark"){
					$vars['task'] = 'mark';
					$vars['id'] = intval($segments["1"]);
				}
				elseif(isset($segments["1"]) && $segments["1"] == "authormycourses"){
					$vars['layout'] = 'authormycourses';
					if(trim($task) == ""){
						$vars['task'] = 'authormycourses';
					}
					else{
						$vars['task'] = trim($task);
						unset($vars['layout']);
					}
					
					if(isset($segments["2"]) && intval($segments["2"]) != "0"){
						if(strpos($segments["2"], ":") === FALSE){
							$vars['userid'] = intval($segments["2"]);
						}
						else{
							$vars['quiz'] = intval($segments["2"]);
						}
					}
				}
				elseif(isset($segments["1"]) && $segments["1"] == "mystudents"){
					$vars['layout'] = 'mystudents';
					
					if(trim($task) != ""){
						$vars['task'] = trim($task);
						unset($vars['layout']);
					}
					
					if(isset($segments["2"]) && intval($segments["2"]) != 0){
						$vars['cid'] = intval($segments["2"]);
					}
					
					if(count($segments) == 4){
						$vars['action'] = $segments["3"];
					}
					if(isset($segments["2"]) && ($segments["2"] == 'passed' || $segments["2"] == 'failed')){
						$vars['action'] = $segments["2"];
					}
				}
				elseif(isset($segments["1"]) && $segments["1"] == "authorquizzes"){
					$vars['layout'] = 'authorquizzes';
					
					if(trim($task) != ""){
						$vars['task'] = trim($task);
						unset($vars['layout']);
					}
					
					if(isset($segments["3"]) && $segments["3"] == "component"){
						$vars['task'] = "addquizzes";
						unset($vars['layout']);
						$vars['tmpl'] = "component";
					}
				}
				elseif(isset($segments["1"]) && $segments["1"] == "authormymedia"){
					if(isset($segments["2"]) && $segments["2"] == "component"){
						if($segments["3"] == "question" || $segments["3"] == "answers"){
							$vars['page_from'] = $segments["2"];
							unset($vars['page_from']);
						}
					}
					$vars['layout'] = 'authormymedia';
					if(trim($task) != ""){
						$vars['task'] = trim($task);
						unset($vars['layout']);
					}										
				}
				elseif(isset($segments["1"]) && $segments["1"] == "authormymediacategories"){
					$vars['layout'] = 'authormymediacategories';
					
					if(trim($task) != ""){
						$vars['task'] = trim($task);
						unset($vars['layout']);
					}
				}
				elseif(isset($segments["1"]) && $segments["1"] == "authorcommissions"){
					$vars['layout'] = 'authorcommissions';
					
					if(trim($task) != ""){
						$vars['task'] = trim($task);
						unset($vars['layout']);
					}
				}
				elseif(isset($segments["1"]) && $segments["1"] == "newStudent"){
					$vars['task'] = "newStudent";
					$vars['id'] = intval($segments["2"]);
					
					if($task == "save"){
						$vars['layout'] = "mystudents";
					}
					else{
						$vars['layout'] = "newStudent";
					}
				}
				elseif(isset($segments["1"]) && $segments["1"] == "studentdetails"){
					$vars['layout'] = 'studentdetails';
					if(isset($segments["2"])){
						$vars["userid"] = intval($segments["2"]);
					}
					$vars["task"] = "studentdetails";
				}
				elseif(isset($segments["1"]) && $segments["1"] == "studentquizes"){
					$vars['layout'] = 'studentquizes';
					if(isset($segments["2"])){
						$vars["pid"] = intval($segments["2"]);
					}
					
					if(isset($segments["3"])){
						$vars["userid"] = intval($segments["3"]);
					}
					$vars["task"] = "studentquizes";
				}
				elseif(isset($segments["1"]) && $segments["1"] == "quizdetails"){
					$vars['layout'] = 'quizdetails';
					if(isset($segments["2"])){
						$vars["pid"] = intval($segments["2"]);
					}
					
					if(isset($segments["3"])){
						$vars["userid"] = intval($segments["3"]);
					}
					
					if(isset($segments["4"])){
						$vars["quiz"] = intval($segments["4"]);
					}
					$vars["task"] = "quizdetails";
				}
				elseif(isset($segments["1"]) && $segments["1"] == "view"){
					$vars['layout'] = 'view';
					if(isset($segments["2"])){
						$vars["cid"] = intval($segments["2"]);
					}
				}
				elseif(isset($item->query["layout"]) && trim($item->query["layout"]) != ""){// click on menu item - teacher
					$vars['layout'] = 'view';
				}
			}
			else{
				if(isset($segments["2"]) && $segments["2"] == "tree"){
					$vars['task'] = "treeCourse";
					$vars['pid'] = intval($segments["1"]);
				}
				elseif(isset($segments["2"]) && $segments["2"] == "info"){
					$vars['task'] = "addCourse";
					$vars['id'] = intval($segments["1"]);
				}
				elseif(isset($segments["2"]) && $segments["2"] == "infom"){
					$vars['task'] = "editMedia";
					$vars['cid'] = intval($segments["1"]);
				}
				elseif(isset($segments["2"]) && $segments["2"] == "infomc"){
					$vars['task'] = "authoraddeditmediacat";
					$vars['id'] = intval($segments["1"]);
				}
				elseif(isset($segments["2"]) && $segments["2"] == "stats"){
					$vars['task'] = "course_stats";
					$vars['id'] = intval($segments["1"]);
				}
				elseif(isset($segments["4"]) && $segments["4"] == "infoq"){
					$vars['task'] = "editQuizFE";
					$vars['cid'] = intval($segments["1"]);
					$vars['v'] = intval($segments["2"]);
					$vars['e'] = intval($segments["3"]);
				}
				elseif(isset($segments["2"]) && $segments["2"] == "infoq"){
					$vars['task'] = "editQuizFE";
					$vars['cid'] = intval($segments["1"]);
					$vars['e'] = "1";
				}
				elseif(isset($segments["3"]) && $segments["3"] == "infoq"){
					$vars['task'] = "editQuizFE";
					$vars['cid'] = intval($segments["1"]);
					$vars['e'] = "1";
				}
				elseif(isset($segments["2"]) && $segments["2"] == "qstats"){
					$vars['task'] = "quizz_stats";
					$vars['id'] = intval($segments["1"]);
				}
				elseif(isset($segments["2"]) && $segments["2"] == "ststats"){
					$vars['task'] = "studentdetails";
					$vars['id'] = intval($segments["1"]);
				}
				elseif(isset($segments["2"]) && $segments["2"] == "mark"){
					$vars['task'] = 'mark';
					$vars['id'] = intval($segments["1"]);
				}
			}
		}
		
	}
	
	if($segments["0"] == "guruProfile" || $segments["0"] == "guruprofile"){
		$vars['view'] = "guruProfile";
		
		if(count($segments) == 2){ // click on menu item My Quizes and redirect to profile page if not customer
			$vars['task'] = "edit";
			$vars['returnpage'] =@$segments["2"];
		}
	}
	
	if($segments["0"] == "guruLogin" || $segments["0"] == "gurulogin"){	
		$vars['view'] = "guruLogin";
		if(isset($segments["1"])){
			$vars['returnpage'] = @$segments["1"];
		}
		
		if(isset($segments["2"])){
			$vars['cid'] = intval(@$segments["2"]);
		}
	}
	
	if($segments["0"] == "guruEditplans" || $segments["0"] == "gurueditplans"){
		$vars['view'] = "guruEditplans";
		$vars['course_id'] = $segments["1"];
		$vars['tmpl'] = "component";
		if(isset($segments["2"])){
			$vars['action'] = trim($segments["2"]);
		}
	}
	
	if($segments["0"] == "guruBuy" || $segments["0"] == "gurubuy"){
		$vars['view'] = "guruBuy";
	}
	
	if($segments["0"] == "guruSearch" || $segments["0"] == "gurusearch"){
		$vars['view'] = "guruSearch";
		$vars['task'] = "search";
	}
	
	if(isset($vars["view"])){
		$temp = str_replace("guru", "", $vars["view"]);
		$temp = ucfirst($temp);
		$vars["controller"] = "guru".$temp;
	}
	
	$task = JFactory::getApplication()->input->get("task", "");
	
	if(trim($task) != ""){
		$vars['task'] = trim($task);
	}
	
	$session = JFactory::getSession();
	$registry = $session->get('registry');
	$CB = $registry->get('CB', "0");
	$ITEMID = $registry->get('ITEMID', "0");
	
	if(@$segments["0"] != "guruorders" && @$segments["1"] != "mycertificates" && @$segments["1"] != "myorders" && $CB == "1"){
		$itemid = $ITEMID;
		
		if(intval($itemid) != 0){
			$vars['Itemid'] = $itemid;
		}
		
		$registry->set('ITEMID', "0");
		$registry->set('CB', "0");
	}

	if(count($vars) == 0){
		JError::raiseError(404, "");
		die();
	}
	
	return $vars;
}
?>