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

require_once (JPATH_COMPONENT_ADMINISTRATOR.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'helper.php');

class guruControllerguruOrders extends guruController {
	var $_model = null;
	
	function __construct () {
		parent::__construct();
		$this->registerTask("mycourses", "myCourses");
		$this->registerTask ("listQuizStud", "listQuizStud");
		$this->registerTask ("show_quizz_res", "show_quizz_res");
		$this->registerTask("view", "viewOrderDetails1");
		$this->registerTask("showrec", "viewOrderDetails2");
		$this->registerTask("renew", "renewtest");
		$this->registerTask("printcertificate", "printcertificate");
		$this->registerTask("sendemailcertificate", "sendemailcertificate");
		$this->_model = $this->getModel("guruOrder");	
	}
	
	function listQuizStud(){
		$view = $this->getView("guruOrders", "html");
		$view->setLayout("listquizstud");
		$view->setModel($this->_model, true);
		$view->listQuizStud();
	}
	function show_quizz_res(){
		$view = $this->getView("guruOrders", "html");
		$view->setLayout("show_quizz_res");
		$view->setModel($this->_model, true);
		$view->show_quizz_res();
	}

	function myorders(){
		$Itemid = JFactory::getApplication()->input->get("Itemid", "0", "raw");
		$user = JFactory::getUser();
		$user_id = $user->id;
		// Check Login		
		if($user_id == "0"){		
			$helper = new guruHelper();
			$itemid_seo = $helper->getSeoItemid();
			$itemid_seo = @$itemid_seo["gurulogin"];
			
			if(intval($itemid_seo) > 0){
				$Itemid = intval($itemid_seo);
			}
		
			$this->setRedirect(JRoute::_("index.php?option=com_guru&view=guruLogin&returnpage=myorders"."&Itemid=".$Itemid, false));
			return true;
		}
		
		$model = $this->getModel("guruOrder");
        $res = $model->checkCustomerProfile($user_id);

        if($res === FALSE){
            $helper = new guruHelper();
			$itemid_seo = $helper->getSeoItemid();
			$itemid_seo = @$itemid_seo["guruprofile"];
			
			if(intval($itemid_seo) > 0){
				$Itemid = intval($itemid_seo);
			}
			else{
				$user = JFactory::getUser();
				$user_id = $user->id;

            	$itemid_menu = $helper->getTeacherMenuItem(intval($user_id));

            	if(intval($itemid_menu) > 0){
                    $Itemid = intval($itemid_menu);
                }
            }
			
			$this->setRedirect(JRoute::_("index.php?option=com_guru&view=guruProfile&task=edit&returnpage=myorders&Itemid=".$Itemid, false));
        }
		
		$view = $this->getView("guruOrders", "html");
		$view->setLayout("myorders");
		$view->setModel($this->_model, true);
		$view->display();
	}
	
	function mycertificates(){
		$Itemid = JFactory::getApplication()->input->get("Itemid", "0", "raw");
		$user = JFactory::getUser();
		$user_id = $user->id;
		// Check Login		
		if($user_id == "0"){		
			$helper = new guruHelper();
			$itemid_seo = $helper->getSeoItemid();
			$itemid_seo = @$itemid_seo["gurulogin"];
			
			if(intval($itemid_seo) > 0){
				$Itemid = intval($itemid_seo);
			}
			
			$this->setRedirect(JRoute::_("index.php?option=com_guru&view=guruLogin&returnpage=mycertificates"."&Itemid=".$Itemid, false));
			return true;
		}
		
		$model = $this->getModel("guruOrder");
        $res = $model->checkCustomerProfile($user_id);

        if($res === FALSE){
			$helper = new guruHelper();
			$itemid_seo = $helper->getSeoItemid();
			$itemid_seo = @$itemid_seo["guruprofile"];
			
			if(intval($itemid_seo) > 0){
				$Itemid = intval($itemid_seo);
			}
			else{
				$user = JFactory::getUser();
				$user_id = $user->id;

            	$itemid_menu = $helper->getTeacherMenuItem(intval($user_id));

            	if(intval($itemid_menu) > 0){
                    $Itemid = intval($itemid_menu);
                }
            }
		
            $this->setRedirect(JRoute::_("index.php?option=com_guru&view=guruProfile&task=edit&returnpage=mycertificate&Itemid=".$Itemid, false));
        }
		
		$view = $this->getView("guruOrders", "html");
		$view->setLayout("mycertificates");
		$view->setModel($this->_model, true);
		//$view->display();
		$view->mycertificates();
	}
	
	function myquizandfexam(){
		$Itemid = JFactory::getApplication()->input->get("Itemid", "0", "raw");
		$user = JFactory::getUser();
		$user_id = $user->id;
		// Check Login		
		if($user_id == "0"){		
			$helper = new guruHelper();
			$itemid_seo = $helper->getSeoItemid();
			$itemid_seo = @$itemid_seo["gurulogin"];
			
			if(intval($itemid_seo) > 0){
				$Itemid = intval($itemid_seo);
			}
		
			$this->setRedirect(JRoute::_("index.php?option=com_guru&view=guruLogin&returnpage=myquizandfexam"."&Itemid=".$Itemid, false));
			return true;
		}
		
		$model = $this->getModel("guruOrder");
        $res = $model->checkCustomerProfile($user_id);

        if($res === FALSE){
			$helper = new guruHelper();
			$itemid_seo = $helper->getSeoItemid();
			$itemid_seo = @$itemid_seo["guruprofile"];
			
			if(intval($itemid_seo) > 0){
				$Itemid = intval($itemid_seo);
			}
			else{
				$user = JFactory::getUser();
				$user_id = $user->id;

            	$itemid_menu = $helper->getTeacherMenuItem(intval($user_id));

            	if(intval($itemid_menu) > 0){
                    $Itemid = intval($itemid_menu);
                }
            }
		
            $this->setRedirect(JRoute::_("index.php?option=com_guru&view=guruProfile&task=edit&returnpage=myquizandfexam&Itemid=".$Itemid, false));
        }
		
		$view = $this->getView("guruOrders", "html");
		$view->setLayout("myquizandfexam");
		$view->setModel($this->_model, true);
		//$view->display();
		$view->myQuizandfexam();
	
	}
	
	function myCourses(){
		$Itemid = JFactory::getApplication()->input->get("Itemid", "0", "raw");
		$order_id = JFactory::getApplication()->input->get("order_id", "0", "raw");

		if(intval($order_id) > 0){ // when return from Payment - Veritrans
			$session = JFactory::getSession();
			$registry = $session->get('registry');
			$registry->set('courses_from_cart', "");
			$registry->set('renew_courses_from_cart', "");
			$registry->set('promo_code', "");
			$registry->set('max_total', "");
			$registry->set('order_id', "");
			$registry->set('promocode', "");
			$registry->set('processor', "");
		}

		$user = JFactory::getUser();
		$user_id = $user->id;
		// Check Login		
		if($user_id == "0"){		
			$helper = new guruHelper();
			$itemid_seo = $helper->getSeoItemid();
			$itemid_seo = @$itemid_seo["gurulogin"];
			
			if(intval($itemid_seo) > 0){
				$Itemid = intval($itemid_seo);
			}
		
			$this->setRedirect(JRoute::_("index.php?option=com_guru&view=guruLogin&returnpage=mycourses"."&Itemid=".$Itemid, false));
			return true;
		}
		
		$view = $this->getView("guruOrders", "html");
		$view->setLayout("mycourses");
		$view->setModel($this->_model, true);
		$model = $this->getModel("guruOrder");
		$view->setModel($model);
		$view->myCourses();
	}
	
	function printcertificate(){
		$view = $this->getView("guruOrders", "html");
		$view->setLayout("printcertificate");
		$view->printcertificate();
	}
	
	function viewOrderDetails1(){
		$view = $this->getView("guruOrders", "html");
		$view->setLayout("orderdetails");
		$view->setModel($this->_model, true);
		$model = $this->getModel("guruOrder");
		$view->setModel($model);
		$view->orderDetails1();
	}
	
	function viewOrderDetails2(){
		$user = JFactory::getUser();
		$user_id = $user->id;
		$Itemid = JFactory::getApplication()->input->get("Itemid", "0", "raw");
		$user_reques = JFactory::getApplication()->input->get("user_reques", "0");
		
		// Check Access
		if($user_id == "0" && $user_reques == 0){
			$helper = new guruHelper();
			$itemid_seo = $helper->getSeoItemid();
			$itemid_seo = @$itemid_seo["gurulogin"];
			
			if(intval($itemid_seo) > 0){
				$Itemid = intval($itemid_seo);
			}
		
			$this->setRedirect(JRoute::_("index.php?option=com_guru&view=guruLogin&returnpage=myorders"."&Itemid=".$Itemid, false));
			return true;
		}
		else{
			$view = $this->getView("guruOrders", "html");
			$view->setLayout("orderdetails");
			$view->setModel($this->_model, true);
			$model = $this->getModel("guruOrder");
			$view->setModel($model);
			$view->orderDetails2();
		}
	}

	function order(){
		JFactory::getApplication()->input->set ("hidemainmenu", 1);
		$view = $this->getView("guruOrders", "html");
		$view->setLayout("order");
		$view->setModel($this->_model, true);
		$view->order();
	}
	
	function checkout(){
		$db = JFactory::getDBO();
		$sql = "select count(*) from #__guru_plugins";
		$db->setQuery($sql);
		$payment_plugins = $db->loadResult();
		$my = JFactory::getUser();		
		$filename = JPATH_BASE.'/components/com_guru/models/guruplugin.php';			
		require_once ($filename);
		$res = guruModelguruPlugin::performCheckout($my->id);
		if($res < 0){
			$this->setRedirect(JRoute::_("index.php?option=com_guru&view=guruPrograms" ));
		}	
	}	
	
	function renew(){
		$order_id = JFactory::getApplication()->input->get("order_id", "");
		$course_id = JFactory::getApplication()->input->get("course_id", "");
		$item_id = JFactory::getApplication()->input->get("Itemid", "0", "raw");
		
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->clear();
		$query->select($db->quoteName('price'));
		$query->from("#__guru_program_renewals");
		$query->where($db->quoteName('product_id') . ' = ' . intval($course_id) . " AND " . $db->quoteName('default') . " = '1'");
		$db->setQuery($query);
		
		$price = $db->loadResult();
		
		if(!isset($price) && $price == NULL){
			$price = "0";
		}
		
		$db = JFactory::getDBO();
		$sql = "SELECT name FROM #__guru_program WHERE id = ".intval($course_id);		
		$db->setQuery($sql);
		$db->execute();
		$name = $db->loadResult();
		
		$session = JFactory::getSession();
		$registry = $session->get('registry');			
		$renew_courses_from_cart = $registry->get('renew_courses_from_cart', "");
		
		if(isset($renew_courses_from_cart) && $renew_courses_from_cart != ""){
			$temp_array = array("course_id"=>$course_id, "value"=>$price, "name"=>$name, "plan"=>"renew");
			$new_value = $renew_courses_from_cart;
			$new_value[$course_id] = $temp_array;
			$registry->set('renew_courses_from_cart', $new_value);
		}
		else{
			$temp_array = array("course_id"=>$course_id, "value"=>$price, "name"=>$name, "plan"=>"renew");
			$new_value = array();
			$new_value[$course_id] = $temp_array;
			$registry->set('renew_courses_from_cart', $new_value);
		}
		
		$this->setRedirect(JRoute::_("index.php?option=com_guru&view=guruBuy&action=renew&Itemid=".$item_id, false));//put order id if renew to same order
	}

	function myProjects(){
		$user = JFactory::getUser();
		$model = $this->getModel("guruOrder");
        $res = $model->checkCustomerProfile($user->id);
        if($res==false){
        	$this->setRedirect(JRoute::_("index.php?option=com_guru", false));
        }

		$view = $this->getView('guruOrders','html');
		$view->setLayout('myProjects');
        $view->myProjects();
	}
	
	function projectDetail(){
		if($res==false){
        	$this->setRedirect(JRoute::_("index.php?option=com_guru", false));
        }
        
		$view = $this->getView('guruOrders','html');
		$view->setLayout('projectDetail');
        $view->projectDetail();
	}

	function savepdfcertificate(){
		$datac = JFactory::getApplication()->input->post->getArray();
		include(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_guru'.DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'gurutask.php');
		$background_color = "";

		$op = JFactory::getApplication()->input->get("op", "");
		
		$db = JFactory::getDbo();
		$sql = "SELECT `imagesin` FROM #__guru_config LIMIT 1";
		$db->setQuery($sql);
		$db->execute();
		$res = $db->loadResult();
		
		//$certificate_path = JUri::base().$res."/certificates/";
		$certificate_path = JPATH_SITE.DS.$res.DS."certificates".DS;
		
		if($op == 9){
			$db = JFactory::getDBO();
			$user = JFactory::getUser();
			$config = JFactory::getConfig();
			$imagename = "SELECT * FROM #__guru_certificates WHERE id=1";
			$db->setQuery($imagename);
			$db->execute();
			$imagename = $db->loadAssocList();
			
			if($imagename[0]["design_background"] != ""){
				$image_theme = explode("/", $imagename[0]["design_background"]);
				$image_theme = $image_theme[count($image_theme) - 1];
			}	
			else{
				$background_color = "background-color:"."#".$imagename[0]["design_background_color"];
			}

			$datetype = "SELECT `datetype` from #__guru_config WHERE id=1";
            $db->setQuery($datetype);
			$db->execute();
            $dateformat = $db->loadResult();
			
			$site_url = JURI::root();
			$coursename = JFactory::getApplication()->input->get('cn', '', "raw");
			$authorname = JFactory::getApplication()->input->get('an', '', "raw");
			$certificateid = JFactory::getApplication()->input->get('id', '', "raw");
			$completiondate = JFactory::getApplication()->input->get('cd', '', "raw");
			$completiondate = date($dateformat, strtotime($completiondate));
			$course_id = JFactory::getApplication()->input->get('ci', '', "raw");
			$student_id = JFactory::getApplication()->input->get('ct', '', "raw");
			$sitename = $config->get('sitename');
			$user_id = $user->id;

			$sql = "select `custom_certificate` from #__guru_program where `id`=".intval($course_id);
			$db->setQuery($sql);
			$db->execute();
			$custom_certificate = $db->loadColumn();
			$custom_certificate = @$custom_certificate["0"];

			if(intval($custom_certificate) == 1){
			    $sql = "SELECT * FROM #__guru_certificates WHERE `course_id`=".intval($course_id);
			    $db->setQuery($sql);
			    $db->execute();
			    $course_imagename = $db->loadAssocList();

			    if(isset($course_imagename) && isset($course_imagename["0"]) && count($course_imagename) > 0){
			        foreach ($course_imagename["0"] as $key => $value){
			            //if(trim($value) != ""){
			                $imagename["0"][$key] = $value;
			            //}
			        }
			    }

			    if($imagename[0]["design_background"] !=""){
					$image_theme = explode("/", $imagename[0]["design_background"]);
					$image_theme = $image_theme[count($image_theme) - 1];
				}	
				else{
					$image_theme = "";
					$background_color= "background-color:"."#".$imagename[0]["design_background_color"];
				}
			}

			if(intval($student_id) != 0){
				$user_id = intval($student_id);
			}
			
			$model_task = new guruModelguruTask();
			$scores_avg_quizzes = $model_task->getFinalScoreQuizz($user_id,$course_id);

			$avg_quizzes_cert = "SELECT avg_certc FROM #__guru_program WHERE id=".intval($course_id );
			$db->setQuery($avg_quizzes_cert);
			$db->execute();
			$avg_quizzes_cert = $db->loadResult();

			$sql = "SELECT id_final_exam FROM #__guru_program WHERE id=".intval($course_id );
			$db->setQuery($sql);
			$result = $db->loadResult();
	
			$sql = "SELECT hasquiz from #__guru_program WHERE id=".intval($course_id );
			$db->setQuery($sql);
			$resulthasq = $db->loadResult();
	
			$sql = "SELECT max_score FROM #__guru_quiz WHERE id=".intval($result);
			$db->setQuery($sql);
			$result_maxs = $db->loadResult();
			
			// final quiz --------------------------------------------------
			$sql = "SELECT id, score_quiz FROM #__guru_quiz_question_taken_v3 WHERE user_id=".intval($user_id)." and quiz_id=".intval($result)." and pid=".intval($course_id)." ORDER BY id DESC LIMIT 0,1";
			$db->setQuery($sql);
			$result_q = $db->loadObject();
			
			$first= explode("|", @$result_q->score_quiz);
			
			@$res = intval(($first[0]/$first[1])*100);
			
			if($resulthasq == 0 && $scores_avg_quizzes == ""){
				$avg_certc = "N/A";
			}
			elseif($resulthasq != 0 && $scores_avg_quizzes == ""){
				$avg_certc = "N/A";
			}
			elseif($resulthasq != 0 && isset($scores_avg_quizzes)){
				$avg_certc = $scores_avg_quizzes.'%';
			} else if ($result && $scores_avg_quizzes) {
				$avg_certc = $scores_avg_quizzes.'%'; 
			}
			// final quiz --------------------------------------------------
			
			// regular ----------------------------------------------
			$s = 0;
			$sql = "select mr.media_id from #__guru_mediarel mr, #__guru_days d where mr.type='dtask' and mr.type_id=d.id and d.pid=".intval($course_id);
			$db->setQuery($sql);
			$db->execute();
			$lessons = $db->loadColumn();
			
			if(!isset($lessons) || count($lessons) == 0){
				$lessons = array("0");
			}
			
			$sql = "select mr.media_id from #__guru_mediarel mr where mr.layout='12' and mr.type='scr_m' and mr.type_id in (".implode(", ", $lessons).")";
			$db->setQuery($sql);
			$db->execute();
			$all_quizzes = $db->loadColumn();
			
			if(isset($all_quizzes) && count($all_quizzes) > 0){
				foreach($all_quizzes as $key_quiz=>$quiz_id){
					$sql = "SELECT score_quiz FROM #__guru_quiz_question_taken_v3 WHERE user_id=".intval($user_id)." and quiz_id=".intval($quiz_id)." and pid=".intval($course_id)." ORDER BY id DESC LIMIT 0,1";
					$db->setQuery($sql);
					$db->execute();
					$result_q = $db->loadColumn();
					$res = @$result_q["0"];
					$s += $res;
				}
				
				$avg_certc1 = "N/A";
				if($s > 0){
					$avg_certc1 = round($s / count($all_quizzes))."%";
				}
			}
			// regular ----------------------------------------------
				
			$firstnamelastname = "SELECT firstname, lastname, company FROM #__guru_customer WHERE id=".intval($user_id);
			$db->setQuery($firstnamelastname);
			$db->execute();
			$firstnamelastname = $db->loadAssocList();
			
			$coursemsg = "SELECT certificate_course_msg FROM #__guru_program WHERE id=".intval($course_id);
			$db->setQuery($coursemsg);
			$db->execute();
			$coursemsg = $db->loadResult();
			$certificate_url = JUri::base()."index.php?option=com_guru&view=guruOrders&task=printcertificate&opt=".$certificateid."&cn=".$coursename."&an=".$authorname."&cd=".$completiondate."&id=".$certificateid;
			$certificate_url = str_replace(" ", "%20", $certificate_url);

			$certificate_url = '<a href="'.$certificate_url.'" target="_blank">'.JText::_("GURU_URL_CERTIFICATE").'</a>';

			if(!isset($imagename[0]["templatespdf"]) || trim($imagename[0]["templatespdf"]) == ""){
				$imagename[0]["templatespdf"] = $imagename[0]["templates1"];
			}

			$imagename[0]["templatespdf"]  = str_replace("[SITENAME]", $sitename, $imagename[0]["templatespdf"]);
			$imagename[0]["templatespdf"]  = str_replace("[STUDENT_FIRST_NAME]", $firstnamelastname[0]["firstname"], $imagename[0]["templatespdf"]);
			$imagename[0]["templatespdf"]  = str_replace("[STUDENT_LAST_NAME]", $firstnamelastname[0]["lastname"], $imagename[0]["templatespdf"]);
			$imagename[0]["templatespdf"]  = str_replace("[SITEURL]", $site_url, $imagename[0]["templatespdf"]);
			$imagename[0]["templatespdf"]  = str_replace("[CERTIFICATE_ID]", $certificateid, $imagename[0]["templatespdf"]);
			$imagename[0]["templatespdf"]  = str_replace("[COMPLETION_DATE]", $completiondate, $imagename[0]["templatespdf"]);
			$imagename[0]["templatespdf"]  = str_replace("[COURSE_NAME]", $coursename, $imagename[0]["templatespdf"]);
			$imagename[0]["templatespdf"]  = str_replace("[AUTHOR_NAME]", $authorname, $imagename[0]["templatespdf"]);
			$imagename[0]["templatespdf"]  = str_replace("[CERT_URL]", $certificate_url, $imagename[0]["templatespdf"]);
			$imagename[0]["templatespdf"]  = str_replace("[CERTIFICATE_URL]", $certificate_url, $imagename[0]["templatespdf"]);
			$imagename[0]["templatespdf"]  = str_replace("[COURSE_MSG]", $coursemsg, $imagename[0]["templatespdf"]);
			$imagename[0]["templatespdf"]  = str_replace("[COURSE_AVG_SCORE]", $avg_certc1, $imagename[0]["templatespdf"]);
        	$imagename[0]["templatespdf"]  = str_replace("[COURSE_FINAL_SCORE]", $avg_certc, $imagename[0]["templatespdf"]);
        	$imagename[0]["templatespdf"]  = str_replace("[STUDENT_COMPANY]", $firstnamelastname[0]["company"], $imagename[0]["templatespdf"]);

			while (ob_get_level())
			ob_end_clean();
			header("Content-Encoding: None", true);
			
			if(strlen($imagename[0]["design_text_color"]) == 3) {
			  $r = hexdec(substr($imagename[0]["design_text_color"],0,1).substr($imagename[0]["design_text_color"],0,1));
			  $g = hexdec(substr($imagename[0]["design_text_color"],1,1).substr($imagename[0]["design_text_color"],1,1));
			  $b = hexdec(substr($imagename[0]["design_text_color"],2,1).substr($imagename[0]["design_text_color"],2,1));
		   	} else {
			  $r = hexdec(substr($imagename[0]["design_text_color"],0,2));
			  $g = hexdec(substr($imagename[0]["design_text_color"],2,2));
			  $b = hexdec(substr($imagename[0]["design_text_color"],4,2));
			}

			$background_color_original = $background_color;
			$background_color = explode(":", $background_color);
			@$background_color[1] = str_replace("#", "", $background_color[1]);
			
			if(strlen($background_color[1]) == 3){
				$rg = hexdec(substr($background_color[1],0,1).substr($background_color[1],0,1));
				$gg = hexdec(substr($background_color[1],1,1).substr($background_color,1,1));
				$bg = hexdec(substr($background_color[1],2,1).substr($background_color[1],2,1));
		   	}
		   	else{
				$rg = hexdec(substr($background_color[1],0,2));
				$gg = hexdec(substr($background_color[1],2,2));
				$bg = hexdec(substr($background_color[1],4,2));
		   	}
		   
			if($imagename[0]["library_pdf"] == 0){
				require_once (JPATH_SITE."/components/com_guru/helpers/fpdf.php");
				$pdf = new PDF('L', 'mm', 'A5');
				
				$not_available_font = array("Arial", "Tahoma", "Times New Roman", "Verdana", "Georgia", "Palatino Linotype", "Arial Black", "Comic Sans MS", "Lucida Console", "sun-extA");

				if(!in_array($imagename[0]["font_certificate"], $not_available_font)){
					$pdf->AddFont($imagename[0]["font_certificate"]);
				}

				$pdf->SetFont($imagename[0]["font_certificate"], '', 12);
				$pdf->SetTextColor($r, $g, $b);
				//set up a page
				$pdf->AddPage();
		
				if($image_theme !=""){
					$pdf->Image($certificate_path.$image_theme, -4, -1, 210, 150);
					//$pdf->Cell(0,75,JText::_("GURU_CERTIFICATE_OF_COMPLETION"),0,1,'C');
				}
				else{
					$pdf->SetFillColor($rg, $gg, $bg);
					//$pdf->Cell(0,115,JText::_("GURU_CERTIFICATE_OF_COMPLETION"),0,1,'C',true);
					$imagename[0]["templatespdf"] = '<div style="'.$background_color_original.'">'.$imagename[0]["templatespdf"].'</div>';
				}

				$pdf->Ln(20);
				$pdf->SetXY(100,50);
				$pdf->WriteHTML(iconv('UTF-8', 'ISO-8859-1', $imagename[0]["templatespdf"]), true);
				$pdf->Output('certificate'.$certificateid.'.pdf', 'D');
			}
			else{
				error_reporting(0);
				ini_set('display_errors','Off');

				require (JPATH_SITE."/components/com_guru/helpers/MPDF/mpdf.php");
				$pdf = new mPDF('utf-8','A4-L', 0, strtolower($imagename[0]["font_certificate"]));
				$imagename[0]["templatespdf"] = '<style> body { font-family:"'.strtolower($imagename[0]["font_certificate"]).'" ; color: rgb('.$r.', '.$g.', '.$b.'); }</style>'.$imagename[0]["templatespdf"];
				
				
				//set up a page
				$pdf->AddPage('L');
		
				if($image_theme !=""){
					$pdf->Image($certificate_path.$image_theme, 0, 0, 298, 210, 'jpg', '', true, false);
					//$pdf->Cell(0,75,JText::_("GURU_CERTIFICATE_OF_COMPLETION"),0,1,'C');
				}
				else{
					$pdf->SetFillColor($rg, $gg, $bg);
					//$pdf->Cell(0,115,JText::_("GURU_CERTIFICATE_OF_COMPLETION"),0,1,'C',true);
					$imagename[0]["templatespdf"] = '<div style="'.$background_color_original.'">'.$imagename[0]["templatespdf"].'</div>';
				}

				//$pdf->Ln(20);
				$pdf->SetXY(100,50);
				$pdf->SetDisplayMode('fullpage');  
				$pdf->WriteHTML($imagename[0]["templatespdf"]);
				$pdf->Output('certificate'.$certificateid.'.pdf','D'); 
				exit;
			}

		}
		else{
			$db = JFactory::getDBO();
			$user = JFactory::getUser();
			$config = JFactory::getConfig();
			$imagename = "SELECT * FROM #__guru_certificates WHERE id=1";
			$db->setQuery($imagename);
			$db->execute();
			$imagename = $db->loadAssocList();
			
			if($imagename[0]["design_background"] !=""){
				$image_theme = explode("/", $imagename[0]["design_background"]);
				$image_theme = $image_theme[count($image_theme) - 1];
			}	
			else{
				$background_color= "background-color:"."#".$imagename[0]["design_background_color"];
			}

			$datetype = "SELECT `datetype` from #__guru_config WHERE id=1";
            $db->setQuery($datetype);
			$db->execute();
            $dateformat = $db->loadResult();
			
			$site_url = JURI::root();
			$coursename = $datac['cn'];
			$authorname = $datac['an'];
			$certificateid = $datac['id'];
			$completiondate = $datac['cd'];
			$completiondate = date($dateformat, strtotime($completiondate));
			$course_id = $datac['ci'];

			$sitename = $config->get('config.sitename');
			$user_id = $user->id;

			$sql = "select `custom_certificate` from #__guru_program where `id`=".intval($course_id);
			$db->setQuery($sql);
			$db->execute();
			$custom_certificate = $db->loadColumn();
			$custom_certificate = @$custom_certificate["0"];

			if(intval($custom_certificate) == 1){
			    $sql = "SELECT * FROM #__guru_certificates WHERE `course_id`=".intval($course_id);
			    $db->setQuery($sql);
			    $db->execute();
			    $course_imagename = $db->loadAssocList();

			    if(isset($course_imagename) && isset($course_imagename["0"]) && count($course_imagename) > 0){
			        foreach ($course_imagename["0"] as $key => $value){
			            //if(trim($value) != ""){
			                $imagename["0"][$key] = $value;
			           	//}
			        }
			    }

			    if($imagename[0]["design_background"] !=""){
					$image_theme = explode("/", $imagename[0]["design_background"]);
					$image_theme = $image_theme[count($image_theme) - 1];
				}	
				else{
					$image_theme = "";
					$background_color= "background-color:"."#".$imagename[0]["design_background_color"];
				}
			}
			
			$scores_avg_quizzes =  guruModelguruTask::getAvgScoresQ($user_id, $course_id);

			$avg_quizzes_cert = "SELECT avg_certc FROM #__guru_program WHERE id=".intval($course_id );
			$db->setQuery($avg_quizzes_cert);
			$db->execute();
			$avg_quizzes_cert = $db->loadResult();


			$sql = "SELECT id_final_exam FROM #__guru_program WHERE id=".intval($course_id );
			$db->setQuery($sql);
			$result = $db->loadResult();
	
			$sql = "SELECT hasquiz from #__guru_program WHERE id=".intval($course_id );
			$db->setQuery($sql);
			$resulthasq = $db->loadResult();
	
			$sql = "SELECT max_score FROM #__guru_quiz WHERE id=".intval($result);
			$db->setQuery($sql);
			$result_maxs = $db->loadResult();
	
			$sql = "SELECT id, score_quiz, time_quiz_taken_per_user  FROM #__guru_quiz_taken_v3 WHERE user_id=".intval($user_id)." and quiz_id=".intval($result)." and pid=".intval($course_id )." ORDER BY id DESC LIMIT 0,1";
			$db->setQuery($sql);
			$result_q = $db->loadObject();
	
			$first= explode("|", @$result_q->score_quiz);
	
			@$res = intval(($first[0]/$first[1])*100);
	
			if($resulthasq == 0 && $scores_avg_quizzes == ""){
				$avg_certc1 = "N/A";
			}
			elseif($resulthasq != 0 && $scores_avg_quizzes == ""){
				$avg_certc1 = "N/A";
			}
			elseif($resulthasq != 0 && isset($scores_avg_quizzes)){
			if($scores_avg_quizzes >= intval($avg_quizzes_cert)){
				$avg_certc1 =  $scores_avg_quizzes.'%'; 
			}
			else{
				$avg_certc1 = $scores_avg_quizzes.'%';
			}
		}
	
		if($result !=0 && $res !="" ){
			if( $res >= $result_maxs){
				$avg_certc = $res.'%';
			}
			elseif($res < $result_maxs){
				$avg_certc = $res.'%';
			}
		}
		elseif(($result !=0 && $result !="")){
			$avg_certc = "N/A";
		}
		elseif($result ==0 || $result ==""){
			$avg_certc = "N/A";
		}
			
			$firstnamelastname = "SELECT firstname, lastname, company FROM #__guru_customer WHERE id=".intval($user_id);
			$db->setQuery($firstnamelastname);
			$db->execute();
			$firstnamelastname = $db->loadAssocList();
			
			$coursemsg = "SELECT certificate_course_msg FROM #__guru_program WHERE id=".intval($course_id);
			$db->setQuery($coursemsg);
			$db->execute();
			$coursemsg = $db->loadResult();
						

			$certificate_url = JUri::base()."index.php?option=com_guru&view=guruOrders&task=printcertificate&opt=".$certificateid."&cn=".$coursename."&an=".$authorname."&cd=".$completiondate."&id=".$certificateid;
			$certificate_url = str_replace(" ", "%20", $certificate_url);

			$certificate_url = '<a href="'.$certificate_url.'" target="_blank">'.JText::_("GURU_URL_CERTIFICATE").'</a>';
			
			$imagename[0]["templates1"]  = str_replace("[SITENAME]", $sitename, $imagename[0]["templates1"]);
			$imagename[0]["templates1"]  = str_replace("[STUDENT_FIRST_NAME]", $firstnamelastname[0]["firstname"], $imagename[0]["templates1"]);
			$imagename[0]["templates1"]  = str_replace("[STUDENT_LAST_NAME]", $firstnamelastname[0]["lastname"], $imagename[0]["templates1"]);
			$imagename[0]["templates1"]  = str_replace("[SITEURL]", $site_url, $imagename[0]["templates1"]);
			$imagename[0]["templates1"]  = str_replace("[CERTIFICATE_ID]", $certificateid, $imagename[0]["templates1"]);
			$imagename[0]["templates1"]  = str_replace("[COMPLETION_DATE]", $completiondate, $imagename[0]["templates1"]);
			$imagename[0]["templates1"]  = str_replace("[COURSE_NAME]", $coursename, $imagename[0]["templates1"]);
			$imagename[0]["templates1"]  = str_replace("[AUTHOR_NAME]", $authorname, $imagename[0]["templates1"]);
			$imagename[0]["templates1"]  = str_replace("[CERT_URL]", $certificate_url, $imagename[0]["templates1"]);
			$imagename[0]["templates1"]  = str_replace("[CERTIFICATE_URL]", $certificate_url, $imagename[0]["templates1"]);
			$imagename[0]["templates1"]  = str_replace("[COURSE_MSG]", $coursemsg, $imagename[0]["templates1"]);
			$imagename[0]["templates1"]  = str_replace("[COURSE_AVG_SCORE]", $avg_certc1, $imagename[0]["templates1"]);
        	$imagename[0]["templates1"]  = str_replace("[COURSE_FINAL_SCORE]", $avg_certc, $imagename[0]["templates1"]);
        	$imagename[0]["templates1"]  = str_replace("[STUDENT_COMPANY]", $firstnamelastname[0]["company"], $imagename[0]["templates1"]);
		
			while (ob_get_level())
			ob_end_clean();
			header("Content-Encoding: None", true);
			
			if(strlen($imagename[0]["design_text_color"]) == 3) {
			  $r = hexdec(substr($imagename[0]["design_text_color"],0,1).substr($imagename[0]["design_text_color"],0,1));
			  $g = hexdec(substr($imagename[0]["design_text_color"],1,1).substr($imagename[0]["design_text_color"],1,1));
			  $b = hexdec(substr($imagename[0]["design_text_color"],2,1).substr($imagename[0]["design_text_color"],2,1));
		   } else {
			  $r = hexdec(substr($imagename[0]["design_text_color"],0,2));
			  $g = hexdec(substr($imagename[0]["design_text_color"],2,2));
			  $b = hexdec(substr($imagename[0]["design_text_color"],4,2));
			}
			$background_color = explode(":",$background_color );
			$background_color[1]=str_replace("#", "", $background_color[1]);
			
			if(strlen($background_color[1] ) == 3) {
			  $rg = hexdec(substr($background_color[1],0,1).substr($background_color[1],0,1));
			  $gg = hexdec(substr($background_color[1],1,1).substr($background_color,1,1));
			  $bg = hexdec(substr($background_color[1],2,1).substr($background_color[1],2,1));
		   } else {
			  $rg = hexdec(substr($background_color[1],0,2));
			  $gg = hexdec(substr($background_color[1],2,2));
			  $bg = hexdec(substr($background_color[1],4,2));
		   }
			
			if($imagename[0]["library_pdf"] == 0){
				require (JPATH_SITE.DIRECTORY_SEPARATOR."components".DIRECTORY_SEPARATOR."com_guru".DIRECTORY_SEPARATOR."helpers".DIRECTORY_SEPARATOR."fpdf.php");
				
				$pdf = new PDF('L', 'mm', 'A5');
		
				$pdf->SetFont($imagename[0]["font_certificate"],'',12);
				$pdf->SetTextColor($r,$g,$b);
				
				//set up a page
				$pdf->AddPage();
		
				if($image_theme !=""){
					$pdf->Image($certificate_path.$image_theme,-4,-1,210, 150);
					//$pdf->Cell(0,75,JText::_("GURU_CERTIFICATE_OF_COMPLETION"),0,1,'C');
		
				}
				else{
					$pdf->SetFillColor($rg,$gg,$bg);
					//$pdf->Cell(0,115,JText::_("GURU_CERTIFICATE_OF_COMPLETION"),0,1,'C',true);
		
				}
				$pdf->Ln(20);
				$pdf->SetXY(100,50);
				$pdf->WriteHTML(iconv('UTF-8', 'ISO-8859-1', $imagename[0]["templates1"]),true);
				$pdf->Output('certificate'.$certificateid.'.pdf','D'); 
			}
			else{
				require (JPATH_SITE.DIRECTORY_SEPARATOR."components".DIRECTORY_SEPARATOR."com_guru".DIRECTORY_SEPARATOR."helpers".DIRECTORY_SEPARATOR."MPDF".DIRECTORY_SEPARATOR."mpdf.php");
				$pdf = new mPDF('utf-8','A4-L');
				$imagename[0]["templates1"] = '<style> body { font-family:"'.strtolower ($imagename[0]["font_certificate"]).'" ; color: rgb('.$r.', '.$g.', '.$b.'); }</style>'.$imagename[0]["templates1"];
				
				
				//set up a page
				$pdf->AddPage('L');
		
				if($image_theme !=""){
					$pdf->Image($certificate_path.$image_theme,0,0,298, 210, 'jpg', '', true, false);
					//$pdf->Cell(0,75,JText::_("GURU_CERTIFICATE_OF_COMPLETION"),0,1,'C');
					
		
				}
				else{
					$pdf->SetFillColor($rg,$gg,$bg);
					//$pdf->Cell(0,115,JText::_("GURU_CERTIFICATE_OF_COMPLETION"),0,1,'C',true);
		
				}
				//$pdf->Ln(20);
				$pdf->SetXY(100,50);
				$pdf->SetDisplayMode('fullpage');  
				$pdf->WriteHTML($imagename[0]["templates1"]);
				$pdf->Output('certificate'.$certificateid.'.pdf','D'); 
				exit;
			} 

			}
	}
	
function sendemailcertificate(){
	$datace = JFactory::getApplication()->input->post->getArray();
	$user = JFactory::getUser();
	include(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_guru'.DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'gurubuy.php');
	
	$config = JFactory::getConfig();
	$from = $config->mailfrom;
	$fromname = $config->fromname;
	$guru_configs = guruModelguruBuy::getConfigs();
	$db = JFactory::getDBO();
	
	$imagename = "SELECT subjectt4 FROM #__guru_certificates WHERE id=1";
	$db->setQuery($imagename);
	$db->execute();
	$imagename = $db->loadResult();
	
	if(isset($guru_configs["0"]["fromname"]) && trim($guru_configs["0"]["fromname"]) != ""){
		$fromname = trim($guru_configs["0"]["fromname"]);
	}
	if(isset($guru_configs["0"]["fromemail"]) && trim($guru_configs["0"]["fromemail"]) != ""){
		$from = trim($guru_configs["0"]["fromemail"]);
	}
	
	$imagename = "SELECT * FROM #__guru_certificates WHERE id=1";
	$db->setQuery($imagename);
	$db->execute();
	$imagename = $db->loadAssocList();
	
	$site_url = JURI::root();
	$coursename = $datace['cn'];
	$authorname = $datace['an'];
	$certificateid = $datace['id'];
	$completiondate = $datace['cd'];
	$completiondate = date("Y-m-d", strtotime($completiondate));
	$course_id =  $datace['ci'];

	$sql = "select `custom_certificate` from #__guru_program where `id`=".intval($course_id);
	$db->setQuery($sql);
	$db->execute();
	$custom_certificate = $db->loadColumn();
	$custom_certificate = @$custom_certificate["0"];

	if(intval($custom_certificate) == 1){
	    $sql = "SELECT * FROM #__guru_certificates WHERE `course_id`=".intval($course_id);
	    $db->setQuery($sql);
	    $db->execute();
	    $course_imagename = $db->loadAssocList();

	    if(isset($course_imagename) && isset($course_imagename["0"]) && count($course_imagename) > 0){
	        foreach ($course_imagename["0"] as $key => $value){
	            //if(trim($value) != ""){
	                $imagename["0"][$key] = $value;
	            //}
	        }
	    }
	}

	$sitename = $config->get('sitename');
	
	$user_id = $user->id;			
	$firstnamelastname = "SELECT firstname, lastname, company FROM #__guru_customer WHERE id=".intval($user_id);
	$db->setQuery($firstnamelastname);
	$db->execute();
	$firstnamelastname = $db->loadAssocList();
	
	$coursemsg = "SELECT certificate_course_msg FROM #__guru_program WHERE id=".intval($course_id);
	$db->setQuery($coursemsg);
	$db->execute();
	$coursemsg = $db->loadResult();
	
	$avg_certc = "SELECT avg_certc FROM #__guru_program WHERE id=".intval($course_id);
	$db->setQuery($avg_certc);
	$db->execute();
	$avg_certc = $db->loadResult()."%";
	
	$certificate_url = JUri::base()."index.php?option=com_guru&view=guruOrders&task=printcertificate&opt=".$certificateid."&cn=".$coursename."&an=".$authorname."&cd=".$completiondate."&id=".$certificateid."&ct=".$user_id;
	$certificate_url = str_replace(" ", "%20", $certificate_url);

	$imagename[0]["templates4"]  = str_replace("[SITENAME]", $sitename, $imagename[0]["templates4"]);
	$imagename[0]["templates4"]  = str_replace("[STUDENT_FIRST_NAME]", $firstnamelastname[0]["firstname"], $imagename[0]["templates4"]);
	$imagename[0]["templates4"]  = str_replace("[STUDENT_LAST_NAME]", $firstnamelastname[0]["lastname"], $imagename[0]["templates4"]);
	$imagename[0]["templates4"]  = str_replace("[CERT_URL]", '<a href="'.$certificate_url.'" target="_blank">'.JText::_("GURU_URL_CERTIFICATE").'</a>', $imagename[0]["templates4"]);
	$imagename[0]["templates4"]  = str_replace("[CERTIFICATE_URL]", '<a href="'.$certificate_url.'" target="_blank">'.JText::_("GURU_URL_CERTIFICATE").'</a>', $imagename[0]["templates4"]);
	$imagename[0]["templates4"]  = str_replace("[CERT_MESSAGE]", $datace["personalmessage"], $imagename[0]["templates4"]);
	$imagename[0]["templates4"]  = str_replace("[SITEURL]", $site_url, $imagename[0]["templates4"]);
	$imagename[0]["templates4"]  = str_replace("[CERTIFICATE_ID]", $certificateid, $imagename[0]["templates4"]);
	$imagename[0]["templates4"]  = str_replace("[COMPLETION_DATE]", $completiondate, $imagename[0]["templates4"]);
	$imagename[0]["templates4"]  = str_replace("[COURSE_NAME]", $coursename, $imagename[0]["templates4"]);
	$imagename[0]["templates4"]  = str_replace("[AUTHOR_NAME]", $authorname, $imagename[0]["templates4"]);
	$imagename[0]["templates4"]  = str_replace("[COURSE_MSG]", $coursemsg, $imagename[0]["templates4"]);
	$imagename[0]["templates4"]  = str_replace("[COURSE_AVG_SCORE]", $avg_certc, $imagename[0]["templates4"]);
    $imagename[0]["templates4"]  = str_replace("[COURSE_FINAL_SCORE]", $avg_certc, $imagename[0]["templates4"]);
    $imagename[0]["templates4"]  = str_replace("[STUDENT_COMPANY]", $firstnamelastname[0]["company"], $imagename[0]["templates4"]);

	$email_body	= $imagename[0]["templates4"];
	
	$recipient = $datace["emails"];
	$recipient = explode(",", $recipient); 
	$mode = true;
	
	$imagename = "SELECT subjectt4 FROM #__guru_certificates WHERE id=1";
	$db->setQuery($imagename);
	$db->execute();
	$imagename = $db->loadResult();

	$imagename  = str_replace("[SITENAME]", $sitename, $imagename);
	$imagename  = str_replace("[STUDENT_FIRST_NAME]", $datace['studentfn'], $imagename);
	$imagename  = str_replace("[STUDENT_LAST_NAME]", $datace['studentln'], $imagename);
	$imagename  = str_replace("[SITEURL]", $site_url, $imagename);
	$imagename  = str_replace("[CERTIFICATE_ID]", $certificateid, $imagename);
	$imagename  = str_replace("[COMPLETION_DATE]", $completiondate, $imagename);
	$imagename  = str_replace("[COURSE_NAME]", $coursename, $imagename);
	$imagename  = str_replace("[AUTHOR_NAME]", $authorname, $imagename);
	$imagename = str_replace("[CERT_MESSAGE]", str_replace("'", "&acute;",$datace["personalmessage"]), $imagename);
				
	$subject_procesed = $imagename;
	$body_procesed = $email_body;

	if(is_array($recipient) && count($recipient) > 0){
		foreach($recipient as $key => $recipient){
			JFactory::getMailer()->sendMail($from, $fromname, $recipient, $subject_procesed, $body_procesed, $mode);
			
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query->clear();
			$query->insert('#__guru_logs');
			$query->columns(array($db->quoteName('userid'), $db->quoteName('emailname'), $db->quoteName('emailid'), $db->quoteName('to'), $db->quoteName('subject'), $db->quoteName('body'), $db->quoteName('buy_date'), $db->quoteName('send_date'), $db->quoteName('buy_type') ));
			$query->values(intval($user_id) . ',' . $db->quote('email-certificate') . ',' . '0' . ',' . $db->quote(trim($recipient)) . ',' . $db->quote(trim($subject_procesed)) . ',' . $db->quote(trim($body_procesed)) . ',' . $db->quote(trim(date("Y-m-d H:i:s"))) . ',' . $db->quote(trim(date("Y-m-d H:i:s"))) . ',' . "''" );
			$db->setQuery($query);
			$db->execute();
		}
	}
	
	echo '
	<script language="javascript" type="text/javascript">
		window.close();
	</script>';
}


};

?>