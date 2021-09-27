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

    $doc =JFactory::getDocument();
    //These scripts are already been included from the administrator\components\com_guru\guru.php file
    //$doc->addScript('components/com_guru/js/jquery.DOMWindow.js');
    $app = JFactory::getApplication();
    $input = $app->input;
    $data_post = $input->post->getArray();
    
    require_once(JPATH_BASE.'/../components/com_guru/helpers/helper.php');
    $guruHelper = new guruHelper();

    $k = 0;
    $n = 0;
    $orders = $this->orders;
    $n = count($orders);
    $config = guruAdminModelguruOrder::getConfig();
    $dates=$this->dates;    
    $datetype = $this->datetype;
    $currencypos = $config->currencypos;
    
    $total = $this->getTotalSum();
?>
    <script language="javascript">
        function chooseExport(value){
            if(value == ""){
                document.adminForm.export.value='';
            }
            else if(value == "csv"){
                document.adminForm.export.value='csv';
            }
            else if(value == "pdf"){
                exist_mpdf = document.getElementById("exist-mpdf").value;
                
                if(exist_mpdf == "1"){
                    document.adminForm.export.value='pdf';
                }
                else{
                    document.getElementById('message_lib').style.display='block';
                    document.adminForm.export.value='';
                }
            }
        }
        
        function exportBtn(){
            choose_export = document.getElementById("choose-export").value;
            
            if(choose_export == ""){
                alert("<?php echo JText::_("GURU_SELECT_EXPORT_MSG"); ?>");
                return false;
            }
            document.adminForm.submit();
        }
        
        function validateForm(){
            document.adminForm.export.value='';
            
            var sDate = new Date(document.getElementById('startdate').value);
            var eDate = new Date(document.getElementById('enddate').value);

            if(Date.parse(document.getElementById('startdate').value) == "Invalid Date") {
                alert("<?php echo JText::_("GURU_INVALID_DATE");?>");
                document.getElementById('startdate').value="";
                return false;
            }
            else if( Date.parse(document.getElementById('enddate').value) == "Invalid Date") {
                alert("<?php echo JText::_("GURU_INVALID_DATE");?>");
                document.getElementById('enddate').value="";
                return false;
            }
            else if(document.getElementById('startdate').value == '0000-00-00'){
                alert("<?php echo JText::_("GURU_INVALID_DATE");?>");
                document.getElementById('startdate').value="";
                return false;
            }
            else if(document.getElementById('enddate').value == '0000-00-00'){
                alert("<?php echo JText::_("GURU_INVALID_DATE");?>");
                document.getElementById('startdate').value="";
                return false;
            }
            else if(document.getElementById('startdate').value != '' && document.getElementById('enddate').value != '' && sDate> eDate)
            {
                alert("<?php echo JText::_("GURU_DATE_GRATER");?>");
                return false;
            }       
        }
    </script>   
<?php   
    $ord_pay = $input->get('ord_payments');
    $startdate = $input->get('startdate');
    $enddate = $input->get('enddate');
    
    $limistart = $app->getUserStateFromRequest('com_surveys.surveys'.'.list.start', 'limitstart');
    $limit = $app->getUserStateFromRequest('com_surveys.surveys'.'.list.limit', 'limit');


    $filter_status = $input->get("filter_status", "-");
    $filter_payement = $input->get("filter_payement", "-");
?>  



<form action="<?php echo JUri::root(true) ?>/administrator/index.php?option=com_guru&controller=guruOrders" id="adminForm" method="post" name="adminForm" onsubmit="return validateForm();">
     <div class="row-fluid">
        <div class="span4">
            <?php
                $session = JFactory::getSession();
                $registry = $session->get('registry');
                $search_order = $registry->get('search_order', "");
            ?>
            
            <input type="text" value="<?php echo $search_order; ?>" name="search"/>&nbsp;&nbsp;
            <input style="margin-left:10px;" class="btn btn-primary" type="submit" value="<?php echo JText::_("GURU_SEARCHTXT"); ?>" name="submit_search"/>
            
            <div class="clearfix" style="padding:3px;"></div>
            
            <select name="filter_status" class="inputbox" onchange="document.adminForm.export.value=''; document.adminForm.submit();">
                <option value="-"  <?php if ($filter_status == "-"){ echo ' selected="selected" '; }?>><?php  echo JText::_("GURU_STATUS"); ?></option>
                <option value="Pending"  <?php if ($filter_status == "Pending"){ echo ' selected="selected" '; }?>><?php  echo JText::_("GURU_AU_PENDING"); ?></option>
                <option value="Paid"  <?php if ($filter_status == "Paid"){ echo ' selected="selected" '; }?>><?php echo JText::_("GURU_O_PAID"); ?></option>
            </select>
            
            <div class="clearfix" style="padding:3px;"></div>
            
            <?php
                // also count disabled payment methods
                $db = JFactory::getDbo();
                $query = $db->getQuery(true)
                    ->select(
                        $db->quoteName(
                            array(
                                'folder',
                                'element',
                                'params',
                                'extension_id'
                            ),
                            array(
                                'type',
                                'name',
                                'params',
                                'id'
                            )
                        )
                    )
                    ->from('#__extensions')
                    ->where('type = ' . $db->quote('plugin'))
                    ->where('folder = ' . $db->q('gurupayment'))
                    ->where('state IN (0,1)');

                $plugins = $db->setQuery($query)->loadObjectList();
            ?>

            <select name="filter_payement" class="inputbox" onchange="document.adminForm.export.value=''; document.adminForm.submit();">
                <option value="-"  <?php if ($filter_payement == "-"){ echo ' selected="selected" '; }?>><?php  echo JText::_("GURU_ORDPAYMENTMETHOD"); ?></option>
                <?php
                    if(isset($plugins) && count($plugins) > 0){
                        foreach($plugins as $key=>$plugin){
                            $plugin_params = json_decode($plugin->params, true);
                            $plugin_label = $plugin_params[$plugin->name."_label"];

                            $selected = "";

                            if($plugin->name == $filter_payement){
                                $selected = 'selected="selected"';
                            }
                ?>
                            <option value="<?php echo $plugin->name; ?>" <?php echo $selected; ?> ><?php echo $plugin_label; ?></option>
                <?php
                        }
                    }
                ?>

                <option value="payauthorize"  <?php if ($filter_payement == "payauthorize"){ echo ' selected="selected" '; }?>><?php  echo JText::_("GURU_PAYAUTHORIZE"); ?></option>
                <option value="paypaypal"  <?php if ($filter_payement == "paypaypal"){ echo ' selected="selected" '; }?>><?php echo JText::_("GURU_PAYPAL"); ?></option>
            </select>
            
            <div class="clearfix" style="padding:3px;"></div>
            
            <?php
                $filter_teacher = JFactory::getApplication()->input->get("filter_teacher", "0");
                $teachers = $this->getAllTeachers();
            ?>
            <select name="filter_teacher" class="inputbox" onchange="document.adminForm.export.value=''; document.adminForm.submit();">
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
        </div>
        
        <div class="span4">
            <?php
                echo JHTML::_("calendar", $startdate, 'startdate', 'startdate'); 
            ?>
            <?php
                echo JHTML::_("calendar", $enddate, 'enddate', 'enddate'); 
            ?>
            <br>
            <input style="margin-top:-10px;" class="btn btn-primary" type="submit" value="<?php echo JText::_("GURU_VIEWSTATGO"); ?>" name="submit_go"/>
        </div>
        <div class="span4">
            <div class="clearfix" style="padding:3px;"></div>
            
            <select id="choose-export" name="choose_export" onchange="javascript:chooseExport(this.value); return false;">
                <option value=""> <?php echo JText::_("GURU_SELECT_EXPORT_FORMAT"); ?> </option>
                <option value="csv"> CSV </option>
                <option value="pdf"> PDF </option>
            </select>
            <?php
                if(file_exists(JPATH_SITE.DIRECTORY_SEPARATOR."components".DIRECTORY_SEPARATOR."com_guru".DIRECTORY_SEPARATOR."helpers".DIRECTORY_SEPARATOR."MPDF".DIRECTORY_SEPARATOR."mpdf.php")){
            ?>
                    <input type="hidden" id="exist-mpdf" value="1" />
            <?php
                }
                else{
            ?>
                    <input type="hidden" id="exist-mpdf" value="0" />
            <?php
                }
            ?>
            
            <input class="btn btn-primary" type="button" onclick="javascript:exportBtn(); return false;" value="<?php echo JText::_("GURU_EXPORT"); ?>" name="submit_go" />
            
            <div class="clearfix"></div>
            
            <br />
            <span class="order-sum-label"><?php echo JText::_("GURU_TOTAL"); ?>:&nbsp;</span>
            <span class="order-sum-value"><?php echo $total; ?></span>
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

<div id="myModal" class="modal-small modal hide">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    </div>
    <div class="modal-body">
    </div>
</div>
<div class="container-fluid">
      <a data-toggle="modal" data-target="#myModal" onclick="showContentVideo('index.php?option=com_guru&controller=guruAbout&task=vimeo&id=27181273&tmpl=component')" class="pull-right guru_video" href="#">
                <img src="<?php echo JURI::base(); ?>components/com_guru/images/icon_video.gif" class="video_img" />
            <?php echo JText::_("GURU_ORDERS_VIDEO"); ?>                  
      </a>
</div>  
<div class="clearfix"></div>
<div class="well well-minimized">
    <?php echo JText::_("GURU_ORDERS_SETTINGS_DESCRIPTION"); ?>
</div>

    <div id="editcell" >
        <table class="table table-striped  table-bordered  adminlist">
            <thead>
                <tr>
                    <th width="20">
                        #
                    </th>
                    <th width="5">
                        <input type="checkbox" onclick="Joomla.checkAll(this)" name="toggle" value="" />
                        <span class="lbl"></span>
                    </th>
                    <th width="20">
                        <?php echo JText::_('GURU_ID');?>
                    </th>
                    <th>
                        <?php echo JText::_('GURU_ORDDATE'); ?>
                    </th>               
                    <th>
                        <?php echo JText::_('GURU_PRICE');?>
                    </th>
                    <th>
                        <?php echo JText::_('GURU_USERNAME');?>
                    </th>
                    <th>
                        <?php echo JText::_('GURU_CUSTOMER_HEAD');?>
                    </th>                   
                    <th>
                        <?php echo JText::_('GURU_STATUS');?>
                    </th>
                    <th>
                        <?php echo JText::_('GURU_COURSE')."(s)";?>
                    </th>
                    <th>
                        <?php echo JText::_('GURU_ORDPAYMENTMETHOD');?>
                    </th>
                    <th>
                        <?php echo JText::_('GURU_PROMOCODE');?>
                    </th>
                    <th>
                        <?php echo JText::_('GURU_INVOICE');?>
                    </th>                                   
                </tr>
            </thead>

            <tbody>
            <?php
                $j = $limistart+1;
                for ($i = 0; $i < $n; $i++):
                    $order = $this->orders[$i];
                    $order =(array)$orders[$i];
                    $id = $order["id"];
                    $checked = JHTML::_('grid.id', $i, $id);
                    $customerlink = JRoute::_("index.php?option=com_guru&controller=guruCustomers&task=edit&cid[]=".$order["userid"]);
            ?>
                <tr class="row<?php echo $k;?>"> 
                    <td align="center">
                        <?php echo $j++; ?>
                    </td>
                    <td>
                        <?php echo $checked;?>
                        <span class="lbl"></span>
                    </td>       
                
                    <td align="center">
                        <a class="a_guru" href="index.php?option=com_guru&controller=guruOrders&task=show&cid=<?php echo $id;?>"><?php echo $id;?></a>
                    </td>       
                
                    <td align="center">
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

                                $date_int = strtotime($order["order_date"]);
                                //$date_string = date("Y-m-d h:i:s A", $date_int);
                                //$date_string = JHTML::_('date', $date_int, $format );
                                //echo $date_string;

                                $timezone = new DateTimeZone( JFactory::getConfig()->get('offset') );
                                $jnow = new JDate($order["order_date"]);
                                $jnow->setTimezone($timezone);
                                $date_string = $jnow->format($format);
                                echo $date_string;
                            }
                            else{
                                $date_int = strtotime($order["order_date"]);
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
                                
                                //$date_string = JHTML::_('date', $date_int, $format);
                                //echo $date_string;

                                $timezone = new DateTimeZone( JFactory::getConfig()->get('offset') );
                                $jnow = new JDate($order["order_date"]);
                                $jnow->setTimezone($timezone);
                                $date_string = $jnow->format($format);
                                echo $date_string;
                            }
                        ?>
                    </td>   
                
                    <td align="center">
                        <?php 
                            $character = "GURU_CURRENCY_".$order["currency"];
                            if(isset($order["amount_paid"]) && trim($order["amount_paid"]) != "" && trim($order["amount_paid"]) != "-1"){
                                
                                $display_price = $guruHelper->displayPrice($order["amount_paid"]);

                                if($currencypos == 0){
                                    echo JText::_($character)." ".$display_price;
                                }
                                else{
                                    echo $display_price." ".JText::_($character);
                                }
                            }
                            else{
                                $display_price = $guruHelper->displayPrice($order["amount"]);

                                if($currencypos == 0){
                                    echo JText::_($character)." ".$display_price;
                                }
                                else{
                                    echo $display_price." ".JText::_($character);
                                }
                                
                            }
                        ?>
                    </td>
                                
                    <td align="center"> 
                        <?php $userlink = JRoute::_("index.php?option=com_users&task=user.edit&id=".$order["userid"]);?>
                        <a class="a_guru" href="<?php echo $userlink; ?>"><?php echo $order["username"]; ?></a>
                    </td>       
            
                    <td align="center">
                        <a class="a_guru" href="<?php echo $customerlink; ?>"><?php echo $order["firstname"]." ".$order["lastname"]; ?></a>
                    </td>
                    
                    <td align="center">
                        <a class="a_guru" href="index.php?option=com_guru&amp;controller=guruOrders&amp;task=cycleStatus&amp;cid[]=<?php echo $id; ?>"><?php echo $order["status"]; ?></a>
                    </td>       
                    <td align="center">
                        <?php 
                        $idsc = array();
                        if(trim($order["courses"]) != ""){
                            $temp1 = explode("|", trim($order["courses"]));
                            if(is_array($temp1) && count($temp1) > 0){
                                foreach($temp1 as $key=>$value){
                                    $temp2 = explode("-", $value);
                                    if (count($temp2) > 2) {
                                        $idsc[] = (int) trim($temp2["0"]);
                                        $course_id_plan[$temp2["0"]] = $temp2["2"];
                                        $id_price[trim($temp2["0"])]["price"] = trim($temp2["1"]);
                                    }
                                }
                            }
                        }
                        
                        $courses = "";
                        if(isset($idsc) && count($idsc) > 0){
                            $idsc = array_diff($idsc, array(''));
                            $courses = guruAdminModelguruOrder::getCourses(implode(",", $idsc));
                        }       
                        if(isset($courses) && is_array($courses) && count($courses) > 0){
                            foreach($courses as $key=>$value){
                                echo '<a href="index.php?option=com_guru&controller=guruDays&pid='.$value["id"].'">'.$value["name"].'</a>'."<br/>";
                            }
                        }
                        ?>
                    </td>
                    <td align="center">
                        <?php 
                            $plugin = array_filter($plugins, function($p) use ($order) {
                                return $p->name === $order["processor"];
                            });
                            $plugin = array_shift($plugin);
                            
                            if(is_object($plugin)) {
                                $params = new JRegistry($plugin->params);
                                $plugin_label = $params->get($order["processor"]."_label");
                            }
                            else{
                                $plugin_label = '';
                            }
                            
                            echo $plugin_label;
                        ?>
                    </td>
                    <td align="center">
                        <?php 
                            echo $this->getPromoName($order["promocodeid"]);
                        ?>
                    </td>
                    <td align="center">
                        <?php
                            $site_root = JURI::root();
                            
                            if(strpos(" ".$site_root, "www") === false && strpos(" ".$site_root, "localhost") === false){
                                $site_root = str_replace("://", "://www.", $site_root);
                            }
                        ?>
                        <a class="modal" href="<?php echo $site_root; ?>index.php?option=com_guru&view=guruOrders&task=showrec&orderid=<?php echo intval($id); ?>&tmpl=component&user_reques=<?php echo intval($order["userid"]); ?>" rel="{handler: 'iframe', size: {x: 700, y: 500}}">
                            <?php echo JText::_("GURU_VIEW"); ?>
                        </a>
                    </td>
                </tr>
                
                
                <?php 
                    $k = 1 - $k;
                    endfor;
                ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="12">
                            <div class="btn-group pull-left hidden-phone">
                                <label for="limit" class="element-invisible"><?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC');?></label>
                                <?php echo $this->pagination->getLimitBox(); ?>
                            </div>
                            <?php echo $this->pagination->getListFooter(); ?>
                        </td>
                    </tr>
            </tfoot>
    </table>
</div>

<input type="hidden" name="option" value="com_guru" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="controller" value="guruOrders" />
<input type="hidden" name="old_limit" value="<?php echo JFactory::getApplication()->input->get("limitstart"); ?>" />
<input type="hidden" name="export" value="" />
</form>
<script language="javascript">
    var first = false;
    function showContentVideo(href){
    first = true;
    jQuery.ajax({
      url: href,
      success: function(response){
       jQuery( '#myModal .modal-body').html(response);
      }
    });
}

jQuery('#myModal').on('hide', function () {
 jQuery('div.modal-body').html('');
});
jQuery('body').click(function () {
    if(!first){
        jQuery('#myModal .modal-body iframe').attr('src', '');
    }
    else{
        first = false;
    }
});
</script>