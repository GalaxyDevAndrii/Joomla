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
jimport( 'joomla.html.pagination' );
JHTML::_('behavior.tooltip');
JHtml::_('behavior.framework');

$doc =JFactory::getDocument();

$doc->addStyleSheet("components/com_guru/css/general.css");
//These scripts are already been included from the administrator\components\com_guru\guru.php file
//$doc->addScript('components/com_guru/js/jquery.DOMWindow.js');
//$doc->addScript('components/com_guru/js/open_modal.js');
//$editor  = JFactory::getEditor();
$editor  = new JEditor(JFactory::getConfig()->get("editor"));
$current_image = "";
$certificates_details =$this->certificates_details; 
$doc->addScript('components/com_guru/js/freecourse.js');

$upload_mb = guruAdminHelper::getUploadMaxSize();
$upload_mb*=1048576; //transform in bytes
$doc =JFactory::getDocument();
$cat_t_prop = "";
	$doc->addScriptDeclaration('
		jQuery.noConflict();
		jQuery(function(){
			function createUploader(){            
				var uploader = new qq.FileUploader({
					element: document.getElementById(\'fileUploader\'),
					action: \''.JURI::root().'administrator/index.php?option=com_guru&controller=guruConfigs&tmpl=component&format=raw&task=guru_file_uploader\',
					params:{
						folder:\'certificates\',
						mediaType:\'image\',
						size: 80,
						type: \''.$cat_t_prop.'\'
					},

					onSubmit: function(id,fileName,folder){
						jQuery(\'.qq-upload-list li\').css(\'display\',\'none\');
					},

					onComplete: function(id,fileName,responseJSON){
						if(responseJSON.success == true){
							jQuery(\'.qq-upload-success\').append(\'- <span style="color:#387C44;">Upload successful</span>\');
							if(responseJSON.locate) {
								jQuery(\'#view_imagelist23\').attr("src", "../"+responseJSON.locate +"/"+ fileName);
								jQuery("#background_image").css("display", "table-row");
								jQuery(\'#image\').val(responseJSON.locate +"/"+ fileName);
								jQuery(\'#background_image\').html(jQuery("<img>").attr("src", "../"+responseJSON.locate +"/"+ fileName));
							}
						}
					},
					allowedExtensions: [\'jpg\', \'jpeg\', \'png\', \'gif\', \'JPG\', \'JPEG\', \'PNG\', \'GIF\', \'xls\', \'XLS\'],
					sizeLimit: \''.$upload_mb.'\',
					multiple: false,
					maxConnections: 1
				});           
			}
			createUploader();
		});
	');

	$doc->addScript('components/com_guru/js/fileuploader.js');
	$doc->addStyleSheet('components/com_guru/css/fileuploader.css');

	$db = JFactory::getDbo();
	$sql = "SELECT `imagesin` FROM #__guru_config LIMIT 1";
	$db->setQuery($sql);
	$db->execute();
	$res = $db->loadResult();
?>

<script language="JavaScript" type="text/javascript" src="<?php echo JURI::base(); ?>components/com_guru/js/colorpicker.js"></script>

<script>

function ChangeLayoutC(file, poz){
	document.getElementById("background_image").innerHTML="<img id='view_imagelist23' data-file='"+file+"' src='<?php echo JURI::root().$res."/certificates/thumbs/";?>"+file+"' alt=''/>";
	document.forms['adminForm'].image.value = "<?php echo $res; ?>/certificates/thumbs/"+file;
	document.getElementById('background_image').style.display = 'table-row';
	document.getElementById('current_image').value = poz;
}

function ChangeTerm(nb){
	if(nb == 3 || nb == 5){
		document.getElementById('avg_certificate').style.display = 'table-row';
	}
	else{
		document.getElementById('avg_certificate').style.display = 'none';
	}
}

function windowopen(){
	window.open("<?php echo JUri::root();?>"+"index.php?option=com_guru&view=guruOrders&task=printcertificate&back=1");
}

function deleteImage(){
	if(confirm('<?php echo JText::_('GURU_DELETE_BG');?>')){
		image_selected = jQuery('#view_imagelist23').data('file');
		image_selected = encodeURIComponent(image_selected);
		var url = 'index.php?option=com_guru&controller=guruCertificate&tmpl=component&format=raw&task=delete_image_ajax&image_selected='+image_selected;
		jQuery.ajax({
			method: 'post',
			asynchronous: 'true',
			url: url,
			data: { 'do' : '1' },
			success: function() {
				current_image = document.getElementById('current_image').value;
				image_node = document.getElementById('td-image-'+current_image);
				image_node.parentNode.removeChild(image_node);
			
				document.getElementById('background_image').style.display = 'none';
				document.getElementById('view_imagelist23').src="components/com_guru/images/blank.png";
				document.getElementById('image').value="";
			}

		});	
		return true;
	}
	else{
		return false;
	}
}

function selectLibrary(val){
	if(val == 1){
		<?php
			if(!file_exists(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_guru'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'MPDF')){
		?>
				document.getElementById('message_lib').style.display="block";
		<?php
			}
		?>
	}
	else{
		document.getElementById('message_lib').style.display="none";
	}
}

</script>



<style>

	#rowsmedia {

		background-color:#eeeeee;

	}

	#rowsmedia tr{

		background-color:white;

	}

	#rowsmainmedia {

		background-color:#eeeeee;

	}

	#rowsmainmedia tr{

		background-color:#eeeeee;

	}

	.adminformCertificate{

	

		background-color: #FFFFFF;

		border: 1px solid #D5D5D5;

		border-collapse: collapse;

		margin: 8px 0 15px;

		width: 50%;

	}

	#colorpicker{
		z-index: 10000;
	}

</style>

<div id="g_certificate_manager">

    <form action="<?php echo JRoute::_('index.php?option=com_guru&controller=guruCertificate') ?>" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">

        

    <div id="myModal" class="modal-small modal hide">

        <div class="modal-header">

            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>

        </div>

        <div class="modal-body">

        </div>

    </div>

    <div class="container-fluid">

          <a data-toggle="modal" data-target="#myModal" onclick="showContentVideo('index.php?option=com_guru&controller=guruAbout&task=vimeo&id=52930940&tmpl=component')" class="pull-right guru_video" href="#">

                    <img src="<?php echo JURI::base(); ?>components/com_guru/images/icon_video.gif" class="video_img" />

                <?php echo JText::_("GURU_CERTIFICATE_VIDEO"); ?>                  

          </a>

    </div>	

    <div class="clearfix"></div>
		
         <div class="row-fluid">

             <ul class="nav nav-tabs">

                    <li class="nav-item active">
                    	<a class="nav-link active" href="#design" data-toggle="tab"><?php echo JText::_('GURU_CERTIFICATE_DESIGN');?></a>
                    </li>

                    <li>
                    	<a class="nav-link" href="#templates_cert" data-toggle="tab"><?php echo JText::_('GURU_CERTIFICATE_TEMPLATES');?></a>
                    </li>

             </ul>

             <div class="tab-content">

                <div class="tab-pane active" id="design">

                    <table style="padding-left:70px;">
						<tr>
							<td>
								<h4>
									<?php echo JText::_('GURU_CHOOSE_LIBRARY');?>
								</h4>
							</td>
							
							<td>
								<select id="library_pdf" name="library_pdf" style="margin-top:20px;" onchange="selectLibrary(this.value);">
                                	<option value="0" <?php if($certificates_details->library_pdf == "0"){echo 'selected="selected"';} ?>><?php echo JText::_("GURU_FPDF"); ?></option>
									<option value="1" <?php if($certificates_details->library_pdf == "1"){echo 'selected="selected"';} ?>><?php echo JText::_("GURU_MPDF"); ?></option>
								</select>
								<?php 
								$display ="none";
								if (!file_exists(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_guru'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'MPDF') && $certificates_details->library_pdf == 1 ){
									$display ="block";	
								}
								?>
								<span id="message_lib" class="alert" style="display:<?php echo $display ?>;">
									<a href='https://member.joomlart.com/downloads/ijoomla/guru/guru-bonuses' target="_blank">
										<?php echo "1. ".JText::_("GURU_DOWNLOAD_MPDF1"); ?>
									</a>
									<br />
									<?php echo "2. ".JText::_("GURU_DOWNLOAD_MPDF2"); ?>
									<br />
									<?php echo "3. ".JText::_("GURU_DOWNLOAD_MPDF3"); ?>
								</span>
							</td>
						</tr>
						
						<tr>
							<td>
								<input class="btn btn-primary guru_buynow" onclick="windowopen();" type="submit" id="previewcertificate" name="previewcertificate" value="<?php echo JText::_("GURU_CERTIFICATE_PREVIEW"); ?>" />
							</td>
						</tr>
					</table>

            <div style="overflow:auto;">

                <table>

                <tr>

                    <td style="padding-left:6px;">

                    <br/>

                        <b><?php echo JText::_('GURU_PREMADE_THEME_CERT');?></b>

                    </td>

                </tr>

                <tr>

                 <td>

                    <?php

                    echo "<table>";

                    //$path= '../images/stories/guru/certificates/thumbs/';
					
					$db = JFactory::getDbo();
					$sql = "SELECT `imagesin` FROM #__guru_config LIMIT 1";
					$db->setQuery($sql);
					$db->execute();
					$res = $db->loadResult();
					$path = JPATH_SITE.DIRECTORY_SEPARATOR.$res.DIRECTORY_SEPARATOR."certificates".DIRECTORY_SEPARATOR."thumbs".DIRECTORY_SEPARATOR;
					$path = str_replace("/", DIRECTORY_SEPARATOR, $path);
					$path_uri = JURI::root().$res."/certificates/thumbs/";
					
					if(!is_dir($path)){
						JFolder::create($path, "0755");
					}

                    $handle = opendir($path);
                    $continue = TRUE;
                    $all_images = array();

                    while ($file = readdir($handle)){
                        if(trim($file) != "" && trim($file) != "." && trim($file) != ".."){
                            $all_images[] = $file;
                        }
                    }

                    $i = 0;	
					
					$design_background_array = explode("/", $certificates_details->design_background);
					$design_background = $design_background_array[count($design_background_array) - 1];
					
                    while($continue){
                        echo '<tr>';
                        for($k=0; $k<6; $k++){
							$file = @$all_images[$i];

                            if($file !== FALSE && trim($file) != "" && trim($file) != "." && trim($file) != ".."){
                                echo "<td id='td-image-".$i."'>
                                		<img class='image-layout' data-file='".trim($file)."' data-image-number='".$i."' 
                                		src='".$path_uri.$file."'>
                                	</td>";
                                if(trim($file) == $design_background){
									$current_image = $i;
								}
								$i++;
                            }
                            else{
                                $continue = FALSE;
                            }
                        }
                        echo '</tr>';
                    }
                    closedir($handle);
                    echo "</table>";
                    ?>
                    	 	<script>
                    	 		jQuery('.image-layout').on('click', function(){
                    	 			var file = jQuery(this).data('file');
                    	 			var number = jQuery(this).data('image-number');
                    	 			ChangeLayoutC(file, number);
                    	 		})
                    	 	</script>

                </td>

               </tr> 

                

               <tr>

                   <td colspan="6">

                    <hr style="color:#D5D5D5; background-color:#F7F7F7; height:2px; border: none;" />

                   </td>

               </tr>

              </table>

           </div>   

          <table id="g_certificate_settings">

           <tr>

           <td style="padding-left:6px;">

           <br/>

            <b><?php echo JText::_('GURU_CUSTOMIZE_CERTIFICATE');?></b>

            </td>

           </tr>

           <tr>

            <td class="well-minimized" style="padding:6px;" colspan="5">

            <?php echo JText::_('GURU_CERTIFICATE_NOTE1');?><br/>

            <?php echo JText::_('GURU_CERTIFICATE_NOTE2');?>

            </td>

           </tr>

           <tr>

           <td style="padding-left:6px;">

           <?php echo JText::_('GURU_CERTIFICATE_BG');?>

           </td>

           <td>

           <div style="height:84px; width:112px;" id="background_image">

             <img id="view_imagelist23" name="view_imagelist" src='../<?php echo ($certificates_details->design_background) ? 
             $certificates_details->design_background : "components/com_guru/images/blank.png"; ?>'/>
				
           </div>

           </td>

            <td  style="padding-right:30px;" colspan="5">

                <input class="btn btn-warning" style="background-color:#EDE5D6; height: 30px; width:200px;"  type="button" value="<?php echo JText::_('GURU_REMOVEBG'); ?>" onclick="deleteImage();" id="deletebtn"/>

                <input type="hidden" name="image" id="image" value="<?php echo $certificates_details->design_background ; ?>" />&nbsp;

           </td>

           </tr>

           <tr>

           <td style="padding-left:6px;">

           <?php echo JText::_('GURU_UPLOAD_YOUR_OWN');?>

           </td>

            <td>

           

                <div id="fileUploader"></div>

            </td>

           </tr>

           <tr> 

            <td colspan="5">

                <span style="padding-left:140px; color:#CC0000"><?php echo JText::_('GURU_BG_IMAGE_SIZE');?></span>

            </td>

          </tr>

           <tr>

           <td style="padding-left:6px;">

          <?php echo JText::_('GURU_CERTIFICATE_BGC');?>

           </td>

           <td >

           <div style="float:left;">

            <a href="javascript:pickColor('pick_zdonecolor');" onClick="document.getElementById('show_hide_box').style.display='none';" id="pick_zdonecolor" style="border-radius: 4px 4px 4px 4px;

        float: left; height: 30px; margin-right: 10px; width: 110px; background-color:#<?php echo $certificates_details->design_background_color ?>">

                            &nbsp;&nbsp;&nbsp;

                            </a>

                            <input type="text" size="7" name="st_donecolor1" id="pick_zdonecolorfield" value="<?php echo $certificates_details->design_background_color ?>" onChange="if (this.value.length == 6) {relateColor('pick_zdonecolor', this.value);}" size="6" maxlength="6" onkeyup="if (this.value.length == 6) {relateColor('pick_zdonecolor', this.value); changeBcolor();}" />

                            <SCRIPT LANGUAGE="javascript">relateColor('pick_zdonecolor', getObj('pick_zdonecolorfield').value);</script>

                             <span id='show_hide_box'></span>

    

                           

           </div>

           </td>

           </tr>

           <tr>

           <td style="padding-left:6px;">

           <?php echo JText::_('GURU_CERTIFICATE_TEXT_COLOR');?>

           </td>

           <td>

           <div style="float:left;">

             <a href="javascript:pickColor('pick_ydonecolor');" onClick="document.getElementById('show_hide_box').style.display='none';" id="pick_ydonecolor" style="border-radius: 4px 4px 4px 4px;

        float: left; height: 30px; margin-right: 10px; width: 110px; background-color:#<?php echo $certificates_details->design_text_color?>">

                            &nbsp;&nbsp;&nbsp;

                            </a>

                            <input type="text" size="7" name="st_donecolor2" id="pick_ydonecolorfield" value="<?php echo $certificates_details->design_text_color?>" onChange="if (this.value.length == 6) {relateColor('pick_ydonecolor', this.value);}" size="6" maxlength="6" onkeyup="if (this.value.length == 6) {relateColor('pick_ydonecolor', this.value); changeBcolor();}" />

                            <SCRIPT LANGUAGE="javascript">relateColor('pick_ydonecolor', getObj('pick_ydonecolorfield').value);</script>

                             <span id='show_hide_box'></span>

    

                           

           </div>

           </td>

           </tr>

            <tr>

               <td style="padding-left:6px;">

               <?php echo JText::_('GURU_FONT');?>

    

               </td>

               <td>

           <select name="font" id="font">

                <option value="Arial" <?php if($certificates_details->font_certificate == "Arial"){echo 'selected="selected"';} ?> style="font-family : Arial">Arial</option>

                <option value="Courier" <?php if($certificates_details->font_certificate == "Courier"){echo 'selected="selected"';} ?> style="font-family : Courier">Courier</option>

                <option value="Tahoma" <?php if($certificates_details->font_certificate == "Tahoma"){echo 'selected="selected"';} ?> style="font-family : Tahoma">Tahoma</option>

                <option value="Times New Roman" <?php if($certificates_details->font_certificate == "Times New Roman"){echo 'selected="selected"';} ?> style="font-family : 'Times New Roman'">Times New Roman</option>

                <option value="Verdana" <?php if($certificates_details->font_certificate == "Verdana"){echo 'selected="selected"';} ?> style="font-family : Verdana">Verdana</option>

                <option value="Georgia" <?php if($certificates_details->font_certificate == "Georgia"){echo 'selected="selected"';} ?> style="font-family : Georgia">Georgia</option>

                <option value="Palatino Linotype" <?php if($certificates_details->font_certificate == "Palatino Linotype"){echo 'selected="selected"';} ?> style="font-family : Palatino Linotype">Palatino Linotype</option>

                <option value="Arial Black" <?php if($certificates_details->font_certificate == "Arial Black"){echo 'selected="selected"';} ?> style="font-family : Arial Black">Arial Black</option>

                <option value="Comic Sans MS" <?php if($certificates_details->font_certificate == "Comic Sans MS"){echo 'selected="selected"';} ?> style="font-family : Comic Sans MS">Comic Sans MS</option>

                <option value="Lucida Console" <?php if($certificates_details->font_certificate == "Lucida Console"){echo 'selected="selected"';} ?> style="font-family : Lucida Console">Lucida Console</option>
                
                <option value="sun-extA" <?php if($certificates_details->font_certificate == "sun-extA"){echo 'selected="selected"';} ?> style="font-family:sun-extA;">Sun-Ext</option>

           </select>

               </td>

           </tr>

           </tr>

           <tr>

            <td>

            <input class="btn btn-success" type="button" onclick="document.adminForm.task.value='save'; submitform();" name="button" value="Save">

            </td>

           </tr>

           </table>

          </div>

                

            <div class="tab-pane" id="templates_cert">

                <table>

                    <tr>

                        <td style="padding:5px;"><b><?php echo JText::_('GURU_TEMPLATESG_CERTIFICATE');?></b></td>

                    </tr>

                    <tr>

                        <td style="padding:5px;"><?php echo JText::_('GURU_TEMPLATEST_CERTIFICATE');?></td>

                    </tr>

                 </table>   

               

                  <table style="width:100%;">

                    <tr>

                        <td valign="top" width="100%" colspan="2">    

                            <div class="g_variables"> 

                                <table class="pull-left">

                                    <tr>

                                        <td class="span4" nowrap="nowrap"><?php echo JText::_('GURU_FIRSTNAME'); ?></td>

                                        <td><?php echo JText::_('GURU_FIRSTNAME2'); ?></td>

                                    </tr> 

                                    <tr>

                                        <td nowrap="nowrap"><?php echo JText::_('GURU_CERTIFICATE_ID'); ?></td>

                                        <td><?php echo JText::_('GURU_CERTIFICATE_ID2'); ?></td>

                                    </tr> 

                                    <tr>

                                        <td nowrap="nowrap"><?php echo JText::_('GURU_CERTIFICATE_URL'); ?></td>

                                        <td><?php echo JText::_('GURU_CERTIFICATE_URL2'); ?></td>

                                    </tr>

                                    <tr>

                                        <td nowrap="nowrap"><?php echo JText::_('GURU_LASTNAME'); ?></td>

                                        <td><?php echo JText::_('GURU_LASTNAME2'); ?></td>

                                    </tr> 

                                    <tr>

                                        <td nowrap="nowrap"><?php echo JText::_('GURU_PRODNAME'); ?></td>

                                        <td><?php echo JText::_('GURU_PRODNAME2'); ?></td>

                                    </tr> 

                                    <tr>

                                        <td nowrap="nowrap"><?php echo JText::_('GURU_AUTHOR_CERTIFICATE'); ?></td>

                                        <td><?php echo JText::_('GURU_AUTHOR_CERTIFICATE2'); ?></td>

                                    </tr>

                                    <tr>

                                        <td nowrap="nowrap"><?php echo JText::_('GURU_COMPANY'); ?></td>

                                        <td><?php echo JText::_('GURU_COMPANY2'); ?></td>

                                    </tr>                    

                                </table>

								 

                                <table class="pull-left">

                                     <tr>

                                        <td class="span4" nowrap="nowrap"><?php echo JText::_('GURU_AVG_SCORE_COURSE'); ?></td>

                                        <td><?php echo JText::_('GURU_AVG_SCORE_COURSE2'); ?></td>

                                    </tr> 

                                    <tr>

                                        <td nowrap="nowrap"><?php echo JText::_('GURU_SITEURL'); ?></td>

                                        <td><?php echo JText::_('GURU_SITEURL2'); ?></td>

                                    </tr> 

                                    <tr>

                                        <td nowrap="nowrap"><?php echo JText::_('GURU_COMPLETION_DATE'); ?></td>

                                        <td><?php echo JText::_('GURU_COMPLETION_DATE2'); ?></td>

                                    </tr>

                                    <tr>

                                        <td nowrap="nowrap"><?php echo JText::_('GURU_CERTIFICATE_COURSE_MSG'); ?></td>

                                        <td><?php echo JText::_('GURU_CERTIFICATE_COURSE_MSG2'); ?></td>

                                    </tr>

                                     <tr>

                                        <td nowrap="nowrap"><?php echo JText::_('GURU_CERTIFICATE_FINALSCORE'); ?></td>

                                        <td><?php echo JText::_('GURU_CERTIFICATE_FINALSCORE2'); ?></td>

                                    </tr> 

                                    <tr>

                                        <td nowrap="nowrap"><?php echo JText::_('GURU_SITENAME'); ?></td>

                                        <td><?php echo JText::_('GURU_SITENAME2'); ?></td>

                                    </tr>        

                                </table>

								<div class="clearfix"></div>

                            </div>

                            </td>

                           </tr>

                         </table>      

                <div class="clearfix"></div>

                <div style=" background-color:#F7F7F7; padding: 5px 7px; "><b><?php echo JText::_('GURU_CERTIFICATE_HTML'); ?></b></div>

                <table>

                <!--
                <tr>

                    <td style="width:850px;"><?php echo JText::_('GURU_CERTIFICATE_ONE1'); ?></td>

                <tr>
                -->

                 <tr>

                        <td valign="top" width="100%" colspan="4">

                            <?php echo $editor->display('certificate', $certificates_details->templates1, '100%', '350', '75', '20', false ); ?>

                        </td>

                 </tr>

                </table>

                <br /> <br />

               	<div style=" background-color:#F7F7F7; padding: 5px 7px; "><b><?php echo JText::_('GURU_CERTIFICATE_PDF'); ?></b></div>

                <table>
					<tr>
						<td valign="top" width="100%" colspan="4">
							<?php
								if(!isset($certificates_details->templatespdf)){
									$certificates_details->templatespdf = $certificates_details->templates1;
								}
							?>
							<?php echo $editor->display('certificatepdf', $certificates_details->templatespdf, '100%', '350', '75', '20', false ); ?>
						</td>
					</tr>
				</table>




                <br/>

                <div style=" background-color:#F7F7F7; padding:5px 7px;"><b><?php echo JText::_('GURU_CERTIFICATE_TWO'); ?></b></div>

                <table>



                <tr>

                    <td style="width:850px;"><?php echo JText::_('GURU_CERTIFICATE_TWO2'); ?></td>

                <tr>

                 <tr>

                        <td valign="top" width="100%" colspan="4">

                            <?php echo $editor->display('certificate_page', $certificates_details->templates2, '100%', '350', '75', '20', false ); ?>

                        </td>

                 </tr>

                </table>

                <br/>

                <div style=" background-color:#F7F7F7; padding:5px 7px; "><b><?php echo JText::_('GURU_CERTIFICATE_THREE'); ?></b></div>

                <table>

                <tr>

                    <td style="width:850px;" colspan="2"><?php echo JText::_('GURU_CERTIFICATE_THREE3'); ?></td>

                </tr>

                 <tr>

                   <td width="73px;"><?php echo JText::_('GURU_EM_SUBJECT'); ?></td>

                    <td><input type="text" size="100" id="subjectt3" name="subjectt3" value="<?php echo $certificates_details->subjectt3 ;?>" /></td><br/>

                </tr>

                <tr>

                        <td colspan="2">

                            <?php echo $editor->display('email_template', $certificates_details->templates3 , '100%', '350', '75', '20', false ); ?>

                        </td>

                 </tr>

                </table>

                <br/>

                <div style=" background-color:#F7F7F7; padding: 5px 7px; "><b><?php echo JText::_('GURU_CERTIFICATE_FOUR'); ?></b></div>

                <b><?php echo JText::_("GURU_TEMPLATESG_CERTIFICATE2"); ?></b>

                <table>

                    <tr>

                        <td><?php echo JText::_('GURU_CERTIFICATE_MESSAGE'); ?></td>

                        <td><?php echo JText::_('GURU_CERTIFICATE_MESSAGE2'); ?></td>

                    </tr>

                </table>

                

                <table>

                <tr>

                    <td style="width:850px;" colspan="2"><?php echo JText::_('GURU_CERTIFICATE_FOUR4'); ?></td>

                </tr>

                <tr>

                    <td width="73px;"><?php echo JText::_('GURU_EM_SUBJECT'); ?></td>

                    <td><input type="text" size="100" id="subjectt4" name="subjectt4" value="<?php echo $certificates_details->subjectt4 ;?>" /></td><br/>

                </tr>

                <tr>

                    <td colspan="2">

                        <?php echo $editor->display('email_mycertificate', $certificates_details->templates4 , '100%', '350', '75', '20', false ); ?>

                    </td>

                 </tr>

                </table>

                </table>

                </div>

               </div>

            </div>

        </div>

        <input type="hidden" name="option" value="com_guru" />
        <input type="hidden" name="task" value="savedesigncert" />
        <input type="hidden" name="controller" value="guruCertificate" />
        <input type="hidden" name="id" value="0" />
		<input type="hidden" name="current_image" id="current_image" value="<?php echo $current_image; ?>" />
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