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
	$data_post = JFactory::getApplication()->input->post->getArray();
	$doc =JFactory::getDocument();
	//These scripts are already been included from the administrator\components\com_guru\guru.php file
//$doc->addScript('components/com_guru/js/jquery.DOMWindow.js');
	//$doc->addScript('components/com_guru/js/open_modal.js');
	
	$k = 0;
	$customers 	= $this->customers;
	$db = JFactory::getDBO();
	$sql = "Select datetype FROM #__guru_config where id=1 ";
	$db->setQuery($sql);
	$format_date = $db->loadColumn();
	$format = $format_date[0];

	$filter		= $this->filters;
	$n			= count($customers);
	?>
	<script language="javascript" type="text/javascript">        
		/*jQuery(function() {
			var w = 745,
				h = 430; 	
			set_modal('.modal2', w, h);		
		});*/
	</script>
	<form name="topform1" method="post" action="<?php echo JUri::root(true) ?>/administrator/index.php?option=com_guru&controller=guruCustomers">
		<table cellspacing="2" cellpadding="2" bgcolor="#ffffff" style="width: 100%;">
			<tbody>
				<tr>
					<td align="right">
						<?php
							$session = JFactory::getSession();
							$registry = $session->get('registry');
							
							$search_value = $filter->search;
						?>
                        
                        <input type="text" value="<?php echo $search_value; ?>" name="search"/>&nbsp;&nbsp;
						<input style="margin-bottom:10px!important;" class="btn btn-primary" type="submit" value="<?php echo JText::_("GURU_SEARCHTXT"); ?>" name="submit_search"/>
					</td>					
				</tr>
			</tbody>
		</table>
	</form>
<div id="myModal" class="modal-small modal hide">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    </div>
    <div class="modal-body">
    </div>
</div>
 <div class="container-fluid">
          <a data-toggle="modal" data-target="#myModal"  class="pull-right guru_video"  onclick="showContentVideo('index.php?option=com_guru&controller=guruAbout&task=vimeo&id=27181347&tmpl=component')" href="#">
                    <img src="<?php echo JURI::base(); ?>components/com_guru/images/icon_video.gif" class="video_img" />
                <?php echo JText::_("GURU_CUSTOMER_VIDEO"); ?>                  
          </a>
	</div>	
	<div class="clearfix"></div>
   
    <div class="well well-minimized">
		<?php echo JText::_("GURU_CUSTOMER_SETTINGS_DESCRIPTION"); ?>
	</div>
	
<form id="adminForm" action="<?php echo JUri::root() ?>administrator/index.php" name="adminForm" method="post">
<div id="editcell" >
<table class="table table-striped adminlist table-bordered">
	<thead>
			<tr>
            	<th width="2%">
					#
				</th>
				<th width="2%">
					<input type="checkbox" name="toggle" value="" onClick="Joomla.checkAll(this);" />
                    <span class="lbl"></span>
				</th>
				<th width="15%">
					<?php echo JText::_("GURU_AUTHOR_NAME"); ?>
				</th>
				<th width="10%">
					<?php echo JText::_("GURU_AUTHOR_USERNAME"); ?>
				</th>
				
				<th width="6%">
					<?php echo JText::_("GURU_AUTHOR_ENABLED"); ?>
				</th>
				<th width="11%">
					<?php echo JText::_("GURU_AUTHOR_GROUP"); ?>
				</th>			
				<th width="15%">
					<?php echo JText::_("GURU_AUTHOR_EMAIL"); ?>
				</th>
				<th width="11%">
					<?php echo JText::_("GURU_AUTHOR_LASTVISITDATE"); ?>
				</th>
				<th width="6%">
					<?php echo JText::_("GURU_AUTHOR_AUTHOR_ID"); ?>
				</th>			
			</tr>
        </thead>
<?php 
	for ($i = 0; $i < $n; $i++){
		$customers[$i] = (array)$customers[$i];
		$id = $customers[$i]["id"];
		$checked = JHTML::_('grid.id', $i, $id);
		$img 	= $customers[$i]['publish'] ? 'publish_x.png' : 'tick.png';	
		$task 	= $customers[$i]['publish'] ? 'unblock' : 'block';
		$alt 	= $customers[$i]['publish'] ? 'Enabled' : 'Blocked';
		$usrlink = JRoute::_("index.php?option=com_users&task=user.edit&id=".$id);
		$lms_link = "index.php?option=com_guru&controller=guruCustomers&task=edit&cid[]=".intval($id);
?>
		<tr class="<?php echo "row".$k; ?>">
        		<td>
					<?php echo $i+1+@$pageNav->limitstart;?>
				</td>
				<td>
					<?php echo $checked;?>
                    <span class="lbl"></span>
				</td>
				<td>
                	<?php
                    	if(trim($customers[$i]["firstname"]) == "" && trim($customers[$i]["lastname"]) == ""){
							$name = $customers[$i]["name"];
							$name_array = explode(" ", $name);
							$first_name = "";
							$last_name = "";
							
							if(count($name_array) == 1){
								$first_name = $name;
							}
							else{
								$last_name = $name_array[count($name_array) - 1];
								unset($name_array[count($name_array) - 1]);
								$first_name = implode(" ", $name_array);
							}
							
							$db = JFactory::getDbo();
							$sql = "update #__guru_customer set firstname='".trim($db->escape($first_name))."', lastname='".trim($db->escape($last_name))."' where id=".intval($customers[$i]["id"]);
							$db->setQuery($sql);
							$db->execute();
							
							$customers[$i]["firstname"] = $first_name;
							$customers[$i]["lastname"] = $last_name;
						}
					?>
                
					<a class="a_guru" href="<?php echo $lms_link; ?>">
						<?php echo $customers[$i]["firstname"]." ".$customers[$i]["lastname"]; ?>
					</a>
				</td>
				<td>
					<a class="a_guru" href="<?php echo $usrlink;?>"><?php echo $customers[$i]["username"]; ?></a>
				</td>
				
				<td align="center">
					<a href="javascript: void(0);" onClick="return listItemTask('cb<?php echo $i;?>','<?php echo $task;?>')">
						<img src="<?php echo JURI::base(); ?>components/com_guru/images/<?php echo $img;?>" width="12" height="12" border="0" alt="<?php echo $alt; ?>" />
					</a>
				</td>
				<td>
					<?php echo  $customers[$i]['usertype']; ?>
				</td>
				<td>
					<a class="a_guru" href="mailto:<?php echo  $customers[$i]['email']; ?>">
						<?php echo  $customers[$i]['email']; ?>
					</a>
				</td>
				<td>
					<?php 
						$date_string = date("".$format."", strtotime( $customers[$i]['lastvisitDate']));
			
					
						if( $customers[$i]['lastvisitDate'] != "0000-00-00 00:00:00"){
							echo $date_string;
						}
						else{
							echo JText::_("GURU_NEVER");
						}					
					?>
				</td>
				<td align="right">
					<?php echo $id;?>
				</td>
			</tr>
			<?php
			$k = 1 - $k;
		}
		?>
        	 <tr>
                <td colspan="11">
                    <div class="pagination pagination-toolbar">
                        <?php echo $this->pagination->getListFooter(); ?>
                    </div>
                    <div class="btn-group pull-left">
                        <label for="limit" class="element-invisible"><?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC');?></label>
                        <?php echo $this->pagination->getLimitBox(); ?>
                   </div>
                </td>
            </tr>
		</table>

</div>

	<input type="hidden" name="option" value="com_guru" />
	<input type="hidden" name="id" value="<?php if (isset($id)){echo $id;}?>"/>
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="controller" value="guruCustomers" />
    <input type="hidden" name="old_limit" value="<?php echo JFactory::getApplication()->input->get("limitstart"); ?>" />
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