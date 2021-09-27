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
$emails = $this->emails;
$search = JFactory::getApplication()->input->get("search", "");
?>

<form id="adminForm" action="<?php echo JUri::root() ?>administrator/index.php" method="post" name="adminForm">
	<table width="100%" style="margin-bottom:10px;">
        <tr>
            <td style="text-align:right;">
                <input type="text" name="search" value="<?php echo trim($search); ?>" />
                <input class="btn btn-primary" type="submit" name="submit_search" value="<?php echo JText::_('GURU_SEARCHTXT');?>" />
            </td>
        </tr>
    </table>
    
    <div class="alert alert-info">
        <?php echo JText::_("GURU_LOGS_INFO"); ?>
    </div>
    
    <table id="articleList" class="table table-striped adminlist table-bordered" style="position: relative;">
        <thead>
            <tr>
                <th width="15%">
                    <?php echo JText::_("GURU_EMAIL_TYPE_NAME"); ?>
                </th>
                <th width="20%">
                    <?php echo JText::_("GURU_VIEWSTATTO"); ?>
                </th>
                <th width="15%">
                    <?php echo JText::_("GURU_SUBJECT"); ?>
                </th>
                <th width="10%">
                    <?php echo JText::_("GURU_DATE_AND_TIME"); ?>
                </th>
                <th width="5%">
                    <?php echo JText::_("GURU_VIEW"); ?>
                </th>
            </tr>
        </thead>
        <?php
            $k = 0;		
            foreach($emails as $key=>$email){
                $email_name = $this->getEmailName($email);
                $to = $email->to;
                $subject = $email->subject;
        ?>
            <tr class="<?php echo "row".$k; ?>">
                <td>
                    <?php
                        if($email->emailid == 0){
                            echo $email_name;
                        }
                        else{
                    ?>
                            <a href="index.php?option=com_guru&controller=guruSubremind&task=edit&cid[]=<?php echo intval($email->emailid); ?>">
                                <?php echo $email_name; ?>
                            </a>
                    <?php
                        }
                    ?>
                </td>
                <td>
                    (<?php echo $email->user_name; ?>)
                </td>
                <td>
                    <?php
                        $subject = $email->subject;
                        if(strlen($subject) > 30){
                            $subject = substr($subject, 0, 30)."...";
                        }
                        echo $subject;
                    ?>
                </td>
                <td>
                    <?php 
                        $email_date_int = strtotime($email->send_date);
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
                <td nowrap="nowrap">
                    <?php
                        echo "<a rel=\"{handler: 'iframe', size: {x: 700, y: 500}}\"  class=\"modal\"  href=\"index.php?option=com_guru&controller=guruLogs&task=editEmail&id=".$email->id."&tmpl=component\">".JText::_('GURU_VIEW_EMAIL')."</a>";
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
    <input type="hidden" name="task" value="emails" />
</form>