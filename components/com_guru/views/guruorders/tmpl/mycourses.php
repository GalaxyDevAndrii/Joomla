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
JHTML::_('behavior.modal');

require_once (JPATH_COMPONENT_ADMINISTRATOR.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'helper.php');

	$document = JFactory::getDocument();


	$document->setTitle(trim(JText::_('GURU_MYCOURSES')));
	$document->setMetaData( 'viewport', 'width=device-width, initial-scale=1.0' );
	$data_post = JFactory::getApplication()->input->post->getArray();
	
	require_once(JPATH_SITE.DIRECTORY_SEPARATOR."components".DIRECTORY_SEPARATOR."com_guru".DIRECTORY_SEPARATOR."helpers".DIRECTORY_SEPARATOR."generate_display.php");
	require_once(JPATH_BASE . "/components/com_guru/helpers/Mobile_Detect.php");
	$guruModelguruOrder = new guruModelguruOrder();
	
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
		
	$document = JFactory::getDocument();
	//$document->addScript("components/com_guru/js/programs.js");
	$db = JFactory::getDBO();
	$user = JFactory::getUser();
	$my_courses = $this->my_courses;
	$Itemid = JFactory::getApplication()->input->get("Itemid", "0", "raw");
	$search = JFactory::getApplication()->input->get("search_course", "");
	$config = $this->getConfigSettings();
	
	$helper = new guruHelper();
	$itemid_seo = $helper->getSeoItemid();
	$itemid_seo = @$itemid_seo["guruorders"];
	
	if(intval($itemid_seo) > 0){
		$Itemid = intval($itemid_seo);
			
		$sql = "select `access` from #__menu where `id`=".intval($Itemid);
		$db->setQuery($sql);
		$db->execute();
		$access = $db->loadColumn();
		$access = @$access["0"];
		
		if(intval($access) == 3){
			// special
			$user_groups = $user->get("groups");
			if(!in_array(8, $user_groups)){
				$Itemid = JFactory::getApplication()->input->get("Itemid", "0", "raw");
			}
		}
	}
	
	$sql = "Select datetype FROM #__guru_config where id=1 ";
	$db->setQuery($sql);
	$format_date = $db->loadResult();
	

	$detect = new Mobile_Detect;
	$deviceType = ($detect->isMobile() ? ($detect->isTablet() ? 'tablet' : 'phone') : 'computer');	
	if($deviceType !='phone'){
		$cname= JText::_("GURU_COURSES_DETAILS");
		$class_title = 'class="guruml20"';

	}
	else{
		$cname= JText::_("GURU_DAYS_NAME");
		$class_title = 'class="guruml0"';

	}
	
	?>
    
<script type="text/javascript" language="javascript">
	document.body.className = document.body.className.replace("modal", "");
</script>
    
<script language="javascript">
	function showContentVideo(href){
		jQuery('#myModal .modal-body iframe').attr('src', href);
	}
	
	jQuery('#myModal').on('hide', function () {
		 jQuery('#myModal .modal-body iframe').attr('src', '');
	});
</script>
    <?php
	$return_url = base64_encode("index.php?option=com_guru&view=guruorders&layout=mycourses&Itemid=".intval($Itemid));
	
	if($config->gurujomsocialprofilestudent == 1){
		$link = JUri::root() . "index.php?option=com_community&view=profile&task=edit&Itemid=".$Itemid;
	}
	else{
		$helper = new guruHelper();
		$itemid_seo = $helper->getSeoItemid();
		$itemid_seo = @$itemid_seo["guruprofile"];
		
		if(intval($itemid_seo) > 0){
			$Itemid = intval($itemid_seo);
			
			$Itemid = intval($itemid_seo);
			
			$sql = "select `access` from #__menu where `id`=".intval($Itemid);
			$db->setQuery($sql);
			$db->execute();
			$access = $db->loadColumn();
			$access = @$access["0"];
			
			if(intval($access) == 3){
				// special
				$user_groups = $user->get("groups");
				if(!in_array(8, $user_groups)){
					$Itemid = JFactory::getApplication()->input->get("Itemid", "0", "raw");
				}
			}
		}
	
		$link = JUri::root() . "index.php?option=com_guru&view=guruProfile&task=edit&Itemid=".$Itemid;
	}
	
	include_once(JPATH_SITE.DIRECTORY_SEPARATOR."components".DIRECTORY_SEPARATOR."com_guru".DIRECTORY_SEPARATOR."helpers".DIRECTORY_SEPARATOR."helper.php");
	$helper = new guruHelper();
	$div_menu = $helper->createStudentMenu();
	$page_title_cart = $helper->createPageTitleAndCart();
	
?>
<div id="myModal" class="modal hide g_modal" style="display:none !important;">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    </div>
    <div class="modal-body">
    <iframe id="g_my_course_pop" style="width:100%; height:100%; border:none;"></iframe>
    </div>
</div>

<div>
    <form action="<?php echo JUri::getInstance() ?>" name="adminForm" method="post">
        <?php
            echo $div_menu;
            echo $page_title_cart;
        ?>
        
        <div class="gru-page-filters">
        	<div class="gru-filter-item">
                <input type="text" class="form-control"  placeholder="<?php echo JText::_("GURU_SEARCH"); ?>" style="margin:0px;" name="search_course" value="<?php if(isset($data_post['search_course'])) echo $data_post['search_course'];?>" >
				<button class="uk-button uk-button-primary wk-button wk-button-primary" type="submit"><?php echo JText::_("GURU_SEARCH"); ?></button>
			</div>
        </div>
        
        <table class="uk-table uk-table-striped wk-table wk-table-striped">
            <tr>
                <th class="g_cell_1"><?php echo $cname; ?></th>
                <th class="g_cell_2 hidden-phone"><?php echo JText::_("GURU_COURSE_PROGRESS"); ?></th>
                <th class="g_cell_3 hidden-phone"><?php echo JText::_("GURU_LAST_VISIT"); ?></th>
                <th class="g_cell_5"><?php echo JText::_("GURU_RENEW"); ?></th>
            </tr>
            <style>
                  /*div.guru-content .btn_renew{
                    height:25px;!important; 
                  }*/
            </style>
            <?php
            $k = 0;
            $already_edited = array();
            
            foreach($my_courses as $key=>$course){
                $bool_expired = false;
                //$jnow = new JDate('now');
                //$date_current = $jnow->toSQL(true);									
				
				$timezone = new DateTimeZone( JFactory::getConfig()->get('offset') );
				$jnow = new JDate('now');
				$jnow->setTimezone($timezone);
				$date_current = $jnow->toSQL(true);
				
                $int_current_date = strtotime($date_current);
                $no_renew = false;
                $course = (object)$course;
                
                $id = $course->course_id;
                $alias = isset($course->alias) ? trim($course->alias) : JFilterOutput::stringURLSafe($course->course_name);
                
                if(!in_array($id, $already_edited)){
                    $already_edited[] = $id;

                $helper = new guruHelper();
                $itemid_menu = $helper->getCourseMenuItem(intval($id));
                $itemid_course = $Itemid;

                if(intval($itemid_menu) > 0){
                    $itemid_course = intval($itemid_menu);
                }
        ?>
                <tr class="guru_row">	
                        <td class="guru_product_name g_cell_1">
                            <a href="<?php echo JRoute::_("index.php?option=com_guru&view=guruPrograms&task=view&cid=".$id."-".$alias."&Itemid=".$itemid_course); ?>"><?php echo $course->course_name; ?></a>
                            <br />
                            <?php echo JText::_("GURU_CATEGORY"); ?>: <?php echo $course->category_name; ?>
                        <?php								
                            $expire = JText::_("GURU_EXPIRES");
                            if($course->plan_name == "Unlimited" || $course->expired_date == "0000-00-00 00:00:00"){
                                $date = '<span class="guru_active">'.JText::_("GURU_UNLIMITED_PLAN").'</span>';
                                $no_renew = true;
                            }
                            else{
                                //$jnow = new JDate('now');
                                //$date_current = $jnow->toSQL(true);									
								
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
                                    //---------------------------
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
                                    //---------------------------
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
                                    //---------------------------
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
                                    //---------------------------
                                }
                            }
                            $nr_orders = $this->countCourseOrders($id);
                        ?>						
                        <br/><?php echo $expire; ?>:<?php echo $date; ?>
                        <?php if ($nr_orders > 0) {
                        ?>
                        <br /><a href="<?php echo JUri::root() ?>index.php?option=com_guru&view=guruorders&layout=myorders&Itemid=<?php echo $Itemid; ?>&course=<?php echo $id; ?>"><?php echo JText::_("GURU_VIEW_ORDERS")." (".$nr_orders.")"; ?></a>
                        <?php
                                } else {}?>
                        </td>
                        <td class="g_cell_2 hidden-phone"> 
                            <?php
                                $user = JFactory::getUser();
                                $user_id = $user->id;
                                $completed_progress = $guruModelguruOrder->courseCompleted($user_id,$id);
                                $date_completed = $guruModelguruOrder->dateCourseCompleted($user_id, $id);
                                $date_completed = date("".$format_date."", strtotime($date_completed));
                                $time_recorded = $guruModelguruOrder->dateCourseRecordTime($user_id, $id);
                        
                                $style_color = "";
                                if($completed_progress == true){
                                    if($deviceType !="phone"){
                                        $var_lang = JText::_('GURU_COMPLETED');
                                        $lesson_module_progress = $var_lang." ". "(".$date_completed.")" ;	
                                        $style_color = 'style="color:#669900"';
                                    }
                                    else{
                                        $var_lang = JText::_('GURU_COMPLETED');
                                        $lesson_module_progress = $var_lang;
                                    }
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

                                $progress_percentage = $guruModelguruOrder->getProgressPercentage($user_id, $id);
                            ?>

                                <div style="display: block; width: 100%;">
                                    <div class="uk-progress uk-progress-small">
                                        <div class="uk-progress-bar" style="width: <?php echo $progress_percentage; ?>%;"></div>
                                    </div>
                                </div>

                            </td>
                            <?php
                                $date_last_visit = $guruModelguruOrder->dateLastVisit($user_id, $id);
								$format_date = str_replace(" H:i:s", "", $format_date);
								
                                if($date_last_visit != "0000-00-00" && $date_last_visit != NULL){
                                    //$date_last_visit = date("".$format_date."", strtotime($date_last_visit));

                                    $timezone = new DateTimeZone( JFactory::getConfig()->get('offset') );
                                    $date_last_visit = new JDate($date_last_visit);
                                    $date_last_visit->setTimezone($timezone);
                                    $date_last_visit = $date_last_visit->format($format_date);
                                }
                                else{
                                    $date_last_visit = "";
                                }

                                $count_quizz_taken = $guruModelguruOrder->countQuizzTakenF($user_id, $id);
                            ?>
                            <td class="g_cell_3 hidden-phone"><?php echo $date_last_visit;  ?></td>
                            <td class="g_cell_5">
                                <?php
                                    if($bool_expired == 0){ // not expired
                                        $date_int = strtotime($course->expired_date);
                                        $difference_int = get_time_difference($int_current_date, $date_int);		
                                        $difference = $difference_int["days"]." ".JText::_("GURU_REAL_DAYS");
                                        if($difference_int["days"] == 0){
                                            if($difference_int["hours"] == 0){
                                                if($difference_int["minutes"] == 0){
                                                    $difference = "0";
                                                }
                                                else{
                                                    $difference = $difference_int["minutes"]." ".JText::_("GURU_REAL_MINUTES");
                                                }
                                            }
                                            else{
                                                $difference = $difference_int["hours"]." ".JText::_("GURU_REAL_HOURS");
                                            }
                                        }
                                    
                                        $comfirm_text = JText::_("GURU_STILL_HAVE")." ".$difference." ".JText::_("GURU_AVAILABLE_COURSE")." ".JText::_("GURU_YES")." ".JText::_("GURU_ADD_TIME")." ".JText::_("GURU_CANCEL")." ".JText::_("GURU_GO_TO_COURSE_PAGE");
                                        if(!$no_renew){
                                ?>
                                        <input type="button" class=" btn btn-warning btn_renew" onclick="javascript:renewCourse(&quot;<?php echo $comfirm_text; ?>&quot;, <?php echo $id; ?> , &quot;<?php echo JURI::root(); ?>&quot;);" value="<?php echo JText::_("GURU_RENEW"); ?>">
                                <?php
                                        }
                                    }
                                    else{// expired
                                        if(!$no_renew){
                                ?>
                                        <input type="button" class="btn btn-warning btn_renew" onclick="document.adminForm.task.value='renew'; document.adminForm.order_id.value=<?php echo $course->order_id; ?>; document.adminForm.course_id.value=<?php echo $id; ?>;  document.adminForm.submit();" value="<?php echo JText::_("GURU_RENEW"); ?>">
                                <?php
                                        }
                                    }
                                ?>		
                            </td>
                        </tr>
                    
        <?php
                }
            }
        ?>
            </table>
        
        <input type="hidden" name="option" value="com_guru" />
        <input type="hidden" name="controller" value="guruOrders" />
        <input type="hidden" name="task" value="mycourses" />
        <input type="hidden" name="order_id" value="" />
        <input type="hidden" name="course_id" value="" />
    </form>
</div>