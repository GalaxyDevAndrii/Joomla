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

	$plugin = $this->plugin;	
	$nullDate = 0;
?>
		
<script type="text/javascript" language="javascript">
	function submitbutton(pressbutton){
		submitform( pressbutton );
	}
</script>


<form id="adminForm" name="adminForm" method="post" action="<?php echo JUri::root() ?>administrator/index.php">
	<fieldset class="adminform">
	<legend><?php echo JText::_('GURU_PLUG_SETTINGS'); ?></legend>
	<table class="admintable">
		<tbody>
			<tr>
				<th><?php echo JText::_('GURU_PLUG_PLUGIN'); ?></th>
				<th><?php echo JText::_('GURU_PLUG_SETTING'); ?></th> 
				<!-- <th><?php //echo JText::_('GURU_PLUG_DEFAULT'); ?></th> -->
				<!-- <th><?php //echo JText::_('GURU_PLUG_SANDBOX'); ?></th> -->
			</tr>
        	<tr>
				<td> 
             		<?php echo $plugin->name; ?> <?php echo JText::_('GURU_PLUG_CONFIGURATION'); ?>
           		</td>
				<td> 
                  <?php echo JText::_('GURU_PLUG_EMAIL'); ?> <input type="text" value="<?php echo $plugin->value; ?>" size="30" name="value"/>
				</td>
			</tr>	
		</tbody>
	</table>
	</fieldset>

	<input type="hidden" name="images" value="" />                
	<input type="hidden" name="option" value="com_guru" />
	<input type="hidden" name="id" value="<?php echo $plugin->id; ?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="controller" value="guruPlugins" />
</form>