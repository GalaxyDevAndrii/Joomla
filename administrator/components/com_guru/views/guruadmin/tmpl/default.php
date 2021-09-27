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
$total_a = @$this->total_a;
$total_b = @$this->total_b;
$total_c = @$this->total_c;
$total_o = @$this->total_o;
$total_r = @$this->total_r;

JHtml::_('behavior.framework',true);
$document = JFactory::getDocument();

$document->addStyleSheet(JURI::root()."administrator/components/com_guru/css/g_graph.css");

$revenue = $this->getRevenue();
$orders = $this->getOrders();
$courses = $this->getCourses();
$teachers = $this->getTeachers();
$students = $this->getStudents();
$currencypos = $this->getCurrencyPos();
$currency = $this->getCurrency();

$db = JFactory::getDBO();

?>

<div class="row-flow">
<!-- start dashboard -->
	<div class="span8">
		<div class="span12">
			<div id="g_basicInfo"  class="span12 g_outer_shell">
                <div class="g_middle_shell">
                    <div class="g_inner_shell">
                        <div class="span3 dash-cell">
                            <div class="infobox infobox-blue infobox-dark">
                            	 <div class="infobox-icon">
                                  	<i class="icon-star"></i>
                                  </div>
                                  <div class="infobox-data">
									<?php
                                    if(isset($revenue) && count($revenue) > 0){
                                        echo '<span class="revenue">';
                                        foreach($revenue as $key=>$value){
                                            if($currencypos == 0){
                                                echo JText::_("GURU_CURRENCY_".$key)." ".number_format($value,2);
                                            }
                                            else{
                                                echo number_format($value,2)." ".JText::_("GURU_CURRENCY_".$key);
                                            }
                                            echo "<br/>";
                                        }
                                        echo '</span>';
										echo '</div><div class="infobox-footer">
										 	<div class="infobox-content">'.JText::_("GURU_REVENUE").'</div>
										  </div>';
                                    }
                                    else{
										echo '<span class="total-orders">';
                                        if($currencypos == 0){
											echo JText::_("GURU_CURRENCY_".$currency)." 0";
										}
										else{
											echo "0 ".JText::_("GURU_CURRENCY_".$currency);
										}
                                        echo '</span>';
										echo '</div><div class="infobox-footer">
										 	<div class="infobox-content">'.JText::_("GURU_REVENUE").'</div>
										  </div>';
                                    }
                                    ?>
                            </div>
                        </div><!--//end box-1 -->
                        <div class="span3 dash-cell">
                            <div class="infobox infobox-green infobox-dark">
                            	 <div class="infobox-icon">
                                  	<i class="icon-cart"></i>
                                  </div>
                                  <div class="infobox-data">
									<?php
                                    echo '<span class="total-orders">
                                            <a href="index.php?option=com_guru&controller=guruOrders">'.intval($orders["0"]).'</a>
                                          </span>';
										  echo '</div><div class="infobox-footer">
										 	<div class="infobox-content"><a href="index.php?option=com_guru&controller=guruOrders">'.JText::_("GURU_TREEORDERS").'</a></div>
										  </div>';
                                    ?>
                            </div>
                        </div><!--//end box-2 -->
                        <div class="span3 dash-cell">
                            <div class="infobox infobox-orange infobox-dark">
                            	<div class="infobox-icon">
                                  	<i class="icon-file"></i>
                                  </div>
                                  <div class="infobox-data">
									<?php
                                    echo '<span class="total-orders">
                                            <a href="index.php?option=com_guru&controller=guruPrograms">'.intval($courses["0"]).'</a>
                                          </span>';
										   echo '</div><div class="infobox-footer">
										 	<div class="infobox-content"> <a href="index.php?option=com_guru&controller=guruPrograms">'.JText::_("GURU_TREEPROGRAMS").'</a></div>
										  </div>';
                                    ?>
                           		 </div>
                        </div><!--//end box-3 -->
                        <div class="span3 dash-cell">
                            <div class="infobox infobox-red infobox-dark">
                            	<div class="infobox-icon">
                                  	<i class="icon-user"></i>
                                  </div>
                                  <div class="infobox-data">
									<?php
                                    echo '<span class="total-orders">
                                            <a href="index.php?option=com_guru&controller=guruAuthor&task=list&filter_status=1&active=1">'.intval($teachers["0"]).'</a>
                                          </span>';
										   echo '</div><div class="infobox-footer">
										 	<div class="infobox-content"> <a href="index.php?option=com_guru&controller=guruAuthor&task=list">'.JText::_("GURU_TEACHERS").'</a></div>
										  </div>';
                                    ?>
                            </div>
                        </div><!--//end box-4 -->
                        <div class="span3 dash-cell">
                            <div class="infobox infobox-pink infobox-dark">
                            	<div class="infobox-icon">
                                  	<i class="js-icon-group"></i>
                                  </div>
                                  <div class="infobox-data">
									<?php
                                    echo '<span class="total-orders">
                                            <a href="index.php?option=com_guru&controller=guruCustomers">'.intval($students["0"]).'</a>
                                          </span>';
									 echo '</div><div class="infobox-footer">
										 	<div class="infobox-content"> <a href="index.php?option=com_guru&controller=guruCustomers">'.JText::_("GURU_COU_STUDENTS").'</a></div>
										  </div>';	  
                                    ?>
                            </div>
                        </div><!--//end box-5 -->
                    </div><!--// end g_inner_shell-->
                </div><!--// end g_middle_shell-->
            </div><!--// end basic information, g_outer_shell-->
		</div>
        <div class="clearfix"></div>
         <div id="g_daily_chart" class="row-flow">
            <div class="span12">
            	<div id="content">
                    <div class="demo-container">
                        <div id="placeholder" class="demo-placeholder"></div>
                    </div>
                </div>    
            </div>
        </div>
</div>		
      
 <div class="span4">       
<?php 
	$notification = $this->showNotification();
	if ($notification == 0){
			if($this->status_message && (trim($this->status_message) != '')){
			// Alkis - hid notification pane because it mentioned Guru and Jomsocial
			// $this->status_message = '<div class="well well-small alert alert-block alert-success">'.$this->status_message.'</div>' ;
		}
		 // echo'<div class="span12">'.$this->status_message.'</div>';
	}	
	?>
    <!--
    <div class="clearfix"></div>
    <div class="row-flow">
        <div class="span12">
            <div id="ijoomla_news_tabs">
            </div>
        </div>
    </div>
    -->
</div>
<div class="clearfix"></div>

<?php
	$recent_orders = $this->getRecentOrders();
	$avg_stud = $this->getAvgCourses();
	$certificategiven = $this->getGivenCertificate();
	$certificatesawarded = $this->getAwardedCertificates();
	$bsc = $this->bestSellingCourse();
?>
<div class="row-fluid">
	<div class="span12 statistic-zone">
    	<div class="span3">
        	<div class="g_header_table">
				<?php
                    echo JText::_("GURU_RECENT_ORDERS");
                ?>
            </div>
            <?php
            	if(isset($recent_orders) && count($recent_orders) > 0){
			?>
            	<table class="table table-striped table-bordered">
            <?php
					foreach($recent_orders as $key=>$order){
						$course = explode("-", $order["courses"]);
						$currency = $order["currency"];
						$date = $order["order_date"];
						
						if($order["amount_paid"] != -1){
							$course["1"] = $order["amount_paid"];
						}
						
						$sql = "Select datetype FROM #__guru_config where id=1 ";
						$format_date = $db->setQuery($sql)->loadColumn();
						$format_date = $format_date[0];
						
						$date = date("".$format_date."", strtotime($date));
						
						$course_name = "SELECT name FROM #__guru_program WHERE id=".intval($course[0]);
						$course_name = $db->setQuery($course_name)->loadColumn();

						@$plan = "SELECT name FROM #__guru_subplan WHERE id=".intval($course[2]);
						$plan = $db->setQuery($plan)->loadColumn();	
									
						$user_name = "SELECT name FROM #__users WHERE id=".intval($order["userid"]);
						$user_name = $db->setQuery($user_name)->loadColumn();
						if($currencypos == 0){
							@$display1 = JText::_("GURU_CURRENCY_".$currency);
						}
						else{
							@$display2 = JText::_("GURU_CURRENCY_".$currency);
						}
						
						
						echo '<tr>
								<td>
							  		<a href="index.php?option=com_guru&controller=guruCustomers&task=edit&cid[]='.intval($order["userid"]).'">'.$user_name[0].'</a>
									 '.JText::_("GURU_BOUGHT_COURSE").' <a href="index.php?option=com_guru&controller=guruPrograms&task=edit&cid[]='.intval(@$course["0"]).'">'.@$course_name["0"].'</a>
									 <br />
									 '.JText::_("GURU_FOR").' '.@$display1.' '.$course[1].' '.@$display2.', '.JText::_("GURU_SUBS_PLAN").':'.' '.' <a href="index.php?option=com_guru&controller=guruSubplan&task=edit&cid[]='.intval(@$course[2]).'">'.@$plan["0"].'</a>'.' '.JText::_("GURU_ON").' '.$date.'
								</td>
						      </tr>';
					}
			?>
            	</table>
            <?php
				}
				else{
					echo '<table class="table table-striped table-bordered"> 
						  	<tr>
								<td>
							  		'.JText::_("GURU_NO_RECENT_ORDERS").' 
								
								</td>
						      </tr>
						 </table>';
				}
			?>
        </div>
        <div class="span3">
        	<div  class="g_header_table">
				<?php
                    echo JText::_("GURU_GENERAL_STATS");
                ?>
            </div>
            <table class="table table-striped table-bordered">
            	<tr>
                	<td>
                    	<?php echo JText::_("GURU_ORDERS_TO_DATE"); ?>
                    </td>
                    <td class="pagination-centered">
                    	<?php
                        	if(intval($orders) > 0){
								echo '<a href="index.php?option=com_guru&controller=guruOrders">'.intval($orders["0"]).'</a>';
							}
							else{
								echo '0';
							}
						?>
                    </td>
                </tr>
                <tr>
                	<td>
                    	<?php echo JText::_("GURU_REVENUE_TO_DATE"); ?>
                    </td>
                    <td class="pagination-centered">
						<?php
                        	if(isset($revenue) && count($revenue) > 0){
								echo '<span class="revenue">';
								foreach($revenue as $key=>$value){
									if($currencypos == 0){
										echo JText::_("GURU_CURRENCY_".$key)." ".number_format($value,2);
									}
									else{
										echo number_format($value,2)." ".JText::_("GURU_CURRENCY_".$key);
									}
									echo "<br/>";
								}
								echo '</span>';
							}
							else{
								echo '<span class="revenue">';
								echo 	"0<br/>";
								echo '</span>';
							}
						?>
					</td>
                </tr>
                <tr>
                	<td>
                    	<?php echo JText::_("GURU_TEACHERS"); ?>
                    </td>
                    <td class="pagination-centered">
                    	<?php
							if(intval($teachers["0"]) > 0){
                        		echo '<a href="index.php?option=com_guru&controller=guruAuthor&task=list&filter_status=1&active=1">'.intval($teachers["0"]).'</a>';
							}
							else{
								echo '0';
							}
						?>
                    </td>
                </tr>
                <tr>
                	<td>
                    	<?php echo JText::_("GURU_TREEPROGRAMS"); ?>
                    </td>
                    <td class="pagination-centered">
                    	<?php
							if(intval($courses["0"]) > 0){
                        		echo '<a href="index.php?option=com_guru&controller=guruPrograms">'.intval($courses["0"]).'</a>';
							}
							else{
								echo '0';
							}
						?>
                    </td>
                </tr>
                <tr>
                	<td>
                    	<?php echo JText::_("GURU_COU_STUDENTS"); ?>
                    </td>
                    <td class="pagination-centered">
                    	<?php
							if(intval($students["0"]) > 0){
                        		echo '<a href="index.php?option=com_guru&controller=guruCustomers">'.intval($students["0"]).'</a>';
							}
							else{
								echo '0';
							}
						?>
                    </td>
                </tr>
                <tr>
                	<td>
                    	<?php echo JText::_("GURU_AVG_STUD_COURSES"); ?>
                    </td>
                    <td class="pagination-centered">
                    	<?php
							if(intval($avg_stud) > 0){
                        		echo intval($avg_stud);
							}
							else{
								echo '0';
							}
						?>
                    </td>
                </tr>
                <tr>
                	<td>
                    	<?php echo JText::_("GURU_CERT_GIVEN"); ?>
                    </td>
                    <td class="pagination-centered">
                    	<?php
							if(intval($certificatesawarded) > 0){
                        		echo intval($certificatesawarded);
							}
							else{
								echo '0';
							}
						?>
                    </td>
                </tr>
            </table>
        </div>
        <div class="span3">
        	<div  class="g_header_table">
				<?php
                    echo JText::_("GURU_BEST_SELLING_CBYO");
                ?>
            </div>
            <?php
                if(isset($bsc) && count($bsc) > 0){
			?>
            		<table class="table table-striped table-bordered">
			<?php
                    foreach($bsc as $key=>$value){
						$course_name = "SELECT name FROM #__guru_program WHERE id=".intval($value["idc"]);
						$db->setQuery($course_name);
						$db->execute();
						$course_name = $db->loadColumn();
						
						$author_id = "SELECT author FROM #__guru_program WHERE id=".intval($value["idc"]);
						$db->setQuery($author_id);
						$db->execute();
						$author_id = $db->loadColumn();

						if(!isset($author_id["0"])){
							continue;
						}
						
						$author_id_array = explode("|", $author_id["0"]);
						$author_id_array = array_filter($author_id_array);
						$list_authors = array();
						
						foreach($author_id_array as $author_key=>$author_id){
							$sql = "SELECT name FROM #__users WHERE id=".intval($author_id);
							$db->setQuery($sql);
							$db->execute();
							$author_name = $db->loadColumn();
							$author_name = @$author_name["0"];
							$list_authors[] = '<a href="index.php?option=com_guru&controller=guruAuthor&task=edit&id='.intval($author_id).'">'.$author_name.'</a>';
						}
						
						$list_authors = implode(", ", $list_authors);
						
						echo '<tr>
								<td>
							  		<a href="index.php?option=com_guru&controller=guruPrograms&task=edit&cid[]='.intval($value["idc"]).'">'.@$course_name["0"].'</a>
									 '.JText::_("GURU_BY").' '.$list_authors.'
								</td>
								<td class="pagination-centered">'.$value["frequency"].'
                                    
                           		</td>
						      </tr>';
					}
			?>
                    </table>
            <?php
				}
				else{
					echo '<table class="table table-striped table-bordered"> 
						  	<tr>
								<td>
							  		'.JText::_("GURU_NO_ORDERS_C").' 
								
								</td>
						      </tr>
						 </table>';
				}
			?>
        </div>
        <div class="span3">
        	<div  class="g_header_table">
				<?php
                    echo JText::_("GURU_NEWEST_CERT");
                ?>
            </div>
            <?php
                if(isset($certificategiven) && count($certificategiven) > 0){
			?>
            		<table class="table table-striped table-bordered">
			<?php
                    foreach($certificategiven as $key=>$certificate){

						$course_namec = "SELECT name FROM #__guru_program WHERE id=".intval($certificate["course_id"]);
						$course_namec = $db->setQuery($course_namec)->loadColumn();
						
						$user_namec = "SELECT name FROM #__users WHERE id=".intval($certificate["user_id"]);
						$user_namec = $db->setQuery($user_namec)->loadColumn();
						
            			echo '<tr>
								<td>
							  		<a href="index.php?option=com_guru&controller=guruCustomers&task=edit&cid[]='.intval($certificate["user_id"]).'">'.$user_namec[0].'</a>
									 '.JText::_("GURU_FOR").' <a href="index.php?option=com_guru&controller=guruPrograms&task=edit&cid[]='.intval($certificate["course_id"]).'">'.@$course_namec["0"].'</a>
								
								</td>
						      </tr>';
					}
			?>
                    </table>
            <?php
				}
				else{
					echo '<table class="table table-striped table-bordered"> 
						  	<tr>
								<td>
							  		'.JText::_("GURU_NO_CERTIFICATES_TOSHOW").' 
								
								</td>
						      </tr>
						 </table>';
				}
			?>
        </div>
    </div>
</div>