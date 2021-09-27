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
	//These scripts are already been included from the administrator\components\com_guru\guru.php file
	//$doc->addScript('components/com_guru/js/jquery.DOMWindow.js');
	
	$k = 0;
	$task = JFactory::getApplication()->input->get("task", '');
	$n = count ($this->programs);
	$programs = $this->programs;
	$configs = guruAdminModelguruProgram::getConfigs();
	$currency = $configs->currency;
	$currencypos = $configs->currencypos;
	$character = trim(JText::_("GURU_CURRENCY_".$currency));
	$tmpl = JFactory::getApplication()->input->get("tmpl", "");
	
	if(trim($tmpl) != ""){
		JFactory::getApplication()->input->set("tmpl", "component");
	}
	$listDirn = "asc";
	$listOrder = "ordering";
	$saveOrderingUrl = 'index.php?option=com_guru&controller=guruPrograms&task=saveOrderAjax&tmpl=component';
    JHtml::_('sortablelist.sortable', 'articleList', 'adminForm', strtolower($listDirn), $saveOrderingUrl);
	JHtml::_('bootstrap.tooltip');
	JHtml::_('behavior.multiselect');
	JHtml::_('dropdown.init');
	
	$data_post = JFactory::getApplication()->input->post->getArray();
?>
<?php //WE ARE NOT LONGER BEEN USING AJAX FROM prototype-1.6.0.2.js, INSTEAD WE WILL BE USING jQuery.ajax({}) function ?>

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
</script>

<script language="javascript" type="text/javascript">
	Joomla.submitbutton = function(pressbutton){
	//function submitbutton(pressbutton) {
		var form = document.adminForm;
		if (pressbutton=='duplicate'){
			if (form['boxchecked'].value == 0) {
					alert( "<?php echo JText::_("GURU_PROGRAM_MAKESEL_JAVAMSG");?>" );
			} 
			else {
				submitform( pressbutton );
			}
		}
		else if(pressbutton=='remove'){
			var temp = confirm("<?php echo JText::_("GURU_REMOVE_ADMIN_COURSES");?>" );
			if(temp == true){
				submitform( pressbutton );
			}
			else{
				return false;
			}
		}
		else { 
			submitform( pressbutton );
		}
	}
	
	function utf8_encode (argString) {
		var string = (argString + '');
		var utftext = "", start, end, stringl = 0;
	 
		start = end = 0;    stringl = string.length;
		for (var n = 0; n < stringl; n++) {
			var c1 = string.charCodeAt(n);
			var enc = null;
			 if (c1 < 128) {
				end++;
			} else if (c1 > 127 && c1 < 2048) {
				enc = String.fromCharCode((c1 >> 6) | 192) + String.fromCharCode((c1 & 63) | 128);
			} else {            enc = String.fromCharCode((c1 >> 12) | 224) + String.fromCharCode(((c1 >> 6) & 63) | 128) + String.fromCharCode((c1 & 63) | 128);
			}
			if (enc !== null) {
				if (end > start) {
					utftext += string.slice(start, end);            }
				utftext += enc;
				start = end = n + 1;
			}
		} 
		if (end > start) {
			utftext += string.slice(start, stringl);
		}
		 return utftext;
	}	
		
	function selectCourseID(prod_id, prod_name) {
		var id_selected = parent.document.getElementById("id_selected").value;
		parent.document.getElementById('course_name_text_'+id_selected).innerHTML = prod_name;		
		parent.document.getElementById('course_id'+id_selected).value = parseInt(prod_id);

		var str = parent.document.getElementById('courses_ids_code_generate').value;
		var promo_code = '|'+id_selected;
		var courses_ids_code_generate = parent.document.getElementById('courses_ids_code_generate');
		parent.document.getElementById('courses_ids_code_generate').value = (str.indexOf(promo_code) === -1) ? courses_ids_code_generate.value+"|"+id_selected  :  courses_ids_code_generate.value;

		showPlanForCourseInclude(id_selected, prod_id);

	}

	function getCourseCost(url, generate_id){
		jQuery.ajax({ url: url,
			method: 'get',
			asynchronous: 'true',
			success: function(html) {
				old_total = window.parent.document.getElementById('amount_hidden').value;
				new_total = parseFloat(old_total)+parseFloat(html);
				if(!isNaN(new_total)){
					var character = "<?php //echo $character; ?>";
					parent.document.getElementById('total').value = parseFloat(new_total);
					parent.document.getElementById('amount_hidden').value = parseFloat(new_total).toFixed(2);
					parent.document.getElementById('total_column').innerHTML = character+" "+parseFloat(new_total).toFixed(2);
					parent.document.getElementById('amount').innerHTML = character+" "+parseFloat(new_total).toFixed(2);
					var promo_code = window.parent.document.getElementById('promocode').value;
					
					window.parent.changeAmount(generate_id);
					window.parent.setPromo(promo_code);	
					parent.document.getElementById('close').click();
				}
			},
		});
	}

	function showPlanForCourseInclude(generate_id, course_id){
		var subscr_type = window.parent.document.getElementById('subscr_type_'+generate_id);
		var licences = window.parent.document.getElementById('licences_'+generate_id);
		
		if(subscr_type.style) {
			if(subscr_type.style.display == 'none') {
				subscr_type.style.display = '';
			}
		}
		if(licences.style) {
			if(licences.style.display == 'none') {
				licences.style.display = '';
			}
		}

		var url = "index.php?option=com_guru&controller=guruPrograms&tmpl=component&format=raw&task=getplans&course_id="+course_id+"&gen_id="+generate_id;
		
		jQuery.ajax({ url: url,
			method: 'get',
			asynchronous: 'true',
			success: function(html) {
				nd = window.parent.document.getElementById('div_licences_select_' + generate_id);
				nd.innerHTML = html;
				var licences_select = parent.document.adminForm.licences_select;
				var licences_selectLength = (licences_select.length == 1) ? 1 : licences_select.length;
				parent.document.getElementById("nr_licenses").value = licences_selectLength;
				//set new value after upload a new course
				var url = "index.php?option=com_guru&controller=guruPrograms&task=getcoursecost&tmpl=component&format=raw&course_id="+course_id;
				getCourseCost(url, generate_id);
			},
		});
	}	
</script>
<?php if($task == "selectCourse"){
		//$font = "font-size:13px;";
		//$input = "height:20px;";
?>
        <!-- <link rel="StyleSheet" href="components/com_guru/css/general.css" type="text/css"/>
        <link rel="StyleSheet" href="components/com_guru/css/guru-j30.css" type="text/css"/>
        <link rel="StyleSheet" href="<?php echo JURI::root(); ?>media/jui/css/bootstrap.min.css" type="text/css"/>
        <link rel="StyleSheet" href="<?php echo JURI::root(); ?>media/jui/css/bootstrap-responsive.min.css" type="text/css"/>
        <script type="text/javascript" src="<?php echo JURI::root(); ?>media/system/js/mootools-core.js"></script>
        <script type="text/javascript" src="<?php echo JURI::root(); ?>media/system/js/core.js"></script>
        <script type="text/javascript" src="<?php echo JURI::root(); ?>media/system/js/mootools-more.js"></script>
        <script type="text/javascript" src="<?php echo JURI::root(); ?>media/jui/js/jquery.min.js"></script>
        <script type="text/javascript" src="<?php echo JURI::root(); ?>media/jui/js/bootstrap.min.js"></script> -->
 <?php
 		}
 		else{
			$font = "";
			$input = "";
		}
?>           
<div id="editcell" >


<form action="<?php echo JUri::root() ?>administrator/index.php" id="adminForm" name="adminForm" method="post" style=" <?php //echo $font; ?> ">
<table style="width: 78%;" cellpadding="5" cellspacing="5" bgcolor="#FFFFFF">
		<tr>
			<td>
				<input style=" <?php echo $input; ?>" type="text" name="search_text" value="<?php if(isset($data_post['search_text'])) echo $data_post['search_text'];?>" />
				<input class="btn btn-primary" type="submit" name="submit_search" value="<?php echo JText::_('GURU_SEARCHTXT');?>" />
			</td>
			<td>
				<?php 
				if(isset($data_post['catid'])){
					$categ_set = $data_post['catid'];
				}	
				else{
					$categ_set = "-1";
				}	
				$lists['treecateg'] = $this->list_all2(1, "catid", 0, $categ_set); ?>
			</td>
			<?php
				if($task != "selectCourse"){
			?>	
			<td>
				<select onchange="document.adminForm.submit()" name="course_publ_status">
					<?php
                        $session = JFactory::getSession();
                        $registry = $session->get('registry');
                        $course_publ_status = $registry->get('course_publ_status', "");
                        
                        if(isset($course_publ_status)){
                            $pb = $course_publ_status;
                        }
                        
                        if(isset($data_post['course_publ_status'])){
                            $pb = $data_post['course_publ_status'];
                        }
                        
                        if(!isset($pb)){
                            $pb = NULL;
                        }
                    ?>
                    <option <?php if($pb=='YN') { echo "selected='selected'";} ?> value="YN"><?php echo JText::_("GURU_ALLYN"); ?></option>
                    <option <?php if($pb=='Y') { echo "selected='selected'";} ?> value="Y"><?php echo JText::_("GURU_PUBLISHED"); ?></option>
                    <option <?php if($pb=='N') { echo "selected='selected'";} ?> value="N"><?php echo JText::_("GURU_UNPUBLISHED"); ?></option>
				</select>	
			</td>
			<?php
				}
			?>			
		<tr>
	</table>

 <div id="myModal" class="modal-small modal hide">
 	<div class="modal-dialog modal-lg">
    	<div class="modal-content">
		    <div class="modal-header">
		        <button type="button" class="close" data-dismiss="modal" aria-hidden="true" onclick="javascript:closeModal();">&times;</button>
		    </div>
		    <div class="modal-body">
		    </div>
		</div>
	</div>
</div>
 <div id="myModal1" class="modal1 modal hide">
    <div class="modal-dialog modal-lg">
    	<div class="modal-content">
		    <div class="modal-header">
		        <button type="button" class="close" data-dismiss="modal" aria-hidden="true" onclick="javascript:closeModal();">&times;</button>
		    </div>
		    <div class="modal-body">
		    </div>
		</div>
	</div>
</div>
 <div class="container-fluid">
 	<?php
			if($task != "selectCourse"){
		?>
          <a data-toggle="modal" data-target="#myModal" class="pull-right guru_video" onclick="showContentVideo('index.php?option=com_guru&controller=guruAbout&task=vimeo&id=27181315&tmpl=component')" href="#">
                    <img src="<?php echo JURI::base(); ?>components/com_guru/images/icon_video.gif" class="video_img" />
                <?php echo JText::_("GURU_COURSES_VIDEO"); ?>                  
          </a>
      <?php } else{?> 
      	<style> 
      		/* div.modal1{	width:650px!important; 
      									left: 0% !important;
      									position: fixed;
      									top: 6% !important;
      									width: 770px !important;
      									z-index: 9999;
      					}  */
		</style>  
      	 <a data-toggle="modal" data-target="#myModal1" class="pull-right guru_video" onclick="showContentVideo1('index.php?option=com_guru&controller=guruAbout&task=vimeo&id=27181315&tmpl=component')" href="#">
                    <img src="<?php echo JURI::base(); ?>components/com_guru/images/icon_video.gif" class="video_img" />
                <?php echo JText::_("GURU_COURSES_VIDEO"); ?>                  
          </a> 
      <?php }?>
	</div>	
	<div class="clearfix"></div>
    <?php
			if($task != "selectCourse"){
		?>	   
                <div class="well well-minimized">
                    <?php echo JText::_("GURU_COURSES_SETTINGS_DESCRIPTION"); ?>
                </div>
     <?php }?>           

<table class="table table-bordered table-striped adminlist" id="articleList" style=" <?php //echo $font; ?> ">
<thead>

	<tr>
    	<?php
			if($task != "selectCourse"){
		?>

    	 <th>
        	<?php echo JHtml::_('grid.sort', '<i class="icon-menu-2"></i>', 'a.ordering', $listDirn, $listOrder, null, 'asc', 'JGRID_HEADING_ORDERING'); ?>
        </th>
			<th width="5">
					<input type="checkbox" onclick="Joomla.checkAll(this)" name="toggle" value="" />
					<span class="lbl"></span>
				</th>
		<?php
			}
		?>
	    <th width="20">
			<?php echo JText::_('GURU_ID');?>
		</th>
		<th>
			<?php echo JText::_('GURU_COURSE_TREE');?>
		</th>
		
		<?php
			if($task != "selectCourse"){
		?>		
			<th> <?php echo JText::_('GURU_EDIT_INFO');?></th>
			<th>
				<?php echo JText::_('GURU_PROGRAM_CAT');?>
			</th>
			<th>
				<?php echo JText::_('GURU_TREECUSTOMERS');?>
			</th>
		<?php
			}
			else{
		?>	
				<th>
					<?php echo JText::_('GURU_PROGRAM_CAT');?>
				</th>
		<?php	
			}
			
			if($task != "selectCourse"){
		?>						
		
		<th>
			<?php echo JText::_('GURU_PUBLISHED');?>
		</th>
        <th>
			<?php echo JText::_('GURU_STATUS');?>
		</th>
		
		<?php
			}
		?>
	</tr>
</thead>

<tbody>

<?php 

for ($i = 0; $i < $n; $i++):
	$program = $this->programs[$i];
	$id = $program->id;
	$checked = JHTML::_('grid.id', $i, $id);
	/*$checked_array = guruAdminModelguruProgram::checkbox_construct( $i, $id, $name='cid' );	
	$checked_array_expld = explode('$$$$$', $checked_array);	
	*/
	if(isset($checked_array_expld["0"])){
		$checked = $checked_array_expld["0"];
	}
		
	$link = JRoute::_("index.php?option=com_guru&controller=guruPrograms&task=edit&cid[]=".$id);	
	$published = JHTML::_('grid.published', $program, $i );
	
	// we find how many days has a program
	$howManyDays = guruAdminModelguruProgram::getpdays($id);
	$categoryObj = "";
	if($program->catid > 0){
		$categoryObj = guruAdminModelguruProgram::getProgramCategory($program->catid);
		$linkcat = JRoute::_("index.php?option=com_guru&controller=guruPcategs&task=edit&cid[]=".$categoryObj->id);
		$link_cat_for_program = '<a class="a_guru" href="'.$linkcat.'" >'.$categoryObj->name.'</a>';
	}
	else{
		$link_cat_for_program = '<span class="muted">'.JText::_('GURU_CATEGORY_SEARCH').'</span>';
	}
	
	if($task == "selectCourse"){
		if($categoryObj != ""){
			$link_cat_for_program = $categoryObj->name;
		}
	}
	
	$howManyStudCourse = guruAdminModelguruProgram::getStudentsNumber($id);	
	$link_nb_stud = JRoute::_("index.php?option=com_guru&controller=guruPrograms&task=show&pid=".$id);
	$link_stud_nb_of_course = '<a class="a_guru" href="'.$link_nb_stud.'" >'.$howManyStudCourse.'</a>';	
				
?>
	<tr class="row<?php echo $k;?>"> 	
    	<?php
			if($task != "selectCourse"){
		?>
    	<td>
			<span class="sortable-handler active" style="cursor: move;">
                <i class="icon-menu"></i>
            </span>
            <input type="text" class="width-20 text-area-order " value="<?php echo $program->ordering; ?>" size="5" name="order[]" style="display:none;">
        </td>    
        <?php }?>
		<?php
			if($task != "selectCourse"){
				echo "<td>".$checked."<span class=\"lbl\"></span></td>";
			}
		?>
	    <td>
	     	<?php echo $id;?>
		</td>		
	    <td>
			<?php				
				if($task == "selectCourse"){
			?>			
				<a class="a_guru" onclick="selectCourseID('<?php echo $id; ?>', '<?php echo trim(str_replace("'", "&acute;", $program->name)); ?>')" >
					<?php echo $program->name." (".$howManyDays->how_many.")"; ?>
				</a>	
			<?php		
				}
				else{
			?>
				<a class="a_guru" href="index.php?option=com_guru&controller=guruDays&pid=<?php echo $id;?>">
					<?php echo $program->name." (".$howManyDays->how_many.")"; ?>
				</a>
			<?php
				}
			?> 	   
		</td>
		<?php
			if($task != "selectCourse"){
		?>	
		<td>
			<a class="a_guru" href="<?php echo $link;?>" ><?php echo JText::_('edit');?></a>
		</td>
		<?php
			}
		?>
	    <td>
	     	<?php echo $link_cat_for_program;?>
		</td>
        
		<?php
			if($task != "selectCourse"){
		?>		
			<td align="center">
	     	<?php echo $link_stud_nb_of_course;?>
			</td>	
		<?php
			}
			
			if($task != "selectCourse"){
		?>	     						
		<td align="center">
			<?php echo $published;?>
		</td>
        <td align="center">
			<?php
            	if($program->status == "0"){ // in pending
			?>
            		<a title="Unpublish Item" onclick="return listItemTask('cb<?php echo $i; ?>','approve')" href="#">
                    	<img src="<?php echo JURI::root(); ?>administrator/components/com_guru/images/publish_x.png">
                    </a>
            <?php
				}
				elseif($program->status == "1"){ // approved
			?>
            		<a title="Unpublish Item" onclick="return listItemTask('cb<?php echo $i; ?>','pending')" href="#">
                    	<img src="<?php echo JURI::root(); ?>administrator/components/com_guru/images/tick.png">
                    </a>
            <?php
				}
			?>
		</td>		
		
		<?php
			}
		?>
	</tr>


<?php 
		$k = 1 - $k;
	endfor;
				

?>	
		 <tr>
                <td colspan="10">
                <?php
                if($task == "selectCourse"){
				?>
                <style>
                	.pagination {
						height: 0px!important;
					}
                
                </style>
                <?php
				}
				?>
                    <div class="pagination pagination-toolbar">
                        <?php echo $this->pagination->getListFooter(); ?>
                    </div>
                    <div class="btn-group pull-left">
                        <label for="limit" class="element-invisible"><?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC');?></label>
                        <?php echo $this->pagination->getLimitBox(); ?>
                   </div>
                </td>
            </tr>

</tbody>


</table>
<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
<?php echo JHtml::_('form.token'); ?>
<input type="hidden" name="option" value="com_guru" />
<input type="hidden" name="task" value="<?php echo $task; ?>" />
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="controller" value="guruPrograms" />
<input type="hidden" name="old_limit" value="<?php echo JFactory::getApplication()->input->get("limitstart"); ?>" />

<?php
	if($task == "selectCourse"){
?>
	<input type="hidden" name="tmpl" value="component" />
<?php
	}
?>
</form>
</div>
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