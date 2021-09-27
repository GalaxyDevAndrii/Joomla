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
?>
<?php
$document =JFactory::getDocument();
//$document->addScript(JURI::root()."administrator/components/com_guru/js/new_student.js");


?>
<form id="adminForm" name="adminForm" method="post">
	<input type="radio"  name="quiz_type" onclick="window.parent.location.href = 'index.php?option=com_guru&controller=guruQuiz&task=edit&v=0';" value="0"/>
    <span class="lbl"></span>
    &nbsp;<?php echo JText::_("GURU_REGULAR_QUIZ"); ?>&nbsp;
	<span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_REGULAR_QUIZ"); ?>" >
		<img border="0" src="components/com_guru/images/icons/tooltip.png">
	</span>
	<br/>
	<input type="radio" name="quiz_type" onclick="window.parent.location.href = 'index.php?option=com_guru&controller=guruQuiz&task=edit&v=1';" value="1"/>
    <span class="lbl"></span>
    &nbsp;<?php echo JText::_("GURU_FINAL_EXAM_QUIZ"); ?>&nbsp;
	<span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_FINAL_EXAM"); ?>" >
		<img border="0" src="components/com_guru/images/icons/tooltip.png">
	</span>
</form>