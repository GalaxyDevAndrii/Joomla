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
$commissions = $this->commissions;
?>	
<script language="javascript" type="text/javascript">
	function IsNumeric(sText){
		var ValidChars = "0123456789.";
		var IsNumber=true;
		var Char;
		for(i = 0; i < sText.length && IsNumber == true; i++){ 
			Char = sText.charAt(i); 
			if(ValidChars.indexOf(Char) == -1){ 
				IsNumber = false; 
			}
		}
		return IsNumber;
	}
	<!--
	Joomla.submitbutton = function(pressbutton){
	//function submitbutton(pressbutton){
		///var form = document.adminForm;
		if (pressbutton=='save' || pressbutton=='apply') {
			var form = document.adminForm;
			
			if(form['commission_plan'].value==''){
				alert('<?php echo JText::_('GURU_COMMTITLERR');?>'); 
				return false;
			} 
			if (form['teacher_earnings'].value==''){
				alert('<?php echo JText::_('GURU_COMMEARNINGSDISCOUNT');?>'); 
				return false;
			}
			if (form['teacher_earnings'].value < 0 || form['teacher_earnings'].value >100){
				alert('<?php echo JText::_('GURU_COMMLIMIT');?>'); 
				return false;
			}
			if (IsNumeric(form['teacher_earnings'].value)==false){
				alert('<?php echo JText::_('GURU_COMM_NB');?>'); 
				return false;
			}
			
			
			submitform( pressbutton );	
		}
		else {
			submitform( pressbutton );
		}
	}
	-->
</script>	
<style>
	.input-append{
		width:22%!important;
		float:left;
	}
</style>
<form action="<?php echo JUri::root() ?>administrator/index.php" method="post" name="adminForm" id="adminForm">
	<div class="clearfix"></div>
     <div class="adminform">	
        <div class="row-fluid">
            <div class="span12"> 
                <div class="row-fluid">
                    <div class="span12">
                        <div class="row-fluid">
                            <div class="control-group">
                                <label class="control-label span3" for="curency"><?php echo JText::_('GURU_COMMISSION_PLAN_NAME');?>&nbsp;<font color="#FF0000">*</font></label>
                            <div class="controls">
                       			<input type="text" value="<?php echo $commissions->commission_plan; ?>" name="commission_plan"/>
                                <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_COMMISSION_PLAN_NAME_TIP"); ?>" >
                                    <img border="0" src="components/com_guru/images/icons/tooltip.png">
                                </span>
                   			</div>
                            </div>										
                        </div>
                    </div>							
                </div>
            </div>						
        </div>
        <div class="row-fluid">
            <div class="span12"> 
                <div class="row-fluid">
                    <div class="span12">
                        <div class="row-fluid">
                            <div class="control-group">
                                <label class="control-label span3" for="layout"><?php echo JText::_('GURU_COMMISSION_TH_EARNINGS_PER_COURSE');?>&nbsp;<font color="#FF0000">*</font>		
               				  </label>
                              <div class="controls">
                                <input type="text" value="<?php echo $commissions->teacher_earnings; ?>" name="teacher_earnings"/>%
                                <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_COMMISSION_TH_EARNINGS_PER_COURSE_TIP"); ?>" >
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
    <input type="hidden" name="id" value="<?php echo $commissions->id;?>" />
    <input type="hidden" name="default_commission" value="<?php echo $commissions->default_commission;?>" />
    <input type="hidden" name="option" value="com_guru" />
    <input type="hidden" name="task" value="" />
    <input type="hidden" name="controller" value="guruCommissions" />
</form>
