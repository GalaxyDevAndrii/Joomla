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
require_once (JPATH_COMPONENT_ADMINISTRATOR.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'helper.php');
$guruHelper = new guruHelper();

$document = JFactory::getDocument();
$order = $this->order["0"];
$promocodeid = $order["promocodeid"];
$discount_details = array();
$currency = $order["currency"];
$character = "GURU_CURRENCY_".$currency;
$guruModelguruOrder = new guruModelguruOrder();
$db = JFactory::getDBO();
$sql = "select invoice_issued_by from #__guru_config where id=1";
$db->setQuery($sql);
$db->execute();
$invoice_issued_by = $db->loadResult();
$totall_discount = 0;
$totall_discount_p = 0;

?>

<script type="text/javascript" language="javascript">
    document.body.className = document.body.className.replace("modal", "");
</script>

<script>
    function printDiv(contentpane, device) {
         //var printContents = document.getElementById("contentpane").innerHTML;
         //var originalContents = document.body.innerHTML;
    
         //document.body.innerHTML = printContents;
         //window.print();
         //document.body.innerHTML = originalContents;

         if(device == "desktop"){
            jQuery(".uk-hidden-large.uk-hidden-medium.invoice").css("display", "none");
            window.print();
         }
         else if(device == "mobile"){
            jQuery(".hidden-phone.invoice").css("display", "none");
            window.print();
         }
    }
</script>

<style>
    .component, .contentpane {
        background-color:#FFFFFF;
    }
</style>

<?php
if($promocodeid != "0"){
    $courses_list_promo = $guruModelguruOrder->getCoursesPromo($promocodeid);
}

$courses_array = explode("|",@$courses_list_promo["0"]);
$courses_array = array_values(array_filter($courses_array));

if(empty($order)){
    $Itemid = JFactory::getApplication()->input->get("Itemid", "0", "raw");
    
    $helper = new guruHelper();
    $itemid_seo = $helper->getSeoItemid();
    $itemid_seo = @$itemid_seo["guruorders"];
    
    if(intval($itemid_seo) > 0){
        $Itemid = intval($itemid_seo);
    }
    
    $app = JFactory::getApplication('site');
    $app->redirect(JRoute::_("index.php?option=com_guru&view=guruorders&layout=myorders&"."&Itemid=".intval($Itemid), false));
}
else{
    $db = JFactory::getDBO();
    $sql = "SELECT currencypos from #__guru_config where id =1";
    $db->setQuery($sql);
    $db->execute();
    $currencypos = $db->loadResult();
    if($currencypos == 0){
        $discount = JText::_($character)." 0.00";
    }
    else{
        $discount = "0.00 ".JText::_($character);
    }
    
    
    if($promocodeid != "0"){
        $discount_details = $guruModelguruOrder->getDiscountDetails($promocodeid);
        if($discount_details["0"]["typediscount"] == "0"){
            if($currencypos == 0){
                $discount = JText::_($character)." ".$discount_details["0"]["discount"];
            }
            else{
                $discount = $discount_details["0"]["discount"]." ".JText::_($character);
            }
        }
        else{
            $discount = $discount_details["0"]["discount"]."%";
        }
    }
    
    
    ?>
        <div class="hidden-phone">
            <?php
                if($this->show === TRUE){
            ?>
                    <button type="button" onclick="javascript:printDiv('contentpane', 'desktop'); return false;" class="uk-button uk-button-primary wk-button wk-button-primary">
                        <img src="<?php echo JUri::base()."components/com_guru/images/print.png"; ?>" alt="Print" />
                        <?php echo JText::_("GURU_PRINT"); ?>
                    </button>
            <?php
                }
            ?>
            
            <form method="post" name="adminForm" action="<?php echo JUri::root() ?>index.php" id="contentpane">
                <div class="uk-alert uk-text-center uk-margin-top uk-margin-bottom wk-alert wk-text-center wk-margin-top wk-margin-bottom">
                    <?php echo JText::_("GURU_INVOICE"); ?> #<?php echo $order["id"] ?>: <?php if($order["status"] == 'Pending'){echo JText::_("GURU_PENDING");} else{echo JText::_("GURU_O_PAID");} ?>
                </div>

                <div class="uk-grid wk-grid">
                    <div class="uk-width-1-1 wk-width-1-1">
                        <span style="font-weight:bold;"><?php echo JText::_("GURU_ISSUED_BY").":"; ?></span>
                        <br />
                        <table  class="uk-table uk-table-striped wk-table wk-table-striped">
                            <tr>
                                <td>
                                    <?php 
                                    $ivoces_details = nl2br($invoice_issued_by);
                                    $ivoces_details = stripslashes($ivoces_details);

                                    echo $ivoces_details ;?>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
                
                <div class="uk-grid wk-grid">
                    <div class="uk-width-1-1 wk-width-1-1">
                        <b>
                            <?php echo JText::_("GURU_MYORDERS_ORDER_DATE"); ?>
                            <?php 
                                $helper = new guruHelper();
                                echo $helper->getDate($order["order_date"]);
                            ?>
                        </b>
                    </div>
                </div>
                
                <div class="uk-grid wk-grid">
                    <div class="uk-width-1-1 wk-width-1-1">
                        <span style="font-weight:bold;"><?php echo JText::_("GURU_BILLED_TO").":"; ?></span>
                    </div>
                </div>
                
                <?php
                if($this->show === TRUE){
                    $customer = $guruModelguruOrder->getCustomerDetails($order["userid"]);
                ?>
                    <div class="uk-grid wk-grid">
                        <div class="uk-width-large-2-3 uk-width-medium-1-1 uk-width-small-1-1 wk-width-large-2-3 wk-width-medium-1-1 wk-width-small-1-1">
                            <table class="uk-table uk-table-striped wk-table wk-table-striped">
                                <tr>
                                    <td><?php echo JText::_("GURU_FIRS_NAME"); ?>:</td>
                                    <td style="padding-left:20px;"><?php echo $customer["0"]["firstname"]; ?></td>
                                </tr>
                                <tr>
                                    <td><?php echo JText::_("GURU_LAST_NAME"); ?>:</td>
                                    <td style="padding-left:20px;"><?php echo $customer["0"]["lastname"]; ?></td>
                                </tr>
                                <tr>
                                    <td><?php echo JText::_("GURU_COMPANY"); ?>:</td>
                                    <td style="padding-left:20px;"><?php echo $customer["0"]["company"]; ?></td>
                                </tr>
                            </table>
                        </div>
                        
                        <?php
                            /*$ivoces_details = nl2br($invoice_issued_by);
                            $ivoces_details = stripslashes($ivoces_details);
                            
                            if(trim($ivoces_details) != ""){
                        ?>
                                <div class="uk-width-large-1-3 uk-width-medium-1-1 uk-width-small-1-1">
                                    <table class="uk-table uk-table-striped">
                                        <tr>
                                            <td>
                                                <?php echo $ivoces_details ;?>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                        <?php
                            }*/
                        ?>
                    </div>
                <?php
                }
                ?>
                
                                   
                                
                <table class="uk-table uk-table-striped uk-margin-top wk-table wk-table-striped wk-margin-top" style="width: 100%; text-align: left; margin-bottom: 50px;">
                        <tr>
                            <th><?php echo JText::_("GURU_COURSE_NAME"); ?></th>
                            <th><?php echo JText::_("GURU_QUANTITY"); ?></th>       
                            <th><?php echo JText::_("GURU_PROGRAM_DETAILS_PRICE"); ?></th>
                            <th><?php echo JText::_("GURU_DISCOUNTPROMO"); ?></th>  
                            <th><?php echo JText::_("GURU_TOTAL"); ?></th>  
                        </tr>
                <?php
                    $ids = "0";
                    $id_price = array();
                    $id = array();
                    
                    if(trim($order["courses"]) != ""){
                        $temp1 = explode("|", trim($order["courses"]));
                        
                        if(is_array($temp1) && count($temp1) > 0){
                            foreach($temp1 as $key=>$value){
                                $temp2 = explode("-", $value);                  
                                if (count($temp2) > 2) {
                                    $id[] = trim($temp2["0"]);
                                    $id_price[trim($temp2["0"])]["price"] = trim($temp2["1"]);                      
                                    //$id_price[trim($temp2["0"])]["quantity"] = trim($temp2["2"]);
                                }
                            }
                        }
                    }
                    
                    $courses = "";
                    if(isset($id) && count($id) > 0){
                        $courses = $guruModelguruOrder->getCourses(implode(",", $id));
                    }   
                    if(isset($courses) && is_array($courses) && count($courses) > 0){
                        $i = 0;
                        $k = 1;
                        foreach($courses as $key=>$value){
                            $price = $id_price[$value["id"]]["price"];
                            @$total_courses_price += (float)$price;
                ?>          
                        <tr class="<?php echo "row".$i; ?> ">
                            <td><?php echo $value["name"]; ?></td>
                            <td>1</td>
                            <td><?php
                            if($currencypos == 0){
                                echo JText::_($character)." ".$guruHelper->displayPrice($id_price[$value["id"]]["price"]);
                            }
                            else{
                                echo $guruHelper->displayPrice($id_price[$value["id"]]["price"])." ".JText::_($character);
                            }
                              ?></td>
                            <td>
                                <?php
                                    if(in_array($value["id"], $courses_array) || count($courses_array) == 0){
                                        $promo_discount_percourse = $guruModelguruOrder->getPromoDiscountCourses($price, $promocodeid);
                                        
                                        if($currencypos == 0){
                                            echo JText::_($character)." ".$guruHelper->displayPrice($promo_discount_percourse)." (".$discount.")";
                                        }
                                        else{
                                            echo $guruHelper->displayPrice($promo_discount_percourse)." ".JText::_($character)." (".$discount.")";
                                        } 
                                        
                                    }
                                    else{
                                        if($currencypos == 0){
                                            echo JText::_($character)." "."0 (0"."%".")"; 
                                        }
                                        else{
                                            echo "0 (0"."%".")"." ".JText::_($character); 
                                        } 
                                    }
                                ?>
                            </td>
                            <td>
                             <?php
                                        if($promocodeid != "0"){
                                            if(in_array($value["id"], $courses_array) || count($courses_array) == 0){
                                                $total_new = $guruModelguruOrder->getPromoDiscountCourse($price,$promocodeid);
                                                $promo_discount_percourse = $guruModelguruOrder->getPromoDiscountCourses($price, $promocodeid );
                                                $totall_discount += $promo_discount_percourse;
                                            }
                                            else{
                                                $total_new = $price;
                                            }   
                                        }
                                        else{
                                            $total_new = $price;
                                            if(@$discount_details["0"]["typediscount"] == "0"){
                                                $totall_discount = JText::_($character)." "."0";
                                            }
                                            else{
                                                $totall_discount = "0"."%";
                                            }
                                        }
                                            
                                        if($currencypos == 0){
                                            echo JText::_($character)." ".$guruHelper->displayPrice(round(((float)$total_new), 2));
                                        }
                                        else{
                                            echo $guruHelper->displayPrice(round(((float)$total_new), 2))." ".JText::_($character); 
                                        }
                                        @$total_final += $total_new;
                                    
                                     ?>
                             </td>
                        </tr>           
                        <?php
                                $i = 1-$i;
                                $k++;
                            }//foreach
                        }//if
                        ?>
                        </table>
                        
                        <table class="uk-table uk-margin-top uk-float-right uk-text-right wk-table wk-margin-top wk-float-right wk-text-right">
                            <tr>
                                <td  style="font-weight: bold; text-align:right;"><?php echo JText::_("GURU_TOTAL"); ?></td>
                                <td><?php
                                        if($currencypos == 0){
                                            echo JText::_($character)." ".$guruHelper->displayPrice($total_courses_price);
                                        }
                                        else{
                                            echo $guruHelper->displayPrice($total_courses_price)." ".JText::_($character);
                                        }
                                 ?></td>
                            </tr>
                            <tr>
                                <td style="font-weight: bold; text-align:right;"><?php echo JText::_("GURU_DISCOUNTPROMO"); ?></td>
                                <td>
                                <?php 
                                    if($currencypos == 0){
                                        echo JText::_($character)." ".$guruHelper->displayPrice($totall_discount);
                                    }
                                    else{
                                        echo $guruHelper->displayPrice($totall_discount)." ".JText::_($character);
                                    }
                                ?></td>
                            </tr>
                            <tr>
                                <td style="font-weight: bold; text-align:right;"><?php echo JText::_("GURU_FINAL_TOTAL"); ?></td>
                                <td>
                                    <?php
                                    if($currencypos == 0){
                                        echo JText::_($character)." ".$guruHelper->displayPrice($total_final);
                                    }
                                    else{
                                        echo $guruHelper->displayPrice($total_final)." ".JText::_($character);
                                    }
                                    ?>
                                </td>
                            </tr>
                      </table>
                                
                <input type="hidden" value="com_guru" name="option" />
                <input type="hidden" value="" name="task" />
                <input type="hidden" value="0" name="boxchecked" />
                <input type="hidden" value="guruOrders" name="controller" />
            </form>
    </div>
    
    <div class="uk-hidden-large uk-hidden-medium wk-hidden-large wk-hidden-medium">
        <?php
            if($this->show === TRUE){
        ?>
                <button type="button" onclick="javascript:printDiv('contentpane', 'mobile'); return false;" class="uk-button uk-button-primary wk-button wk-button-primary">
                    <img src="<?php echo JUri::base()."components/com_guru/images/print.png"; ?>" alt="Print" />
                    <?php echo JText::_("GURU_PRINT"); ?>
                </button>
        <?php
            }
        ?>
        
        
        <form method="post" name="adminForm" action="<?php echo JUri::root() ?>index.php">
            <div class="uk-alert uk-text-center uk-margin-top uk-margin-bottom wk-alert wk-text-center wk-margin-top wk-margin-bottom">
                <?php echo JText::_("GURU_INVOICE"); ?> #<?php echo $order["id"] ?>: <?php if($order["status"] == 'Pending'){echo JText::_("GURU_PENDING");} else{echo JText::_("GURU_O_PAID");} ?>
            </div>
            
            <div class="uk-grid wk-grid">
                <div class="uk-width-1-1 wk-width-1-1">
                    <span style="font-weight:bold;"><?php echo JText::_("GURU_ISSUED_BY").":"; ?></span>
                    <br />
                    <table  class="uk-table uk-table-striped wk-table wk-table-striped">
                        <tr>
                            <td>
                                <?php 
                                $ivoces_details = nl2br($invoice_issued_by);
                                $ivoces_details = stripslashes($ivoces_details);

                                echo $ivoces_details ;?>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
            
            <div class="uk-grid wk-grid">
                <div class="uk-width-1-1 wk-width-1-1">
                    <span align="left"><b><?php echo JText::_("GURU_MYORDERS_ORDER_DATE"); ?>
                    <?php 
                        $helper = new guruHelper();
                        echo $helper->getDate($order["order_date"]);
                    ?></b></span>
                </div>
            </div>
        
            <div class="uk-grid wk-grid">
                <div class="uk-width-1-1 wk-width-1-1">
                    <?php
                        if($this->show === TRUE){
                            $customer = $guruModelguruOrder->getCustomerDetails($order["userid"]);
                    ?>
                        <span style="font-weight:bold;"><?php echo JText::_("GURU_BILLED_TO").":"; ?></span>
                        <br />
                        <table class="uk-table uk-table-striped wk-table wk-table-striped">
                            <tr>
                                <td><?php echo JText::_("GURU_FIRS_NAME"); ?>:</td>
                                <td style="padding-left:20px;"><?php echo $customer["0"]["firstname"]; ?></td>
                            </tr>
                            <tr>
                                <td><?php echo JText::_("GURU_LAST_NAME"); ?>:</td>
                                <td style="padding-left:20px;"><?php echo $customer["0"]["lastname"]; ?></td>
                            </tr>
                            <tr>
                                <td><?php echo JText::_("GURU_COMPANY"); ?>:</td>
                                <td style="padding-left:20px;"><?php echo $customer["0"]["company"]; ?></td>
                            </tr>
                        </table>
                    <?php
                    }
                    ?>
                </div>
            </div>
            
            <div class="uk-grid wk-grid">
                <div class="uk-width-1-1 wk-width-1-1">
                    <table class="uk-table uk-table-striped wk-table wk-table-striped">
                        <tr>
                            <td class="g_cell_1"><b><?php echo JText::_("GURU_COURSE_NAME"); ?></b></td>
                            <?php
                        $ids = "0";
                        $id_price = array();
                        $id = array();
                        
                        if(trim($order["courses"]) != ""){
                            $temp1 = explode("|", trim($order["courses"]));
                            
                            if(is_array($temp1) && count($temp1) > 0){
                                foreach($temp1 as $key=>$value){
                                    $temp2 = explode("-", $value);                  
                                    if (count($temp2) > 2) {
                                        $id[] = trim($temp2["0"]);
                                        $id_price[trim($temp2["0"])]["price"] = trim($temp2["1"]);                      
                                        //$id_price[trim($temp2["0"])]["quantity"] = trim($temp2["2"]);
                                    }
                                }
                            }
                        }
                        
                        $courses = "";
                        if(isset($id) && count($id) > 0){
                            $courses = $guruModelguruOrder->getCourses(implode(",", $id));
                        }   
                        if(isset($courses) && is_array($courses) && count($courses) > 0){
                            foreach($courses as $key=>$value){
                                $price_p = $id_price[$value["id"]]["price"];
                                @$total_courses_price_p += (float)$price_p;
                                
                                if($promocodeid != "0"){
                                    if(in_array($value["id"], $courses_array) || count($courses_array) == 0){
                                        $total_new_p = $guruModelguruOrder->getPromoDiscountCourse($price_p,$promocodeid);
                                        $promo_discount_percourse_p = $guruModelguruOrder->getPromoDiscountCourses($price_p, $promocodeid );
                                        $totall_discount_p += $promo_discount_percourse_p;
                                    }
                                    else{
                                        $total_new_p = $price_p;
                                    }   
                                }
                                else{
                                    $total_new_p = $price_p;
                                    if(@$discount_details["0"]["typediscount"] == "0"){
                                        $totall_discount_p = JText::_($character)." "."0";
                                    }
                                    else{
                                        $totall_discount_p = "0"."%";
                                    }
                                }
                                    
                                
                                @$total_final_p += $total_new_p;
                                                        
                            ?>
                                        
                                <td><?php echo $value["name"]; ?></td>
                            <?php
                                }//foreach
                            }//if
                            ?>
                           </tr> 
                           <tr>
                                <td class="g_cell_2"><b><?php echo JText::_("GURU_PROGRAM_DETAILS_PRICE"); ?></b></td>
                            <?php    
                            if(isset($courses) && is_array($courses) && count($courses) > 0){
                                foreach($courses as $key=>$value){?>
                                        
                                <td>
                                <?php
                                    if($currencypos == 0){
                                        echo JText::_($character)." ".$guruHelper->displayPrice($id_price[$value["id"]]["price"]);
                                    }
                                    else{
                                        echo $guruHelper->displayPrice($id_price[$value["id"]]["price"])." ".JText::_($character);
                                    }
                                      ?>
                                </td>
                            <?php
                                }//foreach
                            }//if
                            ?>
                           </tr>
                           <tr>
                                <td><b><?php echo JText::_("GURU_TOTAL"); ?></b></td>   
                                <td>
                                <?php
                                    if($currencypos == 0){
                                            echo JText::_($character)." ".$guruHelper->displayPrice($total_courses_price_p);
                                        }
                                        else{
                                            echo $guruHelper->displayPrice($total_courses_price_p)." ".JText::_($character); 
                                        }
                                    ?>
                                </td>
                           </tr>
                           <tr>
                            <td><b><?php echo JText::_("GURU_DISCOUNTPROMO"); ?></b></td>   
                             <td>
                                <?php 
                                    if($currencypos == 0){
                                        echo JText::_($character)." ".$guruHelper->displayPrice($totall_discount_p);
                                    }
                                    else{
                                        echo $guruHelper->displayPrice($totall_discount_p)." ".JText::_($character); 
                                    }
                                ?>
                           </td>
                           </tr>
                           <tr>
                            <td><b><?php echo JText::_("GURU_FINAL_TOTAL"); ?></b></td> 
                             <td>
                                <?php 
                                    if($currencypos == 0){
                                        echo JText::_($character)." ".$guruHelper->displayPrice($total_final_p);
                                    }
                                    else{
                                        echo $guruHelper->displayPrice($total_final_p)." ".JText::_($character);
                                    }                       
                                ?>
                            </td>
                           </tr>
                            
                    </table>    
                </div>
            </div>
            <input type="hidden" value="com_guru" name="option" />
            <input type="hidden" value="" name="task" />
            <input type="hidden" value="0" name="boxchecked" />
            <input type="hidden" value="guruOrders" name="controller" />
        </form>
    </div>
<?php
}
?>