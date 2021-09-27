<?php
/*------------------------------------------------------------------------
# com_guru
# ------------------------------------------------------------------------
# author    iJoomla
# copyright Copyright (C) 2013 ijoomla.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.ijoomla.com
# Technical Support:  Forum - http://www.ijoomla.com.com/forum/index/
-------------------------------------------------------------------------*/
defined( '_JEXEC' ) or die( 'Restricted access' );
?>
        <form name="login" method="post" action="<?php echo JUri::root() ?>index.php">
        <input type="hidden" name="Itemid" value="<?php echo $Itemid; ?>" />
  <h2><?php echo JText::_("DSREGORLOG");?></h2>
  <table class="cart_table">
    <tr>
      <td width="50%" valign="top" class="row1"><?php echo JText::_("GURU_ALREADY_CUSTOMER");?>
        <table width="100%"  border="0">
          <tr>
            <td width="40%"><?php echo JText::_("DSUSERNAME");?>: <span class="guru_error">*</span></td>
            <td width="60%"><input name="username" type="text" id="username" size="15">
           </td>
          </tr>
          <tr>
            <td><?php echo JText::_("DSPASS");?>: <span class="guru_error">*</span></td>
            <td><input name="passwd" type="password" id="passwd" size="15">
            </td>
          </tr>
          <tr>
            <td>
              
            </td>
            <td>
		<input type="checkbox" name="rememeber" value="1" />	<?php echo JText::_("DSREMEMBER");?>
	</td>
          </tr>
          <tr>
            <td colspan="2">     
	<?php $link = JRoute::_("index.php?option=com_user&view=remind");?>
	<a href="<?php echo $link;?>"><?php echo JText::_("DSFORGOTUN");?>
	</a> <br />
	<?php $link = JRoute::_("index.php?option=com_user&view=reset");?>
	<a href="<?php echo $link;?>"><?php echo JText::_("DSFORGOTPASS");?></a>
      </a></td>
          </tr>
          <tr>
            <td colspan="2"><input type="submit" name="submit" value="<?php echo JText::_("DSLOGIN");?>" /></td>
          </tr>
        </table> 
	  	<input type="hidden" name="option" value="com_guru" />
		<input type="hidden" name="controller" value="guruProfile" />
		
		<input type="hidden" name="task" value="login" />
		<input type="hidden" name="returnpage" value="<?php echo (JFactory::getApplication()->input->get("returnpage", ""));?>" />		
        </form>
   
   </td>
      <td width="2" valign="top">&nbsp;</td>
      <td width="50%" valign="top" class="row2"><?php echo JText::_("DSNEWCUSTOMERS");?>
        <p>

	<form action="<?php echo JUri::root() ?>index.php" name="adminForm" method="post">
	  	<input type="hidden" name="option" value="com_guru" />
		<input type="hidden" name="task" value="register" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="controller" value="guruProfile" />
		<input type="hidden" name="returnpage" value="<?php echo (JFactory::getApplication()->input->get("returnpage", ""));?>" />		
		<input type="submit" name="submit" value="<?php echo JText::_("DSREGANDCOUNT");?>" />
	</form>
        </p></td>
    </tr>
  </table>
