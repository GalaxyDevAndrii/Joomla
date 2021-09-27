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

class guruAdminModelguruCommissions extends JModelLegacy{
	var $_attributes;
	var $_attribute;
	var $_id = null;
	var $_total = 0;
	var $total = 0;
	var $_pagination = null;
	protected $_context = 'com_guru.guruCommissions';

	function __construct () {
		global $option;
		parent::__construct();
		$cids = JFactory::getApplication()->input->get('cid', 0, "raw");
		$this->setId((int)$cids[0]);
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
			$this->_pagination = new JPagination( $this->_total, $this->getState('limitstart'), $this->getState('limit') );

		}
		return $this->_pagination;
	}
	
	function setId($id) {
		$this->_id = $id;
		$this->_attribute = null;
	}

	function getConfig(){
		$db = JFactory::getDBO();
		$sql="select * from #__guru_config limit 1";
		$db->setQuery($sql);
		$db->execute();
		$result=$db->loadObject();
		return $result;
	}

	function delete(){
		$db = JFactory::getDBO();
		$cids = JFactory::getApplication()->input->get('cid', array(), "raw");
		$return = TRUE;
		
		if(isset($cids) && is_array($cids) && count($cids) > 0){
			foreach($cids as $key=>$id){
				$sql = "select count(0) from #__guru_authors where commission_id=".intval($id);
				$db->setQuery($sql);
				$db->execute();
				$count = $db->loadColumn();
				if($count["0"] == 0){
					$sql = "delete from #__guru_commissions where id=".intval($id);
					$db->setQuery($sql);
					if(!$db->execute()){
						return false;
					}
				}
				else{
					$return = "has teacher";
				}
			}
		}
		return $return;
	}

	function getlistCommissions(){	
		$limitstart=$this->getState('limitstart');
		$limit=$this->getState('limit');
			
		if($limit!=0){
			$limit_cond=" LIMIT ".$limitstart.",".$limit." ";
		} else {
			$limit_cond = NULL;
		}
	
		$db = JFactory::getDBO();
		$sql =  "SELECT count(*) FROM #__guru_commissions";
		$db->setQuery($sql);
		$db->execute();
		$this->_total = $db->loadColumn();
		$this->_total = $this->_total["0"];
	    
		$sql =  "SELECT * FROM #__guru_commissions ".$limit_cond;
					
		$db->setQuery($sql);
		$db->execute();
		$result = $db->loadAssocList();
		return $result;
	}
	
	function setdefault(){
		$id_comm = JFactory::getApplication()->input->get("cid","0", "raw");
		$db = JFactory::getDBO();
		if($id_comm["0"] != "0"){
			$sql = "UPDATE #__guru_commissions set default_commission = 1 where id=".intval($id_comm["0"]);
			$db->setQuery($sql);
			$db->execute();
			
			$sql = "UPDATE #__guru_commissions set default_commission = 0 where id<>".intval($id_comm["0"]);
			$db->setQuery($sql);
			$db->execute();
			
			return true;
		}
		else{
			return false;
		}
		
		
	}	
	
	function save(){
		$item = $this->getTable('guruCommissions');
		$id = JFactory::getApplication()->input->get("id","0");
		$data = JFactory::getApplication()->input->post->getArray();
		$db = JFactory::getDBO();
		if($id !=0){
			$sql = "SELECT count(commission_plan)  FROM #__guru_commissions
			WHERE commission_plan ='".$data['commission_plan']."' and id<> ".intval($id)."";
			$db->setQuery($sql);
			$db->execute();
			$count = $db->loadColumn();
			if($count[0] >0){
				$msg = JText::_('GURU_PROMO_TITLE_EXISTS');
				$app = JFactory::getApplication();
				$app->enqueueMessage($msg, "warning");
				$app->redirect('index.php?option=com_guru&controller=guruCommissions&task=list');
			 
			}
		}
		else{
			$sql = "SELECT count(commission_plan)  FROM #__guru_commissions
			WHERE commission_plan ='".$data['commission_plan']."'";
			$db->setQuery($sql);
			$db->execute();
			$count = $db->loadColumn();
			if($count[0] >0){
				$msg = JText::_('GURU_PROMO_TITLE_EXISTS');
				$app = JFactory::getApplication();
				$app->enqueueMessage($msg, "warning");
				$app->redirect('index.php?option=com_guru&controller=guruCommissions&task=list');
			}
		}

		if(trim($data["id"]) == ""){
			$data["id"] = 0;
		}

		if (!$item->bind($data)){
			JFactory::getApplication()->enqueueMessage($item->getError(), 'error');
			return false;
		}

		if (!$item->check()) {
			JFactory::getApplication()->enqueueMessage($item->getError(), 'error');
			return false;
		}
		
		if (!$item->store()) {
			JFactory::getApplication()->enqueueMessage($item->getError(), 'error');
			return false;
		}

		$sql = "SELECT id  FROM #__guru_commissions
		WHERE  commission_plan ='".$data['commission_plan']."'";
		$db->setQuery($sql);
		$db->execute();
		$id = $db->loadColumn();
		if(isset($id[0]) && $id[0] !="" && $id[0] !=NULL){
			return $id[0];
		}
		else{
			return false;
		}
	}
	
	function getCommissionDetails(){
		if (empty ($this->_package)) {
			$this->_package = $this->getTable("guruCommissions");
			$this->_package->load($this->_id);
			$data = JFactory::getApplication()->input->post->getArray();
			
			if (!$this->_package->bind($data)){
				JFactory::getApplication()->enqueueMessage($this->_package->getError(), 'error');
				return false;
	
			}
	
			if (!$this->_package->check()) {
				JFactory::getApplication()->enqueueMessage($this->_package->getError(), 'error');
				return false;
	
			}
		}
		return $this->_package;
	}
	function getFilters(){
		$app = JFactory::getApplication('administrator');
		@$filter_search = $app->getUserStateFromRequest('search','search','');
		@$filter->search = $filter_search;
		
		return $filter;
	}
	function getAllTeachers(){
		$db = JFactory::getDBO();
		$sql = "select u.id, u.name from #__users u, #__guru_authors a where a.userid=u.id";
		$db->setQuery($sql);
		$db->execute();
		$result = $db->loadAssocList();
		return $result;
	}
	function getTeachersNames(){
		$db = JFactory::getDBO();
		$search = JFactory::getApplication()->input->get("search_text", "");
		$filter_teacher =JFactory::getApplication()->input->get("filter_teacher", "");
		$and = "";
		if(trim($search) != ""){			
			$and .= " and (u.name like '%".addslashes(trim($search))."%' or u.username like '%".addslashes(trim($search))."%' or u.email like '%".addslashes(trim($search))."%')";
		}
		if(intval($filter_teacher) != 0){
			$and .=" and a.author_id=".intval($filter_teacher);
		}
		$sql = "select u.name from #__users u, #__guru_authors_commissions a where a.author_id=u.id ".$and."";
		$db->setQuery($sql);
		$db->execute();
		$result = $db->loadColumn();
		return $result;
	}
	
	function getTeacherName($id){
		$db = JFactory::getDBO();
		$sql = "select u.name from #__users u where u.id=".intval($id);
		$db->setQuery($sql);
		$db->execute();
		$result = $db->loadColumn();
		return $result;
	}
	
	function getTeachersCommissDetails(){
		$db = JFactory::getDBO();
		$sql = "select * from #__guru_authors_commissions where status_payment='paid'";
		$db->setQuery($sql);
		$db->execute();
		$result = $db->loadAssocList();
		return $result;
	}
	
	function getPendingCommissionsToBePaid(){
		$db = JFactory::getDBO();		
		$search = JFactory::getApplication()->input->get("search_text", "");
		$filter_teacher = JFactory::getApplication()->input->get("filter_teacher", "");
		$filter_course = JFactory::getApplication()->input->get("filter_course", "");
		$filter_payment = JFactory::getApplication()->input->get("filter_payment", "0");
		$opt = "";
		$and = "";
		
		$all_users = "Select id from #__users u where (u.name like '%".addslashes(trim($search))."%' or u.username like '%".addslashes(trim($search))."%' or u.email like '%".addslashes(trim($search))."%')";
		$db->setQuery($all_users);
		$db->execute();
		$all_users = $db->loadColumn();
		
		$all_courses = "Select id from #__guru_program u where (u.name like '%".addslashes(trim($search))."%')";
		$db->setQuery($all_courses);
		$db->execute();
		$all_courses = $db->loadColumn();
		
		if(trim($search) != ""){	
			if(isset($all_users) && count($all_users) >0){
				$and .= " and a.author_id IN(".implode(",",$all_users).")";
			}
			else{
				$and .= " and a.author_id IN(0)";
			}
			if(isset($all_courses) && count($all_courses) >0){
				$and .= " OR a.course_id IN(".implode(",",$all_courses).")";
			}
			else{
				$and .= " OR a.course_id IN(0)";
			}		
		}
		if(intval($filter_teacher) != 0){
			$and .=" and  a.author_id=".intval($filter_teacher);
		}
		if(intval($filter_course) != 0){
			$and .=" and  a.course_id=".intval($filter_course);
		}
		if(intval($filter_payment) != 0){
			if(intval($filter_payment) == 1){
				$opt = 0;
			}
			elseif(intval($filter_payment) == 2){
				$opt = 1;
			}
			$all_auth = "Select userid from #__guru_authors where paypal_option = ".$opt;
			$db->setQuery($all_auth);
			$db->execute();
			$all_auth = $db->loadColumn();
			if(isset($all_auth) && count($all_auth) >0){
				$and .= " and a.author_id IN(".implode(",",$all_auth).")";
			}
			else{
				$and .= " and a.author_id IN(0)";
			}
		}
		
		$sql = "select * FROM #__guru_authors_commissions a  where a.status_payment='pending' ".$and;

		$export = JFactory::getApplication()->input->get("export", "");
		
		$db->setQuery($sql);
		$db->execute();
		$result = $db->loadAssocList();
		
		if(isset($result) && count($result) > 0){
			$temp = array();
			foreach($result as $key=>$value){
				
				// author_id-course_id
				if(isset($temp[$value["course_id"]."-".$value["author_id"]."-".$value["currency"]])){
					$temp[$value["course_id"]."-".$value["author_id"]."-".$value["currency"]]["orders"] = $temp[$value["course_id"]."-".$value["author_id"]."-".$value["currency"]]["orders"] + 1;
					$temp[$value["course_id"]."-".$value["author_id"]."-".$value["currency"]]["amount_paid_author"] = $temp[$value["course_id"]."-".$value["author_id"]."-".$value["currency"]]["amount_paid_author"] + $value["amount_paid_author"];
					$temp[$value["course_id"]."-".$value["author_id"]."-".$value["currency"]]["id"] = $value["id"];
					$temp[$value["course_id"]."-".$value["author_id"]."-".$value["currency"]]["currency"] = $value["currency"];

				}
				else{
					$temp[$value["course_id"]."-".$value["author_id"]."-".$value["currency"]]["orders"] = 1;
					$temp[$value["course_id"]."-".$value["author_id"]."-".$value["currency"]]["amount_paid_author"] = $value["amount_paid_author"];
					$temp[$value["course_id"]."-".$value["author_id"]."-".$value["currency"]]["author_id"] = $value["author_id"];
					$temp[$value["course_id"]."-".$value["author_id"]."-".$value["currency"]]["course_id"] = $value["course_id"];
					$temp[$value["course_id"]."-".$value["author_id"]."-".$value["currency"]]["id"] = $value["id"];
					$temp[$value["course_id"]."-".$value["author_id"]."-".$value["currency"]]["currency"] = $value["currency"];
				}
			}
			$result = $temp;
		}
		$result_export = $result;
		$limitstart=$this->getState('limitstart');
		$limit=$this->getState('limit');
		$this->_total = count($result);
		if(count($result)<= $limit && $limitstart == 0){
			//do nothing
		}
		elseif(count($result) > $limit && $limitstart == 0 && $limit == 0){
			//do nothing
		}
		else{
			$result = array_slice($result, $limitstart, $limit);
		}
		
		if($export != ""){
			if($export == 'csv_mass'){
				$cids = JFactory::getApplication()->input->get("cid","", "raw");
				self::exportDetailsMass($cids);
			}
			else{
				self::exportDetails($result_export);
			}
		}
		
		return $result;
	}
	function getPaymentOption($id){
		$db = JFactory::getDBO();	
		$sql = "Select paypal_option from #__guru_authors  where userid=".intval($id);
		$db->setQuery($sql);
		$db->execute();
		$res = $db->loadColumn();	
		return @$res["0"];
	}
	
	function getPaymentPaypalEmail($id){
		$db = JFactory::getDBO();	
		$sql = "Select paypal_email from #__guru_authors  where userid=".intval($id);
		$db->setQuery($sql);
		$db->execute();
		$res = $db->loadColumn();	
		return @$res["0"];
	}
	
	function exportDetailsMass($cids){
		$db = JFactory::getDbo();	
		$temp = array();
		$total = 0;	
		$jnow = new JDate('now');
		$date = $jnow->toSQL();	
		$data = "";
		
		foreach($cids as $key=>$value){
			$cid = explode("-",$value);
			$author_id = $cid["0"];
			$course_id = $cid["1"];
			$id_row = $cid["2"];
			$currency = $cid["3"];
			
			$paypal_email = "Select p.paypal_email from #__guru_authors p where userid=".intval($author_id);
			$db->setQuery($paypal_email);
			$db->execute();
			$paypal_email = $db->loadColumn();	
			$paypal_email = $paypal_email["0"];
			$temp["paypal_email"] = $paypal_email;
			
			$order = "Select id from #__guru_authors_commissions  where author_id=".intval($author_id)." and course_id=".intval($course_id)." and status_payment='pending' and currency='".$currency."'";
			$db->setQuery($order);
			$db->execute();
			$order = $db->loadColumn();	
			
			$amount_paid_author = "Select amount_paid_author from #__guru_authors_commissions where author_id=".intval($author_id)." and course_id=".intval($course_id)." and status_payment ='pending' and currency='".$currency."'";
			$db->setQuery($amount_paid_author);
			$db->execute();
			$amount_paid_author = $db->loadColumn();
			if(count($amount_paid_author)>1){
				for($i = 0; $i<count($amount_paid_author); $i++){
					$total += $amount_paid_author[$i]; 
				}
				$amount_paid_author2 = $total;
				$total = 0;
				
			}
			else{
				$amount_paid_author2 = $amount_paid_author["0"];
			}
			$temp["amount_paid_author"] = number_format($amount_paid_author2,2);
			
			foreach($order as $key=>$valueo){
				$test = "Update #__guru_authors_commissions set status_payment='paid', data ='".$date."', history ='".$id_row."' where id =".$valueo;
				$db->setQuery($test);
				$db->execute();
			}
			
			
			$order = "Select order_id from #__guru_authors_commissions  where author_id=".intval($author_id)." and course_id=".intval($course_id);
			$db->setQuery($order);
			$db->execute();
			$order = $db->loadColumn();	
			$order_id = $order["0"];
			$temp["currency"] = $currency;
			$course_name = self::getCourseName(intval($course_id));
			
			$data .= $temp["paypal_email"].",";
			$data .= '"'.$temp["amount_paid_author"].'",';
			$data .= $temp["currency"].",";
			$data .= ",";
			$data .= JText::_("GURU_COMMISSION_FOR").": ".$course_name["0"]."\n";
		}
		$csv_filename ="masspay_input.csv";
		header("Content-Type: application/x-msdownload");
		header("Content-Disposition: attachment; filename=".$csv_filename);
		header("Pragma: no-cache");
		header("Expires: 0");
		echo utf8_decode($data);
		exit();
		
	}
	
	function make_paid_top(){
		$db = JFactory::getDBO();
		$cids = JFactory::getApplication()->input->get("cid", array(), "raw");
		$cids_boottom = JFactory::getApplication()->input->get("course_second_table", array(), "raw");
		$currency = JFactory::getApplication()->input->get("currency_row","", "raw");
		if(count($cids)>0){
			$cids = $cids;
			$cid = explode("-",$cids["0"]);
		}
		else{
			$cids = $cids_boottom; 
			$cid = explode("-",$cids["0"]);
		}
		$authid = $cid[0];
		
		$jnow = new JDate('now');
		$date_pay = $jnow->toSQL();
		$total = 0;
		
		$count_payments = "Select count_payments from #__guru_authors_commissions_history  where author_id=".intval($authid)." and coin='".$currency."'";
		$db->setQuery($count_payments);
		$db->execute();
		$count_payments = $db->loadColumn();	
		$count_payments = @$count_payments["0"];
		
		foreach($cids as $key=>$value){
			$cid = explode("-",$value);
			$author_id = $cid[0];
			$course_id = $cid[1];
			$id_row = $cid[2];
			$currency = $cid["3"];
			
			$amount_paid_author = "Select sum(amount_paid_author) from #__guru_authors_commissions where author_id=".intval($author_id)." and course_id=".intval($course_id)." and status_payment='pending' and currency='".$currency."'";
			$db->setQuery($amount_paid_author);
			$db->execute();
			$amount_paid_author = $db->loadColumn();
			$total = @$amount_paid_author["0"];
			
			$order = "Select id from #__guru_authors_commissions  where author_id=".intval($author_id)." and course_id=".intval($course_id)." and status_payment='pending' and currency='".$currency."'";
			$db->setQuery($order);
			$db->execute();
			$order = $db->loadColumn();	
			
			$export = JFactory::getApplication()->input->get("export", "");
			if($export != 'csv_mass'){
				foreach($order as $key=>$valueo){
					$test = "Update #__guru_authors_commissions set status_payment='paid', data ='".$date_pay."', history ='".$id_row."' where id =".$valueo;
					$db->setQuery($test);
					$db->execute();
				}
			}
			$count = "Select count(*) from #__guru_authors_commissions_history  where author_id=".intval($author_id);
			$db->setQuery($count);
			$db->execute();
			$count = $db->loadColumn();	
			$count = $count["0"];
			
			$coin = "Select count(*) from #__guru_authors_commissions_history  where author_id=".intval($author_id)." and coin='".$currency."'";
			$db->setQuery($coin);
			$db->execute();
			$coin = $db->loadColumn();	
			$coin = $coin["0"];	
			
			if($count <= 0){
				$sql  = "insert into #__guru_authors_commissions_history (author_id, total, order_auth_ids, data_paid, count_payments, coin) values ";
				$sql .= "(".intval($author_id).", '".$total."', '|".implode("||",$order)."|', '".$date_pay."', ".($count_payments+1).", '".$currency."')";		
	
				$db->setQuery($sql);
				$db->execute();
				$total = 0;
			}
			elseif($count > 0 && $coin <= 0){
				$sql  = "insert into #__guru_authors_commissions_history (author_id, total, order_auth_ids, data_paid, count_payments, coin) values ";
				$sql .= "(".intval($author_id).", '".$total."', '|".implode("||",$order)."|', '".$date_pay."', ".($count_payments+1).", '".$currency."')";		
	
				$db->setQuery($sql);
				$db->execute();
				$total = 0;
			}
			else{
				$total_saved = "Select total from #__guru_authors_commissions_history  where author_id=".intval($author_id)." and coin = '".$currency."'";
				$db->setQuery($total_saved);
				$db->execute();
				$total_saved = $db->loadColumn();
				$total_saved = $total_saved["0"];
				$total_u = $total_saved + $total;
				
				$sql  = 'UPDATE #__guru_authors_commissions_history  set total="'.$total_u.'" , order_auth_ids= CONCAT(order_auth_ids, "|'.implode("||",$order).'|"), data_paid="'.$date_pay.'", count_payments ="'.($count_payments+1).'" where author_id='.intval($author_id)." and coin = '".$currency."'";
				$db->setQuery($sql);
				$db->execute();
				$total = 0;
			}
		}
	
	}
	
	function make_paid(){
		$db = JFactory::getDBO();
		$cids = JFactory::getApplication()->input->get("cid", array(), "raw");
		$cids_boottom = JFactory::getApplication()->input->get("course_second_table", array(), "raw");
		$currency = JFactory::getApplication()->input->get("currency_row","", "raw");
		if(count($cids)>0){
			$cids = $cids;
			$cid = explode("-",$cids["0"]);
		}
		else{
			$cids = $cids_boottom; 
			$cid = explode("-",$cids["0"]);
		}
		$authid = $cid[0];
		
		$jnow = new JDate('now');
		$date_pay = $jnow->toSQL();
		$total = array();
		
		$paypal_cids = array();
		$not_paypal_account = false;
		
		$count_payments = "Select count_payments from #__guru_authors_commissions_history  where author_id=".intval($authid);
		$db->setQuery($count_payments);
		$db->execute();
		$count_payments = $db->loadColumn();	
		$count_payments = @$count_payments["0"];

		foreach($cids as $key=>$value){
			$cid = explode("-",$value);
			$author_id = $cid["0"];
			$course_id = $cid["1"];
			$id_row = $cid["2"];
			$currency = $cid["3"];
			
			$sql = "select paypal_email from #__guru_authors where userid=".intval($author_id)." and paypal_option=0";
			$db->setQuery($sql);
			$db->execute();
			$paypal_email = $db->loadColumn();
			$paypal_email = @$paypal_email["0"];
			if(trim($paypal_email) == ""){
				$not_paypal_account = true;
				continue;
			}
			else{
				$paypal_cids[] = $value;
				
			}
			$amount_paid_author = "Select sum(amount_paid_author) from #__guru_authors_commissions where author_id=".intval($author_id)." and course_id=".intval($course_id)." and status_payment='pending' and currency='".$currency."'";
			$db->setQuery($amount_paid_author);
			$db->execute();
			$amount_paid_author = $db->loadColumn();
			$total = @$amount_paid_author["0"];
			
			$order = "Select id from #__guru_authors_commissions where author_id=".intval($author_id)." and course_id=".intval($course_id)." and status_payment='pending' and currency='".$currency."'";
			$db->setQuery($order);
			$db->execute();
			$order = $db->loadColumn();
			
			$export = JFactory::getApplication()->input->get("export", "");
			if($export != 'csv_mass'){
				foreach($order as $keyo=>$valueo){
					$test = "Update #__guru_authors_commissions set status_payment='paid', data ='".$date_pay."', history ='".$id_row."' where id =".$valueo;
					$db->setQuery($test);
					$db->execute();
				}
			}
			
			$count = "Select count(*) from #__guru_authors_commissions_history where author_id=".intval($author_id);
			$db->setQuery($count);
			$db->execute();
			$count = $db->loadColumn();	
			$count = $count["0"];
			
			$coin = "Select count(*) from #__guru_authors_commissions_history  where author_id=".intval($author_id)." and coin='".$currency."'";
			$db->setQuery($coin);
			$db->execute();
			$coin = $db->loadColumn();	
			$coin = $coin["0"];	
			
			if($count <= 0){
				$sql  = "insert into #__guru_authors_commissions_history (author_id, total, order_auth_ids, data_paid, count_payments,coin) values ";
				$sql .= "(".intval($author_id).", '".$total."', '|".implode("||",$order)."|', '".$date_pay."', ".($count_payments+1).", '".$currency."')";
				$db->setQuery($sql);
				$db->execute();
			}
			elseif($count > 0 && $coin <= 0){
				$sql  = "insert into #__guru_authors_commissions_history (author_id, total, order_auth_ids, data_paid, count_payments,coin) values ";
				$sql .= "(".intval($author_id).", '".$total."', '|".implode("||",$order)."|', '".$date_pay."', ".($count_payments+1).", '".$currency."')";
	
				$db->setQuery($sql);
				$db->execute();
			}
			else{
				$total_saved = "Select total from #__guru_authors_commissions_history  where author_id=".intval($author_id)." and coin = '".$currency."'";
				$db->setQuery($total_saved);
				$db->execute();
				$total_saved = $db->loadColumn();
				$total_saved = $total_saved["0"];
				$total_u = $total_saved + $total;
				
				$sql  = 'UPDATE #__guru_authors_commissions_history  set  total="'.$total_u.'" , order_auth_ids= CONCAT(order_auth_ids, "|'.implode("||",$order).'|"), data_paid="'.$date_pay.'", count_payments ="'.($count_payments+1).'" where author_id='.intval($author_id)." and coin = '".$currency."'";
				$db->setQuery($sql);
				$db->execute();
			}
		}
		
		$export = JFactory::getApplication()->input->get("export", "");	
		if($export != ""){
			if($export == 'csv_mass'){
				if(count($paypal_cids) > 0){
					self::exportDetailsMass($paypal_cids);
				}
			}
		}	
	}
	function make_paid_paypal(){
		$db = JFactory::getDBO();
		$custom = JFactory::getApplication()->input->get("custom", "");
		$currency = JFactory::getApplication()->input->get("mc_currency", "");
		$custom2 = explode("-",$custom);
		$jnow = new JDate('now');
		$date_pay = $jnow->toSQL();
		$total = array();
		$author_id = $custom2["0"];
		
		$count_payments = "Select count_payments from #__guru_authors_commissions_history  where author_id=".intval($custom2["0"]);
		$db->setQuery($count_payments);
		$db->execute();
		$count_payments = $db->loadColumn();	
		$count_payments = $count_payments["0"];

		$order = "Select id from #__guru_authors_commissions where author_id=".intval($custom2["0"])." and course_id=".intval($custom2["1"])." and status_payment='pending' and currency='".$currency."'";
		$db->setQuery($order);
		$db->execute();
		$order = $db->loadColumn();
		
		$amount_paid_author = "Select sum(amount_paid_author) from #__guru_authors_commissions where author_id=".intval($author_id)." and course_id=".intval($custom2["1"])." and status_payment='pending' and currency='".$currency."'";
		$db->setQuery($amount_paid_author);
		$db->execute();
		$amount_paid_author = $db->loadColumn();
		$total = @$amount_paid_author["0"];
		
		foreach($order as $keyo=>$valueo){
			$test = "Update #__guru_authors_commissions set status_payment='paid', data ='".$date_pay."', history ='".$custom2["2"]."' where id =".$valueo;
			$db->setQuery($test);
			$db->execute();
		}
		
		$count = "Select count(*) from #__guru_authors_commissions_history where author_id=".intval($author_id);
			$db->setQuery($count);
			$db->execute();
			$count = $db->loadColumn();	
			$count = $count["0"];
			
			$coin = "Select count(*) from #__guru_authors_commissions_history  where author_id=".intval($author_id)." and coin='".$currency."'";
			$db->setQuery($coin);
			$db->execute();
			$coin = $db->loadColumn();	
			$coin = $coin["0"];	
			
			if($count <= 0){
				$sql  = "insert into #__guru_authors_commissions_history (author_id, total, order_auth_ids, data_paid, count_payments,coin) values ";
				$sql .= "(".intval($author_id).", '".$total."', '|".implode("||",$order)."|', '".$date_pay."', ".($count_payments+1).", '".$currency."')";
				$db->setQuery($sql);
				$db->execute();
			}
			elseif($count > 0 && $coin <= 0){
				$sql  = "insert into #__guru_authors_commissions_history (author_id, total, order_auth_ids, data_paid, count_payments,coin) values ";
				$sql .= "(".intval($author_id).", '".$total."', '|".implode("||",$order)."|', '".$date_pay."', ".($count_payments+1).", '".$currency."')";
	
				$db->setQuery($sql);
				$db->execute();
			}
			else{
				$total_saved = "Select total from #__guru_authors_commissions_history  where author_id=".intval($author_id)." and coin = '".$currency."'";
				$db->setQuery($total_saved);
				$db->execute();
				$total_saved = $db->loadColumn();
				$total_saved = $total_saved["0"];
				$total_u = $total_saved + $total;
				
				$sql  = 'UPDATE #__guru_authors_commissions_history  set  total="'.$total_u.'" , order_auth_ids= CONCAT(order_auth_ids, "|'.implode("||",$order).'|"), data_paid="'.$date_pay.'", count_payments ="'.($count_payments+1).'" where author_id='.intval($author_id)." and coin = '".$currency."'";
				$db->setQuery($sql);
				$db->execute();
			}		
	}
	
	function getPendingCommissionsPaidTotal(){
		$db = JFactory::getDBO();		
		$search = JFactory::getApplication()->input->get("search_text", "");
		
		$all_users = "Select id from #__users u where (u.name like '%".addslashes(trim($search))."%' or u.username like '%".addslashes(trim($search))."%' or u.email like '%".addslashes(trim($search))."%')";
		$db->setQuery($all_users);
		$db->execute();
		$all_users = $db->loadColumn();
		$and = "";

		if(trim($search) != ""){	
			if(isset($all_users) && count($all_users) >0){
				$all_users = implode(",",$all_users);
			}
			else{
				$all_users = "0";
			}
			$and .= " where author_id IN(".$all_users.")";
		}
		
		$sql = "select * from #__guru_authors_commissions_history  ".$and."";
		$db->setQuery($sql);
		$db->execute();
		$result = $db->loadAssocList();
		$result_export = $result;
		$limitstart=$this->getState('limitstart');
		$limit=$this->getState('limit');
		$this->_total = count($result);
		
		if(count($result)<= $limit && $limitstart == 0){
			//do nothing
		}
		elseif(count($result) > $limit && $limitstart == 0 && $limit == 0){
			//do nothing
		}
		else{
			$result = array_slice($result, $limitstart, $limit);
		}
		$export = JFactory::getApplication()->input->get("export", "");
		if($export != ""){
			self::exportDetails($result_export);
		}
		return $result;
	}
	
	
	function getPendingCommissionsPaid(){
		$db = JFactory::getDBO();		
		$search = JFactory::getApplication()->input->get("search_text", "");
		$filter_teacher = JFactory::getApplication()->input->get("filter_teacher", "");
		$and = "";
		$all_users = "Select id from #__users u where (u.name like '%".addslashes(trim($search))."%' or u.username like '%".addslashes(trim($search))."%' or u.email like '%".addslashes(trim($search))."%')";
		$db->setQuery($all_users);
		$db->execute();
		$all_users = $db->loadColumn();
		
		$all_courses = "Select id from #__guru_program u where (u.name like '%".addslashes(trim($search))."%')";
		$db->setQuery($all_courses);
		$db->execute();
		$all_courses = $db->loadColumn();
		
		if(trim($search) != ""){	
			if(isset($all_users) && count($all_users) >0){
				$and .= " and a.author_id IN(".implode(",",$all_users).")";
			}
			else{
				$and .= " and a.author_id IN(0)";
			}
			if(isset($all_courses) && count($all_courses) >0){
				$and .= " OR a.course_id IN(".implode(",",$all_courses).")";
			}	
			else{
				$and .= " OR a.course_id IN(0)";
			}	
		}
		if(intval($filter_teacher) != 0){
			$and .=" and  a.author_id=".intval($filter_teacher);
		}
				
		$sql = "select * from #__guru_authors_commissions a  where a.status_payment='paid' ".$and."";
		$db->setQuery($sql);
		$db->execute();
		$result = $db->loadAssocList();

		if(isset($result) && count($result) > 0){
			$temp = array();
			foreach($result as $key=>$value){
				// course_id-author_id
				if(isset($temp[$value["course_id"]."-".$value["author_id"]."-".$value["history"]."-".$value["currency"]])){
					$temp[$value["course_id"]."-".$value["author_id"]."-".$value["history"]."-".$value["currency"]]["amount_paid_author"] = $temp[$value["course_id"]."-".$value["author_id"]."-".$value["history"]."-".$value["currency"]]["amount_paid_author"] + $value["amount_paid_author"];
					$temp[$value["course_id"]."-".$value["author_id"]."-".$value["history"]."-".$value["currency"]]["course_id"] = $value["course_id"];
					$temp[$value["course_id"]."-".$value["author_id"]."-".$value["history"]."-".$value["currency"]]["author_id"] = $value["author_id"];
					$temp[$value["course_id"]."-".$value["author_id"]."-".$value["history"]."-".$value["currency"]]["id"] = $value["id"];
					$temp[$value["course_id"]."-".$value["author_id"]."-".$value["history"]."-".$value["currency"]]["data"] = $value["data"];
					$temp[$value["course_id"]."-".$value["author_id"]."-".$value["history"]."-".$value["currency"]]["currency"] = $value["currency"];
				}
				else{
					$temp[$value["course_id"]."-".$value["author_id"]."-".$value["history"]."-".$value["currency"]]["amount_paid_author"] = $value["amount_paid_author"];
					$temp[$value["course_id"]."-".$value["author_id"]."-".$value["history"]."-".$value["currency"]]["author_id"] = $value["author_id"];
					$temp[$value["course_id"]."-".$value["author_id"]."-".$value["history"]."-".$value["currency"]]["course_id"] = $value["course_id"];
					$temp[$value["course_id"]."-".$value["author_id"]."-".$value["history"]."-".$value["currency"]]["id"] = $value["id"];
					$temp[$value["course_id"]."-".$value["author_id"]."-".$value["history"]."-".$value["currency"]]["data"] = $value["data"];
					$temp[$value["course_id"]."-".$value["author_id"]."-".$value["history"]."-".$value["currency"]]["currency"] = $value["currency"];
				}
			}
			$result = $temp;
		}
		$result_export = $result;
		$limitstart=$this->getState('limitstart');
		$limit=$this->getState('limit');
		$this->_total = count($result);
		if(count($result)<= $limit && $limitstart == 0){
			//do nothing
		}
		elseif(count($result) > $limit && $limitstart == 0 && $limit == 0){
			// do nothing
		}
		else{
			$result = array_slice($result, $limitstart, $limit);
		}
		
		$export = JFactory::getApplication()->input->get("export", "");
		if($export != ""){
			self::exportDetails($result_export);
		}
		
		return $result;
	}
	
	function getAllCourses(){
		$db = JFactory::getDBO();
		$sql = "select  u.id, u.name from #__guru_program u";
		$db->setQuery($sql);
		$db->execute();
		$result = $db->loadAssocList();
		return $result;
	}
	
	function getTeacherDetails(){
		$db = JFactory::getDBO();
		$cid = JFactory::getApplication()->input->get("cid", "0", "raw");
		$sql = "select u.name from #__users u where u.id =".intval($cid["0"]);
		$db->setQuery($sql);
		$db->execute();
		$result = $db->loadColumn();
		return $result["0"];
	}
	function getCourseName($id){
		$db = JFactory::getDBO();
		$sql = "select u.name from #__guru_program u where u.id=".intval($id);
		$db->setQuery($sql);
		$db->execute();
		$result = $db->loadColumn();
		return $result;	
	}
	function countOrders($course_id, $author_id, $three, $currency){
		$db = JFactory::getDBO();
		$sql = "select count(id) from #__guru_authors_commissions  where course_id=".intval($course_id)." and author_id=".intval($author_id)." and status_payment = 'pending' and currency='".$currency."'";
		$db->setQuery($sql);
		$db->execute();
		$result = $db->loadColumn();
		return $result["0"];	
	}
	function sumToPay($course_id, $author_id){
		$db = JFactory::getDBO();
		$sql = "select SUM(amount_paid_author) from #__guru_authors_commissions  where course_id=".intval($course_id)." and author_id=".intval($author_id)." and status_payment = 'paid'";
		$db->setQuery($sql);
		$db->execute();
		$result = $db->loadColumn();
		return $result["0"];
	}
	
	
	function getPendingDetails($course_id, $author_id, $pageto){
		$search = JFactory::getApplication()->input->get("search_text", "");
		$filter_commission_plan = JFactory::getApplication()->input->get("filter_commission_plan", "");
		$filter_paymentmethod = JFactory::getApplication()->input->get("filter_paymentmethod", "");
		$filter_promocode = JFactory::getApplication()->input->get("filter_promocode", "");
		$id = JFactory::getApplication()->input->get("id", "0");
		$block = JFactory::getApplication()->input->get("block", "0");
		$pagefrom = JFactory::getApplication()->input->get("page","");
		$currency = JFactory::getApplication()->input->get("currency","");
		$and = "";
		$db = JFactory::getDBO();
		$all_users = "Select id from #__users u where (u.name like '%".addslashes(trim($search))."%' or u.username like '%".addslashes(trim($search))."%' or u.email like '%".addslashes(trim($search))."%')";
		$db->setQuery($all_users);
		$db->execute();
		$all_users = $db->loadColumn();
		
		if(intval($filter_commission_plan) != 0){
			$and .=" and  commission_id=".intval($filter_commission_plan);
		}
		if($filter_paymentmethod != ""){
			$and .=" and  payment_method='".$filter_paymentmethod."'";
		}
		if(intval($filter_promocode) != 0){
			$and .=" and  promocode_id=".intval($filter_promocode);
		}		
		if(trim($search) != ""){	
			if(isset($all_users) && count($all_users) >0){
				$and .= " and customer_id IN(".implode(",",$all_users).")";
			}
			else{
				$and .= " and customer_id IN(0)";
			}		
		}
		if($pagefrom == 'history'){
			$sql = "select * from #__guru_authors_commissions  where status_payment='".$pageto."' and course_id=".intval($course_id)." and author_id=".intval($author_id)." and id=".intval($id)." and currency='".$currency."' ".$and." ";
		}
		else{
			if($pagefrom == 'pending'){
				$sql = "select * from #__guru_authors_commissions  where status_payment='".$pageto."' and course_id=".intval($course_id)." and author_id=".intval($author_id)." and currency='".$currency."'"." ".$and." ";
			}
			else{
				$sql = "select * from #__guru_authors_commissions  where status_payment='".$pageto."' and course_id=".intval($course_id)." and author_id=".intval($author_id)." and history =".$block."  ".$and." ";
			}
		}
		$db->setQuery($sql);
		$db->execute();
		$result = $db->loadAssocList();
		
		$export = JFactory::getApplication()->input->get("export", "");
		if($export != ""){
			self::exportDetails($result);
		}

		$limitstart=$this->getState('limitstart');
		$limit=$this->getState('limit');
		$this->_total = count($result);
		if(count($result)<= $limit && $limitstart == 0){
			//do nothing
		}
		elseif(count($result) > $limit && $limitstart == 0 && $limit == 0){
			//do nothing
		}
		else{
			$result = array_slice($result, $limitstart, $limit);
		}
		
		return $result;
	}
	function getPaidDetails($author_id, $orders, $currencyc){
		$orders = str_replace("||", ",", $orders);
		$orders = str_replace("|", "", $orders);
		$orders = array_filter(explode(",", $orders));
		$orders = implode(",",$orders);
		$db = JFactory::getDBO();
		$sql = "select * from #__guru_authors_commissions  where status_payment='paid' and author_id=".intval($author_id)." and currency='".$currencyc."'";
		
		$db->setQuery($sql);
		$db->execute();
		$result = $db->loadAssocList();
		
		if(isset($result) && count($result) > 0){
			$temp = array();
			foreach($result as $key=>$value){
				// author_id-course_id
				if(isset($temp[$value["course_id"]."-".$value["author_id"]."-".$value["history"]."-".$value["currency"]])){
					$temp[$value["course_id"]."-".$value["author_id"]."-".$value["history"]."-".$value["currency"]]["amount_paid_author"] = $temp[$value["course_id"]."-".$value["author_id"]."-".$value["history"]."-".$value["currency"]]["amount_paid_author"] + $value["amount_paid_author"];
					$temp[$value["course_id"]."-".$value["author_id"]."-".$value["history"]."-".$value["currency"]]["course_id"] = $value["course_id"];
					$temp[$value["course_id"]."-".$value["author_id"]."-".$value["history"]."-".$value["currency"]]["author_id"] = $value["author_id"];
					$temp[$value["course_id"]."-".$value["author_id"]."-".$value["history"]."-".$value["currency"]]["id"] = $value["id"];
					$temp[$value["course_id"]."-".$value["author_id"]."-".$value["history"]."-".$value["currency"]]["data"] = $value["data"];
					$temp[$value["course_id"]."-".$value["author_id"]."-".$value["history"]."-".$value["currency"]]["currency"] = $value["currency"];

				}
				else{
					$temp[$value["course_id"]."-".$value["author_id"]."-".$value["history"]."-".$value["currency"]]["amount_paid_author"] = $value["amount_paid_author"];
					$temp[$value["course_id"]."-".$value["author_id"]."-".$value["history"]."-".$value["currency"]]["author_id"] = $value["author_id"];
					$temp[$value["course_id"]."-".$value["author_id"]."-".$value["history"]."-".$value["currency"]]["course_id"] = $value["course_id"];
					$temp[$value["course_id"]."-".$value["author_id"]."-".$value["history"]."-".$value["currency"]]["id"] = $value["id"];
					$temp[$value["course_id"]."-".$value["author_id"]."-".$value["history"]."-".$value["currency"]]["data"] = $value["data"];
					$temp[$value["course_id"]."-".$value["author_id"]."-".$value["history"]."-".$value["currency"]]["currency"] = $value["currency"];

				}
			}
			$result = $temp;
		}

		$export = JFactory::getApplication()->input->get("export", "");
		if($export != ""){
			self::exportDetails($result);
		}
		
		$limitstart=$this->getState('limitstart');
		$limit=$this->getState('limit');
		$this->_total = count($result);
		if(count($result)<= $limit && $limitstart == 0){
			//do nothing
		}
		elseif(count($result) > $limit && $limitstart == 0 && $limit == 0){
			//do nothing
		}
		else{
			$result = array_slice($result, $limitstart, $limit);
		}
		
		return $result;
	}
	function getStudentName($id){
		$db = JFactory::getDBO();
		$sql = "select u.name from #__users u where u.id=".intval($id);
		$db->setQuery($sql);
		$db->execute();
		$result = $db->loadColumn();
		return $result;	
	}
	function getCommissionsDetails($id){
		$db = JFactory::getDBO();
		$sql = "select commission_plan, teacher_earnings from #__guru_commissions where id=".intval($id);
		$db->setQuery($sql);
		$db->execute();
		$result = $db->loadAssocList();
		return $result;		
	}
	function getAllPromos(){
		$db = JFactory::getDBO();
		$sql = "select  u.id, u.code from #__guru_promos u";
		$db->setQuery($sql);
		$db->execute();
		$result = $db->loadAssocList();
		return $result;
	}
	function getAllCommissions(){
		$db = JFactory::getDBO();
		$sql = "select  u.id, u.commission_plan from #__guru_commissions u";
		$db->setQuery($sql);
		$db->execute();
		$result = $db->loadAssocList();
		return $result;
	}
	function getUsers(){
		$db = JFactory::getDBO();
		$sql = "select u.id, u.name, u.username from #__users u, #__guru_customer g where u.id=g.id";
		$db->setQuery($sql);
		$db->execute();
		$result = $db->loadAssocList("id");
		return $result;
	}
	function getDateFormat($data){
		$config = self::getConfig();
		$datetype  = $config->datetype;
		if($config->hour_format == 12){
		$format = " Y-m-d h:i:s A ";
			switch($datetype){
				case "d-m-Y H:i:s": $format = "d-m-Y h:i:s A";
					  break;
				case "d/m/Y H:i:s": $format = "d/m/Y h:i:s A"; 
					  break;
				case "d.m.Y H:i": $format = "d.m.Y h:i A"; 
					  break;
				case "m-d-Y H:i:s": $format = "m-d-Y h:i:s A"; 
					  break;
				case "m/d/Y H:i:s": $format = "m/d/Y h:i:s A"; 
					  break;
				case "Y-m-d H:i:s": $format = "Y-m-d h:i:s A"; 
					  break;
				case "Y/m/d H:i:s": $format = "Y/m/d h:i:s A"; 
					  break;
				case "d-m-Y": $format = "d-m-Y"; 
					  break;
				case "d/m/Y": $format = "d/m/Y"; 
					  break;
				case "m-d-Y": $format = "m-d-Y"; 
					  break;
				case "m/d/Y": $format = "m/d/Y"; 
					  break;
				case "Y-m-d": $format = "Y-m-d"; 
					  break;
				case "Y/m/d": $format = "Y/m/d";	
					  break;	  	  	  	  	  	  	  	  	  	  
			}
			$date_int = strtotime($data);
			$date_string = JHTML::_('date', $date_int, $format );
		}
		else{
			$date_int = strtotime($data);
			//$date_string = date("Y-m-d H:i:s", $date_int);
			$format = "Y-m-d H:M:S";
			switch($datetype){
				case "d-m-Y H:i:s": $format = "d-m-Y H:i:s";
					  break;
				case "d.m.Y H:i": $format = "d.m.Y H:i"; 
					  break;
				case "d/m/Y H:i:s": $format = "d/m/Y H:i:s"; 
					  break;
				case "m-d-Y H:i:s": $format = "m-d-Y H:i:s"; 
					  break;
				case "m/d/Y H:i:s": $format = "m/d/Y H:i:s"; 
					  break;
				case "Y-m-d H:i:s": $format = "Y-m-d H:i:s"; 
					  break;
				case "Y/m/d H:i:s": $format = "Y/m/d H:i:s"; 
					  break;
				case "d-m-Y": $format = "d-m-Y"; 
					  break;
				case "d/m/Y": $format = "d/m/Y"; 
					  break;
				case "m-d-Y": $format = "m-d-Y"; 
					  break;
				case "m/d/Y": $format = "m/d/Y"; 
					  break;
				case "Y-m-d": $format = "Y-m-d"; 
					  break;
				case "Y/m/d": $format = "Y/m/d";		
					  break;  	  	  	  	  	  	  	  	  	  
			}
			$date_string = JHTML::_('date', $date_int, $format);
		}	
		return $date_string;
	}
	
	function getPromoDetails($promo_id, $total, $currency){
		$db = JFactory::getDBO();
		$sql = "select * from #__guru_promos where id='".intval($promo_id)."'";
		$db->setQuery($sql);
		$db->execute();
		$promo = $db->loadObjectList();			
		$promo_details = @$promo["0"];
		@$config = self::getConfig();
		$currencypos = $config->currencypos;
		$character = "GURU_CURRENCY_".$currency;
		if($promo_id == 0){
			$value_to_display = "-";
		}
		else{
			if($promo_details->typediscount == '0') {//use absolute values		
				 $percent =($promo_details->discount*100)/$total;	
				 $percent = number_format($percent, 2);
	
				 if($currencypos == 0){
				 	$value_to_display = $promo_details->title." (".round($percent,1)."% / ".JText::_($character).$promo_details->discount.")";
				 }
				 else{
				 	$value_to_display = $promo_details->title." (".round($percent,1)."% / ".$promo_details->discount.JText::_($character).")";
				 }		
			}
			else{//use percentage
				$percent = ($total *$promo_details->discount)/100;
				$percent = number_format($percent, 2);

				if($currencypos == 0){
					$value_to_display = $promo_details->title." (".$promo_details->discount."% / ".JText::_($character).$percent.")";
				}
				else{
					$value_to_display = $promo_details->title." (".$promo_details->discount."% / ".$percent.JText::_($character).")";
				}		
			}
		}
		return $value_to_display;
	}
	function getPromoDetailsT($promo_id, $total){
		$db = JFactory::getDBO();
		$sql = "select * from #__guru_promos where id='".intval($promo_id)."'";
		$db->setQuery($sql);
		$db->execute();
		$promo = $db->loadObjectList();			
		$promo_details = $promo["0"];
		$config = self::getConfig();
		$currencypos = $config->currencypos;
		if($promo_id == 0){
			$percent = 0;
		}
		else{
			if($promo_details->typediscount == '0') {//use absolute values		
				 $percent =($promo_details->discount*100)/$total;	
			}
			else{//use percentage
				$percent = ($total *$promo_details->discount)/100;
			}
		}
		return $percent;
	}
	
	function exportDetails($result){
		$db = JFactory::getDBO();
		$export = JFactory::getApplication()->input->get("export", "", "raw");
		$task = JFactory::getApplication()->input->get("task", "", "raw");
		$page = JFactory::getApplication()->input->get("page", "", "raw");
		$cids = JFactory::getApplication()->input->get("cid","0", "raw");

		$config = self::getConfig();
		$currencypos = $config->currencypos;
		
		$jnow = new JDate('now');
		$current_date = $jnow->toSQL();
		$current_date = self::getDateFormat($current_date);
		
		$auth_name_top = self::getTeacherName($cids["0"]);
		
		if($export == "csv"){
			$data = "";
			if( $task =="paid"){
				$header1 = array("".JText::_("GURU_DETAILS_CSV1")."","".$current_date."");
				$header = array("".JText::_('GURU_AUTHOR')."", "".JText::_('GURU_TOTAL_PAID_COMM')."", "".JText::_('GURU_NB_PAYMENTS')."", "".JText::_('GURU_LAST_PAYMENT_DATE')."");
			}
			elseif($task =="pending"){
				$header1 = array("".JText::_("GURU_DETAILS_CSV2")."","".$current_date."");
				$header = array("".JText::_('GURU_AUTHOR')."","".JText::_('GURU_COURSE')."(s)"."","".JText::_('GURU_TREEORDERS')."","".JText::_('GURU_COMM_PENDING')."");

			}
			elseif($task =="details" && $page =="paid"){
				$header1 = array("".JText::_("GURU_DETAILS_CSV3")."","".$auth_name_top["0"]."","".$current_date."");
				$header = array("#","".JText::_('GURU_ID')."","".JText::_('GURU_DATE')."","".JText::_('VIEWORDERSAMOUNTPAID')."");
				

			}
			elseif($task =="details" && ($page =="pending"|| $page =="history")){
				$header1 = array("".JText::_("GURU_DETAILS_CSV4")."","".$auth_name_top["0"]."","".$current_date."");
				$header = array("#","".JText::_('GURU_ID')."","".JText::_('GURU_DATE')."","".JText::_('GURU_PRICE')."","".JText::_('GURU_O_PAID')."","".JText::_('GURU_TREECUSTOMERS')."","".JText::_('GURU_ORDPAYMENTMETHOD')."","".JText::_('GURU_PROMOCODE')." (% / ".JText::_('GURU_VALUE').")","".JText::_('GURU_COMMISSION_PLAN')."","".JText::_('GURU_COMMISSIONS')."");

			}
			elseif($task =="details" && $page =="history1"){
				$header1 = array("".JText::_("GURU_DETAILS_CSV5")."","".$auth_name_top["0"]."","".$current_date."");
				$header = array("#","".JText::_('GURU_ID')."","".JText::_('GURU_DATE')."","".JText::_('GURU_PRICE')."","".JText::_('GURU_O_PAID')."","".JText::_('GURU_TREECUSTOMERS')."","".JText::_('GURU_ORDPAYMENTMETHOD')."","".JText::_('GURU_PROMOCODE')." (% / ".JText::_('GURU_VALUE').")","".JText::_('GURU_COMMISSION_PLAN')."","".JText::_('GURU_COMMISSIONS')."");

			}
			elseif($task =="history"){
				$header1 = array("".JText::_("GURU_DETAILS_CSV6")."","".$current_date."");
				$header = array("".JText::_('GURU_AUTHOR')."","".JText::_('VIEWORDERSAMOUNTPAID')."","".JText::_('GURU_PAYMENT_DATE')."");
			}
			
			$data = implode(",", $header1);	
			$data .= "\n\n";
			$data .= implode(",", $header);
			$data .= "\n";
			$inc = 1;
			$total_sum_per_currency = array();
			if(isset($result) && count($result) > 0){
				foreach($result as $key=>$value){
					$auth_name = self::getTeacherName($value["author_id"]);
					@$course_name = self::getCourseName($value["course_id"]);
					@$count_orders = self::countOrders($value["course_id"], $value["author_id"], $three=1,$value["currency"] );
					@$amount_pending = self::sumToPay($value["course_id"], $value["author_id"]); 
					@$student_name = self::getStudentName($value["customer_id"]); 
					@$commission_plan = self::getCommissionsDetails($value["commission_id"]);
					@$promo_name = self::getPromoDetails($value["promocode_id"],$value["price"], $value["currency"]);
					@$sum_price += $value["price"];
					@$sum_price_paid += $value["price_paid"];
					@$promo_for_calc = self::getPromoDetailsT($value["promocode_id"],$value["price"]);
					@$sum_promo += $promo_for_calc;
					$character = "GURU_CURRENCY_".$value["currency"];
					
					if(isset($total_sum_per_currency[$value["currency"]])){
						$total_sum_per_currency[$value["currency"]] += $value["amount_paid_author"];
					}
					else{
						$total_sum_per_currency[$value["currency"]] = $value["amount_paid_author"];
					}
					
					if( $task =="paid"){
						$data_on = self::getDateFormat($value["data_paid"]);
						$data .= $auth_name[0].",";
						$character = "GURU_CURRENCY_".$value["coin"];
						if($currencypos == 0){
							$data .= '"'.JText::_($character)." ".number_format($value["total"],2).'"'.",";
						}
						else{
							$data .= '"'.number_format($value["total"],2)." ".JText::_($character).'"'.",";
						}
						$data .= $value["count_payments"].",";
						$data .= $data_on."\n";
					}
					elseif($task =="pending"){
						$data .= $auth_name[0].",";
						$data .= $course_name[0].",";
						$data .= $count_orders.",";
						if($currencypos == 0){
							$data .= '"'.JText::_($character)." ".number_format($value["amount_paid_author"],2).'"'."\n";
						}
						else{
							$data .= '"'.number_format($value["amount_paid_author"],2)." ".JText::_($character).'"'."\n";
						}
					}
					elseif($task =="details" && $page =="paid"){
						$data_on = self::getDateFormat($value["data"]);
						$data .= $inc.",";
						$data .= $value["id"].",";
						$data .= $data_on.",";
						if($currencypos == 0){
							$data .= '"'.JText::_($character)." ".number_format($value["amount_paid_author"],2).'"'."\n";
						}
						else{
							$data .= '"'.number_format($value["amount_paid_author"],2)." ".JText::_($character).'"'."\n";
						}
					}
					elseif($task =="details" &&($page =="pending"|| $page =="history")){
						$data_on = self::getDateFormat($value["data"]);
						$data .= $inc.",";
						$data .= $value["id"].",";
						$data .= $data_on.",";
						if($currencypos == 0){
							$data .= JText::_($character)." ".$value["price"].",";
						}
						else{
							$data .= $value["price"]." ".JText::_($character).",";
						}
						if($currencypos == 0){
							$data .= JText::_($character)." ".$value["price_paid"].",";
						}
						else{
							$data .= $value["price_paid"]." ".JText::_($character).",";
						}
						$data .= $student_name[0].",";
						$data .= $value["payment_method"].",";
						$data .= $promo_name.",";
						$data .= $commission_plan["0"]["commission_plan"]." (".$commission_plan["0"]["teacher_earnings"]."%)".",";
						if($currencypos == 0){
							$data .= '"'.JText::_($character)." ".number_format($value["amount_paid_author"],2).'"'."\n";
						}
						else{
							$data .= '"'.number_format($value["amount_paid_author"],2)." ".JText::_($character).'"'."\n";
						}
					}
					elseif($task =="details" && $page =="history1"){
						$data_on = self::getDateFormat($value["data"]);
						$data .= $inc.",";
						$data .= $value["id"].",";
						$data .= $data_on.",";
						if($currencypos == 0){
							$data .= JText::_($character)." ".$value["price"].",";
						}
						else{
							$data .= $value["price"]." ".JText::_($character).",";
						}
						if($currencypos == 0){
							$data .= JText::_($character)." ".$value["price_paid"].",";
						}
						else{
							$data .= $value["price_paid"]." ".JText::_($character).",";
						}
						$data .= $student_name[0].",";
						$data .= $value["payment_method"].",";
						$data .= $promo_name.",";
						$data .= $commission_plan["0"]["commission_plan"]." (".$commission_plan["0"]["teacher_earnings"]."%)".",";
						if($currencypos == 0){
							$data .= '"'.JText::_($character)." ".number_format($value["amount_paid_author"],2).'"'."\n";
						}
						else{
							$data .= '"'.number_format($value["amount_paid_author"],2)." ".JText::_($character).'"'."\n";
						}
					}
					elseif($task =="history"){
						$data_on = self::getDateFormat($value["data"]);
						$data .= $auth_name[0].",";
						if($currencypos == 0){
							$data .= '"'.JText::_($character)." ".number_format($value["amount_paid_author"],2).'"'.",";
						}
						else{
							$data .= '"'.number_format($value["amount_paid_author"],2)." ".JText::_($character).'"'.",";
						}
						$data .= $data_on."\n";
					}
					$inc ++;
				}
				
				
				if($currencypos == 0){
					$sum_pricef = '"'.JText::_($character)." ".number_format($sum_price,2).'"';
					$sum_price_paidf = '"'.JText::_($character)." ".number_format($sum_price_paid,2).'"';
					$sum_promof =  '"'.JText::_($character)." ".number_format($sum_promo,2).'"';

				}
				else{
					$sum_pricef = '"'.number_format($sum_price,2)." ".JText::_($character).'"'."\n";
					$sum_price_paidf = '"'.number_format($sum_price_paid,2)." ".JText::_($character).'"';
					$sum_promof = '"'.number_format($sum_promo,2)." ".JText::_($character).'"';

				}
			}
			
			if($task =="details" && $page =="paid"){
				$data .= "\n";
				$data .= ",".",".JText::_('GURU_SUMMARY').",";
				foreach($total_sum_per_currency as $currency=>$value){
					if($currencypos == 0){
						$data .= '"'.JText::_("GURU_CURRENCY_".$currency)." ".number_format($value,2).'"'.",";
					}
					else{
						$data .= '"'.number_format($value,2)." ".JText::_("GURU_CURRENCY_".$currency).'"'.",";
					}
					$data .= "\n";
					$data .= ",";
				}				
			}
			elseif($task =="details" && $page =="history1"){
				$data .= "\n";
				$data .= ",".",".JText::_('GURU_SUMMARY').",";
				$data .= $sum_pricef.",";
				$data .= $sum_price_paidf.",".",".",";
				$data .= $sum_promof.",".",";
				foreach($total_sum_per_currency as $currency=>$value){
					if($currencypos == 0){
						$data .= '"'.JText::_("GURU_CURRENCY_".$currency)." ".number_format($value,2).'"'.",";
					}
					else{
						$data .= '"'.number_format($value,2)." ".JText::_("GURU_CURRENCY_".$currency).'"'.",";
					}
					$data .= "\n";
					$data .= ",";
				}
			}
			
			if( $task =="paid"){
				$csv_filename = "paid_commissions.csv";
			}
			elseif($task =="pending"){
				$data .= "\n";
				$data .= ",".",".JText::_('GURU_SUMMARY').",";
				foreach($total_sum_per_currency as $currency=>$value){
					if($currencypos == 0){
						$data .= '"'.JText::_("GURU_CURRENCY_".$currency)." ".number_format($value,2).'"'.",";
					}
					else{
						$data .= '"'.number_format($value,2)." ".JText::_("GURU_CURRENCY_".$currency).'"'.",";
					}
					$data .= "\n";
					$data .= ",".",".",";
				}
				$csv_filename = "pending_commissions.csv";
			}
			elseif($task =="history"){
				$data .= "\n";
				$data .= JText::_('GURU_SUMMARY').",";
				foreach($total_sum_per_currency as $currency=>$value){
					if($currencypos == 0){
						$data .= '"'.JText::_("GURU_CURRENCY_".$currency)." ".number_format($value,2).'"'.",";
					}
					else{
						$data .= '"'.number_format($value,2)." ".JText::_("GURU_CURRENCY_".$currency).'"'.",";
					}
					$data .= "\n";
					$data .= ",";
				}
				$csv_filename = "history_commissions.csv";
			}
			elseif($task =="details" && ($page =="paid" || $page =="history1" )){
				$csv_filename = "paid_commissions_details.csv";
			}
			elseif($task =="details" && $page =="pending"){
				$data .= "\n";
				$data .= ",".",".JText::_('GURU_SUMMARY').",";
				$data .= $sum_pricef.",";
				$data .= $sum_price_paidf.",".",".",";
				$data .= $sum_promof.",".",";
				foreach($total_sum_per_currency as $currency=>$value){
					if($currencypos == 0){
						$data .= '"'.JText::_("GURU_CURRENCY_".$currency)." ".number_format($value,2).'"'.",";
					}
					else{
						$data .= '"'.number_format($value,2)." ".JText::_("GURU_CURRENCY_".$currency).'"'.",";
					}
					$data .= "\n";
					$data .= ",";
				}
				$csv_filename = "pending_commissions_details.csv";
			}
			elseif($task =="details" && $page =="history"){
				$csv_filename = "history_commissions_details.csv";
			}
			$size_in_bytes = strlen($data);
			header("Content-Type: application/x-msdownload");
			header("Content-Disposition: attachment; filename=".$csv_filename);
			header("Pragma: no-cache");
			header("Expires: 0");
			//echo utf8_decode($data);
			//echo html_entity_decode($data,ENT_QUOTES, 'ISO-8859-15');
			echo $data;

			exit();
		}
	}
	
	
};	
?>