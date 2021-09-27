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
$data_get = JFactory::getApplication()->input->get->getArray();
$data_post = JFactory::getApplication()->input->post->getArray();
?>

<script type="text/javascript" language="javascript">
	document.body.className = document.body.className.replace("modal", "");
</script>

<style>
	.alert-info { height:auto!important;}
	.accordionItem.hideTabs div { display: none; }
	
	/* modal
	-------------------------*/
	.modal{
		position:fixed !important;
	}
</style>

<script language="javascript" type="text/javascript">
	var accordionItems = new Array();		
	function initPhoneTeacherTabs() {
      // Grab the accordion items from the page
      var divs = document.getElementsByTagName( 'div' );
      for ( var i = 0; i < divs.length; i++ ) {
        if ( divs[i].className == 'accordionItem' ) accordionItems.push( divs[i] );
      }

      // Assign onclick events to the accordion item headings
      for ( var i = 0; i < accordionItems.length; i++ ) {
        var h3 = getFirstChildWithTagName( accordionItems[i], 'H3' );
        h3.onclick = toggleItem;
      }

      // Hide all accordion item bodies except the first
      for ( var i = 1; i < accordionItems.length; i++ ) {
        accordionItems[i].className = 'accordionItem hideTabs';
      }
    }

    function toggleItem() {
      var itemClass = this.parentNode.className;

      // Hide all items
      for ( var i = 0; i < accordionItems.length; i++ ) {
        accordionItems[i].className = 'accordionItem hideTabs';
      }

      // Show this item if it was previously hidden
      if ( itemClass == 'accordionItem hideTabs' ) {
        this.parentNode.className = 'accordionItem';
      }
    }

    function getFirstChildWithTagName( element, tagName ) {
      for ( var i = 0; i < element.childNodes.length; i++ ) {
        if ( element.childNodes[i].nodeName == tagName ) return element.childNodes[i];
      }
    }
	
	function isFloat(nr){
		return parseFloat(nr.match(/^-?\d*(\.\d+)?$/)) > 0;
	}
	
	function showContent(href){
		jQuery( '#myModal .modal-body iframe').attr('src', href);
	}
	
function savequiz (pressbutton){
		var form = document.adminForm;
		if (pressbutton == 'save_quizFE' || pressbutton == 'apply_quizFE') {
			if (form['name'].value == "") {
				alert( "<?php echo JText::_("GURU_TASKS_JS_NAME");?>" );
			} 
			else{
				max_score_pass = document.getElementById("max_score_pass").value;
				limit_time_l = document.getElementById("limit_time_l").value;
				limit_time_f = document.getElementById("limit_time_f").value;
				
				if(!isFloat(max_score_pass) || max_score_pass <= 0){
					alert("<?php echo JText::_("GURU_INVALID_NUMBER_AVG"); ?>");
					return false;
				}
				else if(!isFloat(limit_time_l) || limit_time_l <= 0){
					alert("<?php echo JText::_("GURU_INVALID_NUMBER_AVG"); ?>");
					return false;
				}
				else if(!isFloat(limit_time_f) || limit_time_f <= 0){
					alert("<?php echo JText::_("GURU_INVALID_NUMBER_AVG"); ?>");
					return false;
				}
				
				if((parseInt(limit_time_l) < parseInt(limit_time_f)) || (parseInt(limit_time_l) == parseInt(limit_time_f))){
					alert("<?php echo JText::_("GURU_LIMIT2_GRATER_LIMIT1"); ?>");
					return false;
				}
				
				//submitform( pressbutton );
                form.task.value = pressbutton;
                form.submit();
			}
		}
		else{
			//submitform( pressbutton );
            form.task.value = pressbutton;
            form.submit();
		}
	}
</script>

<div id="myModal" class="modal hide g_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-header">
        <button type="button" id="close" class="close" data-dismiss="modal" aria-hidden="true">x</button>
     </div>
     <div class="modal-body">
        <iframe frameborder="0" class="g_leesson_popup" id="g_lesson_level1"></iframe>
    </div>
</div>

<div class="g_row">
	<div class="g_cell span12">
		<div>
			<div>
                <div id="g_myquizzesfeaddedit" class="clearfix com-cont-wrap">
					<?php 	echo $div_menu; //MENU TOP OF AUTHORS?>
                     <div class="row-fluid clearfix">
                        <div class="span12 pagination-right g_margin_bottom">
                                <input type="button" class="btn btn-success" value="<?php echo JText::_("GURU_SAVE"); ?>" onclick="javascript:savequiz('apply_quizFE');" />
                                <input type="button" class="btn btn-success" value="<?php echo JText::_("GURU_SAVE_AND_CLOSE"); ?>" onclick="javascript:savequiz('save_quizFE');" />
                                <input type="button" class="btn btn-inverse" value="<?php echo JText::_("GURU_CLOSE"); ?>" onclick="document.location.href='<?php echo JRoute::_("index.php?option=com_guru&view=guruauthor&task=authorquizzes&layout=authorquizzes"); ?>';" />
                        </div>
                    </div> 
                    <div id="g_teacher_fe">   
                        <form action="<?php echo JUri::root() ?>index.php" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data" class="form-horizontal">
                           <div class="final_exam_teacher_page page_title">
                                <h2> 
                                    <?php
                                        if($program->id < 1){
                                            echo JText::_('GURU_NEW_FE_CREATION');
                                        }
                                        else{
                                            echo JText::_('GURU_EDIT_EXISTING_FE');
                                        }
                                    ?>
                               </h2>
                            </div>
                            
                            <div id="g_registrationformauthorcontent_mobile" class="g_mobile">
                                <div class="container-fluid">
                                    <div id="accordion" class="accordion">
                                        <div class="accordionItem">
                                            <h3 class="g_accordion-group ui-corner-all g_title_active"><?php echo JText::_("GURU_GENERAL");?></h3>
                                            <div class="clearfix tab-body  g_accordion-group g_content_active">
                                            	<div class="control-group clearfix g_row_inner">
                                                    <div class="g_cell span2">
                                                        <div>
                                                            <label class="control-label" for="name"><?php echo JText::_("GURU_NAME");?>: <span class="guru_error">*</span>&nbsp;&nbsp;</label>
                                                        </div>
                                                    </div>       
                                                    <div class="controls g_cell span5">
                                                        <input class="inputbox" type="text" id="name" name="name" size="40" maxlength="255" value="<?php echo $program->name; ?>" />
                                                        <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_QUIZ_NAME"); ?>" >
                                                            <img border="0" src="components/com_guru/images/icons/tooltip.png">
                                                        </span>  
                                                    </div>
                                                </div>
                                                <div class="control-group clearfix g_row_inner">
                                                    <div class="g_cell span2">
                                                        <div>
                                                            <label class="control-label" for="name"><?php echo JText::_("GURU_PRODDESC");?>:</label>
                                                        </div>
                                                    </div>        
                                                    <div class="controls g_cell span5">
                                                        <textarea name="description" id="description" style="width:100%;" rows="8"><?php echo stripslashes($program->description); ?></textarea>
                                                        <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_QUIZ_PRODDESC"); ?>" >
                                                            <img border="0" src="components/com_guru/images/icons/tooltip.png">
                                                        </span> 
                                                    </div>
                                                </div>
                                                
                                                <div class="control-group clearfix g_row_inner">
                                                    <div class="g_cell span2">
                                                        <div>
                                                            <label class="control-label" for="name"><?php echo JText::_("GURU_SHOW_CORRECT_ANS");?>:</label>
                                                        </div>
                                                    </div>        
                                                    <div class="controls g_cell span5">
                                                        <fieldset class="radio btn-group" id="show_correct_ans">
                                                            <?php
                                                                $no_checked = "";
                                                                $yes_cheched = "";
                                                                
                                                                if($program->show_correct_ans == "0"){
                                                                    $no_checked = 'checked="checked"';
                                                                }
                                                                else{
                                                                    $yes_cheched = 'checked="checked"';
                                                                }
                                                            ?>
                                                            <input type="hidden" name="show_correct_ans" value="0">
                                                            <input type="checkbox" <?php echo $yes_cheched; ?> value="1" name="show_correct_ans" class="ace-switch ace-switch-5">
                                                            <span class="lbl"></span>
                                                        </fieldset>
                                                        
                                                        <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_SHOW_CORRECT_ANS"); ?>" >
                                                            <img border="0" src="components/com_guru/images/icons/tooltip.png">
                                                        </span>
                                                    </div>
                                                </div>
                                                
                                                 <div class="control-group clearfix g_row_inner">
                                                    <div class="g_cell span2">
                                                        <div>
                                                            <label class="control-label" for="name"><?php echo JText::_("GURU_MINIMUM_SCORE_FINAL_QUIZ");?>:</label>
                                                        </div>
                                                    </div>        
                                                    <div class="controls g_cell span3">
                                                        <div>
                                                            <div>
                                                                <?php if (isset($program->max_score)){
                                                                        $program->max_score = $program->max_score;
                                                                      }
                                                                      else{
                                                                        $program->max_score = 70;
                                                                      }
                                                                
                                                                
                                                                
                                                                ?>
                                                                    <input class="input-mini" type="text" id="max_score_pass" name="max_score_pass" value="<?php echo $program->max_score;?>" style="float:left !important;" />
                                                            </div> 
                                                        </div>           
                                                    </div>
                                                    <div class="controls g_cell span2">
                                                        <span>%</span>
                                                    </div>
                                                     <div class="controls g_cell span3">
                                                        <select id="show_max_score_pass" name="show_max_score_pass"  style="float:left !important;" >
                                                            <option value="0" <?php if($program->pbl_max_score == "0"){echo 'selected="selected"'; } ?>><?php echo JText::_("GURU_SHOW"); ?></option>
                                                            <option value="1" <?php if($program->pbl_max_score == "1"){echo 'selected="selected"'; } ?>><?php echo JText::_("GURU_HIDE"); ?></option>
                                                        </select>
                                                    </div>
                                                     <div class="controls g_cell span2">
                                                         <span class="editlinktip hasTip" title="<?php echo JText::_('GURU_MAX_SCORE_TOOLTIP'); ?>" >
                                                            <img border="0" src="components/com_guru/images/icons/tooltip.png">
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="control-group clearfix g_row_inner">
                                                    <div class="g_cell span2">
                                                        <div>
                                                            <label class="control-label" for="name"><?php echo JText::_("GURU_QUIZ_CAN_BE_TAKEN");?>:</label>
                                                        </div>
                                                    </div>        
                                                    <div class="controls g_cell span3">
                                                        <div>
                                                            <div>
                                                               <select id="nb_quiz_taken" name="nb_quiz_taken" style="float:left !important;" >
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
                                                            </div>
                                                        </div>        
                                                    </div>
                                                    <div class="controls g_cell span2">
                                                        <div>
                                                            <div>
                                                                <?php echo JText::_("GURU_TIMES_T"); ?>
                                                            </div>
                                                        </div>        
                                                    </div>
                                                     <div class="controls g_cell span4">
                                                        <div>
                                                            <div>
                                                                 <select id="show_nb_quiz_taken" name="show_nb_quiz_taken" style="float:left !important;" >
                                                                        <option value="0" <?php if($program->show_nb_quiz_taken == "0"){echo 'selected="selected"'; } ?>><?php echo JText::_("GURU_SHOW"); ?></option>
                                                                        <option value="1" <?php if($program->show_nb_quiz_taken == "1"){echo 'selected="selected"'; } ?>><?php echo JText::_("GURU_HIDE"); ?></option>
                                                                </select>
                                                            </div>
                                                        </div>        
                                                    </div>
                                                     <div class="controls g_cell span1">
                                                         <span class="editlinktip hasTip" title="<?php echo JText::_('GURU_TIMES_TAKEN_TOOLTIP'); ?>" >
                                                            <img border="0" src="components/com_guru/images/icons/tooltip.png">
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="control-group clearfix g_row_inner">
                                                    <div class="g_cell span2">
                                                        <div>
                                                            <label class="control-label" for="name"><?php echo JText::_("GURU_SELECT_UP_TO");?>:</label>
                                                        </div>
                                                    </div>        
                                                    <div class="controls g_cell span3">
                                                        <div>
                                                            <div>
                                                                <select id="nb_quiz_select_up" name="nb_quiz_select_up" style="float:left !important;" >
                                                                        <?php
                                                                        if (isset($program->nb_quiz_select_up)){
                                                                            $program->nb_quiz_select_up = $program->nb_quiz_select_up;
                                                                        }
                                                                        else{
                                                                            $program->nb_quiz_select_up = 10;
                                                                        }
                                                                        
                                                                            for($i=1; $i<=100; $i++){?>
                                                                                <option value="<?php echo $i;?>" <?php if($program->nb_quiz_select_up == $i){echo 'selected="selected"'; }?> ><?php echo $i;?></option>
                                                                            <?php 
                                                                            }
                                                                            ?>
                                                                </select>
                                                           </div>
                                                       </div>         
                                                    </div>
                                                    <div class="controls g_cell span2">
                                                        <div>
                                                            <div>
                                                                <?php echo JText::_("GURU_QUESTION_RANDOM"); ?>
                                                            </div>
                                                        </div>        
                                                    </div>
                                                     <div class="controls g_cell span4">
                                                        <div>
                                                            <div>
                                                                 <select id="show_nb_quiz_select_up" name="show_nb_quiz_select_up" style="float:left !important;" >
                                                                        <option value="0" <?php if($program->show_nb_quiz_select_up == "0"){echo 'selected="selected"'; } ?>><?php echo JText::_("GURU_SHOW"); ?></option>
                                                                        <option value="1" <?php if($program->show_nb_quiz_select_up == "1"){echo 'selected="selected"'; } ?>><?php echo JText::_("GURU_HIDE"); ?></option>
                                                                </select>
                                                            </div>
                                                        </div>        
                                                    </div>
                                                     <div class="controls g_cell span1">
                                                         <span class="editlinktip hasTip" title="<?php echo JText::_('GURU_QUESTIONS_NUMBER_TOOLTIP'); ?>" >
                                                            <img border="0" src="components/com_guru/images/icons/tooltip.png">
                                                        </span>
                                                    </div>
                                                </div>
                                                
                                                <div class="control-group clearfix g_row_inner">
                                                    <div class="g_cell span2">
                                                        <div>
                                                            <label class="control-label" for="name"><?php echo JText::_("GURU_QUESTIONS_PER_PAGE");?></label>
                                                        </div>
                                                    </div>       
                                                    <div class="controls g_cell span5">
                                                        <?php
                                                            $questions_per_page = $program->questions_per_page;
                                                        ?>
                                                        <select name="questions_per_page">
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
                                                
                                                <div class="control-group clearfix g_row_inner">
                                                    <div class="g_cell">
                                                        <div>
                                                            <label class="control-label" for="name"><?php echo JText::_("GURU_IF_STUDENT_FAILED");?></label>
                                                        </div>  
                                                    </div>      
                                                    <div class="controls">
                                                        <div>
                                                            <div>
                                                                <select name="student_failed">
                                                                    <option value="0" <?php if($program->student_failed == "0"){echo 'selected="selected"';} ?> ><?php echo JText::_("GURU_STUDENT_CONTINUE"); ?></option>
                                                                    <option value="1" <?php if($program->student_failed == "1"){echo 'selected="selected"';} ?> ><?php echo JText::_("GURU_STUDENT_NOT_CONTINUE"); ?></option>
                                                                </select>
                                                                &nbsp;
                                                                <span class="editlinktip hasTip" title="<?php echo JText::_('GURU_IF_STUDENT_FAILED_TIP'); ?>" >
                                                                    <img border="0" src="components/com_guru/images/icons/tooltip.png">
                                                                </span>
                                                           </div>
                                                       </div>         
                                                  </div>
                                                </div>
                                                
                                                <div class="control-group clearfix g_row_inner">
                                                    <div class="g_cell span2">
                                                        <div>
                                                            <label class="control-label g_cell span12" for="name"><?php echo "TIMER";?>:</label>
                                                        </div>
                                                    </div>        
                                                </div>
                                                <div class="control-group clearfix g_row_inner">
                                                    <div class="g_cell span2">
                                                        <div>
                                                            <label class="control-label g_cell span2" for="name"><?php echo JText::_("GURU_EXAM_LIMIT_TIME");?>:</label>
                                                        </div>
                                                    </div>        
                                                    <div class="controls g_cell span3">
                                                        <div>
                                                            <div>
                                                                <?php if (isset($program->limit_time)){
                                                                            $program->limit_time = $program->limit_time;
                                                                          }
                                                                          else{
                                                                            $program->limit_time = 3;
                                                                          }
                                                                    
                                                                    
                                                                    
                                                                    ?>
                                                                        <input class="input-mini" type="text" id="limit_time_l" name="limit_time_l" maxlength="255" value="<?php echo $program->limit_time; ?>" style="float:left !important;" />								
                                                           </div>
                                                        </div>                                                         
                                                    </div>
                                                    <div class="controls g_cell span2">
                                                     <span>&nbsp;<?php echo JText::_('GURU_PROGRAM_DETAILS_MINUTES'); ?>&nbsp;</span>
                                                   </div>   
                                                    <div class="controls g_cell span4">  
                                                      <select id="show_limit_time" name="show_limit_time" style="float:left !important;" >
                                                                <option value="0" <?php if($program->show_limit_time == "0"){echo 'selected="selected"'; } ?>><?php echo JText::_("GURU_SHOW"); ?></option>
                                                                <option value="1" <?php if($program->show_limit_time == "1"){echo 'selected="selected"'; } ?>><?php echo JText::_("GURU_HIDE"); ?></option>
                                                        </select>
                                                    </div>
                                                    <div class="controls g_cell span1">    
                                                        <span class="editlinktip hasTip" title="<?php echo JText::_('GURU_QUIZ_LIMIT_TIME_TOOLTIP'); ?>" >
                                                                <img border="0" src="components/com_guru/images/icons/tooltip.png">
                                                        </span> 
                                                    </div>
                                                </div>
                                                <div class="control-group clearfix g_row_inner">
                                                    <div class="g_cell span2">
                                                        <div>
                                                            <label class="control-label" for="name"><?php echo JText::_("GURU_SHOW_COUNTDOWN");?>:</label>
                                                        </div>
                                                    </div>        
                                                    <div class="controls g_cell span6">
                                                        <select id="show_countdown" name="show_countdown" style="float:left !important;" >
                                                                <option value="0" <?php if($program->show_countdown == "0"){echo 'selected="selected"'; } ?>><?php echo JText::_("GURU_SHOW"); ?></option>
                                                                <option value="1" <?php if($program->show_countdown == "1"){echo 'selected="selected"'; } ?>><?php echo JText::_("GURU_HIDE"); ?></option>
                                                        </select>
                                                    </div>
                                                    <div class="controls g_cell span4">   
                                                        <span class="editlinktip hasTip" title="<?php echo JText::_('GURU_SHOW_COUNTDOWN_TOOLTIP'); ?>" >
                                                                <img border="0" src="components/com_guru/images/icons/tooltip.png">
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="control-group clearfix g_row_inner">
                                                    <div class="g_cell span2">
                                                        <div>
                                                            <label class="control-label g_cell span2" for="name"><?php echo JText::_("GURU_FINISH_ALERT");?>:</label>
                                                        </div>
                                                    </div>        
                                                    <div class="controls g_cell span3">
                                                        <?php if (isset($program->limit_time_f)){
                                                                $program->limit_time_f = $program->limit_time_f;
                                                              }
                                                              else{
                                                                $program->limit_time_f = 1;
                                                              }
                                                        
                                                        
                                                        
                                                        ?>
                                                            <input class="input-mini" type="text" id="limit_time_f" name="limit_time_f" maxlength="255" value="<?php echo $program->limit_time_f; ?>" style="float:left !important;" />
                                                  </div> 
                                                  <div class="controls g_cell span2">
                                                    <div>
                                                        <div>
                                                            <span>&nbsp;<?php echo JText::_('GURU_MINUTES_BEFORE_IS_UP'); ?>&nbsp;</span>
                                                        </div>
                                                    </div>        
                                                  </div>  
                                                  <div class="controls g_cell span4">  
                                                    <div>
                                                        <div> 
                                                            <select id="show_finish_alert" name="show_finish_alert" style="float:left !important;" >
                                                                    <option value="0" <?php if($program->show_finish_alert == "0"){echo 'selected="selected"'; } ?>><?php echo JText::_("GURU_SHOW"); ?></option>
                                                                    <option value="1" <?php if($program->show_finish_alert == "1"){echo 'selected="selected"'; } ?>><?php echo JText::_("GURU_HIDE"); ?></option>
                                                            </select>
                                                        </div>
                                                    </div>        
                                                 </div>
                                                 <div class="controls g_cell span2">   
                                                    <span class="editlinktip hasTip" title="<?php echo JText::_('GURU_FINISH_ALERT_TOOLTIP'); ?>" >
                                                            <img border="0" src="components/com_guru/images/icons/tooltip.png">
                                                    </span>
                                                </div>
                                               </div>
                                            </div>
										</div>
                                        
                                        <div class="accordionItem">
                                            <h3 class="g_accordion-group ui-corner-all g_title_active"><?php echo JText::_("GURU_QUIZZES_INCLUDED");?></h3>
                                            <div class="clearfix tab-body  g_accordion-group g_content_active">
                                            	<table class="table">
                                                    <tr>
                                                        <td>
                                                            <div>
                                                                <div style="float:left;">
                                                                    <a data-toggle="modal" data-target="#myModal" onClick = "showContent('<?php echo JURI::root();?>index.php?option=com_guru&controller=guruAuthor&task=addquizzes&tmpl=component&cid=<?php echo $program->id; ?>');" style="color:#0b55c4 !important;"  href="#"><?php echo JText::_("GURU_ADD_QUIZZES"); ?></a>
                                                                    
                                                                </div>
                                                                <div style="float:left;">
                                                                    <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_QUIZZES"); ?>" >
                                                                        <img border="0" src="components/com_guru/images/icons/tooltip.png">
                                                                    </span>
                                                                </div>
                                                            </div>
                                                            <br/><br/>
                                                            <?php						
                                                                $db =JFactory::getDBO();
                                                                $e = JFactory::getApplication()->input->get("e", "0");
                                                                $cid = JFactory::getApplication()->input->get("cid", "0");
                                                                
                                                                $sql = "SELECT 	quizzes_ids FROM #__guru_quizzes_final WHERE qid=".intval($cid);
                                        
                                                                $db->setQuery($sql);
                                                                $db->execute();
                                                                $result = $db->loadAssocList();
                                                                
                                                                $listofids = array();
                                                                foreach($result as $value){
                                                                    //$result_ids = explode(",",trim($value['quizzes_ids']));
                                                                    $listofids = array_merge($listofids, (array)$value["quizzes_ids"]);
                                                                }
                                                                $listofids = implode(",", array_unique($listofids));
                                                                $listofids = str_replace(",,", ",", $listofids);
                                                                $listofids = "0".$listofids;
                                                                
                                                                
                                                                
                                                                    $sql = "SELECT id, name, published FROM #__guru_quiz WHERE id IN (".$listofids.")";
                                                                    $db->setQuery($sql);
                                                                    $db->execute();
                                                                    $result_name=$db->loadAssocList();	
                                                                
                                                                 ?>
                                                            <table cellspacing="1" cellpadding="5" border="0" bgcolor="#cccccc" width="100%">                  
                                                                <tbody id="rowquestion" <?php if(!isset($result_name)) { echo 'style="display: none;"';} ?>>
                                                                    <tr>
                                                                        <td width="42%">
                                                                            <strong><?php echo JText::_('GURU_QUIZZES_FRONTEND');?></strong>
                                                                        </td>
                                                                        <td width="17%">
                                                                            <strong><?php echo JText::_('GURU_REMOVE');?></strong>
                                                                        </td>
                                                                        <td width="12%">
                                                                           <!-- <strong><?php //echo JText::_('Edit');?></strong>-->
                                                                        </td>
                                                                        <td width="14%">
                                                                            <strong><?php echo JText::_('GURU_PUBLISHED');?></strong>
                                                                        </td>
                                                                    </tr>                                   
                                                                 <?php 
                                                                if(isset($data_post['deleteq'])){
                                                                        $hide_q2del = $data_post['deleteq'];
                                                                }
                                                                else{
                                                                    $hide_q2del = ',';
                                                                }
                                                                $hide_q2del = explode(',', $hide_q2del);
                                                                 
                                                                 for ($i = 0; $i < count($result_name); $i++){ 
                                                                    $link2_remove = '<font color="#FF0000"><span onClick="delete_fq('.$result_name[$i]["id"].','.intval(@$data_get['cid']).', 1)">Remove</span></font>';
                                                                    $sql = "SELECT 	published FROM #__guru_quizzes_final WHERE qid=".$result_name[$i]["id"];
                                                                    $db->setQuery($sql);
                                                                    $db->execute();
                                                                    $published=$db->loadColumn();	
                                                                 ?>
                                                                 
                                                                  <tr id="trfque<?php echo $result_name[$i]["id"]; ?>" <?php if(in_array($result_name[$i]["id"],$hide_q2del)) { ?> style="display:none" <?php } ?>>
                                                                        <td width="42%">
                                                                            <strong><?php echo $result_name[$i]["name"];?></strong>
                                                                        </td>
                                                                         <td width="17%">
                                                                            <?php echo $link2_remove;  ?>
                                                                        </td>
                                                                        <td width="12%">
                                                                           
                                                                                <?php 	if(isset($published["0"]) && $published["0"] == 1){ 
                                                                                            echo "<input type='hidden' id='publ".$result_name[$i]["id"]."' name='publish_q[".$result_name[$i]["id"]."]' value='1' />";
                                                                                        } 
                                                                                        else{ 
                                                                                            echo "<input type='hidden' id='publ".$result_name[$i]["id"]."' name='publish_q[".$result_name[$i]["id"]."]' value='0' />";
                                                                                        }?>
                                                                        </td>
                                                                        <td width="14%" id="publishing<?php echo $result_name[$i]["id"];?>">
                                                                            <?php 
                                                                                if($result_name[$i]["published"] == 1) {
                                                                                    echo '<a class="icon-ok" style="cursor: pointer; text-decoration: none;" onclick="javascript:unpublish(\''.$result_name[$i]["id"].'\');"></a>';
                                                                                }
                                                                                else{
                                                                                    echo '<a class="icon-remove" style="cursor: pointer; text-decoration: none;" onclick="javascript:publish(\''.$result_name[$i]["id"].'\');"></a>';
                                                                                }
                                                                            ?>
                                                                        </td>
                                                                   </tr>      
                                                                   <?php } ?>     
                                                                    
                                                                 
                                                                    <input type="hidden" value="<?php if (isset($data_post['newquizq'])) echo $data_post['newquizq'];?>" id="newquizq" name="newquizq" >
                                                                    <input type="hidden" value="<?php if (isset($data_post['deleteq'])) echo $data_post['deleteq'];?>" id="deleteq" name="deleteq" >
                                                                </tbody>
                                                            </table>
                                                        </td>
                                                    </tr>
                                        
                                                </table>
                                            </div>
										</div>
                                        
                                        <div class="accordionItem">
                                            <h3 class="g_accordion-group1 ui-corner-all g_title_active"><?php //echo JText::_("GURU_PUBLISHING");?></h3>
                                            <div class="clearfix tab-body  g_accordion-group g_content_active">
                                            	<div class="g_date_style">
                                                    <div class="control-group clearfix g_row_inner">
                                                        <div class="g_cell span4">
                                                            <div>
                                                                <label class="control-label" for="name"><?php echo JText::_("GURU_PRODLPBS");?>:</label>
                                                            </div>
                                                        </div>            
                                                        <div class="controls g_cell span4">
                                                            <?php echo $lists['published']; ?>
                                                         </div>
                                                         <div class="controls g_cell span4"> 
                                                            <div>
                                                                <div>  
                                                                    <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_QUIZ_PRODDESC"); ?>" >
                                                                        <img border="0" src="components/com_guru/images/icons/tooltip.png">
                                                                    </span>
                                                                </div>
                                                            </div>        
                                                        </div>
                                                    </div>
                                                    <div class="control-group clearfix g_row_inner">
                                                        <div class="g_cell span4">
                                                            <div>
                                                                <label class="control-label" for="name"><?php echo JText::_("GURU_PRODLSPUB");?>:</label>
                                                            </div>
                                                        </div>        
                                                        <div class="controls g_cell span4">
                                                            <div>
                                                                <div>
                                                                    <?php 
																		$jnow 	= new JDate('now');
																		$now 	= $jnow->toSQL(true);
                                                                        if ($program->id<1){
                                                                            $start_publish =  date("".$dateformat."", $now);
                                                                        }
                                                                        else{
                                                                            $start_publish =  date("".$dateformat."", strtotime($program->startpublish));
                                                                        }
                                                                        echo JHTML::_('calendar', $start_publish, 'startpublish', 'startpublish', $format, array('class'=>'input-mini', 'size'=>'25',  'maxlength'=>'19')); ?>
                                                                 </div>
                                                             </div>           
                                                         </div>
                                                         <div class="controls g_cell span4">  
                                                            <div>
                                                                <div> 
                                                                    <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_QUIZ_PRODLSPUB"); ?>" >
                                                                        <img border="0" src="components/com_guru/images/icons/tooltip.png">
                                                                    </span>
                                                                </div>
                                                            </div>        
                                                        </div>
                                                    </div>
                                                    <div class="control-group clearfix g_row_inner">
                                                        <div class="g_cell span4">
                                                            <div>
                                                                <label class="control-label" for="name"><?php echo JText::_("GURU_PRODLEPUB");?>:</label>
                                                            </div> 
                                                        </div>       
                                                        <div class="controls g_cell span4">
                                                            <div>
                                                                <div>
                                                                <?php 
                                                                     if(substr($program->endpublish,0,4) =='0000' || $program->endpublish == JText::_('GURU_NEVER')|| $program->id<1) $program->endpublish = ""; else $program->endpublish = date("".$dateformat."", strtotime($program->endpublish));
                                                                    echo JHTML::_('calendar', $program->endpublish, 'endpublish', 'endpublish', $format, array('class'=>'input-mini', 'size'=>'25',  'maxlength'=>'19')); ?>
                                                                </div>
                                                            </div>        
                                                         </div>
                                                         <div class="controls g_cell span4">  
                                                            <div>
                                                                <div> 
                                                                     <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_QUIZ_PRODLEPUB"); ?>" >
                                                                        <img border="0" src="components/com_guru/images/icons/tooltip.png">
                                                                    </span>
                                                                </div>
                                                            </div>        
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
										</div>
                                        
									</div>
								</div>
							</div>
                            	
                            <input type="hidden" name="id" value="<?php echo $program->id; ?>" />
                            <input type="hidden" name="task" value="" />
                            <input type="hidden" name="valueop" value="<?php echo $value_option; ?>"/>
                            <input type="hidden" name="option" value="com_guru" />
                            <input type="hidden" name="image" value="<?php if(isset($data_post['image'])){echo $data_post['image'];}else{echo $program->image;}?>" />
                            <input type="hidden" name="controller" value="guruAuthor" />
                            <input type="hidden" name="time_format" id="time_format" value="<?php echo $format; ?>" />
                        </form>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>