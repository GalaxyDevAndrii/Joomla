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

$document =JFactory::getDocument();
$document->addScript(JURI::root()."administrator/components/com_guru/js/new_student.js");
?>

<form id="adminForm" name="adminForm" method="post">
    <table> 
       <tr>
          <td style="padding: 7px" width="15%">
                <?php echo JText::_("GURU_AU_AUTHOR_USERNAME"); ?>&nbsp;&nbsp;<input type="text" name="username" value=""> 
          </td> 
       </tr>
    </table>
	<input type="hidden" name="option" value="com_guru" />
	<input type="hidden" name="task" value="next" />
	<input type="hidden" name="controller" value="guruAuthor" />
  	<input type="hidden" name="author_type" value="1" />

</form>