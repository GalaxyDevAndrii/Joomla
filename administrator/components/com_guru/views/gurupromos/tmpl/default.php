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
	//These scripts are already been included from the administrator\components\com_guru\guru.php file
//$doc->addScript('components/com_guru/js/jquery.DOMWindow.js');

	JHtml::_('bootstrap.tooltip');
	JHtml::_('behavior.multiselect');
	JHtml::_('dropdown.init');
	$k = 0;
	$promos = $this->promos;
	$n = count($promos);
	$config = guruAdminModelguruPromos::getConfig();
	$data_post = JFactory::getApplication()->input->post->getArray();
?>

<div id="myModal" class="modal-small modal hide">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true" onclick="javascript:closeModal();">&times;</button>
	</div>
	<div class="modal-body">
    	<iframe src="" id="iframe-modal"></iframe>
    </div>
</div>

<script language="javascript">
	var first = false;
	function showPromoCourses(href){
		first = true;
		document.getElementById('iframe-modal').src = href;
	}
	
	function closeModal(){
		jQuery('#myModal .modal-body iframe').attr('src', '');
	}
	
	jQuery('#myModal').on('hide', function () {
		jQuery('#myModal .modal-body iframe').attr('src', '');
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

<form name="topform1" method="post" action="<?php echo JUri::root(true) ?>/administrator/index.php?option=com_guru&controller=guruPromos">
		<table cellspacing="2" cellpadding="2" bgcolor="#ffffff" style="width: 100%;">
			<tbody>
				<tr>
					<td align="left">
						<?php
                        	$session = JFactory::getSession();
							$registry = $session->get('registry');
							$search_value = $registry->get('search_promos', "");
							
							if(isset($data_post['search_promos'])) {
								$search_value = $data_post['search_promos'];
							}
						?>
                        <input type="text" value="<?php echo $search_value; ?>" name="search_promos"/>&nbsp;&nbsp;
						<input class="btn btn-primary" type="submit" value="<?php echo JText::_("GURU_SEARCHTXT"); ?>" name="submit_src"/>
					</td>
					<td align="left">
							<select onchange="document.topform1.submit()" name="promos_publ_status">
							<?php 
								$session = JFactory::getSession();
								$registry = $session->get('registry');
								$promos_publ_status = $registry->get('promos_publ_status', "");
								
								if(isset($promos_publ_status) && trim($promos_publ_status) != ""){
									$pb = $promos_publ_status;
								}
								
								if(isset($data_post['promos_publ_status'])){
									$pb = $data_post['promos_publ_status'];
									$registry->set('promos_publ_status', $pb);
								}
								if(!isset($pb)) {$pb=NULL;}
							?>
							<option <?php if($pb=='YN') { echo "selected='selected'";} ?> value="YN"><?php echo JText::_("GURU_ALLYN"); ?></option>
							<option <?php if($pb=='Y') { echo "selected='selected'";} ?> value="Y"><?php echo JText::_("GURU_PUBLISHED"); ?></option>
							<option <?php if($pb=='N') { echo "selected='selected'";} ?> value="N"><?php echo JText::_("GURU_UNPUBLISHED"); ?></option>
							</select>	
					</td>
					
					<td>
						<select onchange="document.topform1.submit()" name="promo_status">
						<?php 
							$session = JFactory::getSession();
							$registry = $session->get('registry');
							$promo_status = $registry->get('promo_status', "");
						
							if(isset($promo_status) && trim($promo_status) != ""){
								$promo_sts = $promo_status;
							}
							
							if(isset($data_post['promo_status'])){
								$promo_sts=$data_post['promo_status'];
								$registry->get('promo_status', $promo_sts);
							}
							
							if(!isset($promo_sts)){
								$promo_sts = NULL;
							}
						?>
						<option <?php if($promo_sts=='YN') { echo "selected='selected'";} ?> value="YN"><?php echo JText::_("GURU_ALL_ACTIVE_STATUS"); ?></option>
						<option <?php if($promo_sts=='Y') { echo "selected='selected'";} ?> value="Y"><?php echo JText::_("GURU_PROMOACTIVE"); ?></option>
						<option <?php if($promo_sts=='N') { echo "selected='selected'";} ?> value="N"><?php echo JText::_("GURU_PROMOINACTIVE"); ?></option>
						</select>	
					</td>
				
				</tr>
			</tbody>
		</table>
	</form>
 <div class="container-fluid">
          <a data-toggle="modal" data-target="#myModal" class="pull-right guru_video" onclick="showContentVideo('index.php?option=com_guru&controller=guruAbout&task=vimeo&id=27181476&tmpl=component')" class="pull-right guru_video" href="#">
                    <img src="<?php echo JURI::base(); ?>components/com_guru/images/icon_video.gif" class="video_img" />
                <?php echo JText::_("GURU_PROMOS_VIDEO"); ?>                  
          </a>
	</div>	
	<div class="clearfix"></div>
   
    <div class="well well-minimized">
		<?php echo JText::_("GURU_PROMOS_SETTINGS_DESCRIPTION"); ?>
	</div>	
	
<form id="adminForm" action="<?php echo JUri::root() ?>administrator/index.php" name="adminForm" method="post">
<div class="clearfix"> </div>

<div id="editcell" >
<table class="table table-striped table-bordered adminlist">
<thead>

	<tr>
		<th width="5">
			<input type="checkbox" onclick="Joomla.checkAll(this)" name="toggle" value="" />
            <span class="lbl"></span>
		</th>
	    <th width="5%">
			<?php echo JText::_('GURU_PROMID');?>
		</th>
		<th width="20%">
			<?php echo JText::_('GURU_PROMTITLE');?>
		</th>
		<th width="15%">
			<?php echo JText::_('GURU_PROMCODE');?>
		</th>
        <th width="15%">
			<?php echo JText::_('GURU_TREEPROGRAMS');?>
		</th>
		<th width="15%">
			<?php echo JText::_('GURU_PROMDATE');?>
		</th>
		<th>
			<?php echo JText::_('GURU_STATUS');?>
		</th>
		<th>
			<?php echo JText::_('GURU_TIME');?>
		</th>
		<th>
			<?php echo JText::_('GURU_USAGE');?>
		</th>
		<th>
			<?php echo JText::_('GURU_PROMPUB_LABEL');?>
		</th>
	</tr>
</thead>

<tbody>

<?php 
	for ($i = 0; $i < $n; $i++):
		$promo = $this->promos[$i];
		$id = $promo->id;
		$checked = JHTML::_('grid.id', $i, $id);
		$link = JRoute::_("index.php?option=com_guru&controller=guruPromos&task=edit&cid[]=".$id);
		$published = JHTML::_('grid.published', $promo, $i );
		$active = 1;$sit=NULL;
		$time_now = date('Y-m-d H:i:s', time());
		
		if($time_now<$promo->codestart){
			$active = 0; $sit=1;
		} elseif(($promo->codeend!='0000-00-00 00:00:00')&&($time_now>$promo->codeend)){
			$active = 0; $sit=2;
		} elseif(isset($promo->codelimit)&&($promo->codelimit!=0)&&($promo->codeused>=$promo->codelimit)){
			$active = 0; $sit=3;
		}
		if((($promo_sts=='Y')&&($active==0))||(($promo_sts=='N')&&($active==1))) continue;
		if((($active==0)&&($promo_sts=='N'))||(($active==1)&&($promo_sts=='Y'))||($promo_sts=='YN')||($promo_sts==NULL)) {
?>			
	
	<tr class="row<?php echo $k;?>"> 
        <td><?php echo $checked;?><span class="lbl"></span></td>							
        <td><a class="a_guru" href="<?php echo $link;?>"><?php echo $promo->id;?></a></td>		
        <td><a class="a_guru" href="<?php echo $link;?>"><?php echo $promo->title;?></a></td>		
        <td><?php echo $promo->code;?></td>
        <td>
            <a class="modal" rel="{handler: 'iframe', size: {x: 770, y: 400}}" href="index.php?option=com_guru&controller=guruPromos&task=show_courses&promo_id=<?php echo $promo->id;?>&tmpl=component"><?php echo JText::_('GURU_APPLIED_FOR');?>
            </a>
        </td>
        <td><?php echo date($config->datetype,strtotime($promo->codestart));?></td>	
        <td><?php 
            if($active==0) echo JText::_('GURU_PROMOINACTIVE'); 
            else echo JText::_('GURU_PROMOACTIVE'); 
            ?> 
        <td><?php echo $promo->codeused; ?></td>		
        <td><?php if($promo->codelimit) echo $promo->codelimit - $promo->codeused;?></td>	
        <td><?php echo $published;?></td>	
	</tr>


<?php }
		$k = 1 - $k;
	endfor;
?>
	</tbody>
            <tfoot>
                <tr>
                    <td colspan="10">
                    	 <div id="filter-bar" class="btn-toolbar">
                            <div class="btn-group pull-left hidden-phone">
                                <label for="limit" class="element-invisible"><?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC');?></label>
                                <?php echo $this->pagination->getLimitBox(); ?>
                            </div>
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
<input type="hidden" name="controller" value="guruPromos" />
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
	
	function showContentVideo1(href){
		first = true;
		jQuery.ajax({
		  url: href,
		  success: function(response){
		   jQuery( '#myModal1 .modal-body').html(response);
		  }
		});
	}
	
	jQuery('#myModal1').on('hide', function () {
	 jQuery('div.modal-body').html('');
	});

	jQuery('#myModal').on('hide', function () {
	 jQuery('div.modal-body').html('');
	});
	
	function closeModal(){
		jQuery('#myModal .modal-body iframe').attr('src', '');
	}
	
	jQuery('body').click(function () {
		if(!first){
			jQuery('#myModal .modal-body iframe').attr('src', '');
		}
		else{
			first = false;
		}
	});
</script>