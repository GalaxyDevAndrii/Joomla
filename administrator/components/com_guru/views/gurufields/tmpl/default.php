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

/*JHtml::_('behavior.framework');*/

defined ('_JEXEC') or die ("Go away.");
	$doc =JFactory::getDocument();
	
	$bar = JToolbar::getInstance('toolbar');
	$k = 0;
	$task = JFactory::getApplication()->input->get("task", '');
	$groups = $this->groups;
	
	$listDirn = "asc";
	$listOrder = "ordering";
	$saveOrderingUrl = 'index.php?option=com_guru&controller=guruFields&task=saveOrderAjax&tmpl=component';
    JHtml::_('sortablelist.sortable', 'articleList', 'adminForm', strtolower($listDirn), $saveOrderingUrl);
	JHtml::_('bootstrap.tooltip');
	JHtml::_('behavior.multiselect');
	JHtml::_('dropdown.init');
	
	$data_post = JFactory::getApplication()->input->post->getArray();
?>

<script type="text/javascript" id="load-jquery">
	// When get this page content through Ajax Request all js files that supposed to be loaded by controller, are not loaded, so we have to load them with javascript only in case there are not already been loaded 
	var element = document.getElementById('load-jquery');
	if(typeof jQuery == 'undefined'){
		document.write('<link href="components/com_guru/css/bootstrap.min.css" rel="stylesheet">');
		document.write('<script src="components/com_guru/js/jquery_1_11_2.js"><\/script>');

	}
	element.parentNode.removeChild(element);
</script>

<script type="text/javascript" id="load-core-js">
	// When get this page content through Ajax Request all js files that supposed to be loaded by controller, are not loaded, so we have to load them with javascript only in case there are not already been loaded 
	var element = document.getElementById('load-core-js');
	if(typeof Joomla == 'undefined'){
		document.write('<script src="components/com_guru/js/core.js"><\/script>');
	}
	element.parentNode.removeChild(element);

	function deleteGroup(poz){
		if(confirm("<?php echo JText::_("GURU_DELETE_FIELDS_GROUP"); ?>")){
			listItemTask('cbg'+poz, 'delete-group');
		}
		else{
			return false;
		}
	}

	function deleteField(poz){
		if(confirm("<?php echo JText::_("GURU_DELETE_FIELD"); ?>")){
			listItemTask('cb'+poz, 'delete-field');
		}
		else{
			return false;
		}
	}
</script>

<style>
	div.modal-header .close{
		right: 14px;
	}

	.modal-header h3{
		padding-left: 20px;
	}

	div[id^="toolbar-popup-new"] button.modal{
		display: none !important;
	}

	div[id^="toolbar-popup-new"]{
		margin: 0px !important;
	}

	#toolbar-popup-new > button.modal,
	#toolbar-popup-new-group > button.modal{
		display: inline-block !important;
	}

	.icon-new-group::before{
	    content: "\2a";
	}

	.ui-sortable-list{
		background-color: #f5f5f5;
		padding: 5px 10px;
		margin-bottom: 20px;
		border:1px solid #ddd;
	}

	.ui-sortable-list h4{
		background-color: #ddd;
		padding: 10px 0px;
		margin: 5px 0px;
		line-height: 25px;
		cursor: move;
	}

	.delete-group-area{
		display: inline-block;
		float: right;
	}
</style>

<form action="<?php echo JUri::root() ?>administrator/index.php" id="adminForm" name="adminForm" method="post">

	<div id="sortable" class="ui-sortable">
<?php
	if(isset($groups) && count($groups) > 0 ){
		$poz = 0;

		foreach($groups as $key=>$group){
?>
 	
 			<div class="ui-sortable-list">
	 			<h4 class="handle">
	 				<i class="icon-menu-2"></i>
	 				<?php echo $group["name"]; ?>
	 				<div class="delete-group-area">
	 					<button class="btn btn-danger" onclick="return deleteGroup(<?php echo $key; ?>);"><?php echo JText::_("GURU_DELETE_GROUP_BTN") ?></button>
	 				</div>
	 			</h4>

	 			<table style="margin-bottom: 10px;">
	 				<tr>
	 					<th style="text-align: left; width: 20%; min-width: 65px;">
	 						<?php echo JText::_("GURU_EDIT"); ?>:
	 					</th>
	 					<td>
	 						<div style="visibility: hidden; width: 0px; height: 0px;">
	 							<input type="checkbox" id="cbg<?php echo $key; ?>" name="cidg[]" value="<?php echo intval($group["id"]); ?>" />
	 							<span class="lbl"></span>
	 						</div>

	 						<?php
					    		$url_group = JURI::root()."administrator/index.php?option=com_guru&task=new_group&controller=guruFields&tmpl=component&id=".intval($group["id"]);
								$bar->appendButton('Popup', 'new-group'.intval($group["id"]), $group["name"], $url_group);
					    	?>
					    	<a href="#" onclick="jQuery('#toolbar-popup-new-group<?php echo intval($group["id"]) ?> .modal').click(); return false;">
					    		<?php echo $group["name"]; ?>
					    	</a>
	 					</td>
	 				</tr>
	 				<tr>
	 					<th style="text-align: left; min-width: 65px;">
	 						<?php echo JText::_("GURU_STATUS"); ?>:
	 					</th>
	 					<td>
	 						<?php
				            	if($group["published"] == "0"){ // not published
							?>
				            		<a title="Publish Item" onclick="return listItemTask('cbg<?php echo $key; ?>', 'published-group')" href="#">
				                    	<img src="<?php echo JURI::root(); ?>administrator/components/com_guru/images/publish_x.png">
				                    </a>
				            <?php
								}
								elseif($group["published"] == "1"){ // published
							?>
				            		<a title="Unpublish Item" onclick="return listItemTask('cbg<?php echo $key; ?>','unpublished-group')" href="#">
				                    	<img src="<?php echo JURI::root(); ?>administrator/components/com_guru/images/tick.png">
				                    </a>
				            <?php
								}
							?>
	 					</td>
	 				</tr>
	 			</table>
				
				<table class="table table-bordered table-striped adminlist" id="articleList">
					<thead>
						<tr>
					    	<th width="5">
					        	<?php echo JHtml::_('grid.sort', '<i class="icon-menu-2"></i>', 'a.ordering', $listDirn, $listOrder, null, 'asc', 'JGRID_HEADING_ORDERING'); ?>
					        </th>

							<th width="5">
								<!--
								<input type="checkbox" onclick="Joomla.checkAll(this)" name="toggle" value="" />
								<span class="lbl"></span>
								-->
							</th>

							<th>
								<?php echo JText::_('GURU_FIELD_NAME');?>
							</th>
							<th>
								<?php echo JText::_('GURU_FIELD_CODE');?>
							</th>
							<th>
								<?php echo JText::_('GURU_TYPE');?>
							</th>
							<th>
								<?php echo JText::_('GURU_FIELD_PUBLISHED');?>
							</th>
							<th>
								<?php echo JText::_('GURU_FIELD_REQUIRED');?>
							</th>
						</tr>
					</thead>

					<tbody>

				<?php
					$fields = $this->getFields($group["id"]);
					$n = count($fields);

					for ($i = 0; $i < $n; $i++):
						$field = $fields[$i];
						$id = $field->id;
						$checked = JHTML::_('grid.id', $poz, $id);
						$published = JHTML::_('grid.published', $field, $poz );	
				?>
						<tr class="row<?php echo $k;?>"> 	
					    	<td>
								<span class="sortable-handler active" style="cursor: move;">
					                <i class="icon-menu"></i>
					            </span>
					            <input type="text" class="width-20 text-area-order " value="<?php echo $field->ordering; ?>" size="5" name="order[]" style="display:none;">
					        </td>    
					        
							<?php
								echo "<td>".$checked."<span class=\"lbl\"></span></td>";
							?>

						    <td>
						    	<?php
						    		$url_field = JURI::root()."administrator/index.php?option=com_guru&task=new_field&controller=guruFields&tmpl=component&id=".intval($id);
									$bar->appendButton('Popup', 'new'.intval($id), $field->name, $url_field);
						    	?>
						    	<a href="#" onclick="jQuery('#toolbar-popup-new<?php echo intval($id) ?> .modal').click(); return false;">
						    		<?php echo $field->name; ?>
						    	</a>
							</td>

							<td>
						     	<?php echo $field->field_code; ?>
							</td>

							<td>
						     	<?php echo strtoupper($field->type); ?>
							</td>

							<td>
						     	<?php echo $published; ?>
							</td>

							<td>
								<?php
					            	if($field->required == "0"){ // not required
								?>
					            		<a title="Required Item" onclick="return listItemTask('cb<?php echo $poz; ?>', 'required')" href="#">
					                    	<img src="<?php echo JURI::root(); ?>administrator/components/com_guru/images/publish_x.png">
					                    </a>
					            <?php
									}
									elseif($field->required == "1"){ // required
								?>
					            		<a title="Un-Required Item" onclick="return listItemTask('cb<?php echo $poz; ?>','un-required')" href="#">
					                    	<img src="<?php echo JURI::root(); ?>administrator/components/com_guru/images/tick.png">
					                    </a>
					            <?php
									}
								?>

								<div class="delete-group-area">
				 					<button class="btn btn-danger" onclick="return deleteField(<?php echo $poz; ?>);"><?php echo JText::_("GURU_DELETE_FIELD_BTN") ?></button>
				 				</div>
							</td>
						</tr>


				<?php 
						$k = 1 - $k;
						$poz ++;
					endfor;
				?>	
						<!--
						<tr>
				            <td colspan="10">
				                <div class="pagination pagination-toolbar">
				                    <?php echo $this->pagination->getListFooter(); ?>
				                </div>
				                <div class="btn-group pull-left">
				                    <label for="limit" class="element-invisible"><?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC');?></label>
				                    <?php echo $this->pagination->getLimitBox(); ?>
				               </div>
				            </td>
				        </tr>
						-->
					</tbody>
				</table>

			</div>
<?php
		}
	}
?>


	<?php echo JHtml::_('form.token'); ?>
	<input type="hidden" name="option" value="com_guru" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="controller" value="guruFields" />
	<input type="hidden" name="old_limit" value="<?php echo JFactory::getApplication()->input->get("limitstart"); ?>" />

	</div>
</form>


<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

<script type="text/javascript">
    jQuery( document ).ready(function() {
        jQuery("#sortable").sortable({
        	handle: '.handle',
        	stop: function (event, ui) {
        		cidg = [];

        		jQuery("input[id^=cbg]").each(function( index ) {
					cidg.push($(this).val());
				});

            	ajax_url = "index.php?option=com_guru&controller=guruFields&task=saveOrderGroupsAjax&tmpl=component";
				
				var data = {
				   	'cidg': cidg
				};

				jQuery.post(ajax_url, data, function(response) {
					
				});
        	}
        });

        jQuery("#sortable").disableSelection();
    });
</script>