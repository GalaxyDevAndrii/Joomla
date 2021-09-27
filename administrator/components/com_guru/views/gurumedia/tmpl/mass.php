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

JHtml::_('behavior.tooltip');
JHTML::_('behavior.modal');
JHtml::_('behavior.multiselect');
JHtml::_('dropdown.init');
JHtml::_('bootstrap.framework');

$action = JFactory::getApplication()->input->get("action", "");
if($action == "next"){
	$category_id = JFactory::getApplication()->input->get("category_id", "0");
	$course_id = JFactory::getApplication()->input->get("course_id", "0");
	
	$category_name = $this->getCategoryName($category_id);
	$course_name = $this->getCourseName($course_id);
	
	$head_msg = "";
	$content = "";
	
	if(intval($course_id) != 0){
		$head_msg = JText::_("GURU_MASS_ADD_TO_COURSE_1")." \"".$category_name."\", ".JText::_("GURU_MASS_ADD_TO_COURSE_2")." \"".$course_name."\" ".JText::_("GURU_SUCCESSFULLY");
		$content .= '<ul>
						<li>
							<a href="index.php?option=com_guru&controller=guruMedia&task=mass">'.JText::_("GURU_MORE_MASS_VIDEOS").'</a>
						</li>
						<li>
							<a href="index.php?option=com_guru&controller=guruDays&pid='.intval($course_id).'">'.JText::_("GURU_VIEW_COURSE_TREE").'</a>
						</li>
						<li>
							<a href="index.php?option=com_guru&controller=guruMediacategs&task=edit&id='.intval($category_id).'">'.JText::_("GURU_VIEW_VIDEO_CATEGORY").'</a>
						</li>
					</ul>';
	}
	else{
		$head_msg = JText::_("GURU_MASS_ADD_TO_MEDIA_1")." \"".$category_name."\" ".JText::_("GURU_SUCCESSFULLY");
		$content .= '<ul>
						<li>
							<a href="index.php?option=com_guru&controller=guruMedia&task=mass">'.JText::_("GURU_MORE_MASS_VIDEOS").'</a>
						</li>
						<li>
							<a href="index.php?option=com_guru&controller=guruMediacategs&task=edit&id='.intval($category_id).'">'.JText::_("GURU_VIEW_VIDEO_CATEGORY").'</a>
						</li>
					</ul>';
	}
	
	echo '<div class="alert alert-success">'.$head_msg.'</div>';
	echo $content;
	
	return true;
}

?>

<script type="text/javascript" src="<?php echo JURI::base(); ?>components/com_guru/views/gurumedia/tmpl/js.js"></script>

<style>
	#bowlG{
		position:relative;
		width:20px;
		height:20px;
	}
	
	#bowl_ringG{
		position:absolute;
		width:20px;
		height:20px;
		border:2px solid #0A0A0A;
		-moz-border-radius:20px;
		-webkit-border-radius:20px;
		-ms-border-radius:20px;
		-o-border-radius:20px;
		border-radius:20px;
	}
	
	.ball_holderG{
		position:absolute;
		width:5px;
		height:20px;
		left:7px;
		top:0px;
		-moz-animation-name:ball_moveG;
		-moz-animation-duration:1.8s;
		-moz-animation-iteration-count:infinite;
		-moz-animation-timing-function:linear;
		-webkit-animation-name:ball_moveG;
		-webkit-animation-duration:1.8s;
		-webkit-animation-iteration-count:infinite;
		-webkit-animation-timing-function:linear;
		-ms-animation-name:ball_moveG;
		-ms-animation-duration:1.8s;
		-ms-animation-iteration-count:infinite;
		-ms-animation-timing-function:linear;
		-o-animation-name:ball_moveG;
		-o-animation-duration:1.8s;
		-o-animation-iteration-count:infinite;
		-o-animation-timing-function:linear;
		animation-name:ball_moveG;
		animation-duration:1.8s;
		animation-iteration-count:infinite;
		animation-timing-function:linear;
	}
	
	.ballG{
		position:absolute;
		left:0px;
		top:-5px;
		width:8px;
		height:8px;
		background:#0F0E0F;
		-moz-border-radius:7px;
		-webkit-border-radius:7px;
		-ms-border-radius:7px;
		-o-border-radius:7px;
		border-radius:7px;
	}
	
	@-moz-keyframes ball_moveG{
		0%{
			-moz-transform:rotate(0deg)
		}
		
		100%{
			-moz-transform:rotate(360deg)
		}
	}
	
	@-webkit-keyframes ball_moveG{
		0%{
			-webkit-transform:rotate(0deg)
		}
		
		100%{
			-webkit-transform:rotate(360deg)
		}
	}
	
	@-ms-keyframes ball_moveG{
		0%{
			-ms-transform:rotate(0deg)
		}
		
		100%{
			-ms-transform:rotate(360deg)
		}	
	}
	
	@-o-keyframes ball_moveG{
		0%{
			-o-transform:rotate(0deg)
		}
		
		100%{
			-o-transform:rotate(360deg)
		}
	}
	
	@keyframes ball_moveG{
		0%{
			transform:rotate(0deg)
		}
		
		100%{
			transform:rotate(360deg)
		}
	}
	
	.form-horizontal .control-label{
		width:15% !important;
	}
</style>

<script language="javascript" type="text/javascript">
	Joomla.submitbutton = function(pressbutton){
		course_id = document.getElementById("course_id").value;
		module_id = document.getElementById("module_id").value;
		teacher_id = document.getElementById("teacher_id").value;
		step_access = document.getElementById("step_access").value;
		
		if(course_id != 0 && module_id == 0){
			alert("<?php echo JText::_("GURU_COURSE_BUT_NO_MODULE"); ?>");
			return false;
		}
		
		if(!eval(document.getElementById("cb0"))){
			alert("<?php echo JText::_("GURU_NO_FETCH_VIDEOS"); ?>");
			return false;
		}
		
		i = 0;
		selected = false;
		while(eval(document.getElementById("cb"+i))){
			if(document.getElementById("cb"+i).checked){
				selected = true;
				break;
			}
			i++;
		}
		
		if(!selected){
			alert("<?php echo JText::_("GURU_NO_VIDEO_SELECTED"); ?>");
			return false;
		}
		
		if(teacher_id == 0){
			alert("<?php echo JText::_("GURU_NO_TEACHER_SELECTED"); ?>");
			return false;
		}
		
		if(step_access == -1){
			alert("<?php echo JText::_("GURU_NO_ACCESS_SELECTED"); ?>");
			return false;
		}
		
		submitform(pressbutton);
	}
</script>
<div class="alert alert-info">
	<?php echo JText::_("GURU_MASS_INFO1")." ".'<a target="_blank" href="index.php?option=com_guru&controller=guruAuthor&task=list">'.JText::_("GURU_HERE")."</a>"."<br/>"; ?>
    <?php echo JText::_("GURU_MASS_INFO2")." ".'<a target="_blank" href="index.php?option=com_guru&controller=guruMediacategs">'.JText::_("GURU_HERE")."</a>"."<br/>"; ?>
    <?php echo JText::_("GURU_MASS_INFO3")." ".'<a target="_blank" href="index.php?option=com_guru&controller=guruPrograms">'.JText::_("GURU_HERE")."</a>"."<br/>"; ?>
    <?php echo JText::_("GURU_MASS_INFO4")." ".'<a target="_blank" href="index.php?option=com_guru&controller=guruPrograms">'.JText::_("GURU_HERE")."</a>"; ?>
</div>        

<form name="adminForm" id="adminForm" action="<?php echo JUri::root() ?>administrator/index.php" method="post" class="form-horizontal add-media-form modal-form">
	
    <div class="control-group">
        <label class="control-label" for="inputEmail">
            <?php echo JText::_('GURU_HOST'); ?>
        </label>
        <div class="controls">
            <select name="host" id="host" onchange="javascript:changeHost(this.value);">
            	<option value="0"> <?php echo JText::_("GURU_SELECT_HOST"); ?> </option>
                <option value="1"> <?php echo JText::_("GURU_YOUTUBE"); ?> </option>
                <option value="2"> <?php echo JText::_("GURU_VIMEO"); ?> </option>
            </select>
            &nbsp; 
			<span class="editlinktip hasTip" title="<?php echo JText::_('TOOLTIP_SELECT_HOST'); ?>" >
			<img src="components/com_guru/images/icons/tooltip.png" border="0"/></span>
            &nbsp;
            &nbsp;
            &nbsp;
            
            <div id="div-playlist" style="display:none; float: right; margin-right: 25%;">
            	<table>
                	<tr>
                    	<td>
							<?php echo JText::_("GURU_PLAYLIST"); ?>
                		</td>
                        <td>
                            <input type="text" value="" name="playlist" id="playlist" />
                            &nbsp; 
                            <span class="editlinktip hasTip" title="<?php echo JText::_('TOOLTIP_ADD_PLAYLIST'); ?>" >
                            <img src="components/com_guru/images/icons/tooltip.png" border="0"/></span>
                		</td>
                	</tr>
                    <tr>
                		<td>
			                <?php echo JText::_("GURU_API_KEY"); ?>
						</td>
                        <td>
                            <input type="text" value="" name="apikey" id="apikey" />
                            &nbsp; 
                            <span class="editlinktip hasTip" title="<?php echo JText::_('TOOLTIP_ADD_API_KEY'); ?>" >
                            <img src="components/com_guru/images/icons/tooltip.png" border="0"/></span>
                		</td>
					</tr>
                    <tr>
                    	<td colspan="2">
                        	<a href="http://tiny.cc/youtube-api" target="_blank"> <?php echo JText::_("GURU_INSTRUCTIONS"); ?> </a>
                        </td>
                    </tr>
				</table>
            </div>
            
            <div id="div-album" style="display:none; float: right; margin-right: 30%;">
            	<?php echo JText::_("GURU_ALBUM"); ?>
                <input type="text" value="" name="album" id="album" />
                &nbsp; 
				<span class="editlinktip hasTip" title="<?php echo JText::_('TOOLTIP_ADD_ALBUM'); ?>" >
				<img src="components/com_guru/images/icons/tooltip.png" border="0"/></span>
            </div>
        </div>
    </div>
    
    <div class="control-group" id="all-row" style="display:none;">
        <label class="control-label" for="inputEmail">
        </label>
        <div class="controls">
			<div id="youtube-pagination" style="display:none;">
            	<?php echo JText::_("GURU_PAGE"); ?>
                <select name="start_with" id="start_with" class="input-mini">
                	<?php
                    	for($i=1; $i<=50; $i++){
							echo '<option value="'.$i.'">'.$i.'</option>';
						}
					?>
                </select>
                &nbsp; 
				<span class="editlinktip hasTip" title="<?php echo JText::_('TOOLTIP_START_WITH'); ?>" >
				<img src="components/com_guru/images/icons/tooltip.png" border="0"/></span>
                &nbsp;&nbsp;&nbsp;
                <?php echo JText::_("GURU_PER_PAGE"); ?>
                <select name="per_page" id="per_page" class="input-mini">
                	<?php
                    	for($i=1; $i<=50; $i++){
							$selected = "";
							if($i == 25){
								$selected = 'selected="selected"';
							}
							echo '<option value="'.$i.'" '.$selected.'>'.$i.'</option>';
						}
					?>
                </select>
                &nbsp; 
				<span class="editlinktip hasTip" title="<?php echo JText::_('TOOLTIP_PER_PAGE'); ?>" >
				<img src="components/com_guru/images/icons/tooltip.png" border="0"/></span>
            </div>
            
            <div id="vimeo-pagination" style="display:none;">
            	<?php echo JText::_("GURU_PAGE"); ?>
                <select name="page" id="page" class="input-mini">
                	<?php
                    	for($i=1; $i<=3; $i++){
							echo '<option value="'.$i.'">'.$i.'</option>';
						}
					?>
                </select>
                &nbsp; 
				<span class="editlinktip hasTip" title="<?php echo JText::_('TOOLTIP_VIMEO_PAGE'); ?>" >
				<img src="components/com_guru/images/icons/tooltip.png" border="0"/></span>
            </div>
        </div>
    </div>
    
    <div class="control-group">
        <label class="control-label" for="inputEmail">
            <?php echo JText::_('GURU_TREEMEDIACAT'); ?>
        </label>
        <div class="controls">
            <?php echo $this->parentCategory(NULL); ?>
            &nbsp; 
			<span class="editlinktip hasTip" title="<?php echo JText::_('TOOLTIP_VIDEO_CATEG'); ?>" >
			<img src="components/com_guru/images/icons/tooltip.png" border="0"/></span>
        </div>
    </div>
    
    <div class="control-group">
        <label class="control-label" for="inputEmail">
            <?php echo JText::_('GURU_CREATE_LESSON_AUTOMATIC'); ?>
        </label>
        <div class="controls">
            <select name="course_id" id="course_id" onchange="javascript:changeMassCourse(this.value);" style="float:left;">
				<option value="0"><?php echo JText::_("GURU_SELECT_COURSE"); ?></option>
			<?php
            	$courses = $this->getCourses();
				if(isset($courses) && count($courses) > 0){
					foreach($courses as $key=>$value){
						echo '<option value="'.intval($value["id"]).'">'.$value["name"].'</option>';
					}
				}
			?>
            </select>
            <div style="float:left; margin-left:4px;">
                &nbsp; 
                <span class="editlinktip hasTip" title="<?php echo JText::_('TOOLTIP_COUSE_ID'); ?>" >
                <img src="components/com_guru/images/icons/tooltip.png" border="0"/></span>
			</div>
            
            <div id="div-modules" style="float: left; margin-left: 20px;">
				<select name="module_id" id="module_id">
                	<option value="0"><?php echo JText::_("GURU_SELECT_MODULE"); ?></option>
                </select>
            </div>
            <div style="float:left;">
                &nbsp; 
                <span class="editlinktip hasTip" title="<?php echo JText::_('TOOLTIP_MODULE_ID'); ?>" >
                <img src="components/com_guru/images/icons/tooltip.png" border="0"/></span>
            </div>
            
            <div class="clearfix"></div>
     </div>
   </div>  
   <div class="control-group">
        <label class="control-label" for="inputEmail">
        </label>
        <div class="controls">       
            <div style="float: left;">
				<select name="teacher_id" id="teacher_id">
                	<option value="0"><?php echo JText::_("GURU_SELECT_TEACHER"); ?></option>
                    <?php
						$teachers = $this->getTeachers();
						if(isset($teachers) && count($teachers) > 0){
							foreach($teachers as $key=>$value){
								echo '<option value="'.intval($value["id"]).'">'.$value["name"].'</option>';
							}
						}
					?>
                </select>
            </div>
            <div style="float:left;">
                &nbsp;&nbsp; 
                <span class="editlinktip hasTip" title="<?php echo JText::_('TOOLTIP_TEACHER_ID'); ?>" >
                <img src="components/com_guru/images/icons/tooltip.png" border="0"/></span>
            </div>
            
            <div style="float: left; margin-left: 20px;">
				<select name="step_access" id="step_access">
					<option value="-1"><?php echo JText::_("GURU_LESSON_ACCESS"); ?></option>
                    <option value="0"><?php echo JText::_("GURU_COU_STUDENTS"); ?></option>
					<option value="1"><?php echo JText::_("GURU_REG_MEMBERS"); ?></option>
					<option value="2"><?php echo JText::_("GURU_REG_GUESTS"); ?></option> 
				</select>
            </div>
            <div style="float:left;">
                &nbsp; 
                <span class="editlinktip hasTip" title="<?php echo JText::_('TOOLTIP_LESSON_ACCESS'); ?>" >
                <img src="components/com_guru/images/icons/tooltip.png" border="0"/></span>
            </div>
            
        </div>
    </div>
    
    <div class="control-group">
        <label class="control-label" for="inputEmail">
        </label>
        <div class="controls">
            <input type="button" name="featch" class="btn btn-success pull-left" value="<?php echo JText::_("GURU_FETCH_VIDEOS"); ?>" onclick="javascript:listMassVideos('<?php echo JURI::root(); ?>');" />
            <div id="bowlG" style="float:left; display:none;">
                <div id="bowl_ringG">
                    <div class="ball_holderG">
                        <div class="ballG">
                        </div>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
    
    <div class="alert alert-info">
    	<?php echo JText::_("GURU_POSTED_VIDEOS"); ?>
    </div>
    
    <div id="list-of-videos">
    </div>
    
	<input type="hidden" name="option" value="com_guru" />
    <input type="hidden" name="task" value="" />
    <input type="hidden" name="controller" value="guruMedia" />
</form>