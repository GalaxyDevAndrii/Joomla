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

if(!defined('CRONDEBUGLOG')){
	define('CRONDEBUGLOG', '0');
}

function sendEmailOnPurcase($course_id, $order_id, $order_expiration, $plan_id){
	$all_emails = getOnPurchaseEmails($course_id);
	$order = getOrderDetails($order_id);
	$all_plans = getPlanExpiration();
	$guru_configs = getConfigs();
	
	if(isset($all_emails) && count($all_emails) > 0){
		$db = JFactory::getDbo();
		$sql = "select `mail_purchase_subject`, `mail_purchase_template` from #__guru_program where `id`=".intval($course_id);
		$db->setQuery($sql);
		$db->execute();
		$custom_purchase_email = $db->loadAssocList();

		foreach($all_emails as $email_key=>$email_value){
			if(isset($custom_purchase_email) && isset($custom_purchase_email["0"]["mail_purchase_subject"]) && trim($custom_purchase_email["0"]["mail_purchase_subject"]) != ""){
				$email_value["subject"] = trim($custom_purchase_email["0"]["mail_purchase_subject"]);
			}

			if(isset($custom_purchase_email) && isset($custom_purchase_email["0"]["mail_purchase_template"]) && trim($custom_purchase_email["0"]["mail_purchase_template"]) != ""){
				$email_value["body"] = trim($custom_purchase_email["0"]["mail_purchase_template"]);
			}

			submitEmail($email_value, $course_id, $order["0"], $order_expiration, $all_plans[$plan_id]["name"], $guru_configs);
		}
	}
}	

function getOnPurchaseEmails($course_id){
	$db = JFactory::getDBO();
	$sql = "select s.* from #__guru_subremind s, #__guru_program_reminders p where p.product_id=".intval($course_id)." and p.emailreminder_id=s.id and s.term=11";
	$db->setQuery($sql);
	$db->execute();
	$result = $db->loadAssocList();

	return $result;
}

function getOrderDetails($order_id){
	$db = JFactory::getDBO();
	$sql = "select id, userid, order_date, courses, number_of_licenses from #__guru_order where id=".intval($order_id);
	$db->setQuery($sql);
	$db->execute();
	$result = $db->loadAssocList();
	return $result;
}

function getPlanExpiration(){
	$db = JFactory::getDBO();
	$sql = "select * from #__guru_subplan";
	$db->setQuery($sql);
	$db->execute();
	$result = $db->loadAssocList("id");
	return $result;
}

function getConfigs(){
	$db = JFactory::getDBO();
	$sql = "select * from #__guru_config where id=1";
	$db->setQuery($sql);
	$db->execute();
	$result = $db->loadAssocList();
	return $result;
}

function getCourseEmails($course_id){
	$db = JFactory::getDBO();
	$sql = "select s.*, ps.* from #__guru_subremind s, #__guru_program_reminders ps where ps.product_id=".intval($course_id)." and s.id=ps.emailreminder_id";
	$db->setQuery($sql);
	$db->execute();
	$result = $db->loadAssocList();
	return $result;
}

function getGuruUserDetails($id){
	$db = JFactory::getDBO();
	$sql = "select * from #__users where id=".intval($id);
	$db->setQuery($sql);
	$db->execute();
	$result = $db->loadAssocList();
	return $result;
}

function submitEmail($email_details, $course_id, $course_value, $order_expiration, $plan_name, $guru_configs){
	$user_details = getGuruUserDetails($course_value["userid"]);
	$config = new JConfig();
	$from = $config->mailfrom;
	$fromname = $config->fromname;
	$db = JFactory::getDBO();

	if(isset($guru_configs["0"]["fromname"]) && trim($guru_configs["0"]["fromname"]) != ""){
		$fromname = trim($guru_configs["0"]["fromname"]);
	}
	if(isset($guru_configs["0"]["fromemail"]) && trim($guru_configs["0"]["fromemail"]) != ""){
		$from = trim($guru_configs["0"]["fromemail"]);
	}
		
	$recipient = array($user_details["0"]["email"]);
	$mode = true;
	$subject_procesed = processText($email_details["subject"], $config, $course_value, $user_details, $order_expiration, $course_id, $plan_name, $guru_configs);
	$body_procesed = processText($email_details["body"], $config, $course_value, $user_details, $order_expiration, $course_id, $plan_name, $guru_configs);

	JFactory::getMailer()->sendMail($from, $fromname, $recipient, $subject_procesed, $body_procesed, $mode);
	
	$sql = "insert into #__guru_logs (`userid`, `productid`, `emailname`, `emailid`, `to`, `subject`, `body`, `buy_date`, `send_date`, `buy_type`) values ('".intval($user_details["0"]["id"])."', ".$course_id.", '', '".intval($email_details["id"])."', '".trim($user_details["0"]["email"])."', '".addslashes(trim($subject_procesed))."', '".addslashes(trim($body_procesed))."', '".date("Y-m-d H:i:s")."', '".date("Y-m-d H:i:s")."', 'new')";
	$db->setQuery($sql);
	$db->execute();
}

function submitEmail2($email_details, $course_id, $course_value, $order_expiration, $plan_name, $guru_configs, $lesson_order_no){
	$user_details = getGuruUserDetails($course_value["userid"]);
	$config = new JConfig();	$from = $config->mailfrom;
	$fromname = $config->fromname;
	
	if(isset($guru_configs["0"]["fromname"]) && trim($guru_configs["0"]["fromname"]) != ""){
		$fromname = trim($guru_configs["0"]["fromname"]);
	}	
	
	if(isset($guru_configs["0"]["fromemail"]) && trim($guru_configs["0"]["fromemail"]) != ""){	
		$from = trim($guru_configs["0"]["fromemail"]);	}
		$recipient = array($user_details["0"]["email"]);
		$mode = true;
		$db = JFactory::getDBO();
		$sql1 = "SELECT id FROM #__guru_task WHERE id IN (SELECT media_id FROM #__guru_mediarel WHERE type = 'dtask' AND type_id in (SELECT id FROM #__guru_days WHERE pid =".intval($course_id).")) order by ordering  LIMIT 1 OFFSET ".($lesson_order_no - 1);
		$db->setQuery($sql1);
		$db->execute();
		$lesson_id = $db->loadResult();	
	   
		$sql = "select t.name as lesson_name, m.type_id as lesson_module from #__guru_task t, #__guru_mediarel m where t.id = ".intval($lesson_id)." and m.type='dtask' and m.media_id=".intval($lesson_id)." and m.type_id <> 0";
		$db->setQuery($sql);	
		$db->execute();	
		$lesson_data = $db->loadRow();
		
		$sql = "select catid from #__guru_program where id=".$course_id;
		$db->setQuery($sql);	
		$db->execute();	
		$catid = $db->loadResult();
		
		$sql = "SELECT count(id) FROM #__guru_task WHERE id IN (SELECT media_id FROM #__guru_mediarel WHERE type = 'dtask' AND type_id in (SELECT id FROM #__guru_days WHERE pid =".intval($course_id)."))";
		$db->setQuery($sql);	
		$db->execute();	
		$count_less = $db->loadResult();

		$subject_procesed = processText1($email_details["subject"], $config, $course_value, $user_details, $order_expiration, $course_id, $plan_name, $guru_configs,$lesson_data,$lesson_id,$catid);
		$body_procesed = processText1($email_details["body"], $config, $course_value, $user_details, $order_expiration, $course_id, $plan_name, $guru_configs,$lesson_data,$lesson_id,$catid);
		
		if($lesson_order_no <= $count_less){
			JFactory::getMailer()->sendMail($from, $fromname, $recipient, $subject_procesed, $body_procesed, $mode);
			
			$sql = "insert into #__guru_logs (`userid`, `emailname`, `emailid`, `to`, `subject`, `body`, `buy_date`, `send_date`, `buy_type`) values ('".intval($course_value["userid"])."', 'new-lesson', '0', '".trim($user_details["0"]["email"])."', '".addslashes(trim($subject_procesed))."', '".addslashes(trim($body_procesed))."', '', '".date("Y-m-d H:i:s")."', '')";
			$db->setQuery($sql);
			$db->execute();
		}
}

function getCustomerDetails($id){
	$db = JFactory::getDBO();
	$sql = "select * from #__guru_customer where id=".intval($id);
	$db->setQuery($sql);
	$db->execute();
	$result = $db->loadAssocList();
	return $result;
}

function getRenewDefaultValue($course_id, $guru_configs){
	$db = JFactory::getDBO();		
	$sql = "SELECT `price` FROM `#__guru_program_plans` WHERE `product_id` = ".intval($course_id)." and `default` = 1";		
	$db->setQuery($sql);
	$db->execute();
	$price = $db->loadResult();
	return JText::_("GURU_CURRENCY_".$guru_configs["0"]["currency"])." ".$price;
}

function getCourseName($id){
	$db = JFactory::getDBO();
	$sql = "select name from #__guru_program where id=".intval($id);
	$db->setQuery($sql);
	$db->execute();
	$result = $db->loadResult();
	return $result;
}

function processText($text, $config, $course_value, $user_details, $order_expiration, $course_id, $plan_name, $guru_configs){	
	$customer_details = getCustomerDetails($user_details["0"]["id"]);
	$site_name = $config->sitename;
	$email = $user_details["0"]["email"];
	$first_name = $customer_details["0"]["firstname"];
	$last_name = $customer_details["0"]["lastname"];
	$site_url = JURI::root();
	$user_name = $user_details["0"]["username"];
	$renew_url = JURI::root().'index.php?option=com_guru&controller=guruPrograms&task=buy_action&course_id='.$course_id.'&action=renewemail';
	
	$product_url = "";
	$renew_term = getRenewDefaultValue($course_id, $guru_configs);
	$license_number = $course_value["id"];
	$my_licenses = "";
	$product_name = getCourseName($course_id);

	if($order_expiration == "0000-00-00 00:00:00" || $order_expiration == FALSE){
		$expire_date = JText::_("GURU_LIFETIME_MEMBERSHIP");
	}
	else{
		$expire_date = $order_expiration;
		$date_format = $guru_configs["0"]["datetype"];
		$expire_date = strtotime($expire_date);
		$expire_date = date($date_format, $expire_date);
	}
	
	$my_orders = JURI::root()."index.php?option=com_guru&view=guruorders&layout=myorders";
	$subscription_term = $plan_name;
	
	$text = str_replace("[SITENAME]", $site_name, $text);
	$text = str_replace("[STUDENT_EMAIL]", $email, $text);
	$text = str_replace("[STUDENT_FIRST_NAME]", $first_name, $text);
	$text = str_replace("[SITEURL]", $site_url, $text);
	$text = str_replace("[STUDENT_USER_NAME]", $user_name, $text);
	$text = str_replace("[PRODUCT_URL]", $product_url, $text);
	$text = str_replace("[STUDENT_LAST_NAME]", $last_name, $text);
	$text = str_replace("[RENEW_TERM]", $renew_term, $text);
	$text = str_replace("[RENEW_URL]", $renew_url, $text);
	$text = str_replace("[LICENSE_NUMBER]", $license_number, $text);
	$text = str_replace("[MY_LICENSES]", $my_licenses, $text);
	$text = str_replace("[COURSE_NAME]", $product_name, $text);
	$text = str_replace("[EXPIRE_DATE]", $expire_date, $text);
	$text = str_replace("[MY_ORDERS]", $my_orders, $text);
	$text = str_replace("[SUBSCRIPTION_TERM]", $subscription_term, $text);
	
	$course_url = JRoute::_("index.php?option=com_guru&view=guruPrograms&task=view&cid=".$course_id, true, -1);
	$app = JFactory::getApplication();
	
	if($app->isAdmin()){
		$course_url = JURI::root()."index.php?option=com_guru&view=guruPrograms&task=view&cid=".intval($course_id);
	}
	
	$course_href = '<a href="'.$course_url.'" target="_blank">'.$course_url.'</a>';
	
	$text = str_replace("[COURSE_URL]", $course_href, $text);
	$text = str_replace("[MY_COURSES]", JURI::root()."index.php?option=com_guru&view=guruorders&layout=mycourses", $text);
	$text = str_replace("[MY_ORDERS]", JURI::root()."index.php?option=com_guru&view=guruorders&layout=myorders", $text);
	
	return $text;
}

function processText1($text, $config, $course_value, $user_details, $order_expiration, $course_id, $plan_name, $guru_configs, $lesson_data,$lesson_id,$catid){
	$customer_details = getCustomerDetails($user_details["0"]["id"]);
	$site_name = $config->sitename;
	$email = $user_details["0"]["email"];
	$first_name = $customer_details["0"]["firstname"];
	$last_name = $customer_details["0"]["lastname"];
	$site_url = JURI::root();
	$user_name = $user_details["0"]["username"];
	$renew_url = JURI::root().'index.php?option=com_guru&controller=guruPrograms&task=buy_action&course_id='.$course_id.'&action=renewemail';
	
	$product_url = "";
	$renew_term = getRenewDefaultValue($course_id, $guru_configs);
	$license_number = "1";
	$my_licenses = "";
	$product_name = getCourseName($course_id);
	
	if(($order_expiration == "0000-00-00 00:00:00" || $order_expiration == FALSE) && $plan_name =="Unlimited Access")
	{
		$expire_date = JText::_("GURU_LIFETIME_MEMBERSHIP");
	}
	else{		
		
		$expire_date = $order_expiration;
		$date_format = $guru_configs["0"]["datetype"];
		$expire_date = strtotime($expire_date);
		$expire_date = date($date_format, $expire_date);
	}
	
	$my_orders = "";
	$subscription_term = $plan_name;
	
	$lesson_title = $lesson_data["0"];
	$module_lesson = $lesson_data["1"];
	$text = str_replace("[LESSON_TITLE]",$lesson_title, $text);
	$text = str_replace("[LESSON_URL]", JRoute::_("index.php?option=com_guru&view=guruTasks&catid=".intval($catid)."&task=view&module=".$module_lesson."&cid=".$lesson_id."&e=1", true, -1), $text);
	$text = str_replace("[SITENAME]", $site_name, $text);
	$text = str_replace("[STUDENT_EMAIL]", $email, $text);
	$text = str_replace("[STUDENT_FIRST_NAME]", $first_name, $text);
	$text = str_replace("[SITEURL]", $site_url, $text);
	$text = str_replace("[STUDENT_USER_NAME]", $user_name, $text);
	$text = str_replace("[PRODUCT_URL]", $product_url, $text);
	$text = str_replace("[STUDENT_LAST_NAME]", $last_name, $text);
	$text = str_replace("[RENEW_TERM]", $renew_term, $text);
	$text = str_replace("[RENEW_URL]", $renew_url, $text);
	$text = str_replace("[LICENSE_NUMBER]", $license_number, $text);
	$text = str_replace("[MY_LICENSES]", $my_licenses, $text);
	$text = str_replace("[COURSE_NAME]", $product_name, $text);
	$text = str_replace("[EXPIRE_DATE]", $expire_date, $text);
	$text = str_replace("[MY_ORDERS]", $my_orders, $text);
	$text = str_replace("[SUBSCRIPTION_TERM]", $subscription_term, $text);
	
	$course_url = JRoute::_("index.php?option=com_guru&view=guruPrograms&task=view&cid=".$course_id, true, -1);
	$app = JFactory::getApplication();
	
	if($app->isAdmin()){
		$course_url = JURI::root()."index.php?option=com_guru&view=guruPrograms&task=view&cid=".intval($course_id);
	}
	
	$course_href = '<a href="'.$course_url.'" target="_blank">'.$course_url.'</a>';
	
	$text = str_replace("[COURSE_URL]", $course_href, $text);
	$text = str_replace("[MY_COURSES]", JURI::root()."index.php?option=com_guru&view=guruorders&layout=mycourses", $text);
	$text = str_replace("[MY_ORDERS]", JURI::root()."index.php?option=com_guru&view=guruorders&layout=myorders", $text);
	
	return $text;
}


function getRealTerm($term){
	$return = "0";
	switch($term){
		case "1" : $return = "-1";
		           break;
		case "2" : $return = "-2";
		           break;
		case "3" : $return = "-3";
		           break;
		case "4" : $return = "-7";
		           break;
		case "5" : $return = "-14";
		           break;
		case "6" : $return = "+1";
		           break;
		case "7" : $return = "+2";
		           break;
		case "8" : $return = "+3";
		           break;
		case "9" : $return = "+7";
		           break;
		case "10" : $return = "+14";
		           break;
	}
	return $return;
}

function guru_cronjobs(){
	$timezone = new DateTimeZone( JFactory::getConfig()->get('offset') );
	$jnow = new JDate('now');
	$jnow->setTimezone($timezone);
	$date_today = $jnow->toSQL(true);
	$date_today_int = strtotime($date_today);
	$day_today = date("d", $date_today_int);
	
	$db = JFactory::getDBO();
	$sql = "select last_check_date from #__guru_config where id=1";
	$db->setQuery($sql);
	$db->execute();
	$last_check_date = $db->loadResult();
	$int_last_check = strtotime($last_check_date);
	$day_last_check = date("d", $int_last_check);

	$timezone = new DateTimeZone( JFactory::getConfig()->get('offset') );
	$jnow = new JDate('now');
	$jnow->setTimezone($timezone);
	$date = $jnow->toSQL(true);
	$date = date("d", strtotime($date));

	if($day_today != $day_last_check){
		$sql = "select c.* from #__guru_buy_courses c, #__guru_order o where o.`id`=c.`order_id` and o.`status`='Paid'";
		$db->setQuery($sql);
		$db->execute();
		$all_courses = $db->loadAssocList();
		$all_plans = getPlanExpiration();

		if(isset($all_courses) && count($all_courses) > 0){
			$guru_configs = getConfigs();

			foreach($all_courses as $course_key=>$course_value){
				$date_today_int = strtotime($date_today);
				$course_id = $course_value["course_id"];
				$plan_id = intval($course_value["plan_id"]);
				$emails_for_course = getCourseEmails($course_id);

				//----------------------New Lesson Email------------------------------------
				if(isset($emails_for_course) && count($emails_for_course) > 0){
					foreach($emails_for_course as $email_key=>$email_value){
						if($email_value["term"] == "12"){
							$sql = "select DATE_FORMAT(p.start_release, '%Y-%m-%d') as start_release, p.course_type, p.lesson_release,  p.lessons_show from #__guru_program p WHERE p.id=".intval($course_id);								
							$db->setQuery($sql);								
							$db->execute();								
							$coursetype_details = $db->loadAssocList();

							if($coursetype_details[0]["course_type"] == 1) {																		
								$buy_date = strtotime($course_value["buy_date"]);	
								$buy_date = date("Y-m-d", $buy_date);	
								$buy_date = strtotime($buy_date);
								
								$next_release_date = $buy_date;
								
								$jnow = new JDate('now');
								$my_today_date = $jnow->toSql(true);
								$my_today_date = strtotime($my_today_date);
								$my_today_date = date("Y-m-d", $my_today_date);
								$my_today_date = strtotime($my_today_date);
																
								$release_count = 1;

								while($next_release_date < $my_today_date) {																				
									if($coursetype_details[0]["lesson_release"] == 1){			
										$next_release_date = strtotime ( '+1 day' , $next_release_date) ;
									}										
									elseif($coursetype_details[0]["lesson_release"] == 2){											
										$next_release_date = strtotime ( '+1 week' , $next_release_date) ;										
									}										
									elseif($coursetype_details[0]["lesson_release"] == 3){											
										$next_release_date = strtotime ( '+1 month' , $next_release_date) ;										
									}
									else{
										break;
									}

									$release_count++;									
								}

								$last_check_date = strtotime($last_check_date);

								if($my_today_date == $next_release_date && $next_release_date != $buy_date){
									submitEmail2($email_value, $course_id, $course_value, @$order_expiration, $all_plans[$plan_id]["name"], $guru_configs, $release_count);
								}
							}
						}		
					}

					//----------------------------------------------------------
					if(isset($emails_for_course) && count($emails_for_course) > 0 /*&& $course_value["email_send"] == 0*/){
						if(isset($all_plans[$plan_id]["term"]) && $all_plans[$plan_id]["term"] != "0"){	
							$order_expiration = strtotime($course_value["expired_date"]);
							$order_expiration_string = date("Y-m-d", $order_expiration);
							$order_expiration = strtotime($order_expiration_string);
							$date_totay_string = date("Y-m-d", $date_today_int);
							$date_today_int = strtotime($date_totay_string);
							
							foreach($emails_for_course as $email_key=>$email_value){
								$alert_date = "";

								if($email_value["term"] == "0" && ($date_today_int == $order_expiration)){
									// on expiration date
									$order_expiration = date("Y-m-d H:i:s", $order_expiration);
									submitEmail($email_value, $course_id, $course_value, $order_expiration, $all_plans[$plan_id]["name"], $guru_configs);
								}
								elseif($email_value["term"] != "0" && ($email_value["term"] >= 1 && $email_value["term"] <=5)){
									// X days/weeks before expiration
									@$alert_date = strtotime(getRealTerm($email_value["term"])." days", $order_expiration);
								}
								elseif($email_value["term"] != "0" && ($email_value["term"] >= 6 && $email_value["term"] <=10)){
									// X days/weeks after expiration
									@$alert_date = strtotime(getRealTerm($email_value["term"])." days", $order_expiration);
								}
								
								if(trim($alert_date) != ""){
									$alert_date_string = date("Y-m-d H:i:s", $alert_date);
									$alert_date = strtotime($alert_date_string);
								}

								if(isset($alert_date) && trim($alert_date) != "" && $date_today_int == $alert_date){
									$order_expiration = date("Y-m-d H:i:s", $order_expiration);
									submitEmail($email_value, $course_id, $course_value, $order_expiration, $all_plans[$plan_id]["name"], $guru_configs);
								}
							}

							
						}

						$sql = "update #__guru_buy_courses set email_send = 1 where userid=".intval($course_value["userid"])." and course_id = ".intval($course_id);
						$db->setQuery($sql);
						$db->execute();
					}//if sometime expire this course
				}//if we have emails for send							
			}//foreach order
			
			//set today date to not check emails on this day
			$sql = "update #__guru_config set last_check_date='".$date_today."'";
			$db->setQuery($sql);
			$db->execute();
		}//if we have courses
	}//if today not search
}
?>