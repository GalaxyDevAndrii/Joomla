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
	echo "<table>";
	
	$path= JPATH_SITE.DIRECTORY_SEPARATOR.'images/stories/guru/certificates/thumbs/';
	
	$handle=opendir($path);
	$continue = TRUE;
	$all_images = array();
	
	while (($file = readdir($handle))!==false){
		if(trim($file) != "" && trim($file) != "." && trim($file) != ".."){
			$all_images[] = $file;
		}
	}
		
	$i = 0;	
	while($continue){
		echo '<tr>';
		for($k=0; $k<5; $k++){
			$file = $all_images[$i];
			if($file !== FALSE && trim($file) != "" && trim($file) != "." && trim($file) != ".."){
				echo "<td><img onClick=\"javascript:ChangeLayoutC('$file');\" src=$path$file></td>";
				$i++;
			}
			else{
				$continue = FALSE;
			}
		}
		echo '</tr>';
	}
	closedir($handle);
	echo '</table>';
?>