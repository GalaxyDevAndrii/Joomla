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
// no direct access
defined('_JEXEC') or die('Restricted access');
jimport('joomla.html.pagination');
JHTML::_('behavior.tooltip');
JHTML::_('behavior.framework');
JHtml::_('behavior.calendar');

$doc = JFactory::getDocument();

//$doc->addScript('components/com_guru/js/guru_modal.js');

$doc->addScriptDeclaration('
    var accordionItems = new Array();       
    function initPhoneTeacherTabs() {
        // Grab the accordion items from the page
        var divs = document.getElementsByTagName( \'div\' );
        
        for ( var i = 0; i < divs.length; i++ ) {
            if ( divs[i].className == \'accordionItem\' ) accordionItems.push( divs[i] );
        }
        
        // Assign onclick events to the accordion item headings
        for ( var i = 0; i < accordionItems.length; i++ ) {
            var h3 = getFirstChildWithTagName( accordionItems[i], \'H3\' );
            h3.onclick = toggleItem;
        }
        
        // Hide all accordion item bodies except the first
        for ( var i = 1; i < accordionItems.length; i++ ) {
            accordionItems[i].className = \'accordionItem hideTabs\';
        }
    }
    
    function toggleItem() {
      var itemClass = this.parentNode.className;

      // Hide all items
      for ( var i = 0; i < accordionItems.length; i++ ) {
        accordionItems[i].className = \'accordionItem hideTabs\';
      }

      // Show this item if it was previously hidden
      if ( itemClass == \'accordionItem hideTabs\' ) {
        this.parentNode.className = \'accordionItem\';
      }
    }

    function getFirstChildWithTagName( element, tagName ) {
      for ( var i = 0; i < element.childNodes.length; i++ ) {
        if ( element.childNodes[i].nodeName == tagName ) return element.childNodes[i];
      }
    }
    
    jQuery(document).ready(function($) {
        initPhoneTeacherTabs();
    });
    
    /*document.onreadystatechange = function(){
        initPhoneTeacherTabs();
    }*/
    function showContent(href){
        first = true;
        jQuery( \'#myModal .modal-body iframe\').attr(\'src\', href);
    }
    function closeModal(){
        jQuery(\'#myModal .modal-body iframe\').attr(\'src\', \'\');
    }
');
require_once(JPATH_COMPONENT_ADMINISTRATOR . DIRECTORY_SEPARATOR . "models" . DIRECTORY_SEPARATOR . "gurucertificate.php");
$program = $this->program;

$free_limit = "";

if (isset($program->free_limit) && trim($program->free_limit) != "") {
    $free_limit = intval($program->free_limit);
}

if ($free_limit == 0) {
    $free_limit = "";
}

require_once(JPATH_BASE . "/components/com_guru/helpers/Mobile_Detect.php");
$detect = new Mobile_Detect;
$deviceType = ($detect->isMobile() ? ($detect->isTablet() ? 'tablet' : 'phone') : 'computer');

if (isset($program->id) && $program->id != NULL) {
    $chb_free_courses = $this->selectedCoursesforFree();
    $step_access_courses = $this->getStepAccessCourses();
    $selected_course = $this->getSelectedCourse();
}
$lists = $program->lists;
$editorul = new JEditor(JFactory::getConfig()->get("editor"));

$mmediam = ((array)$this->mmediam) ? $this->mmediam : array();
//echo "<pre>";print_r($mmediam);die;
if (isset($this->mmediam_preq)) {
    foreach ($this->mmediam_preq as $element) {
        $vect[] = $element->id;
    }
}

$cid = JFactory::getApplication()->input->get("cid", array());
$id = intval(@$cid["0"]);

$config = $this->config;

$allow_teacher_action = json_decode($config->st_authorpage);
$teacher_edit_courses = $allow_teacher_action->teacher_edit_courses;

if (intval($program->id) > 0 && $teacher_edit_courses == 1) {
    $app = JFactory::getApplication();
    $app->redirect(JRoute::_("index.php?option=com_guru&view=guruauthor&task=authormycourses&layout=authormycourses"));
    return true;
}

$config_courses = json_decode($config->psgspage);
$courses_t_prop = $config_courses->courses_image_size_type == "0" ? "width" : "heigth";

$list_authors = $this->listAuthors();
$dateformat = $this->gurudateformat;

$upload_mb = guruAdminHelper::getUploadMaxSize();
$upload_mb *= 1048576; //transform in bytes
$doc = JFactory::getDocument();
$div_menu = $this->authorGuruMenuBar();

$free_courses_script = "";

if ($config->course_is_free_show == 1) {
    $free_courses_script = '    
        jQuery(document).ready(function($) {
            document.getElementById("free_courses").style.display = "' . (@$step_access_courses == 0 ? "block" : "none") . '";
        });
    ';
}

$doc->addScriptDeclaration('
    //jQuery.noConflict();
    jQuery(function(){
        function createUploader(){            
            var uploader = new qq.FileUploader({
                element: document.getElementById(\'fileUploader\'),
                action: \'' . JURI::root() . 'index.php?option=com_guru&controller=guruAuthor&tmpl=component&format=raw&task=upload_ajax_image\',
                params:{
                    folder:\'courses\',
                    mediaType:\'image\',
                    size: ' . intval($config_courses->courses_image_size) . ',
                    type: \'' . $courses_t_prop . '\'
                },
                onSubmit: function(id,fileName){
                    jQuery(\'.qq-upload-list li\').css(\'display\',\'none\');
                },
                onComplete: function(id,fileName,responseJSON){
                    if(responseJSON.success == true){
                        jQuery(\'.qq-upload-success\').append(\'- <span style="color:#387C44;">Upload successful</span>\');
                        if(responseJSON.locate) {
                            //jQuery(\'#view_imagelist23\').attr("src", "../"+responseJSON.locate +"/"+ fileName);
                            jQuery(\'#view_imagelist23\').attr("src", "' . JURI::root() . '"+responseJSON.locate +"/"+ fileName+"?timestamp=" + new Date().getTime());
                            jQuery(\'#image\').val(responseJSON.locate +"/"+ fileName);
                        }
                    }
                },
                allowedExtensions: [\'jpg\', \'jpeg\', \'png\', \'gif\', \'JPG\', \'JPEG\', \'PNG\', \'GIF\', \'xls\', \'XLS\'],
                sizeLimit: ' . $upload_mb . ',
                multiple: false,
                maxConnections: 1
            });           
        }
        createUploader();
    });
    
    ' . $free_courses_script . '
    
');

$doc->addScriptDeclaration('
    //jQuery.noConflict();
    jQuery(function(){
        function createUploader1(){            
            var uploader = new qq.FileUploader({
                element: document.getElementById(\'fileUploader1\'),
                action: \'' . JURI::root() . 'index.php?option=com_guru&controller=guruAuthor&tmpl=component&format=raw&task=upload_ajax_image\',
                params:{
                    folder:\'courses\',
                    mediaType:\'image\',
                    size: ' . intval($config_courses->courses_image_size) . ',
                    type: \'' . $courses_t_prop . '\'
                },
                onSubmit: function(id,fileName){
                    jQuery(\'.qq-upload-list li\').css(\'display\',\'none\');
                },
                onComplete: function(id,fileName,responseJSON){
                    if(responseJSON.success == true){
                        jQuery(\'.qq-upload-success\').append(\'- <span style="color:#387C44;">Upload successful</span>\');
                        if(responseJSON.locate) {
                            jQuery(\'#view_imagelist24\').attr("src", "' . JURI::root() . '"+responseJSON.locate +"/"+ fileName+"?timestamp=" + new Date().getTime());
                            jQuery(\'#image_avatar\').val(responseJSON.locate +"/"+ fileName);
                        }
                    }
                },
                allowedExtensions: [\'jpg\', \'jpeg\', \'png\', \'gif\', \'JPG\', \'JPEG\', \'PNG\', \'GIF\', \'xls\', \'XLS\'],
                sizeLimit: ' . $upload_mb . ',
                multiple: false,
                maxConnections: 1
            });           
        }
        createUploader1();
    });
    //jQuery(document).ready(function($) {
//      document.getElementById("free_courses").style.display = ' . (@$step_access_courses == 0 ? '"block"' : '"none"') . ';
//  });
//  
');
//$doc->addScript('components/com_guru/js/fileuploader.js');
JHtml::stylesheet('components/com_guru/css/fileuploader.css');
$doc->setTitle(trim(JText::_('GURU_AUTHOR')) . " " . trim(JText::_('GURU_AUTHOR_MY_COURSE')));

//$doc->addScript(JURI::root().'components/com_guru/js/redactor.min.js');
JHtml::stylesheet('components/com_guru/css/redactor.css');

JHtml::stylesheet("components/com_guru/css/tabs.css");

$og_title = "";
$og_type = "";
$og_image = "";
$og_url = "";
$og_desc = "";

if (isset($program->og_tags) && trim($program->og_tags) != "") {
    $og_tags = json_decode($program->og_tags, true);
    $og_title = $og_tags["og_title"];
    $og_type = $og_tags["og_type"];
    $og_image = $og_tags["og_image"];
    $og_url = $og_tags["og_url"];
    $og_desc = $og_tags["og_desc"];
}

$listDirn = "asc";
$listOrder = "ordering";
$saveOrderingUrl = 'index.php?option=com_guru&controller=guruAuthor&task=saveOrderExercices&tmpl=component';
JHtml::_('sortablelist.sortable', 'articleList', 'adminForm', strtolower($listDirn), $saveOrderingUrl);
JHtml::_('behavior.multiselect');
JHtml::_('dropdown.init');

?>

<script type="text/javascript" language="javascript">
    document.body.className = document.body.className.replace("modal", "");
</script>

<style type="text/css">
    #rowsmedia {
        background-color: #eeeeee;
    }

    #rowsmedia tr {
        background-color: white;
    }

    #rowsmainmedia {
        background-color: #eeeeee;
    }

    #rowsmainmedia tr {
        background-color: #eeeeee;
    }

    .accordionItem.hideTabs div {
        display: none;
    }
</style>

<script language="javascript" type="text/javascript">
    function selectrealtime(val) {
        var div = document.getElementById("free_courses");
        div.style.display = val == 0 ? "block" : "none";

        var div = document.getElementById("members-list");
        div.style.display = val == 1 ? "block" : "none";
    }

    function checkIfCertificateIsSet() {
        chb_free_courses = document.getElementById("chb_free_courses").checked;
        if (chb_free_courses == true) {
            if (document.getElementById("step_access_courses").value == 2) { // free for guest
                if (document.getElementById("certificate_setts").value != 1) { // not selected "no certificate"
                    document.getElementById("certificate_setts").value = 1;
                    alert("<?php echo JText::_("COM_GURU_NO_CERTIFICATE_2"); ?>");
                    return true;
                }
            }
        }
    }

    function guruShowLessons(value) {
        if (value == 1) {
            document.getElementById('lessons_release_td').style.display = "block";
            document.getElementById('lessons_show_td').style.display = "block";
            guruPopupChangeOption();
        } else {
            document.getElementById('lessons_release_td').style.display = "none";
            document.getElementById('lessons_show_td').style.display = "none";
            document.getElementById("based-on-hour").style.display = "none";
            document.getElementById("based-on-lesson-per-release").style.display = "none";
        }

    }

    function guruPopupChangeOption() {
        //alert("This change will be propagated over the way the lessons of this course will be released!");

        //$lesson_release_value = document.adminForm.lesson_release.value;
        var lesson_release = document.getElementById("lesson_release");
        var index = lesson_release.selectedIndex;
        $lesson_release_value = lesson_release.options[index].value;
        switch (parseInt($lesson_release_value)) {
            case 4:
                document.getElementById("based-on-hour").style.display = "block";
                document.getElementById("based-on-lesson-per-release").style.display = "none";
                break;
            case 0:
                document.getElementById("based-on-hour").style.display = "none";
                document.getElementById("based-on-lesson-per-release").style.display = "none";
                break
            default:
                document.getElementById("based-on-lesson-per-release").style.display = "block";
                document.getElementById("based-on-hour").style.display = "none";
                break;
        }
    }

    function ChangeTermCourse(nb, id) {
        chb_free_courses = false;
        if (eval(document.getElementById("chb_free_courses"))) {
            chb_free_courses = document.getElementById("chb_free_courses").checked;
        }

        if (chb_free_courses == true) {
            if (document.getElementById("step_access_courses").value == 2) { // free for guest
                if (nb != 1) { // select certificate
                    document.getElementById("certificate_setts").value = 1;
                    alert("<?php echo JText::_("COM_GURU_NO_CERTIFICATE_1"); ?>");
                    return false;
                }
            }
        }

        if (nb == 4 || nb == 6) {
            document.getElementById('avg_certificate').style.display = 'table-row';

            jQuery.ajax({
                url: Joomla.getOptions('system.paths').root + '/index.php?option=com_guru&controller=guruPrograms&task=savenbquizzes&id=' + id,
            });
        } else {
            document.getElementById('avg_certificate').style.display = 'none';

        }
        if (nb == 1) {
            document.getElementById('coursecertifiactemsg').style.display = 'none';
        } else {
            document.getElementById('coursecertifiactemsg').style.display = 'block';
        }
    }

    function IsNumeric(sText) {
        var ValidChars = "0123456789.";
        var IsNumber = true;
        var Char;
        for (i = 0; i < sText.length && IsNumber == true; i++) {
            Char = sText.charAt(i);
            if (ValidChars.indexOf(Char) == -1) {
                IsNumber = false;
            }
        }
        return IsNumber;
    }

    function isFloat(nr) {
        return parseFloat(nr.match(/^-?\d*(\.\d+)?$/)) > 0;
    }

    function saveCourse(pressbutton) {

        <?php if (!empty($config->require_cover_avatar)) : ?>
            var $cover = jQuery('input.image_cover');
            var $avatar = jQuery('input.image_avatar');

            if (!$cover.val()) {
                return alert('Missing course image cover.')
            }

            if (!$avatar.val()) {
                return alert('Missing course avatar');
            }
        <?php endif ?>

        var form = document.adminForm;
        var prisfinalq = document.getElementById('final_quizzes').value;
        var hasquiz = <?php if (isset($program->hasquiz)) {
                            echo $program->hasquiz;
                        } else {
                            echo 0;
                        } ?>;
        var certificateterm = document.getElementById('certificate_setts').value;
        var avgval = document.getElementById('avg_cert').value;

        var sDate = document.getElementById('startpublish').value;
        sDate = sDate.split(" ");
        sDate = sDate[0];

        var eDate = document.getElementById('endpublish').value;
        eDate = eDate.split(" ");
        eDate = eDate[0];

        sDate = new Date(sDate + "");
        eDate = new Date(eDate + "");

        sDate = sDate.getTime();
        eDate = eDate.getTime();

        if (pressbutton == 'save' || pressbutton == 'apply') {
            name = form["name"].value;
            id = document.getElementById("id").value;
            continue_save = true;

            /*var req = jQuery.ajax({
                async: false,
                method: 'get',
                url: '<?php echo JURI::root(); ?>index.php?option=com_guru&controller=guruPrograms&tmpl=component&format=raw&task=check_values&action=check_values&name='+name+'&id='+id,
                data: { 'do' : '1' },
                success: function(response){
                    if(response.trim() == "exist"){
                        alert("<?php echo JText::_("GURU_ALERT_EXISTING_NAME_ALIAS"); ?>");
                        continue_save = false;
                        return false;
                    }
                }
            });*/

            jQuery.ajax({
                async: false,
                url: '<?php echo JURI::root(); ?>index.php?option=com_guru&controller=guruPrograms&tmpl=component&format=raw&task=check_values&action=check_values&name=' + name + '&id=' + id,
                success: function(response) {
                    if (response.trim() == "exist") {
                        alert("<?php echo JText::_("GURU_ALERT_EXISTING_NAME_ALIAS"); ?>");
                        continue_save = false;
                        return false;
                    }
                }
            });

            if (!continue_save) {
                return false;
            }

            <?php if ($config->course_is_free_show == 1) { ?>
                chb_free_courses = document.getElementById("chb_free_courses").checked;
            <?php } else {
            ?>
                chb_free_courses = false;
            <?php
            }
            ?>

            if (chb_free_courses == false) {
                // start if check if price is correct
                k = 0;
                subscription_added = false;
                while (eval(document.getElementById("subscription_price_" + k))) {
                    if (document.getElementById("subscriptions_" + k).checked == true) {
                        subscription_added = true;
                        subscription_price = document.getElementById("subscription_price_" + k).value;
                        if (subscription_price != "") {
                            if (subscription_price <= 0) {
                                alert("<?php echo JText::_("GURU_ALERT_INVALID_PRICE"); ?>");
                                return false;
                            }
                        } else {
                            alert("<?php echo JText::_("GURU_ALERT_INVALID_PRICE"); ?>");
                            return false;
                        }
                    }
                    k++;
                }

                if (!subscription_added) {
                    alert("<?php echo JText::_("GURU_ALERT_ADD_PRICE"); ?>");
                    return false;
                }

                // chekc if default plan is selected
                subscriptions = document.getElementsByName("subscription_default");
                checked = false;
                for (i = 0; i < subscriptions.length; i++) {
                    if (subscriptions[i].checked) {
                        checked = true;
                        break;
                    }
                }

                if (!checked) {
                    alert("<?php echo JText::_("GURU_ALERT_ADD_DEFAULT_PLAN"); ?>");
                    return false;
                }

                k = 0;
                while (eval(document.getElementById("renewal_price_" + k))) {
                    if (document.getElementById("renewals_" + k).checked == true) {
                        renewal_price = document.getElementById("renewal_price_" + k).value;
                        if (renewal_price != "") {
                            if (renewal_price <= 0) {
                                alert("<?php echo JText::_("GURU_ALERT_INVALID_PRICE"); ?>");
                                return false;
                            }
                        } else {
                            alert("<?php echo JText::_("GURU_ALERT_INVALID_PRICE"); ?>");
                            return false;
                        }
                    }
                    k++;
                }
                // stop if check if price is correct
            }
            if ((certificateterm == 3 || certificateterm == 5) && prisfinalq == 0 && 0 != <?php echo intval($program->id); ?>) {
                alert("<?php echo JText::_("GURU_ALERT_CERT_FEX"); ?>");
                return false;
            } else if ((certificateterm == 4 || certificateterm == 6) && hasquiz == 0 && 0 != <?php echo intval($program->id); ?>) {
                alert("<?php echo JText::_("GURU_ALERT_CERT_QAVG"); ?>");
                return false;
            } else if ((isNaN(avgval) || parseInt(avgval) < 0) && pressbutton != 'cancel') {
                alert("<?php echo JText::_("GURU_INVALID_NUMBER_AVG"); ?>");
                return false;
            } else if (form['name'].value == "") {
                alert("<?php echo JText::_("GURU_CS_PLSINSNAME"); ?>");
                return false;
            } else if (form['catid'].value == -1) {
                alert("<?php echo JText::_("GURU_PR_PLSINSCATEG"); ?>");
                return false;
            } else if (document.getElementById('startpublish').value == '') {
                alert("<?php echo JText::_("GURU_INVALID_DATE"); ?>");
                document.getElementById('startdate').value = "";
                return false;
            } else if (document.getElementById('startpublish').value != '' && document.getElementById('endpublish').value != '' && sDate > eDate) {
                alert("<?php echo JText::_("GURU_DATE_GRATER"); ?>");
                return false;
            } else if (chb_free_courses == true) {
                if (form['step_access_courses'].value == 0 && form['selected_course[]'].value == 0) {
                    alert("<?php echo JText::_("GURU_SELECTED_COURSE_ALERT"); ?>");
                } else {
                    document.getElementById("form_task").value = pressbutton;
                    //submitform( pressbutton );
                    form.task.value = pressbutton;
                    form.submit();
                }
            } else {
                document.getElementById("form_task").value = pressbutton;
                if (pressbutton == 'apply') {
                    //submitform('apply');
                    form.task.value = pressbutton;
                    form.submit();
                } else {
                    //submitform( pressbutton );
                    form.task.value = pressbutton;
                    form.submit();
                }
            }
        } else {
            if (pressbutton == 'apply') {
                document.getElementById("form_task").value = pressbutton;
                //submitform('apply');
                form.task.value = pressbutton;
                form.submit();
            } else {
                document.getElementById("form_task").value = pressbutton;
                //submitform( pressbutton );
                form.task.value = pressbutton;
                form.submit();
            }
        }
    }

    function typevar(variable) {
        return (typeof(variable));
    }

    function delete_temp(i) {
        document.getElementById('tr' + i).style.display = 'none';
        document.getElementById('mediafiletodel').value = document.getElementById('mediafiletodel').value + ',' + i;
    }

    function delete_temp_preq(i) {
        var liveadded = document.getElementById('liveadded').value;
        var temp = new Array();
        var ok = true;
        temp = liveadded.split('|');
        document.getElementById('tr' + i).style.display = 'none';
        document.getElementById('preqfiletodel').value = document.getElementById('preqfiletodel').value + ',' + i;
        for (var j = 0; j <= temp.length - 1; j++) {
            if (document.getElementById('tr' + temp[j]).style.display != 'none') {
                ok = false;
            }
        }
        <?php
        if (isset($vect)) {
            echo "var exist = new Array();";
            $count = 0;
            foreach ($vect as $element) {
                echo "exist[" . $count . "]=" . $element . ";";
                $count++;
            }
        } else {
            echo "var exist=0;";
        }
        ?>
        if (typevar(exist) != 'number') {
            for (var j = 0; j <= exist.length - 1; j++) {
                if (document.getElementById('2tr' + exist[j]).style.display != 'none') {
                    ok = false;
                }
            }
        }
        if (ok == true) {
            document.getElementById('rowspreq').style.display = 'none';
        }
    }

    function delete_temp2_preq(i) {
        var liveadded = document.getElementById('liveadded').value;
        var temp = new Array();
        var ok = true;
        temp = liveadded.split('|');
        temp['test'] = 'b';
        document.getElementById('2tr' + i).style.display = 'none';
        document.getElementById('preqfiletodel').value = document.getElementById('preqfiletodel').value + ',' + i;
        if (temp.length > 1) {
            for (var j = 0; j <= temp.length - 1; j++) {
                if (document.getElementById('tr' + temp[j]).style.display != 'none') {
                    ok = false;
                }
            }
        }
        <?php
        if (isset($vect)) {
            echo "var exist = new Array();";
            $count = 0;
            foreach ($vect as $element) {
                echo "exist[" . $count . "]=" . $element . ";";
                $count++;
            }
        } else {
            echo "var exist=0;";
        }
        ?>
        if (typevar(exist) != 'number') {
            for (var j = 0; j <= exist.length - 1; j++) {
                if (document.getElementById('2tr' + exist[j]).style.display != 'none') {
                    ok = false;
                }
            }
        }
        if (ok == true) {
            document.getElementById('rowspreq').style.display = 'none';
        }
    }

    function deleteMedia(id) {
        var mediafiles;
        document.getElementById('tr' + id).style.display = "none";
        mediafiles = document.getElementById('mediafiles').value;
        array = new Array();
        array = mediafiles.split(",");
        for (i = 0; i < array.length; i++) {
            if (array[i] == id) {
                array.splice(i, 1);
            }
        }
        document.getElementById('mediafiles').value = array.toString();
    }

    function deleteCourse(id) {
        var mediafiles;
        document.getElementById('tr_' + id).style.display = "none";
        mediafiles = document.getElementById('preqfiles').value;
        array = new Array();
        array = mediafiles.split(",");
        for (i = 0; i < array.length; i++) {
            if (array[i] == id) {
                array.splice(i, 1);
            }
        }
        document.getElementById('preqfiles').value = array.toString();
    }

    function deleteImage(id) {
        var url = '<?php echo JURI::root(); ?>index.php?option=com_guru&controller=guruPrograms&tmpl=component&format=raw&task=delete_image_ajax&id=' + id + "&avatar=0";
        var req = jQuery.ajax({
            url: url,
            method: 'get',
            data: {
                'do': '1'
            },
            success: function() {
                document.getElementById('view_imagelist23').src = "<?php echo JURI::base(); ?>components/com_guru/images/blank.png";
                document.getElementById('deletebtn').style.display = "none";
                document.getElementById('img_name').value = "";
                document.getElementById('image').value = "";
            },
        })
        return true;
    }

    function deleteImageA(id) {
        var url = '<?php echo JURI::root(); ?>index.php?option=com_guru&controller=guruPrograms&tmpl=component&format=raw&task=delete_image_ajax&id=' + id + "&avatar=1";
        var req = jQuery.ajax({
            method: 'get',
            url: url,
            data: {
                'do': '1'
            },
            success: function() {
                document.getElementById('view_imagelist24').src = "<?php echo JURI::base(); ?>components/com_guru/images/blank.png";
                document.getElementById('deletebtn2').style.display = "none";
                document.getElementById('image_avatar2').value = "";
                document.getElementById('image_avatar').value = "";
            },
        })
        return true;
    }

    function checkPlans(id) {
        var $checked = jQuery('#' + id + ' .plain:checkbox:checked');
        var $total = jQuery('#' + id + ' .plain:checkbox');

        if ($checked.length < $total.length) {
            //console.log('Not all boxes are checked');
            $total.each(function() {
                jQuery(this).prop('checked', true);
            });
        } else {
            //console.log('All boxes are checked');
            $total.each(function() {
                jQuery(this).prop('checked', false);
            });
        }
    }

    function publishUn(i) {
        var url = '<?php echo JURI::root(); ?>index.php?option=com_guru&controller=guruAuthor&tmpl=component&format=raw&task=pub_unpub_ajax&id=' + i;
        var myAjax = jQuery.ajax({
            method: 'get',
            url: url,
            data: {
                'do': '1'
            },
            success: function(data) {
                if (data.trim() == 'publish') {
                    element_id = "g_publish" + i;
                    document.getElementById(element_id).className = "icon-ok";
                } else if (data.trim() == 'unpublish') {
                    element_id = "g_publish" + i;
                    document.getElementById(element_id).className = "icon-remove";
                }
            },
        })
        return true;
    }

    function checkNegativeNb(nb) {
        if (parseInt(nb) < 0 || isNaN(nb)) {
            alert("<?php echo JText::_("GURU_INVALID_NUMBER_AVG"); ?>");
            return false;
        } else {
            return true;
        }
    }

    function changeTab(tabid) {
        jQuery(".header-tab").each(function() {
            jQuery(this).removeClass("uk-active");
        });

        jQuery(".content-tab").each(function() {
            jQuery(this).removeClass("uk-active");
        });

        jQuery("#" + tabid).addClass("uk-active");
        jQuery("#" + tabid + "-content").addClass("uk-active");
    }
</script>

<div id="myModal" class="modal g_modal hide" style="position: fixed !important; display:none;">
    <div class="modal-header">
        <button id="g_modal_close_button" type="button" class="close" data-dismiss="modal" aria-hidden="true" onclick="javascript:closeModal();">&times;</button>
    </div>
    <div class="modal-body">
        <iframe id="g_add_course"></iframe>
    </div>
</div>


<div id="g_authoraddcourse" class="clearfix com-cont-wrap">
    <?php echo $div_menu; //MENU TOP OF AUTHORS 
    ?>
    <?php
    if ($deviceType != "phone") {
        // computer or tablet
    ?>
        <form action="<?php echo JURI::root(); ?>index.php?option=com_guru?view=guruAuthor" class="uk-form uk-form-horizontal wk-form wk-form-horizontal" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">
            <div class="uk-grid uk-margin-top uk-margin-bottom wk-grid wk-margin-top wk-margin-bottom">
                <div class="uk-width-1-1 uk-width-medium-1-3 wk-width-1-1 wk-width-medium-1-3">
                    <h2 class="gru-page-title"><?php if ($program->id < 1) echo JText::_('GURU_NEWCOURSE');
                                                else echo JText::_('GURU_EDITCOURSE'); ?></h2>
                </div>
                <div class="uk-width-1-1 uk-width-2-3 uk-text-right uk-hidden-small wk-width-1-1 wk-width-2-3 wk-text-right wk-hidden-small">
                    <div class="uk-button-group wk-button-group">
                        <input type="button" class="uk-button uk-button-success wk-button wk-button-success" value="<?php echo JText::_("GURU_SAVE"); ?>" onclick="javascript:saveCourse('apply');" />
                        <input type="button" class="uk-button uk-button-success wk-button wk-button-success" value="<?php echo JText::_("GURU_SAVE_AND_CLOSE"); ?>" onclick="javascript:saveCourse('save');" />

                        <?php
                        if (isset($program->id) && intval($program->id) != 0) {
                        ?>

                            <input type="button" class="uk-button uk-button-success wk-button wk-button-success" value="<?php echo JText::_("GURU_ADD_EDIT_LESSONS"); ?>" onclick="window.location='<?php echo JRoute::_("index.php?option=com_guru&view=guruauthor&task=treeCourse&pid=" . intval($program->id)); ?>';" />

                        <?php
                        }
                        ?>

                        <input type="button" class="uk-button uk-button-primary wk-button wk-button-primary" value="<?php echo JText::_("GURU_CLOSE"); ?>" onclick="document.location.href='<?php echo JRoute::_("index.php?option=com_guru&view=guruauthor&task=authormycourses&layout=authormycourses"); ?>';" />
                    </div>
                </div>
                <div class="uk-width-1-1 uk-visible-small wk-width-1-1 wk-visible-small">
                    <input type="button" class="uk-button uk-button-success uk-width-1-1 wk-button wk-button-success wk-width-1-1" value="<?php echo JText::_("GURU_SAVE"); ?>" onclick="javascript:saveCourse('apply');" />
                    <input type="button" class="uk-button uk-button-success uk-width-1-1 wk-button wk-button-success wk-width-1-1" value="<?php echo JText::_("GURU_SAVE_AND_CLOSE"); ?>" onclick="javascript:saveCourse('save');" />

                    <?php
                    if (isset($program->id) && intval($program->id) != 0) {
                    ?>

                        <input type="button" class="uk-button uk-button-success uk-width-1-1 wk-button wk-button-success wk-width-1-1" value="<?php echo JText::_("GURU_ADD_EDIT_LESSONS"); ?>" onclick="window.location='<?php echo JRoute::_("index.php?option=com_guru&view=guruauthor&task=treeCourse&pid=" . intval($program->id)); ?>';" />

                    <?php
                    }
                    ?>

                    <input type="button" class="uk-button uk-button-primary uk-width-1-1 wk-button wk-button-primary wk-width-1-1" value="<?php echo JText::_("GURU_CLOSE"); ?>" onclick="document.location.href='<?php echo JRoute::_("index.php?option=com_guru&view=guruauthor&task=authormycourses&layout=authormycourses"); ?>';" />
                </div>
            </div>

            <ul data-uk-switcher="{connect:'#subnav-pill-content-1'}" data-uk-tab class="uk-tab uk-padding-remove wk-tab wk-padding-remove">
                <li id="general-tab" onclick="javascript:changeTab('general-tab'); return false;" class="header-tab uk-active wk-active"><a href="#"><?php echo JText::_('GURU_GENERAL'); ?></a></li>
                <li id="desc-tab" onclick="javascript:changeTab('desc-tab'); return false;" class="header-tab"><a href="#"><?php echo JText::_('GURU_PRODDESC'); ?></a></li>
                <li id="avatar-tab" onclick="javascript:changeTab('avatar-tab'); return false;" class="header-tab"><a href="#"><?php echo JText::_('GURU_IMAGE_COVER'); ?></a></li>
                <li id="exercise-tab" onclick="javascript:changeTab('exercise-tab'); return false;" class="header-tab"><a href="#"><?php echo JText::_('GURU_EXERCISE_FILES'); ?></a></li>
                <li id="price-tab" onclick="javascript:changeTab('price-tab'); return false;" class="header-tab"><a href="#"><?php echo JText::_('GURU_PRICING_PLANS'); ?></a></li>
                <li id="require-tab" onclick="javascript:changeTab('require-tab'); return false;" class="header-tab"><a href="#"><?php echo JText::_('GURU_DAY_REQ'); ?></a></li>
            </ul>
            <br />

            <ul class="uk-switcher wk-switcher" id="subnav-pill-content-1">
                <li id="general-tab-content" class="uk-active wk-active content-tab">
                    <div class="uk-form-row wk-form-row">
                        <label class="uk-form-label wk-form-label" for="name">
                            <?php echo JText::_("GURU_PRODNAME"); ?>:
                            <span class="uk-text-danger wk-text-danger">*</span>
                        </label>
                        <div class="uk-form-controls wk-form-controls">
                            <input class="inputbox" type="text" name="name" size="40" maxlength="255" value="<?php echo str_replace('"', '&quot;', $program->name); ?>" />
                            <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_PRODNAME"); ?>">
                                <img border="0" src="components/com_guru/images/icons/tooltip.png">
                            </span>
                        </div>
                    </div>

                    <div class="uk-form-row wk-form-row">
                        <label class="uk-form-label wk-form-label" for="name">
                            <?php echo JText::_("GURU_CATEGPARENT"); ?>:
                            <span class="uk-text-danger wk-text-danger">*</span>
                        </label>
                        <div class="uk-form-controls wk-form-controls">
                            <span>
                                <?php $lists['treecateg'] = $this->list_all(0, "catid", $program->catid, $program->catid); ?>
                            </span>
                            <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_CATEGPARENT"); ?>">
                                <img border="0" src="components/com_guru/images/icons/tooltip.png">
                            </span>
                        </div>
                    </div>

                    <div class="uk-form-row wk-form-row">
                        <label class="uk-form-label wk-form-label" for="name">
                            <?php echo JText::_("GURU_LEVEL"); ?>:
                        </label>
                        <div class="uk-form-controls wk-form-controls">
                            <span>
                                <?php echo $lists['level']; ?>
                            </span>
                            <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_LEVEL"); ?>">
                                <img border="0" src="components/com_guru/images/icons/tooltip.png">
                            </span>
                        </div>
                    </div>

                    <?php
                    $js_installed = $this->isJSInstalled();

                    if ($js_installed) {
                        $js_groups = $this->getJSGroups();
                        $saved_group = "0";

                        if (isset($program->js_groups)) {
                            $saved_group = intval($program->js_groups);
                        }
                    ?>
                        <div class="uk-form-row wk-form-row">
                            <label class="uk-form-label wk-form-label" for="name">
                                <?php echo JText::_('GURU_STUDENT_TO_JS_GROUP'); ?>:
                            </label>
                            <div class="uk-form-controls wk-form-controls">
                                <select name="js_groups">
                                    <option value="0"> <?php echo JText::_("GURU_NO_ASSIGN"); ?> </option>
                                    <?php
                                    if (isset($js_groups) && count($js_groups) > 0) {
                                        foreach ($js_groups as $key => $group) {
                                            $selected = "";

                                            if (intval($saved_group) == intval($group["id"])) {
                                                $selected = 'selected="selected"';
                                            }
                                    ?>
                                            <option value="<?php echo intval($group["id"]); ?>" <?php echo $selected; ?>> <?php echo trim($group["name"]); ?> </option>
                                    <?php
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                    <?php
                    }
                    ?>

                    <div class="uk-form-row wk-form-row">
                        <label class="uk-form-label wk-form-label" for="name">
                            <?php echo JText::_("GURU_PRODLPBS"); ?>:
                        </label>
                        <div class="uk-form-controls wk-form-controls">
                            <?php echo $lists['published']; ?>
                            <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_PRODLPBS"); ?>">
                                <img border="0" src="components/com_guru/images/icons/tooltip.png">
                            </span>
                        </div>
                    </div>

                    <div class="uk-form-row wk-form-row">
                        <label class="uk-form-label wk-form-label" for="name">
                            <?php echo JText::_("GURU_PRODLSPUB"); ?>:
                        </label>
                        <div class="uk-form-controls wk-form-controls">
                            <?php
                            $format = "%m-%d-%Y";
                            switch ($dateformat) {
                                case "d-m-Y H:i:s":
                                    $format = "%d-%m-%Y %H:%M:%S";
                                    break;
                                case "d.m.Y H:i":
                                    $format = "%d.%m.%Y %H:%M";
                                    break;
                                case "d/m/Y H:i:s":
                                    $format = "%d/%m/%Y %H:%M:%S";
                                    break;
                                case "m-d-Y H:i:s":
                                    $format = "%m-%d-%Y %H:%M:%S";
                                    break;
                                case "m/d/Y H:i:s":
                                    $format = "%m/%d/%Y %H:%M:%S";
                                    break;
                                case "Y-m-d H:i:s":
                                    $format = "%Y-%m-%d %H:%M:%S";
                                    break;
                                case "Y/m/d H:i:s":
                                    $format = "%Y/%m/%d %H:%M:%S";
                                    break;
                                case "d-m-Y":
                                    $format = "%d-%m-%Y";
                                    break;
                                case "d/m/Y":
                                    $format = "%d/%m/%Y";
                                    break;
                                case "m-d-Y":
                                    $format = "%m-%d-%Y";
                                    break;
                                case "m/d/Y":
                                    $format = "%m/%d/%Y";
                                    break;
                                case "Y-m-d":
                                    $format = "%Y-%m-%d";
                                    break;
                                case "Y/m/d":
                                    $format = "%Y/%m/%d";
                                    break;
                            }
                            $jnow   = new JDate('now');
                            $now    = $jnow->toSQL(true);

                            if ($program->id < 1) $start_publish =  date("" . $dateformat . "", strtotime($now));
                            else $start_publish = date("" . $dateformat . "", strtotime($program->startpublish));
                            echo JHTML::_('calendar', $start_publish, 'startpublish', 'startpublish', $format, array("showTime" => "", "todayBtn" => "", "weekNumbers" => "", "fillTable" => "", "singleHeader" => "")); // calendar change
                            ?>
                            <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_PRODLSPUB"); ?>">
                                <img border="0" src="components/com_guru/images/icons/tooltip.png">
                            </span>
                        </div>
                    </div>

                    <div class="uk-form-row wk-form-row">
                        <label class="uk-form-label wk-form-label" for="name">
                            <?php echo JText::_("GURU_PRODLEPUB"); ?>:
                        </label>
                        <div class="uk-form-controls wk-form-controls">
                            <?php
                            if (substr($program->endpublish, 0, 4) == '0000' || $program->id < 1) {
                                $end_publish = "";
                            } else {
                                $end_publish = date("" . $dateformat . "", strtotime($program->endpublish));
                            }

                            echo JHTML::_('calendar', $end_publish, 'endpublish', 'endpublish', $format, array("showTime" => "", "todayBtn" => "", "weekNumbers" => "", "fillTable" => "", "singleHeader" => "")); // calendar change
                            ?>
                            <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_PRODLEPUB"); ?>">
                                <img border="0" src="components/com_guru/images/icons/tooltip.png">
                            </span>
                        </div>
                    </div>

                    <div class="uk-form-row wk-form-row">
                        <label class="uk-form-label wk-form-label" for="name">
                            <?php echo JText::_("GURU_SKIP_MODULE_PAGE"); ?>:
                        </label>
                        <div class="uk-form-controls wk-form-controls">
                            <span>
                                <?php
                                $skip_module = $program->skip_module;
                                if (!isset($skip_module) || $skip_module == "") {
                                    $skip_module = 0;
                                }
                                ?>
                                <input type="hidden" name="skip_module" value="0">
                                <?php
                                $checked = '';
                                if ($skip_module == 1) {
                                    $checked = 'checked="checked"';
                                }
                                ?>
                                <input type="checkbox" <?php echo $checked; ?> value="1" class="ace-switch ace-switch-5" name="skip_module">
                                <span class="lbl"></span>
                                <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_SKIP"); ?>">
                                    <img border="0" src="components/com_guru/images/icons/tooltip.png">
                                </span>
                            </span>
                        </div>
                    </div>

                    <div class="uk-form-row wk-form-row">
                        <label class="uk-form-label wk-form-label" for="name">
                            <?php echo JText::_("GURU_RESET_ON_RENEW"); ?>:
                        </label>
                        <div class="uk-form-controls wk-form-controls">
                            <span>
                                <?php
                                $reset_on_renew = $program->reset_on_renew;
                                if (!isset($reset_on_renew) || $reset_on_renew == "") {
                                    $reset_on_renew = 0;
                                }
                                ?>
                                <input type="hidden" name="reset_on_renew" value="0">
                                <?php
                                $checked = '';
                                if ($reset_on_renew == 1) {
                                    $checked = 'checked="checked"';
                                }
                                ?>
                                <input type="checkbox" <?php echo $checked; ?> value="1" class="ace-switch ace-switch-5" name="reset_on_renew">
                                <span class="lbl"></span>
                                <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_RESET_ON_RENEW"); ?>">
                                    <img border="0" src="components/com_guru/images/icons/tooltip.png">
                                </span>
                            </span>
                        </div>
                    </div>

                    <div class="uk-form-row wk-form-row">
                        <label class="uk-form-label wk-form-label" for="name">
                            <?php echo JText::_("GURU_COURSE_TYPE"); ?>:
                        </label>
                        <div class="uk-form-controls wk-form-controls">
                            <span>
                                <?php
                                $disabled = "";
                                /*if(intval($program->id) != 0){
                                            $disabled = 'disabled="disabled"';
                                            echo '<input type="hidden" name="course_type" value="'.$program->course_type.'" />';
                                        }*/
                                ?>
                                <select id="course_type" <?php echo $disabled; ?> name="course_type" onchange="javascript:guruShowLessons(value)">
                                    <option value="0" <?php if ($program->course_type == "0") {
                                                            echo 'selected="selected"';
                                                        } ?>><?php echo  JText::_("GURU_NON_SEQ"); ?></option>
                                    <option value="1" <?php if ($program->course_type == "1") {
                                                            echo 'selected="selected"';
                                                        } ?>><?php echo  JText::_("GURU_SEQ");  ?></option>
                                </select>
                            </span>
                            <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_SEQ"); ?>">
                                <img border="0" src="components/com_guru/images/icons/tooltip.png">
                            </span>
                        </div>
                    </div>

                    <div class="uk-form-row wk-form-row" id="lessons_release_td" <?php if ($program->course_type == 1) {
                                                                                        $style = "style=display:table-row;";
                                                                                        echo $style;
                                                                                    } else {
                                                                                        $style = "style=display:none;";
                                                                                        echo $style;
                                                                                    } ?>>
                        <label class="uk-form-label wk-form-label" for="name">
                            <?php echo JText::_("GURU_LESSON_RELEASE"); ?>:
                        </label>
                        <div class="uk-form-controls wk-form-controls">
                            <span>
                                <?php
                                $disabled = "";
                                /*if(intval($id) != 0){
                                            $disabled = 'disabled="disabled"';
                                            echo '<input type="hidden" name="lesson_release" value="'.$program->lesson_release.'" />';
                                        }*/
                                // start changes for lessons per release
                                ?>

                                <!--   <select id="lesson_release" <?php echo $disabled; ?> name="lesson_release" onchange="javascript:guruPopupChangeOption()">
                                  <option value="0" <?php if ($program->lesson_release == "0") {
                                                        echo 'selected="selected"';
                                                    } ?>><?php echo JText::_("GURU_ALL_AT_ONCE"); ?></option>
                                  <option value="1" <?php if ($program->lesson_release == "1") {
                                                        echo 'selected="selected"';
                                                    } ?>><?php echo JText::_("GURU_ONE_PER_DAY"); ?></option>
                                  <option value="2" <?php if ($program->lesson_release == "2") {
                                                        echo 'selected="selected"';
                                                    } ?>><?php echo JText::_("GURU_ONE_PER_W"); ?></option>
                                  <option value="3" <?php if ($program->lesson_release == "3") {
                                                        echo 'selected="selected"';
                                                    } ?>><?php echo JText::_("GURU_ONE_PER_M"); ?></option>
                                 </select> -->

                                <select id="lesson_release" <?php echo $disabled; ?> name="lesson_release" onchange="javascript:guruPopupChangeOption()">
                                    <option value="1" <?php if ($program->lesson_release == "1") {
                                                            echo 'selected="selected"';
                                                        } ?>><?php echo JText::_("GURU_LESSONS_PER_DAY"); ?></option>
                                    <option value="2" <?php if ($program->lesson_release == "2") {
                                                            echo 'selected="selected"';
                                                        } ?>><?php echo JText::_("GURU_LESSONS_PER_W"); ?></option>
                                    <option value="3" <?php if ($program->lesson_release == "3") {
                                                            echo 'selected="selected"';
                                                        } ?>><?php echo JText::_("GURU_LESSONS_PER_M"); ?></option>
                                    <option value="4" <?php if ($program->lesson_release == "4") {
                                                            echo 'selected="selected"';
                                                        } ?>><?php echo JText::_("GURU_BASED_ON_HOURS"); ?></option>
                                    <option value="0" <?php if ($program->lesson_release == "0") {
                                                            echo 'selected="selected"';
                                                        } ?>><?php echo JText::_("GURU_ALL_AT_ONCE"); ?></option>

                                </select>
                            </span>
                            <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_LESSON_RELEASE"); ?>">
                                <img border="0" src="components/com_guru/images/icons/tooltip.png">
                            </span>
                        </div>
                    </div>
                    <?php
                    $display_hour = "none";

                    if ($program->lesson_release == "4" && $program->course_type == 1) {
                        $display_hour = "block";
                    }

                    $display_lessons_per_release = "none";

                    if ($program->lesson_release != "4" && $program->lesson_release != "0" && isset($program->id) && $program->course_type == 1) {
                        $display_lessons_per_release = "block";
                    }
                    ?>

                    <div id="based-on-hour" class="uk-form-row wk-form-row" style="display: <?php echo $display_hour; ?>;">
                        <label class="uk-form-label wk-form-label" for="based-on-hour">
                            <?php echo JText::_("GURU_AFTER_EACH_HOUR"); ?>
                        </label>
                        <div class="uk-form-controls wk-form-controls">
                            <select style="" name="after_hours" <?php echo $disabled; ?>>
                                <?php
                                for ($h = 1; $h <= 12; $h++) {
                                    $selected = "";

                                    if ($program->after_hours == $h) {
                                        $selected = 'selected="selected"';
                                    }
                                ?>
                                    <option value="<?php echo $h; ?>" <?php echo $selected; ?>> <?php echo $h; ?> </option>
                                <?php
                                }
                                ?>
                            </select>
                            <?php echo JText::_("GURU_HOURS"); ?>
                        </div>
                    </div>

                    <div id="based-on-lesson-per-release" class="uk-form-row wk-form-row" style="display: <?php echo $display_lessons_per_release; ?>;">
                        <label class="uk-form-label wk-form-label" for="based-on-lesson-per-release">
                            <?php echo JText::_("GURU_SELECT_LESSONS_PER_RELEASE"); ?>
                        </label>
                        <div class="uk-form-controls wk-form-controls">
                            <select style="" name="lessons_per_release" <?php echo $disabled; ?>>
                                <?php
                                for ($h = 1; $h <= 12; $h++) {
                                    $selected = "";

                                    if ($program->lessons_per_release == $h) {
                                        $selected = 'selected="selected"';
                                    }
                                ?>
                                    <option value="<?php echo $h; ?>" <?php echo $selected; ?>> <?php echo $h; ?> </option>
                                <?php
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <?php   // end changes for lessons per release 
                    ?>

                    <div class="uk-form-row wk-form-row" id="lessons_show_td" <?php if ($program->course_type == 1) {
                                                                                    $style = "style=display:table-row;";
                                                                                    echo $style;
                                                                                } else {
                                                                                    $style = "style=display:none;";
                                                                                    echo $style;
                                                                                } ?>>
                        <label class="uk-form-label wk-form-label" for="name">
                            <?php echo JText::_("GURU_SHOW_UNRELEASED_LESSONS"); ?>:
                            <span class="uk-text-danger wk-text-danger">*</span>
                        </label>
                        <div class="uk-form-controls wk-form-controls">
                            <span>
                                <select id="lessons_show" name="lessons_show">
                                    <option value="1" <?php if ($program->lessons_show == "1") {
                                                            echo 'selected="selected"';
                                                        } ?>><?php echo JText::_("GURU_GRAYED_TEXT"); ?></option>
                                    <option value="2" <?php if ($program->lessons_show == "2") {
                                                            echo 'selected="selected"';
                                                        } ?>><?php echo JText::_("GURU_SHOULD_NOT_SHOW"); ?></option>
                                </select>
                            </span>
                            <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_SHOW_UNRELEASED_LESSONS"); ?>">
                                <img border="0" src="components/com_guru/images/icons/tooltip.png">
                            </span>
                        </div>
                    </div>

                    <div class="uk-form-row wk-form-row">
                        <label class="uk-form-label wk-form-label" for="name">
                            <?php echo JText::_("GURU_FINAL_EXAM"); ?>:
                            <span class="uk-text-danger wk-text-danger">*</span>
                        </label>
                        <div class="uk-form-controls wk-form-controls">
                            <span>
                                <select id="final_quizzes" name="final_quizzes">
                                    <option value="0" <?php if ($program->id_final_exam == "0") {
                                                            echo 'selected="selected"';
                                                        } ?>><?php echo JText::_("GURU_NO_FINAL_EXAM"); ?></option>
                                    <?php
                                    $cid = "0";
                                    $db = JFactory::getDBO();
                                    $user = JFactory::getUser();

                                    $sql = "SELECT `id` as cid , `name` as name FROM #__guru_quiz WHERE `is_final` = 1 and `author`=" . intval($user->id) . " ORDER by name asc";
                                    $db->setQuery($sql);
                                    $db->execute();
                                    $result = $db->loadAssocList();

                                    if (is_array($result) && count($result) > 0) {
                                        foreach ($result as $key => $values) {
                                            $quizid = $values["cid"];
                                            $quizname = $values["name"];

                                    ?>
                                            <option value="<?php echo $quizid; ?>" <?php if ($program->id_final_exam == $quizid) {
                                                                                        echo 'selected="selected"';
                                                                                    } ?>><?php echo $quizname; ?></option>
                                    <?php
                                        }
                                    }
                                    ?>
                                </select>
                            </span>
                            <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_FINAL_EXAM"); ?>">
                                <img border="0" src="components/com_guru/images/icons/tooltip.png">
                            </span>
                        </div>
                    </div>

                    <div class="uk-form-row wk-form-row">
                        <label class="uk-form-label wk-form-label" for="name">
                            <?php echo JText::_("GURU_CERTIFICATE_TERM"); ?>:
                        </label>
                        <div class="uk-form-controls wk-form-controls">
                            <span>
                                <select id="certificate_setts" name="certificate_setts" onchange="javascript:ChangeTermCourse(this.value,'<?php echo $program->id; ?>' )" class="uk-float-left wk-float-left">
                                    <option value="1" <?php if ($program->certificate_term  == "1") {
                                                            echo 'selected="selected"';
                                                        } ?>><?php echo JText::_("GURU_NO_CERTIFICATE"); ?></option>
                                    <option value="2" <?php if ($program->certificate_term  == "2") {
                                                            echo 'selected="selected"';
                                                        } ?>><?php echo JText::_("GURU_COMPLETE_ALL_LESSONS"); ?></option>
                                    <option value="3" <?php if ($program->certificate_term  == "3") {
                                                            echo 'selected="selected"';
                                                        } ?>><?php echo JText::_("GURU_PASS_FINAL_EXAM"); ?></option>
                                    <option value="4" <?php if ($program->certificate_term  == "4") {
                                                            echo 'selected="selected"';
                                                        } ?>><?php echo JText::_("GURU_PASS_THE_QUIZZES_IN_AVG"); ?></option>
                                    <option value="5" <?php if ($program->certificate_term  == "5") {
                                                            echo 'selected="selected"';
                                                        } ?>><?php echo JText::_("GURU_CERT_TERM_FALFE"); ?></option>
                                    <option value="6" <?php if ($program->certificate_term  == "6") {
                                                            echo 'selected="selected"';
                                                        } ?>><?php echo JText::_("GURU_CERT_TERM_FALPQAVG"); ?></option>
                                </select>
                            </span>

                            <div id="avg_certificate" class="uk-float-left wk-float-left" style=" <?php if ($program->certificate_term  == "4" || $program->certificate_term  == "6") {
                                                                                                        echo 'display:table-row;';
                                                                                                    } else {
                                                                                                        echo 'display:none; ';
                                                                                                    } ?>">
                                &nbsp;&nbsp;<input onchange="javascript:checkNegativeNb(this.value);" size="5px;" type="text" name="avg_cert" id="avg_cert" value="<?php if ($program->id == "") {
                                                                                                                                                                        echo '70';
                                                                                                                                                                    } else {
                                                                                                                                                                        echo $program->avg_certc;
                                                                                                                                                                    } ?>" class="input-mini" /> %
                            </div>

                            <span class="editlinktip hasTip uk-float-left wk-float-left" title="<?php echo JText::_("GURU_TIP_CERTIFICATE_TERM"); ?>">
                                <img border="0" src="components/com_guru/images/icons/tooltip.png">
                            </span>

                        </div>
                    </div>

                    <div class="uk-form-row wk-form-row" id="coursecertifiactemsg" style=" <?php if ($program->certificate_term  != "1" && $program->certificate_term  != "0") {
                                                                                                echo 'display:block;';
                                                                                            } else {
                                                                                                echo 'display:none;';
                                                                                            } ?>">
                        <label class="uk-form-label wk-form-label" for="name">
                            <?php echo JText::_("GURU_CERTIFICATE_COURSE_MSG"); ?>:
                        </label>
                        <div class="uk-form-controls wk-form-controls">
                            <span>
                                <textarea style="float-left" id="coursemessage" name="coursemessage" cols="50" rows="4" maxlength="7000"><?php echo stripslashes($program->certificate_course_msg); ?></textarea>
                            </span>
                            <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_CERTIFICATE_COURSE_MSG1"); ?>">
                                <img border="0" src="components/com_guru/images/icons/tooltip.png">
                            </span>
                        </div>
                    </div>
                </li>

                <li id="desc-tab-content" class="content-tab">
                    <div class="uk-form-row wk-form-row">
                        <div onmouseover="document.getElementById('editor_clicked').value = 'description';">
                            <textarea id="description" name="description" class="useredactor" style="width:100%; height:100px;"><?php echo $program->description; ?></textarea>
                            <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_PRODDESC"); ?>">
                                <img border="0" src="components/com_guru/images/icons/tooltip.png">
                            </span>
                        </div>
                </li>

                <li id="avatar-tab-content" class="content-tab">
                    <div class="uk-form-row wk-form-row">
                        <label class="uk-form-label wk-form-label" for="name">
                            <?php echo JText::_("GURU_OG_TITLE"); ?>:
                        </label>
                        <div class="uk-form-controls wk-form-controls">
                            <input type="text" name="og_title" value="<?php echo $og_title; ?>" />
                        </div>
                    </div>

                    <div class="uk-form-row wk-form-row">
                        <label class="uk-form-label wk-form-label" for="name">
                            <?php echo JText::_("GURU_OG_TYPE"); ?>:
                        </label>
                        <div class="uk-form-controls wk-form-controls">
                            <input type="text" name="og_type" value="<?php echo $og_type; ?>" />
                        </div>
                    </div>

                    <div class="uk-form-row wk-form-row">
                        <label class="uk-form-label wk-form-label" for="name">
                            <?php echo JText::_("GURU_OG_IMAGE"); ?>:
                        </label>
                        <div class="uk-form-controls wk-form-controls">
                            <input type="text" name="og_image" value="<?php echo $og_image; ?>" />
                        </div>
                    </div>

                    <div class="uk-form-row wk-form-row">
                        <label class="uk-form-label wk-form-label" for="name">
                            <?php echo JText::_("GURU_OG_URL"); ?>:
                        </label>
                        <div class="uk-form-controls wk-form-controls">
                            <input type="text" name="og_url" value="<?php echo $og_url; ?>" />
                        </div>
                    </div>

                    <div class="uk-form-row wk-form-row">
                        <label class="uk-form-label wk-form-label" for="name">
                            <?php echo JText::_("GURU_OG_DESC"); ?>:
                        </label>
                        <div class="uk-form-controls wk-form-controls">
                            <textarea name="og_desc"><?php echo $og_desc; ?></textarea>
                        </div>
                    </div>


                    <h4 class="gru-page-subtitle"><?php echo JText::_('GURU_IMAGE_COVER2'); ?></h4>

                    <div class="uk-alert wk-alert">
                        <?php echo JText::_('GURU_IMAGE_COVER_USAGE'); ?>
                    </div>

                    <div class="uk-form-row wk-form-row">
                        <label class="uk-form-label wk-form-label" for="name">
                            <?php echo JText::_("GURU_IMAGE_COVER2"); ?>:
                        </label>
                        <div class="uk-form-controls wk-form-controls">
                            <div style="float:left;">
                                <div id="fileUploader"></div>
                            </div>
                            &nbsp;
                            <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_IMAGE"); ?>">
                                <img border="0" src="components/com_guru/images/icons/tooltip.png">
                            </span>
                            <div style="float:left;">
                                <?php echo JText::_('GURU_RECOMMENDED_SIZE'); ?>
                            </div>
                            <input type="hidden" name="image" id="image" class="image_cover" value="<?php echo $program->image; ?>" />
                        </div>
                    </div>

                    <div class="uk-form-row wk-form-row">
                        <label class="uk-form-label wk-form-label" for="name">
                            <?php echo JText::_("GURU_PRODCIMG_COVER"); ?>:
                        </label>
                        <div class="uk-form-controls wk-form-controls">
                            <?php
                            if (isset($program->image) && $program->image != "") {

                            ?>
                                <img id="view_imagelist23" name="view_imagelist" src='<?php echo JURI::root(); ?><?php echo $program->image; ?>' />
                                <br />
                                <input style="margin-top:10px;" type="button" class="btn btn-danger" value="<?php echo JText::_('GURU_REMOVE'); ?>" onclick="deleteImage('<?php echo $program->id; ?>');" id="deletebtn" />
                                <input type="hidden" value="<?php echo $program->image; ?>" name="img_name" id="img_name" />
                            <?php
                            } else {
                                echo "<img id='view_imagelist23' name='view_imagelist' src='" . JURI::base() . "components/com_guru/images/blank.png'/>";
                            }
                            ?>
                        </div>
                    </div>

                    <h4 class="gru-page-subtitle"><?php echo JText::_('GURU_IMAGE_AVATAR'); ?></h4>

                    <div class="uk-alert wk-alert">
                        <?php echo JText::_('GURU_IMAGE_AVATAR_USAGE'); ?>
                    </div>

                    <div class="uk-form-row wk-form-row">
                        <label class="uk-form-label wk-form-label" for="name">
                            <?php echo JText::_("GURU_IMAGE_AVATAR"); ?>:
                        </label>
                        <div class="uk-form-controls wk-form-controls">
                            <div style="float:left;">
                                <div id="fileUploader1"></div>
                            </div>
                            &nbsp;
                            <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_IMAGE"); ?>">
                                <img border="0" src="components/com_guru/images/icons/tooltip.png">
                            </span>
                            <div style="float:left;">
                                <?php echo JText::_('GURU_RECOMMENDED_SIZE2'); ?>
                            </div>
                            <input type="hidden" name="image_avatar" id="image_avatar" class="image_avatar" value="<?php echo $program->image_avatar; ?>" />
                        </div>
                    </div>

                    <div class="uk-form-row wk-form-row">
                        <label class="uk-form-label wk-form-label" for="name">
                            <?php echo JText::_("GURU_PRODCIMG_AVATAR"); ?>:
                        </label>
                        <div class="uk-form-controls wk-form-controls">
                            <?php
                            if (isset($program->image_avatar) && $program->image_avatar != "") {
                            ?>
                                <img id="view_imagelist24" name="view_imagelist1" src='<?php echo JURI::root(); ?><?php echo $program->image_avatar; ?>' />
                                <br />
                                <input style="margin-top:10px;" type="button" class="btn btn-danger" value="<?php echo JText::_('GURU_REMOVE'); ?>" onclick="deleteImageA('<?php echo $program->id; ?>');" id="deletebtn2" />
                                <input type="hidden" value="<?php echo $program->image_avatar; ?>" name="image_avatar2" id="image_avatar2" />
                            <?php
                            } else {
                                echo "<img id='view_imagelist24' name='view_imagelist' src='" . JURI::base() . "components/com_guru/images/blank.png'/>";
                            }
                            ?>
                        </div>
                    </div>
                </li>

                <li id="exercise-tab-content" class="content-tab">
                    <a onclick="javascript:openMyModal(0, 0, '<?php echo JURI::root(); ?>index.php?option=com_guru&view=guruauthor&task=addexercise&tmpl=component&cid=<?php echo $program->id; ?>'); return false;" href="#">
                        <input type="button" class="uk-button uk-button-primary wk-button wk-button-primary" value="<?php echo JText::_("GURU_EXERCISE_FILE"); ?>" />
                    </a>

                    <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_EXERCISE_FILE"); ?>">
                        &nbsp;<img border="0" src="components/com_guru/images/icons/tooltip.png">
                    </span>

                    <table id="articleList" class="uk-table uk-table-striped uk-margin-top wk-table wk-table-striped wk-margin-top">
                        <tr>
                            <th class="g_cell_1">#</th>
                            <th class="g_cell_2"><?php echo JText::_('GURU_FILE_MEDIA_NAME'); ?></th>
                            <th class="g_cell_3"><?php echo JText::_('GURU_PUBLISHED'); ?></th>
                            <th class="g_cell_4"><?php echo JText::_("GURU_GUEST_ACCESS"); ?></th>
                            <th class="g_cell_5"><?php echo JText::_("GURU_REMOVE"); ?></th>
                        </tr>
                        <tbody id="rowsmedia">
                            <?php
                            $oldids = "";
                            $display_none = '';
                            if ($program->id != NULL) {
                                $existing_ids = $this->existing_ids($program->id);
                            }

                            $tt = array();
                            if (is_array(@$existing_ids) && count(@$existing_ids) > 0) {
                                foreach ($existing_ids as $ex_idz) {
                                    $tt[] = $ex_idz->media_id;
                                }
                            }
                            $more_media_files = new stdClass();
                            $more_media_files = (array)$more_media_files;

                            $i = 0;
                            $pageNav = new JPagination(count($mmediam), 0, count($mmediam));
                            $n = count($mmediam);

                            foreach ($mmediam as $mmedial) {
                            ?>
                                <tr class="guru_row" id="tr<?php echo intval($mmedial->media_id); ?>">
                                    <td class="g_cell_1">
                                        <?php echo $i + 1; ?>
                                        <input type="text" class="width-20 text-area-order " value="<?php echo $mmedial->order; ?>" size="5" name="order[]" style="display:none;">
                                        <?php
                                        $checked = JHTML::_('grid.id', $i, $mmedial->media_id);
                                        $checked = str_replace("<input ", '<input style="visibility:hidden;"', $checked);
                                        echo $checked;
                                        ?>
                                    </td>
                                    <td class="g_cell_2">
                                        <?php
                                        switch ($mmedial->type) {
                                            case "video": {
                                                    $img_path = JURI::root() . "components/com_guru/images/video.gif";
                                                    echo '<img src="' . $img_path . '" alt="video type"/>';
                                                }
                                                break;
                                            case "docs": {
                                                    $img_path = JURI::root() . "components/com_guru/images/doc.gif";
                                                    echo '<img src="' . $img_path . '" alt="video type"/>';
                                                }
                                                break;
                                            case "url": {
                                                    $img_path = JURI::root() . "components/com_guru/images/url.gif";
                                                    echo '<img src="' . $img_path . '" alt="video type"/>';
                                                }
                                                break;
                                            case "image": {
                                                    $img_path = JURI::root() . "components/com_guru/images/image.jpg";
                                                    echo '<img src="' . $img_path . '" alt="video type"/>';
                                                }
                                                break;
                                            case "audio": {
                                                    $img_path = JURI::root() . "components/com_guru/images/audio.gif";
                                                    echo '<img src="' . $img_path . '" alt="audio type"/>';
                                                }
                                                break;
                                            case "quiz": {
                                                    $img_path = JURI::root() . "components/com_guru/images/quiz.gif";
                                                    echo '<img src="' . $img_path . '" alt="quiz type"/>';
                                                }
                                                break;
                                            case "text": {
                                                    $img_path = JURI::root() . "components/com_guru/images/doc.gif";
                                                    echo '<img src="' . $img_path . '" alt="doc type"/>';
                                                }
                                                break;
                                            case "file": {
                                                    $img_path = JURI::root() . "components/com_guru/images/file.gif";
                                                    echo '<img src="' . $img_path . '" alt="doc type"/>';
                                                }
                                                break;
                                        }
                                        ?>
                                        <a class="a_guru" href="<?php echo JUri::root() ?>index.php?option=com_guru&controller=guruAuthor&task=editMedia&cid=<?php echo $mmedial->media_id; ?>" target="_blank"><?php echo $mmedial->name; ?></a>
                                    </td>
                                    <td class="g_cell_3">

                                        <?php
                                        if ($mmedial->published == 1) {
                                        ?>
                                            <a id="g_publish<?php echo $mmedial->media_id; ?>" style="cursor: pointer;" class="icon-ok" title="Publish Item" onclick="javascript:publishUn(<?php echo $mmedial->media_id; ?>)"></a>
                                        <?php
                                        } else { ?>
                                            <a id="g_publish<?php echo $mmedial->media_id; ?>" style="cursor: pointer;" class="icon-remove" title="Unpublish Item" onclick="javascript:publishUn(<?php echo $mmedial->media_id; ?>)"></a>
                                        <?php
                                        }
                                        ?>
                                    </td>
                                    <td class="g_cell_4">
                                        <select name="<?php echo "access" . $mmedial->media_id ?>">
                                            <option value="0" <?php if ($mmedial->access == 0) echo "selected"; ?>><?php echo JText::_("GURU_COU_STUDENTS"); ?></option>
                                            <option value="1" <?php if ($mmedial->access == 1) echo "selected"; ?>><?php echo JText::_("GURU_REG_MEMBERS"); ?></option>
                                            <option value="2" <?php if ($mmedial->access == 2) echo "selected"; ?>><?php echo JText::_("GURU_REG_GUESTS"); ?></option>
                                        </select>
                                    </td>
                                    <td class="g_cell_5"><?php
                                                            $img_path = "components/com_guru/images/delete2.gif";
                                                            echo '<img onclick="deleteMedia(\'' . $mmedial->media_id . '\');" src="' . $img_path . '" alt="delete"/>';
                                                            ?>
                                    </td>
                                </tr>
                                <?php $i++;
                                $oldids = $oldids . $mmedial->media_id . ',';
                            }
                            if (count($more_media_files) > 0)
                                foreach ($more_media_files as $more_media_files_val) {
                                    $display_none = '';
                                    if (!in_array($more_media_files_val->media_id, $tt)) { // if start
                                ?>
                                    <tr id="1tr<?php echo $more_media_files_val->media_id; ?>" <?php echo $display_none; ?>>
                                        <td width="8%"></td>
                                        <td width="39%">
                                            <a class="a_guru" href="<?php echo JUri::root() ?>index.php?option=com_guru&controller=guruAuthor&task=editMedia&cid=<?php echo $mmedial->media_id; ?>" target="_blank"><?php echo $more_media_files_val->name; ?></a>
                                        </td>
                                        <td width="9%">
                                            <?php echo $more_media_files_val->type; ?>
                                        </td>
                                        <td width="18%">--</td>
                                        <td width="12%">
                                            <?php echo $link2_remove; ?>
                                        </td>
                                        <td width="14%">
                                            <?php echo $more_media_files_val->published; ?>
                                        </td>
                                    </tr>
                            <?php $oldids = $oldids . $more_media_files_val->media_id . ',';
                                    } // if end
                                } // foreach end 
                            ?>
                        </tbody>
                    </table>
                    <input type="hidden" value="<?php echo $oldids; ?>" name="mediafiles" id="mediafiles">
                    <input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
                    <input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
                    <?php echo JHtml::_('form.token'); ?>
                </li>

                <li id="price-tab-content" class="content-tab">
                    <?php if ($config->course_is_free_show == 1) { ?>
                        <div>
                            <div class="uk-grid wk-grid">
                                <div class="uk-width-1-1 uk-width-medium-1-3 uk-margin-small-top wk-width-1-1 wk-width-medium-1-3 wk-margin-small-top">
                                    <input onclick="javascript:checkIfCertificateIsSet();" type="checkbox" id="chb_free_courses" name="chb_free_courses" <?php if (@$chb_free_courses == "1") echo 'checked="checked"';
                                                                                                                                                            else {
                                                                                                                                                            } ?> />
                                    <span class="lbl"><?php echo trim(JText::_('GURU_FREE_COURSES')); ?></span>
                                </div>
                                <div class="uk-width-1-1 uk-width-medium-1-3 wk-width-1-1 wk-width-medium-1-3">
                                    <select id="step_access_courses" name="step_access_courses" onchange="javascript:selectrealtime(this.value); checkIfCertificateIsSet();" class="uk-display-inline-block wk-display-inline-block">
                                        <option value="0" <?php if (@$step_access_courses == 0) echo "selected"; ?>><?php echo JText::_("GURU_COU_STUDENTS"); ?></option>
                                        <option value="1" <?php if (@$step_access_courses == 1) echo "selected"; ?>><?php echo JText::_("GURU_REG_MEMBERS"); ?></option>
                                        <option value="2" <?php if (@$step_access_courses == 2) echo "selected"; ?>><?php echo JText::_("GURU_REG_GUESTS"); ?></option>
                                    </select>
                                    <span class="uk-display-inline-block wk-display-inline-block"><?php echo trim(JText::_('GURU_OF')); ?></span>
                                </div>
                                <div class="uk-width-1-1 uk-width-medium-1-3 wk-width-1-1 wk-width-medium-1-3" id="free_courses">
                                    <?php $this->getCourseListForStudents(); ?>
                                </div>

                                <?php
                                $display = "none";
                                if (isset($chb_free_courses) && $chb_free_courses == "1") {
                                    if ($step_access_courses == "1") {
                                        $display = "block";
                                    }
                                }
                                ?>

                                <div class="uk-width-1-1 uk-width-medium-1-3 wk-width-1-1 wk-width-medium-1-3" id="members-list" style="display:<?php echo $display; ?>;">
                                    <?php
                                    $groups = array();
                                    if (isset($program->groups_access) && trim($program->groups_access) != "") {
                                        $groups = explode(",", trim($program->groups_access));
                                    }
                                    echo JHtml::_('access.usergroups', 'groups', $groups, true);
                                    ?>
                                </div>
                            </div>
                        </div>

                        <div style="margin-top: 10px; margin-bottom: 10px;">
                            <?php echo JText::_("GURU_LIMIT_FREE_NUMBER"); ?> <br />
                            <span class="muted"><?php echo JText::_("GURU_LIMIT_FREE_NUMBER_INFO"); ?></span>
                            <input type="text" class="span1" name="free_limit" value="<?php echo $free_limit; ?>">
                        </div>

                    <?php } ?>

                    <h4 class="gru-page-subtitle">
                        <?php echo trim(JText::_('GURU_SUBSCRIPTIONS')) . ' ' . trim(strtolower(JText::_('GURU_SUBS_PLANS'))); ?>&nbsp;
                        <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_SUBSCRIPTIONS"); ?>">
                            <img border="0" src="components/com_guru/images/icons/tooltip.png">
                        </span>
                    </h4>

                    <?php echo $this->plans; ?>

                    <h4 class="gru-page-subtitle">
                        <?php echo trim(JText::_('GURU_RENW_PLANS')) . ' ' . trim(strtolower(JText::_('GURU_RENW_PLANS'))); ?>&nbsp;
                        <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_RENW_PLANS"); ?>">
                            <img border="0" src="components/com_guru/images/icons/tooltip.png">
                        </span>
                    </h4>

                    <?php echo $this->renewals; ?>
                </li>

                <li id="require-tab-content" class="content-tab">
                    <?php
                    //$mmediam_preq = $this->mmediam_preq;
                    $mmediam_preq = ((array)$this->mmediam_preq) ? $this->mmediam_preq : array();
                    //echo "<pre>";print_r($mmediam_preq);die;
                    $table_display = "block";

                    if ($mmediam_preq == NULL || (is_array($mmediam_preq) && count($mmediam_preq) <= 0)) {
                        $table_display = "none";
                    }
                    ?>

                    <div id="table_courses_id" class="g_table_wrap" style="display:<?php echo $table_display; ?>;">
                        <div class="g_table uk-clearfix uk-margin-left wk-clearfix wk-margin-left">
                            <?php
                            $old_preq_ids = '';
                            if ($program->id != "" || $program->id != NULL) {
                                $preq_existing_ids = $this->preq_existing_ids($program->id);
                            }
                            ?>
                            <div id="rowspreq">
                                <div class="uk-grid wk-grid">
                                    <div class="uk-width-1-6 uk-text-bold wk-width-1-6 wk-text-bold">
                                        <?php echo JText::_("GURU_ID"); ?>
                                    </div>
                                    <div class="uk-width-3-6 uk-text-bold wk-width-3-6 wk-text-bold">
                                        <?php echo JText::_("GURU_NAME"); ?>
                                    </div>
                                    <div class="uk-width-2-6 uk-text-bold wk-width-2-6 wk-text-bold">
                                        <?php echo JText::_("GURU_REMOVE"); ?>
                                    </div>
                                </div>

                                <?php
                                $table_rows = "";
                                if ($mmediam_preq != NULL) {
                                    foreach ($mmediam_preq as $element) {
                                        $table_rows .= "<div id='tr_" . $element->media_id . "' class='uk-grid wk-grid' style='margin-top:5px;'>";
                                        $table_rows .=      "<div class='uk-width-1-6 wk-width-1-6'>
                                                                        " . $element->media_id . "
                                                                     </div>";
                                        $table_rows .=      '<div class="uk-width-3-6 wk-width-3-6">
                                                                        <a class="a_guru" target="_blank" href="' . JUri::root() . 'index.php?option=com_guru&view=guruPrograms&task=view&cid=' . $element->media_id . '">' . $element->name . "</a>
                                                                     </div>";
                                        $table_rows .=      '<div class="uk-width-2-6 wk-width-2-6">
                                                                        <img onclick="deleteCourse(\'' . $element->media_id . '\');" alt="delete" src="' . JURI::root() . "components/com_guru/images/delete2.gif" . '" />
                                                                     </div>';
                                        $table_rows .= "</div>";
                                    }
                                    echo $table_rows;
                                }
                                ?>
                            </div>
                        </div>
                    </div>

                    <h4 class="gru-page-subtitle"><?php echo JText::_("GURU_DAY_OPREQ"); ?>:</h4>
                    <div class="uk-form-row wk-form-row">
                        <div>
                            <div onmouseover="document.getElementById('editor_clicked').value = 'pre_req';">
                                <textarea id="full_bio" name="pre_req" class="useredactor" style="width:100%; height:100px;"><?php echo $program->pre_req; ?></textarea>
                            </div>
                            <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_DAY_OPREQ"); ?>">
                                <img border="0" src="components/com_guru/images/icons/tooltip.png">
                            </span>
                        </div>
                    </div>


                    <h4 class="gru-page-subtitle"><?php echo JText::_("GURU_DAY_PREQBK"); ?>:</h4>
                    <div class="uk-form-row wk-form-row">
                        <div>
                            <div onmouseover="document.getElementById('editor_clicked').value = 'pre_req_books';">
                                <textarea id="pre_req_books" name="pre_req_books" class="useredactor" style="width:100%; height:100px;"><?php echo $program->pre_req_books; ?></textarea>
                            </div>
                            <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_DAY_PREQBK"); ?>">
                                <img border="0" src="components/com_guru/images/icons/tooltip.png">
                            </span>
                        </div>
                    </div>

                    <h4 class="gru-page-subtitle"><?php echo JText::_("GURU_DAY_OREQ"); ?>:</h4>
                    <div class="uk-form-row wk-form-row">
                        <div>
                            <div onmouseover="document.getElementById('editor_clicked').value = 'reqmts';">
                                <textarea id="reqmts" name="reqmts" class="useredactor" style="width:100%; height:100px;"><?php echo $program->reqmts; ?></textarea>
                            </div>
                            <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_DAY_OREQ"); ?>">
                                <img border="0" src="components/com_guru/images/icons/tooltip.png">
                            </span>
                        </div>
                    </div>
                </li>

            </ul>

            <input type="hidden" name="id" id="id" value="<?php echo $program->id; ?>" />
            <input type="hidden" name="task" id="form_task" value="" />
            <input type="hidden" name="option" value="com_guru" />
            <input type="hidden" name="controller" value="guruAuthor" />
            <input type="hidden" name="view" value="guruauthor" />
            <input type="hidden" name="boxchecked" value="" />
            <input type="hidden" name="task2" value="edit" />
            <input type="hidden" name="media_number" value="<?php echo @$n; ?>" id="media_number" />
            <input type="hidden" id="editor_clicked" name="editor_clicked" value="" />
            <input type="hidden" name="g_page" value="<?php if (intval($program->id) == 0) {
                                                            echo "courseadd";
                                                        } else {
                                                            echo "courseedit";
                                                        } ?>">

            <script type="text/javascript" language="javascript">
                function jSelectArticle(id, title, catid, object, link, lang) {
                    editor_clicked = document.getElementById("editor_clicked").value;

                    var hreflang = '';
                    if (lang !== '') {
                        var hreflang = ' hreflang = "' + lang + '"';
                    }
                    var tag = '<a' + hreflang + ' href="' + link + '">' + title + '</a>';
                    jInsertEditorText(tag, editor_clicked);
                    SqueezeBox.close();
                }
            </script>
            <hr class="uk-divider wk-divider">
            <div class="uk-margin-top uk-text-right wk-margin-top wk-text-right">
                <div class="uk-button-group wk-button-group">
                    <input type="button" class="uk-button uk-button-success wk-button wk-button-success" value="<?php echo JText::_("GURU_SAVE"); ?>" onclick="javascript:saveCourse('apply');" />
                    <input type="button" class="uk-button uk-button-success wk-button wk-button-success" value="<?php echo JText::_("GURU_SAVE_AND_CLOSE"); ?>" onclick="javascript:saveCourse('save');" />
                    <input type="button" class="uk-button uk-button-primary wk-button wk-button-primary" value="<?php echo JText::_("GURU_CLOSE"); ?>" onclick="document.location.href='<?php echo JRoute::_("index.php?option=com_guru&view=guruauthor&task=authormycourses&layout=authormycourses"); ?>';" />
                </div>
            </div>
        </form>
    <?php
    } elseif ($deviceType == "phone") {
        // if device is phone
    ?>
        <form action="<?php echo JURI::root(); ?>index.php?option=com_guru?view=guruAuthor" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">
            <div class="page_title">
                <h2>
                    <?php if ($program->id < 1) echo JText::_('GURU_NEWCOURSE');
                    else echo JText::_('GURU_EDITCOURSE'); ?>
                </h2>
                <div class="uk-width-1-1 uk-width-2-3 uk-text-right uk-hidden-small wk-width-1-1 wk-width-2-3 wk-text-right wk-hidden-small">
                    <div class="uk-button-group wk-button-group">
                        <input type="button" class="uk-button uk-button-success wk-button wk-button-success" value="<?php echo JText::_("GURU_SAVE"); ?>" onclick="javascript:saveCourse('apply');" />
                        <input type="button" class="uk-button uk-button-success wk-button wk-button-success" value="<?php echo JText::_("GURU_SAVE_AND_CLOSE"); ?>" onclick="javascript:saveCourse('save');" />

                        <?php
                        if (isset($program->id) && intval($program->id) != 0) {
                        ?>

                            <input type="button" class="uk-button uk-button-success wk-button wk-button-success" value="<?php echo JText::_("GURU_ADD_EDIT_LESSONS"); ?>" onclick="window.location='<?php echo JRoute::_("index.php?option=com_guru&view=guruauthor&task=treeCourse&pid=" . intval($program->id)); ?>';" />

                        <?php
                        }
                        ?>

                        <input type="button" class="uk-button uk-button-primary wk-button wk-button-primary" value="<?php echo JText::_("GURU_CLOSE"); ?>" onclick="document.location.href='<?php echo JRoute::_("index.php?option=com_guru&view=guruauthor&task=authormycourses&layout=authormycourses"); ?>';" />
                    </div>
                </div>
                <div class="uk-width-1-1 uk-visible-small wk-width-1-1 wk-visible-small">
                    <input type="button" class="uk-button uk-button-success uk-width-1-1 wk-button wk-button-success wk-width-1-1" value="<?php echo JText::_("GURU_SAVE"); ?>" onclick="javascript:saveCourse('apply');" />
                    <input type="button" class="uk-button uk-button-success uk-width-1-1 wk-button wk-button-success wk-width-1-1" value="<?php echo JText::_("GURU_SAVE_AND_CLOSE"); ?>" onclick="javascript:saveCourse('save');" />

                    <?php
                    if (isset($program->id) && intval($program->id) != 0) {
                    ?>

                        <input type="button" class="uk-button uk-button-success uk-width-1-1 wk-button wk-button-success wk-width-1-1" value="<?php echo JText::_("GURU_ADD_EDIT_LESSONS"); ?>" onclick="window.location='<?php echo JRoute::_("index.php?option=com_guru&view=guruauthor&task=treeCourse&pid=" . intval($program->id)); ?>';" />

                    <?php
                    }
                    ?>

                    <input type="button" class="uk-button uk-button-primary uk-width-1-1 wk-button wk-button-primary wk-width-1-1" value="<?php echo JText::_("GURU_CLOSE"); ?>" onclick="document.location.href='<?php echo JRoute::_("index.php?option=com_guru&view=guruauthor&task=authormycourses&layout=authormycourses"); ?>';" />
                </div>
            </div>

            <div id="g_registrationformauthorcontent_mobile" class="uk-hidden-large uk-hidden-medium wk-hidden-large wk-hidden-medium">
                <div class="container-fluid">
                    <div id="accordion" class="accordion">

                        <div class="accordionItem">
                            <h3 class="g_accordion-group ui-corner-all g_title_active"><?php echo JText::_("GURU_GENERAL"); ?></h3>
                            <div class="clearfix tab-body  g_accordion-group g_content_active">
                                <div class="control-group clearfix">
                                    <label class="control-label g_cell span3" for="name"><?php echo JText::_("GURU_PRODNAME"); ?>: <font color="#ff0000">*</font></label>
                                    <div class="controls g_cell span9">
                                        <span>
                                            <input class="inputbox" type="text" name="name" size="40" maxlength="255" value="<?php echo $program->name; ?>" />
                                            <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_PRODNAME"); ?>">
                                                <img border="0" src="components/com_guru/images/icons/tooltip.png">
                                            </span>
                                        </span>
                                    </div>
                                </div>

                                <div class="control-group">
                                    <label class="control-label" for="name"><?php echo JText::_("GURU_CATEGPARENT"); ?>:</label>
                                    <div class="controls">
                                        <?php $lists['treecateg'] = $this->list_all(0, "catid", $program->catid, $program->catid); ?>
                                        <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_CATEGPARENT"); ?>">
                                            <img border="0" src="components/com_guru/images/icons/tooltip.png">
                                        </span>
                                    </div>
                                </div>

                                <div class="control-group">
                                    <label class="control-label" for="name"><?php echo JText::_("GURU_LEVEL"); ?>:</label>
                                    <div class="controls">
                                        <?php echo $lists['level']; ?>
                                        <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_LEVEL"); ?>">
                                            <img border="0" src="components/com_guru/images/icons/tooltip.png">
                                        </span>
                                    </div>
                                </div>

                                <div class="g_date_style">
                                    <div class="control-group clearfix">
                                        <label class="control-label g_cell span3" for="name"><?php echo JText::_("GURU_PRODLPBS"); ?>:</label>
                                        <div class="controls g_cell span5">
                                            <?php echo $lists['published']; ?>
                                            <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_PRODLPBS"); ?>">
                                                <img border="0" src="components/com_guru/images/icons/tooltip.png">
                                            </span>
                                        </div>
                                    </div>
                                    <div class="control-group clearfix">
                                        <label class="control-label g_cell span3" for="name"><?php echo JText::_("GURU_PRODLSPUB"); ?>:</label>
                                        <div class="controls g_cell span5">
                                            <?php
                                            $format = "%m-%d-%Y";
                                            switch ($dateformat) {
                                                case "d-m-Y H:i:s":
                                                    $format = "%d-%m-%Y %H:%M:%S";
                                                    break;
                                                case "d.m.Y H:i":
                                                    $format = "%d.%m.%Y %H:%M";
                                                    break;
                                                case "d/m/Y H:i:s":
                                                    $format = "%d/%m/%Y %H:%M:%S";
                                                    break;
                                                case "m-d-Y H:i:s":
                                                    $format = "%m-%d-%Y %H:%M:%S";
                                                    break;
                                                case "m/d/Y H:i:s":
                                                    $format = "%m/%d/%Y %H:%M:%S";
                                                    break;
                                                case "Y-m-d H:i:s":
                                                    $format = "%Y-%m-%d %H:%M:%S";
                                                    break;
                                                case "Y/m/d H:i:s":
                                                    $format = "%Y/%m/%d %H:%M:%S";
                                                    break;
                                                case "d-m-Y":
                                                    $format = "%d-%m-%Y";
                                                    break;
                                                case "d/m/Y":
                                                    $format = "%d/%m/%Y";
                                                    break;
                                                case "m-d-Y":
                                                    $format = "%m-%d-%Y";
                                                    break;
                                                case "m/d/Y":
                                                    $format = "%m/%d/%Y";
                                                    break;
                                                case "Y-m-d":
                                                    $format = "%Y-%m-%d";
                                                    break;
                                                case "Y/m/d":
                                                    $format = "%Y/%m/%d";
                                                    break;
                                            }
                                            $jnow   = new JDate('now');
                                            $now    = $jnow->toSQL(true);

                                            if ($program->id < 1) $start_publish =  date("" . $dateformat . "", strtotime($now));
                                            else $start_publish = date("" . $dateformat . "", strtotime($program->startpublish));

                                            echo JHTML::_('calendar', $start_publish, 'startpublish', 'startpublish', $format, array("showTime" => "", "todayBtn" => "", "weekNumbers" => "", "fillTable" => "", "singleHeader" => "")); // calendar change

                                            ?>

                                            <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_PRODLSPUB"); ?>">
                                                <img border="0" src="components/com_guru/images/icons/tooltip.png">
                                            </span>
                                        </div>

                                    </div>
                                    <div class="control-group clearfix">
                                        <label class="control-label g_cell span3" for="name"><?php echo JText::_("GURU_PRODLEPUB"); ?>:</label>
                                        <div class="controls g_cell span5">
                                            <?php
                                            if (substr($program->endpublish, 0, 4) == '0000' || $program->id < 1) {
                                                $end_publish = "";
                                            } else {
                                                $end_publish = date("" . $dateformat . "", strtotime($program->endpublish));
                                            }

                                            echo JHTML::_('calendar', $end_publish, 'endpublish', 'endpublish', $format, array("showTime" => "", "todayBtn" => "", "weekNumbers" => "", "fillTable" => "", "singleHeader" => "")); // calendar change
                                            ?>
                                            <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_PRODLEPUB"); ?>">
                                                <img border="0" src="components/com_guru/images/icons/tooltip.png">
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="control-group clearfix">
                                    <label class="control-label g_cell span3" for="name"><?php echo JText::_("GURU_SKIP_MODULE_PAGE"); ?>:</label>
                                    <div class="controls g_cell span5">
                                        <span>
                                            <?php
                                            $skip_module = $program->skip_module;
                                            if (!isset($skip_module) || $skip_module == "") {
                                                $skip_module = 0;
                                            }
                                            ?>
                                            <input type="hidden" name="skip_module" value="0">
                                            <?php
                                            $checked = '';
                                            if ($skip_module == 1) {
                                                $checked = 'checked="checked"';
                                            }
                                            ?>
                                            <input type="checkbox" <?php echo $checked; ?> value="1" class="ace-switch ace-switch-5" name="skip_module">
                                            <span class="lbl"></span>
                                        </span>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <label class="control-label" for="name"><?php echo JText::_("GURU_COURSE_TYPE"); ?>:</label>
                                    <div class="controls">
                                        <?php
                                        $disabled = "";
                                        /*if(intval($program->id) != 0){
                                                    $disabled = 'disabled="disabled"';
                                                    echo '<input type="hidden" name="course_type" value="'.$program->course_type.'" />';
                                                }*/
                                        ?>
                                        <select id="course_type" class="input-medium" <?php echo $disabled; ?> name="course_type" onchange="javascript:guruShowLessons(value)">
                                            <option value="0" <?php if ($program->course_type == "0") {
                                                                    echo 'selected="selected"';
                                                                } ?>><?php echo  JText::_("GURU_NON_SEQ"); ?></option>
                                            <option value="1" <?php if ($program->course_type == "1") {
                                                                    echo 'selected="selected"';
                                                                } ?>><?php echo  JText::_("GURU_SEQ");  ?></option>
                                        </select>
                                        <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_SEQ"); ?>">
                                            <img border="0" src="components/com_guru/images/icons/tooltip.png">
                                        </span>
                                    </div>
                                </div>

                                <div class="control-group clearfix" id="lessons_release_td" <?php if ($program->course_type == 1) {
                                                                                                $style = "style=display:block;";
                                                                                                echo $style;
                                                                                            } else {
                                                                                                $style = "style=display:none;";
                                                                                                echo $style;
                                                                                            } ?>>
                                    <label class="control-label g_cell span3" for="name"><?php echo JText::_("GURU_LESSON_RELEASE"); ?>:</label>
                                    <div class="controls g_cell span5">
                                        <?php
                                        $disabled = "";
                                        /*if(intval($id) != 0){
                                                    $disabled = 'disabled="disabled"';
                                                    echo '<input type="hidden" name="lesson_release" value="'.$program->lesson_release.'" />';
                                                }*/
                                        ?>

                                        <select id="lesson_release" class="input-medium" <?php echo $disabled; ?> name="lesson_release" onchange="javascript:guruPopupChangeOption()">
                                            <option value="0" <?php if ($program->lesson_release == "0") {
                                                                    echo 'selected="selected"';
                                                                } ?>><?php echo JText::_("GURU_ALL_AT_ONCE"); ?></option>
                                            <option value="1" <?php if ($program->lesson_release == "1") {
                                                                    echo 'selected="selected"';
                                                                } ?>><?php echo JText::_("GURU_ONE_PER_DAY"); ?></option>
                                            <option value="2" <?php if ($program->lesson_release == "2") {
                                                                    echo 'selected="selected"';
                                                                } ?>><?php echo JText::_("GURU_ONE_PER_W"); ?></option>
                                            <option value="3" <?php if ($program->lesson_release == "3") {
                                                                    echo 'selected="selected"';
                                                                } ?>><?php echo JText::_("GURU_ONE_PER_M"); ?></option>
                                        </select>
                                        <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_LESSON_RELEASE"); ?>">
                                            <img border="0" src="components/com_guru/images/icons/tooltip.png">
                                        </span>
                                    </div>
                                </div>

                                <div class="control-group clearfix" id="lessons_show_td" <?php if ($program->course_type == 1) {
                                                                                                $style = "style=display:block;";
                                                                                                echo $style;
                                                                                            } else {
                                                                                                $style = "style=display:none;";
                                                                                                echo $style;
                                                                                            } ?>>
                                    <label class="control-label g_cell span3" for="name"><?php echo JText::_("GURU_SHOW_UNRELEASED_LESSONS"); ?>:</label>
                                    <div class="controls g_cell span5">
                                        <select id="lessons_show" class="input-medium" name="lessons_show">
                                            <option value="1" <?php if ($program->lessons_show == "1") {
                                                                    echo 'selected="selected"';
                                                                } ?>><?php echo JText::_("GURU_GRAYED_TEXT"); ?></option>
                                            <option value="2" <?php if ($program->lessons_show == "2") {
                                                                    echo 'selected="selected"';
                                                                } ?>><?php echo JText::_("GURU_SHOULD_NOT_SHOW"); ?></option>
                                        </select>
                                        <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_SHOW_UNRELEASED_LESSONS"); ?>">
                                            <img border="0" src="components/com_guru/images/icons/tooltip.png">
                                        </span>
                                    </div>
                                </div>

                                <div class="control-group clearfix">
                                    <label class="control-label g_cell span3" for="name"><?php echo JText::_("GURU_FINAL_EXAM"); ?>:</label>
                                    <div class="controls g_cell span5">
                                        <select id="final_quizzes" class="input-medium" name="final_quizzes">
                                            <option value="0" <?php if ($program->id_final_exam == "0") {
                                                                    echo 'selected="selected"';
                                                                } ?>><?php echo JText::_("GURU_NO_FINAL_EXAM"); ?></option>
                                            <?php
                                            $cid = "0";
                                            $db = JFactory::getDBO();
                                            $user = JFactory::getUser();
                                            $sql = "SELECT `id` as cid, `name` as name FROM #__guru_quiz WHERE `is_final` = 1 and `author`=" . intval($user->id) . " ORDER by name asc";

                                            $db->setQuery($sql);
                                            $db->execute();
                                            $result = $db->loadAssocList();

                                            if (is_array($result) && count($result) > 0) {
                                                foreach ($result as $key => $values) {
                                                    $quizid = $values["cid"];
                                                    $quizname = $values["name"];

                                            ?>
                                                    <option value="<?php echo $quizid; ?>" <?php if ($program->id_final_exam == $quizid) {
                                                                                                echo 'selected="selected"';
                                                                                            } ?>><?php echo $quizname; ?></option>
                                            <?php
                                                }
                                            }
                                            ?>
                                        </select>
                                        <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_FINAL_EXAM"); ?>">
                                            <img border="0" src="components/com_guru/images/icons/tooltip.png">
                                        </span>
                                    </div>
                                </div>
                                <div class="control-group clearfix">
                                    <label class="control-label g_cell span3" for="name"><?php echo JText::_("GURU_CERTIFICATE_TERM"); ?>:</label>
                                    <div class="controls g_cell span5">
                                        <select id="certificate_setts" class="input-medium" name="certificate_setts" onchange="javascript:ChangeTermCourse(this.value,'<?php echo $program->id; ?>' )">
                                            <option value="1" <?php if ($program->certificate_term  == "1") {
                                                                    echo 'selected="selected"';
                                                                } ?>><?php echo JText::_("GURU_NO_CERTIFICATE"); ?></option>
                                            <option value="2" <?php if ($program->certificate_term  == "2") {
                                                                    echo 'selected="selected"';
                                                                } ?>><?php echo JText::_("GURU_COMPLETE_ALL_LESSONS"); ?></option>
                                            <option value="3" <?php if ($program->certificate_term  == "3") {
                                                                    echo 'selected="selected"';
                                                                } ?>><?php echo JText::_("GURU_PASS_FINAL_EXAM"); ?></option>
                                            <option value="4" <?php if ($program->certificate_term  == "4") {
                                                                    echo 'selected="selected"';
                                                                } ?>><?php echo JText::_("GURU_PASS_THE_QUIZZES_IN_AVG"); ?></option>
                                            <option value="5" <?php if ($program->certificate_term  == "5") {
                                                                    echo 'selected="selected"';
                                                                } ?>><?php echo JText::_("GURU_CERT_TERM_FALFE"); ?></option>
                                            <option value="6" <?php if ($program->certificate_term  == "6") {
                                                                    echo 'selected="selected"';
                                                                } ?>><?php echo JText::_("GURU_CERT_TERM_FALPQAVG"); ?></option>
                                        </select>
                                        <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_CERTIFICATE_TERM"); ?>">
                                            <img border="0" src="components/com_guru/images/icons/tooltip.png">
                                        </span>
                                    </div>

                                    <div class="controls g_cell span3" id="avg_certificate" style=" <?php if ($program->certificate_term  == "4" || $program->certificate_term  == "6") {
                                                                                                        echo 'display:table-row;';
                                                                                                    } else {
                                                                                                        echo 'display:none; ';
                                                                                                    } ?>">
                                        <input onchange="javascript:checkNegativeNb(this.value);" size="5px;" type="text" name="avg_cert" id="avg_cert" value="<?php if ($program->id == "") {
                                                                                                                                                                    echo '70';
                                                                                                                                                                } else {
                                                                                                                                                                    echo $program->avg_certc;
                                                                                                                                                                } ?>" class="input-mini" /> %
                                    </div>
                                </div>
                                <div class="control-group clearfix" id="coursecertifiactemsg" style=" <?php if ($program->certificate_term  != "1" && $program->certificate_term  != "0") {
                                                                                                            echo 'display:block;';
                                                                                                        } else {
                                                                                                            echo 'display:none;';
                                                                                                        } ?>">
                                    <label class="control-label g_cell span3" for="name"><?php echo JText::_("GURU_CERTIFICATE_COURSE_MSG"); ?>:</label>
                                    <div class="controls g_cell span5">
                                        <span>
                                            <textarea style="float-left" id="coursemessage" name="coursemessage" cols="50" rows="4" maxlength="7000"><?php echo stripslashes($program->certificate_course_msg); ?></textarea>
                                        </span>
                                    </div>
                                    <div class="controls g_cell span2" style="float-left">
                                        <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_CERTIFICATE_COURSE_MSG1"); ?>">
                                            <img border="0" src="components/com_guru/images/icons/tooltip.png">
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="accordionItem">
                            <h3 class="g_accordion-group ui-corner-all g_title_active"><?php echo JText::_("GURU_PRODDESC"); ?></h3>
                            <div class="clearfix tab-body  g_accordion-group g_content_active">
                                <div class="control-group clearfix">
                                    <label class="control-label g_cell span2" for="name"><?php echo JText::_("GURU_PRODDESC"); ?>:</label>
                                    <div class="controls g_cell span9">
                                        <div onmouseover="document.getElementById('editor_clicked').value = 'description';">
                                            <?php
                                            //echo $editorul->display( 'description', ''.stripslashes($program->description),'100%', '220px', '20', '50' ); 
                                            ?>
                                            <textarea id="description" name="description" class="useredactor" style="width:100%; height:100px;"><?php echo $program->description; ?></textarea>
                                        </div>
                                    </div>
                                    <div class="controls g_cell span1">
                                        <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_PRODDESC"); ?>">
                                            <img border="0" src="components/com_guru/images/icons/tooltip.png">
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="accordionItem">
                            <h3 class="g_accordion-group ui-corner-all g_title_active"><?php echo JText::_("GURU_IMAGE_COVER"); ?></h3>
                            <div class="clearfix tab-body  g_accordion-group g_content_active">
                                <div class="well">
                                    <h5><?php echo JText::_('GURU_IMAGE_COVER2'); ?></h5>
                                </div>
                                <div class="alert alert-info">
                                    <?php echo JText::_('GURU_IMAGE_COVER_USAGE'); ?>
                                </div>

                                <div class="control-group clearfix">
                                    <label class="control-label g_cell span2" for="name"><?php echo JText::_("GURU_IMAGE_COVER2"); ?>:</label>
                                    <div class="controls g_cell span5">
                                        <div style="float:left;">
                                            <div id="fileUploader"></div>
                                        </div>
                                    </div>
                                    <div class="controls g_cell span1">
                                        <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_IMAGE"); ?>">
                                            <img border="0" src="components/com_guru/images/icons/tooltip.png">
                                        </span>
                                    </div>
                                    <div class="controls g_cell span4">
                                        <div style="float:left; padding-left:10px;">
                                            <?php echo JText::_('GURU_RECOMMENDED_SIZE'); ?>
                                        </div>
                                    </div>
                                    <input type="hidden" name="image" id="image" class="image_cover" value="<?php echo $program->image; ?>" />&nbsp;
                                </div>
                                <div class="control-group clearfix">
                                    <label class="control-label g_cell span2" for="name"><?php echo JText::_("GURU_PRODCIMG_COVER"); ?>:</label>
                                    <div class="controls g_cell span10">
                                        <?php
                                        if (isset($program->image) && $program->image != "") {

                                        ?>
                                            <img id="view_imagelist23" name="view_imagelist" src='<?php echo JURI::root(); ?><?php echo $program->image; ?>' />
                                            <br />
                                            <input style="margin-top:10px;" type="button" class="btn btn-danger" value="<?php echo JText::_('GURU_REMOVE'); ?>" onclick="deleteImage('<?php echo $program->id; ?>');" id="deletebtn" />
                                            <input type="hidden" value="<?php echo $program->image; ?>" name="img_name" id="img_name" />
                                        <?php
                                        } else {
                                            echo "<img id='view_imagelist23' name='view_imagelist' src='" . JURI::base() . "components/com_guru/images/blank.png'/>";
                                        }
                                        ?>
                                    </div>
                                </div>

                                <div class="well">
                                    <h5><?php echo JText::_('GURU_IMAGE_AVATAR'); ?></h5>
                                </div>
                                <div class="alert alert-info">
                                    <?php echo JText::_('GURU_IMAGE_AVATAR_USAGE'); ?>
                                </div>
                                <div class="control-group clearfix">
                                    <label class="control-label g_cell span2" for="name"><?php echo JText::_("GURU_IMAGE_AVATAR"); ?>:</label>
                                    <div class="controls g_cell span5">
                                        <div style="float:left;">
                                            <div id="fileUploader1"></div>
                                        </div>
                                    </div>
                                    <div class="controls g_cell span1">
                                        <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_IMAGE"); ?>">
                                            <img border="0" src="components/com_guru/images/icons/tooltip.png">
                                        </span>
                                    </div>
                                    <input type="hidden" name="image_avatar" id="image_avatar" class="image_avatar" value="<?php echo $program->image_avatar; ?>" />&nbsp;
                                </div>
                                <div class="control-group clearfix">
                                    <label class="control-label g_cell span2" for="name"><?php echo JText::_("GURU_PRODCIMG_AVATAR"); ?>:</label>
                                    <div class="controls g_cell span10">
                                        <?php
                                        if (isset($program->image_avatar) && $program->image_avatar != "") {
                                        ?>
                                            <img id="view_imagelist24" name="view_imagelist1" src='<?php echo JURI::root(); ?><?php echo $program->image_avatar; ?>' />
                                            <br />
                                            <input style="margin-top:10px;" type="button" class="btn btn-danger" value="<?php echo JText::_('GURU_REMOVE'); ?>" onclick="deleteImageA('<?php echo $program->id; ?>');" id="deletebtn2" />
                                            <input type="hidden" value="<?php echo $program->image_avatar; ?>" name="image_avatar2" id="image_avatar2" />
                                        <?php
                                        } else {
                                            echo "<img id='view_imagelist24' name='view_imagelist' src='" . JURI::base() . "components/com_guru/images/blank.png'/>";
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="accordionItem">
                            <h3 class="g_accordion-group ui-corner-all g_title_active"><?php echo JText::_("GURU_EXERCISE_FILES"); ?></h3>
                            <div class="clearfix tab-body  g_accordion-group g_content_active">
                                <div style="float:left;">
                                    <a onclick="javascript:openMyModal(0, 0, '<?php echo JURI::root(); ?>index.php?option=com_guru&view=guruauthor&task=addexercise&tmpl=component&cid=<?php echo $program->id; ?>'); return false;" href="#">
                                        <input type="button" class="uk-button uk-button-primary wk-button wk-button-primary" value="<?php echo JText::_("GURU_EXERCISE_FILE"); ?>" />
                                    </a>
                                    &nbsp;
                                </div>
                                <div style="float:left;">
                                    <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_EXERCISE_FILE"); ?>">
                                        <img border="0" src="components/com_guru/images/icons/tooltip.png">
                                    </span>
                                </div>
                                <div class="clearfix">
                                    <div class="g_table_wrap">
                                        <table id="articleList" class="table table-striped">
                                            <tr class="g_table_header">
                                                <th width="1%"></th>
                                                <th width="1%"></th>
                                                <th class="g_cell_1 center">#</th>
                                                <th class="g_cell_2"><?php echo JText::_('GURU_FILE_MEDIA_NAME'); ?></th>
                                                <th class="g_cell_3"><?php echo JText::_('GURU_PUBLISHED'); ?></th>
                                                <th class="g_cell_4"><?php echo JText::_("GURU_GUEST_ACCESS"); ?></th>
                                                <th class="g_cell_5"><?php echo JText::_("GURU_REMOVE"); ?></th>
                                            </tr>
                                            <tbody id="rowsmedia">
                                                <?php
                                                $oldids = "";
                                                $display_none = '';
                                                if ($program->id != NULL) {
                                                    $existing_ids = $this->existing_ids($program->id);
                                                }

                                                $tt = array();
                                                if (is_array(@$existing_ids) && count(@$existing_ids) > 0) {
                                                    foreach ($existing_ids as $ex_idz) {
                                                        $tt[] = $ex_idz->media_id;
                                                    }
                                                }
                                                $more_media_files = new stdClass();
                                                $more_media_files = (array)$more_media_files;

                                                $i = 0;
                                                $pageNav = new JPagination(count($mmediam), 0, count($mmediam));
                                                $n = count($mmediam);

                                                foreach ($mmediam as $mmedial) {
                                                ?>
                                                    <tr class="guru_row" id="tr<?php echo intval($mmedial->media_id); ?>">
                                                        <td>
                                                            <span class="sortable-handler active" style="cursor: move;">
                                                                <i class="icon-menu"></i>
                                                            </span>
                                                            <input type="text" class="width-20 text-area-order " value="<?php echo $mmedial->order; ?>" size="5" name="order[]" style="display:none;">
                                                        </td>
                                                        <td style="visibility:hidden;">
                                                            <?php $checked = JHTML::_('grid.id', $i, $mmedial->media_id);
                                                            echo $checked; ?>
                                                        </td>
                                                        <td class="g_cell_1"><?php echo $i + 1; ?></td>
                                                        <td class="g_cell_2">
                                                            <?php
                                                            switch ($mmedial->type) {
                                                                case "video": {
                                                                        $img_path = JURI::root() . "components/com_guru/images/video.gif";
                                                                        echo '<img src="' . $img_path . '" alt="video type"/>';
                                                                    }
                                                                    break;
                                                                case "docs": {
                                                                        $img_path = JURI::root() . "components/com_guru/images/doc.gif";
                                                                        echo '<img src="' . $img_path . '" alt="video type"/>';
                                                                    }
                                                                    break;
                                                                case "url": {
                                                                        $img_path = JURI::root() . "components/com_guru/images/url.gif";
                                                                        echo '<img src="' . $img_path . '" alt="video type"/>';
                                                                    }
                                                                    break;
                                                                case "image": {
                                                                        $img_path = JURI::root() . "components/com_guru/images/image.jpg";
                                                                        echo '<img src="' . $img_path . '" alt="video type"/>';
                                                                    }
                                                                    break;
                                                                case "audio": {
                                                                        $img_path = JURI::root() . "components/com_guru/images/audio.gif";
                                                                        echo '<img src="' . $img_path . '" alt="audio type"/>';
                                                                    }
                                                                    break;
                                                                case "quiz": {
                                                                        $img_path = JURI::root() . "components/com_guru/images/quiz.gif";
                                                                        echo '<img src="' . $img_path . '" alt="quiz type"/>';
                                                                    }
                                                                    break;
                                                                case "text": {
                                                                        $img_path = JURI::root() . "components/com_guru/images/doc.gif";
                                                                        echo '<img src="' . $img_path . '" alt="doc type"/>';
                                                                    }
                                                                    break;
                                                                case "file": {
                                                                        $img_path = JURI::root() . "components/com_guru/images/file.gif";
                                                                        echo '<img src="' . $img_path . '" alt="doc type"/>';
                                                                    }
                                                                    break;
                                                            }
                                                            ?>
                                                            <a class="a_guru" href="<?php echo JUri::root() ?>index.php?option=com_guru&controller=guruAuthor&task=editMedia&cid=<?php echo $mmedial->media_id; ?>" target="_blank"><?php echo $mmedial->name; ?></a>
                                                        </td>
                                                        <td class="g_cell_3">

                                                            <?php
                                                            if ($mmedial->published == 1) {
                                                            ?>
                                                                <a id="g_publish<?php echo $mmedial->media_id; ?>" style="cursor: pointer;" class="icon-ok" title="Publish Item" onclick="javascript:publishUn(<?php echo $mmedial->media_id; ?>)"></a>
                                                            <?php
                                                            } else { ?>
                                                                <a id="g_publish<?php echo $mmedial->media_id; ?>" style="cursor: pointer;" class="icon-remove" title="Unpublish Item" onclick="javascript:publishUn(<?php echo $mmedial->media_id; ?>)"></a>
                                                            <?php
                                                            }
                                                            ?>
                                                        </td>
                                                        <td class="g_cell_4">
                                                            <select name="<?php echo "access" . $mmedial->media_id ?>">
                                                                <option value="0" <?php if ($mmedial->access == 0) echo "selected"; ?>><?php echo JText::_("GURU_COU_STUDENTS"); ?></option>
                                                                <option value="1" <?php if ($mmedial->access == 1) echo "selected"; ?>><?php echo JText::_("GURU_REG_MEMBERS"); ?></option>
                                                                <option value="2" <?php if ($mmedial->access == 2) echo "selected"; ?>><?php echo JText::_("GURU_REG_GUESTS"); ?></option>
                                                            </select>
                                                        </td>
                                                        <td class="g_cell_5"><?php
                                                                                $img_path = "components/com_guru/images/delete2.gif";
                                                                                echo '<img onclick="deleteMedia(\'' . $mmedial->media_id . '\');" src="' . $img_path . '" alt="delete"/>';
                                                                                ?>
                                                        </td>
                                                    </tr>
                                                    <?php $i++;
                                                    $oldids = $oldids . $mmedial->media_id . ',';
                                                }
                                                if (count($more_media_files) > 0)
                                                    foreach ($more_media_files as $more_media_files_val) {
                                                        $display_none = '';
                                                        if (!in_array($more_media_files_val->media_id, $tt)) { // if start
                                                    ?>
                                                        <tr id="1tr<?php echo $more_media_files_val->media_id; ?>" <?php echo $display_none; ?>>
                                                            <td width="8%"></td>
                                                            <td width="39%">
                                                                <a class="a_guru" href="<?php echo JUri::root() ?>index.php?option=com_guru&controller=guruAuthor&task=editMedia&cid=<?php echo $mmedial->media_id; ?>" target="_blank"><?php echo $more_media_files_val->name; ?></a>
                                                            </td>
                                                            <td width="9%">
                                                                <?php echo $more_media_files_val->type; ?>
                                                            </td>
                                                            <td width="18%">--</td>
                                                            <td width="12%">
                                                                <?php echo $link2_remove; ?>
                                                            </td>
                                                            <td width="14%">
                                                                <?php echo $more_media_files_val->published; ?>
                                                            </td>
                                                        </tr>
                                                <?php $oldids = $oldids . $more_media_files_val->media_id . ',';
                                                        } // if end
                                                    } // foreach end 
                                                ?>
                                            </tbody>
                                        </table>
                                        <input type="hidden" value="<?php echo $oldids; ?>" name="mediafiles" id="mediafiles">
                                        <input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
                                        <input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
                                        <?php echo JHtml::_('form.token'); ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="accordionItem">
                            <h3 class="g_accordion-group ui-corner-all g_title_active"><?php echo JText::_("GURU_PRICING_PLANS"); ?></h3>
                            <div class="clearfix tab-body  g_accordion-group g_content_active">
                                <?php if ($config->course_is_free_show == 1) { ?>
                                    <div class="uk-grid wk-grid">
                                        <div class="uk-width-1-1 uk-width-medium-1-3 wk-width-1-1 wk-width-medium-1-3">
                                            <input onclick="javascript:checkIfCertificateIsSet();" type="checkbox" id="chb_free_courses" name="chb_free_courses" <?php if (@$chb_free_courses == "1") echo 'checked="checked"';
                                                                                                                                                                    else {
                                                                                                                                                                    } ?>>
                                            <span class="lbl"><?php echo trim(JText::_('GURU_FREE_COURSES')); ?></span>
                                        </div>
                                        <div class="uk-width-1-1 uk-width-medium-1-3 wk-width-1-1 wk-width-medium-1-3">
                                            <select id="step_access_courses" name="step_access_courses" onchange="javascript:selectrealtime(this.value); checkIfCertificateIsSet();" class="input-medium">
                                                <option value="0" <?php if (@$step_access_courses == 0) echo "selected"; ?>><?php echo JText::_("GURU_COU_STUDENTS"); ?></option>
                                                <option value="1" <?php if (@$step_access_courses == 1) echo "selected"; ?>><?php echo JText::_("GURU_REG_MEMBERS"); ?></option>
                                                <option value="2" <?php if (@$step_access_courses == 2) echo "selected"; ?>><?php echo JText::_("GURU_REG_GUESTS"); ?></option>
                                            </select>
                                            <span><?php echo trim(JText::_('GURU_OF')); ?></span>
                                        </div>
                                        <div class="uk-width-1-1 uk-width-medium-1-3 wk-width-1-1 wk-width-medium-1-3">
                                            <?php $this->getCourseListForStudents(); ?>
                                        </div>
                                    </div>
                                <?php } ?>

                                <div style="float:left;">
                                    <?php echo trim(JText::_('GURU_SUBSCRIPTIONS')) . ' ' . trim(strtolower(JText::_('GURU_SUBS_PLANS'))); ?>&nbsp;
                                </div>
                                <div style="float:left;">
                                    <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_SUBSCRIPTIONS"); ?>">
                                        <img border="0" src="components/com_guru/images/icons/tooltip.png">
                                    </span>
                                </div>
                                <div class="g_table_wrap">
                                    <div class="g_table clearfix">
                                        <div class="">
                                            <?php echo $this->plans; ?>
                                        </div>
                                    </div>
                                </div>
                                <div style="float:left;">
                                    <?php echo JText::_('GURU_RENW_PLANS'); ?>&nbsp;
                                </div>
                                <div style="float:left;">
                                    <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_RENW_PLANS"); ?>">
                                        <img border="0" src="components/com_guru/images/icons/tooltip.png">
                                    </span>
                                </div>
                                <div class="g_table_wrap">
                                    <div class="g_table clearfix">
                                        <div class="">
                                            <?php echo $this->renewals; ?>
                                        </div>
                                    </div>
                                </div>
                                <!--<div style="float:left;">
                                        <?php //echo JText::_('GURU_EMAIL_PLANS'); 
                                        ?>&nbsp;
                                    </div>
                                    <div style="float:left;">
                                        <span class="editlinktip hasTip" title="<?php //echo JText::_("GURU_TIP_EMAIL_PLANS"); 
                                                                                ?>" >
                                            <img border="0" src="components/com_guru/images/icons/tooltip.png">
                                        </span>
                                   </div>
                                    <div class="g_table_wrap">
                                        <div class="g_table clearfix"> 
                                            <div class="">
                                                <?php //echo $this->emails; 
                                                ?>
                                            </div>
                                        </div>
                                    </div>-->
                            </div>
                        </div>

                        <div class="accordionItem">
                            <h3 class="g_accordion-group ui-corner-all g_title_active"><?php echo JText::_("GURU_DAY_REQ"); ?></h3>
                            <div class="clearfix tab-body  g_accordion-group g_content_active">
                                <div class="control-group clearfix">
                                    <?php
                                    $mmediam_preq = $this->mmediam_preq;
                                    $table_display = "table";

                                    if ($mmediam_preq == NULL || (is_array($mmediam_preq) && count($mmediam_preq) <= 0)) {
                                        $table_display = "none";
                                    }
                                    ?>
                                    <div id="table_courses_id" class="g_table_wrap" style="display:<?php echo $table_display; ?>;">
                                        <div class="g_table clearfix">
                                            <?php
                                            $old_preq_ids = '';
                                            if ($program->id != "" || $program->id != NULL) {
                                                $preq_existing_ids = $this->preq_existing_ids($program->id);
                                            }
                                            ?>
                                            <div id="rowspreq">
                                                <div class="g_table_row">
                                                    <div class="g_cell span1 g_table_cell g_th">
                                                        <div>
                                                            <div>
                                                                <?php echo JText::_("GURU_ID"); ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="g_cell span1 g_table_cell g_th">
                                                        <div>
                                                            <div>
                                                                <?php echo JText::_("GURU_NAME"); ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="g_cell span1 g_table_cell g_th">
                                                        <div>
                                                            <div>
                                                                <?php echo JText::_("GURU_REMOVE"); ?>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <?php
                                                    $table_rows = "";
                                                    if ($mmediam_preq != NULL) {
                                                        foreach ($mmediam_preq as $element) {
                                                            $table_rows .= "<div id='tr_" . $element->media_id . "' class='g_table_row'>";
                                                            $table_rows .=      "<div class='g_cell span1 g_table_cell'>
                                                                                                <div>
                                                                                                    <div>" . $element->media_id . "</div>
                                                                                                    </div>
                                                                                                </div>";
                                                            $table_rows .=      '<div class="g_cell span1 g_table_cell">
                                                                                                <div>
                                                                                                    <div><a class="a_guru" target="_blank" href="' . JUri::root() . 'index.php?option=com_guru&controller=guruTasks&task=edit&cid=' . $element->media_id . '">' . $element->name . "</a></div>
                                                                                                    </div>
                                                                                                </div>";
                                                            $table_rows .=      '<div class="g_cell span1 g_table_cell">
                                                                                                <div>
                                                                                                    <div>
                                                                                                        <img onclick="deleteCourse(\'' . $element->media_id . '\');" alt="delete" src="' . JURI::root() . "components/com_guru/images/delete2.gif" . '" /></div>
                                                                                                    </div>
                                                                                                </div>';
                                                            $table_rows .= "</div>";
                                                        }
                                                        echo $table_rows;
                                                    }
                                                    ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="control-group clearfix">
                                    <label class="control-label g_cell span2" for="name"><?php echo JText::_("GURU_DAY_OPREQ"); ?>:</label>
                                    <div class="controls g_cell span9">
                                        <div onmouseover="document.getElementById('editor_clicked').value = 'pre_req';">
                                            <?php //echo $editorul->display( 'pre_req', ''.stripslashes($program->pre_req),'100%', '220px', '20', '50' ); 
                                            ?>
                                            <textarea id="pre_req" name="pre_req" class="useredactor" style="width:100%; height:100px;"><?php echo $program->pre_req; ?></textarea>
                                        </div>
                                    </div>
                                    <div class="controls g_cell span1">
                                        <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_DAY_OPREQ"); ?>">
                                            <img border="0" src="components/com_guru/images/icons/tooltip.png">
                                        </span>
                                    </div>
                                </div>
                                <div class="control-group clearfix">
                                    <label class="control-label g_cell span2" for="name"><?php echo JText::_("GURU_DAY_PREQBK"); ?>:</label>
                                    <div class="controls g_cell span9">
                                        <div onmouseover="document.getElementById('editor_clicked').value = 'pre_req_books';">
                                            <?php //echo $editorul->display( 'pre_req_books', ''.stripslashes($program->pre_req_books),'100%', '220px', '20', '50' ); 
                                            ?>
                                            <textarea id="pre_req_books" name="pre_req_books" class="useredactor" style="width:100%; height:100px;"><?php echo $program->pre_req_books; ?></textarea>
                                        </div>
                                    </div>
                                    <div class="controls g_cell span1">
                                        <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_DAY_PREQBK"); ?>">
                                            <img border="0" src="components/com_guru/images/icons/tooltip.png">
                                        </span>
                                    </div>
                                </div>
                                <div class="control-group clearfix">
                                    <label class="control-label g_cell span2" for="name"><?php echo JText::_("GURU_DAY_OREQ"); ?>:</label>
                                    <div class="controls g_cell span9">
                                        <div onmouseover="document.getElementById('editor_clicked').value = 'reqmts';">
                                            <?php //echo $editorul->display( 'reqmts', ''.stripslashes($program->reqmts),'100%', '220px', '20', '50' ); 
                                            ?>
                                            <textarea id="reqmts" name="reqmts" class="useredactor" style="width:100%; height:100px;"><?php echo $program->reqmts; ?></textarea>
                                        </div>
                                    </div>
                                    <div class="controls g_cell span1">
                                        <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_DAY_OREQ"); ?>">
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
<input type="hidden" name="id" id="id" value="<?php echo $program->id; ?>" />
<input type="hidden" name="task" id="form_task" value="" />
<input type="hidden" name="option" value="com_guru" />
<input type="hidden" name="controller" value="guruAuthor" />
<input type="hidden" name="view" value="guruauthor" />
<input type="hidden" name="boxchecked" value="" />
<input type="hidden" name="task2" value="edit" />
<input type="hidden" name="media_number" value="<?php echo @$n; ?>" id="media_number" />
<input type="hidden" id="editor_clicked" name="editor_clicked" value="" />
<input type="hidden" name="g_page" value="<?php if (intval($program->id) == 0) {
                                                echo "courseadd";
                                            } else {
                                                echo "courseedit";
                                            } ?>">
</form>
<?php
    }

    $upload_script = 'jQuery( document ).ready(function(){
                            jQuery(".useredactor").redactor({
                                 buttons: [\'bold\', \'italic\', \'underline\', \'link\', \'alignment\', \'unorderedlist\', \'orderedlist\']
                            });
                            jQuery(".redactor_useredactor").css("height","400px");
                          });';
    $doc->addScriptDeclaration($upload_script);
?>
</div>
<?php
include(JPATH_SITE . '/administrator/components/com_guru/views/modals/modal_with_iframe.php');
?>
<script type="text/javascript" language="javascript" src="<?php echo JURI::root() . '/administrator/components/com_guru/js/modal_with_iframe.js'; ?>"> </script>