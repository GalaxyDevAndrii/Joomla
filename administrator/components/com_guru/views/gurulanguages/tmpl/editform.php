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
	$doc =JFactory::getDocument();
	//These scripts are already been included from the administrator\components\com_guru\guru.php file
//$doc->addScript('components/com_guru/js/jquery.DOMWindow.js');

	$filename = JPATH_SITE . "/administrator/language/en-GB/en-GB.com_guru.ini" ;
	$handle = fopen( $filename, 'r' ) ;
	$admin_file = fread($handle, filesize( $filename ) ) ;
	fclose( $handle ) ;		
	
	$filename = JPATH_SITE . "/language/en-GB/en-GB.com_guru.ini" ;
	$handle = fopen( $filename, 'r' ) ;
	$front_file = fread($handle, filesize( $filename ) ) ;
	fclose( $handle ) ;	
	?>

		<script type="text/javascript" language="javascript">
		<!--

		Joomla.submitbutton = function(pressbutton){
		//function submitbutton(pressbutton) {
			submitform( pressbutton );
		}    
	
		-->
		</script>
        
        
 <div id="myModal" class="modal-small modal hide">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    </div>
    <div class="modal-body">
    </div>
</div>
 <div class="container-fluid">
          <a data-toggle="modal" data-target="#myModal" class="pull-right guru_video" onclick="showContentVideo('index.php?option=com_guru&controller=guruAbout&task=vimeo&id=27181372&tmpl=component')" class="pull-right guru_video" href="#">
                    <img src="<?php echo JURI::base(); ?>components/com_guru/images/icon_video.gif" class="video_img" />
                <?php echo JText::_("GURU_LANGUAGE_VIDEO"); ?>                  
          </a>
	</div>	
	<div class="clearfix"></div>
    <div class="well well-minimized">
		<?php echo JText::_("GURU_LANGUAGE_SETTINGS_DESCRIPTION1").'<a target="_blank" href="https://www.ijoomla.com/translations/guru-5-x">'.JText::_("GURU_LANGUAGE_SETTINGS_DESCRIPTION2").'</a>'.JText::_("GURU_LANGUAGE_SETTINGS_DESCRIPTION3"); ?>
	</div>       
<div class="widget-header widget-header-flat"><h5><?php echo  JText::_('GURU_TREELANGUAGES');?></h5></div>
	<div class="widget-body">
    	<div class="widget-main">	
         <form id="adminForm" name="adminForm" method="post" action="<?php echo JUri::root() ?>administrator/index.php">
            <table class="admintable">
                <tbody>
                    <tr>
                        <th><?php echo JText::_('GURU_LANG_ADMIN'); ?></th>
                        <th><?php echo JText::_('GURU_LANG_FRONT'); ?></th> 
                    </tr>
                    <tr>
                        <td> 
                            <textarea disabled style="width:auto!important;" cols="63" rows="40" name="admin_file"><?php echo stripslashes($admin_file) ; ?></textarea>	
                        </td>
                        <td> 
                          <textarea  disabled style="width:auto!important;" cols="63" rows="40" name="front_file"><?php echo stripslashes($front_file) ; ?></textarea>	
                        </td>
                    </tr>	
                </tbody>
            </table>
        
                <input type="hidden" name="images" value="" />                
                    <input type="hidden" name="option" value="com_guru" />
                    <input type="hidden" name="id" value="<?php //echo $plugin->id; ?>" />
                    <input type="hidden" name="task" value="" />
                <input type="hidden" name="controller" value="guruLanguages" />
                </form>
     </div>
   </div>
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