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
$data_post = JFactory::getApplication()->input->post->getArray();
?>

<script type="text/javascript" language="javascript">
	document.body.className = document.body.className.replace("modal", "");
</script>

<style>
	.alert-info { height:auto!important;}
	.accordionItem.hideTabs div { display: none; }
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
	
	function save_quizFE(pressbutton){
		var form = document.adminForm;
		if (pressbutton == 'save_quizFE' || pressbutton == 'apply_quizFE') {
			if (form['name'].value == "") {
				alert( "<?php echo JText::_("GURU_TASKS_JS_NAME");?>" );
			} 
			else {
				//---------------------------------
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
				//---------------------------------
				//submitform( pressbutton );
                form.task.value = pressbutton;
                form.submit();
			}
		}
		else {
			//submitform( pressbutton );
            form.task.value = pressbutton;
            form.submit();
		}
	}
	
	function showContent(href){
		first = true;
		jQuery( '#myModal .modal-body iframe').attr('src', href);
	}
	
	function closeModal(){
		jQuery('#myModal .modal-body iframe').attr('src', '');
	}
</script>

<div id="myModal" class="modal g_modal hide" style="position: fixed !important;">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true" onclick="javascript:closeModal();">&times;</button>
    </div>
    <div class="modal-body">
    	<iframe id="g_quiz_phone"></iframe>
    </div>
</div>
<div class="g_row">
	<div class="g_cell span12">
		<div>
			<div>
                <div id="g_myquizzesaddedit" class="clearfix com-cont-wrap">
					<?php 	echo $div_menu; //MENU TOP OF AUTHORS?>
                     <div class="row-fluid clearfix">
                        <div class="span12 pagination-right g_margin_bottom">
                                <input type="button" class="btn btn-success" value="<?php echo JText::_("GURU_SAVE"); ?>" onclick="javascript:save_quizFE('apply_quizFE');" />
                                <input type="button" class="btn btn-success" value="<?php echo JText::_("GURU_SAVE_AND_CLOSE"); ?>" onclick="javascript:save_quizFE('save_quizFE');" />
                                <input type="button" class="btn btn-inverse" value="<?php echo JText::_("GURU_CLOSE"); ?>" onclick="document.location.href='<?php echo JRoute::_("index.php?option=com_guru&view=guruauthor&task=authorquizzes&layout=authorquizzes"); ?>';" />
                        </div>
                    </div> 
                    <div id="g_teacher_quiz">   
                            <form action="<?php echo JUri::root() ?>index.php" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data" class="form-horizontal">
                                    <div class="well">
                                        <?php
                                            if($program->id < 1){
                                                echo JText::_('GURU_NEW_Q_CREATION');
                                            }
                                            else{
                                                echo JText::_('GURU_EDIT_EXISTING_QUIZ');
                                            }
                                        ?>
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
                                                                <div>
                                                                    <div>
                                                                        <input class="inputbox" type="text" id="name" name="name" size="40" maxlength="255" value="<?php echo $program->name; ?>" />
                                                                        <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_QUIZ_NAME"); ?>" >
                                                                            <img border="0" src="components/com_guru/images/icons/tooltip.png">
                                                                        </span>
                                                                    </div> 
                                                                </div>     
                                                            </div>
                                                        </div>
                                                        <div class="control-group clearfix g_row_inner">
                                                            <div class="g_cell span2">
                                                                <div>
                                                                    <label class="control-label" for="name"><?php echo JText::_("GURU_PRODDESC");?>:</label>
                                                                </div>
                                                            </div>        
                                                            <div class="controls g_cell span5">
                                                                <div>
                                                                    <div>
                                                                        <textarea name="description" id="description" style="width:100%;" rows="8"><?php echo stripslashes($program->description); ?></textarea>
                                                                        <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_QUIZ_PRODDESC"); ?>" >
                                                                            <img border="0" src="components/com_guru/images/icons/tooltip.png">
                                                                        </span>
                                                                    </div>
                                                                </div>        
                                                            </div>
                                                        </div>
                                                        
                                                        <div class="control-group clearfix g_row_inner">
                                                            <div class="g_cell span2">
                                                                <div>
                                                                    <label class="control-label" for="name"><?php echo JText::_("GURU_SHOW_CORRECT_ANS");?>:</label>
                                                                </div>
                                                            </div>        
                                                            <div class="controls g_cell span5">
                                                                <div>
                                                                    <div>
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
                                                            </div>
                                                        </div>
                                                        
                                                         <div class="control-group clearfix g_row_inner">
                                                            <div class="g_cell span2">
                                                                <div>
                                                                    <label class="control-label" for="author_blog"><?php echo JText::_("GURU_MINIMUM_SCORE_QUIZ");?>:</label>
                                                                </div>
                                                            </div>        
                                                            <div class="controls g_cell span4">
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
                                                                            <input type="text" id="max_score_pass" name="max_score_pass" value="<?php echo $program->max_score;?>" style="float:left !important;" />&nbsp;
                                                                        </span> 
                                                                   </div>
                                                               </div>            
                                                            </div>
                                                             <div class="controls g_cell span4">
                                                                <div>
                                                                    <div>
                                                                        <span>
                                                                             <select id="show_max_score_pass" name="show_max_score_pass"  style="float:left !important;" >
                                                                                <option value="0" <?php if($program->pbl_max_score == "0"){echo 'selected="selected"'; } ?>><?php echo JText::_("GURU_SHOW"); ?></option>
                                                                                <option value="1" <?php if($program->pbl_max_score == "1"){echo 'selected="selected"'; } ?>><?php echo JText::_("GURU_HIDE"); ?></option>
                                                                            </select>
                                                                        </span> 
                                                                    </div>
                                                               </div>        
                                                            </div>
                                                            <div class="controls g_cell span1"> 
                                                                <div> 
                                                                    <div>     
                                                                        <span class="editlinktip hasTip" title="<?php echo JText::_('GURU_MAX_SCORE_TOOLTIP'); ?>" >
                                                                            <img border="0" src="components/com_guru/images/icons/tooltip.png">
                                                                        </span> 
                                                                    </div>
                                                                </div>           
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
                                                                         <span>
                                                                            <?php echo JText::_("GURU_TIMES_T"); ?>
                                                                         </span>
                                                                    </div>
                                                                </div>         
                                                            </div>
                                                             <div class="controls g_cell span3">
                                                                <div>
                                                                    <div>
                                                                        <select id="show_nb_quiz_taken" name="show_nb_quiz_taken" style="float:left !important;" >
                                                                                <option value="0" <?php if($program->show_nb_quiz_taken == "0"){echo 'selected="selected"'; } ?>><?php echo JText::_("GURU_SHOW"); ?></option>
                                                                                <option value="1" <?php if($program->show_nb_quiz_taken == "1"){echo 'selected="selected"'; } ?>><?php echo JText::_("GURU_HIDE"); ?></option>
                                                                        </select>&nbsp;
                                                                    </div>
                                                                </div>    
                                                             </div> 
                                                             <div class="controls g_cell span2"> 
                                                                <div>
                                                                    <div> 
                                                                        <span class="editlinktip hasTip" title="<?php echo JText::_('GURU_TIMES_TAKEN_TOOLTIP'); ?>" >
                                                                            <img border="0" src="components/com_guru/images/icons/tooltip.png">
                                                                        </span>
                                                                    </div>
                                                                </div>    
                                                            </div>
                                                        </div>    
                                                        <div class="control-group clearfix g_row_inner">
                                                            <div class="g_cell span2">
                                                                <div>
                                                                    <label class="control-label" for="name"><?php echo JText::_("GURU_SELECT_UP_TO");?>: <span class="guru_error">*</span>&nbsp;&nbsp;</label>
                                                                </div>  
                                                            </div>      
                                                            <div class="controls g_cell span3">
                                                                <div>
                                                                    <div>
                                                                        <select id="nb_quiz_select_up" name="nb_quiz_select_up" style="float:left !important;" >
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
                                                                   </div>
                                                               </div>         
                                                          </div>      
                                                          <div class="controls g_cell span2">
                                                            <div>
                                                                <div>
                                                                    <span>&nbsp;<?php echo JText::_('GURU_QUESTION_RANDOM'); ?>&nbsp;</span>
                                                                </div>
                                                            </div>        
                                                          </div>      
                                                          <div class="controls g_cell span3"> 
                                                            <div>
                                                                <div>     
                                                                   <select id="show_nb_quiz_select_up" name="show_nb_quiz_select_up" style="float:left !important;" >
                                                                            <option value="0" <?php if($program->show_nb_quiz_select_up == "0"){echo 'selected="selected"'; } ?>><?php echo JText::_("GURU_SHOW"); ?></option>
                                                                            <option value="1" <?php if($program->show_nb_quiz_select_up == "1"){echo 'selected="selected"'; } ?>><?php echo JText::_("GURU_HIDE"); ?></option>
                                                                    </select>
                                                                </div>
                                                            </div>        
                                                          </div>
                                                          <div class="controls g_cell span2">  
                                                            <div>
                                                                <div>    
                                                                   <span class="editlinktip hasTip" title="<?php echo JText::_('GURU_QUESTIONS_NUMBER_TOOLTIP'); ?>" >
                                                                            <img border="0" src="components/com_guru/images/icons/tooltip.png">
                                                                   </span>
                                                                </div>
                                                            </div>       
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
                                                                    <label class="control-label" for="name"><?php echo "TIMER";?>:</label>
                                                                </div>
                                                            </div>        
                                                        </div>
                                                        <div class="control-group clearfix g_row_inner">
                                                            <div class="g_cell span2">
                                                                <div>
                                                                    <label class="control-label" for="name"><?php echo JText::_("GURU_QUIZ_LIMIT_TIME");?>:</label>
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
                                                                            <input class="input-mini" type="text" id="limit_time_l" name="limit_time_l" value="<?php echo $program->limit_time; ?>" style="float:left !important;" />
                                                                    </div>
                                                                </div>    
                                                            </div>
                                                            <div class="controls g_cell span2">
                                                                <div>
                                                                    <div>
                                                                        <span>&nbsp;<?php echo JText::_('GURU_PROGRAM_DETAILS_MINUTES'); ?>&nbsp;</span>
                                                                    </div>
                                                                </div>        
                                                           </div>   
                                                            <div class="controls g_cell span3">
                                                                <div>
                                                                    <div>  
                                                                       <select id="show_limit_time" name="show_limit_time" style="float:left !important;" >
                                                                                <option value="0" <?php if($program->show_limit_time == "0"){echo 'selected="selected"'; } ?>><?php echo JText::_("GURU_SHOW"); ?></option>
                                                                                <option value="1" <?php if($program->show_limit_time == "1"){echo 'selected="selected"'; } ?>><?php echo JText::_("GURU_HIDE"); ?></option>
                                                                       </select>
                                                                    </div>
                                                                </div>    
                                                            </div>
                                                            <div class="controls g_cell span2">
                                                                <div>
                                                                    <div>    
                                                                        <span class="editlinktip hasTip" title="<?php echo JText::_('GURU_QUIZ_LIMIT_TIME_TOOLTIP'); ?>" >
                                                                                <img border="0" src="components/com_guru/images/icons/tooltip.png">
                                                                        </span>
                                                                    </div>
                                                               </div>          
                                                            </div>
                                                        </div>
                                                        <div class="control-group clearfix g_row_inner">
                                                            <div class="g_cell span2">
                                                                <div>
                                                                    <label class="control-label" for="name"><?php echo JText::_("GURU_SHOW_COUNTDOWN");?>:</label>
                                                                </div>
                                                            </div>        
                                                            <div class="controls g_cell span6">
                                                                <div>
                                                                    <div>
                                                                        <select id="show_countdown" name="show_countdown" style="float:left !important;" >
                                                                                <option value="0" <?php if($program->show_countdown == "0"){echo 'selected="selected"'; } ?>><?php echo JText::_("GURU_SHOW"); ?></option>
                                                                                <option value="1" <?php if($program->show_countdown == "1"){echo 'selected="selected"'; } ?>><?php echo JText::_("GURU_HIDE"); ?></option>
                                                                        </select>
                                                                    </div>
                                                                </div>        
                                                            </div>
                                                            <div class="controls g_cell span4">
                                                                <div>
                                                                    <div>   
                                                                        <span class="editlinktip hasTip" title="<?php echo JText::_('GURU_SHOW_COUNTDOWN_TOOLTIP'); ?>" >
                                                                                <img border="0" src="components/com_guru/images/icons/tooltip.png">
                                                                        </span>
                                                                    </div>
                                                               </div>         
                                                            </div>
                                                        </div>
                                                        <div class="control-group clearfix g_row_inner">
                                                            <div class="g_cell span2">
                                                                <div>
                                                                    <label class="control-label" for="name"><?php echo JText::_("GURU_FINISH_ALERT");?>:</label>
                                                                </div>
                                                            </div>       
                                                            <div class="controls g_cell span3">
                                                                <div>
                                                                    <div>
                                                                    <?php if (isset($program->limit_time_f)){
                                                                            $program->limit_time_f = $program->limit_time_f;
                                                                          }
                                                                          else{
                                                                            $program->limit_time_f = 1;
                                                                          }
                                                                    
                                                                    
                                                                    
                                                                    ?>
                                                                        <input type="text" id="limit_time_f" name="limit_time_f" class="input-mini" value="<?php echo $program->limit_time_f; ?>" style="float:left !important;" />
                                                                   </div>
                                                               </div> 
                                                          </div> 
                                                          <div class="controls g_cell span2">
                                                            <div>
                                                                <div>
                                                                    <span>&nbsp;<?php echo JText::_('GURU_MINUTES_BEFORE_IS_UP'); ?>&nbsp;</span>
                                                                </div>
                                                            </div>        
                                                          </div>  
                                                          <div class="controls g_cell span3"> 
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
                                                    <h3 class="g_accordion-group ui-corner-all g_title_active"><?php echo JText::_("GURU_QUESTIONS");?></h3>
                                                    <div class="clearfix tab-body  g_accordion-group g_content_active">
                                                    	<table class="table">
                                                            <tr>
                                                                <td>
                                                                    <div>
                                                                        <div style="float:left;">
                                                                            <a data-toggle="modal" data-target="#myModal" onclick="showContent('<?php echo JURI::root(); ?>index.php?option=com_guru&controller=guruAuthor&task=addquestion&tmpl=component&no_html=1&cid=<?php echo $program->id; ?>')" href="#">
																				<?php echo JText::_('GURU_ADD_QUESTION');?>
																			</a>
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
                                                                                <th width="42%">
                                                                                    <strong><?php echo JText::_('GURU_QUESTIONS');?></strong>
                                                                                </th>
                                                                                <th width="12%">
                                                                                    <strong><?php echo JText::_('GURU_EDIT');?></strong>
                                                                                </th>
                                                                                <th width="17%">
                                                                                    <strong><?php echo JText::_('GURU_REMOVE');?></strong>
                                                                                </th>
                                                                                <th>
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
                                                                            $link2_remove = '<font class="btn btn-danger"><span onClick="delete_q('.$mmedial->id.','.$program->id.',0)">'.JText::_('GURU_REMOVE').'</span></font>';
                                                                        ?>
                                                                            
                                                                            <tr class="row<?php echo $i%2;?>" id="trque<?php echo $mmedial->id; ?>" <?php if(in_array($mmedial->id,$hide_q2del)) { ?> style="display:none" <?php } ?>>
                                                                                <td id="tdq<?php echo $mmedial->id?>" width="42%">
                                                                                    <a data-toggle="modal" data-target="#myModal" onclick="showContent('<?php echo JURI::root(); ?>index.php?option=com_guru&controller=guruAuthor&task=editquestion&is_from_modal=1&tmpl=component&no_html=1&cid=<?php echo $program->id.'&qid='.$mmedial->id; ?>')" href="#">
																						<?php if (strlen ($mmedial->text) >55){echo substr(str_replace("\'","&acute;" ,$mmedial->text),0,55).'...';}else{echo str_replace("\'","&acute;" ,$mmedial->text);}?>
																					</a>
                                                                                    
                                                                                </td>
                                                                                 <td width="12%">
                                                                                    <a rel="{handler: 'iframe', size: {x: 770, y: 400}, iframeOptions: {id: 'g_quiz_phoneedit'}}"  href="<?php echo JURI::root(); ?>index.php?option=com_guru&controller=guruAuthor&task=editquestion&is_from_modal=1&tmpl=component&no_html=1&cid=<?php echo $program->id.'&qid='.$mmedial->id;?>" class="modal"><?php echo JText::_('GURU_EDIT');?></a>
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
                                                                    <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_QUIZ_PRODDESC"); ?>" >
                                                                        <img border="0" src="components/com_guru/images/icons/tooltip.png">
                                                                    </span>
                                                                </div>
                                                            </div>
                                                            <div class="control-group clearfix g_row_inner">
                                                                <div class="g_cell span4">
                                                                    <div>
                                                                        <label class="control-label" for="name"><?php echo JText::_("GURU_PRODLSPUB");?>:</label>
                                                                    </div>
                                                                </div>        
                                                                <div class="controls g_cell span4">
                                                                    <?php 
																		$jnow 	= new JDate('now');
                                                                		$now 	= $jnow->toSQL(true);
                                                                        if ($program->id<1){
                                                                            $start_publish =  date("".$dateformat."", $now);
                                                                        }
                                                                        else{
                                                                            $start_publish =  date("".$dateformat."", strtotime($program->startpublish));
                                                                        }
                                                                        echo JHTML::_('calendar', $start_publish, 'startpublish', 'startpublish', $format, array('class'=>'input-small', 'size'=>'25',  'maxlength'=>'19')); ?>
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
                                                                    <?php 
                                                                         if(substr($program->endpublish,0,4) =='0000' || $program->endpublish == JText::_('GURU_NEVER')|| $program->id<1) $program->endpublish = ""; else $program->endpublish = date("".$dateformat."", strtotime($program->endpublish));
                                                                        echo JHTML::_('calendar', $program->endpublish, 'endpublish', 'endpublish', $format, array('class'=>'input-small', 'size'=>'25',  'maxlength'=>'19')); ?>
                                                                 </div>
                                                                 <div class="controls g_cell span4">   
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
	
                                    
                                    
                                    
                                <input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
                                <input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
                                <?php echo JHtml::_('form.token'); ?>	
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
  </div>