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

?>

<script language="javascript" type="text/javascript">
	function IsValidNumeric(sText){
		var ValidChars = "0123456789.";
		var IsNumber=true;
		var Char;
		for (i = 0; i < sText.length && IsNumber == true; i++) { 
			Char = sText.charAt(i); 
			if (ValidChars.indexOf(Char) == -1)  { IsNumber = false; }
		}
		return IsNumber;
	}
	
	function changeCategoryImageAlignment(alignment){
		if(alignment == "0"){
			// center
			document.getElementById("category-image-size").style.display = "none";
		}
		else{
			// left or right
			document.getElementById("category-image-size").style.display = "block";
		}
	}
	
	Joomla.submitbutton = function(pressbutton){
		<?php
			$validation_script = "";
			if($tab == "2"){
					$validation_script = 'if(document.adminForm.ctgscols.value =="cols"){
											alert("'.JText::_("GURU_DEFAULT_VIDEO_SIZE_NUMBER").'");
											return false;
										  }
										  else if(document.adminForm.coursescols.value =="cols"){
										  	alert("'.JText::_("GURU_DEFAULT_VIDEO_SIZE_NUMBER").'");
										  	return false;
										  }
										  else if(document.adminForm.authorscols.value =="cols"){
										  	alert("'.JText::_("GURU_DEFAULT_VIDEO_SIZE_NUMBER").'");
										  	return false;
										  }
										  /*else if(document.adminForm.ctgs_description_length.value == "" || document.adminForm.ctgs_description_length.value == 0){
										  	alert("'.JText::_("GURU_CTGS_DESC_LENGTH_ALERT").'");
											return false;
										  }
										  else if(document.adminForm.ctgs_description_length.value != "" && !IsValidNumeric(document.adminForm.ctgs_description_length.value)){
											alert("'.JText::_("GURU_CTGS_DESC_LENGTH_ALERT").'");
											return false;
										  }*/
										  else if(document.adminForm.ctg_image_size.value == "" || document.adminForm.ctg_image_size.value == 0){
										  	alert("'.JText::_("GURU_CTG_IMAGE_SIZE_ALERT").'");
											return false;
										  }
										  else if(document.adminForm.ctg_image_size.value != "" && !IsValidNumeric(document.adminForm.ctg_image_size.value)){
											alert("'.JText::_("GURU_CTG_IMAGE_SIZE_ALERT").'");
											return false;
										  }
										  /*else if(document.adminForm.ctg_description_length.value == "" || document.adminForm.ctg_description_length.value == 0){
										  	alert("'.JText::_("GURU_CTG_DESC_LENGTH_ALERT").'");
											return false;
										  }
										  else if(document.adminForm.ctg_description_length.value != "" && !IsValidNumeric(document.adminForm.ctg_description_length.value)){
											alert("'.JText::_("GURU_CTG_DESC_LENGTH_ALERT").'");
											return false;
										  }*/
										  /*else if(document.adminForm.courses_description_length.value == "" || document.adminForm.courses_description_length.value == 0){
										  	alert("'.JText::_("GURU_COURSES_DESC_LENGTH_ALERT").'");
											return false;
										  }
										  else if(document.adminForm.courses_description_length.value != "" && !IsValidNumeric(document.adminForm.courses_description_length.value)){
											alert("'.JText::_("GURU_COURSES_DESC_LENGTH_ALERT").'");
											return false;
										  }*/
										  else if(document.adminForm.authors_image_size.value == "" || document.adminForm.authors_image_size.value == 0){
										  	alert("'.JText::_("GURU_AUTHORS_IMAGE_SIZE_ALERT").'");
											return false;
										  }
										  else if(document.adminForm.authors_image_size.value != "" && !IsValidNumeric(document.adminForm.authors_image_size.value)){
											alert("'.JText::_("GURU_AUTHORS_IMAGE_SIZE_ALERT").'");
											return false;
										  }
										  /*else if(document.adminForm.authors_description_length.value == "" || document.adminForm.authors_description_length.value == 0){
										  	alert("'.JText::_("GURU_AUTHORS_DESC_LENGTH_ALERT").'");
											return false;
										  }
										  else if(document.adminForm.authors_description_length.value != "" && !IsValidNumeric(document.adminForm.authors_description_length.value)){
											alert("'.JText::_("GURU_AUTHORS_DESC_LENGTH_ALERT").'");
											return false;
										  }*/
										  else if(document.adminForm.author_image_size.value == "" || document.adminForm.author_image_size.value == 0){
										  	alert("'.JText::_("GURU_AUTHOR_IMAGE_SIZE_ALERT").'");
											return false;
										  }
										  else if(document.adminForm.author_image_size.value != "" && !IsValidNumeric(document.adminForm.author_image_size.value)){
											alert("'.JText::_("GURU_AUTHOR_IMAGE_SIZE_ALERT").'");
											return false;
										  }
										  /*else if(document.adminForm.author_description_length.value == "" || document.adminForm.author_description_length.value == 0){
										  	alert("'.JText::_("GURU_AUTHOR_DESC_LENGTH_ALERT").'");
											return false;
										  }
										  else if(document.adminForm.author_description_length.value != "" && !IsValidNumeric(document.adminForm.author_description_length.value)){
											alert("'.JText::_("GURU_AUTHOR_DESC_LENGTH_ALERT").'");
											return false;
										  }*/
										  ';
					echo $validation_script;
			}
		?>	
		//submitform( pressbutton );
		document.adminForm.task.value = pressbutton;
		document.adminForm.submit();
	}
</script>

<?php
	defined('_JEXEC') or die('Restricted Access');		
	
	$list_categories = json_decode($this->configs->ctgspage);
	$category = json_decode($this->configs->ctgpage);
	$courses = json_decode($this->configs->psgspage);
	$course = json_decode($this->configs->psgpage);
	$authors = json_decode($this->configs->authorspage);
	$author = json_decode($this->configs->authorpage);
	$course_lesson_release = $this->configs->course_lesson_release;
	$course_certificate = $this->configs->course_certificate;
	$course_exercises = $this->configs->course_exercises;
	
	//List of categories
	$ctgslayout = "";
	$ctgscols = "2";
	$ctgs_image_alignment = "";
	$ctgs_description_length = "";
	$ctgs_description_type = "";
	$ctgs_description_mode = "0";
	$ctgs_description_alignment = "";
	$ctgs_read_more = "";
	$ctgs_read_more_align = "";
	$ctgs_show_empty_catgs = "";
	
	//Category Page
	$ctg_image_size = "";
	$ctg_image_size_type = "";
	$ctg_image_alignment = "";
	$ctg_description_length = "";
	$ctg_description_type = "";
	$ctg_description_mode = "";
	$ctg_description_alignment = "";
	$ctg_students_number = "1";
	
	//List of Courses
	$courseslayout = "";
	$coursescols = "2";
	$courses_image_size = "";
	$courses_image_size_type = "";
	$courses_image_alignment = "";
	$courses_wrap_image = "";
	$courses_description_length = "";
	$courses_description_type = "";
	$courses_description_mode = "";
	$courses_description_alignment = "";
	$courses_read_more = "";
	$courses_read_more_align = "";
	
	//Course Page
	$course_image_size = "";
	$course_image_size_type = "";
	$course_image_alignment = "";
	$course_wrap_image = "";
	$show_course_image = "0";
	$show_all_cloase_all = "0";
	$show_all_cloase_all_default = "1";
	$course_author_name_show = "";
	$course_released_date = "";
	$duration = "";
	$quiz_status = "";
	$course_level = "";
	$course_price = "";
	$course_price_type = "";
	$course_table_contents = "";
	$course_description_show = "";
	$course_tab_price = "";
	$course_author = "";
	$course_requirements = "";
	$course_buy_button = "";
	$course_buy_button_location = "";

	$course_table_contents_ordering = "0";
	$course_description_show_ordering = "0";
	$course_tab_price_ordering = "0";
	$course_author_ordering = "0";
	$course_requirements_ordering = "0";
	$course_exercises_ordering = "0";
	$default_active_tab = "1";

	//List authors
	$authorslayout = "";
	$authorscols = "2";
	$authors_image_size = "";
	$authors_image_size_type = "";
	$authors_image_alignment = "";
	$authors_wrap_image = "";
	$authors_description_length = "";
	$authors_description_type = "";
	$authors_description_mode = "";
	$authors_description_alignment = "";
	$authors_read_more = "";
	$authors_read_more_align = "";
	
	//Author page
	$author_image_size = "";
	$author_image_size_type = "";
	$author_image_alignment = "";
	$author_wrap_image = "";
	$author_description_length = "";
	$author_description_type = "";
	$author_description_alignment = "";
	
	if(isset($list_categories) && !empty($list_categories)){
		$ctgslayout = $list_categories->ctgslayout;
		$ctgscols = $list_categories->ctgscols;
		$ctgs_image_alignment = $list_categories->ctgs_image_alignment;
		$ctgs_description_length = $list_categories->ctgs_description_length;
		$ctgs_description_type = $list_categories->ctgs_description_type;
		$ctgs_description_mode = $list_categories->ctgs_description_mode;
		$ctgs_description_alignment = $list_categories->ctgs_description_alignment;
		$ctgs_read_more = $list_categories->ctgs_read_more;
		$ctgs_read_more_align = $list_categories->ctgs_read_more_align;
		$ctgs_show_empty_catgs = $list_categories->ctgs_show_empty_catgs;
	}
	
	if(isset($category) && !empty($category)){		
		$ctg_image_size = $category->ctg_image_size;
		$ctg_image_size_type = $category->ctg_image_size_type;
		$ctg_image_alignment = $category->ctg_image_alignment;
		$ctg_description_length = $category->ctg_description_length;
		$ctg_description_type = $category->ctg_description_type;
		$ctg_description_mode = $category->ctg_description_mode;
		$ctg_description_alignment = $category->ctg_description_alignment;
		$ctg_students_number = @$category->ctg_students_number;
	}
	
	if(isset($courses) && !empty($courses)){		
		$courseslayout = $courses->courseslayout;
		$coursescols = $courses->coursescols;
		$courses_image_size = $courses->courses_image_size;
		$courses_image_size_type = $courses->courses_image_size_type;
		$courses_image_alignment = $courses->courses_image_alignment;
		$courses_wrap_image = $courses->courses_wrap_image;
		$courses_description_length = $courses->courses_description_length;
		$courses_description_type = $courses->courses_description_type;
		$courses_description_mode = $courses->courses_description_mode;
		$courses_description_alignment = $courses->courses_description_alignment;
		$courses_read_more = $courses->courses_read_more;
		$courses_read_more_align = $courses->courses_read_more_align;
	}
	
	if(isset($course) && !empty($course)){
		$course_image_size = $course->course_image_size;
		$course_image_size_type = $course->course_image_size_type;
		$course_image_alignment = $course->course_image_alignment;
		$course_wrap_image = $course->course_wrap_image;
		$show_course_image = $course->show_course_image;
		$course_author_name_show = $course->course_author_name_show;
		$course_released_date = $course->course_released_date;
		$duration = @$course->duration;
		$quiz_status = @$course->quiz_status;
		$course_level = $course->course_level;
		$course_price = $course->course_price;
		$course_price_type = $course->course_price_type;
		$course_table_contents = $course->course_table_contents;
		$course_description_show = $course->course_description_show;
		$course_tab_price = $course->course_tab_price;
		$course_author = $course->course_author;
		$course_requirements = $course->course_requirements;
		$course_buy_button = $course->course_buy_button;
		$course_buy_button_location = $course->course_buy_button_location;
		$show_all_cloase_all = $course->show_all_cloase_all;
		$show_all_cloase_all_default = @$course->show_all_cloase_all_default;
		$show_course_studentamount =  $course->show_course_studentamount;

		$course_table_contents_ordering = isset($course->course_table_contents_ordering) ? $course->course_table_contents_ordering : '0';
		$course_description_show_ordering = isset($course->course_description_show_ordering) ? $course->course_description_show_ordering : '0';
		$course_tab_price_ordering = isset($course->course_tab_price_ordering) ? $course->course_tab_price_ordering : '0';
		$course_author_ordering = isset($course->course_author_ordering) ? $course->course_author_ordering : '0';
		$course_requirements_ordering = isset($course->course_requirements_ordering) ? $course->course_requirements_ordering : '0';
		$course_exercises_ordering = isset($course->course_exercises_ordering) ? $course->course_exercises_ordering : '0';
		$default_active_tab = isset($course->default_active_tab) ? $course->default_active_tab : '1';
	}
	
	if(isset($authors) && !empty($authors)){
		$authorslayout = $authors->authorslayout;
		$authorscols = $authors->authorscols;
		$authors_image_size = $authors->authors_image_size;
		$authors_image_size_type = $authors->authors_image_size_type;
		$authors_image_alignment = $authors->authors_image_alignment;
		$authors_wrap_image = $authors->authors_wrap_image;
		$authors_description_length = $authors->authors_description_length;
		$authors_description_mode = $authors->authors_description_mode;
		$authors_description_alignment = $authors->authors_description_alignment;
		$authors_read_more = $authors->authors_read_more;
		$authors_read_more_align = $authors->authors_read_more_align;
	}
	
	if(isset($author) && !empty($author)){
		$author_image_size = $author->author_image_size;
		$author_image_size_type = $author->author_image_size_type;
		$author_image_alignment = $author->author_image_alignment;
		$author_wrap_image = $author->author_wrap_image;
		$author_description_length = $author->author_description_length;
		$author_description_type = $author->author_description_type;
		$author_description_alignment = $author->author_description_alignment;
	}
?>

<script language="javascript" type="text/javascript">
	function changeStyleColumns(value, id){
		if(value == 0){
			document.getElementById(id).style.display = "none";
		}
		else{
			document.getElementById(id).style.display = "block";
		}
	}
</script>
<!-- ------------------------------- List of categories ------------------------------- -->

<?php
	$active_tab = JFactory::getApplication()->input->get("active_tab", "categories_active");
	$categories_active = "";
	$categories_tab = "";
	$category_active = "";
	$category_tab = "";
	$courses_active = "";
	$courses_tab = "";
	$course_active = "";
	$course_tab = "";
	$teachers_active = "";
	$teachers_tab = "";
	$teacher_active = "";
	$teacher_tab = "";
	
	switch($active_tab){
		case "categories_active":{
			$categories_active = 'class="active"';
			$categories_tab = 'active';
			break;
		}
		case "category_active":{
			$category_active = 'class="active"';
			$category_tab = 'active';
			break;
		}
		case "courses_active":{
			$courses_active = 'class="active"';
			$courses_tab = 'active';
			break;
		}
		case "course_active":{
			$course_active = 'class="active"';
			$course_tab = 'active';
			break;
		}
		case "teachers_active":{
			$teachers_active = 'class="active"';
			$teachers_tab = 'active';
			break;
		}
		case "teacher_active":{
			$teacher_active = 'class="active"';
			$teacher_tab = 'active';
			break;
		}
	}
?>

<input type="hidden" id="active_tab" name="active_tab" value="<?php echo $active_tab; ?>" />

 <div class="row-fluid">
     <ul class="nav nav-tabs">
            <li <?php echo $categories_active; ?> >
            	<a href="#Lcat" data-toggle="tab" onclick="document.getElementById('active_tab').value='categories_active';" ><?php echo JText::_('GURU_LIST_CATEGORIES');?></a>
			</li>
            
            <li <?php echo $category_active; ?> >
            	<a href="#cat_page" data-toggle="tab" onclick="document.getElementById('active_tab').value='category_active';" ><?php echo JText::_('GURU_CATEGORY_PAGE');?></a>
			</li>
            
            <li <?php echo $courses_active; ?> >
            	<a href="#list_courses" data-toggle="tab" onclick="document.getElementById('active_tab').value='courses_active';" ><?php echo JText::_('GURU_LIST_COURSES');?></a>
			</li>
            
            <li <?php echo $course_active; ?> >
            	<a href="#course_page" data-toggle="tab" onclick="document.getElementById('active_tab').value='course_active';" ><?php echo JText::_('GURU_COURSE_PAGE');?></a>
			</li>
            
            <li <?php echo $teachers_active; ?> >
            	<a href="#list_auth" data-toggle="tab" onclick="document.getElementById('active_tab').value='teachers_active';" ><?php echo JText::_('GURU_LIST_AUTHORS');?></a>
			</li>
            
            <li <?php echo $teacher_active; ?> >
            	<a href="#auth_page" data-toggle="tab" onclick="document.getElementById('active_tab').value='teacher_active';" ><?php echo JText::_('GURU_AUTHOR_PAGE');?></a>
			</li>
     </ul>
     
     <div class="tab-content">
        <div class="tab-pane <?php echo $categories_tab; ?>" id="Lcat">
        
			<div class="row-fluid">
            <div class="span12"> 
                <div class="row-fluid">
                    <div class="span12">
                        <div class="row-fluid">
                            <div class="control-group">
                                <label class="control-label" for="layout"><?php echo JText::_('GURU_LAYOUT');?>		
               				  </label>
                              <div class="controls">
                              	<div class="pull-left">
                                    <select id="ctgslayout" name="ctgslayout" onchange="javascript:changeStyleColumns(this.value, 'td_ctgscols');">
                                        <option value="0" <?php if($ctgslayout == "0"){echo 'selected="selected"'; } ?>><?php echo JText::_("TREE"); ?></option>
                                        <option value="1" <?php if($ctgslayout == "1"){echo 'selected="selected"'; } ?>><?php echo JText::_("MINI_PROFILE"); ?></option>
                                    </select>
                                    <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_CATEGORIES_LAYOUT"); ?>" >
                                        <img border="0" src="components/com_guru/images/icons/tooltip.png">
                                    </span>
                                </div>
                                <div class="pull-left" id="td_ctgscols" style=" margin-left:15px; display:<?php if($ctgslayout == "0"){echo "none";}else{echo "block";} ?>;">			
                                    <select id="ctgscols" name="ctgscols">
                                        <option value="cols" <?php if($ctgscols == "cols"){echo 'selected="selected"'; } ?>><?php echo JText::_("GURU_COLS"); ?></option>
                                        <option value="1" <?php if($ctgscols == "1"){echo 'selected="selected"'; } ?>>1</option>
                                        <option value="2" <?php if($ctgscols == "2"){echo 'selected="selected"'; } ?>>2</option>
                                        <option value="3" <?php if($ctgscols == "3"){echo 'selected="selected"'; } ?>>3</option>				
                                        <option value="4" <?php if($ctgscols == "4"){echo 'selected="selected"'; } ?>>4</option>				
                                    </select>
                                    <?php echo JText::_("GURU_COLS_DETAILS"); ?>
                            </div>
                   			</div>
                            </div>										
                        </div>
                    </div>							
                </div>
            </div>						
        </div>	
        <div class="row-fluid">
            <div class="span12"> 
                <div class="row-fluid">
                    <div class="span12">
                        <div class="row-fluid">
                            <div class="control-group">
                                <label class="control-label" for="img_align"><?php echo JText::_('GURU_IMAGE_ALIGNMENT');?>		
               				  </label>
                              <div class="controls">
                       			<select id="ctgs_image_alignment" name="ctgs_image_alignment">
                                    <option value="0" <?php if($ctgs_image_alignment == "0"){echo 'selected="selected"'; } ?>><?php echo JText::_("GURU_CENTER"); ?></option>
                                    <option value="1" <?php if($ctgs_image_alignment == "1"){echo 'selected="selected"'; } ?>><?php echo JText::_("GURU_LEFT"); ?></option>
                                    <option value="2" <?php if($ctgs_image_alignment == "2"){echo 'selected="selected"'; } ?>><?php echo JText::_("GURU_RIGHT"); ?></option>
                                </select>
                                <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_CATEGORIES_ALIGNMENT"); ?>" >
                                    <img border="0" src="components/com_guru/images/icons/tooltip.png">
                                </span>
                   			</div>
                            </div>										
                        </div>
                    </div>							
                </div>
            </div>						
        </div>	
        <div class="row-fluid">
            <div class="span12"> 
                <div class="row-fluid">
                    <div class="span12">
                        <div class="row-fluid">
                            <div class="control-group">
                                <label class="control-label" for="img_align"><?php echo JText::_('GURU_DESCRIPTION_LENGTH');?><span style="color:#FF0000">*</span>		
               				  </label>
                              <div class="controls">
                       			<div class="pull-left">
                       				<input style="width:83%" type="text" id="ctgs_description_length" name="ctgs_description_length" value="<?php echo $ctgs_description_length; ?>"/>
                                </div>
                                <div class="pull-left">
                                   <select style="margin-left:15px;" id="ctgs_description_type" name="ctgs_description_type">
                                        <option value="0" <?php if($ctgs_description_type == "0"){echo 'selected="selected"'; } ?>><?php echo JText::_("GURU_CHARACTERS"); ?></option>
                                        <option value="1" <?php if($ctgs_description_type == "1"){echo 'selected="selected"'; } ?>><?php echo JText::_("GURU_WORDS"); ?></option>				
                                    </select>
                                    <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_CATEGORIES_DESCRIPTION_LENGTH"); ?>" >
                                        <img border="0" src="components/com_guru/images/icons/tooltip.png">
                                    </span>
                                </div>
                   			</div>
                            </div>										
                        </div>
                    </div>							
                </div>
            </div>						
        </div>
        
        <div class="row-fluid">
            <div class="span12"> 
                <div class="row-fluid">
                    <div class="span12">
                        <div class="row-fluid">
                            <div class="control-group">
								<label class="control-label" for="img_align"><?php echo JText::_('GURU_DESCRIPTION_MODE');?>		
								</label>
                                <div class="controls">
                                    <select name="ctgs_description_mode">
                                        <option value="0" <?php if($ctgs_description_mode == "0"){echo 'selected="selected"'; } ?>><?php echo JText::_("GURU_TEXT_MODE"); ?></option>
                                        <option value="1" <?php if($ctgs_description_mode == "1"){echo 'selected="selected"'; } ?>><?php echo JText::_("GURU_HTML_MODE"); ?></option>				
                                    </select>
                                    <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_CATEGORIES_DESCRIPTION_MODE"); ?>" >
                                        <img border="0" src="components/com_guru/images/icons/tooltip.png">
                                    </span>
                                </div>
                            </div>										
                        </div>
                    </div>							
                </div>
            </div>						
        </div>
        
         <div class="row-fluid">
            <div class="span12"> 
                <div class="row-fluid">
                    <div class="span12">
                        <div class="row-fluid">
                            <div class="control-group">
                                <label class="control-label" for="desc_align"><?php echo JText::_('GURU_DESCRIPTION_ALIGNMENT');?><span style="color:#FF0000">*</span>		
               				 	</label>
                              	<div class="controls">
                                    <select id="ctgs_description_alignment" name="ctgs_description_alignment">
                                        <option value="0" <?php if($ctgs_description_alignment == "0"){echo 'selected="selected"'; } ?>><?php echo JText::_("GURU_LEFT"); ?></option>
                                        <option value="1" <?php if($ctgs_description_alignment == "1"){echo 'selected="selected"'; } ?>><?php echo JText::_("GURU_RIGHT"); ?></option>
                                    </select>
                                    <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_CATEGORIES_DESCRIPTION_ALIGNMENT"); ?>" >
                                        <img border="0" src="components/com_guru/images/icons/tooltip.png">
                                    </span>
                                </div>
                   			</div>
                          </div>										
                        </div>
                    </div>							
                </div>
            </div>						
            <div class="row-fluid">
                <div class="span12"> 
                    <div class="row-fluid">
                        <div class="span12">
                            <div class="row-fluid">
                                <div class="control-group">
                                    <label class="control-label" for="readmore"><?php echo JText::_('GURU_READ_MORE');?>		
                                  </label>
                                  <div class="controls">
                                    <input type="hidden" name="ctgs_read_more" value="1">
									<?php
										$checked = '';
										if($ctgs_read_more == 0){
											$checked = 'checked="checked"';
										}
									?>
									<input type="checkbox" <?php echo $checked; ?> value="0" class="ace-switch ace-switch-5" name="ctgs_read_more">
									<span class="lbl"></span>
									
                                    <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_CATEGORIES_READ_MORE"); ?>" >
                                        <img border="0" src="components/com_guru/images/icons/tooltip.png">
                                    </span>
                                    </div>
                                </div>
                                </div>										
                            </div>
                        </div>							
                    </div>
             <div class="row-fluid">
            <div class="span12"> 
                <div class="row-fluid">
                    <div class="span12">
                        <div class="row-fluid">
                            <div class="control-group">
                                <label class="control-label" for="desc_align"><?php echo JText::_('GURU_READ_MORE_ALIGNMENT');?>		
               				  </label>
                              <div class="controls">
                       			<select id="ctgs_read_more_align" name="ctgs_read_more_align">
                                    <option value="0" <?php if($ctgs_read_more_align == "0"){echo 'selected="selected"'; } ?>><?php echo JText::_("GURU_LEFT"); ?></option>
                                    <option value="1" <?php if($ctgs_read_more_align == "1"){echo 'selected="selected"'; } ?>><?php echo JText::_("GURU_RIGHT"); ?></option>
                                </select>
                                <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_READ_MORE_ALIGNMENT"); ?>" >
                                    <img border="0" src="components/com_guru/images/icons/tooltip.png">
                                </span>
                               </div>
                   			</div>
                            </div>										
                        </div>
                    </div>							
                </div>
            </div>						
             <div class="row-fluid">
                <div class="span12"> 
                    <div class="row-fluid">
                        <div class="span12">
                            <div class="row-fluid">
                                <div class="control-group">
                                    <label class="control-label" for="desc_align"><?php echo JText::_('GURU_SHOW_EMPTY_CATGS');?>		
                                  </label>
                                  <div class="controls">
                                    <input type="hidden" name="ctgs_show_empty_catgs" value="1">
									<?php
										$checked = '';
										if($ctgs_show_empty_catgs == 0){
											$checked = 'checked="checked"';
										}
									?>
									<input type="checkbox" <?php echo $checked; ?> value="0" class="ace-switch ace-switch-5" name="ctgs_show_empty_catgs">
									<span class="lbl"></span>
									
                                    <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_CATEGORIES_SHOW_EMPTY_CATGS"); ?>" >
                                        <img border="0" src="components/com_guru/images/icons/tooltip.png">
                                    </span>
                                   </div>
                                </div>
                                </div>										
                            </div>
                        </div>							
                    </div>
                </div>						       
            </div>	 
       </div>
        <div class="tab-pane <?php echo $category_tab; ?>" id="cat_page">
       		
            <div class="row-fluid">
                <div class="span12"> 
                    <div class="row-fluid">
                        <div class="span12">
                            <div class="row-fluid">
                                <div class="control-group">
                                    <label class="control-label" for="desc_align"><?php echo JText::_('GURU_IMAGE_ALIGNMENT');?>		
                                  </label>
                                  <div class="controls">
                                    <select id="ctg_image_alignment" name="ctg_image_alignment" onchange="javascript:changeCategoryImageAlignment(this.value)">
                                    	<option value="0" <?php if($ctg_image_alignment == "0"){echo 'selected="selected"'; } ?>><?php echo JText::_("GURU_CENTER"); ?></option>
                                        <option value="1" <?php if($ctg_image_alignment == "1"){echo 'selected="selected"'; } ?>><?php echo JText::_("GURU_LEFT"); ?></option>
                                        <option value="2" <?php if($ctg_image_alignment == "2"){echo 'selected="selected"'; } ?>><?php echo JText::_("GURU_RIGHT"); ?></option>
                                    </select>
                                    <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_CATEGORY_IMAGE_ALIGNMENT"); ?>" >
                                        <img border="0" src="components/com_guru/images/icons/tooltip.png">
                                    </span>
                                   </div>
                                </div>
                            </div>										
                        </div>
                    </div>							
                </div>
            </div>
            
            <div id="category-image-size" class="row-fluid" <?php if($ctg_image_alignment == "0"){echo 'style="display:none;"'; } ?> >
                <div class="span12"> 
                    <div class="row-fluid">
                        <div class="span12">
                            <div class="row-fluid">
                                <div class="control-group">
                                    <label class="control-label" for="desc_align"><?php echo JText::_('JAS_IMGSIZE');?>	<span style="color:#FF0000">*</span>	
                                  </label>
                                  <div class="controls">
                                    <div class="pull-left">
                                        <input style="width:83%" type="text" id="ctg_image_size" name="ctg_image_size" value="<?php echo $ctg_image_size; ?>" />
                                    </div>
                                    <div class="pull-left">
                                        <select  style="margin-left:15px;" id="ctg_image_size_type" name="ctg_image_size_type">
                                            <option value="0" <?php if($ctg_image_size_type == "0"){echo 'selected="selected"'; } ?>><?php echo JText::_("GURU_WIDE"); ?></option>
                                            <option value="1" <?php if($ctg_image_size_type == "1"){echo 'selected="selected"'; } ?>><?php echo JText::_("GURU_HEIGHT"); ?></option>				
                                        </select>
                                        <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_CATEGORY_IMGSIZE"); ?>" >
                                            <img border="0" src="components/com_guru/images/icons/tooltip.png">
                                        </span>
                                    </div>
                                   </div>
                                </div>
                            </div>										
                        </div>
                    </div>							
                </div>
            </div>						
            
        <div class="row-fluid">
        <div class="span12"> 
            <div class="row-fluid">
                <div class="span12">
                    <div class="row-fluid">
                        <div class="control-group">
                            <label class="control-label" for="desc_leng"><?php echo JText::_('GURU_DESCRIPTION_LENGTH');?>	<span style="color:#FF0000">*</span>	
                          </label>
                          <div class="controls">
                            <div class="pull-left">
                                <input style="width:83%" type="text" id="ctg_description_length" name="ctg_description_length" value="<?php echo $ctg_description_length; ?>" />
                            </div>
                            <div class="pull-left">
                                <select style="margin-left:15px;" id="ctg_description_type" name="ctg_description_type">
                                    <option value="0" <?php if($ctg_description_type == "0"){echo 'selected="selected"'; } ?>><?php echo JText::_("GURU_CHARACTERS"); ?></option>
                                    <option value="1" <?php if($ctg_description_type == "1"){echo 'selected="selected"'; } ?>><?php echo JText::_("GURU_WORDS"); ?></option>				
                                </select>
                                <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_CATEGORY_DESCRIPTION_LENGTH"); ?>" >
                                    <img border="0" src="components/com_guru/images/icons/tooltip.png">
                                </span>
                            </div>
                           </div>
                        </div>
                        </div>										
                    </div>
                </div>							
            </div>
        </div>
        
        <div class="row-fluid">
            <div class="span12"> 
                <div class="row-fluid">
                    <div class="span12">
                        <div class="row-fluid">
                            <div class="control-group">
								<label class="control-label" for="img_align"><?php echo JText::_('GURU_DESCRIPTION_MODE');?>		
								</label>
                                <div class="controls">
                                    <select name="ctg_description_mode">
                                        <option value="0" <?php if($ctg_description_mode == "0"){echo 'selected="selected"'; } ?>><?php echo JText::_("GURU_TEXT_MODE"); ?></option>
                                        <option value="1" <?php if($ctg_description_mode == "1"){echo 'selected="selected"'; } ?>><?php echo JText::_("GURU_HTML_MODE"); ?></option>				
                                    </select>
                                    <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_CATEGORY_DESCRIPTION_MODE"); ?>" >
                                        <img border="0" src="components/com_guru/images/icons/tooltip.png">
                                    </span>
                                </div>
                            </div>										
                        </div>
                    </div>							
                </div>
            </div>						
        </div>
        
		<div class="row-fluid">
            <div class="span12"> 
                <div class="row-fluid">
                    <div class="span12">
                        <div class="row-fluid">
                            <div class="control-group">
                                <label class="control-label" for="desc_align"><?php echo JText::_('GURU_DESCRIPTION_ALIGNMENT');?>		
               				  </label>
                              <div class="controls">
                       			<select id="ctg_description_alignment" name="ctg_description_alignment">
                                    <option value="0" <?php if($ctg_description_alignment == "0"){echo 'selected="selected"'; } ?>><?php echo JText::_("GURU_LEFT"); ?></option>
                                    <option value="1" <?php if($ctg_description_alignment == "1"){echo 'selected="selected"'; } ?>><?php echo JText::_("GURU_RIGHT"); ?></option>
                                </select>
                                <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_CATEGORY_DESCRIPTION_ALIGNMENT"); ?>" >
                                    <img border="0" src="components/com_guru/images/icons/tooltip.png">
                                </span>
                               </div>
                   			</div>
                            </div>										
                        </div>
                    </div>							

                </div>
            </div>

           	<div class="row-fluid">
	            <div class="span12"> 
	                <div class="row-fluid">
	                    <div class="span12">
	                        <div class="row-fluid">
	                            <div class="control-group">
	                                <label class="control-label" for="desc_align"><?php echo JText::_('GURU_SHOW_STUDENTS_NUMBER');?></label>
	                       			
	                              	<div class="controls">
	                                    <input type="hidden" name="ctg_students_number" value="1">
										<?php
											$checked = '';
											if($ctg_students_number == 0){
												$checked = 'checked="checked"';
											}
										?>
										<input type="checkbox" <?php echo $checked; ?> value="0" class="ace-switch ace-switch-5" name="ctg_students_number">
										<span class="lbl"></span>
										
	                                    <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_COURSES_READ_MORE"); ?>" >
	                                        <img border="0" src="components/com_guru/images/icons/tooltip.png">
	                                    </span>
                                   	</div>
	                   			</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>
        <div class="tab-pane <?php echo $courses_tab; ?>" id="list_courses">
        <div class="row-fluid">
            <div class="span12"> 
                <div class="row-fluid">
                    <div class="span12">
                        <div class="row-fluid">
                            <div class="control-group">
                                <label class="control-label" for="layout"><?php echo JText::_('GURU_LAYOUT');?>		
               				  </label>
                              <div class="controls">
                              	<div class="pull-left">
                                   <select id="courseslayout" name="courseslayout" onchange="javascript:changeStyleColumns(this.value, 'td_coursescols');">
                                        <option value="0" <?php if($courseslayout == "0"){echo 'selected="selected"'; } ?>><?php echo JText::_("TREE"); ?></option>
                                        <option value="1" <?php if($courseslayout == "1"){echo 'selected="selected"'; } ?>><?php echo JText::_("MINI_PROFILE"); ?></option>
                                    </select>
                                   <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_COURSES_LAYOUT"); ?>" >
                                        <img border="0" src="components/com_guru/images/icons/tooltip.png">
                                    </span>
                                </div>
                                <div class="pull-left" id="td_coursescols" style=" margin-left:15px; display:<?php if($courseslayout == "0"){echo "none";}else{echo "block";} ?>;">			
                                    <select id="coursescols" name="coursescols">
                                        <option value="cols" <?php if($coursescols == "cols"){echo 'selected="selected"'; } ?>><?php echo JText::_("GURU_COLS"); ?></option>
                                        <option value="1" <?php if($coursescols == "1"){echo 'selected="selected"'; } ?>>1</option>
                                        <option value="2" <?php if($coursescols == "2"){echo 'selected="selected"'; } ?>>2</option>
                                        <option value="3" <?php if($coursescols == "3"){echo 'selected="selected"'; } ?>>3</option>				
                                        <option value="4" <?php if($coursescols == "4"){echo 'selected="selected"'; } ?>>4</option>				
                                    </select>
                                    <?php echo JText::_("GURU_COLS_DETAILS"); ?>
                            </div>
                   			</div>
                            </div>										
                        </div>
                    </div>							
                </div>
            </div>						
        </div>
       	
        <!--<div class="row-fluid">
            <div class="span12"> 
                <div class="row-fluid">
                    <div class="span12">
                        <div class="row-fluid">
                            <div class="control-group">
                                <label class="control-label" for="desc_leng"><?php echo JText::_('JAS_IMGSIZE');?><span style="color:#FF0000">*</span>	
                              </label>
                              <div class="controls">
                                <div class="pull-left">
                                    <input style="width:83%" type="text" id="courses_image_size" name="courses_image_size" value="<?php echo $courses_image_size; ?>" />
                                </div>
                                <div class="pull-left">
                                   <select style="margin-left:15px;" id="courses_image_size_type" name="courses_image_size_type">
                                        <option value="0" <?php if($courses_image_size_type == "0"){echo 'selected="selected"'; } ?>><?php echo JText::_("GURU_WIDE"); ?></option>
                                        <option value="1" <?php if($courses_image_size_type == "1"){echo 'selected="selected"'; } ?>><?php echo JText::_("GURU_HEIGHT"); ?></option>				
                                    </select>
                                    <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_COURSES_IMGSIZE"); ?>" >
                                        <img border="0" src="components/com_guru/images/icons/tooltip.png">
                                    </span>
                                </div>
                               </div>
                            </div>
                            </div>										
                        </div>
                    </div>							
                </div>
        </div>	-->
       <div class="row-fluid">
            <div class="span12"> 
                <div class="row-fluid">
                    <div class="span12">
                        <div class="row-fluid">
                            <div class="control-group">
                                <label class="control-label" for="desc_align"><?php echo JText::_('GURU_IMAGE_ALIGNMENT');?>		
               				  </label>
                              <div class="controls">
                       			<select id="courses_image_alignment" name="courses_image_alignment">
                                    <option value="0" <?php if($courses_image_alignment == "0"){echo 'selected="selected"'; } ?>><?php echo JText::_("GURU_CENTER"); ?></option>
                                    <option value="1" <?php if($courses_image_alignment == "1"){echo 'selected="selected"'; } ?>><?php echo JText::_("GURU_LEFT"); ?></option>
                                    <option value="2" <?php if($courses_image_alignment == "2"){echo 'selected="selected"'; } ?>><?php echo JText::_("GURU_RIGHT"); ?></option>
                                </select>
                                <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_COURSES_IMAGE_ALIGNMENT"); ?>" >
                                    <img border="0" src="components/com_guru/images/icons/tooltip.png">
                                </span>
                               </div>
                   			</div>
                            </div>										
                        </div>
                    </div>							
                </div>
            </div>	
            <!--<div class="row-fluid">
            <div class="span12"> 
                <div class="row-fluid">
                    <div class="span12">
                        <div class="row-fluid">
                            <div class="control-group">
                                <label class="control-label" for="desc_align"><?php echo JText::_('GURU_WRAP_IMAGE');?>		
               				  </label>
                              <div class="controls">
                       			<input type="hidden" name="courses_wrap_image" value="1">
								<?php
									$checked = '';
									if($courses_wrap_image == 0){
										$checked = 'checked="checked"';
									}
								?>
								<input type="checkbox" <?php echo $checked; ?> value="0" class="ace-switch ace-switch-5" name="courses_wrap_image">
								<span class="lbl"></span>
								
                                <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_COURSES_WRAP_IMAGE"); ?>" >
                                    <img border="0" src="components/com_guru/images/icons/tooltip.png">
                                </span>
                               </div>
                   			</div>
                            </div>										
                        </div>
                    </div>							
                </div>
            </div>	-->
            <div class="row-fluid">
            <div class="span12"> 
                <div class="row-fluid">
                    <div class="span12">
                        <div class="row-fluid">
                            <div class="control-group">
                                <label class="control-label" for="desc_leng"><?php echo JText::_('GURU_DESCRIPTION_LENGTH');?><span style="color:#FF0000">*</span>	
                              </label>
                              <div class="controls">
                                <div class="pull-left">
                                    <input style="width:83%" type="text" id="courses_description_length" name="courses_description_length" value="<?php echo $courses_description_length; ?>"/>
                                </div>
                                <div class="pull-left">
                                   <select style="margin-left:15px;" id="courses_description_type" name="courses_description_type">
                                        <option value="0" <?php if($courses_description_type == "0"){echo 'selected="selected"'; } ?>><?php echo JText::_("GURU_CHARACTERS"); ?></option>
                                        <option value="1" <?php if($courses_description_type == "1"){echo 'selected="selected"'; } ?>><?php echo JText::_("GURU_WORDS"); ?></option>				
                                    </select>
                                    <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_COURSES_DESCRIPTION_LENGTH"); ?>" >
                                        <img border="0" src="components/com_guru/images/icons/tooltip.png">
                                    </span>
                                </div>
                               </div>
                            </div>
                            </div>										
                        </div>
                    </div>							
                </div>
        </div>
        
        <div class="row-fluid">
            <div class="span12"> 
                <div class="row-fluid">
                    <div class="span12">
                        <div class="row-fluid">
                            <div class="control-group">
								<label class="control-label" for="img_align"><?php echo JText::_('GURU_DESCRIPTION_MODE');?>		
								</label>
                                <div class="controls">
                                    <select name="courses_description_mode">
                                        <option value="0" <?php if($courses_description_mode == "0"){echo 'selected="selected"'; } ?>><?php echo JText::_("GURU_TEXT_MODE"); ?></option>
                                        <option value="1" <?php if($courses_description_mode == "1"){echo 'selected="selected"'; } ?>><?php echo JText::_("GURU_HTML_MODE"); ?></option>				
                                    </select>
                                    <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_COURSES_DESCRIPTION_MODE"); ?>" >
                                        <img border="0" src="components/com_guru/images/icons/tooltip.png">
                                    </span>
                                </div>
                            </div>										
                        </div>
                    </div>							
                </div>
            </div>						
        </div>
        
        <div class="row-fluid">
            <div class="span12"> 
                <div class="row-fluid">
                    <div class="span12">
                        <div class="row-fluid">
                            <div class="control-group">
                                <label class="control-label" for="desc_align"><?php echo JText::_('GURU_DESCRIPTION_ALIGNMENT');?>		
               				  </label>
                              <div class="controls">
                       			<select id="courses_description_alignment" name="courses_description_alignment">
                                    <option value="0" <?php if($courses_description_alignment == "0"){echo 'selected="selected"'; } ?>><?php echo JText::_("GURU_LEFT"); ?></option>
                                    <option value="1" <?php if($courses_description_alignment == "1"){echo 'selected="selected"'; } ?>><?php echo JText::_("GURU_RIGHT"); ?></option>
                                </select>
                                <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_COURSES_DESCRIPTION_ALIGNMENT"); ?>" >
                                    <img border="0" src="components/com_guru/images/icons/tooltip.png">
                                </span>
                               </div>
                   			</div>
                            </div>										
                        </div>
                    </div>							
                </div>
            </div>
            <div class="row-fluid">
                <div class="span12"> 
                    <div class="row-fluid">
                        <div class="span12">
                            <div class="row-fluid">
                                <div class="control-group">
                                    <label class="control-label" for="desc_align"><?php echo JText::_('GURU_READ_MORE');?>		
                                  </label>
                                  <div class="controls">
                                    <input type="hidden" name="courses_read_more" value="1">
									<?php
										$checked = '';
										if($courses_read_more == 0){
											$checked = 'checked="checked"';
										}
									?>
									<input type="checkbox" <?php echo $checked; ?> value="0" class="ace-switch ace-switch-5" name="courses_read_more">
									<span class="lbl"></span>
									
                                    <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_COURSES_READ_MORE"); ?>" >
                                        <img border="0" src="components/com_guru/images/icons/tooltip.png">
                                    </span>
                                   </div>
                                </div>
                                </div>										
                            </div>
                        </div>							
                    </div>
            </div>	
            <div class="row-fluid">
                <div class="span12"> 
                    <div class="row-fluid">
                        <div class="span12">
                            <div class="row-fluid">
                                <div class="control-group">
                                    <label class="control-label" for="desc_align"><?php echo JText::_('GURU_READ_MORE_ALIGNMENT');?>		
                                  </label>
                                  <div class="controls">
                                    <select id="courses_read_more_align" name="courses_read_more_align">
                                        <option value="0" <?php if($courses_read_more_align == "0"){echo 'selected="selected"'; } ?>><?php echo JText::_("GURU_LEFT"); ?></option>
                                        <option value="1" <?php if($courses_read_more_align == "1"){echo 'selected="selected"'; } ?>><?php echo JText::_("GURU_RIGHT"); ?></option>								
                                    </select>
                                    <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_COURSES_READ_MORE_ALIGNMENT"); ?>" >
                                        <img border="0" src="components/com_guru/images/icons/tooltip.png">
                                    </span>
                                   </div>
                                </div>
                                </div>										
                            </div>
                        </div>							
                    </div>
            </div>
          </div>  
            <div class="tab-pane <?php echo $course_tab; ?>" id="course_page">	
            	<div class="well">
		<?php echo JText::_("IJLM_TOP_AREA"); ?>
	</div>
	
    	 	
            <div class="row-fluid" style="display:none;">
                <div class="span12"> 
                    <div class="row-fluid">
                        <div class="span12">
                            <div class="row-fluid">
                                <div class="control-group">
                                    <label class="control-label" for="desc_align"><?php echo JText::_('GURU_SHOW_COURSE_IMAGE');?>		
                                  </label>
                                  <div class="controls">
                                    <input type="hidden" name="show_course_image" value="1">
									<?php
										$checked = '';
										if($show_course_image == 0){
											$checked = 'checked="checked"';
										}
									?>
									<input type="checkbox" <?php echo $checked; ?> value="0" class="ace-switch ace-switch-5" name="show_course_image">
									<span class="lbl"></span>
									
                                    <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_SHOW_COURSE_IMAGE"); ?>" >
                                        <img border="0" src="components/com_guru/images/icons/tooltip.png">
                                    </span>
                                   </div>
                                </div>
                                </div>										
                            </div>
                        </div>							
                    </div>
            </div>	
            <div class="row-fluid">
                <div class="span12"> 
                    <div class="row-fluid">
                        <div class="span12">
                            <div class="row-fluid">
                                <div class="control-group">
                                    <label class="control-label" for="amount_stud"><?php echo JText::_('GURU_SHOW_AMOUNT_STUDENTS');?>		
                                  </label>
                                  <div class="controls">
                                    <input type="hidden" name="show_course_studentamount" value="1">
									<?php
										$checked = '';
										
										if(isset($show_course_studentamount) && $show_course_studentamount == 0){
											$checked = 'checked="checked"';
										}
									?>
									<input type="checkbox" <?php echo $checked; ?> value="0" class="ace-switch ace-switch-5" name="show_course_studentamount">
									<span class="lbl"></span>
									
                                    <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_GURU_SHOW_AMOUNT_STUDENTS"); ?>" >
                                        <img border="0" src="components/com_guru/images/icons/tooltip.png">
                                    </span>
                                   </div>
                                </div>
                                </div>										
                            </div>
                        </div>							
                    </div>
            </div>	
            <div class="row-fluid">
                <div class="span12"> 
                    <div class="row-fluid">
                        <div class="span12">
                            <div class="row-fluid">
                                <div class="control-group">
                                    <label class="control-label" for="desc_align"><?php echo JText::_('GURU_COURSE_AUTHOR_NAME');?>		
                                  </label>
                                  <div class="controls">
                                    <select id="course_author_name_show" name="course_author_name_show">
                                        <option value="0" <?php if($course_author_name_show == "0"){echo 'selected="selected"'; } ?>><?php echo JText::_("GURU_SHOW"); ?></option>
                                        <option value="1" <?php if($course_author_name_show == "1"){echo 'selected="selected"'; } ?>><?php echo JText::_("GURU_HIDE"); ?></option>
                                    </select>
                                    <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_COURSE_AUTHOR_NAME"); ?>" >
                                        <img border="0" src="components/com_guru/images/icons/tooltip.png">
                                    </span>
                                   </div>
                                </div>
                                </div>										
                            </div>
                        </div>							
                    </div>
            </div>
             <div class="row-fluid">
                <div class="span12"> 
                    <div class="row-fluid">
                        <div class="span12">
                            <div class="row-fluid">
                                <div class="control-group">
                                    <label class="control-label" for="desc_align"><?php echo JText::_('GURU_RELEASED_DATE');?>		
                                  </label>
                                  <div class="controls">
                                   <select id="course_released_date" name="course_released_date">
                                        <option value="0" <?php if($course_released_date == "0"){echo 'selected="selected"'; } ?>><?php echo JText::_("GURU_SHOW"); ?></option>
                                        <option value="1" <?php if($course_released_date == "1"){echo 'selected="selected"'; } ?>><?php echo JText::_("GURU_HIDE"); ?></option>
                                    </select>
                                    <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_COURSE_RELEASED_DATE"); ?>" >
                                        <img border="0" src="components/com_guru/images/icons/tooltip.png">
                                    </span>
                                   </div>
                                </div>
                                </div>										
                            </div>
                        </div>							
                    </div>
            </div>
            
            <div class="row-fluid">
                <div class="span12"> 
                    <div class="row-fluid">
                        <div class="span12">
                            <div class="row-fluid">
                                <div class="control-group">
                                    <label class="control-label" for="desc_align"><?php echo JText::_('GURU_DURATION');?>		
                                  </label>
                                  <div class="controls">
                                   <select id="duration" name="duration">
                                        <option value="0" <?php if($duration == "0"){echo 'selected="selected"'; } ?>><?php echo JText::_("GURU_SHOW"); ?></option>
                                        <option value="1" <?php if($duration == "1"){echo 'selected="selected"'; } ?>><?php echo JText::_("GURU_HIDE"); ?></option>
                                    </select>
                                    <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_DURATION"); ?>" >
                                        <img border="0" src="components/com_guru/images/icons/tooltip.png">
                                    </span>
                                   </div>
                                </div>
                                </div>										
                            </div>
                        </div>							
                    </div>
            </div>
            
            <div class="row-fluid">
                <div class="span12"> 
                    <div class="row-fluid">
                        <div class="span12">
                            <div class="row-fluid">
                                <div class="control-group">
                                    <label class="control-label" for="desc_align"><?php echo JText::_('GURU_QUIZ_STATUS');?>		
                                  </label>
                                  <div class="controls">
                                   <select id="quiz_status" name="quiz_status">
                                        <option value="0" <?php if($quiz_status == "0"){echo 'selected="selected"'; } ?>><?php echo JText::_("GURU_SHOW"); ?></option>
                                        <option value="1" <?php if($quiz_status == "1"){echo 'selected="selected"'; } ?>><?php echo JText::_("GURU_HIDE"); ?></option>
                                    </select>
                                    <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_QUIZ_STATUS"); ?>" >
                                        <img border="0" src="components/com_guru/images/icons/tooltip.png">
                                    </span>
                                   </div>
                                </div>
                                </div>										
                            </div>
                        </div>							
                    </div>
            </div>
            	
            <div class="row-fluid">
                <div class="span12"> 
                    <div class="row-fluid">
                        <div class="span12">
                            <div class="row-fluid">
                                <div class="control-group">
                                    <label class="control-label" for="desc_align"><?php echo JText::_('GURU_LEVEL');?>		
                                  </label>
                                  <div class="controls">
                                   <select id="course_level" name="course_level">
                                        <option value="0" <?php if($course_level == "0"){echo 'selected="selected"'; } ?>><?php echo JText::_("GURU_SHOW"); ?></option>
                                        <option value="1" <?php if($course_level == "1"){echo 'selected="selected"'; } ?>><?php echo JText::_("GURU_HIDE"); ?></option>
                                    </select>
                                    <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_COURSE_LEVEL"); ?>" >
                                        <img border="0" src="components/com_guru/images/icons/tooltip.png">
                                    </span>
                                   </div>
                                </div>
                                </div>										
                            </div>
                        </div>							
                    </div>
            </div>	
            <div class="row-fluid">
                <div class="span12"> 
                    <div class="row-fluid">
                        <div class="span12">
                            <div class="row-fluid">
                                <div class="control-group">
                                    <label class="control-label" for="desc_align"><?php echo JText::_('GURU_PRICE');?>		
                                  </label>
                                  <div class="controls">
                                   	<div class="pull-left">
                                    	<select id="course_price" name="course_price">
                                            <option value="0" <?php if($course_price == "0"){echo 'selected="selected"'; } ?>><?php echo JText::_("GURU_SHOW"); ?></option>
                                            <option value="1" <?php if($course_price == "1"){echo 'selected="selected"'; } ?>><?php echo JText::_("GURU_HIDE"); ?></option>
                                        </select>
                                    </div>
                                    <div class="pull-left">
                                    	<select style="margin-left:30px;" id="course_price_type" name="course_price_type">
                                            <option value="0" <?php if($course_price_type == "0"){echo 'selected="selected"'; } ?>><?php echo JText::_("GURU_LOWEST_PLAN"); ?></option>
                                            <option value="1" <?php if($course_price_type == "1"){echo 'selected="selected"'; } ?>><?php echo JText::_("GURU_PRICE_RANGE"); ?></option>
                                        </select>
                                        <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_COURSE_PRICE"); ?>" >
                                            <img border="0" src="components/com_guru/images/icons/tooltip.png">
                                        </span>
                                    </div>
                                   </div>
                                </div>
                                </div>										
                            </div>
                        </div>							
                    </div>
            </div>
            <div class="row-fluid">
                <div class="span12"> 
                    <div class="row-fluid">
                        <div class="span12">
                            <div class="row-fluid">
                                <div class="control-group">
                                    <label class="control-label" for="desc_align"><?php echo JText::_('GURU_LESON_RELEASE');?>		
                                  </label>
                                  <div class="controls">
                                  <select id="course_lesson_release" name="course_lesson_release">
                                        <option value="0" <?php if($course_lesson_release == "0"){echo 'selected="selected"'; } ?>><?php echo JText::_("GURU_SHOW"); ?></option>
                                        <option value="1" <?php if($course_lesson_release == "1"){echo 'selected="selected"'; } ?>><?php echo JText::_("GURU_HIDE"); ?></option>
                                    </select>
                                    <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_LESON_RELEASE"); ?>" >
                                        <img border="0" src="components/com_guru/images/icons/tooltip.png">
                                    </span>
                                   </div>
                                </div>
                            </div>										
                        </div>
                    </div>							
                </div>
        </div>
        	
        <div class="row-fluid">
            <div class="span12"> 
                <div class="row-fluid">
                    <div class="span12">
                        <div class="row-fluid">
                            <div class="control-group">
                                <label class="control-label" for="desc_align"><?php echo JText::_('GURU_CERTIFICATE_ONE');?>		
                              </label>
                              <div class="controls">
                              <select id="course_certificate" name="course_certificate">
                                    <option value="0" <?php if($course_certificate == "0"){echo 'selected="selected"'; } ?>><?php echo JText::_("GURU_SHOW"); ?></option>
                                    <option value="1" <?php if($course_certificate == "1"){echo 'selected="selected"'; } ?>><?php echo JText::_("GURU_HIDE"); ?></option>
                                </select>
                                <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_SHOW_CERTIFICATE"); ?>" >
                                    <img border="0" src="components/com_guru/images/icons/tooltip.png">
                                </span>
                               </div>
                            </div>
                        </div>										
                    </div>
                </div>							
            </div>
        </div>
            
    <div class="well">
		<?php echo JText::_("IJLM_TABS"); ?>
	</div>	
    
    <div class="row-fluid">
        <div class="span12"> 
            <div class="row-fluid">
                <div class="span12">
                    <div class="row-fluid">
                        <div class="control-group">
                            <label class="control-label" for="desc_align"><?php echo JText::_('GURU_TAB_TABLE_CONTENT');?>		
                          </label>
                          <div class="controls">
                           <select id="course_table_contents" name="course_table_contents">
                                <option value="0" <?php if($course_table_contents == "0"){echo 'selected="selected"'; } ?>><?php echo JText::_("GURU_SHOW"); ?></option>
                                <option value="1" <?php if($course_table_contents == "1"){echo 'selected="selected"'; } ?>><?php echo JText::_("GURU_HIDE"); ?></option>
                            </select>

                            <span style="margin-left: 15px;"> <?php echo JText::_("GURU_ORDERING"); ?> </span>
                            <input type="text" class="span1" value="<?php echo $course_table_contents_ordering; ?>" name="course_table_contents_ordering" />

                            <span style="margin-left: 15px;"> <?php echo JText::_("GURU_DEFAULT_ACTIVE_TAB"); ?> </span>
                            <input type="radio" value="1" <?php if($default_active_tab == 1){echo 'checked';} ?> name="default_active_tab" /><span class="lbl"></span>

                            &nbsp;
                            <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_COURSE_TABLE_CONTENT"); ?>" >
                                <img border="0" src="components/com_guru/images/icons/tooltip.png">
                            </span>
                           </div>
                        </div>
                        </div>										
                    </div>
                </div>							
            </div>
    </div>	
    <div class="row-fluid">
        <div class="span12"> 
            <div class="row-fluid">
                <div class="span12">
                    <div class="row-fluid">
                        <div class="control-group">
                            <label class="control-label" for="desc_align"><?php echo JText::_('GURU_DESCRIPTION');?>		
                          </label>
                          <div class="controls">
                           <select id="course_description_show" name="course_description_show">
                                <option value="0" <?php if($course_description_show == "0"){echo 'selected="selected"'; } ?>><?php echo JText::_("GURU_SHOW"); ?></option>
                                <option value="1" <?php if($course_description_show == "1"){echo 'selected="selected"'; } ?>><?php echo JText::_("GURU_HIDE"); ?></option>
                            </select>

                            <span style="margin-left: 15px;"> <?php echo JText::_("GURU_ORDERING"); ?> </span>
                            <input type="text" class="span1" value="<?php echo $course_description_show_ordering; ?>" name="course_description_show_ordering" />

                            <span style="margin-left: 15px;"> <?php echo JText::_("GURU_DEFAULT_ACTIVE_TAB"); ?> </span>
                            <input type="radio" value="2" <?php if($default_active_tab == 2){echo 'checked';} ?> name="default_active_tab" /><span class="lbl"></span>

                            &nbsp;
                            <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_COURSE_DESCRIPTION"); ?>" >
                                <img border="0" src="components/com_guru/images/icons/tooltip.png">
                            </span>
                           </div>
                        </div>
                        </div>										
                    </div>
                </div>							
            </div>
    </div>
    <div class="row-fluid">
        <div class="span12"> 
            <div class="row-fluid">
                <div class="span12">
                    <div class="row-fluid">
                        <div class="control-group">
                            <label class="control-label" for="desc_align"><?php echo JText::_('GURU_PRICE');?>		
                          </label>
                          <div class="controls">
                           <select id="course_tab_price" name="course_tab_price">
                                <option value="0" <?php if($course_tab_price == "0"){echo 'selected="selected"'; } ?>><?php echo JText::_("GURU_SHOW"); ?></option>
                                <option value="1" <?php if($course_tab_price == "1"){echo 'selected="selected"'; } ?>><?php echo JText::_("GURU_HIDE"); ?></option>
                            </select>

                            <span style="margin-left: 15px;"> <?php echo JText::_("GURU_ORDERING"); ?> </span>
                            <input type="text" class="span1" value="<?php echo $course_tab_price_ordering; ?>" name="course_tab_price_ordering" />

                            <span style="margin-left: 15px;"> <?php echo JText::_("GURU_DEFAULT_ACTIVE_TAB"); ?> </span>
                            <input type="radio" value="3" <?php if($default_active_tab == 3){echo 'checked';} ?> name="default_active_tab" /><span class="lbl"></span>

                            &nbsp;
                            <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_COURSE_TAB_PRICE"); ?>" >
                                <img border="0" src="components/com_guru/images/icons/tooltip.png">
                            </span>
                           </div>
                        </div>
                        </div>										
                    </div>
                </div>							
            </div>
    </div>
    <div class="row-fluid">
        <div class="span12"> 
            <div class="row-fluid">
                <div class="span12">
                    <div class="row-fluid">
                        <div class="control-group">
                            <label class="control-label" for="desc_align"><?php echo JText::_('GURU_AUTHOR');?>		
                          </label>
                          <div class="controls">
                           <select id="course_author" name="course_author">
                                <option value="0" <?php if($course_author == "0"){echo 'selected="selected"'; } ?>><?php echo JText::_("GURU_SHOW"); ?></option>
                                <option value="1" <?php if($course_author == "1"){echo 'selected="selected"'; } ?>><?php echo JText::_("GURU_HIDE"); ?></option>
                            </select>

                            <span style="margin-left: 15px;"> <?php echo JText::_("GURU_ORDERING"); ?> </span>
                            <input type="text" class="span1" value="<?php echo $course_author_ordering; ?>" name="course_author_ordering" />

                            <span style="margin-left: 15px;"> <?php echo JText::_("GURU_DEFAULT_ACTIVE_TAB"); ?> </span>
                            <input type="radio" value="4" <?php if($default_active_tab == 4){echo 'checked';} ?> name="default_active_tab" /><span class="lbl"></span>

                            &nbsp;
                            <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_COURSE_TAB_AUTHOR"); ?>" >
                                <img border="0" src="components/com_guru/images/icons/tooltip.png">
                            </span>
                           </div>
                        </div>
                        </div>										
                    </div>
                </div>							
            </div>
    </div>	
    <div class="row-fluid">
        <div class="span12"> 
            <div class="row-fluid">
                <div class="span12">
                    <div class="row-fluid">
                        <div class="control-group">
                            <label class="control-label" for="desc_align"><?php echo JText::_('GURU_DAY_REQ');?>		
                          </label>
                          <div class="controls">
                           <select id="course_requirements" name="course_requirements">
                                <option value="0" <?php if($course_requirements == "0"){echo 'selected="selected"'; } ?>><?php echo JText::_("GURU_SHOW"); ?></option>
                                <option value="1" <?php if($course_requirements == "1"){echo 'selected="selected"'; } ?>><?php echo JText::_("GURU_HIDE"); ?></option>
                            </select>

                            <span style="margin-left: 15px;"> <?php echo JText::_("GURU_ORDERING"); ?> </span>
                            <input type="text" class="span1" value="<?php echo $course_requirements_ordering; ?>" name="course_requirements_ordering" />

                            <span style="margin-left: 15px;"> <?php echo JText::_("GURU_DEFAULT_ACTIVE_TAB"); ?> </span>
                            <input type="radio" value="5" <?php if($default_active_tab == 5){echo 'checked';} ?> name="default_active_tab" /><span class="lbl"></span>

                            &nbsp;
                            <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_COURSE_TAB_DAY_REQ"); ?>" >
                                <img border="0" src="components/com_guru/images/icons/tooltip.png">
                            </span>
                           </div>
                        </div>
                        </div>										
                    </div>
                </div>							
            </div>
    </div>
    
    <div class="row-fluid">
        <div class="span12"> 
            <div class="row-fluid">
                <div class="span12">
                    <div class="row-fluid">
                        <div class="control-group">
                          <label class="control-label" for="desc_align"><?php echo JText::_('GURU_EXERCISE_FILES');?>		
                          </label>
                          <div class="controls">
                            <select id="course_exercises" name="course_exercises">
                                <option value="0" <?php if($course_exercises == "0"){echo 'selected="selected"'; } ?>><?php echo JText::_("GURU_SHOW"); ?></option>
                                <option value="1" <?php if($course_exercises == "1"){echo 'selected="selected"'; } ?>><?php echo JText::_("GURU_HIDE"); ?></option>
                            </select>

                            <span style="margin-left: 15px;"> <?php echo JText::_("GURU_ORDERING"); ?> </span>
                            <input type="text" class="span1" value="<?php echo $course_exercises_ordering; ?>" name="course_exercises_ordering" />

                            <span style="margin-left: 15px;"> <?php echo JText::_("GURU_DEFAULT_ACTIVE_TAB"); ?> </span>
                            <input type="radio" value="6" <?php if($default_active_tab == 6){echo 'checked';} ?> name="default_active_tab" /><span class="lbl"></span>

                            &nbsp;
                            <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_EXERCISE_FILES"); ?>" >
                                <img border="0" src="components/com_guru/images/icons/tooltip.png">
                            </span>
                           </div>
                        </div>
                    </div>										
                </div>
            </div>							
        </div>
    </div>
    
    <div class="well">
		<?php echo JText::_("GURU_OTHERS"); ?>
	</div>	
     
    <div class="row-fluid">
        <div class="span12"> 
            <div class="row-fluid">
                <div class="span12">
                    <div class="row-fluid">
                        <div class="control-group">
                            <label class="control-label" for="desc_align"><?php echo JText::_('GURU_SHOW_BUY_BUTTON');?>		
                          </label>
                          <div class="controls">
							<input type="hidden" name="course_buy_button" value="1">
							<?php
								$checked = '';
								if($course_buy_button == 0){
									$checked = 'checked="checked"';
								}
							?>
							<input type="checkbox" <?php echo $checked; ?> value="0" class="ace-switch ace-switch-5" name="course_buy_button">
							<span class="lbl"></span>
							
                            <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_COURSE_SHOW_BUY_BUTTON"); ?>" >
                                <img border="0" src="components/com_guru/images/icons/tooltip.png">
                            </span>
                           </div>
                        </div>
                        </div>										
                    </div>
                </div>							
            </div>
    </div>	
    <div class="row-fluid">
        <div class="span12"> 
            <div class="row-fluid">
                <div class="span12">
                    <div class="row-fluid">
                        <div class="control-group">
                            <label class="control-label" for="desc_align"><?php echo JText::_('GURU_BUY_BUTTON_LOCATION');?>		
                          </label>
                          <div class="controls">
                          <select id="course_buy_button_location" name="course_buy_button_location">
                                <option value="0" <?php if($course_buy_button_location == "0"){echo 'selected="selected"'; } ?>><?php echo JText::_("GURU_TOP"); ?></option>
                                <option value="1" <?php if($course_buy_button_location == "1"){echo 'selected="selected"'; } ?>><?php echo JText::_("GURU_BOTTOM"); ?></option>
                                <option value="2" <?php if($course_buy_button_location == "2"){echo 'selected="selected"'; } ?>><?php echo JText::_("GURU_TOP_BOTTOM"); ?></option>
                            </select>
                            <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_COURSE_BUTTON_LOCATION"); ?>" >
                                <img border="0" src="components/com_guru/images/icons/tooltip.png">
                            </span>
                           </div>
                        </div>
                        </div>										
                    </div>
                </div>							
            </div>
    </div>	
    
    <div class="row-fluid">
        <div class="span12"> 
            <div class="row-fluid">
                <div class="span12">
                    <div class="row-fluid">
                        <div class="control-group">
                            <label class="control-label" for="desc_align"><?php echo JText::_('GURU_SHOW_ALL_CLOSE_ALL');?></label>
                          	<div class="controls">
                        		<input type="hidden" name="show_all_cloase_all" value="1">
								<?php
									$checked = '';
									if($show_all_cloase_all == 0){
										$checked = 'checked="checked"';
									}
								?>
								<input type="checkbox" <?php echo $checked; ?> value="0" class="ace-switch ace-switch-5" name="show_all_cloase_all">
								<span class="lbl"></span>
							
	                            <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_SHOW_ALL_CLOSE_ALL"); ?>" >
	                                <img border="0" src="components/com_guru/images/icons/tooltip.png">
	                            </span>
                           	</div>
                        </div>
					</div>										
				</div>
			</div>							
		</div>
    </div>


    <div class="row-fluid">
        <div class="span12"> 
            <div class="row-fluid">
                <div class="span12">
                    <div class="row-fluid">
                        <div class="control-group">
                            <label class="control-label" for="desc_align"><?php echo JText::_('GURU_SHOW_ALL_CLOSE_ALL_DEFAULT');?></label>
                          	<div class="controls">
                        		<input type="hidden" name="show_all_cloase_all_default" value="1">
								<?php
									$checked = '';
									if($show_all_cloase_all_default == 0){
										$checked = 'checked="checked"';
									}
								?>
								<input type="checkbox" <?php echo $checked; ?> value="0" class="ace-switch ace-switch-5" name="show_all_cloase_all_default">
								<span class="lbl"></span>
							
	                            <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_SHOW_ALL_CLOSE_ALL_DEFAULT_TIP"); ?>" >
	                                <img border="0" src="components/com_guru/images/icons/tooltip.png">
	                            </span>
                           	</div>
                        </div>
					</div>										
				</div>
			</div>							
		</div>
    </div>



            </div>
            <div class="tab-pane <?php echo $teachers_tab; ?>" id="list_auth">
            	 <div class="row-fluid">
        <div class="span12"> 
            <div class="row-fluid">
                <div class="span12">
                    <div class="row-fluid">
                        <div class="control-group">
                            <label class="control-label" for="layout"><?php echo JText::_('GURU_LAYOUT');?>		
                          </label>
                          <div class="controls">
                            <div class="pull-left">
                               <select id="authorslayout" name="authorslayout" onchange="javascript:changeStyleColumns(this.value, 'td_authorscols');">
                                    <option value="0" <?php if($authorslayout == "0"){echo 'selected="selected"'; } ?>><?php echo JText::_("TREE"); ?></option>
                                    <option value="1" <?php if($authorslayout == "1"){echo 'selected="selected"'; } ?>><?php echo JText::_("MINI_PROFILE"); ?></option>
                                </select>
                                <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_AUTHORS_LAYOUT"); ?>" >
                                    <img border="0" src="components/com_guru/images/icons/tooltip.png">
                                </span>
                            </div>
                            <div class="pull-left" id="td_authorscols" style="display:<?php if($authorslayout=="0"){echo "none";}else{echo "block";} ?>;">			
                               <select style="margin-left:15px;" id="authorscols" name="authorscols">
                                    <option value="cols" <?php if($authorscols == "cols"){echo 'selected="selected"'; } ?>><?php echo JText::_("GURU_COLS"); ?></option>
                                    <option value="1" <?php if($authorscols == "1"){echo 'selected="selected"'; } ?>>1</option>
                                    <option value="2" <?php if($authorscols == "2"){echo 'selected="selected"'; } ?>>2</option>
                                    <option value="3" <?php if($authorscols == "3"){echo 'selected="selected"'; } ?>>3</option>				
                                    <option value="4" <?php if($authorscols == "4"){echo 'selected="selected"'; } ?>>4</option>				
                                </select>
                                <?php echo JText::_("GURU_COLS_DETAILS"); ?>
                        </div>
                        </div>
                        </div>										
                    </div>
                </div>							
            </div>
        </div>						
    </div>	
    <div class="row-fluid">
            <div class="span12"> 
                <div class="row-fluid">
                    <div class="span12">
                        <div class="row-fluid">
                            <div class="control-group">
                                <label class="control-label" for="desc_leng"><?php echo JText::_('JAS_IMGSIZE');?><span style="color:#FF0000">*</span>	
                              </label>
                              <div class="controls">
                                <div class="pull-left">
                                    <input style="width:83%" type="text" id="authors_image_size" name="authors_image_size" value="<?php echo $authors_image_size; ?>" />
                                </div>
                                <div class="pull-left">
                                   <select style="margin-left:15px;" id="authors_image_size_type" name="authors_image_size_type">
                                        <option value="0" <?php if($authors_image_size_type == "0"){echo 'selected="selected"'; } ?>><?php echo JText::_("GURU_WIDE"); ?></option>
                                        <option value="1" <?php if($authors_image_size_type == "1"){echo 'selected="selected"'; } ?>><?php echo JText::_("GURU_HEIGHT"); ?></option>				
                                    </select>
                                    <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_AUTHORS_IMGSIZE"); ?>" >
                                        <img border="0" src="components/com_guru/images/icons/tooltip.png">
                                    </span>
                                </div>
                               </div>
                            </div>
                            </div>										
                        </div>
                    </div>							
                </div>
        </div>
        <div class="row-fluid">
        <div class="span12"> 
            <div class="row-fluid">
                <div class="span12">
                    <div class="row-fluid">
                        <div class="control-group">
                            <label class="control-label" for="desc_align"><?php echo JText::_('GURU_IMAGE_ALIGNMENT');?>		
                          </label>
                          <div class="controls">
                        	 <select id="authors_image_alignment" name="authors_image_alignment">
                                <option value="0" <?php if($authors_image_alignment == "0"){echo 'selected="selected"'; } ?>><?php echo JText::_("GURU_LEFT"); ?></option>
                                <option value="1" <?php if($authors_image_alignment == "1"){echo 'selected="selected"'; } ?>><?php echo JText::_("GURU_RIGHT"); ?></option>
                            </select>
                            <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_AUTHORS_IMAGE_ALIGNMENT"); ?>" >
                                <img border="0" src="components/com_guru/images/icons/tooltip.png">
                            </span>
                           </div>
                        </div>
                        </div>										
                    </div>
                </div>							
            </div>
    </div>	
     <div class="row-fluid">
        <div class="span12"> 
            <div class="row-fluid">
                <div class="span12">
                    <div class="row-fluid">
                        <div class="control-group">
                            <label class="control-label" for="desc_align"><?php echo JText::_('GURU_WRAP_IMAGE');?>		
                          </label>
                          <div class="controls">
								<input type="hidden" name="authors_wrap_image" value="1">
								<?php
									$checked = '';
									if($authors_wrap_image == 0){
										$checked = 'checked="checked"';
									}
								?>
								<input type="checkbox" <?php echo $checked; ?> value="0" class="ace-switch ace-switch-5" name="authors_wrap_image">
								<span class="lbl"></span>
							
								<span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_AUTHORS_WRAP_IMAGE"); ?>" >
									<img border="0" src="components/com_guru/images/icons/tooltip.png">
								</span>
                           </div>
                        </div>
                        </div>										
                    </div>
                </div>							
            </div>
    </div>	
    <div class="row-fluid">
            <div class="span12"> 
                <div class="row-fluid">
                    <div class="span12">
                        <div class="row-fluid">
                            <div class="control-group">
                                <label class="control-label" for="desc_leng"><?php echo JText::_('GURU_DESCRIPTION_LENGTH');?><span style="color:#FF0000">*</span>	
                              </label>
                              <div class="controls">
                                <div class="pull-left">
                                    <input style="width:83%" type="text" id="authors_description_length" name="authors_description_length" value="<?php echo $authors_description_length; ?>"/>
                                </div>
                                <div class="pull-left">
                                   <select style="margin-left:15px;" id="authors_description_type" name="authors_description_type">
                                       <option value="0" <?php if($authors_description_type == "0"){echo 'selected="selected"'; } ?>><?php echo JText::_("GURU_CHARACTERS"); ?></option>
                                        <option value="1" <?php if($authors_description_type == "1"){echo 'selected="selected"'; } ?>><?php echo JText::_("GURU_WORDS"); ?></option>				
                                    </select>
                                    <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_AUTHORS_DESCRIPTION_LENGTH"); ?>" >
                                        <img border="0" src="components/com_guru/images/icons/tooltip.png">
                                    </span>
                                </div>
                               </div>
                            </div>
                            </div>										
                        </div>
                    </div>							
                </div>
        </div>
        
        <div class="row-fluid">
            <div class="span12"> 
                <div class="row-fluid">
                    <div class="span12">
                        <div class="row-fluid">
                            <div class="control-group">
								<label class="control-label" for="img_align"><?php echo JText::_('GURU_DESCRIPTION_MODE');?>		
								</label>
                                <div class="controls">
                                    <select name="authors_description_mode">
                                        <option value="0" <?php if($authors_description_mode == "0"){echo 'selected="selected"'; } ?>><?php echo JText::_("GURU_TEXT_MODE"); ?></option>
                                        <option value="1" <?php if($authors_description_mode == "1"){echo 'selected="selected"'; } ?>><?php echo JText::_("GURU_HTML_MODE"); ?></option>				
                                    </select>
                                    <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_AUTHORS_DESCRIPTION_MODE"); ?>" >
                                        <img border="0" src="components/com_guru/images/icons/tooltip.png">
                                    </span>
                                </div>
                            </div>										
                        </div>
                    </div>							
                </div>
            </div>						
        </div>
        
        <div class="row-fluid">
        <div class="span12"> 
            <div class="row-fluid">
                <div class="span12">
                    <div class="row-fluid">
                        <div class="control-group">
                            <label class="control-label" for="desc_align"><?php echo JText::_('GURU_DESCRIPTION_ALIGNMENT');?>		
                          </label>
                          <div class="controls">
                        	 <select id="authors_description_alignment" name="authors_description_alignment">
                                <option value="0" <?php if($authors_description_alignment == "0"){echo 'selected="selected"'; } ?>><?php echo JText::_("GURU_LEFT"); ?></option>
                                <option value="1" <?php if($authors_description_alignment == "1"){echo 'selected="selected"'; } ?>><?php echo JText::_("GURU_RIGHT"); ?></option>
                            </select>
                            <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_AUTHORS_DESCRIPTION_ALIGNMENT"); ?>" >
                                <img border="0" src="components/com_guru/images/icons/tooltip.png">
                            </span>
                           </div>
                        </div>
                        </div>										
                    </div>
                </div>							
            </div>
    </div>	
     <div class="row-fluid">
        <div class="span12"> 
            <div class="row-fluid">
                <div class="span12">
                    <div class="row-fluid">
                        <div class="control-group">
                            <label class="control-label" for="desc_align"><?php echo JText::_('GURU_READ_MORE');?>		
                          </label>
                          <div class="controls">
								<input type="hidden" name="authors_read_more" value="1">
								<?php
									$checked = '';
									if($authors_read_more == 0){
										$checked = 'checked="checked"';
									}
								?>
								<input type="checkbox" <?php echo $checked; ?> value="0" class="ace-switch ace-switch-5" name="authors_read_more">
								<span class="lbl"></span>
							
                                <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_AUTHORS_READ_MORE"); ?>" >
                                    <img border="0" src="components/com_guru/images/icons/tooltip.png">
                                </span>
                           </div>
                        </div>
                        </div>										
                    </div>
                </div>							
            </div>
    </div>
     <div class="row-fluid">
        <div class="span12"> 
            <div class="row-fluid">
                <div class="span12">
                    <div class="row-fluid">
                        <div class="control-group">
                            <label class="control-label" for="desc_align"><?php echo JText::_('GURU_READ_MORE_ALIGNMENT');?>		
                          </label>
                          <div class="controls">
                        	 <select id="authors_read_more_align" name="authors_read_more_align">
                                <option value="0" <?php if($authors_read_more_align == "0"){echo 'selected="selected"'; } ?>><?php echo JText::_("GURU_LEFT"); ?></option>
                                <option value="1" <?php if($authors_read_more_align == "1"){echo 'selected="selected"'; } ?>><?php echo JText::_("GURU_RIGHT"); ?></option>
                            </select>
                            <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_AUTHORS_READ_MORE_ALIGNMENT"); ?>" >
                                <img border="0" src="components/com_guru/images/icons/tooltip.png">
                            </span>
                           </div>
                        </div>
                        </div>										
                    </div>
                </div>							
            </div>
    </div>	
            </div>
            <div class="tab-pane <?php echo $teacher_tab; ?>" id="auth_page">
            	 <div class="row-fluid">
            <div class="span12"> 
                <div class="row-fluid">
                    <div class="span12">
                        <div class="row-fluid">
                            <div class="control-group">
                                <label class="control-label" for="desc_leng"><?php echo JText::_('JAS_IMGSIZE');?><span style="color:#FF0000">*</span>	
                              </label>
                              <div class="controls">
                                <div class="pull-left">
                                    <input style="width:83%" type="text" id="author_image_size" name="author_image_size" value="<?php echo $author_image_size; ?>"/>
                                </div>
                                <div class="pull-left">
                                   <select  style="margin-left:15px;" id="author_image_size_type" name="author_image_size_type">
                                        <option value="0" <?php if($author_image_size_type == "0"){echo 'selected="selected"'; } ?>><?php echo JText::_("GURU_WIDE"); ?></option>
                                        <option value="1" <?php if($author_image_size_type == "1"){echo 'selected="selected"'; } ?>><?php echo JText::_("GURU_HEIGHT"); ?></option>				
                                    </select>
                                    <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_AUTHOR_IMGSIZE"); ?>" >
                                        <img border="0" src="components/com_guru/images/icons/tooltip.png">
                                    </span>
                                </div>
                               </div>
                            </div>
                            </div>										
                        </div>
                    </div>							
                </div>
        </div>
        <div class="row-fluid">
        <div class="span12"> 
            <div class="row-fluid">
                <div class="span12">
                    <div class="row-fluid">
                        <div class="control-group">
                            <label class="control-label" for="desc_align"><?php echo JText::_('GURU_IMAGE_ALIGNMENT');?>		
                          </label>
                          <div class="controls">
                        	 <select id="author_image_alignment" name="author_image_alignment">
                                <option value="0" <?php if($author_image_alignment == "0"){echo 'selected="selected"'; } ?>><?php echo JText::_("GURU_LEFT"); ?></option>
                                <option value="1" <?php if($author_image_alignment == "1"){echo 'selected="selected"'; } ?>><?php echo JText::_("GURU_RIGHT"); ?></option>
                            </select>
                            <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_AUTHOR_IMAGE_ALIGNMENT"); ?>" >
                                <img border="0" src="components/com_guru/images/icons/tooltip.png">
                            </span>
                           </div>
                        </div>
                        </div>										
                    </div>
                </div>							
            </div>
    </div>	
    <div class="row-fluid">
        <div class="span12"> 
            <div class="row-fluid">
                <div class="span12">
                    <div class="row-fluid">
                        <div class="control-group">
                            <label class="control-label" for="desc_align"><?php echo JText::_('GURU_WRAP_IMAGE');?>		
                          </label>
                          <div class="controls">
								<input type="hidden" name="author_wrap_image" value="1">
								<?php
									$checked = '';
									if($author_wrap_image == 0){
										$checked = 'checked="checked"';
									}
								?>
								<input type="checkbox" <?php echo $checked; ?> value="0" class="ace-switch ace-switch-5" name="author_wrap_image">
								<span class="lbl"></span>
								
								<span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_AUTHOR_WRAP_IMAGE"); ?>" >
									<img border="0" src="components/com_guru/images/icons/tooltip.png">
								</span>
                           </div>
                        </div>
                        </div>										
                    </div>
                </div>							
            </div>
    </div>	
    <div class="row-fluid">
            <div class="span12"> 
                <div class="row-fluid">
                    <div class="span12">
                        <div class="row-fluid">
                            <div class="control-group">
                                <label class="control-label" for="desc_leng"><?php echo JText::_('GURU_DESCRIPTION_LENGTH');?><span style="color:#FF0000">*</span>	
                              </label>
                              <div class="controls">
                                <div class="pull-left">
                                    <input style="width:83%" type="text" id="author_description_length" name="author_description_length" value="<?php echo $author_description_length; ?>"/>
                                </div>
                                 <div class="pull-left">
                                   <select style="margin-left:15px;" id="author_description_type" name="author_description_type">
                                       <option value="0" <?php if($author_description_type == "0"){echo 'selected="selected"'; } ?>><?php echo JText::_("GURU_CHARACTERS"); ?></option>
                                        <option value="1" <?php if($author_description_type == "1"){echo 'selected="selected"'; } ?>><?php echo JText::_("GURU_WORDS"); ?></option>				
                                    </select>
                                    <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_AUTHORS_DESCRIPTION_LENGTH"); ?>" >
                                        <img border="0" src="components/com_guru/images/icons/tooltip.png">
                                    </span>
                                </div>
                               </div>
                            </div>
                            </div>										
                        </div>
                    </div>							
                </div>
        </div>
    <div class="row-fluid">
        <div class="span12"> 
            <div class="row-fluid">
                <div class="span12">
                    <div class="row-fluid">
                        <div class="control-group">
                            <label class="control-label" for="desc_align"><?php echo JText::_('GURU_DESCRIPTION_ALIGNMENT');?>		
                          </label>
                          <div class="controls">
                        	 <select id="author_description_alignment" name="author_description_alignment">
                                <option value="0" <?php if($author_description_alignment == "0"){echo 'selected="selected"'; } ?>><?php echo JText::_("GURU_LEFT"); ?></option>
                                <option value="1" <?php if($author_description_alignment == "1"){echo 'selected="selected"'; } ?>><?php echo JText::_("GURU_RIGHT"); ?></option>
                            </select>
                            <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_AUTHOR_DESCRIPTION_ALIGNMENT"); ?>" >
                                <img border="0" src="components/com_guru/images/icons/tooltip.png">
                            </span>
                           </div>
                        </div>
                        </div>										
                    </div>
                </div>							
            </div>
    </div>				
            </div>
            
     </div> 