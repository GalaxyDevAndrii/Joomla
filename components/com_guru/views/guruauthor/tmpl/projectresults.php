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
    <div class="uk-width-1-1 uk-width-medium-1-2 wk-width-1-1 wk-width-medium-1-2"><h2 class="gru-page-title"><?php echo JText::_('GURU_TEACHER_PROJECT_RESULTS');?> : <?php echo $this->projectDetail->title?></h2></div>
    <br>
    <div class="uk-width-1-2 uk-hidden-small uk-text-right uk-margin-top wk-width-1-2 wk-hidden-small wk-text-right wk-margin-top">
        <div class="uk-button-group wk-button-group">
            <!-- This is the button toggling the dropdown -->
            <button class="uk-button uk-button-success wk-button wk-button-success" onclick="saveResult();"><?php echo JText::_('GURU_SAVE'); ?></button>
            <a class="uk-button uk-button-danger wk-button wk-button-danger" href="<?php echo JRoute::_('index.php?option=com_guru&view=guruauthor&task=projects&layout=projects')?>"><?php echo JText::_('GURU_CLOSE'); ?></a>
        </div>
    </div>
</div>
    <div class="clearfix"></div>
    <div class="g_sect clearfix">
        <div class="g_table_wrap">
            <form action="<?php echo JRoute::_('index.php?option=com_guru&view=guruauthor&task=saveScore')?>" class="form-horizontal" id="adminForm" method="get" name="adminForm" enctype="multipart/form-data">
                <div class="clearfix"></div>
                <div class="g_table_wrap">
                    <table id="g_authorcourse" class="uk-table uk-table-striped wk-table wk-table-striped">
                        <tbody><tr>
                            <th class="g_cell_3"><?php echo JText::_('GURU_NAME')?></th>
                            <th class="g_cell_4"><?php echo JText::_('GURU_FILE')?></th>
                            <th class="g_cell_7 hidden-phone"><?php echo JText::_('GURU_PRODDESC')?></th>
                            <th class="g_cell_9 hidden-phone">
                                <span class="hidden-phone"><?php echo JText::_('GURU_MYORDERS_ORDER_DATE')?></span>
                            </th>
                            <th class="g_cell_4"><?php echo JText::_('GURU_QUIZ_SCORE')?></th>
                        </tr>
                        <?php if(!empty($this->listProjectResults)):?>
                        <?php foreach ($this->listProjectResults as $projectResult) :?>
                            <tr class="guru_row">   
                                <td class="guru_product_name g_cell_3"><?php echo $projectResult->student_name; ?></td>
                                <td class="guru_product_name g_cell_3">
                                    <a href="<?php echo JURI::root()."index.php?option=com_guru&view=guruProjects&task=download&cid=".intval($projectResult->id)."&user_id=".intval($projectResult->student_id); ?>" target="_blank">
                                        <i class="fa fa-download"></i>
                                        <?php echo JText::_('GURU_DOWNLOAD_FILE'); ?>
                                    </a>
                                </td>
                                <td class="g_cell_6 hidden-phone"><?php echo $projectResult->desc; ?></td>
                                <td class="g_cell_6 hidden-phone">
                                    <?php echo $guruHelper->getDate($projectResult->created_date); ?>
                                </td>
                                <td class="guru_product_name g_cell_3"><input type="text" name="scores[]" style="width:30px;text-align: center;" value="<?php echo $projectResult->score; ?>"/>
                                    <input type="text" name="ids[]" style="display: none" value="<?php echo $projectResult->id; ?>"/>
                                </td>
                            </tr>    
                        <?php endforeach?>
                        <?php endif?>
                    </tbody></table>
            </div>
           
            <div class="pagination pagination-centered"><input type="hidden" value="0" name="limitstart"></div>            
            <input type="hidden" name="task" id="task" value="saveScore">
            <input type="hidden" name="option" value="com_guru">
            <input type="hidden" name="controller" id="controller" value="guruProjects">
            <?php echo JHTML::_('form.token'); ?>
        </form>
        </div>
    </div>
    
    <script type="text/javascript">
        function saveResult(){
            $ = jQuery;
            $('#adminForm').attr('method','POST');
            $('#adminForm').submit();
        }
       
    </script>