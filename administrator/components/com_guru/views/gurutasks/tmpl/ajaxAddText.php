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

$database = JFactory::getDBO();
$q = "SELECT * FROM #__guru_config WHERE id = '1'";
$database->setQuery($q);
$configs = $database->loadObjectList();

$data_get = JFactory::getApplication()->input->get->getArray();

$q = "SELECT * FROM #__guru_media WHERE id = ".intval($data_get['id']);
$database->setQuery($q);
$result = $database->loadObjectList();
	
$the_media = $result["0"];


if($the_media->type == 'text'){
	$media = $the_media->code;
	if(strpos($media, "src=") !== FALSE){
		$the_base_link = explode('administrator/', $_SERVER['HTTP_REFERER']);
		$the_base_link = $the_base_link[0];
		$media = str_replace('src="', 'src="'.$the_base_link, $media);
	}
}
if($the_media->type == 'docs'){	
	$the_base_link = explode('administrator/', $_SERVER['HTTP_REFERER']);
	$the_base_link = $the_base_link[0];				
	
	//$media = 'The selected element is a text file that can\'t have a preview';
	$media = JText::_("GURU_NO_PREVIEW");
	
	if($the_media->source == 'local' && (substr($the_media->local,(strlen($the_media->local)-3),3) == 'txt' || substr($the_media->local,(strlen($the_media->local)-3),3) == 'pdf') && $the_media->width == 0)
	$media='<div class="contentpane">
					<iframe id="blockrandom"
						name="iframe"
						src="'.$the_base_link.'/'.$configs->docsin.'/'.$the_media->local.'"
						width="100%"
						height="600"
						scrolling="auto"
						align="top"
						frameborder="2"
						class="wrapper">
						This option will not work correctly. Unfortunately, your browser does not support inline frames.</iframe>
					</div>';
					
	if($the_media->source == 'local' && $the_media->width == 1)
	$media = '<a href="'.$the_base_link.'/'.$configs->docsin.'/'.$the_media->local.'" target="_blank">'.$the_media->name.'</a>';

	if($the_media->source == 'url'  && $the_media->width == 0)
	$media='<div class="contentpane">
					<iframe id="blockrandom"
						name="iframe"
						src="'.$the_media->url.'"
						width="100%"
						height="600"
						scrolling="auto"
						align="top"
						frameborder="2"
						class="wrapper">
						This option will not work correctly. Unfortunately, your browser does not support inline frames.</iframe>
					</div>';		
					
	if($the_media->source == 'url'  && $the_media->width == 1)
	$media='<a href="'.$the_media->url.'" target="_blank">'.$the_media->name.'</a>';								
}

if($the_media->type == 'quiz'){
	$media = '';
	
	$q = "SELECT * FROM ".$dbprefix."guru_quiz WHERE id = ".$the_media->source;
	$database->setQuery($q);
	$database->execute();
	$result_quiz = $database->loadObjectList();
	$result_quiz = $result_quiz["0"];
	
	$media = $media. '<strong>'.$result_quiz->name.'</strong><br /><br />';
	$media = $media. $result_quiz->description.'<br /><br />';
	
	$q  = "SELECT * FROM ".$dbprefix."guru_questions WHERE qid = ".$the_media->source;
	$database->setQuery($q);
	$database->execute();
	$quiz_questions = $database->loadAssocList();
	
	foreach($quiz_questions as $key => $one_question)
		{
			$media = $media.'<div align="left">'.$one_question['text'].'<div>';
			
			$media = $media.'<div align="left" style="padding-left:30px;">';
			if($one_question['a1']!='')
				$media = $media.'<input type="radio" name="'.$one_question['id'].'">'.$one_question['a1'].'</input><br />';
			if($one_question['a2']!='')
				$media = $media.'<input type="radio" name="'.$one_question['id'].'">'.$one_question['a2'].'</input><br />';
			if($one_question['a3']!='')
				$media = $media.'<input type="radio" name="'.$one_question['id'].'">'.$one_question['a3'].'</input><br />';
			if($one_question['a4']!='')
				$media = $media.'<input type="radio" name="'.$one_question['id'].'">'.$one_question['a4'].'</input><br />';
			if($one_question['a5']!='')
				$media = $media.'<input type="radio" name="'.$one_question['id'].'">'.$one_question['a5'].'</input><br />';
			if($one_question['a6']!='')
				$media = $media.'<input type="radio" name="'.$one_question['id'].'">'.$one_question['a6'].'</input><br />';
			if($one_question['a7']!='')
				$media = $media.'<input type="radio" name="'.$one_question['id'].'">'.$one_question['a7'].'</input><br />';
			if($one_question['a8']!='')
				$media = $media.'<input type="radio" name="'.$one_question['id'].'">'.$one_question['a8'].'</input><br />';
			if($one_question['a9']!='')
				$media = $media.'<input type="radio" name="'.$one_question['id'].'">'.$one_question['a9'].'</input><br />';		
			if($one_question['a10']!='')
				$media = $media.'<input type="radio" name="'.$one_question['id'].'">'.$one_question['a10'].'</input><br />';		
			$media = $media.'</div>';																																										
		}		
		
	$media = $media.'<br /><div align="left" style="padding-left:30px;"><input type="submit" value="Submit" disabled="disabled" /></div><input name="text_is_quiz" type="hidden" value="1">';	
}	

echo stripslashes($media); 
die();
?>