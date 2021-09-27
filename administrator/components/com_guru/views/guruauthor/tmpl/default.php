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


$rows = $this->authorList;
$total = count($rows);
$pageNav = $this->pagination;

$doc =JFactory::getDocument();
$db = JFactory::getDBO();
$sql = "Select datetype, hour_format FROM #__guru_config where id=1 ";
$db->setQuery($sql);
$format_date = $db->loadAssocList();
$format = $format_date[0]["datetype"];
$hour_format = $format_date[0]["hour_format"];
if($hour_format == 12){
	$format = $format." A";
}
$listDirn = "asc";
$listOrder = "ordering";
$saveOrderingUrl = 'index.php?option=com_guru&controller=guruAuthor&task=saveOrderAjax&tmpl=component';
JHtml::_('sortablelist.sortable', 'articleList', 'adminForm', strtolower($listDirn), $saveOrderingUrl);
JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('dropdown.init');

$data_post = JFactory::getApplication()->input->post->getArray();
?>
<style>.alert-info { height:auto!important;}</style>

<script language="javascript" type="text/javascript">        
	jQuery(function() {
		var w = 745,
			h = 430; 	
		set_modal('.modal2', w, h);		
	});
</script>
<form name="topform1" method="post" action="<?php echo JUri::root(true) ?>/administrator/index.php?option=com_guru&controller=guruAuthor&task=list">
    <table cellspacing="2" cellpadding="2" bgcolor="#ffffff" style="width: 100%;">
        <tbody>
            <tr>
                <td align="left">
                	 <?php 
                    	$filter_status = JFactory::getApplication()->input->get("filter_status", "");
						echo JText::_("GURU_FILTER_TEACHER");
            		 ?>
                	<select name="filter_status" class="inputbox" onchange="document.topform1.submit();">
                    	<option value="-"  <?php if ($filter_status == "-"){ echo ' selected="selected" '; }?>><?php  echo JText::_("GURU_STATUS_SEARCH"); ?></option>
                        <option value="0"  <?php if ($filter_status == "0"){ echo ' selected="selected" '; }?>><?php echo JText::_("GURU_DECLINE"); ?></option>
                        <option value="1"  <?php if ($filter_status == "1"){ echo ' selected="selected" '; }?>><?php echo JText::_("GURU_APROVE"); ?></option>
                        <option value="2"  <?php if ($filter_status == "2"){ echo ' selected="selected" '; }?>><?php echo JText::_("GURU_AU_PENDING"); ?></option>
                    </select>
               </td>     
                <td align="right">
                    <?php
                    	$session = JFactory::getSession();
						$registry = $session->get('registry');
						
						$search_value = "";
						
						if(isset($data_post['search'])) {
							$search_value = $data_post['search'];
						}
						else{
							$search_value = $registry->get('search', "");
						}
					?>
                    
                    <input type="text" value="<?php echo $search_value; ?>" name="search"/> &nbsp;&nbsp;
                    <input class="btn  btn-primary" type="submit" value="<?php echo JText::_("GURU_SEARCHTXT"); ?>" name="submit_search"/>
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
          <a data-toggle="modal" data-target="#myModal" class="pull-right guru_video" onclick="showContentVideo('index.php?option=com_guru&controller=guruAbout&task=vimeo&id=30058405&tmpl=component')" href="#">
                    <img src="<?php echo JURI::base(); ?>components/com_guru/images/icon_video.gif" class="video_img" />
                <?php echo JText::_("GURU_TEACHER_VIDEO"); ?>                  
          </a>
	</div>	
	<div class="clearfix"></div>
    <div class="well well-minimized">
		<?php echo JText::_("GURU_TEACHER_SETTINGS_DESCRIPTION"); ?>
	</div>

<form action="<?php echo JUri::root() ?>administrator/index.php" id="adminForm" method="post" name="adminForm">	
	<table class="table table-striped adminlist table-bordered" id="articleList">
    	<thead>
			<tr>
            	<th width="1%">
					<?php echo JHtml::_('grid.sort', '<i class="icon-menu-2"></i>', 'a.ordering', $listDirn, $listOrder, null, 'asc', 'JGRID_HEADING_ORDERING'); ?>
                </th>
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
					<?php echo JText::_("GURU_AUTHOR_APPROVED"); ?>
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
		$k = 0;		
		$i = 0;
		$n = count($rows);
		foreach($rows as $key=>$row) {
			$row = (array)$row;
			if($row['enabled'] == 0){
				$img = '<img src="'.JURI::base().'components/com_guru/images/publish_x.png" width="12" height="12" border="0"/>';
			}
			elseif($row['enabled'] == 1){
				$img = '<img src="'.JURI::base().'components/com_guru/images/tick.png" width="12" height="12" border="0"/>';
			}
			elseif($row['enabled'] == 2){
				$img = '<i class="icon-clock"></i>';
			}
			$task 	= $row['enabled'] ? 'block':'unblock';
			$link 	= 'index.php?option=com_guru&controller=guruAuthor&task=edit&id='.$row['user_id'];
		?>
			<tr class="<?php echo "row".$k; ?>">
            	<td>
                    <span class="sortable-handler active" style="cursor: move;">
                        <i class="icon-menu"></i>
                    </span>
                    <input type="text" class="width-20 text-area-order " value="<?php echo $row["ordering"]; ?>" size="5" name="order[]" style="display:none;">
                </td>    
				<td>
					<?php echo $i+1+$pageNav->limitstart;?>
				</td>
				<td>
					<?php echo JHTML::_('grid.id', $i, $row['user_id'] ); ?>
                    <span class="lbl"></span>
				</td>
				<td>
					<a class="a_guru" href="<?php echo $link; ?>">
						<?php echo $row['name']; ?>
					</a>
				</td>
				<td>
					<?php echo $row['username']; ?>
				</td>
				
				<td align="center">
					<?php echo $img;?>
				</td>
				<td>
					<?php echo str_replace(",", ", ", $row['usertype']); ?>
				</td>
				<td>
					<a class="a_guru" href="mailto:<?php echo $row['email']; ?>">
						<?php echo $row['email']; ?>
					</a>
				</td>
				<td>
					<?php 
						
						$date_string = date("".$format."", strtotime($row['lastvisitDate']));
			
					
						if($row['lastvisitDate'] != "0000-00-00 00:00:00"){
							echo $date_string;
						}
						else{
							echo JText::_("GURU_NEVER");
						}					
					?>
				</td>
				<td align="right">
					<?php echo $row['user_id']; ?>
				</td>
			</tr>
			<?php
			$k = 1 - $k;
			$i ++;
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
        <input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
        <input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
        <?php echo JHtml::_('form.token'); ?>
		<input type="hidden" name="option" value="com_guru" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="controller" value="guruAuthor" />
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