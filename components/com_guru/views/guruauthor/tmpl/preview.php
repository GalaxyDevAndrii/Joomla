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
$config = $this->config;
$media	= $this->media;

$align = "center";
if($media->type == "text"){
	$align = "left";
}


JHtml::script('plugins/content/jw_allvideos/jw_allvideos/includes/js/mediaplayer/jwplayer.min.js');
JHtml::script('plugins/content/jw_allvideos/jw_allvideos/includes/js/wmvplayer/silverlight.js');
JHtml::script('plugins/content/jw_allvideos/jw_allvideos/includes/js/wmvplayer/wmvplayer.js');
JHtml::script('plugins/content/jw_allvideos/jw_allvideos/includes/js/quicktimeplayer/AC_QuickTime.js');

?>

<script type="text/javascript" language="javascript">
	document.body.className = document.body.className.replace("modal", "");
</script>

<div align="<?php echo $align; ?>">
		<?php 
			if($media->type == "docs"){
		?>
			<div>
		<?php		
				$path = JURI::root().$config->docsin."/".$media->local;					
				echo $media->code;			
		?>
			</div>
		<?php	
			}
			elseif($media->type == "Article"){
				$pattern = '/src="([^"]*)"/';
				preg_match($pattern, $media->code, $matches);
				@$src = $matches[1];
				for($i=0; $i<count($src); $i++){
					$src1 = JURI::root().$src;
					$media->code = str_replace($src, $src1, $media->code);
				}
				echo $media->code;

			}
			elseif($media->type == "text"){
				$pattern = '/src="([^"]*)"/';
				preg_match($pattern, $media->code, $matches);
				$src = @$matches[1];
				
				if(isset($src) && is_array($src) && count($src) > 0){
					for($i=0; $i<count($src); $i++){
						$src1 = JURI::root().$src;
						$media->code = str_replace($src, $src1, $media->code);
					}
				}
				
				if($media->show_instruction ==2){
					echo $media->code;	
					echo $media->name;		
				}
				elseif($media->show_instruction ==1){
					echo $media->code;	
					echo '<i>'.$media->instructions.'</i><br/>';
					echo $media->name;
				}	
				elseif($media->show_instruction ==0){
					echo '<i>'.$media->instructions.'</i><br/>';
					echo $media->code;	
					echo $media->name;	
				}	
			}
			else{
		?>		
			<div align="<?php echo $align; ?>">
		<?php	
	
				echo $media->code;
		?>
			</div>
		<?php	
			}	
		?>
</div>
