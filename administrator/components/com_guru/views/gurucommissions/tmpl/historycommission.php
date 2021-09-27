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
$doc =JFactory::getDocument();
$doc->addScript(JURI::root().'/components/com_guru/js/sorttable.js');
$commissions_paid = $this->commissions_paid;
$n = count($commissions_paid);
$sum = 0;
$config = $this->config;
$currencypos = $config->currencypos;
$datetype  = $config->datetype;
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
		document.getElementById('history_commissions').style.height = (screen_height -150)+'px';
		
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
<form  id="adminForm" name="adminForm" method="post">
<div id="myModal1" class="modal hide" style="">
    <div class="modal-header">
        <button type="button" id="close" class="close" data-dismiss="modal" aria-hidden="true">x</button>
     </div>
     <div class="modal-bodyc" style="background-color:#FFFFFF;" >
        <iframe id="history_commissions" style="" width="100%" frameborder="0"></iframe>
    </div>
</div>
<table style="width: 78%;" cellpadding="5" cellspacing="5" bgcolor="#FFFFFF">
    <tr>
        <td>
            <input type="text" name="search_text" value="<?php if(isset($data_post['search_text'])) echo $data_post['search_text'];?>" />
            <input class="btn btn-primary" type="submit" onclick="document.getElementById('export').value=''" name="submit_search" value="<?php echo JText::_('GURU_SEARCHTXT');?>" />
        </td>
        <td>
        	<?php
            	$filter_teacher = JFactory::getApplication()->input->get("filter_teacher", "0");
				$teachers = $this->getAllTeachers();
			?>
            <select name="filter_teacher" class="inputbox" onchange="document.getElementById('export').value=''; document.adminForm.submit();">
                <option value="0" <?php if($filter_teacher == "0"){ echo 'selected="selected"'; }?>><?php  echo JText::_("GURU_SELECT_TEACHER"); ?></option>
                <?php
                	if(isset($teachers) && count($teachers) > 0){
						foreach($teachers as $key=>$teacher){
				?>
                			<option value="<?php echo $teacher["id"]; ?>" <?php if($teacher["id"] == $filter_teacher){echo 'selected="selected"';} ?> ><?php echo $teacher["name"]; ?></option>
                <?php
						}
					}
				?>
            </select>
        </td>
    <tr>
</table>
<table class="sortable table table-striped adminlist table-bordered">
    <thead>
        <tr>
             <th>
            	#
            </th>
            <th>
            	 <?php echo JText::_('GURU_ID');?><i class="icon-menu-2"></i>
            </th>
            <th>
                <?php echo JText::_('GURU_AUTHOR');?><i class="icon-menu-2"></i>
            </th>
            <th class="sorttable_numeric">
                <?php echo JText::_('VIEWORDERSAMOUNTPAID');?><i class="icon-menu-2"></i>
            </th>
            <th> <?php echo JText::_('GURU_PAYMENT_DATE');?><i class="icon-menu-2"></i>
            </th>
            <th>
                <?php echo JText::_('GURU_VIEW_DETAILS');?>
            </th>
        </tr>
    </thead>
    
    <tbody>
    
    <?php
		$inc = 1;
		$total_sum_per_currency = array();
        foreach($commissions_paid as $key=>$value){
		
		if($value["amount_paid_author"] == 0){
			//continue; /** commented this to show all orders even price or commision is 0 **/
		}
		
		$teachername = $guruAdminModelguruCommissions->getTeacherName($value["author_id"]);
		$character = "GURU_CURRENCY_".$value["currency"];
		
		if(isset($total_sum_per_currency[$value["currency"]])){
				$total_sum_per_currency[$value["currency"]] += $value["amount_paid_author"];
			}
			else{
				$total_sum_per_currency[$value["currency"]] = $value["amount_paid_author"];
			}
    ?>
        <tr> 
        	<td>
				<?php echo $inc; ?>
            </td>
            <td>
                <?php echo $value["id"];?>
            </td>	
            <td>
            	<?php echo '<a href="index.php?option=com_guru&controller=guruAuthor&task=edit&id='.$value["author_id"].'">'.$teachername[0].'</a>';?>
            </td>
            
            <td>
            	
            	<?php
					if($currencypos == 0){
						echo JText::_($character)." ".number_format($value["amount_paid_author"],2);
					}
					else{
						echo number_format($value[$i]["amount_paid_author"],2)." ".JText::_($character);
					}
				
				 ?>
            </td>
            
            <td>
            	<?php 
					if($config->hour_format == 12){
					$format = " Y-m-d h:i:s A ";
						switch($datetype){
							case "d-m-Y H:i:s": $format = "d-m-Y h:i:s A";
								  break;
							case "d/m/Y H:i:s": $format = "d/m/Y h:i:s A"; 
								  break;
							case "d.m.Y H:i": $format = "d.m.Y h:i A"; 
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
						$date_int = strtotime($value["data"]);
						$date_string = JHTML::_('date', $date_int, $format );
					}
					else{
						$date_int = strtotime($value["data"]);
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
            	<a class="btn btn-primary" data-toggle="modal" data-target="#myModal1" onClick = "showContent1('index.php?option=com_guru&controller=guruCommissions&task=details&page=history1&tmpl=component&course_id=<?php echo $value["course_id"];?>&cid[]=<?php echo $value["author_id"];?>&date=<?php echo $value["data"];?>&block=<?php echo $value["id"];?>&p=1');" href="#"><?php echo JText::_("GURU_DETAILS"); ?></a>	
            </td>
        </tr>
    <?php 
		$inc ++;
       }
    ?>  
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
    </tbody>
</table>
<table class="table table-striped table-bordered">
	<tr>
        <th>
        </th>
        <th>
            <?php echo JText::_('VIEWORDERSAMOUNTPAID');?>
        </th>
    </tr>
    <tr>
        <td>
           <b><?php echo JText::_('GURU_SUMMARY');?></b>
        </td>
        <td>
           <b><?php
				foreach($total_sum_per_currency as $currency=>$value){
					if($currencypos == 0){
						echo JText::_("GURU_CURRENCY_".$currency)." ".number_format($value,2);
					}
					else{
						echo number_format($value,2)." ".JText::_("GURU_CURRENCY_".$currency);
					}
					echo"<br/>";	
				}
			 ?>
		   </b>
        </td>
    </tr>
</table>

<input type="hidden" name="option" value="com_guru" />
<input type="hidden" id="export" name="export" value="" />
<input type="hidden" name="task" value="history" />
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="controller" value="guruCommissions" />
<input type="hidden" name="old_limit" value="<?php echo JFactory::getApplication()->input->get("limitstart"); ?>" />
</form>