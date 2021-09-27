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
	
	$filename = JPATH_SITE . "/components/com_guru/css/trainer_style_X.css";
	$handle = fopen($filename, 'r') ;
	$css_file = fread($handle, filesize($filename));
	fclose($handle);
	
	$style_categories = json_decode($this->configs->st_ctgspage);
	$style_category = json_decode($this->configs->st_ctgpage);
	$style_courses = json_decode($this->configs->st_psgspage);
	$style_course = json_decode($this->configs->st_psgpage);
	$style_authors = json_decode($this->configs->st_authorspage);
	$style_author = json_decode($this->configs->st_authorpage);
	
	//List of categories
	$ctgs_page_title = "title_guru";
	$ctgs_categ_name = "name_guru";
	$ctgs_image = "image_guru";
	$ctgs_description = "description_guru";
	$ctgs_st_read_more = "readon";
	
	//Category Page
	$ctg_name = "title_guru";
	$ctg_image = "image_guru";
	$ctg_description = "description_guru";
	$ctg_sub_title = "sub_title_guru";
	
	//List of Courses
	$courses_page_title = "title_guru";
	$courses_name = "name_guru";
	$courses_image = "image_guru";
	$courses_description = "description_guru";
	$courses_st_read_more = "readon";
	
	//Course Page
	$course_name = "title_guru";
	$course_image = "image_guru";
	$course_top_field_name = "field_name_guru";
	$course_top_field_value = "field_value_guru";
	$course_tabs_module_name = "name_guru";	
	$course_tabs_step_name = "step_name_guru";
	$course_description = "description_guru";
	$course_price_field_name = "field_name_guru";
	$course_price_field_value = "field_value_guru";
	$course_author_name = "title_guru";
	$course_author_bio = "description_guru";
	$course_author_image = "image_guru";
	$course_req_field_name = "field_name_guru";
	$course_req_field_value = "field_value_guru";
	$course_other_button = "button";
	$course_other_background = "buy_background";
	
	//List authors
	$authors_page_title = "title_guru";
	$authors_name = "name_guru";
	$authors_image = "image_guru";
	$authors_description = "description_guru";
	$authors_st_read_more = "readon";
	
	//Author page
	$author_name = "name_guru";
	$author_image = "image_guru";
	$author_description = "description_guru";
	$author_st_read_more = "readon";
	
	if(isset($style_categories) && count($style_categories) > 0){		
		$ctgs_page_title = $style_categories->ctgs_page_title;
		$ctgs_categ_name = $style_categories->ctgs_categ_name;
		$ctgs_image = $style_categories->ctgs_image;
		$ctgs_description = $style_categories->ctgs_description;
		$ctgs_st_read_more = $style_categories->ctgs_st_read_more;		
	}
	
	if(isset($style_category) && count($style_category) > 0){		
		$ctg_name = $style_category->ctg_name;
		$ctg_image = $style_category->ctg_image;
		$ctg_description = $style_category->ctg_description;
		$ctg_sub_title = $style_category->ctg_sub_title;
	}
	
	if(isset($style_courses) && count($style_courses) > 0){		
		$courses_page_title = $style_courses->courses_page_title;
		$courses_name = $style_courses->courses_name;
		$courses_image = $style_courses->courses_image;
		$courses_description = $style_courses->courses_description;
		$courses_st_read_more = $style_courses->courses_st_read_more;
	}
	
	if(isset($style_course) && count($style_course) > 0){
		$course_name = $style_course->course_name;
		$course_image = $style_course->course_image;
		$course_top_field_name = $style_course->course_top_field_name;
		$course_top_field_value = $style_course->course_top_field_value;
		$course_tabs_module_name = $style_course->course_tabs_module_name;
		$course_tabs_step_name = $style_course->course_tabs_step_name;
		$course_description = $style_course->course_description;
		$course_price_field_name = $style_course->course_price_field_name;
		$course_price_field_value = $style_course->course_price_field_value;
		$course_author_name = $style_course->course_author_name;
		$course_author_bio = $style_course->course_author_bio;
		$course_author_image = $style_course->course_author_image;
		$course_req_field_name = $style_course->course_req_field_name;
		$course_req_field_value = $style_course->course_req_field_value;
		$course_other_button = $style_course->course_other_button;
		$course_other_background = $style_course->course_other_background;
	}
	
	if(isset($style_authors) && count($style_authors) > 0){
		$authors_page_title = $style_authors->authors_page_title;
		$authors_name = $style_authors->authors_name;
		$authors_image = $style_authors->authors_image;
		$authors_description = $style_authors->authors_description;
		$authors_st_read_more = $style_authors->authors_st_read_more;
	}
	
	if(isset($style_author) && count($style_author) > 0){
		$author_name = $style_author->author_name;
		$author_image = $style_author->author_image;
		$author_description = $style_author->author_description;
		$author_st_read_more = $style_author->author_st_read_more;
	}
?>

<table class="layout_table" cellpadding="0" cellspacing="0" width="100%">	
	<table width="100%" cellpadding="0" cellspacing="0">
		<tr>
			<td valign="top" width="45%">
				<table width="100%" cellpadding="0" cellspacing="0" class="layout_table">
					<!-- ------------------------------- List of categories ------------------------------- -->				
					<tr class="header_row">
						<td>
							<?php echo JText::_("GURU_LIST_CATEGORIES"); ?>
						</td>
						<td>
							<?php echo JText::_("GURU_CLASS"); ?>
						</td>
					</tr>
					
					<tr>
						<td>
							<?php echo JText::_("GURU_PAGE_TITLE"); ?>
						</td>
						<td>
							<input type="text" name="ctgs_page_title" value="<?php echo $ctgs_page_title; ?>">
						</td>
					</tr>
					
					<tr>
						<td>
							<?php echo JText::_("GURU_CATEGORIES_NAME"); ?>
						</td>
						<td>
							<input type="text" name="ctgs_categ_name" value="<?php echo $ctgs_categ_name; ?>">
						</td>
					</tr>
					
					<tr>
						<td>
							<?php echo JText::_("GURU_IMAGE_CAPITAL"); ?>
						</td>
						<td>
							<input type="text" name="ctgs_image" value="<?php echo $ctgs_image; ?>">
						</td>
					</tr>
					
					<tr>
						<td>
							<?php echo JText::_("GURU_CATEGDESC"); ?>
						</td>
						<td>
							<input type="text" name="ctgs_description" value="<?php echo $ctgs_description; ?>">
						</td>
					</tr>
					
					<tr>
						<td>
							<?php echo JText::_("GURU_READ_MORE"); ?>
						</td>
						<td>
							<input type="text" name="ctgs_st_read_more" value="<?php echo $ctgs_st_read_more; ?>">
						</td>
					</tr>
					
					<!-- ------------------------------- Category Page ------------------------------- -->				
					<tr class="header_row">
						<td colspan="2">
							<?php echo JText::_("GURU_CATEGORY_PAGE"); ?>
						</td>
					</tr>
					
					<tr>
						<td>
							<?php echo JText::_("GURU_CATEGNAME"); ?>
						</td>
						<td>
							<input type="text" name="ctg_name" value="<?php echo $ctg_name; ?>">
						</td>
					</tr>
					
					<tr>
						<td>
							<?php echo JText::_("GURU_IMAGE_CAPITAL"); ?>
						</td>
						<td>
							<input type="text" name="ctg_image" value="<?php echo $ctg_image; ?>">
						</td>
					</tr>
					
					<tr>
						<td>
							<?php echo JText::_("GURU_CATEGDESC"); ?>
						</td>
						<td>
							<input type="text" name="ctg_description" value="<?php echo $ctg_description; ?>">
						</td>
					</tr>	
					
					<tr>
						<td>
							<?php echo JText::_("GURU_SUB_TITLE"); ?>
						</td>
						<td>
							<input type="text" name="ctg_sub_title" value="<?php echo $ctg_sub_title; ?>">
						</td>
					</tr>
					
					<!-- ------------------------------- List of Courses ------------------------------- -->				
					<tr class="header_row">
						<td colspan="2">
							<?php echo JText::_("GURU_LIST_COURSES"); ?>
						</td>
					</tr>
					
					<tr>
						<td>
							<?php echo JText::_("GURU_PAGE_TITLE"); ?>
						</td>
						<td>
							<input type="text" name="courses_page_title" value="<?php echo $courses_page_title; ?>">
						</td>
					</tr>
					
					<tr>
						<td>
							<?php echo JText::_("GURU_COURSES_NAME"); ?>
						</td>
						<td>
							<input type="text" name="courses_name" value="<?php echo $courses_name; ?>">
						</td>
					</tr>
					
					<tr>
						<td>
							<?php echo JText::_("GURU_IMAGE_CAPITAL"); ?>
						</td>
						<td>
							<input type="text" name="courses_image" value="<?php echo $courses_image; ?>">
						</td>
					</tr>
					
					<tr>
						<td>
							<?php echo JText::_("GURU_CATEGDESC"); ?>
						</td>
						<td>
							<input type="text" name="courses_description" value="<?php echo $courses_description; ?>">
						</td>
					</tr>
					
					<tr>
						<td>
							<?php echo JText::_("GURU_READ_MORE"); ?>
						</td>
						<td>
							<input type="text" name="courses_st_read_more" value="<?php echo $courses_st_read_more; ?>">
						</td>
					</tr>
					
					<!-- ------------------------------- Course Page ------------------------------- -->				
					<tr class="header_row">
						<td colspan="2">
							<?php echo JText::_("GURU_COURSE_PAGE"); ?>
						</td>
					</tr>
					
					<tr>
						<td>
							<?php echo JText::_("GURU_COURSE_NAME"); ?>
						</td>
						<td>
							<input type="text" name="course_name" value="<?php echo $course_name; ?>">
						</td>
					</tr>
					
					<tr class="header_row2">
						<td colspan="3">
							<?php echo JText::_("IJLM_TOP_AREA"); ?>
						</td>
					</tr>
					
					<tr>
						<td>
							<?php echo JText::_("GURU_IMAGE_CAPITAL"); ?>
						</td>
						<td>
							<input type="text" name="course_image" value="<?php echo $course_image; ?>">
						</td>
					</tr>
					
					<tr>
						<td>
							<?php echo JText::_("GURU_FIELD_NAME"); ?>
						</td>
						<td>
							<input type="text" name="course_top_field_name" value="<?php echo $course_top_field_name; ?>">
						</td>
					</tr>
					
					<tr>
						<td>
							<?php echo JText::_("GURU_FIELD_VALUE"); ?>
						</td>
						<td>
							<input type="text" name="course_top_field_value" value="<?php echo $course_top_field_value; ?>">
						</td>
					</tr>
					
					<tr class="header_row2">
						<td colspan="3">
							<?php echo JText::_("IJLM_TABS"); ?>
						</td>
					</tr>
					
					<tr>
						<td colspan="2">
							<?php echo JText::_("GURU_TAB_TABLE_CONTENT"); ?>
						</td>
					</tr>
					
					<tr>
						<td style="padding-left:30px;">
							<?php echo JText::_("GURU_DAYNAME"); ?>
						</td>
						<td>
							<input type="text" name="course_tabs_module_name" value="<?php echo $course_tabs_module_name; ?>">
						</td>
					</tr>
					
					<tr>
						<td style="padding-left:30px;">
							<?php echo JText::_("GURU_TSKNAME"); ?>
						</td>
						<td>
							<input type="text" name="course_tabs_step_name" value="<?php echo $course_tabs_step_name; ?>">
						</td>
					</tr>
					
					<tr>
						<td>
							<?php echo JText::_("GURU_DESCRIPTION"); ?>
						</td>
						<td>
							<input type="text" name="course_description" value="<?php echo $course_description; ?>">
						</td>
					</tr>
					
					<tr>
						<td colspan="2">
							<?php echo JText::_("GURU_PRICE"); ?>
						</td>
					</tr>
					
					<tr>
						<td style="padding-left:30px;">
							<?php echo JText::_("GURU_FIELD_NAME"); ?>
						</td>
						<td>
							<input type="text" name="course_price_field_name" value="<?php echo $course_price_field_name; ?>">
						</td>
					</tr>
					
					<tr>
						<td style="padding-left:30px;">
							<?php echo JText::_("GURU_FIELD_VALUE"); ?>
						</td>
						<td>
							<input type="text" name="course_price_field_value" value="<?php echo $course_price_field_value; ?>">
						</td>
					</tr>
					
					<tr>
						<td colspan="2">
							<?php echo JText::_("GURU_AUTHOR"); ?>
						</td>
					</tr>
					
					<tr>
						<td style="padding-left:30px;">
							<?php echo JText::_("GURU_COURSE_AUTHOR_NAME"); ?>
						</td>
						<td>
							<input type="text" name="course_author_name" value="<?php echo $course_author_name; ?>">
						</td>
					</tr>
					
					<tr>
						<td style="padding-left:30px;">
							<?php echo JText::_("GURU_BIO"); ?>
						</td>
						<td>
							<input type="text" name="course_author_bio" value="<?php echo $course_author_bio; ?>">
						</td>
					</tr>
					
					<tr>
						<td style="padding-left:30px;">
							<?php echo JText::_("GURU_IMAGE_CAPITAL"); ?>
						</td>
						<td>
							<input type="text" name="course_author_image" value="<?php echo $course_author_image; ?>">
						</td>
					</tr>
					
					<tr>
						<td colspan="2">
							<?php echo JText::_("GURU_DAY_REQ"); ?>
						</td>
					</tr>
					
					<tr>
						<td style="padding-left:30px;">
							<?php echo JText::_("GURU_FIELD_NAME"); ?>
						</td>
						<td>
							<input type="text" name="course_req_field_name" value="<?php echo $course_req_field_name; ?>">
						</td>
					</tr>
					
					<tr>
						<td style="padding-left:30px;">
							<?php echo JText::_("GURU_FIELD_VALUE"); ?>
						</td>
						<td>
							<input type="text" name="course_req_field_value" value="<?php echo $course_req_field_value; ?>">
						</td>
					</tr>
					
					<tr class="header_row2">
						<td colspan="3">
							<?php echo JText::_("GURU_BUY_BUTTON"); ?>
						</td>
					</tr>
					
					<tr>
						<td style="padding-left:30px;">
							<?php echo JText::_("GURU_BUTTON"); ?>
						</td>
						<td>
							<input type="text" name="course_other_button" value="<?php echo $course_other_button; ?>">
						</td>
					</tr>
					
					<tr>
						<td style="padding-left:30px;">
							<?php echo JText::_("GURU_BACKGROUND"); ?>
						</td>
						<td>
							<input type="text" name="course_other_background" value="<?php echo $course_other_background; ?>">
						</td>
					</tr>
					
					<!-- ------------------------------- List of Authors ------------------------------- -->				
					<tr class="header_row">
						<td colspan="2">
							<?php echo JText::_("GURU_LIST_AUTHORS"); ?>
						</td>
					</tr>
					
					<tr>
						<td>
							<?php echo JText::_("GURU_PAGE_TITLE"); ?>
						</td>
						<td>
							<input type="text" name="authors_page_title" value="<?php echo $authors_page_title; ?>">
						</td>
					</tr>
					
					<tr>
						<td>
							<?php echo JText::_("GURU_COURSE_AUTHORS_NAME"); ?>
						</td>
						<td>
							<input type="text" name="authors_name" value="<?php echo $authors_name; ?>">
						</td>
					</tr>
					
					<tr>
						<td>
							<?php echo JText::_("GURU_IMAGE_CAPITAL"); ?>
						</td>
						<td>
							<input type="text" name="authors_image" value="<?php echo $authors_image; ?>">
						</td>
					</tr>
					
					<tr>
						<td>
							<?php echo JText::_("GURU_CATEGDESC"); ?>
						</td>
						<td>
							<input type="text" name="authors_description" value="<?php echo $authors_description; ?>">
						</td>
					</tr>
					
					<tr>
						<td>
							<?php echo JText::_("GURU_READ_MORE"); ?>
						</td>
						<td>
							<input type="text" name="authors_st_read_more" value="<?php echo $authors_st_read_more; ?>">
						</td>
					</tr>
					
					<!-- ------------------------------- Author Page ------------------------------- -->				
					<tr class="header_row">
						<td colspan="2">
							<?php echo JText::_("GURU_AUTHOR_PAGE"); ?>
						</td>
					</tr>
					
					<tr>
						<td>
							<?php echo JText::_("GURU_COURSE_AUTHORS_NAME"); ?>
						</td>
						<td>
							<input type="text" name="author_name" value="<?php echo $author_name; ?>">
						</td>
					</tr>
					
					<tr>
						<td>
							<?php echo JText::_("GURU_IMAGE_CAPITAL"); ?>
						</td>
						<td>
							<input type="text" name="author_image" value="<?php echo $author_image; ?>">
						</td>
					</tr>
					
					<tr>
						<td>
							<?php echo JText::_("GURU_CATEGDESC"); ?>
						</td>
						<td>
							<input type="text" name="author_description" value="<?php echo $author_description; ?>">
						</td>
					</tr>
					
					<tr>
						<td>
							<?php echo JText::_("GURU_READ_MORE"); ?>
						</td>
						<td>
							<input type="text" name="author_st_read_more" value="<?php echo $author_st_read_more; ?>">
						</td>
					</tr>
				</table>
			</td>									
			<td valign="top" style="padding-left: 50px;">
				<textarea cols="60" rows="40" name="css_file"><?php echo $css_file; ?></textarea>
			</td>
		</tr>	
</table>