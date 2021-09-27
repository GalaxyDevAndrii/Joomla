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

jimport('joomla.html.pagination'); 
JHtml::_('behavior.framework');
JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('dropdown.init');
JHtml::_('formbehavior.chosen', 'select');

$purchases = $this->purchases;
$search = JFactory::getApplication()->input->get("search", "");
$purchase_type = JFactory::getApplication()->input->get("purchase_type", "");
?>

<form id="adminForm" action="<?php echo JUri::root() ?>administrator/index.php" method="post" name="adminform">
	<table width="100%" style="margin-bottom:10px;">
        <tr>
            <td align="left">
				<?php echo JText::_("GURU_PURCHASES"); ?>:
                <select name="purchase_type" onchange="document.adminform.submit();">
                    <option value="" <?php if($purchase_type == ""){echo 'selected="selected"';} ?> ><?php echo JText::_("GURU_SELECT"); ?></option>
                    <option value="new" <?php if($purchase_type == "new"){echo 'selected="selected"';} ?> ><?php echo JText::_("GURU_NEW"); ?></option>
                    <option value="renew" <?php if($purchase_type == "renew"){echo 'selected="selected"';} ?> ><?php echo JText::_("GURU_RENEWAL"); ?></option>
                </select>
            </td>
            <td style="text-align:right;">
                <input type="text" name="search" value="<?php echo trim($search); ?>" />
                <input class="btn btn-primary" type="submit" name="submit_search" value="<?php echo JText::_('GURU_SEARCHTXT');?>" />
            </td>
        </tr>
    </table>
    
    <div class="alert alert-info">
        <?php echo JText::_("GURU_PURCHASE_INFO"); ?>
    </div>
    
    <table id="articleList" class="table table-striped adminlist table-bordered" style="position: relative;">
        <thead>
            <tr>
                <th width="20%">
                    <?php echo JText::_("GURU_NAME_USERNAME"); ?>
                </th>
                <th width="15%">
                    <?php echo JText::_("GURU_ACTION"); ?>
                </th>
                <th width="20%">
                    <?php echo JText::_("GURU_COURSE"); ?>
                </th>
                <th width="15%">
                    <?php echo JText::_("GURU_DATE_AND_TIME"); ?>
                </th>
            </tr>
        </thead>
        <?php
			$k = 0;		
            foreach($purchases as $key=>$purchase){
        ?>
            <tr class="<?php echo "row".$k; ?>">
                <td>
                    <a href="index.php?option=com_guru&controller=guruCustomers&task=edit&cid[]=<?php echo intval($purchase->userid); ?>">
                    	<?php echo $purchase->name ?>
                    </a> (<?php echo $purchase->username; ?>)
                </td>
                <td>
                	<?php
                		if($purchase->buy_type == "new"){
							echo JText::_("GURU_PURCHASE_NEW");
						}
						elseif($purchase->buy_type == "approved-order"){
							echo JText::_("GURU_APPROVED_ORDER");
						}
						else{
							echo JText::_("GURU_PURCHASE_RENEW");
						}
					?>
                </td>
                <td>
                	<?php
                		echo $purchase->course;
					?>
                </td>
                <td>
                    <?php 
                        $email_date_int = strtotime($purchase->send_date);
                        $email_day = date("d", $email_date_int);
                        $email_month = date("m", $email_date_int);
                        $email_year = date("Y", $email_date_int);
                        $email_hour = date("H", $email_date_int);
                        $email_min = date("i", $email_date_int);
    
                        $today = date("Y-m-d H:i:s");
                        $today_int = strtotime($today);
                        $today_day = date("d", $today_int);
                        $today_month = date("m", $today_int);
                        $today_year = date("Y", $today_int);
                        $today_hour = date("H", $today_int);
                        $today_min = date("i", $today_int);
                        
                        if(($today_day == $email_day) && ($today_month == $email_month) && ($today_year == $email_year)){
                            echo JText::_("GURU_TODAY")." (".date('Y-m-d', $email_date_int).") ".JText::_("GURU_AT")." ".date("H:i:s A", $email_date_int)." (PST)";
                        }
                        elseif((($today_day-1) == $email_day) && ($today_month == $email_month) && ($today_year == $email_year)){
                            echo JText::_("GURU_YESTERDAY")." (".date('Y-m-d', $email_date_int).") ".JText::_("GURU_AT")." ".date("H:i:s A", $email_date_int)." (PST)";
                        }
                        else{
                            echo strftime("%A", $email_date_int)." (".date('Y-m-d', $email_date_int).") ".JText::_("GURU_AT")." ".date("H:i:s A", $email_date_int)." (PST)";
                        }
                    ?>
                </td>
            </tr>
        <?php
                $k = 1 - $k;
            }
        ?>
        <tr>
            <td colspan="5">
                <?php
                    $total_pag = $this->pagination->pagesTotal;
                    $pag_start = $this->pagination->pagesStart;
                    if($total_pag > ($pag_start + 9)){
                        $this->pagination->pagesStop = ($pag_start + 9);
                    }
                    else{
                        $this->pagination->pagesStop = $total_pag;
                    }
                    echo $this->pagination->getListFooter();
                ?>
            </td>
        </tr>
    </table>
    
    <input type="hidden" name="option" value="com_guru" />
    <input type="hidden" name="controller" value="guruLogs" />
    <input type="hidden" name="task" value="purchases" />
</form>