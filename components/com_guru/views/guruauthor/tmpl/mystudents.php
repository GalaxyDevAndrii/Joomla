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
$db = JFactory::getDBO();
$div_menu = $this->authorGuruMenuBar();
$students = $this->students;
$doc = JFactory::getDocument();
$doc->setTitle(trim(JText::_('GURU_AUTHOR'))." ".trim(JText::_('GURU_AUTHOR_MY_STUDENTS')));
$isteacher = $this->isTeacherOrNot();
$config = $this->config;
$allow_teacher_action = json_decode($config->st_authorpage);//take all the allowed action from administator settings
$teacher_add_students = @$allow_teacher_action->teacher_add_students; //allow or not action Add students
@$from = JFactory::getApplication()->input->get("from", "");

//$doc->addScript('components/com_guru/js/guru_modal.js');
JHtml::stylesheet("components/com_guru/css/tabs.css");

if(isset($allow_teacher_action->teacher_menu_students) && $allow_teacher_action->teacher_menu_students == 1){
    echo $div_menu;
    echo '<div class="alert alert-info" style="margin-top: 20px; text-align: center;">'.JText::_("GURU_STUDENTS_LIST_NOT_AVAILABLE").'</div>';
    return true;
}

?>

<script type="text/javascript" language="javascript">
	document.body.className = document.body.className.replace("modal", "");
</script>

<script type="text/javascript" language="javascript">
	function guruExport(type){
		course = document.getElementById('filter_course').value;
		url = '<?php echo JURI::root(); ?>'+'index.php?option=com_guru&controller=guruAuthor&task=export_'+type+'&course='+course+'&tmpl=component&format=raw';
		var win = window.open(url, '_blank');
		win.focus();
	}
</script>

<div class="gru-mystudentslist">
    <?php echo $div_menu; //MENU TOP OF AUTHORS?>
  
    <h2 class="gru-page-title"><?php echo JText::_('GURU_AUTHOR_MY_STUDENTS');?></h2>
    <?php 
    if($isteacher >0){
    ?>
        <div id="g_mystudentsauthor" class="g_sect clearfix">
            <form class="form-horizontal" id="adminForm" method="post" name="adminForm" enctype="multipart/form-data" action="<?php echo JRoute::_("index.php?option=com_guru&view=guruauthor&task=mystudents&layout=mystudents") ?>">
                
                <!-- Start Search -->
                 <div class="gru-page-filters">
					<div class="gru-filter-item">
                        <input type="text" class="form-control" name="filter_search" id="filter_search" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" />
                        <button class="uk-button uk-button-primary wk-button wk-button-primary" type="submit"><?php echo JText::_("GURU_SEARCH"); ?></button>
					</div>
					
                    <div class="gru-filter-item"> 
                        <?php
                            $filter_course = $this->escape($this->state->get('filter.course'));
                            $my_courses = $this->getMyCourses();
                        ?>
                        <select name="filter_course" id="filter_course" onchange="document.adminForm.submit();">
                            <option value="0"> <?php echo JText::_("GURU_SELECT_COURSE"); ?> </option>
                            <?php
                                if(isset($my_courses) && count($my_courses) > 0){
                                    foreach($my_courses as $key=>$value){
                                        if($value["published"] == 0){
                                            continue;
                                        }
                                        $selected = "";
                                        if($value["id"] == $filter_course){
                                            $selected = 'selected="selected"';
                                        }
                                        echo '<option value="'.$value["id"].'" '.$selected.'>'.$value["name"].'</option>';
                                    }
                                }
                            ?>
                        </select>
					</div>
                    
                    <div class="gru-filter-item">
                        <a href="#" onclick="javascript:guruExport('pdf'); return false;" title="<?php echo JText::_("GURU_EXPORT_PDF"); ?>">
                            <img src="<?php echo JURI::root(); ?>components/com_guru/images/pdf.png">
                        </a>
                        
                        <a href="#" onclick="javascript:guruExport('csv'); return false;" title="<?php echo JText::_("GURU_EXPORT_CSV"); ?>">
                            <img src="<?php echo JURI::root(); ?>components/com_guru/images/excel.png">
                        </a>
                   </div> 
                </div><!-- /input-group -->
                <!-- End Search -->
                <div class="clearfix"></div>
                <div class="g_table_wrap g_margin_top">
                    <table id="g_authorstudent" class="uk-table uk-table-striped wk-table wk-table-striped">
                        <tr>
                            <th></th>
                            <th class="g_cell_3"><?php echo JText::_('GURU_FULL_NAME'); ?></th>
                            <th class="g_cell_4"><?php echo JText::_("GURU_STATS"); ?></th>
                            <th class="g_cell_5 hidden-phone"><?php echo JText::_("GURU_EMAIL"); ?></th>
                            <th class="g_cell_6 hidden-phone"><?php echo JText::_("GURU_PROGRAM_PROGRAMS"); ?></th>
                            <th class="g_cell_7"><?php echo JText::_("GURU_CERTIFICATES"); ?></th>
                        </tr>
                        <?php 
                        if(isset($students) && count($students) > 0 && $students !== FALSE){
                            $i = 0;
							$itemid = JFactory::getApplication()->input->get("Itemid", "0", "raw");
							
							$helper = new guruHelper();
							$itemid_seo = $helper->getSeoItemid();
							$itemid_seo = @$itemid_seo["guruauthor"];
							
							if(intval($itemid_seo) > 0){
								$itemid = intval($itemid_seo);
							}
							
                            foreach($students as $key=>$student){
                                $id = $student->id;
                                $checked = JHTML::_('grid.id', $i, $id);
                        ?>
                            <tr class="guru_row">
                                <td>
                                    <?php
                                        if(trim($student->image) == ""){
                                            $grav_url = "http://www.gravatar.com/avatar/".md5(strtolower(trim($student->email)))."?d=mm&s=40";
                                            echo '<img src="'.$grav_url.'" alt="'.$student->name.'" title="'.$student->name.'"/>';
                                        }
                                        else{
                                            echo '<img src="'.JURI::root().trim($student->image).'" style="width:40px;" />';
                                        }
                                    ?>
                                </td>
                                
                                <td class="g_cell_3">
                                    <?php
                                        $student_progress_url = JURI::root()."index.php?option=com_guru&view=guruauthor&layout=student_progress&id=".intval($id)."&tmpl=component";
                                    ?>

                                    <a class="hidden-phone" onclick="javascript:openMyModal(0, 0, '<?php echo $student_progress_url; ?>'); return false;" href="#">
                                        <?php echo $student->firstname." ".$student->lastname; ?>
                                    </a>
                                </td>
                                
                                <td class="g_cell_4">
                                    <?php
                                        $link_modal = JURI::root()."index.php?option=com_guru&view=guruauthor&layout=studentdetails&userid=".intval($id)."&tmpl=component";
                                        $link_phone = JRoute::_("index.php?option=com_guru&view=guruauthor&layout=studentdetails&userid=".intval($id)."&Itemid=".intval($itemid));
                                    ?>
                                    <a class="hidden-phone" onclick="javascript:openMyModal(0, 0, '<?php echo $link_modal; ?>'); return false;" href="#">
                                        <i class="fa fa-list"></i>
                                    </a>
                                    
                                    <a class="uk-hidden-large uk-hidden-medium wk-hidden-large wk-hidden-medium" href="<?php echo $link_phone; ?>">
                                        <i class="fa fa-list"></i>
                                    </a>
                                    
                                </td>
                                <td class="g_cell_5 hidden-phone"><?php echo $student->email;?></td>
                                <td class="g_cell_6 hidden-phone">
                                    <?php
                                        $courses = $student->courses;
                                        $courses = explode("-", $courses);
                                        $courses = array_unique($courses);
                                        $sum = count($courses);
                                    
                                    ?>
                                        <a class="hidden-phone" onclick="javascript:openMyModal(0, 0, '<?php echo $link_modal; ?>'); return false;" href="#">
                                            <?php echo intval($sum); ?>
                                        </a>
                                        
                                        <a class="uk-hidden-large uk-hidden-medium wk-hidden-large wk-hidden-medium" href="<?php echo $link_phone; ?>">
                                            <?php echo intval($sum); ?>
                                        </a>
                               </td>
                               <td id="student-<?php echo intval($id); ?>-certificates" class="g_cell_7 loading-students-certificates" data-student-id="<?php echo intval($id); ?>">
                                    <img src="<?php echo JURI::root()."components/com_guru/images/loading.gif"; ?>" />
                                    <?php
                                        /*$link_modal_cert = JURI::root()."index.php?option=com_guru&view=guruauthor&layout=studentdetailscert&userid=".intval($id)."&tmpl=component";
                                        
                                        $student_certificates = $this->getStudentCertificates(intval($id), "sum");

                                        if(intval($student_certificates) == 0){
                                            echo "0";
                                        }
                                        else{
                                    ?>
                                            <a onclick="javascript:openMyModal(0, 0, '<?php echo $link_modal_cert; ?>'); return false;" href="#">
                                                <?php echo intval($student_certificates); ?>
                                            </a>
                                    <?php
                                        }*/
                                    ?>
                               </td>
                           </tr>
                        <?php
                                $i ++;
                            }
                        }
                        ?>	
                       </table>
                </div>
               
               <?php
                    echo $this->pagination->getLimitBox();
                    $pages = $this->pagination->getPagesLinks();
                    include_once(JPATH_SITE.DIRECTORY_SEPARATOR."components".DIRECTORY_SEPARATOR."com_guru".DIRECTORY_SEPARATOR."helpers".DIRECTORY_SEPARATOR."helper.php");
                    $helper = new guruHelper();
                    $pages = $helper->transformPagination($pages);
                    echo $pages;
                ?>
               
                <input type="hidden" name="option" value="com_guru" />
                <input type="hidden" name="controller" value="guruAuthor" />
                <input type="hidden" name="view" value="guruauthor" />
                <input type="hidden" name="task" value="mystudents" />
                <input type="hidden" name="action" value="<?php echo JFactory::getApplication()->input->get("action", ""); ?>" />
                <input type="hidden" name="qid" value="<?php echo JFactory::getApplication()->input->get("qid", ""); ?>" />
                <input type="hidden" name="cid" value="<?php echo JFactory::getApplication()->input->get("cid", ""); ?>" />
            </form>
       </div> 
  <?php
  }
  else{
    ?>
    <div class="g_table_row">
        <div class="g_cell span1 g_table_cell">
            <div>
                <div>
                    <?php echo JText::_("GURU_ONLY_AUTHORS");?>
                </div>
            </div>
        </div>
   </div>     
  <?php  
  }
  ?>   
</div>

<script>
    jQuery( document ).ready(function() {
        var student_ids = [];
        var poz = 0;
        
        jQuery(".loading-students-certificates").each(function(index){
            student_id = jQuery(this).attr("data-student-id");
            student_ids.push(parseInt(student_id));
        });

        function loadStudentCertificates(student_id){
            url = '<?php echo JURI::root(); ?>index.php?option=com_guru&controller=guruAuthor&tmpl=component&format=raw&task=loading_student_certificates&student_id='+parseInt(student_id);
            
            jQuery.ajax({
                url : url,
                success: function(data) {
                    jQuery("#student-"+parseInt(student_id)+"-certificates").html(data);
                    poz++;
                    if (student_ids[poz]) {
                        loadStudentCertificates(student_ids[poz]);
                    }
                }
            });
        }

        if(student_ids.length > 0){
            loadStudentCertificates(student_ids[poz]);
        }
    });
</script>