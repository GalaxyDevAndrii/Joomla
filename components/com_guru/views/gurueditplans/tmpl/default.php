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

$plans = $this->plans;
$guruModelguruEditplans = new guruModelguruEditplans();
$config = $guruModelguruEditplans->getConfigs();
$currency = $config["0"]["currency"];
$character = JTExt::_("GURU_CURRENCY_".$currency); 
$currencypos = $config['0']['currencypos'];
$course_id = intval(JFactory::getApplication()->input->get("course_id", "0"));
$action = JFactory::getApplication()->input->get("action", "");
$my = JFactory::getUser();
$user_id = $my->id;
$db = JFactory::getDBO();

$document = JFactory::getDocument();

jimport('joomla.language.helper');
$lang_value = JLanguageHelper::detectLanguage();		
$lang = new JLanguage();
$lang->load('com_guru',JPATH_BASE,$lang_value);

$Itemid = JFactory::getApplication()->input->get("Itemid", "0", "raw");
?>

<script type="text/javascript" language="javascript">
    document.body.className = document.body.className.replace("modal", "");
</script>

<link rel="stylesheet" href="<?php echo JURI::root().'components/com_guru/css/uikit.almost-flat.min.css'; ?>"/>

<style>
	.modal-no-plans-msg{
		margin: 15px !important;
	}

    #g_prices_modal tr:last-child td {
        border: none;
    }

    #g_prices_modal.uk-panel {
        padding: 0;
        overflow: hidden;
    }
    
    /*body.contentpane,*/ .guru-lesson-loading {
        background-color: #FFF !important;
    }
</style>

<div id="g_content" class="gru-content">
	<?php
    if($action == ""){
    ?>
    	<?php
        	if(!isset($plans) || count($plans) == 0){
				echo '<div class="uk-alert uk-hidden-small wk-alert wk-hidden-small modal-no-plans-msg">'.JText::_("GURU_NO_PLANS_FOR_GROUP").'</div>';
			}
			else{
				$helper = new guruHelper();
				$itemid_seo = $helper->getSeoItemid();
				$itemid_seo = @$itemid_seo["gurueditplans"];
				
				if(intval($itemid_seo) > 0){
					$Itemid = intval($itemid_seo);
				}
			
		?>
                <form action="<?php echo JRoute::_('index.php?option=com_guru&view=guruEditplans&task=buy&Itemid='.intval($Itemid)); ?>" name="adminForm" method="post" style="padding:30px; font-family:arial;">
                    <div id="g_prices_modal_text"  class="g_prices_modal_text">
                        <b>
                        <?php 
                            echo $config["0"]["content_selling"];
                        ?>
                        </b>
                    </div>

                    <hr class="uk-article-divider wk-article-divider">
                            
                    <table id="g_prices_modal" class="uk-panel uk-panel-box uk-table uk-table-hover uk-table-striped uk-table-condensed wk-panel wk-panel-box wk-table wk-table-hover wk-table-striped wk-table-condensed">
                        <?php
                            foreach($plans as $key=>$value){ 
                        ?>
                            <tr>
                                <td width="1%" style="padding: 10px;">
                                    <input type="radio" name="course_plans" value="<?php echo $value["price"] ?>" <?php if($value["default"] == "1"){echo 'checked="checked"';} ?>/> 
                                </td>
                                <td width="10%" nowrap="nowrap" style="padding: 10px;">
                                    <?php echo $value["name"]; ?>
                                </td>
                                <td nowrap="nowrap" style="padding: 10px;">
                                    <span class="uk-badge uk-badge-success uk-badge-notification wk-badge wk-badge-success wk-badge-notification">
                                    <?php 
                                        echo $currencypos 
                                            ? $guruHelper->displayPrice($value["price"]) . ' ' . $character
                                            : $character . ' ' . $guruHelper->displayPrice($value["price"]);
                                    ?>
                                    </span>
                                </td>
                            </tr>    
                        <?php
                            }
                        ?>
                    </table>
                        
                    <input type="hidden" name="option" value="com_guru" />
                    <input type="hidden" name="controller" value="guruEditplans" />
                    <input type="hidden" name="view" value="guruEditplans" />
                    <input type="hidden" name="tmpl" value="component" />
                    <input type="hidden" name="task" value="buy" />
                    <input type="hidden" name="course_id" value="<?php echo intval($course_id); ?>" />
                    <input type="hidden" name="Itemid" value="<?php echo intval($Itemid); ?>" />
                    
                    <input type="submit" name="continue" class="uk-button uk-button-primary wk-button wk-button-primary" value="<?php echo JText::_("GURU_CONTINUE"); ?>" />
                </form>
    <?php
			}
    }
    elseif($action == "renew"){ // for renew
        $sql = "select expired_date from #__guru_buy_courses where userid=".intval($user_id)." and course_id=".intval($course_id);
        $db->setQuery($sql);
        $db->execute();
        $expired_date_string = $db->loadResult();
        $expired_date_int = strtotime($expired_date_string);
        $jnow = new JDate('now');
        $current_date_string = $jnow->toSQL(true);
        $current_date_int = strtotime($current_date_string);
        
        $sql = "select pp.price, pp.default, s.name from #__guru_program_renewals pp, #__guru_subplan s where s.id = pp.plan_id and pp.product_id=".intval($course_id)." order by s.ordering asc";
        $db->setQuery($sql);
        $db->execute();
        $plans = $db->loadAssocList();
        
        if(count($plans) == 0){ // no plans for renew
            $plans = $this->plans; // from buy plans
        }
		
		if(!isset($plans) || count($plans) == 0){
			echo '<div class="uk-alert uk-hidden-small wk-alert wk-hidden-small modal-no-plans-msg">'.JText::_("GURU_NO_PLANS_FOR_GROUP").'</div>';
		}
		else{
    ?>
        
                    <?php
                    $difference_int = get_time_difference($current_date_int, $expired_date_int);
                    if($difference_int){ //not expired
    ?>
                    <form action="<?php echo JUri::root(true) ?>/index.php?option=com_guru&view=guruEditplans&task=renew" name="adminForm" method="post" style="padding:10px;">
                        <div id="g_prices_modal_text"  class="g_prices_modal_text">
                            <b>
                            <?php 
                                echo $config["0"]["content_selling"];
                            ?>
                            </b>
                        </div>
                        
                        
                    <table id="g_prices_modal" class="uk-table wk-table">
                        <?php
                            foreach($plans as $key=>$value){
                        ?>
                            <tr>
                                <td width="1%">
                                    <input type="radio" name="course_plans" value="<?php echo $value["price"] ?>" <?php if($value["default"] == "1"){echo 'checked="checked"';} ?>/> 
                                </td>
                                <td style="font-family: Georgia;" width="10%" nowrap="nowrap">
                                    <?php echo $value["name"]; ?>
                                </td>
                                <td nowrap="nowrap">
                                    <?php
                                        echo $currencypos 
                                        ? $guruHelper->displayPrice($value["price"]) . ' ' . $character
                                        : $character . ' ' . $guruHelper->displayPrice($value["price"]);
                                    ?>
                                </td>
                            </tr>    
                        <?php
                            }
                        ?>
                    </table>
                    
                    <input type="hidden" name="option" value="com_guru" />
                    <input type="hidden" name="controller" value="guruEditplans" />
                    <input type="hidden" name="view" value="guruEditplans" />
                    <input type="hidden" name="tmpl" value="component" />
                    <input type="hidden" name="task" value="renew" />
                    <input type="hidden" name="course_id" value="<?php echo intval($course_id); ?>" />
                    <table>
                        <tr>
                            <td>
    <?php
                        echo JText::_("GURU_STILL_HAVE")." ".$difference_int["days"]." ".JText::_("GURU_AVAILABLE_COURSE")." ".'<a href="#" onclick="document.adminForm.submit();">'.JText::_("GURU_YES").'</a>'." ".JText::_("GURU_ADD_TIME")." ".'<a href="#" onclick="document.adminForm.task.value=\'course\'; document.adminForm.submit();">'.JText::_("GURU_NO").'</a>'." ".JText::_("GURU_GO_TO_COURSE_PAGE");
    ?>
                            </td>
                        </tr>
                    </table>
                    </form>
    <?php					
                    }
                    else{//expired, buy now
    ?>
                    <form action="<?php echo JUri::root(true) ?>/index.php?option=com_guru&view=guruEditplans&task=renew" name="adminForm" method="post" style="padding:10px;">
                         <div id="g_prices_modal_text"  class="g_prices_modal_text">
                            <b>
                            <?php 
                                echo $config["0"]["content_selling"];
                            ?>
                            </b>
                        </div>
                        <table id="g_prices_modal" class="uk-table wk-table">
                            <?php
                                foreach($plans as $key=>$value){
                            ?>
                                <tr>
                                    <td width="1%">
                                        <input type="radio" name="course_plans" value="<?php echo $value["price"] ?>" <?php if($value["default"] == "1"){echo 'checked="checked"';} ?>/> 
                                    </td>
                                    <td style="font-family: Georgia;" width="10%" nowrap="nowrap">
                                        <?php echo $value["name"]; ?>
                                    </td>
                                    <td nowrap="nowrap">
                                        <?php
                                            echo $currencypos 
                                            ? $guruHelper->displayPrice($value["price"]) . ' ' . $character
                                            : $character . ' ' . $guruHelper->displayPrice($value["price"]);
                                        ?>
                                    </td>
                                </tr>    
                            <?php
                                }
                            ?>
                        </table>
                        
                        <input type="hidden" name="option" value="com_guru" />
                        <input type="hidden" name="controller" value="guruEditplans" />
                        <input type="hidden" name="view" value="guruEditplans" />
                        <input type="hidden" name="tmpl" value="component" />
                        <input type="hidden" name="task" value="renew" />
                        <input type="hidden" name="course_id" value="<?php echo intval($course_id); ?>" />
                    
                        <input type="submit" class="uk-button uk-button-success wk-button wk-button-success" name="continue" value="<?php echo JText::_("GURU_CONTINUE_ARROW"); ?>" />
                    </form>
                        
    <?php					
                }
			}
    }
    
    function get_time_difference($start, $end){
        $uts['start'] = $start;
        $uts['end'] = $end;
        if( $uts['start'] !== -1 && $uts['end'] !== -1){
            if($uts['end'] >= $uts['start']){
                $diff = $uts['end'] - $uts['start'];
                if($days=intval((floor($diff/86400)))){
                    $diff = $diff % 86400;
                }
                    
                if($hours=intval((floor($diff/3600)))){
                    $diff = $diff % 3600;
                }	
                
                if($minutes=intval((floor($diff/60)))){
                    $diff = $diff % 60;
                }	
                $diff = intval($diff);
                return( array('days'=>$days, 'hours'=>$hours, 'minutes'=>$minutes, 'seconds'=>$diff));
            }
            else{
                return false;
            }
        }
        return false;
    }
    
    ?>
</div>
