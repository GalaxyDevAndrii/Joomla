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

	$data = $this->data;
	$lists = $this->lists;
	$_row=$this->ad;
	$nullDate = 0;
	$livesite = JURI::base();	
	$configuration = guruAdminModelguruMedia::getConfig();	
	//$editorul  = JFactory::getEditor();
	$editorul  = new JEditor(JFactory::getConfig()->get("editor"));
	$data_post = JFactory::getApplication()->input->post->getArray();
	
	?>
	
	
		            
	<script language="javascript" type="text/javascript">
		<!--
		var flashFolder = '<?php echo $lists['flash_directory'] ?>';
		
		function changeDisplayFlash() {
			var imgSrc = document.adminForm['swf_url'].value;
			var pic_span = document.getElementById("swf_file");
			if (imgSrc != '') {
				imgsrc = flashFolder + imgSrc;
				
				pic_span.innerHTML="<OBJECT classid=\"clsid:D27CDB6E-AE6D-11cf-96B8-444553540000\" codebase=\"http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,0,0\" ID=\"banner\"><PARAM NAME=\"movie\" VALUE=\""+imgsrc+"?link=window=_self\"><param name=\"wmode\" value=\"transparent\"><PARAM NAME=\"quality\" VALUE=\"high\"><EMBED SRC=\""+imgsrc+"?link=window=_self\"  QUALITY=\"high\" wmode=\"transparent\" TYPE=\"application/x-shockwave-flash\" PLUGINSPAGE=\"http://www.macromedia.com/go/getflashplayer\"></EMBED></OBJECT>";
				
			} 
			else {
				pic_span.innerHTML="";
			}

		}
		
		function changefolder() {								
			submitbutton('changes');
		}
		
		function getSelectedValue2( frmName, srcListName ) {
			var form = eval( 'document.' + frmName );
			var srcList = form[srcListName];
			//alert(srcList);

			i = srcList.selectedIndex;
			if (i != null && i > -1) {
				return srcList.options[i].value;
			} else {
				return null;
			}
		}		
		
		Joomla.submitbutton = function(pressbutton){
		//function submitbutton(pressbutton) {
		//alert("~~~~"+pressbutton+"~~~~");
			var form = document.adminForm;
			<?php //echo $editorul->save( 'text' ); ?>
			if (pressbutton=='save' || pressbutton=='apply') { 

				if (form['name'].value == "") {
					alert( "<?php echo JText::_("GURU_MEDIA_JS_NAME_ERR");?>" );
					return false;
					} 
				else if (form['type'].value == 0) {
					alert( "<?php echo JText::_("GURU_MEDIA_JS_TYPE_ERR");?>" ); 
					return false;
					}
				else if (form['type'].value == 'image' && form['is_image'].value == 0) {
					alert( "<?php echo JText::_("GURU_MEDIA_JS_IMAGE_ERR");?>" );
					return false;
					}
				else if (form['type'].value == 'text' && document.getElementById('textblock').value =='') {
					alert( "<?php echo JText::_("GURU_MEDIA_JS_TEXT_ERR");?>" );
					return false;
					}					
				else {
					submitform( pressbutton );
					if(pressbutton!='apply') {
							window.top.setTimeout('window.parent.document.getElementById("sbox-window").close()', 200);
						}
					}
			}
			else 
				{
					if(pressbutton!='apply') {
						window.top.setTimeout('window.parent.document.getElementById("sbox-window").close()', 200);
						}
					submitform( pressbutton );
				}	
		}
        function change_radio_code() {
	       document.getElementById('source_code').checked = 'checked';
		   document.getElementById('source_url').checked = '';
		   document.getElementById('source_local').checked = '';
		   document.getElementById('was_uploaded').value = 0;
		   document.getElementById('to_hide_row').style.display = "none";
        }
        function change_radio_url() {
	       document.getElementById('source_code').checked = '';
		   document.getElementById('source_url').checked = 'checked';
		   document.getElementById('source_local').checked = '';
		   document.getElementById('was_uploaded').value = 0;
		   document.getElementById('to_hide_row').style.display = "none";
        }	
        function change_radio_local() {
	       document.getElementById('source_code').checked = '';
		   document.getElementById('source_url').checked = '';
		   document.getElementById('source_local').checked = 'checked';
		   document.getElementById('was_uploaded').value = 0;
		   document.getElementById('to_hide_row').style.display = "none";
        }	
		function show_hidden_row() {
			document.getElementById('to_hide_row').style.display = '';  
			document.getElementById('was_uploaded').value = 1;		
		}		
		function hide_hidden_row() {
			document.getElementById('to_hide_row').style.display = 'none';  
			document.getElementById('was_uploaded').value = 0;		
		}		
		//--> 
		
		function on_over_size(id){
			document.getElementById(id).style.background = '#D8E6FB';			
		}
		
		function on_out_size(id){
			if((id=='ysize1')&&(document.getElementById('width_v').value==480)){
				document.getElementById(id).style.background = '#D8E6FB';
				return true;
			}
			if((id=='ysize2')&&(document.getElementById('width_v').value==560)){
				document.getElementById(id).style.background = '#D8E6FB';
				return true;
			}
			if((id=='ysize3')&&(document.getElementById('width_v').value==630)){
				document.getElementById(id).style.background = '#D8E6FB';
				return true;
			}
			if((id=='ysize4')&&(document.getElementById('width_v').value==853)){
				document.getElementById(id).style.background = '#D8E6FB';
				return true;
			}
			if((id=='ysize5')&&(document.getElementById('width_v').value==212)){
				document.getElementById(id).style.background = '#D8E6FB';
				return true;
			}
			if((id=='ysize6')&&(document.getElementById('width_v').value==320)){
				document.getElementById(id).style.background = '#D8E6FB';
				return true;
			}
			document.getElementById(id).style.background = '#CCCCCC';			
		}
		
		function on_click_size(id){
			var height, width;
			document.getElementById('ysize1').style.background = '#CCCCCC';			
			document.getElementById('ysize2').style.background = '#CCCCCC';			
			document.getElementById('ysize3').style.background = '#CCCCCC';			
			document.getElementById('ysize4').style.background = '#CCCCCC';		
			document.getElementById('ysize5').style.background = '#CCCCCC';						
			document.getElementById('ysize6').style.background = '#CCCCCC';					
			document.getElementById(id).style.background = '#D8E6FB';	
			if(id=='ysize1'){
				width=480;
				height=295;				
			}
			if(id=='ysize2'){
				width=560;
				height=340;				
			}
			if(id=='ysize3'){
				width=630;
				height=385;				
			}
			if(id=='ysize4'){
				width=853;
				height=505;				
			}
			if(id=='ysize5'){
				width=212;
				height=172;				
			}
			if(id=='ysize6'){
				width=320;
				height=265;				
			}
			document.getElementById('width_v').value = width;		
			document.getElementById('height_v').value = height;			
			//alert(width);
			//alert(height);
		}
		
		function close_gb(){
			//window.parent.setTimeout('document.getElementById("sbox-window").close()', 1);
			window.parent.close_modal();
		}

		</script>
<?php 
	$document = JFactory::getDocument();
	$document->addStyleSheet("components/com_guru/css/ytb.css"); 
?>

<form action="<?php echo JUri::root(true) ?>/administrator/index.php?option=com_guru&controller=guruMedia&task=editsbox&tmpl=component&cid[]=" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">

<fieldset class="adminform">
<div class="well"><?php if(isset($_row->id)) {echo  JText::_('GURU_MEDIADET_EDIT');} else{echo  JText::_('GURU_MEDIADET_NEW');} ?></div>



<div style="float:right;">
	<div id="toolbar" class="btn-toolbar pull-right no-margin">
    	<div id="toolbar-apply" class="btn-wrapper">
			<button class="btn btn-success" onclick="javascript:submitbutton('save');">
                <span class="icon-apply icon-white"></span>
                Save
			</button>
		</div>
		<div id="toolbar-save" class="btn-wrapper">
			<button class="btn btn-success" onclick="javascript:submitbutton('apply');">
				<span class="icon-save"></span>
				Save &amp; Close
			</button>
		</div>
		<div id="toolbar-cancel" class="btn-wrapper">
			<button class="btn" onclick="javascript:close_gb2();">
                <span class="icon-cancel"></span>
                Close
			</button>
		</div>
	</div>
</div>


<table border="0" width="100%" class="adminform">
		<tr>
			<td width="20%"><?php echo JText::_('GURU_TYPE'); ?>:<font color="#ff0000">*</font> </td>
			<td width="80%">
			<select name="type" onChange=" document.adminForm.submit(); ">
			<?php /*	<select name="type" onChange="javascript: displayblock(this.value); "> */ ?>
			        <option value="0"><?php echo JText::_("GURU_SELECT_TYPE"); ?></option>
					<option value="video" <?php if ($_row->type=='video') echo 'selected="selected"';?> ><?php echo JText::_('GURU_MEDIATYPEVIDEO');?></option>
			        <option value="audio" <?php if ($_row->type=='audio') echo 'selected="selected"';?>><?php echo JText::_('GURU_MEDIATYPEAUDIO');?></option>
			        <option value="docs" <?php if ($_row->type=='docs') echo 'selected="selected"';?>><?php echo JText::_('GURU_MEDIATYPEDOCS');?></option>
			        <option value="url" <?php if ($_row->type=='url') echo 'selected="selected"';?>><?php echo JText::_('GURU_MEDIATYPEURL');?></option>
                    <option value="Article" <?php if ($_row->type=='Article') echo 'selected="selected"';?>><?php echo JText::_('GURU_MEDIATYPEARTICLE');?></option>
					<option value="image" <?php if ($_row->type=='image') echo 'selected="selected"';?>><?php echo JText::_('GURU_MEDIATYPEIMAGE');?></option>					
					<option value="text" <?php if ($_row->type=='text') echo 'selected="selected"';?>><?php echo JText::_('GURU_MEDIATYPETEXT');?></option>
					<option value="quiz" <?php if ($_row->type=='quiz') echo 'selected="selected"';?>><?php echo JText::_('GURU_MEDIATYPEQUIZ');?></option>
			    </select>
			</td>
		</tr>

		<tr>
			<td width="20%" nowrap> <?php echo JText::_('Name');?>:<font color="#ff0000">*</font> </td>
			<td width="80%">
				<input class="formField" type="text" name="name" id="name" size="60" value="<?php echo $_row->name; ?>">
			</td>
		</tr>

		<tr>
			<td width="20%"><?php echo JText::_('NEWADAPPROVED');?>:</td>
			<td width="80%">
				<?php echo $lists['approved'];?>
			</td>
		</tr>		

		<tr>
			<td width="20%"><?php echo JText::_('GURU_INSTR');?>:</td>
			<td width="80%">
				<textarea class="formField" type="text" name="instructions" rows="2" cols="60" ><?php echo stripslashes($_row->instructions); ?></textarea>
			</td>
		</tr>
		

		<?php 
		$style = ' style="display:none"';
		$styleupload = ' style="display:none"';
		
		$type_of = '';
		if ($_row->type=='video' || $_row->type=='audio' || $_row->type=='docs') $style = '' ;
		if($_row->type=='video' || (isset($data_post['type']) && $data_post['type'] =='video'))
			{
			 	$type_of = JText::_('GURU_MEDIATYPEVIDEO');
				$type_of2 =  JText::_('GURU_MEDIATYPEVIDEOS');
			 	$code_of_file = ' style=""';
				$display_list_of_dir = $lists['image_dir'];
				$display_list_of_files = $lists['image_url'];
				$folder_of_files = $configuration->videoin;
				$styleupload = ' ';
			 }
		if($_row->type=='audio' || (isset($data_post['type']) && $data_post['type'] =='audio'))
			{
				$type_of = JText::_('GURU_MEDIATYPEAUDIO');
				$type_of2 = JText::_('GURU_MEDIATYPEAUDIOS');
				$code_of_file = ' style=""';
				$display_list_of_dir = $lists['audio_dir'];
				$display_list_of_files = $lists['audio_url'];
				$folder_of_files = $configuration->audioin;
				$styleupload = ' ';
			} 
		if($_row->type=='docs' || (isset($data_post['type']) && $data_post['type'] =='docs'))
			{
				$type_of = JText::_('GURU_MEDIATYPEDOCS');	
				$type_of2 = JText::_('GURU_MEDIATYPEDOCSS');	
				$code_of_file = ' style="display:none"';
				$display_list_of_dir = $lists['docs_dir'];
				$display_list_of_files = $lists['docs_url'];
				$folder_of_files = $configuration->docsin;	
				$styleupload = ' ';								
			}
		if($_row->type=='image' || (isset($data_post['type']) && $data_post['type'] =='image'))
			{
				$styleupload = ' ';								
			}			
		?>
		
		<tr id="videoblock" <?php echo $style; ?>>
		<td colspan="2">
		<table width="100%" border="0" cellpadding="0" cellspacing="0">
		<tr>
			<td>
			<?php echo $type_of; ?>:<font color="#ff0000">*</font> 
			</td>
			<td>
			 <?php echo $type_of2; ?><br/>
			 <table cellspacing="0" cellpadding="5" border="0" width="100%">
		      <tbody>
		      
			  <tr bgcolor="#eeeeee" valign="top" id="code_of_file" <?php echo $code_of_file;?>>
		       
			   <?php echo $_row->source;?>
			   
			    <td width="5%"><input id="source_code" <?php if($_row->source=='code') echo 'checked="checked"';?> type="radio" value="code" name="source"/></td>
		        <td width="28%"><?php echo $type_of.' '.JText::_('GURU_MEDIATYPECODE');?></td>
		        <td width="67%"><textarea cols="35" name="code" onKeyPress="javascript:change_radio_code()" onPaste="javascript:change_radio_code()"><?php echo stripslashes($_row->code); ?></textarea></td>
		      </tr>
		      
			  <tr bgcolor="#ffffff" valign="top">
		        <td width="5%"><input id="source_url" type="radio" <?php if($_row->source=='url') echo 'checked="checked"';?> value="url" name="source" onChange="javascript:hide_hidden_row();"/></td>
		        <td width="28%"><?php echo $type_of.' '.JText::_('GURU_MEDIATYPEURLURL');?></td>
		        <td width="67%"><input type="text" onKeyPress="javascript:change_radio_url()" onPaste="javascript:change_radio_url()" size="40" value="<?php echo $_row->url;?>" name="url"  onChange="javascript:hide_hidden_row();"/></td>
		      </tr>
			  
		      <tr bgcolor="#eeeeee" valign="top">
		        <td><input id="source_local" type="radio" <?php if($_row->source=='local' && $_row->uploaded==0) echo 'checked="checked"';?> value="local" name="source"  onChange="javascript:hide_hidden_row();"/></td>
		        <td><?php echo JText::_('GURU_MEDIATYPECHOOSE').' '.$type_of2;?><br/>
		            <font size="1">(<?php echo JText::_("GURU_UPLOAD_WITH_TO_FTP"); ?> <?php echo $folder_of_files; ?> folder) </font></td>
		        <td><?php echo $display_list_of_dir; //$lists['image_dir'];?>
		        <?php 
				if(isset($now_selected)&&($now_selected!='')) {echo str_replace($now_selected.'"',$now_selected.'" selected="selected"',$display_list_of_files); }//$lists['image_url'];
				else echo $display_list_of_files;
				?></td>
		      </tr>
			  
			  <tr bgcolor="#eeeeee" valign="top" >
			  	<td rowspan="2">
					<input id="source_local" type="radio" <?php if($_row->source=='local' && $_row->uploaded==1) echo 'checked="checked"';?> value="local" name="source" onChange="javascript:show_hidden_row();" />
				</td>
				<td colspan="2" valign="top">
					<table>
						<tr valign="top" id="uploadblock" <?php //echo $styleupload; ?>>
							<td width="9%"><?php echo JText::_('Upload');?>:</td>
							<td width="91%">
								<input class="inputbox" type="file" name="image_file" size="35" value="" />&nbsp;&nbsp;<input class="inputbox" type="submit" value="Upload" onclick="return UploadImage();">
								<script  language="javascript" type="text/javascript">
									function UploadImage() {								
									
									var fileControl = document.adminForm.image_file;
									/*
									var thisext = fileControl.value.substr(fileControl.value.lastIndexOf('.'));
									if (thisext != ".jpeg" && thisext != ".jpg" && thisext != ".gif" && thisext != ".png" && thisext != ".JPEG" && thisext != ".JPG" && thisext != ".GIF" && thisext != ".PNG")  
									{ alert('<?php echo JText::_('Invalid image type!');?>');
									  return false;
									}  
									
									if (fileControl.value) { */
										document.adminForm.image.value = fileControl.value;
										document.adminForm.task.value = 'uploadsbox';
										document.getElementById('was_uploaded').value = 1;
										document.getElementById('to_hide_row').style.display = "";
										return true;
									/*}
									
									return false;*/
									}
								</script>
							</td>
						</tr>
							
						<tr valign="top">
							<td width="9%"></td>
							<td width="91%"><?php
								$UPLOAD_MAX_SIZE = guruAdminHelper::getUploadMaxSize();

								echo "<font color='#FF0000'>";
								echo JText::_('GURU_MEDIA_MAX_UPL_V_1')." ";
								echo $UPLOAD_MAX_SIZE."M ";
								echo JText::_('GURU_MEDIA_MAX_UPL_V_2');
								echo "</font>";
							?></td>
						</tr>

						<tr id="to_hide_row" style="display:none">
							<td>
							<?php echo 'Uploaded file'; //JText::_('GURU_PRODCIMG');?>:
							</td>
							<td>
							<?php 
								if(isset($data_post['image'])) $the_file = $data_post['image']; else $the_file = $_row->local;
							?>
							<?php echo $the_file;  /* <img src="<?php echo str_replace('/administrator','/',JURI::base()); echo $configuration->imagesin.'/';?><?php if(isset($data_post['image'])) echo $data_post['image']; else echo $categ->image;?>" alt="" />*/ ?>
							</td>							
						</tr>
					</table>
				</td>
			  </tr>
			  
			  <?php if($_row->type=='audio' || (isset($data_post['type']) && $data_post['type'] =='audio')) {?>
			  <!-- #eeeeee -->
			  <tr bgcolor="#FFFFFF" valign="top" id="player_size" <?php //echo $code_of_file;?>>
		        <td colspan="2"><table cellspacing="0" cellpadding="5" border="0" width="100%">
		            <tbody><tr>
		              <td width="10%"><?php echo JText::_('GURU_MEDIA_SIZE'); ?></td>
		              <td width="90%"><input type="text" size="10" value="<?php if(isset($_row->id)&&($_row->id>0)) {echo $_row->width;} else {echo "250";}?>" name="width_a"/><input type="hidden" size="10" value="50" name="height_a"/>
		            <?php echo JText::_('GURU_MEDIA_WIDTH'); ?></td>
		            </tr>
		        </tbody></table></td>
		        <td>&nbsp;</td>
		      </tr>
			  <?php } elseif($_row->type=='docs' || (isset($data_post['type']) && $data_post['type'] =='docs')){?>

			    <tr bgcolor="#eeeeee" valign="top" id="player_size" <?php //echo $code_of_file;?>>
		        <td colspan="2">
					<table>
						<tr>
							<td width="10%">
								<?php echo JText::_('GURU_MEDIA_DISPL_DOC'); ?>
							</td>
							<td><script type="text/javascript">
								function wh(y){
									if(y==1){
										document.getElementById('whdoc').style.display='';
									} 
									if (y==0) {
										document.getElementById('whdoc').style.display='none';
									}	
								}
							</script>
								<select id="display_as" name="display_as">
									<option value="wrapper" onclick="javascript:wh(1)"><?php echo JText::_('GURU_MEDIA_DISPL_DOC_W'); ?></option>
									<option value="link" onclick="javascript:wh(0)" <?php if($_row->type=='docs' && $_row->width==1) {echo 'selected = "selected"'; $sel_link=1;}?>><?php echo JText::_('GURU_MEDIA_DISPL_DOC_L'); ?></option>
								</select>
							</td>
						</tr>
					</table>
						
				<table id="whdoc" border="0" <?php if(isset($sel_link)){ echo 'style="display:none;"';}?>>
		            <tbody><tr>
		              <td><?php echo JText::_('GURU_MEDIA_SIZE'); ?>&nbsp;&nbsp;<input type="text" size="10" value="<?php if($_row->width>99){echo $_row->width;}else {echo "600";} ?>" name="width"/>
		            X
		              <input type="text" size="10" value="<?php if($_row->height>99){echo $_row->height;}else {echo "800";}?>" name="height"/>
		            <?php echo JText::_('GURU_MEDIA_WIDTH_HEIGHT'); ?></td><td>&nbsp;</td>
		            </tr>
		        	</tbody>
				</table>
				</td>
		      </tr>	
			  
			  <?php } else {?>
			  		  
			  <tr bgcolor="#eeeeee" valign="top" id="player_size" <?php echo $code_of_file;?>>
		        <!--<td> </td>-->
		        <td colspan="2"><table cellspacing="0" cellpadding="5" border="0" width="100%">
		         <tbody>
					<tr>
						<td width="10%" valign="top"><?php echo JText::_('GURU_MEDIA_SIZE'); ?></td>
						<td width="90%"><input type="hidden" id="width_v" size="10" value="<?php echo $_row->width;?>" name="width_v"/><input type="hidden" id="height_v" size="10" value="<?php echo $_row->height;?>" name="height_v"/>
						<div class="ycontainer">
						
						<a id="ya5" class="" onclick="javascript:on_click_size('ysize5');" onmouseover="javascript:on_over_size('ysize5');" onmouseout="javascript:on_out_size('ysize5');">
							<div class="">
								<span id="" class="centertext">212x172</span>
								<div id="ysize5" <?php 
									if(isset($_row->height)&&($_row->height=='172')){
										echo 'style="background-color: #D8E6FB;"';
									}
								?>>
									<div class="yborder"></div>
								</div>
							</div>
						</a>
						
						<a id="ya6" class="" onclick="javascript:on_click_size('ysize6');" onmouseover="javascript:on_over_size('ysize6');" onmouseout="javascript:on_out_size('ysize6');">
							<div class="">
								<span id="" class="centertext">320x265</span>
								<div id="ysize6" <?php 
									if(isset($_row->height)&&($_row->height=='265')){
										echo 'style="background-color: #D8E6FB;"';
									}
								?>>
									<div class="yborder"></div>
								</div>
							</div>
						</a>
						
						<a id="ya1" class="" onclick="javascript:on_click_size('ysize1');" onmouseover="javascript:on_over_size('ysize1');" onmouseout="javascript:on_out_size('ysize1');">
							<div class="">
								<span id="" class="centertext">480x295</span>
								<div id="ysize1" <?php 
									if(isset($_row->height)&&($_row->height=='295')){
										echo 'style="background-color: #D8E6FB;"';
									}
								?>>
									<div class="yborder"></div>
								</div>
							</div>
						</a>
						
						<a id="ya2" class="" onclick="javascript:on_click_size('ysize2');" onmouseover="javascript:on_over_size('ysize2');" onmouseout="javascript:on_out_size('ysize2');">
							<div class="">
								<span id="" class="centertext">560x340</span>
								<div id="ysize2" <?php 
									if(isset($_row->height)&&($_row->height=='340')){
										echo 'style="background-color: #D8E6FB;"';
									}
								?>>
									<div class="yborder"></div>
								</div>
							</div>
						</a>
						
						<a id="ya3" class="" onclick="javascript:on_click_size('ysize3');" onmouseover="javascript:on_over_size('ysize3');" onmouseout="javascript:on_out_size('ysize3');">
							<div class="">
								<span id="" class="centertext">630x385</span>
								<div id="ysize3" <?php 
									if(isset($_row->height)&&($_row->height=='385')){
										echo 'style="background-color: #D8E6FB;"';
									}
								?>>
									<div class="yborder"></div>
								</div>
							</div>
						</a>

						<a id="ya4" class="" onclick="javascript:on_click_size('ysize4');" onmouseover="javascript:on_over_size('ysize4');" onmouseout="javascript:on_out_size('ysize4');">
							<div class="">
								<span id="" class="centertext">853x505</span>
								<div id="ysize4" <?php 
									if(isset($_row->height)&&($_row->height=='505')){
										echo 'style="background-color: #D8E6FB;"';
									}
								?>>
									<div class="yborder"></div>
								</div>
							</div>
						</a>
	
						<input type="hidden" id="video_size" name="video_size" value="" />

						</div>
						</td>
					</tr>
		        </tbody></table></td>
		      </tr>
			  <?php } ?>
		    </tbody></table>
			</td>
		
		</table>
		</td>
		</tr>	
				
		<?php 
		$style2 = ' style="display:none"';
		if ($_row->type=='url') { $style2 = '' ;?>		
		<tr id="urlblock" <?php echo $style2; ?>>
		<td colspan="2">
		<table width="100%" border="0" cellpadding="0" cellspacing="0">
		<tr>
			<td width="20%"><?php echo 'URL';?>:</td>
			<td width="80%"><input type="text" size="40" value="<?php echo $_row->url;?>" name="url"/>
			</td>
		</tr>
		</table>
		</td>
		</tr>
		<?php } ?>
        <?php 
		$style6 = ' style="display:none"';
		if ($_row->type=='Article') { $style6 = '' ;?>		
		<tr id="artblock" <?php echo $style6; ?>>
		<td colspan="2">
		<table width="100%" border="0" cellpadding="0" cellspacing="0">
		<tr>
			<td width="20%"><?php echo JText::_('GURU_MEDIATYPEARTICLE');?>:</td>
			<td width="80%"><input type="text" size="40" value="<?php echo $_row->code;?>" name="Article"/>
			</td>
		</tr>
		</table>
		</td>
		</tr>
        <?php } ?>
		<?php 
		$style3 = ' style="display:none"';
		if ($_row->type=='image') { $style3 = '' ;?>		
		<tr id="imageblock" <?php echo $style3; ?>>
		<td colspan="2">
		<table width="100%" border="0" cellpadding="0" cellspacing="0">
		<tr>
			<td width="20%">
								
				<?php  echo JText::_('GURU_GEN_IM_FIS');?>:
			</td>	
			<td width="80%">
				<?php 
				$media_fullpx = 200;
				$media_prop = 'w';
				$is_image = 0;
				if($_row->width>0 && $_row->height == 0)
					{
						$media_fullpx = $_row->width;
						$media_prop = 'w';
						$is_image = 1;
					}	
				if($_row->height >0 && $_row->width == 0)
					{
						$media_fullpx = $_row->height;					
						$media_prop = 'h';
						$is_image = 1;
					}	
				?>		
				<input type="text" size="8" name="media_fullpx" value="<?php echo $media_fullpx;?>" /> px 
					<select name="media_prop">
						<option value="w" <?php if($media_prop=='w') echo 'selected="selected"'; ?>><?php  echo JText::_('GURU_PROPW');?></option>
						<option value="h" <?php if($media_prop=='h') echo 'selected="selected"'; ?>><?php  echo JText::_('GURU_PROPH');?></option>
					</select>	
				<input type="hidden" id="is_image" name="is_image" value="<?php echo $is_image;?>" />									
			</td>
		</tr>
		<tr>
			<td>
			<?php echo JText::_('GURU_PRODCIMG');?>:
			</td>
			<td>
			<?php 
				if(isset($data_post['image'])) $the_image = $data_post['image']; else $the_image = $_row->local;
				// generating thumb image - start
				$img_size = @getimagesize(JPATH_SITE.DIRECTORY_SEPARATOR.$configuration->imagesin.'/'.$the_image);
				$img_width = $img_size[0];
				$img_height = $img_size[1];
				if($img_width>0 && $img_height>0)
				{ 
					if($media_prop=='w')
						{
							$thumb_width = $media_fullpx;
							$thumb_height = $img_height / ($img_width/$media_fullpx);
						}
					elseif($media_prop=='h')	
						{
							$thumb_height = $media_fullpx;
							$thumb_width = $img_width / ($img_height/$media_fullpx);		
						}
					
					$image_to_thumb = JPATH_SITE.DIRECTORY_SEPARATOR.$configuration->imagesin.'/'.$the_image;
					$image_full_thumb = guruAdminHelper::create_thumbnails($image_to_thumb, $thumb_width, $thumb_height,$img_width,$img_height, 'full_');
					//echo JPATH_SITE.DIRECTORY_SEPARATOR.'images'.DIRECTORY_SEPARATOR.'stories'.DIRECTORY_SEPARATOR.$image_full_thumb;
					$media_image = '<img style="margin:5px;" border="0" alt="" src="'.JURI::root().$configuration->imagesin.DIRECTORY_SEPARATOR.$image_full_thumb.'" />';
				}
				else
					$media_image = 'none';
				// generating thumb image - stop				
			?>
			<?php echo $media_image; /* <img src="<?php echo str_replace('/administrator','/',JURI::base()); echo $configuration->imagesin.'/';?><?php if(isset($data_post['image'])) echo $data_post['image']; else echo $categ->image;?>" alt="" />*/ ?>
			</td>
		</tr>			
		</table>
		</td>
		</tr>
		<?php } ?>	
		<?php 
		$style4 = ' style="display:none"';
		if ($_row->type=='text') { $style4 = '' ;?>		
		<tr id="textblock" <?php echo $style4; ?>>
		<td colspan="2">
		<table width="100%" border="0" cellpadding="0" cellspacing="0">
		<tr>
			<td width="20%"><?php echo JText::_('GURU_MEDIATYPETEXT');?>:<font color="#ff0000">*</font></td>
			<td width="80%">
				<?php if (isset($data_post['text'])) $editor_text = stripslashes($data_post['text']); else $editor_text = stripslashes($_row->code);
				echo $editorul->display( 'text', ''.$editor_text,'100%', '300px', '20', '60' );			
				?>
			</td>
		</tr>
		</table>
		</td>
		</tr>
		<?php } ?>	
		<?php 
		$style5 = ' style="display:none"';
		if ($_row->type=='quiz') { $style5 = '' ;?>		
		<tr id="quizblock" <?php echo $style5; ?>>
		<td colspan="2">
		<table width="100%" border="0" cellpadding="0" cellspacing="0">
		<tr>
			<td width="20%"><?php echo JText::_('GURU_MEDIATYPEQUIZ');?>:<font color="#ff0000">*</font></td>
			<td width="80%">
				<?php 
				$qid = '';
				if($_row->source > 0)	
					$qid = $_row->source;
				if(isset($data_post['qid']))
					$qid = intval($data_post['qid']);
				$quiz_list = guruAdminModelguruMedia::generate_quiz_list($qid);
				echo $quiz_list; ?>
			</td>
		</tr>
		</table>
		</td>
		</tr>
		<?php } ?>										
		</table>	
		<input type="hidden" name="option" value="com_guru" />
		<input type="hidden" name="task" value="editsbox" />
		<input type="hidden" name="id" value="<?php echo $_row->id;?>" />
		<input type="hidden" name="image" value="<?php if(isset($data_post['image'])) echo $data_post['image']; else echo $_row->local;?>" />
		<input type="hidden" name="controller" value="guruMedia" />
		<input type="hidden" name="was_uploaded" id="was_uploaded" value="1" />
		</form>
</fieldset>		
