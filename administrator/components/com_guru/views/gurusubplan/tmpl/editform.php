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

$plan = $this->plan;

?>
<script type="text/javascript">

        function checkUnlimited(el) {

            //var durationcount = document.getElementById('duration_count');
            var durationtype = document.getElementById('period');

            if (el.selectedIndex == 0) {
                durationtype.style.display = 'none';
            } else {
                durationtype.style.display = 'inline';
            }
        }
		Joomla.submitbutton = function(pressbutton){
		var form = document.adminForm;
		if (pressbutton=='save') {
			if (form['name'].value == "") {
				alert( "<?php echo JText::_("GURU_TASKS_JS_NAME");?>" );
				return false;
			} 
		 	else{
				submitform( pressbutton );
			}
		}
		else {
			submitform( pressbutton );
		}
	}

</script>
 <div id="myModal" class="modal hide">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    </div>
    <div class="modal-body">
    </div>
</div>
<form id="adminForm" action="<?php echo JUri::root() ?>administrator/index.php" name="adminForm" method="post">
    
    <fieldset>


        <table>
            <tr>
                <td width="30"><?php echo JText::_('GURU_NAME'); ?></td>
                <td>
					<input type="text" name="name" value="<?php echo $plan->name; ?>"/>
					<span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_PLAN_NAME"); ?>" >
						<img border="0" src="components/com_guru/images/icons/tooltip.png">
					</span>
				</td>
            </tr>
            <tr>
                <td><?php echo JText::_('GURU_TERM'); ?></td>
                <td>
					<?php  echo $this->lists['duration_count']; ?> <?php  echo $this->lists['duration_type']; ?>
					<span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_PLAN_TERM"); ?>" >
						<img border="0" src="components/com_guru/images/icons/tooltip.png">
					</span>
				</td>
            </tr>
            <tr>
                <td><?php echo JText::_('GURU_PUBLISHED'); ?></td>
                <td>
					<?php  echo $this->lists['published']; ?>
					<span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_PLAN_PUBLISHED"); ?>" >
						<img border="0" src="components/com_guru/images/icons/tooltip.png">
					</span>
				</td>
            </tr>
        </table>

    </fieldset>

    <input type="hidden" name="id" value="<?php echo $plan->id; ?>"/>
    <input type="hidden" name="task" value="" />
    <input type="hidden" name="option" value="com_guru" />
    <input type="hidden" name="controller" value="guruSubplan" />

</form>