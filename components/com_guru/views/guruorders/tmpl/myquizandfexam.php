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
JHTML::_('behavior.modal', 'a.modal');
jimport('joomla.html.pagination');
include(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_guru'.DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'gurutask.php');
$document = JFactory::getDocument();
//$document->addScript("components/com_guru/js/programs.js");
$document->setMetaData( 'viewport', 'width=device-width, initial-scale=1.0' );
require_once(JPATH_BASE . "/components/com_guru/helpers/Mobile_Detect.php");

JHtml::_('bootstrap.tooltip');
JHtml::_('dropdown.init');

$db = JFactory::getDBO();
$user = JFactory::getUser();
$user_id = $user->id;
$Itemid = JFactory::getApplication()->input->get("Itemid", "0", "raw");
$search = JFactory::getApplication()->input->get("search_course", "", "raw");
$config = $this->getConfigSettings();
$cid = array();
$guruModelguruOrder = new guruModelguruOrder();
$guruModelguruTask = new guruModelguruTask();

$data_post = JFactory::getApplication()->input->post->getArray();

$certcourseidlist = $guruModelguruOrder->getCourseidsList($user_id);
$certificates_general = $guruModelguruOrder->getCertificate(); 

$document->setTitle(trim(JText::_('GURU_QUIZZ_FINAL_EXAM')));

//$document->addScript('components/com_guru/js/guru_modal.js');

$detect = new Mobile_Detect;
$deviceType = ($detect->isMobile() ? ($detect->isTablet() ? 'tablet' : 'phone') : 'computer');
if($deviceType =="phone"){
	$styledisplay = 'display:inline-block !important;';
	$class_title = 'class="guruml20"';
}
else{
	$styledisplay = '';
	$class_title = 'class="guruml20"';
}

include_once(JPATH_SITE.DIRECTORY_SEPARATOR."components".DIRECTORY_SEPARATOR."com_guru".DIRECTORY_SEPARATOR."helpers".DIRECTORY_SEPARATOR."helper.php");
$helper = new guruHelper();
$div_menu = $helper->createStudentMenu();
$page_title_cart = $helper->createPageTitleAndCart();

//$document->addScript('components/com_guru/js/guru_modal.js');
JHtml::stylesheet('components/com_guru/css/tabs.css');

?>

<script type="text/javascript" language="javascript">
	document.body.className = document.body.className.replace("modal", "");
</script>

<div class="gru-myquizzes">
	<form name="adminForm" method="post" id="adminForm" action="<?php echo JUri::getInstance() ?>">
		<?php
			echo $div_menu;
			echo $page_title_cart;
		?>
        
        <div class="gru-page-filters">
        	<div class="gru-filter-item">
                <input type="text" class="form-control uk-form-width-medium wk-form-width-medium" style="margin:0px;" placeholder="<?php echo JText::_("GURU_SEARCH"); ?>" name="search_course" value="<?php if(isset($data_post['search_course'])) echo $data_post['search_course'];?>" >
                <button class="uk-button uk-button-primary wk-button wk-button-primary" type="submit"><?php echo JText::_("GURU_SEARCH"); ?></button>
            </div>
            
            <div class="gru-filter-item">
                <select class="uk-form-width-small wk-form-width-small" style="margin:0px;" name="selectcoursesd" id="selectcoursesd" onchange="document.adminForm.submit();" >
                     <option value="0" <?php if(@$psd == "0"){echo 'selected="selected"';} ?>><?php echo JText::_("GURU_SELECT_COURSE");?></option>
    
                    <?php
                     if(isset($data_post['search_course']) && $data_post['search_course'] !="" ){
                        $search_id  ="SELECT id FROM #__guru_program where name like '%".$data_post['search_course']."%' LIMIT 0,1";
                        $db->setQuery($search_id);
                        $db->execute();
                        $search_id = $db->loadResult();
                        
                        
                        $psd=search_id;
                     }
                     if(isset($data_post['selectcoursesd'])){
                        $psd=$data_post['selectcoursesd'];
                     }
                     if(isset($data_post['selecttyped'])){
                       $ptd=$data_post['selecttyped'];
                     }
                     if(isset($data_post['selectstatus'])){
                       $pcd=$data_post['selectstatus'];
                     }
                     if(!isset($psd)) {$psd=NULL;}
                     if(!isset($ptd)) {$ptd=NULL;}
                     if(!isset($pcd)) {$pcd=NULL;}
                    ?>
    
             <?php
                    $cidd = "SELECT distinct pid from #__guru_quiz_taken_v3 where user_id=".intval($user_id);
                    $db->setQuery($cidd);
                    $cidd= $db->loadAssocList();
    
                        foreach($cidd as $key => $values){						
                            $course_id = $values["pid"];
                            $sql = "SELECT name FROM #__guru_program WHERE id=".intval($course_id);		
                            $db->setQuery($sql);
                            $db->execute();
                            $result_name = $db->loadResult();
                            if($psd == $course_id){
                                $selected = 'selected="selected"';
                            }
                            else {
                                $selected = '';
                            }
                            ?>			
                    <option value="<?php echo $course_id;?>"<?php echo $selected ; ?>><?php echo $result_name; ?></option>
                    <?php
                        }
                    ?>
                </select>
           </div>
           
			<div class="gru-filter-item"> 
                <select class="uk-form-width-small wk-form-width-small" style="margin:0px;" name="selecttyped" id="selecttyped" onchange="document.adminForm.submit();" >
                    <option value="0" <?php if($ptd == "0"){echo 'selected="selected"';} ?>><?php echo JText::_("GURU_SELECT_TYPE");?></option>
                    <option value="1" <?php if($ptd == "1"){echo 'selected="selected"';} ?>><?php echo JText::_("GURU_QUIZ");?></option>
                    <option value="2" <?php if($ptd == "2"){echo 'selected="selected"';} ?>><?php echo JText::_("GURU_FQUIZ");?></option>
                </select>
			</div>
            
            <div class="gru-filter-item">
                <select class="uk-form-width-small wk-form-width-small" style="margin:0px;" name="selectstatus" id="selectstatus" onchange="document.adminForm.submit();" >
                    <option value="0" <?php if($pcd == "0"){echo 'selected="selected"';} ?>><?php echo JText::_("GURU_SELECT_STATUS");?></option>
                    <option value="1" <?php if($pcd == "1"){echo 'selected="selected"';} ?>><?php echo JText::_("GURU_QUIZ_PASSED_STATUS");?></option>
                    <option value="2" <?php if($pcd == "2"){echo 'selected="selected"';} ?>><?php echo JText::_("GURU_QUIZ_FAILED_STATUS");?></option>
                </select>
			</div>
        </div>
        
        <table class="uk-table uk-table-striped wk-table wk-table-striped">
            <tr>
                <th class="g_cell_1"><?php echo JText::_("GURU_DAYS_NAME"); ?></th>
                <th class="g_cell_2 g_hide_mobile"><?php echo JText::_("GURU_PROGRAM_PROGRAMS"); ?></th>
                <th class="g_cell_3 g_hide_mobile g_hide_small_size"><?php echo JText::_("GURU_TYPE"); ?></th>
                <th class="g_cell_4 g_hide_mobile"><?php echo JText::_("GURU_DATE_TAKEN"); ?></th>
                <th class="g_cell_5"><?php echo JText::_("GURU_ATTEPMT_Q"); ?></th>
                <th class="g_cell_6 g_hide_mobile"><?php echo JText::_("GURU_PASSING_SCORE"); ?></th>
                <th class="g_cell_7 "><?php echo JText::_("GURU_MYSCORE"); ?></th>
            </tr>
            
                <?php
                    $lang = JFactory::getLanguage()->getTag();
                    $lang = explode("-", $lang);
                    $lang = @$lang["0"];

                    $k = 0;
                    $hascertificate = false;
                    $already_edited = array();
                    $db	= JFactory::getDBO();
                    $datetype = "SELECT datetype from #__guru_config WHERE id=1";
                    $db->setQuery($datetype);
                    $db->execute();
                    $datetype = $db->loadResult();
                    
                    $avg_quizzes_cert = "SELECT avg_cert from #__guru_certificates WHERE id=1";
                    $db->setQuery($avg_quizzes_cert);
                    $db->execute();
                    $avg_quizzes_cert = $db->loadResult();
                    
                    if(isset($ps) && $ps!=0 && $ps != NULL){
                        $cidd = $ps;
                    }
                    else{
                        $selectcoursesd = JFactory::getApplication()->input->get("selectcoursesd", "0");
                        $and = "";
                        if(intval($selectcoursesd) != 0){
                            $and .= " and pid=".intval($selectcoursesd);
                        }
                        
                        $sql = "SELECT pid from #__guru_quiz_taken_v3 where user_id=".intval($user_id).$and;
                        $db->setQuery($sql);
                        $db->execute();
                        $cid = $db->loadColumn();
                    }
                    
                    $search = JFactory::getApplication()->input->get("search_course", "", "raw");
                    $selectcoursesd = JFactory::getApplication()->input->get("selectcoursesd", "0", "raw");
                    $and = "";
                    if(trim($search) != ""){
                        $and .= " and q.name like '%".addslashes(trim($search))."%'";
                    }
                    
                    if(intval($selectcoursesd) != 0){
                        $and .= " and qz.pid=".intval($selectcoursesd);
                    }
                    
                    $sql =  "SELECT q.id, q.name, q.time_quiz_taken, q.is_final, qz.pid FROM  #__guru_quiz_taken_v3 qz INNER JOIN  #__guru_quiz q ON (qz.quiz_id = q.id) WHERE user_id = ".intval($user_id)." ".$and;
                    
                    if(isset($ps) && $ps != 0 && $ps != NULL ){
                        $sql .= " and qz.pid=".$ps;
                    }
                    if(isset($pt) && $pt != 0 && $pt != NULL ){
                        if($pt == 1){
                            $sql .= " and q.is_final=0";
                        }
                        elseif($pt == 2){
                            $sql .= " and q.is_final=1";
                        }
                    }
                     if(isset($psd) && $psd != 0 && $psd != NULL ){
                        $sql .= " and qz.pid=".$psd;
                    }
                    if(isset($ptd) && $ptd != 0 && $ptd != NULL ){
                        if($ptd == 1){
                            $sql .= " and q.is_final=0";
                        }
                        elseif($ptd == 2){
                            $sql .= " and q.is_final=1";
                        }
                    }
                    
                    $sql .= " group by q.id, q.name, q.time_quiz_taken, q.is_final, qz.pid ORDER BY qz.id DESC";
                    
                    $db->setQuery($sql);
                    $db->execute();
                    $my_quizzes = $db->loadAssocList();	
                    $total_rows = 0;
                    
                    $limit_request = JFactory::getApplication()->input->get("limit", "-1");
					
					$session = JFactory::getSession();
					$registry = $session->get('registry');
					
                    if($limit_request == -1){
                        $config = new JConfig();
                        						
						$quiz_limit = $registry->get('quiz_limit', NULL);
						
						if(isset($quiz_limit)){
                            $limit = $quiz_limit;
                        }
                        else{
                            $limit = $config->list_limit;
							$registry->set('quiz_limit', $limit);
                        }
                    }
                    else{
						$registry->set('quiz_limit', $limit_request);
                        $limit = $limit_request;
                    }
                    
                    $limitstart = JFactory::getApplication()->input->get("limitstart", 0);
                    $row = 0;
                    
                    foreach($my_quizzes as $key=>$value){
                        $class = "odd";
                        if($k%2 != 0){
                            $class = "even";
                        }
                        
                        $id = $my_quizzes[$key]["id"];
                        
                        $val = $my_quizzes[$key]["pid"];
    
                        $sql = "SELECT id_final_exam FROM #__guru_program WHERE id=".intval($val);
                        $db->setQuery($sql);
                        $result = $db->loadResult();
                        
                        $sql = "SELECT hasquiz from #__guru_program WHERE id=".intval($val);
                        $db->setQuery($sql);
                        $resulthasq = $db->loadResult();
                        
                        $sqlm = "SELECT max_score FROM #__guru_quiz WHERE id=".intval($id);
                        $db->setQuery($sqlm);
                        $result_maxs = $db->loadResult();

                        $sql = "SELECT p.`name`, p.`published`, c.`name` as category_name from #__guru_program p, #__guru_category c WHERE p.`id` =".intval($val)." and p.`catid`=c.`id`";
                        $db->setQuery($sql);
                        $db->execute();
                        $result = $db->loadAssocList();
                        $coursename = $result["0"]["name"];
                        $published = $result["0"]["published"];
                        $category_name = $result["0"]["category_name"];
                        
                        if($my_quizzes[$key]["is_final"] == 1){
                            $type =  JText::_("GURU_FQUIZ");
                        }
                        else{
                            $type = JText::_("GURU_QUIZ");
                        }
                        
                        $sql = "SELECT time_quiz_taken FROM #__guru_quiz WHERE id=".intval($id);
                        $db->setQuery($sql);
                        $time_quiz_taken = $db->loadResult();
						$time_quiz_taken_int = $time_quiz_taken;
                        
                        $sql = "SELECT * from #__guru_quiz_question_taken_v3 where user_id=".intval($user_id)." and quiz_id=".intval($id)." and pid=".intval($val)." order by date_taken_quiz desc";
                        
                        $db->setQuery($sql);
                        $quiz_taken_rows = $db->loadAssocList();
                        
                        if($my_quizzes[$key]["is_final"] == 0){
                            $my_quizzes[$key]["is_final"] = "1";
                        }
                        elseif($my_quizzes[$key]["is_final"] == 1){
                            $my_quizzes[$key]["is_final"] = "2";
                        }
                        
                        if(count($quiz_taken_rows) > 0){
                            $poz = count($quiz_taken_rows);
							
							foreach($quiz_taken_rows as $key_row=>$value_row){
								$selecttyped = JFactory::getApplication()->input->get("selecttyped", "0");
                                
                                if($selecttyped != 0 && $selecttyped != $my_quizzes[$key]["is_final"]){
                                    continue;
                                }
                                
                                $date_taken =  date($datetype, strtotime($value_row["date_taken_quiz"]));
                                $res = $value_row["score_quiz"];
                        
								// start check if quiz is not marked by teacher
								$quiz_graded = true;
								$questions = $value_row["question_ids"];
								
								if(trim($questions) == ""){
									$questions = "0";
								}
								
								$student = $value_row["user_id"];
								$quiz_id = $value_row["quiz_id"];
								
								$sql = "select id from #__guru_questions_v3 where id in (".trim($questions).") and type='essay'";
								$db->setQuery($sql);
								$db->execute();
								$essay_questions = $db->loadColumn();
								
								if(isset($essay_questions) && count($essay_questions) > 0){
									foreach($essay_questions as $key_essay=>$essay_question){
										$sql = "select count(*) from #__guru_quiz_essay_mark where question_id=".intval($essay_question)." and user_id=".intval($student);
										$db->setQuery($sql);
										$db->execute();
										$count = $db->loadColumn();
										$count = @$count["0"];
										
										if(intval($count) == 0){
											$quiz_graded = false;
											break;
										}
									}
								}
								// stop check if quiz is not marked by teacher
								
								if(!$quiz_graded){
									$passfail = '<span style="color:#F89406;"> '.JText::_("GURU_QUIZ_PENDING").'</span>'; 
                                    $pcolor='color:#F89406;';
								}
                                elseif($res >= $result_maxs){
                                    $selectstatus = JFactory::getApplication()->input->get("selectstatus", "0");
                                    if($selectstatus != 0 && $selectstatus == 2){
                                        continue;
                                    }
                                    
                                    $passfail = '<span  style="color:#66CC00;"> '.JText::_("GURU_QUIZ_PASSED").'</span>'; 
                                    $pcolor='color:#66CC00;';
                                }
                                else{
                                    if(@$selectstatus != 0 && @$selectstatus == 1){
                                        continue;
                                    }
                                
                                    $passfail = '<span  style="color:#FF0000;"> '.JText::_("GURU_QUIZ_FAILED").'</span>'; 
                                    $pcolor='color:#FF0000;';
                                }
                                
                                if( ($row >= $limitstart && $row < $limitstart + $limit) || $limit == 0){
                        ?>
                                <tr>
                                    <td class="g_cell_1"><?php echo $my_quizzes[$key]["name"];?></td>
                                    <td class="g_cell_2 g_hide_mobile"> 
                                    <?php
                                        if($published == 0){
                                            echo $coursename;
                                        }
                                        else{
                                            $helper = new guruHelper();
                                            $itemid_menu = $helper->getCourseMenuItem(intval($value["pid"]));
                                            $itemid_course = $Itemid;

                                            if(intval($itemid_menu) > 0){
                                                $itemid_course = intval($itemid_menu);
                                            }
                                    ?>
                                            <a href="<?php echo JRoute::_("index.php?option=com_guru&view=guruPrograms&task=view&cid=".intval($value["pid"])."-".@$alias."&Itemid=".intval($itemid_course)); ?>">
                                                <?php echo $coursename; ?>
                                            </a>
                                    <?php
                                        }
                                        
                                        if($time_quiz_taken == 11){
                                            $time_quiz_taken = JText::_("GURU_UNLIMITED");
                                        }

                                        echo "<br />";
                                        echo JText::_("GURU_CATEGORY"); ?>: <?php echo $category_name;
                                    ?>
                                    </td>
                                    <td class="g_cell_3 g_hide_mobile g_hide_small_size"><?php echo $type; ?></td>
                                    <td class="g_cell_4 g_hide_mobile"><?php echo $date_taken; ?></td>
                                    <td class="g_cell_5">
										<?php
											$retake_button = false;
											
											if($key_row == 0){
												if($res < $result_maxs){
													// quiz not passed
													if($time_quiz_taken_int == 11){
														// umlimited
														$retake_button = true;
													}
													elseif($poz < $time_quiz_taken_int){
														$retake_button = true;
													}
												}
											}
											
                                        	echo $poz."/".$time_quiz_taken;
											
											if($retake_button === true){
												echo '<br /> <input type="button" class="uk-button uk-button-success uk-button-small wk-button wk-button-success wk-button-small" value="'.JText::_("GURU_RETAKE").'" onclick="window.location.href=\''.JRoute::_("index.php?option=com_guru&view=guruPrograms&task=view&cid=".intval($value["pid"])."-".@$alias."&quiz=".intval($id)).'\';" />';
											}
										?>
									</td>
                                    <td class="g_cell_6 g_hide_mobile"><?php echo $result_maxs."%"; ?></td>
                                    <td class="g_cell_7 ">
                                        <a class="g_hide_mobile" onclick="javascript:openMyModal(0, 0, '<?php echo JURI::root().'index.php?option=com_guru&view=guruauthor&task=student_quizdetails&layout=student_quizdetails&pid='.intval($value['pid']).'&userid='.intval($user_id).'&quiz='.intval($value_row['quiz_id']).'&id='.intval($value_row['id']).'&tmpl=component&lang='.$lang; ?>');" href="javascript:;">
                                            <?php echo $res."%".$passfail;?>
                                        </a>
                                    </td>
                                </tr>
                <?php
                                }
                                $total_rows ++;
                                $row ++;
                                $poz--;
                            }
                        }
                        $k++;
                    }
                ?>
        </table>
        
        <table>
            <tr>
                <td>
                    <?php
                        $config = new JConfig();
                        
                        $pagination = new JPagination(NULL, 0, 5);
                        
						$session = JFactory::getSession();
						$registry = $session->get('registry');
						$limit = $registry->get('quiz_limit', "");
						
                        $limitstart = JFactory::getApplication()->input->get("limitstart", 0);
                        $total = $total_rows;
                        $pagesStart = 0;
                        $pagesStop = 0;
                        $pagesCurrent = 0; 
                        $pagesTotal = 0;
                        
                        if($total > $limitstart){
                            $pagesTotal = @ceil($total / $limit);
                            if($pagesTotal <= 10){
                                $pagesStart = 1;
                                $pagesStop = $pagesTotal;
                                $pagesCurrent = @($limitstart / $limit) + 1;
                            }
                            else{
                                $pagesCurrent = ($limitstart / $limit) + 1;
                                if($pagesCurrent - 5 > 1){
                                    $pagesStart = $pagesCurrent - 5;
                                    if($pagesCurrent + 4 >= $pagesTotal){
                                        $pagesStop = $pagesTotal;
                                    }
                                    else{
                                        $pagesStop = $pagesCurrent + 4;
                                    }
                                }
                                else{
                                    $pagesStart = 1;
                                    if($pagesTotal <= 10){
                                        $pagesStop = $pagesTotal;
                                    }
                                    else{
                                        $pagesStop = 10;
                                    }
                                }
                            }
                        }
                        
                        $pagination->limitstart = $limitstart;
                        $pagination->limit = $limit;
                        $pagination->total = $total;
                        $pagination->pagesStart = $pagesStart;
                        $pagination->pagesStop = $pagesStop;
                        $pagination->pagesCurrent = $pagesCurrent;
                        $pagination->pagesTotal = $pagesTotal;
                        
                        echo $pagination->getListFooter();
                    ?>
                </td>
            </tr>
        </table>
       
        <input type="hidden" name="option" value="com_guru" />
        <input type="hidden" name="controller" value="guruOrders" />
        <input type="hidden" name="view" value="guruOrders" />
        <input type="hidden" name="task" value="myquizandfexam" />
	</form>
</div>
<script type="text/javascript" language="javascript">
	window.onload=function(){
        var $limit = document.getElementById("limit");
        if ($limit) {
            $limit.value = <?php echo intval($limit); ?>;
        }
	};
</script>