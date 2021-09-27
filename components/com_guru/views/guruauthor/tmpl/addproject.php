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

$data_get = JFactory::getApplication()->input->get->getArray();
$data_post = JFactory::getApplication()->input->post->getArray();

$projects = $this->projects;
$n = count($projects);

JHtml::_('behavior.framework');

?>

<script type="text/javascript" language="javascript">
	document.body.className = document.body.className.replace("modal", "");
</script>

<style>
	.component{
		background:transparent;
	}
</style>

<style>
	table.adminlist {
		background-color:#E7E7E7;
		border-spacing:1px;
		color:#666666;
		width:100%;
		font-family:Arial,Helvetica,sans-serif;
		font-size:11px;
	}
</style>
<link rel="stylesheet" href="<?php echo JURI::base()."components/com_guru/css/modal.css";?>" type="text/css" />

<script type="text/javascript" src="<?php //echo JURI::root().'media/system/js/mootools.js' ?>"></script>
<script type="text/javascript" src="<?php echo JURI::base().'components/com_guru/js/modal.js' ?>"></script>
<script type="text/javascript">

function loadjscssfile(filename, filetype){
	if (filetype=="js"){ //if filename is a external JavaScript file
  		var fileref=document.createElement('script');
  		fileref.setAttribute("type","text/javascript");
  		fileref.setAttribute("src", filename);
	}
 	else if (filetype=="css"){ //if filename is an external CSS file
 		var fileref=document.createElement("link");
 		fileref.setAttribute("rel", "stylesheet");
  		fileref.setAttribute("type", "text/css");
  		fileref.setAttribute("href", filename);
 	}
	
 	if (typeof fileref!="undefined"){
  		document.getElementsByTagName("head")[0].appendChild(fileref);
	}
}

function loadprototipe(){
	//loadjscssfile("<?php echo JURI::root().'components/com_guru/views/guruauthor/tmpl/prototype-1.6.0.2.js' ?>","js");
}

function addproject (idu, name, asoc_file, description) {
	//loadprototipe();
	
	var url = '<?php echo JURI::root(); ?>index.php?option=com_guru&controller=guruAuthor&tmpl=component&format=raw&task=add_media_ajax&id='+idu+'&type=project';
	
	jQuery.ajax({
		async: false,
		url: url,
		method: 'get',
		success: function(transport) {
			replace_m = document.getElementById('to_replace').value;
			to_be_replaced = parent.document.getElementById('media_'+replace_m);
			to_be_replaced.innerHTML = '&nbsp;';
			
			if(replace_m == 99){
				if ((transport.match(/(.*?).pdf(.*?)/))&&(!transport.match(/(.*?).iframe(.*?)/))) {to_be_replaced.innerHTML += transport+'<p /><div  style="text-align:center"><i>' + description + '</i></div>'; } else {
					to_be_replaced.innerHTML += transport+'<br /><div style="text-align:center"><i>'+ name +'</i></div><br /><div  style="text-align:center"><i>' + description + '</i></div>';
				}
			} else {
				to_be_replaced.innerHTML += transport;
				parent.document.getElementById("media_"+99).style.display="";
				parent.document.getElementById("description_med_99").innerHTML=''+name;
				
				parent.document.getElementById('before_menu_med_'+replace_m).style.display = 'none';
				parent.document.getElementById('after_menu_med_'+replace_m).style.display = '';
				parent.document.getElementById('db_media_'+replace_m).value = idu;
			}			
		
			screen_id = document.getElementById('the_screen_id').value;
			replace_edit_link = parent.document.getElementById('a_edit_media_'+replace_m);
			replace_edit_link.href = Joomla.getOptions('system.paths').root + '/index.php?option=com_guru&controller=guruAuthor&tmpl=component&task=editProject&cid='+ idu;
			
			if ((transport.match(/(.*?).pdf(.*?)/))&&(!transport.match(/(.*?).iframe(.*?)/))) {
				var qwe='&nbsp;'+transport+'<p /><div  style="text-align:center"><i>' + description + '</i></div>';
			} else {
				var qwe='&nbsp;'+transport+'<br /><div style="text-align:center"><i>'+ name +'</i></div><div  style="text-align:center"><i>' + description + '</i></div>';
			}
			window.parent.test(replace_m, idu,qwe);
		}
	 });
	 
	setTimeout('window.parent.document.getElementById("close").click()',1000);
	return true;
}

</script>

	<div style="float: left; font-weight:bold"><?php echo JText::_("GURU_CLICK_TO_PROJECT"); ?></div>
		<br /><br />
	<div>
<form name="adminForm2" action="<?php echo JUri::root() ?>/index.php?option=com_guru&controller=guruAuthor&task=addproject&med=<?php echo $data_get['med'];?>&tmpl=component&cid=<?php echo $data_get['cid'];?><?php if(isset($data_get['project'])){echo "&project=".$data_get['project'];}?><?php if(isset($_REQUEST['type'])){echo "&type=".$_REQUEST['type'];}?>" method="post">
	<div class="gru-page-filters">
            <div class="gru-filter-item">
				<input type="text" class="form-control" name="search_project" value="<?php
					
					$session = JFactory::getSession();
					$registry = $session->get('registry');
					$search_project_tskmed = $registry->get('search_project_tskmed', "");
					
					if(isset($data_post['search_project'])&&($data_post['search_project']!='')){
						echo $data_post['search_project'];
					} 
					elseif(isset($search_project_tskmed)&&($search_project_tskmed!='')) {
						echo $search_project_tskmed;
					}?>" />
				<input type="hidden" name="type" value="<?php if(isset($data_get['type'])) echo $data_get['type']; elseif (isset($data_post['type'])) echo $data_post['type'];?>" />
				<input class="btn btn-primary" type="submit" name="submit_search" value="<?php echo JText::_("GURU_SEARCHTXT"); ?>" />
                <input type="hidden" name="type" value="<?php echo $_REQUEST['type']; ?>">
			</div>
        </div>		
</form>
</div>
<br />

<div class="clearfix"></div>

<div>
<div id="editcell">
	<table class="table table-striped adminlist">
		<thead>
			<tr>
				<th width="20"><?php echo JText::_("GURU_ID"); ?></th>
				<th><?php echo JText::_("GURU_TITLE"); ?></th>
				<th><?php echo JText::_("GURU_PROGRAM"); ?></th>
			</tr>
		</thead>
		<tbody>
<?php 
		@$pid = intval($data_get['cid'][0]);
		
		$session = JFactory::getSession();
		$registry = $session->get('registry');
		$addmed_tskmed_to_rep = $registry->get('addmed_tskmed_to_rep', "");
		
		if ($n>0) { 
			for ($i = 0; $i < $n; $i++){
				$file =$projects[$i];
				if(isset($data_get['med'])){	
					$media_to_replace = $data_get['med'];
					$session = JFactory::getSession();
					$registry = $session->get('registry');
					$registry->set('addmed_tskmed_to_rep', $data_get['med']);
					
				} elseif(isset($addmed_tskmed_to_rep) && trim($addmed_tskmed_to_rep) != ""){
					$media_to_replace = $addmed_tskmed_to_rep;
				} else {
					$media_to_replace = NULL;
			}

			$id = $file->id;
			$checked = JHTML::_('grid.id', $i, $id);
			$asoc_file = "-";
			$link = "addproject('".$id."', '".addslashes($file->title)."', '".addslashes($asoc_file)."', '' ); ";
		?>
			<tr class="camp0"> 
	   			<td><?php echo $file->id;?></td>		
	    		<td><a onmouseover="loadprototipe()" onclick="<?php echo $link;?>" href="#"><?php echo $file->title;?></a></td>	
				<td>
                	<?php
                    	$course = $this->projectCourse($id);
						echo $course;
					?>
                </td>	
			</tr>
		<?php 
		} // endif for MEDIA check
 } ?>

<style>
	.pagination ul > li {
		display: inline-flex;
	}
</style>

	<form name="adminForm" id="adminForm" action="<?php echo JUri::root(true) ?>/index.php?option=com_guru&controller=guruAuthor&task=addproject&med=<?php echo $data_get['med'];?>&tmpl=component&cid=<?php echo $data_get['cid'];?>" method="post">
		<table>
        	<tr>
        		<td colspan="6">
                    <div class="btn-group pull-left">
                        <?php
                        	//echo $this->pagination->getLimitBox();
							echo $this->pagination->getLimitBox();
							$pages = $this->pagination->getPagesLinks();
							include_once(JPATH_SITE.DIRECTORY_SEPARATOR."components".DIRECTORY_SEPARATOR."com_guru".DIRECTORY_SEPARATOR."helpers".DIRECTORY_SEPARATOR."helper.php");
							$helper = new guruHelper();
							$pages = $helper->transformPagination($pages);
							echo $pages;
						?>
                   </div>
                </td>
			</tr>
		</table>
		<input type="hidden" name="filter2" id="filter2" value="<?php if(isset($filter2)) {echo $filter2;} else {echo '3';}?>" />
        <input type="hidden" name="old_limit" value="<?php echo JFactory::getApplication()->input->get("limitstart"); ?>" />
	</form>
	</tbody>
</table>
</div>

</div>
<input type="hidden" id="to_replace" value="<?php 
	echo @$media_to_replace; 
?>">
<input type="hidden" id="the_screen_id" value="<?php 
	echo $pid; 
?>">