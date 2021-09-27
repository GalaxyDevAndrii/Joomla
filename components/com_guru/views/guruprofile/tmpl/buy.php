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
$program = $this->program;
$countdays = $this->countdays;
$program_status = $this->program_status;

$my = JFactory::getUser();
if($my->id<1) {header("Location: index.php?option=com_guru&view=guruProfile&task=account");}

if($program_status==0)
	$price = $program->price;
else
	{ // for an already bought program we calculate the price - begin
		if($program->redo=='same')
			$price = $program->price;
		else
			{
				if($program->discount=='percent')
					$price = number_format(($program->price * (100 - $program->redocost) / 100) , 2);
				elseif($program->discount=='amount')	
					$price = number_format(($program->price - $program->redocost) , 2);
				else
					$price = $program->price;	
			}	
	} // for an already bought program we calculate the price - end	
?>
<form name="adminForm" action="<?php echo JUri::root(true) ?>/index.php?option=com_guru&view=guruOrders" method="post">
<table cellspacing="0" cellpadding="5" border="0" width="100%">
  <tbody><tr valign="top">
    <td width="50%"><h2><?php echo JText::_("GURU_BUY_NOW");?></h2></td>
    <td width="50%"></td>
  </tr>
  <tr valign="top">
    <td><div align="right">
      <table cellspacing="0" cellpadding="5" border="0" width="100%">
        <tbody><tr>
          <td width="30%"><?php echo JText::_("GURU_BUY_PROGRAM");?></td>
          <td width="70%"><?php echo $program->name;?></td>
        </tr>
        <tr>
          <td><?php echo JText::_("GURU_BUY_DAYS");?></td>
          <td><?php echo $countdays; ?></td>
        </tr>
        <tr>
          <td><?php echo JText::_("GURU_BUY_PRICE");?></td>
          <td><?php echo $price;?></td>
        </tr>
        <tr>
          <td><?php echo JText::_("GURU_BUY_PROMO");?></td>
          <td><input type="text" size="10" name="promocode"/> <?php echo JText::_("GURU_BUY_OPTIONAL");?></td>
        </tr>
        <tr>
          <td><?php echo JText::_("GURU_BUY_PAYMENT_METH");?></td>
          <td><select name="select">
            <option selected="selected">PayPal</option>
          </select></td>
        </tr>
        <tr>
          <td></td>
          <td></td>
        </tr>
        <tr>
          <td></td>
          <td><input type="submit" onclick="" value="<?php echo JText::_("GURU_BUY_CONTINUE");?>" name="Submit"/></td>
        </tr>
      </tbody></table>
    </div></td>
    <td></td>
  </tr>
</tbody></table>
<input type="hidden" name="option" value="com_guru" />
<input type="hidden" name="tid" value="<?php echo $program->id;?>" />
<input type="hidden" name="task" value="checkout" />
<input type="hidden" name="controller" value="guruOrders" />
</form>	