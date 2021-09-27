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

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
JHTML::_('behavior.tooltip');
?>

<form action="" method="post" name="adminForm" id="adminForm">	
	<!--<fieldset class="adminform">
		<legend><?php //echo JText::_("GURU_SELECT_LEGEND"); ?></legend>-->
			<input type="radio" onclick="window.location='<?php echo JURI::root()."administrator/index.php?option=com_guru&amp;controller=guruOrders&amp;task=checkcreateuser&amp;usertype=1"; ?>'" value="1" name="usertype" id="newuser">
            <span class="lbl"></span>
			<?php echo JText::_("GURU_NEW_USER"); ?>
			<span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_NEW_USER"); ?>" >
				<img border="0" src="components/com_guru/images/icons/tooltip.png">
			</span>
			<br>
			<input type="radio" onclick="window.location='<?php echo JURI::root()."administrator/index.php?option=com_guru&amp;controller=guruOrders&amp;task=checkcreateuser&amp;usertype=2"; ?>'" value="2" name="usertype" id="newcustomer">
            <span class="lbl"></span>
			<?php echo JText::_("GURU_NEW_CUSTOMER"); ?>
			<span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_ORDER_NEW_CUSTOMER"); ?>" >
				<img border="0" src="components/com_guru/images/icons/tooltip.png">
			</span>
			<br>
			<input type="radio" onclick="window.location='<?php echo JURI::root()."administrator/index.php?option=com_guru&amp;controller=guruOrders&amp;task=checkcreateuser&amp;usertype=3"; ?>'" value="3" name="usertype" id="existingcustomer">
            <span class="lbl"></span>
			<?php echo JText::_("GURU_EXISTING_CUSTOMER"); ?>
			<span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_EXISTING_CUSTOMER"); ?>" >
				<img border="0" src="components/com_guru/images/icons/tooltip.png">
			</span>
			<br>
	<!--</fieldset>-->

	<input type="hidden" name="task" value="" />
	<input type="hidden" name="controller" value="guruOrders" />
</form>