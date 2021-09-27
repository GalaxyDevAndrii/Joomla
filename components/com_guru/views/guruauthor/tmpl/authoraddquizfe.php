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

	$document = JFactory::getDocument();
	$div_menu = $this->authorGuruMenuBar();
	$document->setTitle(trim(JText::_('GURU_AUTHOR'))." ".trim(JText::_('GURU_AUTHOR_MY_QUIZZES')));
?>

<script type="text/javascript" language="javascript">
	document.body.className = document.body.className.replace("modal", "");
</script>

<div class="g_row clearfix">
	<div class="g_cell span12">
		<div>
			<div>
                <div id="g_quizzes_options" class="clearfix com-cont-wrap">
					<?php 	echo $div_menu; //MENU TOP OF AUTHORS?>
                    <form name="adminForm" method="post">
                        <input type="radio"  name="quiz_type" onclick="window.parent.location.href = '<?php echo JUri::root() ?>index.php?option=com_guru&controller=guruAuthor&task=editQuizFE&v=0';" value="0"/>
                        <span class="lbl"></span>
                        &nbsp;<?php echo JText::_("GURU_REGULAR_QUIZ"); ?>&nbsp;
                        <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_REGULAR_QUIZ"); ?>" >
                            <img border="0" src="components/com_guru/images/icons/tooltip.png">
                        </span>
                        <br/>
                        <input type="radio" name="quiz_type" onclick="window.parent.location.href = '<?php echo JUri::root() ?>index.php?option=com_guru&controller=guruAuthor&task=editQuizFE&v=1';" value="1"/>
                        <span class="lbl"></span>
                        &nbsp;<?php echo JText::_("GURU_FINAL_EXAM_QUIZ"); ?>&nbsp;
                        <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_FINAL_EXAM"); ?>" >
                            <img border="0" src="components/com_guru/images/icons/tooltip.png">
                        </span>
                    </form>
				</div>
			</div>
		</div>
	</div>
</div>                 
                    
                    