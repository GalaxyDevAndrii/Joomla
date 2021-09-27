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

?>
<div align="center">
		<?php
			if($media->type == "docs"){				
		?>
			<div>
		<?php
				$ext = explode(".", $media->local);
				$ext = $ext[count($ext) - 1];
				
				if(strtolower($ext) == "pdf"){
					$path = JURI::root().$config->docsin."/".$media->local;
		?>
        
        			<object style="width:<?php echo $media->width; ?>px; height:<?php echo $media->height; ?>px;" data="<?php echo $path; ?>" type="application/pdf">
                        <embed src="<?php echo $path; ?>" type="application/pdf" />
                    </object>
        <?php
				}
				else{
					$path = JURI::root().$config->docsin."/".$media->local;
					echo $media->code;
				}
		?>
			</div>
		<?php	
			}
			elseif($media->type == "Article"){
				$pattern = '/src="([^"]*)"/';
				preg_match($pattern, $media->code, $matches);
				$src = @$matches[1];
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

				if(isset($src)){
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