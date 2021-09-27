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
$doc =JFactory::getDocument();
$doc->addScript(JURI::root().'/components/com_guru/js/sorttable.js');
$details = $this->details;
$total_paid = $this->total_paid;
$config = $this->config;
$datetype  = $config->datetype;
$currencypos = $config->currencypos;
$character = "GURU_CURRENCY_".$config->currency;
$guruModelguruAuthor = new guruModelguruAuthor();
$pagination = $this->getDetailsPagination();

//-----------------------------------------------------------------------------------
$tot = "";
if(isset($total_paid) && count($total_paid) > 0){
	$temp = array();
	foreach($total_paid as $key=>$value){
		if(isset($temp[$value["currency"]])){
			$temp[$value["currency"]] += $value["amount_paid_author"];
		}
		else{
			$temp[$value["currency"]] = $value["amount_paid_author"];
		}
	}
	
	if($currencypos == 0){
		foreach($temp as $currency=>$value){
			$temp[$currency] = JText::_("GURU_CURRENCY_".$currency)." ".number_format($value, 2);
		}
		$tot = implode("&nbsp;&nbsp;&nbsp;", $temp);
	}
	else{
		foreach($temp as $currency=>$value){
			$temp[$currency] = number_format($value, 2)." ".JText::_("GURU_CURRENCY_".$currency);
		}
		$tot = implode("&nbsp;&nbsp;&nbsp;", $temp);
	}
}
//-----------------------------------------------------------------------------------

?>

<script type="text/javascript" language="javascript">
	document.body.className = document.body.className.replace("modal", "");
</script>

<div class="uk-grid wk-grid">
	<div class="uk-width-large-1-2 uk-width-medium-1-2 uk-width-small-1-1 wk-width-large-1-2 wk-width-medium-1-2 wk-width-small-1-1">
    	<h2 class="gru-page-title"><?php echo JText::_('GURU_COMMISSIONS_RECEIVED')." ".">"." ".JText::_('GURU_VIEW_DETAILS'); ?></h2>
    </div>
    
    <div class="uk-width-large-1-2 uk-width-medium-1-2 uk-width-small-1-1 uk-text-right wk-width-large-1-2 wk-width-medium-1-2 wk-width-small-1-1 wk-text-right">
    	<div class="uk-button-dropdown wk-button-dropdown" data-uk-dropdown>
            <button class="uk-button uk-button-primary wk-button wk-button-primary" onclick="document.getElementById('export-csv').click();"><?php echo JText::_("GURU_EXPORT"); ?></button>
            <div class="uk-dropdown uk-dropdown-small uk-text-left wk-dropdown wk-dropdown-small wk-text-left">
				<ul class="uk-nav uk-nav-dropdown wk-nav wk-nav-dropdown">
					<li>
                        <div id="export-csv" onclick="document.adminForm.export.value='csv'; document.adminForm.submit();" style="width: 100%; padding: 2px 10px; cursor: pointer;"><?php echo JText::_("GURU_CSV");?></div>
                    </li>
				</ul>
            </div>
        </div>
    </div>
</div>

<div class="uk-grid wk-grid">
	<div class="uk-width-1-1 wk-width-1-1">
    	<?php echo JText::_('GURU_COMMISSIONS_RECEIVED').":"." ".'<b>'.$tot.'</b>'; ?>
    </div>
</div>

<style>
	.component, .contentpane {
		background-color:#FFFFFF;
	}
</style>

<form id="adminForm" name="adminForm" method="post">
	<div class="gru-page-filters">
    	<div class="gru-filter-item"  style="line-height: 30px;">
			<?php  echo JText::_("GURU_FILTER_BY"); ?>
		</div>
    	<div class="gru-filter-item">
			<?php
                $user = JFactory::getUser();
				$teacher_id = $user->id;
				$filter_course = JFactory::getApplication()->input->get("filter_course", "0");
                $course = $guruModelguruAuthor->getAllCourses($teacher_id);
            ?>
        
            <select name="filter_course" style="margin:0px;" onchange="document.getElementById('export').value=''; document.adminForm.submit();">
                <option value="0" <?php if($filter_course == "0"){ echo 'selected="selected"'; }?>><?php  echo JText::_("GURU_SELECT_COURSE"); ?></option>
                <?php
                    if(isset($course) && count($course) > 0){
                        foreach($course as $key=>$courses){
                ?>
                            <option value="<?php echo $courses["id"]; ?>" <?php if($courses["id"] == $filter_course){echo 'selected="selected"';} ?> ><?php echo $courses["name"]; ?></option>
                <?php
                        }
                    }
                ?>
            </select>
		</div>
    </div>
    
    <div class="clearfix"></div>
    
    <table class="sortable uk-table uk-table-striped wk-table wk-table-striped">
        <thead>
            <tr>
                <th>
                #
                </th>
                <th>
                    <?php echo JText::_('GURU_ID');?><i class="icon-menu-2"></i>
                </th>
                <th>
                    <?php echo JText::_('GURU_MYORDERS_ORDER_DATE');?><i class="icon-menu-2"></i>
                </th>
                <th>
					<?php echo JText::_('GURU_COURSE_NAME');?><i class="icon-menu-2"></i>
                </th>
                <th class="sorttable_numeric">
                    <?php echo JText::_('VIEWORDERSAMOUNTPAID'); ?><i class="icon-menu-2"></i>
                </th> 
                <th>
                    <?php echo JText::_('GURU_VIEW_DETAILS');?>
                </th> 
            </tr>
        </thead>
        
        <tbody>
        <?php
        $inc = 1;
		
		foreach($details as $key=>$value){
			$temp_key = explode("-", $key);
			$course_name =  $guruModelguruAuthor->getCourseName1($value["course_id"]);
			$character = "GURU_CURRENCY_".$temp_key["3"];
        ?>
            <tr> 
                <td>
                    <?php echo $inc; ?>
                </td>
                <td>
                    <?php echo $value["id"];?>
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
					<?php echo $course_name;?>
                </td>
                <td>
                <?php
                if($currencypos == 0){
                    echo JText::_($character)." ".number_format($value["amount_paid_author"],2);
                }
                else{
                    echo number_format($value["amount_paid_author"],2)." ".JText::_($character);
                }
                ?>
                </td>
                <td>
                    <a class="btn btn-primary" href ="<?php echo JUri::root() ?>index.php?option=com_guru&controller=guruAuthor&task=details_paid&tmpl=component&course_id=<?php echo $value["course_id"];?>&cid[]=<?php echo $value["author_id"];?>&block=<?php echo $value["id"];?>&date=<?php echo $value["data"];?>"><?php echo JText::_("GURU_DETAILS"); ?></a>
                </td> 
            </tr>
            <?php			
            $inc ++;
        }
        
    ?>
     <tfoot>
        <tr>
            <td colspan="10">
                <div class="btn-group pull-left">
                    <label for="limit" class="element-invisible"><?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC');?></label>
                    <?php
                    	$select_limit = $pagination->getLimitBox();
						$select_limit = str_replace('this.form.submit()', "document.getElementById('export').value=''; Joomla.submitform()", $select_limit);
						echo $select_limit;
					?>
                </div>
                <?php
                	$pages = $pagination->getListFooter();
					include_once(JPATH_SITE.DIRECTORY_SEPARATOR."components".DIRECTORY_SEPARATOR."com_guru".DIRECTORY_SEPARATOR."helpers".DIRECTORY_SEPARATOR."helper.php");
					$helper = new guruHelper();
					$pages = str_replace('name="limitstart"', 'name="temp_limitstart"', $pages);
					$pages = $helper->transformPagination($pages);
					$pages = str_replace('Joomla.submitform()', "document.getElementById('export').value=''; Joomla.submitform()", $pages);
					echo $pages;
				?>
                
            </td>
        </tr>
    </tfoot>
</table> 
	<input type="hidden" name="task" value="<?php echo JFactory::getApplication()->input->get("task", "authorcommissions_paid");?>" />
    <input type="hidden" name="option" value="com_guru" />
    <input type="hidden" name="controller" value="guruAuthor" />
    <input type="hidden" name="view" value="guruauthor" />
    <input type="hidden" name="boxchecked" value="" />
    <input type="hidden" id="export" name="export" value="" />
    <input type="hidden" name="old_limit" value="<?php echo intval($pagination->limit); ?>" />
</form>
