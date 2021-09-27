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

$doc = JFactory::getDocument();
JHTML::_('behavior.modal');
JHtml::_('behavior.calendar');

$doc = JFactory::getDocument();
//$doc->addScript('components/com_guru/js/guru_modal.js');
JHtml::stylesheet("components/com_guru/css/tabs.css");

$program = $this->program;
if($program->id == ""){
	$program_id = 0;
}
else{
	$program_id = $program->id;
}	
$lists = $program->lists;	
$mmediam = $this->mmediam;
$mainmedia = $this->mainmedia;
$configuration = $this->getConfigsObject();

$temp_size = $configuration->lesson_window_size_back;
$temp_size_array = explode("x", $temp_size);
$width = $temp_size_array["1"]-20;
$height = $temp_size_array["0"]-20;	
$db = JFactory::getDBO();
$sql = "Select datetype FROM #__guru_config where id=1 ";
$db->setQuery($sql);
$format_date = $db->loadColumn();
$dateformat = $format_date[0];

$data_post = JFactory::getApplication()->input->post->getArray();

$amount_quest = $this->getAmountQuestions($program_id);

$format = "%m-%d-%Y";
switch($dateformat){
	case "d-m-Y H:i:s": $format = "%d-%m-%Y %H:%M:%S";
		  break;
    case "d.m.Y H:i": $format = "%d.%m.%Y %H:%M"; 
        break;
	case "d/m/Y H:i:s": $format = "%d/%m/%Y %H:%M:%S"; 
		  break;
	case "m-d-Y H:i:s": $format = "%m-%d-%Y %H:%M:%S"; 
		  break;
	case "m/d/Y H:i:s": $format = "%m/%d/%Y %H:%M:%S"; 
		  break;
	case "Y-m-d H:i:s": $format = "%Y-%m-%d %H:%M:%S"; 
		  break;
	case "Y/m/d H:i:s": $format = "%Y/%m/%d %H:%M:%S"; 
		  break;
	case "d-m-Y": $format = "%d-%m-%Y"; 
		  break;
	case "d/m/Y": $format = "%d/%m/%Y"; 
		  break;
	case "m-d-Y": $format = "%m-%d-%Y"; 
		  break;
	case "m/d/Y": $format = "%m/%d/%Y"; 
		  break;
	case "Y-m-d": $format = "%Y-%m-%d"; 
		  break;
	case "Y/m/d": $format = "%Y/%m/%d";		
		  break;  	  	  	  	  	  	  	  	  	  
}
?>

<script type="text/javascript" language="javascript">
	document.body.className = document.body.className.replace("modal", "");
</script>

<script language="javascript" type="text/javascript">	
		
	function submitbutton2(pressbutton) {
		var form = document.adminForm;
		if (pressbutton=='save_quiz') {
			if (form['name'].value == "") {
				alert( "<?php echo JText::_("GURU_TASKS_JS_NAME");?>" );
			} 
			else{
				//submitform(pressbutton);
                form.task.value = pressbutton;
                form.submit();
			}
		}
	}
	
	function loadjscssfile(filename, filetype){
		if (filetype=="js"){ //if filename is a external JavaScript file
			var fileref=document.createElement('script')
			fileref.setAttribute("type","text/javascript")
			fileref.setAttribute("src", filename)
		}
		else if (filetype=="css"){ //if filename is an external CSS file
			var fileref=document.createElement("link")
			fileref.setAttribute("rel", "stylesheet")
			fileref.setAttribute("type", "text/css")
			fileref.setAttribute("href", filename)
		}
		if (typeof fileref!="undefined"){
			document.getElementsByTagName("head")[0].appendChild(fileref);
		}
	}

	function loadprototipe(){
		//loadjscssfile("<?php echo JURI::base().'components/com_guru/views/guruauthor/tmpl/prototype-1.6.0.2.js' ?>","js");
	}

	function addmedia (idu, name, asoc_file, description) {
		//loadprototipe();
		
		jQuery.ajax({
			url: '<?php echo JURI::root(); ?>index.php?option=com_guru&controller=guruAuthor&tmpl=component&format=raw&task=add_quizz_ajax&id='+idu+'&type=quiz',
			cache: false
		})
		.done(function(transport) {
			to_be_replaced=parent.document.getElementById('media_15');
			replace_m=15;
			to_be_replaced.innerHTML = '&nbsp;';
	
			to_be_replaced.innerHTML += transport;
			parent.document.getElementById("media_"+99).style.display="";
			parent.document.getElementById("description_med_99").innerHTML=''+name;
				
			parent.document.getElementById('before_menu_med_'+replace_m).style.display = 'none';
			parent.document.getElementById('after_menu_med_'+replace_m).style.display = '';
			parent.document.getElementById('db_media_'+replace_m).value = idu;
			
			replace_edit_link = parent.document.getElementById('a_edit_media_'+replace_m);
			replace_edit_link.href = '<?php echo JUri::root() ?>index.php?option=com_guru&controller=guruAuthor&tmpl=component&task=editQuiz&cid='+ idu;
			var qwe='&nbsp;'+transport+'<br /><div style="text-align:center"><i>'+ name +'</i></div><div  style="text-align:center"><i>' + description + '</i></div>';
					
			window.parent.test(replace_m, idu,qwe);
		});
		
		setTimeout('window.parent.document.getElementById("close").click()',1000);
		return true;
	}
	
</script>
<script>
	function delete_temp_m(i){
		document.getElementById('trm'+i).style.display = 'none';
		document.getElementById('mediafiletodel').value =  document.getElementById('mediafiletodel').value+','+i;
	}
	function delete_q(i){
		document.getElementById('trque'+i).style.display = 'none';
		document.getElementById('deleteq').value =  document.getElementById('deleteq').value+','+i;
	}
</script>

<div style="float:right">
	<div id="toolbar" class="btn-toolbar pull-right g_margin_right">
        <div id="toolbar-apply" class="btn-wrapper">
            <button class="uk-button uk-button-success wk-button wk-button-success" onclick="javascript:submitbutton2('save_quiz');">
                <span class="fa fa-floppy-o"></span>
                Save
            </button>
        </div>
   </div>
</div>

<form class="form-horizontal" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">
 	<input type="hidden" name="page_width" value="0" />
	<input type="hidden" name="page_height" value="0" />
 	<script type="text/javascript">
		<?php 
			if($configuration->back_size_type == "1"){ 
				echo 'document.adminForm.page_width.value="'.$width.'";';
				echo 'document.adminForm.page_height.value="'.$height.'";';
			}
		
		?>
	</script>
    
     <ul id="gurutabs" data-tabs="tabs" class="nav nav-tabs">
        <li class="active"><a href="#tab1" data-toggle="tab"><?php echo JText::_("GURU_GENERAL");?></a></li>
        <li><a href="#tab2" data-toggle="tab"><?php echo JText::_("GURU_QUESTIONS");?></a></li>
   </ul>
   <div class="tab-content" style="border-top:none!important;">
   <div class="tab-pane active" id="tab1">
		<div class="control-group clearfix g_row_inner">
            <div class="g_cell">
                <div>
                    <label class="control-label" for="name"><?php echo JText::_("GURU_NAME");?>: <span class="guru_error">*</span>&nbsp;&nbsp;</label>
                </div>
            </div>
            <div class="controls">
                <div>
                    <div>
                        <input type="text" id="name" name="name" value="<?php echo $program->name; ?>" />
                        <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_QUIZ_NAME"); ?>" >
                            <img border="0" src="components/com_guru/images/icons/tooltip.png">
                        </span>
                    </div> 
                </div>     
            </div>
        </div>
        
        <div class="control-group clearfix g_row_inner">
            <div class="g_cell">
                <div>
                    <label class="control-label" for="name"><?php echo JText::_("GURU_PRODDESC");?>:</label>
                </div>
            </div>        
            <div class="controls">
                <div>
                    <div>
                        <textarea name="description" id="description" cols="40" rows="8"><?php echo stripslashes($program->description); ?></textarea>
                        <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_QUIZ_PRODDESC"); ?>" >
                            <img border="0" src="components/com_guru/images/icons/tooltip.png">
                        </span>
                    </div>
                </div>        
            </div>
        </div>
        
         <div class="control-group clearfix g_row_inner">
            <div class="g_cell">
                <div>
                    <label class="control-label" for="author_blog"><?php echo JText::_("GURU_MINIMUM_SCORE_QUIZ");?>:</label>
                </div>
            </div>        
            <div class="controls">
                <div>
                    <div>
                        <span>
                           <?php if (isset($program->max_score)){
                                $program->max_score = $program->max_score;
                              }
                              else{
                                $program->max_score = 70;
                              }
                        ?>
                            <input type="text" id="max_score_pass" name="max_score_pass" value="<?php echo $program->max_score;?>" class="input-mini pull-left" />&nbsp;
                            <select id="show_max_score_pass" name="show_max_score_pass"  class="input-small" >
                                <option value="0" <?php if($program->pbl_max_score == "0"){echo 'selected="selected"'; } ?>><?php echo JText::_("GURU_SHOW"); ?></option>
                                <option value="1" <?php if($program->pbl_max_score == "1"){echo 'selected="selected"'; } ?>><?php echo JText::_("GURU_HIDE"); ?></option>
                            </select>
                            <span class="editlinktip hasTip" title="<?php echo JText::_('GURU_MAX_SCORE_TOOLTIP'); ?>" >
                                <img border="0" src="components/com_guru/images/icons/tooltip.png">
                            </span>
                        </span> 
                   </div>
               </div>            
            </div>
        </div>
        
        <div class="control-group clearfix g_row_inner">
            <div class="g_cell">
                <div>
                    <label class="control-label" for="name"><?php echo JText::_("GURU_QUIZ_CAN_BE_TAKEN");?>:</label>
                </div>
            </div>        
            <div class="controls">
                <div>
                    <div>
                       <select id="nb_quiz_taken" name="nb_quiz_taken" class="input-mini pull-left" >
                            <option value="1" <?php if($program->time_quiz_taken == "1"){echo 'selected="selected"'; }?> >1</option>
                            <option value="2" <?php if($program->time_quiz_taken == "2"){echo 'selected="selected"'; }?> >2</option>
                            <option value="3" <?php if($program->time_quiz_taken == "3"){echo 'selected="selected"'; }?> >3</option>
                            <option value="4" <?php if($program->time_quiz_taken == "4"){echo 'selected="selected"'; }?> >4</option>
                            <option value="5" <?php if($program->time_quiz_taken == "5"){echo 'selected="selected"'; }?> >5</option>
                            <option value="6" <?php if($program->time_quiz_taken == "6"){echo 'selected="selected"'; }?> >6</option>
                            <option value="7" <?php if($program->time_quiz_taken == "7"){echo 'selected="selected"'; }?> >7</option>
                            <option value="8" <?php if($program->time_quiz_taken == "8"){echo 'selected="selected"'; }?> >8</option>
                            <option value="9" <?php if($program->time_quiz_taken == "9"){echo 'selected="selected"'; }?> >9</option>
                            <option value="10"<?php if($program->time_quiz_taken == "10"){echo 'selected="selected"'; }?> >10</option>
                            <option value="20" <?php if($program->time_quiz_taken == "20"){echo 'selected="selected"'; }?> >20</option>
                            <option value="30" <?php if($program->time_quiz_taken == "30"){echo 'selected="selected"'; }?> >30</option>
                            <option value="40" <?php if($program->time_quiz_taken == "40"){echo 'selected="selected"'; }?> >40</option>
                            <option value="50" <?php if($program->time_quiz_taken == "50"){echo 'selected="selected"'; }?> >50</option>
                            <option value="11" <?php if($program->time_quiz_taken == "11"){echo 'selected="selected"'; }?>><?php echo JText::_("GURU_UNLIMPROMO");?></option>
                        </select>
                        <span class="pull-left" style="line-height: 30px; padding: 0 5px;">
                            <?php echo JText::_("GURU_TIMES_T"); ?>
                        </span>
                        
                        <select id="show_nb_quiz_taken" name="show_nb_quiz_taken" class="input-small" >
                            <option value="0" <?php if($program->show_nb_quiz_taken == "0"){echo 'selected="selected"'; } ?>><?php echo JText::_("GURU_SHOW"); ?></option>
                            <option value="1" <?php if($program->show_nb_quiz_taken == "1"){echo 'selected="selected"'; } ?>><?php echo JText::_("GURU_HIDE"); ?></option>
                        </select>&nbsp;
                        <span class="editlinktip hasTip" title="<?php echo JText::_('GURU_TIMES_TAKEN_TOOLTIP'); ?>" >
                            <img border="0" src="components/com_guru/images/icons/tooltip.png">
                        </span>
                   </div>
               </div>        
            </div>
        </div>
        
        <div class="control-group clearfix g_row_inner">
            <div class="g_cell">
                <div>
                    <label class="control-label" for="name"><?php echo JText::_("GURU_SELECT_UP_TO");?>: <span class="guru_error">*</span>&nbsp;&nbsp;</label>
                </div>  
            </div>      
            <div class="controls">
                <div>
                    <div>
                        <select id="nb_quiz_select_up" name="nb_quiz_select_up" class="input-mini pull-left" >
                            <?php
                            if(isset($amount_quest) && $amount_quest !=0 ){
                                for($i=$amount_quest; $i>=1; $i--){?>
                                    <option value="<?php echo $i;?>" <?php if($program->nb_quiz_select_up == $i){echo 'selected="selected"'; }?> ><?php echo $i;?></option>
                                <?php 
                                }
                            }
                            else{
                            ?>
                                <option value="text1"><?php echo "Please add questions first";?></option>
                            
                            <?php
                            }
                            ?>
                        </select>
                        <span class="pull-left" style="padding:0px 5px; line-height:30px;">
                            <?php echo JText::_('GURU_QUESTION_RANDOM'); ?>
                        </span>
                        <select id="show_nb_quiz_select_up" name="show_nb_quiz_select_up" class="input-small" >
                            <option value="0" <?php if($program->show_nb_quiz_select_up == "0"){echo 'selected="selected"'; } ?>><?php echo JText::_("GURU_SHOW"); ?></option>
                            <option value="1" <?php if($program->show_nb_quiz_select_up == "1"){echo 'selected="selected"'; } ?>><?php echo JText::_("GURU_HIDE"); ?></option>
                        </select>
                        <span class="editlinktip hasTip" title="<?php echo JText::_('GURU_QUESTIONS_NUMBER_TOOLTIP'); ?>" >
                            <img border="0" src="components/com_guru/images/icons/tooltip.png">
                        </span>
                   </div>
               </div>         
          </div>
        </div>
        
        <div class="control-group clearfix g_row_inner">
            <div class="g_cell">
                <div>
                    <label class="control-label" for="name"><?php echo JText::_("GURU_QUESTIONS_PER_PAGE");?></label>
                </div>  
            </div>      
            <div class="controls">
                <div>
                    <div>
                        <?php
                            $questions_per_page = $program->questions_per_page;
                        ?>
                        <select name="questions_per_page" class="input-mini">
                            <option value="5" <?php if($questions_per_page == "5"){echo 'selected="selected"';} ?> >5</option>
                            <option value="10" <?php if($questions_per_page == "10"){echo 'selected="selected"';} ?> >10</option>
                            <option value="15" <?php if($questions_per_page == "15"){echo 'selected="selected"';} ?> >15</option>
                            <option value="20" <?php if($questions_per_page == "20"){echo 'selected="selected"';} ?> >20</option>
                            <option value="25" <?php if($questions_per_page == "25"){echo 'selected="selected"';} ?> >25</option>
                            <option value="30" <?php if($questions_per_page == "30"){echo 'selected="selected"';} ?> >30</option>
                            <option value="50" <?php if($questions_per_page == "50"){echo 'selected="selected"';} ?> >50</option>
                            <option value="100" <?php if($questions_per_page == "100"){echo 'selected="selected"';} ?> >100</option>
                            <option value="0" <?php if($questions_per_page == "0"){echo 'selected="selected"';} ?> >All</option>
                        </select>
                        &nbsp;
                        <span class="editlinktip hasTip" title="<?php echo JText::_('GURU_QUESTIONS_PER_PAGE_TOOLTIP'); ?>" >
                            <img border="0" src="components/com_guru/images/icons/tooltip.png">
                        </span>
                   </div>
               </div>         
          </div>
        </div>
        
        <div class="alert alert-info"><?php echo JText::_("GURU_TIMER"); ?></div>
        
        <div class="control-group clearfix g_row_inner">
            <div class="g_cell">
                <div>
                    <label class="control-label" for="name"><?php echo JText::_("GURU_QUIZ_LIMIT_TIME");?>:</label>
                </div>  
            </div>      
            <div class="controls">
                <div>
                    <div>
                        <?php if (isset($program->limit_time)){
                                $program->limit_time = $program->limit_time;
                              }
                              else{
                                $program->limit_time = 3;
                              }
                        
                        
                        
                        ?>
                        	<select id="limit_time_l" name="limit_time_l" class="pull-left" style="margin-right:10px;">
                            	<option value="0"> <?php echo JText::_("GURU_UNLIMPROMO"); ?> </option>
                                <?php
                                	for($i=1; $i<=25; $i++){
										$selected = "";
										$time = JText::_("GURU_MINUTES");
										
										if($i == $program->limit_time){
											$selected = 'selected="selected"';
										}
										
										if($i == 1){
											$time = JText::_("GURU_MINUTE");
										}
										
										echo '<option value="'.$i.'" '.$selected.'>'.$i." ".$time.'</option>';
									}
								?>
                            </select>
                        
                            <select id="show_limit_time" name="show_limit_time" class="input-small" >
                                <option value="0" <?php if($program->show_limit_time == "0"){echo 'selected="selected"'; } ?>><?php echo JText::_("GURU_SHOW"); ?></option>
                                <option value="1" <?php if($program->show_limit_time == "1"){echo 'selected="selected"'; } ?>><?php echo JText::_("GURU_HIDE"); ?></option>
                            </select>
                            
                            <span class="editlinktip hasTip" title="<?php echo JText::_('GURU_QUIZ_LIMIT_TIME_TOOLTIP'); ?>" >
                                <img border="0" src="components/com_guru/images/icons/tooltip.png">
                            </span>
                    </div>
                </div>    
            </div>
        </div>
        
        <div class="control-group clearfix g_row_inner">
            <div class="g_cell">
                <div>
                    <label class="control-label" for="name"><?php echo JText::_("GURU_SHOW_COUNTDOWN");?>:</label>
                </div>
            </div>        
            <div class="controls">
                <div>
                    <div>
                        <select id="show_countdown" name="show_countdown" class="input-small pull-left" >
                            <option value="0" <?php if($program->show_countdown == "0"){echo 'selected="selected"'; } ?>><?php echo JText::_("GURU_SHOW"); ?></option>
                            <option value="1" <?php if($program->show_countdown == "1"){echo 'selected="selected"'; } ?>><?php echo JText::_("GURU_HIDE"); ?></option>
                        </select>
                        <span class="editlinktip hasTip" title="<?php echo JText::_('GURU_SHOW_COUNTDOWN_TOOLTIP'); ?>" >
                            <img border="0" src="components/com_guru/images/icons/tooltip.png">
                        </span>
                    </div>
                </div>        
            </div>
        </div>
        
        <div class="control-group clearfix g_row_inner">
            <div class="g_cell">
                <div>
                    <label class="control-label" for="name"><?php echo JText::_("GURU_FINISH_ALERT");?>:</label>
                </div>
            </div>       
            <div class="controls">
                <div>
                    <div>
                    <?php if (isset($program->limit_time_f)){
                            $program->limit_time_f = $program->limit_time_f;
                          }
                          else{
                            $program->limit_time_f = 1;
                          }
                    
                    
                    
                    ?>
                        <select id="limit_time_f" name="limit_time_f" class="pull-left">
                            <option value="2" <?php if($program->limit_time_f == "2"){echo 'selected="selected"';} ?> > 2 <?php echo JText::_("GURU_MINUTES"); ?> </option>
                            <option value="1" <?php if($program->limit_time_f == "1"){echo 'selected="selected"';} ?> > 1 <?php echo JText::_("GURU_MINUTE"); ?> </option>
                            <option value="30" <?php if($program->limit_time_f == "30"){echo 'selected="selected"';} ?> > 30 <?php echo JText::_("GURU_SECONDS"); ?> </option>
                            <option value="15" <?php if($program->limit_time_f == "15"){echo 'selected="selected"';} ?> > 15 <?php echo JText::_("GURU_SECONDS"); ?> </option>
                        </select>
                        
                        <span class="pull-left" style="padding:0px 5px; line-height:30px;">
                            <?php echo JText::_('GURU_MINUTES_BEFORE_IS_UP'); ?>
                        </span>
                        <select id="show_finish_alert" name="show_finish_alert" class="pull-left input-small" >
                            <option value="0" <?php if($program->show_finish_alert == "0"){echo 'selected="selected"'; } ?>><?php echo JText::_("GURU_SHOW"); ?></option>
                            <option value="1" <?php if($program->show_finish_alert == "1"){echo 'selected="selected"'; } ?>><?php echo JText::_("GURU_HIDE"); ?></option>
                        </select>
                        <span class="editlinktip hasTip" title="<?php echo JText::_('GURU_FINISH_ALERT_TOOLTIP'); ?>" >
                            <img border="0" src="components/com_guru/images/icons/tooltip.png">
                        </span>
                   </div>
               </div> 
          </div>
       </div>
       
       <div class="alert alert-info"><?php echo JText::_("GURU_PUBLISHING"); ?></div>
    
        <div class="control-group clearfix g_row_inner">
            <div class="g_cell">
                <div>
                    <label class="control-label" for="name"><?php echo JText::_("GURU_PRODLPBS");?>:</label>
                </div>
            </div>
            <div class="controls">
                <?php echo $lists['published']; ?>
                <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_QUIZ_PRODDESC"); ?>" >
                    <img border="0" src="components/com_guru/images/icons/tooltip.png">
                </span>
            </div>
        </div>
        
        <div class="control-group clearfix g_row_inner">
            <div class="g_cell">
                <div>
                    <label class="control-label" for="name"><?php echo JText::_("GURU_PRODLSPUB");?>:</label>
                </div>
            </div>
            <div class="controls">
                <?php 
                    if($program->id<1){
                        $start_publish =  date("".$dateformat."", time());
                    }
                    else{
                        $start_publish =  date("".$dateformat."", strtotime($program->startpublish));
                    }
                    echo JHTML::_('calendar', $start_publish, 'startpublish', 'startpublish', $format, array("showTime"=>"", "todayBtn"=>"", "weekNumbers"=>"", "fillTable"=>"", "singleHeader"=>"")); // calendar change
                ?>
                &nbsp;
                <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_QUIZ_PRODLSPUB"); ?>" >
                    <img border="0" src="components/com_guru/images/icons/tooltip.png">
                </span>
            </div>
        </div>
        
        <div class="control-group clearfix g_row_inner">
            <div class="g_cell">
                <div>
                    <label class="control-label" for="name"><?php echo JText::_("GURU_PRODLEPUB");?>:</label>
                </div>
            </div>
            <div class="controls">
                <?php 
                    if(substr($program->endpublish,0,4) =='0000' || $program->endpublish == JText::_('GURU_NEVER')|| $program->id<1) $program->endpublish = ""; else $program->endpublish = date("".$dateformat."", strtotime($program->endpublish));
                    
                    echo JHTML::_('calendar', $program->endpublish, 'endpublish', 'endpublish', $format, array("showTime"=>"", "todayBtn"=>"", "weekNumbers"=>"", "fillTable"=>"", "singleHeader"=>"")); // calendar change
                    
                ?>
                &nbsp;
                <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_QUIZ_PRODLEPUB"); ?>" >
                    <img border="0" src="components/com_guru/images/icons/tooltip.png">
                </span>
            </div>
        </div>
       
    </div>
        
    <div class="tab-pane" id="tab2">
		<table class="table">
            <tr>
                <td>
                    <div>
                        <div style="float:left;">
                        	<div data-uk-dropdown="{mode:'click'}" class="uk-button-dropdown wk-button-dropdown" aria-haspopup="true" aria-expanded="false">
                                <button class="uk-button uk-button-success wk-button wk-button-success"><?php echo JText::_('GURU_ADD_QUESTION'); ?> <i class="uk-icon-caret-down wk-icon-caret-down"></i></button>
                                <div class="uk-dropdown uk-dropdown-small wk-dropdown wk-dropdown-small" style="">
                                    <ul class="uk-nav uk-nav-dropdown wk-nav wk-nav-dropdown">
                                        <li>
                                            <a href="#" class="question-title" onclick="javascript:openMyModal('0', '0', '<?php echo JURI::root(); ?>index.php?option=com_guru&controller=guruAuthor&task=addquestion&tmpl=component&is_from_modal=1&cid=<?php echo $program->id;?>&type=true_false&new_add=1'); return false;">
                                                <?php echo JText::_("GURU_QUIZ_TRUE_FALSE"); ?>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="#" class="question-title" onclick="javascript:openMyModal('0', '0', '<?php echo JURI::root(); ?>index.php?option=com_guru&controller=guruAuthor&task=addquestion&tmpl=component&is_from_modal=1&cid=<?php echo $program->id;?>&type=single&new_add=1'); return false;">
                                                <?php echo JText::_("GURU_QUIZ_SINGLE_CHOICE"); ?>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="#" class="question-title" onclick="javascript:openMyModal('0', '0', '<?php echo JURI::root(); ?>index.php?option=com_guru&controller=guruAuthor&task=addquestion&tmpl=component&is_from_modal=1&cid=<?php echo $program->id;?>&type=multiple&new_add=1'); return false;">
                                                <?php echo JText::_("GURU_QUIZ_MULTIPLE_CHOICE"); ?>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="#" class="question-title" onclick="javascript:openMyModal('0', '0', '<?php echo JURI::root(); ?>index.php?option=com_guru&controller=guruAuthor&task=addquestion&tmpl=component&is_from_modal=1&cid=<?php echo $program->id;?>&type=essay&new_add=1'); return false;">
                                                <?php echo JText::_("GURU_QUIZ_ESSAY"); ?>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        
                        
                            <!--<a rel="{handler: 'iframe', size: {x: 800, y: 700}, iframeOptions: {id: 'g_teacher_addquestionss'}}" href="<?php echo JURI::root(); ?>index.php?option=com_guru&controller=guruAuthor&task=addquestion&tmpl=component&no_html=1&cid=<?php echo $program_id;?>&is_from_modal=1" class="modal" style="color:#666666 !important; text-decoration:underline !important;">
                <?php echo JText::_('GURU_ADD_QUESTION'); ?>
            				</a> -->
                        </div>
                        <div style="float:left;">
                            <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_QUIZ_ADD_QUESTION"); ?>" >
                                <img border="0" src="components/com_guru/images/icons/tooltip.png">
                            </span>
                        </div>
                    </div>
                    <br/><br/>
                    <table id="articleList" class="table" cellspacing="1" cellpadding="5" border="0" bgcolor="#cccccc" width="100%">
                        <thead>
                            <tr>
                                <th width="1%">
                                    <a href="#" onclick="changeSort();"><i class="icon-menu-2"></i></a>
                                    <?php
                                        $sortquestion = JFactory::getApplication()->input->get("sortquestion", "");
                                    ?>
                                    <input type="hidden" name="sortquestion" id="sortquestion" value="<?php echo $sortquestion; ?>" />
                                </th>
                                <th width="1%"></th>
                                <th width="42%">
                                    <strong><?php echo JText::_('GURU_QUESTIONS');?></strong>
                                </th>
                                <th width="12%">
                                    <strong><?php echo JText::_('GURU_EDIT');?></strong>
                                </th>
                                 <th width="17%">
                                    <strong><?php echo JText::_('GURU_REMOVE');?></strong>
                                </th>
                                <th width="14%">
                                    <strong><?php echo JText::_('GURU_PUBLISHED');?></strong>
                                </th>
                            </tr> 
                        <thead>    
                        <tbody id="rowquestion">                
                        <?php 
                        if(isset($data_post['deleteq'])){
                            $hide_q2del = $data_post['deleteq'];
                        }
                        else{
                            $hide_q2del = ',';
    
                        }
                        $hide_q2del = explode(',', $hide_q2del);
                        $i = 0;
                        foreach ($mmediam as $mmedial) { 
                            $link2_remove = '<font class="uk-button uk-button-danger wk-button wk-button-danger"><span onClick="delete_q('.$mmedial->id.','.$program->id.',0)">'.JText::_('GURU_REMOVE').'</span></font>';
                        ?>
                            
                            <tr class="row<?php echo $i%2;?>" id="trque<?php echo $mmedial->id; ?>" <?php if(in_array($mmedial->id,$hide_q2del)) { ?> style="display:none" <?php } ?>>
                                <td>
                                    <span class="sortable-handler active" style="cursor: move;">
                                        <i class="icon-menu"></i>
                                    </span>
                                    <input type="text" class="width-20 text-area-order " value="<?php echo @$mmedial->reorder; ?>" size="5" name="order[]" style="display:none;">
                                </td> 
                                <td width="1%" style="text-align:center; visibility:hidden;">>
                                    <?php
                                        $checked = JHTML::_('grid.id', $i, $mmedial->id); echo $checked;
                                    ?>
                                </td>
                                
                                <td id="tdq<?php echo $mmedial->id?>" width="42%">
                                	<a href="#" class="question-title" onclick="javascript:openMyModal('0', '0', '<?php echo JURI::root(); ?>index.php?option=com_guru&controller=guruAuthor&task=addquestion&tmpl=component&is_from_modal=1&cid=<?php echo $program->id;?>&type=<?php echo $mmedial->type; ?>&qid=<?php echo $mmedial->id; ?>'); return false;">
										<?php if (strlen ($mmedial->question_content) >55){echo substr(str_replace("\'","&acute;" ,$mmedial->question_content),0,55).'...';}else{echo str_replace("\'","&acute;" ,$mmedial->question_content);}?>
                                    </a>
                                
                                    <!--<a rel="{handler: 'iframe', size: {x: 800, y: 700}, iframeOptions: {id: 'g_teacher_editquestion'}}"  href="<?php echo JURI::root(); ?>index.php?option=com_guru&controller=guruAuthor&task=addquestion&is_from_modal=1&tmpl=component&no_html=1&cid=<?php echo $program->id.'&qid='.$mmedial->id.'&type='.$mmedial->type;?>"  class="modal question-title"><?php if (strlen ($mmedial->question_content) >55){echo substr(str_replace("\'","&acute;" ,$mmedial->question_content),0,55).'...';}else{echo str_replace("\'","&acute;" ,$mmedial->question_content);}?></a> -->
                                </td>
                                <td width="12%">
                                    <a href="#" class="question-title" onclick="javascript:openMyModal('0', '0', '<?php echo JURI::root(); ?>index.php?option=com_guru&controller=guruAuthor&task=addquestion&tmpl=component&is_from_modal=1&cid=<?php echo $program->id;?>&type=<?php echo $mmedial->type; ?>&qid=<?php echo $mmedial->id; ?>'); return false;">
										<?php echo JText::_('GURU_EDIT');?>
                                    </a>
                                    
                                    <!--<a class=" modal btn btn-primary" rel="{handler: 'iframe', size: {x: 800, y: 700}, iframeOptions: {id: 'g_teacher_editquestions'}}"  href="<?php echo JURI::root(); ?>index.php?option=com_guru&controller=guruAuthor&task=editquestion&is_from_modal=1&tmpl=component&no_html=1&cid=<?php echo $program->id.'&qid='.$mmedial->id;?>"><?php echo JText::_('GURU_EDIT');?></a> -->
                                    
                                        <?php 	if($mmedial->published==1){ 
                                                    echo "<input type='hidden' id='publ".$mmedial->id."' name='publish_q[".$mmedial->id."]' value='1' />";
                                                } 
                                                else{ 
                                                    echo "<input type='hidden' id='publ".$mmedial->id."' name='publish_q[".$mmedial->id."]' value='0' />";
                                                }?>
                                </td>
                                <td width="17%">
                                    <?php echo $link2_remove; ?>
                                </td>
                                <td width="14%" id="publishing<?php echo $mmedial->id;?>">
                                    <?php 
                                        if($mmedial->published == 1) {
                                            echo '<a class="icon-ok" style="cursor: pointer; text-decoration: none;" onclick="javascript:unpublish(\''.$mmedial->id.'\');"></a>';
                                        }
                                        else{
                                            echo '<a class="icon-remove" style="cursor: pointer; text-decoration: none;" onclick="javascript:publish(\''.$mmedial->id.'\');"></a>';
                                        }
                                    ?>
                                </td>
                            </tr>
                        <?php
                        $i++;
                        }//end foreach?>
                            <input type="hidden" value="<?php if (isset($data_post['newquizq'])) echo $data_post['newquizq'];?>" id="newquizq" name="newquizq" >
                            <input type="hidden" value="<?php if (isset($data_post['deleteq'])) echo $data_post['deleteq'];?>" id="deleteq" name="deleteq" >
                            
                        </tbody>
                    </table>
                </td>
            </tr>
        </table>
	</div>
 </div>
    <input type="hidden" name="id" value="<?php echo $program_id; ?>" />
    <input type="hidden" name="task" value="save_quiz" />
    <input type="hidden" name="option" value="com_guru" />
    <input type="hidden" name="image" value="<?php if(isset($data_post['image'])) echo $data_post['image']; else echo $program->image;?>" />
    <input type="hidden" name="controller" value="guruAuthor" />
    <input type="hidden" name="is_from_modal" value="1">
    <a id="close_gb" style="display:none;">#</a>
</form>