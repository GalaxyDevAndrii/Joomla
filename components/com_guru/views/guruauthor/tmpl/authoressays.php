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
$div_menu = $this->authorGuruMenuBar();

$doc = JFactory::getDocument();
//$doc->addScript('components/com_guru/js/guru_modal.js');
$items = $this->items;

?>

<script type="text/javascript" language="javascript">
	document.body.className = document.body.className.replace("modal", "");
	
	function saveEssayScore(key, user_id, question_id, quiz_id, course_id, elm){
        elm.disabled = true;
		var teacher_answer = document.getElementById("teacher-response-"+key).value;
		var grade = document.getElementById("essay-grade-"+key).value;

        var url = "<?php echo JURI::base();?>index.php?option=com_guru&controller=guruAuthor&task=saveMark&tmpl=component&format=raw";

        jQuery.ajax({
            url : url,
            cache: false,
            data: {'user_id' : user_id, 'question_id' : question_id, 'quiz_id' : quiz_id, 'teacher_answer' : teacher_answer, 'grade' : grade, 'course_id' : course_id}
        })
        .done(function(transport) {
            if(transport == "1"){
                var filter_status = document.getElementById("filter_status").value;
                if(parseInt(filter_status) == 0){
                    var $grade = document.getElementById("grade-essays");
                    if ($grade) {
                        var grade_essays = $grade.innerHTML;
                        $grade.innerHTML = parseInt(grade_essays) - 1;
                    }
                    
                    
                    var row = document.getElementById("essay-row-"+key);
                    if (row) {
                        row.parentNode.removeChild(row);
                    }
                }
                else{
                    alert("<?php echo JText::_("GURU_GRADE_SAVED"); ?>");
                }
            }
        })
        .always(function() {
            elm.disabled = false;
        });
	}
</script>

<div id="g_myessaysauthor" class="clearfix com-cont-wrap">
    <?php
        echo $div_menu;
    ?>
    
    <form  action="<?php echo JUri::getInstance() ?>" class="form-horizontal" id="adminForm" method="post" name="adminForm">
        <div class="uk-grid uk-margin wk-grid wk-margin essays-search">
            <div class="uk-width-1-1 uk-width-medium-1-3 wk-width-1-1 wk-width-medium-1-3">
                <h2 class="gru-page-title"><?php echo JText::_('GURU_GRADE_ESSAYS');?></h2>
            </div>
            <div class="uk-width-1-1 uk-width-2-3 uk-text-right wk-width-1-1 wk-width-2-3 wk-text-right">
				<div>
                    <input type="text" class="form-control" name="filter_search" placeholder="<?php echo JText::_("GURU_SEARCH_ESSAYS"); ?>" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" />
                    <button class="uk-button uk-button-primary wk-button wk-button-primary hidden-phone" type="submit"><?php echo JText::_("GURU_GO"); ?></button>
                </div>
            </div>
        </div>
        
        <div class="uk-grid uk-margin wk-grid wk-margin essays-filters">
            <div class="uk-width-1-1 uk-width-medium-1-3 wk-width-1-1 wk-width-medium-1-3">
                <?php
					$filter_courses = $this->escape($this->state->get('filter.courses'));
                	echo $this->getUserListCourses($filter_courses);
				?>
            </div>
            <div class="uk-width-1-1 uk-width-1-3 wk-width-1-1 wk-width-1-3">
				<?php
					$filter_essays = $this->escape($this->state->get('filter.essays'));
                	echo $this->getUserListEssays($filter_essays);
				?>
            </div>
            <div class="uk-width-1-1 uk-width-1-3 wk-width-1-1 wk-width-1-3">
				<?php
					$filter_status = $this->escape($this->state->get('filter.status'));
                	echo $this->getUserEssayStatus($filter_status);
				?>
            </div>
        </div>
        
        <table class="uk-table uk-table-striped wk-table wk-table-striped" id="g_authoressays">
        	<tr>
            	<th width="20%">
                	<?php echo JText::_("GURU_STUDENT"); ?>
                </th>
                <th width="55%">
                	<?php echo JText::_("GURU_ANSWER"); ?>
                </th>
                <th width="25%">
                	<?php echo JText::_("GURU_SCORE"); ?>
                </th>
            </tr>
            
            <?php
            	if(isset($items) && count($items) > 0){
					foreach($items as $key=>$item){
			?>
            			<tr id="essay-row-<?php echo intval($key); ?>">
                        	<td class="text-center">
                            	<?php
									if(trim($item->image) == ""){
										$grav_url = "http://www.gravatar.com/avatar/".md5(strtolower(trim($item->email)))."?d=mm&s=40";
										echo '<img src="'.$grav_url.'" alt="'.$item->firstname.'" title="'.$item->firstname.'"/>';
									}
									else{
										echo '<img src="'.JURI::root().trim($item->image).'" style="width:40px;" />';
									}
									echo '<br />';
									echo $item->firstname." ".$item->lastname;
								?>
                            </td>
                            
                            <td>
                            	<div class="essay-question-title">
                                	<?php echo strip_tags($item->question_content); ?>
                                </div>
                                
                                <div class="essay-question-response">
                                	<?php echo $item->answers_given; ?>
                                </div>
                            </td>
                            
                            <td>
                            	<textarea id="teacher-response-<?php echo intval($key); ?>" class="essay-teacher-response"><?php echo trim($item->feedback_quiz_results); ?></textarea>
                                <div class="essay-actions">
                                	<div>
                                        <select id="essay-grade-<?php echo intval($key); ?>" style="width:auto !important;">
                                            <option value="0"> <?php echo JText::_("GURU_SELECT_GRADE"); ?> </option>
                                            <?php
                                                for($i=0; $i<=$item->points; $i++){
                                                    $selected = '';
                                                    
                                                    if(intval($item->grade) == $i){
                                                        $selected = 'selected="selected"';
                                                    }
                                                
                                                    echo '<option value="'.$i.'" '.$selected.'>'.$i.'</option>';
                                                }
                                            ?>
                                        </select>
                                    </div>
                                    
                                    <div>
                                    	<input type="button" class="uk-button uk-button-success wk-button wk-button-success essay-save-action" value="<?php echo JText::_("GURU_SAVE"); ?>" onclick="javascript:saveEssayScore(<?php echo intval($key); ?>, <?php echo intval($item->user_id); ?>, <?php echo intval($item->question_id); ?>, <?php echo intval($item->qid); ?>, <?php echo intval($item->pid); ?>, this);" />
                                    </div>
                                </div>
                            </td>
                        </tr>
            <?php
					}
				}
			?>
            
        </table>
        
        <div class="pagination-limit">
        	<?php echo $this->pagination->getLimitBox(); ?>
        </div>
        <div class="pagination-pages">
        	<?php
				$pages = $this->pagination->getPagesLinks();
				include_once(JPATH_SITE.DIRECTORY_SEPARATOR."components".DIRECTORY_SEPARATOR."com_guru".DIRECTORY_SEPARATOR."helpers".DIRECTORY_SEPARATOR."helper.php");
				$helper = new guruHelper();
				$pages = $helper->transformPagination($pages);
				echo $pages;
			?>
        </div>
        
        <input type="hidden" name="task" value="authoressays" />
        <input type="hidden" name="option" value="com_guru" />
        <input type="hidden" name="controller" value="guruAuthor" />
    </form>
    
</div>