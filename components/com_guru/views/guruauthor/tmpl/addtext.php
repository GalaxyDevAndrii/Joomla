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

JHtml::_('behavior.framework');
$medias = $this->medias;
$n = count($medias);
$db = JFactory::getDBO();

$sql = "select count(*) from #__guru_media";
$db->setQuery($sql);
$db->execute();
$count = $db->loadresult();
$action = JFactory::getApplication()->input->get("action", "");

$doc = JFactory::getDocument();
$data_get = JFactory::getApplication()->input->get->getArray();
$data_post = JFactory::getApplication()->input->post->getArray();

if(!isset($count) || $count == 0){
	echo "<b>".JText::_("GURU_NO_MEDIA")."</b>";
	return;
}

$v = 1;
$z = 1;
?>

<script type="text/javascript" language="javascript">
	document.body.className = document.body.className.replace("modal", "");
</script>

<!-- <script type="text/javascript" src="<?php //echo JURI::root(); ?>media/jui/js/jquery.min.js"></script>
<script type="text/javascript" src="<?php //echo JURI::root(); ?>media/jui/js/bootstrap.min.js"></script>
<script type="text/javascript" src="<?php //echo JURI::root(); ?>media/system/js/mootools-core.js"></script>
<script type="text/javascript" src="<?php //echo JURI::root(); ?>media/system/js/core.js"></script>
<script type="text/javascript" src="<?php //echo JURI::root(); ?>media/system/js/mootools-more.js"></script> -->

<script type="text/javascript">
function loadjscssfile(filename, filetype){
 if (filetype=="js"){ //if filename is a external JavaScript file
  var fileref=document.createElement('script')
  fileref.setAttribute("type","text/javascript")
  fileref.setAttribute("src", filename)
 }
 else if (filetype=="css"){ //if filename is an external CSS file
  var fileref=document.createElement("link")
  fileref.setAttribute("rel", "stylesheet")
  fileref.setAttribute("type", "text/css")
  fileref.setAttribute("href", filename)
 }
 if (typeof fileref!="undefined")
  document.getElementsByTagName("head")[0].appendChild(fileref)
}
</script>

<!--<script type="text/javascript" src="<?php echo JURI::base().'components/com_guru/views/guruauthor/tmpl/prototype-1.6.0.2.js' ?>"></script>-->


<script>

function loadprototipe(){
	//loadjscssfile("<?php echo JURI::base().'components/com_guru/views/guruauthor/tmpl/prototype-1.6.0.2.js' ?>","js");
}
function addmedia (idu, name, asoc_file, description, isquiz, the_real_quiz_id) {
    //loadprototipe();
	var url = '<?php echo JURI::root(); ?>index.php?option=com_guru&controller=guruAuthor&tmpl=component&format=raw&task=add_text_ajax&id='+idu;

	if(the_real_quiz_id == "new_module"){	
		replace_m = document.getElementById('to_replace').value;
		to_be_replaced = parent.document.getElementById('media_'+replace_m);
		to_be_replaced.innerHTML = name;
		parent.document.getElementById('db_media_'+replace_m).value = idu;
		parent.document.getElementById('before_menu_med_'+replace_m).style.display = 'none';
		parent.document.getElementById('after_menu_med_'+replace_m).style.display = '';
		
		replace_m = document.getElementById('to_replace').value;	
		to_be_replaced = parent.document.getElementById('text_'+replace_m);
		to_be_replaced.innerHTML = "";
		window.parent.document.getElementById('close-window').click();

		return true;
	}
	<?php
		if($v == 1){
	?>

		jQuery.ajax({
			async: false,
			url: url,
			success: function(html) {
		        replace_m = document.getElementById('to_replace').value;
				to_be_replaced = parent.document.getElementById('text_'+replace_m);
				to_be_replaced.innerHTML = '&nbsp;';
				to_be_replaced.innerHTML += html;
		
				parent.document.getElementById('before_menu_txt_'+replace_m).style.display = 'none';
				parent.document.getElementById('after_menu_txt_'+replace_m).style.display = '';
				parent.document.getElementById('db_text_'+replace_m).value = idu;
				
				screen_id = document.getElementById('the_screen_id').value;
				replace_edit_link = parent.document.getElementById('a_edit_text_'+replace_m);
				if(isquiz == '0')
					replace_edit_link.href = '<?php echo JURI::root(); ?>index.php?option=com_guru&controller=guruAuthor&tmpl=component&task=editsboxx&cid='+ idu +'&scr=' + screen_id;		
				else	
					replace_edit_link.href = '<?php echo JURI::root(); ?>index.php?option=com_guru&controller=guruAuthor&tmpl=component&task=editQuiz&cid='+ the_real_quiz_id +'&scr=' + screen_id;		
					
				var qwe='<div style="text-align:center"><i>'+ name +'</i></div><br /><br /><div  style="text-align:center"><i>' + description + '</i></div>&nbsp;'+html+'<br /><br />';
				window.parent.txtest(replace_m, idu,qwe);
			}
		});

		/*var req = jQuery.ajax({
			method: 'get',
			asynchronous: 'false',
			url: url,
			data: { 'do' : '1' },	
			success: function(tree, elements, html){
				replace_m = document.getElementById('to_replace').value;
				to_be_replaced = parent.document.getElementById('text_'+replace_m);
				to_be_replaced.innerHTML = '&nbsp;';
				to_be_replaced.innerHTML += html;
		
				parent.document.getElementById('before_menu_txt_'+replace_m).style.display = 'none';
				parent.document.getElementById('after_menu_txt_'+replace_m).style.display = '';
				parent.document.getElementById('db_text_'+replace_m).value = idu;
				
				screen_id = document.getElementById('the_screen_id').value;
				replace_edit_link = parent.document.getElementById('a_edit_text_'+replace_m);
				if(isquiz == '0')
					replace_edit_link.href = '<?php echo JURI::root(); ?>index.php?option=com_guru&controller=guruAuthor&tmpl=component&task=editsboxx&cid='+ idu +'&scr=' + screen_id;		
				else	
					replace_edit_link.href = '<?php echo JURI::root(); ?>index.php?option=com_guru&controller=guruAuthor&tmpl=component&task=editQuiz&cid='+ the_real_quiz_id +'&scr=' + screen_id;		
					
				var qwe='<div style="text-align:center"><i>'+ name +'</i></div><br /><br /><div  style="text-align:center"><i>' + description + '</i></div>&nbsp;'+html+'<br /><br />';
				window.parent.txtest(replace_m, idu,qwe);
			},
		})*/
	<?php  }
		if($z == 1){?>
		//window.parent.close_modal();
	<?php  }?>
	setTimeout('window.parent.document.getElementById("close").click()',1000);

	return true;
}
</script>

<style>
	.component{
		background:transparent;
	}
</style>

<div class="gru-addtext" style="padding:10px;">
	<h2 class="gru-page-title">
    	<?php echo JText::_("GURU_CLICK_TO_TEXT"); ?>
	</h2>
	
    <form name="form1" action="<?php echo JUri::root(true) ?>/index.php?option=com_guru&controller=guruAuthor&task=addtext&txt=<?php echo $data_get['txt'];?>&tmpl=component&cid=<?php echo $data_get['cid'];?>" method="post">
    
        <div class="gru-page-filters">
            <div class="gru-filter-item">
				<?php
					$session = JFactory::getSession();
					$registry = $session->get('registry');
					$search_value_session = $registry->get('search_value', "");
					$search_value = JFactory::getApplication()->input->get("search_text", "");
					
                    if((isset($search_value_session)) && ($search_value == "")){
                        $search_value = $search_value_session;
                    }
                ?>
                <input type="text" class="form-control" name="search_text" value="<?php echo $search_value; ?>" class="form-control inputbox" />
                <button type="submit" name="submit_search" class="uk-button uk-button-primary wk-button wk-button-primary"><?php echo JText::_("GURU_SEARCHTXT"); ?></button>
                <input type="hidden" name="type" value="<?php if(isset($data_get['type'])) echo $data_get['type']; elseif (isset($data_post['type'])) echo $data_post['type'];?>" />
			</div>
        </div>
    
        <input type="hidden" name="controller" value="guruAuthor" />
        <input type="hidden" name="task" value="addtext" />	
        <input type="hidden" name="filter2" id="filter2" value="<?php if(isset($filter2)) {echo $filter2;} else {echo '3';}?>" />
        <input type="hidden" name="cid" value="<?php echo @$pid; ?>">
        <input type="hidden" name="action" value="<?php echo $action; ?>">
    </form>
    
    <div class="clearfix"></div>
    
    <table class="uk-table uk-table-striped wk-table wk-table-striped">
        <tr>
            <th width="20"><?php echo JText::_("GURU_ID"); ?></th>
            <th><?php echo JText::_("GURU_NAME"); ?></th>
    		<th><?php echo JTExt::_("GURU_PUBLISHED"); ?></th>
        </tr>

		<?php 
            $pid = intval($_REQUEST['cid']);
            if($n>0){ 
            for ($i = 0; $i < $n; $i++):
            $file = $this->medias[$i];	
            $media_to_replace = $data_get['txt'];
        
            $id = $file->id;
            $checked = JHTML::_('grid.id', $i, $id);
            $asoc_file = $this->get_asoc_file_for_media($id);
            
            if($file->type=='quiz')
                {
                    $the_quiz_id = $this->real_quiz_id($file->id);
                }		
            
			$instructions = $file->instructions;
			$instructions = nl2br($instructions);
			$instructions = str_replace("<br/>", " ", $instructions);
			$instructions = str_replace("<br />", " ", $instructions);
			$instructions = str_replace("\n", " ", $instructions);
			$instructions = str_replace("\r", " ", $instructions);
			$instructions = str_replace("\r\n", " ", $instructions);
			
            if($file->type=='quiz'){
                $link = "addmedia('".$id."', '".addslashes($file->name)."', '".addslashes($asoc_file)."', '".addslashes($instructions)."' , '1', '".$the_quiz_id."' ); return false;";
            }	
            else{
                if($action == "new_module"){
                    $link = "addmedia('".$id."', '".addslashes($file->name)."', '".addslashes($asoc_file)."', '".addslashes($instructions)."' , '0', 'new_module' ); return false;";
                }
                else{
                    $link = "addmedia('".$id."', '".addslashes($file->name)."', '".addslashes($asoc_file)."', '".addslashes($instructions)."' , '0', '0' ); return false;";
                }	
            }
            $published = JHTML::_('grid.published', $file, $i ); 
            
            if($file->type=='text')
            {
            ?>
            <tr class="camp0"> 
               <td><?php echo $file->id;?></td>		
                <td><a onclick="<?php echo $link;?>" href="#"><?php echo $file->name;?></a></td>		
                <td>
					<?php
                    	//echo $published;
						
						if($file->published == 0){
							echo '<a title="Publish Item" onclick="return listItemTask(\'cb'.$i.'\', \'unpublish\')" href="#">
									<i class="fa fa-times-circle"></i>
								  </a>';
						}
						else{
							echo '<a title="Unpublish Item" onclick="return listItemTask(\'cb'.$i.'\',\'publish\')" href="#">
									<i class="fa fa-check-circle-o"></i>
								  </a>';
						}
					?>
				</td>
            </tr>
        <?php 
            } // endif for MEDIA check
            endfor;
         } ?>

        <form name="adminForm" action="<?php echo JUri::root(true) ?>/index.php?option=com_guru&controller=guruAuthor&task=addtext&txt=<?php echo $data_get['txt'];?>&tmpl=component&cid=<?php echo $data_get['cid'];?>" method="post">
        
            <input type="hidden" name="filter2" id="filter2" value="<?php if(isset($filter2)) {echo $filter2;} else {echo '3';}?>" />
            
            <input type="hidden" name="controller" value="guruAuthor" />
            <input type="hidden" name="task" value="addtext" />
            <input type="hidden" name="cid" value="<?php echo $pid; ?>">
            <input type="hidden" name="action" value="<?php echo $action; ?>">
        </form> 
     
    </table>
	<input type="hidden" id="to_replace" value="<?php echo @$media_to_replace; ?>">
	<input type="hidden" id="the_screen_id" value="<?php echo @$pid; ?>">
</div>