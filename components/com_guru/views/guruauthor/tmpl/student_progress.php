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

defined( '_JEXEC' ) or die( 'Restricted access' );
JHTML::_('behavior.tooltip');

$userid = JFactory::getApplication()->input->get("id", "0");
$student = $this->student;
$student_courses = $this->student_courses;
$config = $this->config;

if(isset($student["0"])){
	$student = $student["0"];
}

require_once(JPATH_SITE.DIRECTORY_SEPARATOR."components".DIRECTORY_SEPARATOR."com_guru".DIRECTORY_SEPARATOR."models".DIRECTORY_SEPARATOR."guruorder.php");
$guruModelguruOrder = new guruModelguruOrder();

$certcourseidlist = $guruModelguruOrder->getCourseidsList($student["user_id"]);

function get_time_difference($start, $end){
	$uts['start'] = $start;
	$uts['end'] = $end;
	
	if( $uts['start'] !== -1 && $uts['end'] !== -1){
		if($uts['end'] >= $uts['start']){
			$diff = $uts['end'] - $uts['start'];
			if($days=intval((floor($diff/86400)))){
				$diff = $diff % 86400;
			}
				
			if($hours=intval((floor($diff/3600)))){
				$diff = $diff % 3600;
			}	
			
			if($minutes=intval((floor($diff/60)))){
				$diff = $diff % 60;
			}	
			$diff = intval($diff);
			return( array('days'=>$days, 'hours'=>$hours, 'minutes'=>$minutes, 'seconds'=>$diff));
		}
		else{
			return false;
		}
	}
	return false;
}

?>

<script type="text/javascript" language="javascript">
    document.body.className = document.body.className.replace("modal", "");

	function openWinCertificate1(t1,t2,t3,t4,t5,t6){
	    t1 = encodeURIComponent(t1);
	    t2 = encodeURIComponent(t2);
	    t3 = encodeURIComponent(t3);
	    t4 = encodeURIComponent(t4);
	    t5 = encodeURIComponent(t5);
	    t6 = encodeURIComponent(t6);
	    
	    myWindow=window.open('<?php echo JURI::root();?>index.php?option=com_guru&view=guruOrders&task=printcertificate&op=1&cn='+t1+'&an='+t2+'&id='+t3+'&cd='+t4+'&ci='+t5+'&tmpl=component&ct='+t6,'','width=800,height=600, resizable = 0');
	    myWindow.focus();
	}

	function openWinCertificate4(t1,t2,t3,t4,t5,t6){
	    t1 = encodeURIComponent(t1);
	    t2 = encodeURIComponent(t2);
	    t3 = encodeURIComponent(t3);
	    t4 = encodeURIComponent(t4);
	    t5 = encodeURIComponent(t5);
	    t6 = encodeURIComponent(t6);
	    
	    myWindow=window.open('<?php echo JURI::root();?>index.php?option=com_guru&view=guruOrders&task=savepdfcertificate&op=9&cn='+t1+'&an='+t2+'&id='+t3+'&cd='+t4+'&ci='+t5+'&tmpl=component&ct='+t6,'','width=800,height=600, resizable = 0');
	    myWindow.focus();
	}
</script>

<?php
	if(isset($student["name"])){
		$image = $this->userImage($student["user_id"]);
		$user_id = intval($student["user_id"]);
?>
		<h4>
            <?php
                if(trim($image) == ""){
                    $grav_url = "http://www.gravatar.com/avatar/".md5(strtolower(trim($student["email"])))."?d=mm&s=40";
                    echo '<img src="'.$grav_url.'" alt="'.$student["name"].'" title="'.$student["name"].'"/>&nbsp;';
                }
                else{
                    echo '<img src="'.JURI::root().trim($image).'" style="width:40px;" alt="'.$student["name"].'" title="'.$student["name"].'" />&nbsp;';
                }

                echo $student["name"];
            ?>
        </h4>
<?php
	}

	if(isset($student_courses) && count($student_courses) > 0){
?>
		<table class="uk-table uk-table-striped wk-table wk-table-striped">
            <tr>
                <th width="30%" class="g_cell_1"><?php echo JText::_("GURU_PROGRAM"); ?></th>
                <th width="25%" class="g_cell_2"><?php echo JText::_("GURU_COURSE_PROGRESS"); ?></th>
                <th width="10%" class="g_cell_3"><?php echo JText::_("GURU_LAST_VISIT"); ?></th>
                <th width="10%" class="g_cell_4"><?php echo JText::_("GURU_QUIZES_AVG_SCORE"); ?></th>
                <th width="10%" class="g_cell_5"><?php echo JText::_("GURU_FINAL_EXAM_SCORE"); ?></th>
                <th width="15%" class="g_cell_6"><?php echo JText::_("GURU_OPTIONS"); ?></th>
            </tr>
<?php
		$already_edited = array();
		$scores_avg_quizzes = 0;
		$db = JFactory::getDbo();

		foreach($student_courses as $key=>$course){
			$id = $course->course_id;
			$nb_ofscores = 0;
            $avg_quizzes_cert = $course->avg_certc;
            $id_final_exam = $course->id_final_exam;
            $certterm = $course->certerm;

			// start calculate sum for all quizes from course------------------------------------
	        $sql = "select mr.`media_id` from #__guru_mediarel mr, #__guru_days d where mr.`type`='dtask' and mr.`type_id`=d.`id` and d.`pid`=".intval($id);
	        $db->setQuery($sql);
	        $db->execute();
	        $lessons = $db->loadColumn();
	        
	        if(!isset($lessons) || count($lessons) == 0){
	            $lessons = array("0");
	        }
	        
	        $sql = "select mr.`media_id` from #__guru_mediarel mr where mr.`layout`='12' and mr.`type`='scr_m' and mr.`type_id` in (".implode(", ", $lessons).")";
	        $db->setQuery($sql);
	        $db->execute();
	        $all_quizzes = $db->loadColumn();
	        
			$s = 0;
			
	        if(isset($all_quizzes) && count($all_quizzes) > 0){
	            foreach($all_quizzes as $key_quiz=>$quiz_id){
	                $sql = "SELECT score_quiz FROM #__guru_quiz_question_taken_v3 WHERE user_id=".intval($user_id)." and quiz_id=".intval($quiz_id)." and pid=".intval($id)." ORDER BY id DESC LIMIT 0,1";
	                $db->setQuery($sql);
	                $db->execute();
	                $result_q = $db->loadColumn();
	                $res = @$result_q["0"];
	                $s += $res;
	                
	                $sql = "SELECT `failed` FROM #__guru_quiz_question_taken_v3 WHERE user_id=".intval($user_id)." and quiz_id=".intval($quiz_id)." and pid=".intval($id)." ORDER BY id DESC LIMIT 0,1";
	                $db->setQuery($sql);
	                $db->execute();
	                $failed = $db->loadColumn();
	                $failed = @$failed["0"];
	            }
	        }
	        // stop calculate sum for all quizes from course------------------------------------
							
			if(is_array($all_quizzes) && count($all_quizzes) > 0){
				$nb_ofscores = count($all_quizzes);
			}

			if($nb_ofscores != 0){
                $scores_avg_quizzes = intval($s / $nb_ofscores);
            }

			if(!in_array($id, $already_edited)){
                $already_edited[] = $id;
?>
				<tr class="guru_row">	
					<td class="guru_product_name g_cell_1">
						<b><?php echo $course->course_name; ?></b>

						<?php								
                            $expire = JText::_("GURU_EXPIRES");
                            
                            if($course->plan_name == "Unlimited" || $course->expired_date == "0000-00-00 00:00:00"){
                                $date = '<span class="guru_active">'.JText::_("GURU_UNLIMITED_PLAN").'</span>';
                                $no_renew = true;
                            }
                            else{
                                $timezone = new DateTimeZone( JFactory::getConfig()->get('offset') );
								$jnow = new JDate('now');
								$jnow->setTimezone($timezone);
								$date_current = $jnow->toSQL(true);
								
                                $int_current_date = strtotime($date_current);
                                $bool_expired = false;
                                $date_string = "";
                                
                                if($int_current_date > strtotime($course->expired_date)){ //expired
                                    $bool_expired = true;
                                    $expire = JText::_("GURU_EXPIRED");
                                    $date_int = strtotime($course->expired_date);
                                    $date_string = "";

                                    if($config->hour_format == 24){
                                        $date_string = JHTML::_('date', $date_int, 'Y-m-d H:M:S');
                                    }
                                    elseif($config->hour_format == 12){
                                        $date_string = JHTML::_('date', $date_int, 'Y-m-d l:M:S p');
                                    }
                                    
                                    $difference_int = get_time_difference($date_int, $int_current_date);
                                    $difference = $difference_int["days"]." ".JText::_("GURU_REAL_DAYS");
                                    
                                    if($difference_int["days"] == 0){
                                        if($difference_int["hours"] == 0){
                                            if($difference_int["minutes"] == 0){
                                                $difference = "0";
                                            }
                                            else{
                                                $difference = $difference_int["minutes"]." ".JText::_("GURU_REAL_MINUTES")." ".JText::_("GURU_AGO");
                                            }
                                        }
                                        else{
                                            $difference = $difference_int["hours"]." ".JText::_("GURU_REAL_HOURS").", ".
                                                          $difference_int["minutes"]." ".JText::_("GURU_REAL_MINUTES")." ".JText::_("GURU_AGO");
                                        }
                                    }
                                    else{
                                        $difference = $difference_int["days"]." ".JText::_("GURU_REAL_DAYS").", ".
                                                      $difference = $difference_int["hours"]." ".JText::_("GURU_REAL_HOURS").", ".
                                                      $difference_int["minutes"]." ".JText::_("GURU_REAL_MINUTES")." ".JText::_("GURU_AGO");
                                    }
                                    $date = '<span class="guru_expired">'.$difference." (".JHTML::_('date', $date_int, 'm-d-Y').")".'</span>';
                                }
                                else{
                                    $bool_expired = false;
                                    $expire = JText::_("GURU_EXPIRES");
                                    $date_int = strtotime($course->expired_date);
                                    $date_string = "";
                                    if($config->hour_format == 24){
                                        $date_string = JHTML::_('date', $date_int, 'Y-m-d H:M:S');
                                    }
                                    elseif($config->hour_format == 12){
                                        $date_string = JHTML::_('date', $date_int, 'Y-m-d l:M:S p');
                                    }
                                    
                                    $difference_int = get_time_difference($int_current_date, $date_int);
                                    $difference = $difference_int["days"]." ".JText::_("GURU_REAL_DAYS");
                                    
                                    if($difference_int["days"] == 0){
                                        if($difference_int["hours"] == 0){
                                            if($difference_int["minutes"] == 0){
                                                $difference = "0";
                                            }
                                            else{
                                                $difference = JText::_("GURU_IN")." ".$difference_int["minutes"]." ".JText::_("GURU_REAL_MINUTES");
                                            }
                                        }
                                        else{
                                            $difference = JText::_("GURU_IN")." ".$difference_int["hours"]." ".JText::_("GURU_REAL_HOURS").", ".
                                                          $difference_int["minutes"]." ".JText::_("GURU_REAL_MINUTES");
                                        }
                                    }
                                    else{
                                        $difference = JText::_("GURU_IN")." ".$difference_int["days"]." ".JText::_("GURU_REAL_DAYS").", ".
                                                      $difference = $difference_int["hours"]." ".JText::_("GURU_REAL_HOURS").", ".
                                                      $difference_int["minutes"]." ".JText::_("GURU_REAL_MINUTES");
                                    }
                                    $date = '<span class="guru_active">'.$difference.'</span>';
                                }
                            }
                        ?>					
                        <br/>
                        <?php echo $expire; ?>: <?php echo $date; ?>
					</td>

					<td class="g_cell_2"> 
<?php
						$user_id = $student["user_id"];
	                    $completed_progress = $guruModelguruOrder->courseCompleted($user_id, $id);
	                    $date_completed = $guruModelguruOrder->dateCourseCompleted($user_id, $id);
	                    $date_completed = date("".$config->datetype."", strtotime($date_completed));
	                    $certificateid =  $guruModelguruOrder->getCertificateId($user_id, $id);
	                    $time_recorded = $guruModelguruOrder->dateCourseRecordTime($user_id, $id);
	                    $style_color = "";

						if($completed_progress == true){
	                        $var_lang = JText::_('GURU_COMPLETED');
	                        $lesson_module_progress = $var_lang." ". "(".$date_completed.")" ;	
	                        $style_color = 'style="color:#669900"';
	                    }
	                    else{
	                        $lesson_module_progress = $guruModelguruOrder->getLastViewedLessandMod($user_id, $id);	
	                    }
	                                
	                    if(isset($lesson_module_progress)){
	                         echo $lesson_module_progress; 
	                    } 
	                    else{
	                        echo "";
	                    }

	                    if(isset($time_recorded) && $time_recorded["show_time"]){
                        	$time_recorded["time"] = preg_replace("/:/", "h ", $time_recorded["time"], 1);
                        	$time_recorded["time"] = preg_replace("/:/", "m ", $time_recorded["time"], 2);
                        	$time_recorded["time"] .= "s";
                    ?>

                    		<br />

                        	<div class="record-time-label">
                        	<?php
                        		echo JText::_("GURU_CERT_TERM_TIME_RECORDED").": ".$time_recorded["time"];
                        	?>
                        	</div>
                    <?php
                    	}
                    ?>

					</td>

					<td class="g_cell_3">
						<?php
							$date_last_visit = $guruModelguruOrder->dateLastVisit($user_id, $id);
							$format_date = $config->datetype;
							$format_date = str_replace(" H:i:s", "", $format_date);

							if($date_last_visit != "0000-00-00" && $date_last_visit != NULL){
								$date_last_visit = date("".$format_date."", strtotime($date_last_visit));
							}
							else{
								$date_last_visit = "";
							}

							$count_quizz_taken = $guruModelguruOrder->countQuizzTakenF($user_id, $id);

							echo $date_last_visit;
						?>
					</td>

					<td class="g_cell_4">
<?php
						$resulthasq = $course->hasquiz;

                        if($resulthasq == 0 && $scores_avg_quizzes == ""){
                            echo JText::_("GURU_NO_QUIZZES");
                        }
                        elseif($resulthasq != 0 && $scores_avg_quizzes == ""){
                            echo JText::_("GURU_NOT_TAKEN");
                        }
                        elseif($resulthasq != 0 && isset($scores_avg_quizzes)){
                            if($scores_avg_quizzes >= intval($avg_quizzes_cert)){
                                echo $scores_avg_quizzes.'%'.'<span  style="color:#66CC00;">'.JText::_("GURU_QUIZ_PASSED").'</span>'; 
                            }
                            else{
                                echo $scores_avg_quizzes.'%'.'<span  style="color:#FF0000;">'.JText::_("GURU_QUIZ_FAILED").'</span>';
                            }
                        } 
?>
                    </td>

                    <td class="g_cell_5">
<?php
						$sql = "SELECT `max_score` FROM #__guru_quiz WHERE `id`=".intval($id_final_exam);
                        $db->setQuery($sql);
						$db->execute();
                        $result_maxs = $db->loadResult();

	                    if($id_final_exam != 0 && $res != "" ){
	                        if( $res >= $result_maxs){
	                            echo $res.'%'.'<span  style="color:#66CC00;">'.JText::_("GURU_QUIZ_PASSED").'</span>';
	                        }
	                        elseif($res < $result_maxs){
	                            echo $res.'%'.'<span  style="color:#FF0000;">'.JText::_("GURU_QUIZ_FAILED").'</span>';
	                        }
	                    }
	                    elseif(($id_final_exam != 0 && $id_final_exam != "")){
	                        echo JText::_("GURU_NOT_TAKEN");
	                    }
	                    elseif($id_final_exam == 0 || $id_final_exam == ""){
	                        echo JText::_("GURU_NO_FINAL_EXAM");
	                    }
?>
                    </td>

                    <td class="g_cell_6" nowrap="nowrap">
<?php
						$hascertficate = false;
						$completed_course = $completed_progress;

						//--------------hascertificate calculation-------------------
                        if($certterm == 1 || $certterm == 0){
                            $hascertficate = false;
                        }
						
                        if($certterm == 2){
                            if($completed_course == true){
                                $hascertficate = true;
                            }
                            else{
                                $hascertficate = false;
                            }
                        }
                        elseif($certterm == 3){
                            if( $res >= $result_maxs){
								$hascertficate = true;
                            }
                            else{
                                $hascertficate = false;
                            }
                        }
                        elseif($certterm == 4){
                            if($scores_avg_quizzes >= intval($avg_quizzes_cert)){
                                $hascertficate = true;
                            }
                            else{
                                $hascertficate = false;
                            }
                        }
                        elseif($certterm == 5){
                            if($completed_course==true && isset($result_maxs) && $res >= intval($result_maxs)){
                                $hascertficate = true;
                            }
                            else{
                                $hascertficate = false;
                            }
                        }
                        elseif($certterm == 6){
                            if($completed_course==true && isset($scores_avg_quizzes) && ($scores_avg_quizzes >= intval($avg_quizzes_cert))){
                                $hascertficate = true;
                            }
                            else{
                                $hascertficate = false;
                            }
                        }

                        if(!isset($res)){
							$res = 0;
						}
						
						if( $hascertficate && !in_array($id_final_exam, $certcourseidlist) ){
							$db = JFactory::getDbo();
							
							$sql = "select `author` from #__guru_program where `id`=".intval($id);
							$db->setQuery($sql);
							$db->execute();
							$course_author = $db->loadColumn();
							$course_author = @$course_author["0"];
							$course_author = explode("|", $course_author);
							$certificate_course_author = 0;
							
							foreach($course_author as $key_author=>$value_author){
								if(intval($value_author) != 0){
									$certificate_course_author = intval($value_author);
									break;
								}
							}
							
							$joomla_user = JFactory::getUser();
							$jnow = new JDate('now');
							$current_date_cert = $jnow->toSQL(true);
							
							// check if certificate already added
							$sql = "select count(*) from #__guru_mycertificates where `course_id`=".intval($id)." and `author_id`=".intval($certificate_course_author)." and `user_id`=".intval($student["user_id"]);
							$db->setQuery($sql);
							$db->execute();
							$count_certificates = $db->loadColumn();
							$count_certificates = @$count_certificates["0"];
							
							if(intval($count_certificates) == 0){
								$sql = 'insert into #__guru_mycertificates (`course_id`, `author_id`, `user_id`, `emailcert`, `datecertificate`) values ("'.intval($id).'", "'.intval($certificate_course_author).'", "'.intval($student["user_id"]).'", \'0\', "'.$current_date_cert.'")';
								$db->setQuery($sql);
								$db->execute();
							}
							
							$certcourseidlist[] = $id_final_exam;
							//$certcourseidlist[] = $id;
						}
						
						if($hascertficate == false ){
							if($certterm == 0){
								$span = JText::_("GURU_NO_CERT_MYC");
							}
							elseif($certterm == 1){
								$span = JText::_("GURU_NO_CERT_MYC");
							}
							elseif($certterm == 2){
								$span = JText::_("GURU_ALLLESS_CERT_MYC");
							}
							elseif($certterm == 3){
								if($res == ""){
									$span =  JText::_("GURU_PASSF_CERT_MYC")." ".$result_maxs."%,".JText::_("GURU_YOUR_SCORE_IS2");
								}
								elseif(isset($result_maxs) && $res < intval($result_maxs) && $failed == "1"){
									$span =  JText::_("GURU_PASSF_CERT_MYC")." ".$result_maxs."%, ".JText::_("GURU_FINAL_FAILED");
								}
								elseif(isset($result_maxs) && $res < intval($result_maxs)){
									$span =  JText::_("GURU_PASSF_CERT_MYC")." ".$result_maxs."%,".JText::_("GURU_YOUR_SCORE_IS") ." ".$res."%";
								}
								else{
									$span = JText::_("GURU_PASSF_CERT_MYC")." ".$result_maxs."%,".JText::_("GURU_YOUR_SCORE_IS2");
								}
							}
							elseif($certterm == 4){
								if(isset($scores_avg_quizzes) && ($scores_avg_quizzes < intval($avg_quizzes_cert))){
									$span = JText::_("GURU_PASSAVG")." ".$avg_quizzes_cert."%,".JText::_("GURU_YOUR_SCORE_WAS")." ".$scores_avg_quizzes."%";
								}
								elseif($scores_avg_quizzes == null){
									$span = JText::_("GURU_PASSAVG")." ".$avg_quizzes_cert."%,".JText::_("GURU_YOUR_SCORE_WAS2");
								}
								
							}
							elseif($certterm == 5){
								if($completed_course==true && isset($result_maxs) && $res < intval($result_maxs)){
									$span =  JText::_("GURU_FINISH_ALL_LESSONS_PASSFE1")." ".$result_maxs."%".JText::_("GURU_FINISH_ALL_LESSONS_PASSFE2") ." ".$res."%";
								}
								elseif($completed_course==false && isset($result_maxs) && $result_maxs < intval($res)){
									$span = JText::_("GURU_FINISH_ALL_LESSONS_PASSFE1")." ".$result_maxs."%".JText::_("GURU_FINISH_ALL_LESSONS_PASSFE4");

								}
								elseif($completed_course==false && isset($result_maxs) && $res < intval($result_maxs)){
									$span = JText::_("GURU_FINISH_ALL_LESSONS_PASSFE1")." ".$result_maxs."%".JText::_("GURU_FINISH_ALL_LESSONS_PASSFE3")." ".$res."%";
								}
								elseif($completed_course==false && $res == ""){
									$span = JText::_("GURU_FINISH_ALL_LESSONS_PASSFE1")." ".$result_maxs."%".JText::_("GURU_FINISH_ALL_LESSONS_PASSFE5")." ".$res."%";
								}
							}
							elseif($certterm == 6){
								if($completed_course==true && isset($scores_avg_quizzes) && ($scores_avg_quizzes < intval($avg_quizzes_cert))){
									$span = JText::_("GURU_FINISH_ALL_LESSONS_PASSAVG1")." ".$avg_quizzes_cert."%".JText::_("GURU_FINISH_ALL_LESSONS_PASSAVG2")." ".$scores_avg_quizzes."%";
								}
								elseif($completed_course==false && isset($scores_avg_quizzes) && ($avg_quizzes_cert < intval($scores_avg_quizzes))){
									$span = JText::_("GURU_FINISH_ALL_LESSONS_PASSAVG1")." ".$avg_quizzes_cert."%".JText::_("GURU_FINISH_ALL_LESSONS_PASSAVG4");

								}
								elseif($completed_course==false && $scores_avg_quizzes == ""){
									$span = JText::_("GURU_FINISH_ALL_LESSONS_PASSFE1")." ".$avg_quizzes_cert."%".JText::_("GURU_FINISH_ALL_LESSONS_PASSAVG5");
								}								
								else{
									$span = JText::_("GURU_FINISH_ALL_LESSONS_PASSAVG1")." ".$avg_quizzes_cert."%".JText::_("GURU_FINISH_ALL_LESSONS_PASSAVG3")." ".$scores_avg_quizzes."%";
								}
							}
?>
							<span style="color:#FF6600">
								<?php echo JText::_("GURU_NOT_ELIGIBLE"); ?>
							</span>
							<br/>
							<span class="editlinktip hasTip" title="<?php echo $span; ?>" style="color:#0099FF; font-size:12px;">
								<?php echo "( ".JText::_("GURU_WHY")." )"; ?>
							</span>
<?php
						}
						elseif(in_array($id_final_exam, $certcourseidlist) && ($hascertficate == true || $hascertficate == 1)){
							$sql = "SELECT `author_id` from #__guru_mycertificates where `course_id`=".intval($id)." and `user_id`=".intval($student["user_id"]);
							$db->setQuery($sql);
							$db->execute();
							$author_ids = $db->loadColumn();
							
							if(!is_array($author_ids) || count($author_ids) == 0){
								$author_ids = array("0");
							}

							$sql = "SELECT `name` FROM #__users WHERE `id` IN (".implode(",", $author_ids).") ";		
                            $db->setQuery($sql);
							$db->execute();
                            $author_name = $db->loadColumn();
                            $author_name = implode(", ", $author_name);

							$replace_text = array("'", '"');
							$replace_with = array("&acute;", "&quot;");

							$sql = "SELECT `datecertificate` from #__guru_mycertificates where `course_id`=".intval($id)." and `user_id`=".intval($user_id);
                            $db->setQuery($sql);
							$db->execute();
                            $date_completed = $db->loadResult();
                            
                            $date_completed =  date($config->datetype, strtotime($date_completed));
?>
							<a href="#" onclick="openWinCertificate1('<?php echo str_replace($replace_text, $replace_with, $course->course_name)?>','<?php echo str_replace($replace_text, $replace_with, $author_name); ?>','<?php echo $certificateid; ?>', '<?php echo $date_completed; ?>', '<?php echo $id; ?>', '<?php echo intval($userid); ?>'); return false;">
								<img title="<?php echo JText::_("GURU_VIEW_TOOLTIP"); ?>" src="<?php echo JUri::root()."/images/stories/guru/certificates/viewed.png"; ?>" align="viewed" />
							</a>
                                                
							<a href="#" onclick="openWinCertificate4('<?php echo str_replace($replace_text, $replace_with, $course->course_name)?>','<?php echo str_replace($replace_text, $replace_with, $author_name); ?>','<?php echo $certificateid; ?>', '<?php echo $date_completed; ?>', '<?php echo $id; ?>', '<?php echo intval($userid); ?>'); return false;">
								<img title="<?php echo JText::_("GURU_DLD_TOOLTIP"); ?>" src="<?php echo JUri::root()."/images/stories/guru/certificates/download.png"; ?>" align="viewed" />
							</a>
<?php
						}
?>
                    </td>

				</tr>
<?php
			}
		}
?>
		</table>
<?php
	}
?>