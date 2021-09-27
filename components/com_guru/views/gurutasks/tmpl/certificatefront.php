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
JHTML::_('behavior.modal', 'a.modal');

$document = JFactory::getDocument();
$document->setTitle( JText::_("GURU_MYCERTIFICATES"));

$db = JFactory::getDBO();
$config = JFactory::getConfig();
$course_id = JFactory::getApplication()->input->get("course_id", "0", "raw");

if(intval($course_id) == 0){
	$course_id = JFactory::getApplication()->input->get("ci", "0", "raw");
}

$certificate = JFactory::getApplication()->input->get("certificate", "0", "raw");

/*$user = JFactory::getUser();
$user_id = $user->id;*/
$sql = "SELECT user_id FROM #__guru_mycertificates WHERE id = ".intval($certificate)." AND course_id=".intval($course_id);
$db->setQuery($sql);
$db->execute();
$user_id = $db->loadResult();

$guruModelguruTask = new guruModelguruTask();
$sql = "SELECT name from #__guru_program WHERE id =".intval($course_id);
$db->setQuery($sql);
$db->execute();
$result = $db->loadResult();

$imagename = "SELECT * FROM #__guru_certificates WHERE id=1";
$db->setQuery($imagename);
$db->execute();
$imagename = $db->loadAssocList();

$sql = "select `custom_certificate` from #__guru_program where `id`=".intval($course_id);
$db->setQuery($sql);
$db->execute();
$custom_certificate = $db->loadColumn();
$custom_certificate = @$custom_certificate["0"];

if(intval($custom_certificate) == 1){
	$sql = "SELECT * FROM #__guru_certificates WHERE `course_id`=".intval($course_id);
	$db->setQuery($sql);
	$db->execute();
	$course_imagename = $db->loadAssocList();

	if(isset($course_imagename) && isset($course_imagename["0"]) && count($course_imagename) > 0){
		foreach ($course_imagename["0"] as $key => $value){
			//if(trim($value) != ""){
				$imagename["0"][$key] = $value;
			//}
		}
	}
}

$authorname = "SELECT name from #__users where id IN (SELECT author_id FROM #__guru_mycertificates WHERE user_id = ".intval($user_id)." AND course_id =".intval($course_id)." )";
$db->setQuery($authorname);
$db->execute();
$authorname = $db->loadColumn();
$authorname = implode(", ", $authorname);

$background_color = "";

if($imagename[0]["design_background"] !=""){
	$image_theme = explode("/", $imagename[0]["design_background"]);
	if(trim($image_theme[4]) =='thumbs'){
		$image_theme = $image_theme[5];
	}
	else{
		$image_theme = $image_theme[4];
	}
	}	
else{
	$background_color= "background-color:"."#".$imagename[0]["design_background_color"];
}	

$site_url = JURI::root();
$coursename = $result;
$authorname = $authorname;

$date_completed = "SELECT datecertificate FROM #__guru_mycertificates WHERE user_id=".intval($user_id)." AND course_id=".intval($course_id);
$db->setQuery($date_completed);
$db->execute();
$date_completed = $db->loadResult();

$format = "SELECT datetype FROM #__guru_config WHERE id=1";
$db->setQuery($format);
$db->execute();
$format = $db->loadResult();

if(isset($date_completed) && $date_completed !='0000-00-00' ){
	//$date_completed = date($format, strtotime($date_completed));
	$date_completed = date("Y-m-d", strtotime($date_completed));
	$completiondate = $date_completed;

}
else{
	$completiondate = "";
}
$certificateid = "SELECT id FROM #__guru_mycertificates WHERE user_id = ".intval($user_id)." AND course_id=".intval($course_id);
$db->setQuery($certificateid);
$db->execute();
$certificateid = $db->loadResult();

$sitename = $config->get( 'sitename');


$coursemsg = "SELECT certificate_course_msg FROM #__guru_program WHERE id=".intval($course_id);
$db->setQuery($coursemsg);
$db->execute();
$coursemsg = $db->loadResult();

$firstnamelastname = "SELECT firstname, lastname, company FROM #__guru_customer WHERE id=".intval($user_id);
$db->setQuery($firstnamelastname);
$db->execute();
$firstnamelastname = $db->loadAssocList();

$scores_avg_quizzes =  $guruModelguruTask->getAvgScoresQ($user_id,$course_id);

$avg_quizzes_cert = "SELECT avg_certc FROM #__guru_program WHERE id=".intval($course_id);
$db->setQuery($avg_quizzes_cert);
$db->execute();
$avg_quizzes_cert = $db->loadResult();

$sql = "SELECT hasquiz from #__guru_program WHERE id=".intval($course_id);
$db->setQuery($sql);
$resulthasq = $db->loadResult();

$s = 0;
$sql = "select mr.media_id from #__guru_mediarel mr, #__guru_days d where mr.type='dtask' and mr.type_id=d.id and d.pid=".intval($course_id);
$db->setQuery($sql);
$db->execute();
$lessons = $db->loadColumn();

if(!isset($lessons) || count($lessons) == 0){
	$lessons = array("0");
}

$sql = "select mr.media_id from #__guru_mediarel mr where mr.layout='12' and mr.type='scr_m' and mr.type_id in (".implode(", ", $lessons).")";
$db->setQuery($sql);
$db->execute();
$all_quizzes = $db->loadColumn();

$avg_certc = "N/A";

if(isset($all_quizzes) && count($all_quizzes) > 0){
	foreach($all_quizzes as $key_quiz=>$quiz_id){
		$sql = "SELECT score_quiz FROM #__guru_quiz_question_taken_v3 WHERE user_id=".intval($user_id)." and quiz_id=".intval($quiz_id)." and pid=".intval($course_id)." ORDER BY id DESC LIMIT 0,1";
		$db->setQuery($sql);
		$db->execute();
		$result_q = $db->loadColumn();
		$res = @$result_q["0"];
		$s += $res;
	}
	
	$avg_certc = "N/A";
	
	if($s > 0){
		$avg_certc = round($s / count($all_quizzes))."%";
	}
}
//-----------------------------------------------------------------

$certificate_href = JURI::root()."index.php?option=com_guru&view=guruTasks&task=viewcertificate&certificate=".intval($certificateid)."&pdf=1&dw=2&ci=2&course_id=".intval($course_id);
$certificate_url = '<a href="'.$certificate_href.'" target="_blank">'.JText::_("GURU_URL_CERTIFICATE").'</a>';

$imagename[0]["templates1"]  = str_replace("[SITENAME]", $sitename, $imagename[0]["templates1"]);
$imagename[0]["templates1"]  = str_replace("[STUDENT_FIRST_NAME]", @$firstnamelastname[0]["firstname"], $imagename[0]["templates1"]);
$imagename[0]["templates1"]  = str_replace("[STUDENT_LAST_NAME]", @$firstnamelastname[0]["lastname"], $imagename[0]["templates1"]);
$imagename[0]["templates1"]  = str_replace("[SITEURL]", $site_url, $imagename[0]["templates1"]);
$imagename[0]["templates1"]  = str_replace("[CERTIFICATE_ID]", $certificateid, $imagename[0]["templates1"]);
$imagename[0]["templates1"]  = str_replace("[COMPLETION_DATE]", $completiondate, $imagename[0]["templates1"]);
$imagename[0]["templates1"]  = str_replace("[COURSE_NAME]", $coursename, $imagename[0]["templates1"]);
$imagename[0]["templates1"]  = str_replace("[AUTHOR_NAME]", $authorname, $imagename[0]["templates1"]);
$imagename[0]["templates1"]  = str_replace("[COURSE_MSG]", $coursemsg, $imagename[0]["templates1"]);
$imagename[0]["templates1"]  = str_replace("[COURSE_AVG_SCORE]", $avg_certc, $imagename[0]["templates1"]);
$imagename[0]["templates1"]  = str_replace("[COURSE_FINAL_SCORE]", $avg_certc, $imagename[0]["templates1"]);
$imagename[0]["templates1"]  = str_replace("[CERTIFICATE_URL]", $certificate_url, $imagename[0]["templates1"]);
$imagename[0]["templates1"]  = str_replace("[STUDENT_COMPANY]", @$firstnamelastname[0]["company"], $imagename[0]["templates1"]);

$document = JFactory::getDocument();
?>
<script>
	function openWinCertificate2(t1,t2,t3,t4,t5){
		t1 = encodeURIComponent(t1);
		t2 = encodeURIComponent(t2);
		t3 = encodeURIComponent(t3);
		t4 = encodeURIComponent(t4);
		t5 = encodeURIComponent(t5);
	
		myWindow=window.open('<?php echo JURI::root();?>index.php?option=com_guru&view=guruOrders&task=printcertificate&op=2&cn='+t1+'&an='+t2+'&id='+t3+'&cd='+t4+'&ci='+t5+'&tmpl=component','','width=850,height=600, resizable = 0');
		myWindow.focus();
	}
	
	function openWinCertificate3(t1,t2,t3,t4,t5){
		t1 = encodeURIComponent(t1);
		t2 = encodeURIComponent(t2);
		t3 = encodeURIComponent(t3);
		t4 = encodeURIComponent(t4);
		t5 = encodeURIComponent(t5);
	
		myWindow=window.open('<?php echo JURI::root();?>index.php?option=com_guru&view=guruOrders&task=printcertificate&op=3&cn='+t1+'&an='+t2+'&id='+t3+'&cd='+t4+'&ci='+t5+'&tmpl=component','','width=800,height=250, resizable = 0');
		myWindow.focus();
	}
	
	function openWinCertificate4(t1,t2,t3,t4,t5){
		t1 = encodeURIComponent(t1);
		t2 = encodeURIComponent(t2);
		t3 = encodeURIComponent(t3);
		t4 = encodeURIComponent(t4);
		t5 = encodeURIComponent(t5);
	
		myWindow=window.open('<?php echo JURI::root();?>index.php?option=com_guru&view=guruOrders&task=savepdfcertificate&op=9&cn='+t1+'&an='+t2+'&id='+t3+'&cd='+t4+'&ci='+t5+'&tmpl=component','','width=800,height=600, resizable = 0');
		myWindow.focus();
	}
</script>

<?php

$cid = JFactory::getApplication()->input->get("prev_lesson_id");
$module_prev_lesson = JFactory::getApplication()->input->get("module_prev_lesson");
$catid = JFactory::getApplication()->input->get("catid");
$tmpl = JFactory::getApplication()->input->get("tmpl");
$pid = JFactory::getApplication()->input->get("course_id");

if($tmpl == ""){
	$tmpl = "";
	$position="position:relative;";
}
else{
	$tmpl = "&tmpl=component";
	$position="position:absolute;";
}

$db = JFactory::getDbo();
$sql = "SELECT `imagesin` FROM #__guru_config LIMIT 1";
$db->setQuery($sql);
$db->execute();
$res = $db->loadResult();
$certificate_path = JUri::base().$res."/certificates/";

?>

<div id="g_lesson_navs" class="clearfix">
    <div class="g_lesson_nav_bar clearfix">
        <div class="pull-left">
            <a  onclick="javascript:closeBox();" href="<?php echo JRoute::_('index.php?option=com_guru&view=guruPrograms&task=view&cid='.$pid.'&Itemid='.@$Itemid); ?>">
            <img style="border:none;" src="<?php echo JUri::base()."components/com_guru/images/home.png"; ?>" alt="<?php echo JText::_("GURU_COURSE_HOME_PAGE"); ?>" title="<?php echo JText::_("GURU_COURSE_HOME_PAGE"); ?>"/>
        </a>
        </div> 
        <div class="pull-right">  
            <a href="<?php echo JRoute::_("index.php?option=com_guru&view=guruTasks&catid=".intval($catid)."&task=view&module=".$module_prev_lesson."&cid=".$cid.$tmpl); ?>">
                            <img style="border:none;" src="<?php echo JUri::base()."components/com_guru/images/back.png"; ?>" alt="<?php echo JText::_("GURU_PREVIOUS_LESSON"); ?>" title="<?php echo JText::_("GURU_PREVIOUS_LESSON"); ?>"/>
             </a>
             <a onClick="window.location.reload();">
                    <img style="border:none;" src="<?php echo JUri::base()."components/com_guru/images/repeat.png"; ?>" alt="<?php echo JText::_("GURU_REFRESH_PAGE"); ?>" title="<?php echo JText::_("GURU_REFRESH_PAGE"); ?>"/></a>
        </div>
    </div>  
  </div>  
<div class="g_sect">
	<div class="g_row"> 
		<div class="g_cell span12">
        	<div>
                <div style="<?php echo $position;?>">
                	<div style="text-align:center">
                        <p style="font-size:36px;"><?php echo JText::_("GURU_CONGRAT"); ?></p>
                        <p style="font-size:26px;"><?php echo JText::_("GURU_EFFORTS_AWERDED"); ?></p>
                        <button type="submit" class="btn btn-primary" name="download_cert"  onclick="openWinCertificate4('<?php echo str_replace("'","&acute;",$coursename)?>','<?php echo $authorname ?>','<?php echo $certificateid ?>', '<?php echo $date_completed ?>', '<?php echo $course_id; ?>')" ><i class="icon-download"></i> <?php echo JText::_("GURU_DOWNLOAD_CERT"); ?></button>
                        <button type="submit" class="btn btn-primary" name="share_cert"  onclick="openWinCertificate3('<?php echo str_replace("'","&acute;",$coursename)?>','<?php echo $authorname ?>','<?php echo $certificateid ?>', '<?php echo $date_completed ?>', '<?php echo $course_id; ?>')" ><i class="icon-share"></i> <?php echo JText::_("GURU_SHARE_CERTIFICATE"); ?></button>
                        <button type="submit" class="btn btn-primary"  name="email_cert"  onclick="openWinCertificate2('<?php echo str_replace("'","&acute;",$coursename)?>','<?php echo $authorname ?>','<?php echo $certificateid ?>', '<?php echo $date_completed ?>', '<?php echo $course_id; ?>')" ><i class="icon-envelope"></i> <?php echo JText::_("GURU_EMAIL_CERTIFICATE"); ?></button>
                    </div>    
                    <div style="font-family:<?php echo $imagename[0]["font_certificate"]; ?>; margin-top:15px; min-height:600px; <?php echo $background_color;?>; background-size:100% 100%; background-repeat:no-repeat; background-image:url(<?php echo $certificate_path.$image_theme; ?>); position:relative;">
                        <div class="g_certificate_view" style="color:<?php echo "#".$imagename[0]["design_text_color"]; ?>">
                        	<div>
                            	<?php echo $imagename[0]["templates1"]; ?>
                            </div>    
                        </div>
                   </div> 
       			</div> 
       		</div>  
		</div>
	</div>
</div>