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
$guruHelper = new guruHelper;
?>
<div class="gru-mycoursesauthor">
	<?php echo $div_menu?>
</div>

<div class="uk-grid uk-margin wk-grid wk-margin">
    <div class="uk-width-1-1 uk-width-medium-1-2 wk-width-1-1 wk-width-medium-1-2"><h2 class="gru-page-title"><?php echo JText::_('GURU_AUTHOR_MY_SCORM');?></h2></div>
    <br>
    <div class="uk-width-1-2 uk-hidden-small uk-text-right uk-margin-top wk-width-1-2 wk-hidden-small wk-text-right wk-margin-top">
        <div class="uk-button-group wk-button-group">
            <!-- This is the button toggling the dropdown -->
            <a class="uk-button uk-button-success wk-button wk-button-success" href="<?php echo JRoute::_('index.php?option=com_guru&view=guruauthor&task=scormForm&layout=scormForm')?>"><?php echo JText::_('GURU_NEW_SCORM'); ?></a>
            <button class="uk-button uk-button-danger wk-button wk-button-danger" onclick="deleteScorm();"><?php echo JText::_('GURU_DELETE'); ?></button>
        </div>
    </div>
</div>
    <div class="clearfix"></div>
    <div class="g_sect clearfix">
        <div class="g_table_wrap">
            <form action="<?php echo JRoute::_('index.php?option=com_guru&view=guruauthor&task=scorms&layout=scorm')?>" class="form-horizontal" id="adminForm" method="get" name="adminForm" enctype="multipart/form-data">
                <!-- Start Search -->
                
                <div class="gru-page-filters">
                    <div class="gru-filter-item">
                        <select class="uk-form-width-small wk-form-width-small" name="filter_course_id" id="filter_course_id" onchange="document.adminForm.submit();">
                            <option value="0"><?php echo JText::_("GURU_SELECT_COURSE");?></option>
                            <?php
                                if(!empty($this->my_courses)){
                                    foreach($this->my_courses as $key=>$course){
                                        $selected = "";
                                        if($course["id"] == $this->filter_course_id){
                                            $selected = 'selected="selected"';
                                        }
                                        echo '<option value="'.$course["id"].'" '.$selected.'>'.$course["name"].'</option>';
                                    }
                                }
                            ?>                  
                        </select>
                    </div>
                    <div class="gru-filter-item">
                        <input type="text" class="form-control" value="<?php echo $this->filter_keyword?$this->filter_keyword:''?>" name="filter_keyword" placeholder="<?php echo JText::_('GURU_SEARCHTXT')?>">
                        <button class="uk-button uk-button-primary wk-button wk-button-primary hidden-phone" type="submit"><?php echo JText::_('GURU_SEARCHTXT')?></button>
                    </div>
                </div>
                
                <!-- End Search -->
                <div class="clearfix"></div>
                <div class="g_table_wrap">
                    <table id="g_authorcourse" class="uk-table uk-table-striped wk-table wk-table-striped">
                        <tbody><tr>
                            <th class="g_cell_1"><input type="checkbox" name="checkall-toggle" value="" onclick="Joomla.checkAll(this);"></th>
                            <th class="g_cell_3"><?php echo JText::_('GURU_TITLE')?></th>
                            <th class="g_cell_4"><?php echo JText::_('GURU_COURSE_NAMEF')?></th>
                            <th class="g_cell_8 hidden-phone">
                                <span class="hidden-phone"><?php echo JText::_('GURU_PRODLSPUB')?></span>
                            </th>
                            <th class="g_cell_9 hidden-phone">
                                <span class="hidden-phone"><?php echo JText::_('GURU_PRODLEPUB')?></span>
                            </th>
                        </tr>
                        <?php if(!empty($this->listScorm)):?>
                        <?php foreach ($this->listScorm as $scorm) :?>
                            <tr class="guru_row">   
                                <td class="g_cell_1"><input type="checkbox" id="cb0" name="cid[]" value="<?php echo $scorm->id?>" onclick="Joomla.isChecked(this.checked);"></td>
                                <td class="guru_product_name g_cell_3"><a href="<?php echo JRoute::_('index.php?option=com_guru&view=guruauthor&task=scormForm&id='.$scorm->id)?>"><?php echo $scorm->title?></a></td>
                                <td class="guru_product_name g_cell_3"><?php echo $scorm->course_name?></td>
                                
                                <td class="g_cell_6 hidden-phone">
                                    <?php
                                        echo $guruHelper->getDate($scorm->start);
                                    ?>
                                </td>

                                <td class="g_cell_6 hidden-phone">
                                    <?php
                                        if(trim($scorm->end) != '0000-00-00 00:00:00'){
                                            echo $guruHelper->getDate($scorm->end);
                                        }
                                        else{
                                            echo JText::_("GURU_NEVER");
                                        }
                                    ?>
                                </td>
                              </tr>
                           </tr>     
                        <?php endforeach?>
                        <?php endif?>
                    </tbody></table>
            </div>
           
           <?php
                echo $this->pagination->getLimitBox();
                $pages = $this->pagination->getPagesLinks();
                //echo $this->pagination->getListFooter();
                include_once(JPATH_SITE.DIRECTORY_SEPARATOR."components".DIRECTORY_SEPARATOR."com_guru".DIRECTORY_SEPARATOR."helpers".DIRECTORY_SEPARATOR."helper.php");
                $helper = new guruHelper();
                $pages = $helper->transformPagination($pages);
                echo $pages;
            ?>
            <div class="pagination pagination-centered"></div>            
            <input type="hidden" name="task" id="task" value="scorm">
            <input type="hidden" name="option" value="com_guru">
            <input type="hidden" name="controller" id="controller" value="guruAuthor">
            <?php echo JHTML::_('form.token'); ?>
        </form>
        </div>
    </div>
    
    <script type="text/javascript">
        $ = jQuery;

        function deleteScorm(){
            $('#adminForm').attr('method','POST');
            $('#task').val('deleteScorm');
            $('#controller').val('guruScorm');
            if(confirm("<?php echo JText::_('GURU_CONFIRM_DELETE_SCORM')?>")){
                document.adminForm.submit();
            }
        }
    </script>