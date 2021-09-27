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

$project = $this->project;
$listProjectsResults = $this->listProjectsResults;

require_once(JPATH_BASE.'/../components/com_guru/helpers/helper.php');
$guruHelper = new guruHelper;

?>

<h4><?php echo JText::_("GURU_PROJECT").": ".$project["title"]; ?></h4>

<form action="<?php echo JUri::root() ?>administrator/index.php" class="form-horizontal" id="adminForm" method="post" name="adminForm" enctype="multipart/form-data">
    <table class="table table-striped table-bordered adminlist">
        <thead>
            <tr>
                <th width="25%"><?php echo JText::_('GURU_STUDENT_NAME');?></th>
                <th width="25%"><?php echo JText::_('GURU_FILE');?></th>
                <th width="20%"><?php echo JText::_('GURU_DESCRIPTION');?></th>
                <th width="15%"><?php echo JText::_('GURU_DATE');?></th>
                <th width="15%"><?php echo JText::_('GURU_SCORE');?></th>
            </tr>
        </thead>                
        <tbody>
        	<?php
        		if(isset($listProjectsResults) && count($listProjectsResults) > 0){
        			foreach ($listProjectsResults as $projectResult){
        	?>
        				<tr class="guru_row">   
                            <td><?php echo $projectResult->student_name; ?></td>
                            <td>
                                <a href="<?php echo JURI::root()."index.php?option=com_guru&view=guruProjects&task=download&cid=".intval($projectResult->id)."&user_id=".intval($projectResult->student_id); ?>" target="_blank">
                                    <?php echo JText::_('GURU_DOWNLOAD_FILE'); ?>
                                </a>
                            </td>
                            <td><?php echo $projectResult->desc; ?></td>
                            <td>
                                <?php echo $guruHelper->getDate($projectResult->created_date); ?>
                            </td>
                            <td>
                            	<input type="text" name="scores[]" style="width:30px; text-align: center;" value="<?php echo $projectResult->score; ?>" />
                                <input type="text" name="ids[]" style="display: none" value="<?php echo $projectResult->id; ?>" />
                            </td>
                        </tr>
        	<?php
        			}
        		}
        	?>

        	<tr>
                <td colspan="5">
                    <div class="pagination pagination-toolbar">
                        <?php echo $this->pagination->getListFooter(); ?>
                    </div>
                    <div class="btn-group pull-left">
                        <label for="limit" class="element-invisible"><?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC');?></label>
                        <?php echo $this->pagination->getLimitBox(); ?>
                   </div>
                </td>
        	</tr>
        </tbody>
    </table>

    <input type="hidden" name="option" value="com_guru" />
    <input type="hidden" name="task" value="resultProject" />
    <input type="hidden" name="controller" value="guruProjects" />
    <input type="hidden" name="id" value="<?php echo intval($project["id"]); ?>" />
    <input type="hidden" name="old_limit" value="<?php echo JFactory::getApplication()->input->get("limitstart"); ?>" />
</form>