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
$commissions_paid_teacher = $this->commissions_paid_teacher;
$n = count($commissions_paid_teacher);
$config = $this->config;
$currencypos = $config->currencypos;
$datetype  = $config->datetype;
$doc =JFactory::getDocument();
$doc->addScript(JURI::root().'/components/com_guru/js/sorttable.js');
include_once(JPATH_SITE.DIRECTORY_SEPARATOR.'administrator'.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_guru'.DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.'gurudays'.DIRECTORY_SEPARATOR.'tmpl'.DIRECTORY_SEPARATOR.'course_modal.php');
$doc->addStyleSheet(JURI::base()."components/com_guru/css/g_admin_modal.css");
$guruAdminModelguruCommissions = new guruAdminModelguruCommissions();
$data_post = JFactory::getApplication()->input->post->getArray();
?>
<style>
	div.modal {
			z-index: 9999;
			margin-left:-45%;
			top:6%;
			padding:10px;
			width:90%;
	}
	.modal-backdrop, .modal-backdrop.fade.in {
		opacity: 0.4 !important;
	}
	div.modal-header{
		padding :5px;
	}
</style>
<script>
	var first = false;
	
	function showContent1(href){
		first = true;
		jQuery( '#myModal1 .modal-bodyc iframe').attr('src', href);
		screen_height = window.innerHeight;
		document.getElementById('myModal1').style.height = (screen_height -110)+'px';
		document.getElementById('paid_commissions').style.height = (screen_height -150)+'px';
	}
	jQuery('body').click(function () {
		if(!first){
			jQuery('#myModal1 .modal-bodyc iframe').attr('src', '');
		}
		else{
			first = false;
		}
	});
</script>
<form action="<?php echo JUri::root() ?>administrator/index.php" id="adminForm" name="adminForm" method="post">
<div id="myModal1" class="modal hide" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-header">
        <button type="button" id="close" class="close" data-dismiss="modal" aria-hidden="true">x</button>
     </div>
     <div class="modal-bodyc">
     	<iframe id="paid_commissions" width="100%"  frameborder="0" style="" ></iframe>
    </div>
</div>

 <span id="message_lib" class="alert" style="display:none; margin-top: 5px;">
    <a href='http://www.ijoomla.com/redirect/guru/mpdf.htm' target="_blank">
        <?php echo "1. ".JText::_("GURU_DOWNLOAD_MPDF1"); ?>
    </a>
    <br />
    <?php echo "2. ".JText::_("GURU_DOWNLOAD_MPDF2"); ?>
    <br />
    <?php echo "3. ".JText::_("GURU_DOWNLOAD_MPDF3"); ?>
</span>
<table style="width: 100%;" cellpadding="5" cellspacing="5" bgcolor="#FFFFFF">
    <tr>
        <td>
            <input type="text" name="search_text" value="<?php if(isset($data_post['search_text'])) echo $data_post['search_text'];?>" />
            <input class="btn btn-primary" onclick="document.getElementById('export').value=''" type="submit" name="submit_search" value="<?php echo JText::_('GURU_SEARCHTXT');?>" />
        </td>
    <tr>
</table>
<table class="sortable table table-striped adminlist table-bordered">
    <thead>
        <tr>
            <th class="sorttable_nosort">
                <?php echo JText::_('GURU_AUTHOR');?><i class="icon-menu-2"></i>
            </th>
            <th>
                <?php echo JText::_('GURU_TOTAL_PAID_COMM');?><i class="icon-menu-2"></i>
            </th>
            <th> <?php echo JText::_('GURU_NB_PAYMENTS');?><i class="icon-menu-2"></i>
            </th>
            <th>
                <?php echo JText::_('GURU_LAST_PAYMENT_DATE');?><i class="icon-menu-2"></i>
            </th>
            <th>
                <?php echo JText::_('GURU_VIEW_DETAILS');?>
            </th>            
        </tr>
    </thead>
    
    <tbody>
    
    <?php
        for ($i = 0; $i < $n; $i++):
			$teachername = $guruAdminModelguruCommissions->getTeacherName($commissions_paid_teacher[$i]["author_id"]);
			$character = "GURU_CURRENCY_".$commissions_paid_teacher[$i]["coin"];
			
			if($commissions_paid_teacher[$i]["total"] == 0){
				//continue; /** commented this to show all orders even price or commision is 0 **/
			}

    ?>
        <tr> 
           <td>
           		<?php echo '<a href="index.php?option=com_guru&controller=guruAuthor&task=edit&id='.$commissions_paid_teacher[$i]["author_id"].'">'.$teachername[0].'</a>';?>
           </td>
           
           <td>
           		<?php 
					if($currencypos == 0){
						echo JText::_($character)." ".number_format($commissions_paid_teacher[$i]["total"],2);
					}
					else{
						echo number_format($commissions_paid_teacher[$i]["total"],2)." ".JText::_($character);
					}
			   ?>
           </td>
           <td>
           		<?php echo $commissions_paid_teacher[$i]["count_payments"];?>
           </td>
           <td>
           		<?php 
					if($config->hour_format == 12){
					$format = " Y-m-d h:i:s A ";
						switch($datetype){
							case "d-m-Y H:i:s": $format = "d-m-Y h:i:s A";
								  break;
							case "d.m.Y H:i": $format = "d.m.Y h:i A"; 
					  			break;
							case "d/m/Y H:i:s": $format = "d/m/Y h:i:s A"; 
								  break;
							case "m-d-Y H:i:s": $format = "m-d-Y h:i:s A"; 
								  break;
							case "m/d/Y H:i:s": $format = "m/d/Y h:i:s A"; 
								  break;
							case "Y-m-d H:i:s": $format = "Y-m-d h:i:s A"; 
								  break;
							case "Y/m/d H:i:s": $format = "Y/m/d h:i:s A"; 
								  break;
							case "d-m-Y": $format = "d-m-Y"; 
								  break;
							case "d/m/Y": $format = "d/m/Y"; 
								  break;
							case "m-d-Y": $format = "m-d-Y"; 
								  break;
							case "m/d/Y": $format = "m/d/Y"; 
								  break;
							case "Y-m-d": $format = "Y-m-d"; 
								  break;
							case "Y/m/d": $format = "Y/m/d";	
								  break;	  	  	  	  	  	  	  	  	  	  
						}
						$date_int = strtotime($commissions_paid_teacher[$i]["data_paid"]);
						$date_string = JHTML::_('date', $date_int, $format );
					}
					else{
						$date_int = strtotime($commissions_paid_teacher[$i]["data_paid"]);
						//$date_string = date("Y-m-d H:i:s", $date_int);
						$format = "Y-m-d H:M:S";
						switch($datetype){
							case "d-m-Y H:i:s": $format = "d-m-Y H:i:s";
								  break;
							case "d.m.Y H:i": $format = "d.m.Y H:i"; 
					  			break;
							case "d/m/Y H:i:s": $format = "d/m/Y H:i:s"; 
								  break;
							case "m-d-Y H:i:s": $format = "m-d-Y H:i:s"; 
								  break;
							case "m/d/Y H:i:s": $format = "m/d/Y H:i:s"; 
								  break;
							case "Y-m-d H:i:s": $format = "Y-m-d H:i:s"; 
								  break;
							case "Y/m/d H:i:s": $format = "Y/m/d H:i:s"; 
								  break;
							case "d-m-Y": $format = "d-m-Y"; 
								  break;
							case "d/m/Y": $format = "d/m/Y"; 
								  break;
							case "m-d-Y": $format = "m-d-Y"; 
								  break;
							case "m/d/Y": $format = "m/d/Y"; 
								  break;
							case "Y-m-d": $format = "Y-m-d"; 
								  break;
							case "Y/m/d": $format = "Y/m/d";		
								  break;  	  	  	  	  	  	  	  	  	  
						}
						$date_string = JHTML::_('date', $date_int, $format);
					}	
				
				?>
           		<?php echo $date_string;?>
           </td>
           <td>
           	  <a class="btn btn-primary" data-toggle="modal" data-target="#myModal1" onClick = "showContent1('index.php?option=com_guru&controller=guruCommissions&task=details&page=paid&tmpl=component&orders=<?php echo $commissions_paid_teacher[$i]["order_auth_ids"];?>&cid[]=<?php echo $commissions_paid_teacher[$i]["author_id"];?>&currencyc=<?php echo $commissions_paid_teacher[$i]["coin"];?>');" href="#"><?php echo JText::_("GURU_DETAILS"); ?></a>
           </td>
        </tr>
    <?php 
        endfor;
    ?>
    </tbody>
    <tfoot>
        <tr>
            <td colspan="10">
                <div class="btn-group pull-left hidden-phone">
                    <label for="limit" class="element-invisible"><?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC');?></label>
                    <?php
                    	$select_limit = $this->pagination->getLimitBox();
						$select_limit = str_replace('Joomla.submitform()', "document.getElementById('export').value=''; Joomla.submitform()", $select_limit);
						echo $select_limit;
					?>
                </div>
                <?php
                	$pages = $this->pagination->getListFooter();
					$pages = str_replace('Joomla.submitform()', "document.getElementById('export').value=''; Joomla.submitform()", $pages);
					echo $pages;
				?>
            </td>
        </tr>
    </tfoot>
</table>
<input type="hidden" name="option" value="com_guru" />
<input type="hidden" id="export" name="export" value="" />
<input type="hidden" name="task" value="<?php echo JFactory::getApplication()->input->get("task", "paid");?>" />
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="controller" value="guruCommissions" />
<input type="hidden" name="old_limit" value="<?php echo JFactory::getApplication()->input->get("limitstart"); ?>" />
</form>