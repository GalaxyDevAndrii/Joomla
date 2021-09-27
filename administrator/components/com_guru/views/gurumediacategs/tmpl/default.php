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
jimport('joomla.html.html.grid');

$items = $this->items;	

$search = JFactory::getApplication()->input->get("filter_search", "", "raw");
$state = JFactory::getApplication()->input->get("filter_state", "-1", "raw");
?>
<form id="adminForm" action="<?php echo JUri::root() ?>administrator/index.php" method="post" name="adminForm">
	<table width="100%">
		<tr>                	
			<td align="right" width="80%">
				<input type="text" name="filter_search" value="<?php echo $search; ?>" onchange="this.form.submit();" />
				<input type="button" class="btn btn-primary" value="<?php echo JText::_('GURU_SEARCHTXT'); ?>" onclick="this.form.submit();" />
			</td>
			<td align="right" width="10%">
				<select name="filter_state" onchange="this.form.submit();">
					<option value="-1" <?php if($state == "-1"){echo 'selected="selected"';} ?>><?php echo JText::_("GURU_SELECT_STATUS"); ?></option>
					<option value="1" <?php if($state == "1"){echo 'selected="selected"';} ?>><?php echo JText::_("GURU_PUBLISHED"); ?></option>
					<option value="0" <?php if($state == "0"){echo 'selected="selected"';} ?>><?php echo JText::_("GURU_UNPUBLISHED"); ?></option>
				</select>
			</td>
		</tr>
	</table>
	
	<table class="table table-striped table-bordered adminlist">
		<thead>	
            <th width="2%" align="center"><input type="checkbox" onclick="Joomla.checkAll(this)" value="" name="toggle"/><span class="lbl"></span></th>
            
            <th width="2%" align="center">#</th>
            <th width="25%" align="center"><?php echo JText::_('GURU_CATEGNAME');?></th>
            <th width="25%" align="center"><?php echo JText::_('GURU_TASK_MEDIA')." "."#";?></th>
             
            <th width="20%" align="center"><?php echo JText::_('GURU_PUBLISHED');?></th>
        </thead>
		</tbody>
        <?php
        $i = 0;
        $k = ($this->pagination->limitstart)+1;
		
        if(isset($items) && $items != NULL){	   
			foreach($items as $key=>$value){	
				$value = (object)$value;
        ?>    	
                <tr class="row<?php echo $i%2; ?>">                     
                    <td align="center">
                        <?php echo JHtml::_('grid.id', $i, $value->id); ?>
                        <span class="lbl"></span>
                    </td> 
                    <td align="center">
                        <?php echo $k;?>
                    </td> 
                    <td>
                    	<?php
                        	$line = "";
							for($j=0; $j<$value->level; $j++){
								$line .= '&#151;';
							}
						?>
                        <a class="a_guru" href="index.php?option=com_guru&controller=guruMediacategs&task=edit&id=<?php echo $value->id?>"><?php echo $line."(".$value->level.") ".$value->name;?></a>  
                    </td> 
                    <td align="center">
                    	<?php echo $value->nb_medias;?>
                    </td>
                    <td align="center">						
                        <?php echo  JHTML::_('grid.published', $value, $i ); ?>
                    </td>         
				</tr>
               <?php					
				$i++;
				$k++;
			}//end foreach
        }
		?>
        </tbody>
		<tfoot>
			<tr>
                <td colspan="10">
                    <div class="pagination pagination-toolbar">
                        <?php echo $this->pagination->getListFooter(); ?>
                    </div>
                    <div class="btn-group pull-left">
                        <label for="limit" class="element-invisible"><?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC');?></label>
                        <?php echo $this->pagination->getLimitBox(); ?>
                   </div>
                </td>
            </tr>
		</tfoot>
	</table>
	<input type="hidden" name="controller" value="guruMediacategs" />
	<input type="hidden" name="option" value="com_guru" />	
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
 <input type="hidden" name="old_limit" value="<?php echo JFactory::getApplication()->input->get("limitstart"); ?>" />

</form>