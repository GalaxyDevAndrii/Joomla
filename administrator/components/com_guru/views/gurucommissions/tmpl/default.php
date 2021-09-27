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
	$k = 0;
	$n = count ($this->programs);
	$programs = $this->programs;
?>
<script type="text/javascript" src="<?php echo JURI::root(); ?>components/com_guru/js/sorttable.js"></script>
<form action="<?php echo JUri::root() ?>administrator/index.php" id="adminForm" name="adminForm" method="post">
<table class="sortable table table-striped adminlist table-bordered">
    <thead>
        <tr>
            <th width="20">
                #
            </th>
            <th class="sorttable_nosort" width="2%">
                <input type="checkbox" name="toggle" value="" onClick="Joomla.checkAll(this);" />
                <span class="lbl"></span>
            </th>
            <th class="sorttable_nosort">
                <?php echo JText::_('GURU_DEFAULT');?>
            </th>
            <th>
                <?php echo JText::_('GURU_COMMISSION_PLAN');?><i class="icon-menu-2"></i>
            </th>
            <th> <?php echo JText::_('GURU_COMMISSION_TH_EARNINGS')." %";?><i class="icon-menu-2"></i>
            </th>
            <th>
                <?php echo JText::_('GURU_EDIT_DETAILS');?>
            </th>
        </tr>
    </thead>
    
    <tbody>
    
    <?php
        for ($i = 0; $i < $n; $i++):
        $program = (object)$this->programs[$i];
        $id = $program->id;
    ?>
        <tr> 
            <td>
                <?php echo $i+1+@$pageNav->limitstart;?>
            </td>
            <td>
                <?php echo JHTML::_('grid.id', $i, $id); ?>
                <span class="lbl"></span>
            </td>
            <td>
                <input type="radio" <?php if($program->default_commission != "1"){ echo "disabled";}?> name="default_commission" value="0" <?php if($program->default_commission == "1"){echo 'checked="checked"';} ?>/>
                <span class="lbl"></span>
            </td>	
            <td>
                <?php echo $program->commission_plan;?>
            </td>		
            <td>
               <?php echo $program->teacher_earnings." %";?>
            </td>	
             <td>
               <?php echo '<a class="btn btn-primary" href="index.php?option=com_guru&controller=guruCommissions&task=edit&cid[]='.$id.'">'.JText::_('GURU_EDIT').'</a>';?>
            </td>		
        </tr>
    <?php 
        endfor;
    ?>
    </tbody>
</table>
<input type="hidden" name="option" value="com_guru" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="controller" value="guruCommissions" />
</form>