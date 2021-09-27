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
defined('_JEXEC') or die('Restricted access');

require_once(JPATH_SITE . DIRECTORY_SEPARATOR . "components" . DIRECTORY_SEPARATOR . "com_guru" . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'custom_fields.php');

JHTML::_('behavior.tooltip');
JHtml::_('bootstrap.framework');
JHtml::_('behavior.formvalidator');

$document = JFactory::getDocument();
$editor1  = new JEditor(JFactory::getConfig()->get("editor"));

$document->addScriptDeclaration('
    document.onreadystatechange = function(){
        initPhoneTeacherTabs();
    }
');

$document->setMetaData('viewport', 'width=device-width, initial-scale=1.0');
require_once(JPATH_BASE . "/components/com_guru/helpers/Mobile_Detect.php");
$detect = new Mobile_Detect;
$deviceType = ($detect->isMobile() ? ($detect->isTablet() ? 'tablet' : 'phone') : 'computer');

$configs = $this->getConfigs();
$terms_cond_teacher = $configs["0"]["terms_cond_teacher"];
$terms_cond_teacher_content = $configs["0"]["terms_cond_teacher_content"];

$charset = '0123456789AaBbCcDdEeFfGgHhIiJjKkLlMmNnOoPpQqRrSsTtUuVvWwXxYyZz';
$code = '';
$code_length = 5;

for ($i = 0; $i < $code_length; $i++) {
    $code = $code . substr($charset, mt_rand(0, strlen($charset) - 1), 1);
}

$user = JFactory::getUser();
$user_id = $user->id;
$params = JComponentHelper::getParams('com_users');
$allowUserRegistration = $params->get('allowUserRegistration');

if ($user_id == 0 && $allowUserRegistration == 0) {
    $app = JFactory::getApplication();

    $helper = new guruHelper();
    $itemid_seo = $helper->getSeoItemid();
    $itemid_seo = @$itemid_seo["gurulogin"];

    if (intval($itemid_seo) > 0) {
        $Itemid = intval($itemid_seo);
    }

    $app->redirect(JRoute::_("index.php?option=com_guru&view=guruLogin&Itemid=" . intval($Itemid)));
    return true;
}
?>

<script type="text/javascript" language="javascript">
    document.body.className = document.body.className.replace("modal", "");
</script>

<style>
    .alert-info {
        height: auto !important;
    }

    .accordionItem.hideTabs div {
        display: none;
    }

    div.guru-content .btn_renew {
        height: 28px;
         !important;
    }
</style>

<!-- <script type="text/javascript" src="<?php echo JURI::root(); ?>media/vendor/jquery/js/jquery.min.js"></script> -->

<script language="javascript" type="text/javascript">
    var accordionItems = new Array();

    function initPhoneTeacherTabs() {
        // Grab the accordion items from the page
        var divs = document.getElementsByTagName('div');
        for (var i = 0; i < divs.length; i++) {
            if (divs[i].className == 'accordionItem') accordionItems.push(divs[i]);
        }

        // Assign onclick events to the accordion item headings
        for (var i = 0; i < accordionItems.length; i++) {
            var h3 = getFirstChildWithTagName(accordionItems[i], 'H3');
            h3.onclick = toggleItem;
        }

        // Hide all accordion item bodies except the first
        for (var i = 1; i < accordionItems.length; i++) {
            accordionItems[i].className = 'accordionItem hideTabs';
        }
    }

    function toggleItem() {
        var itemClass = this.parentNode.className;

        // Hide all items
        for (var i = 0; i < accordionItems.length; i++) {
            accordionItems[i].className = 'accordionItem hideTabs';
        }

        // Show this item if it was previously hidden
        if (itemClass == 'accordionItem hideTabs') {
            this.parentNode.className = 'accordionItem';
        }
    }

    function getFirstChildWithTagName(element, tagName) {
        for (var i = 0; i < element.childNodes.length; i++) {
            if (element.childNodes[i].nodeName == tagName) return element.childNodes[i];
        }
    }


    function validateAuForm(field) {
        var form = document.adminForm;
        var r = new RegExp("[\<|\>|\"|\'|\%|\;|\(|\)|\&|\+|\-]", "i");
        var x = new RegExp("[\<|\>|\"]", "i");
        var filter = /^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,13}(?:\.[a-z]{2})?)$/i
        var valid_entry = true;

        // validate name --------------------------------------------------------------
        if (field == "name") {
            if (form.name.value == "") {
                document.getElementById("text_red_n").style.display = "block";
                valid_entry = false;
            } else if (form.name.value != "") {
                document.getElementById("text_red_n").style.display = "none";
            }

            if (x.exec(form.name.value)) {
                document.getElementById("text_red_ni").style.display = "block";
                valid_entry = false;
            }
        }

        // validate username --------------------------------------------------------------
        if (field == "username") {
            if (document.getElementById('guru_username').value == "") {
                document.getElementById("text_red_ul").style.display = "block";
                valid_entry = false;
            }

            if (document.getElementById('guru_username').value != "") {
                document.getElementById("text_red_ul").style.display = "none";
            }

            if (r.exec(form.username.value) || form.username.value.length <= 3) {
                document.getElementById("text_red_uc").style.display = "block";
                valid_entry = false;
            } else if (form.username.value.length > 3) {
                document.getElementById("text_red_uc").style.display = "none";
            }
        }


        // validate email --------------------------------------------------------------
        if (field == "email") {
            if (document.getElementById('email').value == "") {
                document.getElementById("text_rede").style.display = "block";
                valid_entry = false;
            }

            if (document.getElementById('email').value != "") {
                document.getElementById("text_rede").style.display = "none";
            }

            if (filter.test(form.email.value)) {
                document.getElementById("text_redv").style.display = "none";
            } else if (!filter.test(form.email.value)) {
                document.getElementById("text_redv").style.display = "block";
                valid_entry = false;
            }
        }

        // validate website --------------------------------------------------------------
        if (field == "website") {
            if (form.website.value.substring(0, 7) != "http://" && form.website.value.substring(0, 8) != "https://") {
                document.getElementById("text_red_wb").style.display = "block";
                valid_entry = false;
            } else if (form.website.value.substring(0, 7) != "") {
                document.getElementById("text_red_wb").style.display = "none";
            }
        }

        // validate blog --------------------------------------------------------------
        if (field == "blog") {
            if (form.blog.value.substring(0, 7) != "http://" && form.blog.value.substring(0, 8) != "https://") {
                document.getElementById("text_red_blog").style.display = "block";
                valid_entry = false;
            } else if (form.blog.value.substring(0, 7) != "") {
                document.getElementById("text_red_blog").style.display = "none";
            }
        }

        // validate facebook --------------------------------------------------------------
        if (field == "facebook") {
            if (form.facebook.value.substring(0, 7) != "http://" && form.facebook.value.substring(0, 8) != "https://") {
                document.getElementById("text_red_fb").style.display = "block";
                valid_entry = false;
            } else if (form.facebook.value.substring(0, 7) != "") {
                document.getElementById("text_red_fb").style.display = "none";
            }
        }

        // validate password --------------------------------------------------------------
        if (field == "password") {
            if (form.password.value == "") {
                document.getElementById("text_red_pass2").style.display = "block";
                valid_entry = false;
            } else if (form.password.value != "") {
                document.getElementById("text_red_pass2").style.display = "none";
            }
        }

        // validate password2 --------------------------------------------------------------
        if (field == "password2") {
            if (form.password2.value == "") {
                document.getElementById("text_red_pass4").style.display = "block";
                valid_entry = false;
            } else if (form.password2.value != "") {
                document.getElementById("text_red_pass4").style.display = "none";
            }

            if (form.password.value != form.password2.value) {
                document.getElementById("text_red_pass3").style.display = "block";
                valid_entry = false;
            } else if (form.password.value == form.password2.value) {
                document.getElementById("text_red_pass3").style.display = "none";
            }
        }

        valid_entry = checkAllFields();
    }

    function validateAuFormAjax(field) {
        var valid_entry = true;
        username = document.getElementById("guru_username").value;
        email = document.getElementById("email").value;
        if (username != "") {
            htmlvalue = "0";
            var req = jQuery.ajax({
                method: 'get',
                async: true,
                url: '<?php echo JURI::root() . "index.php?option=com_guru&controller=guruAuthor&tmpl=component&task=checkExistingUserU"; ?>&username=' + username + '&email=' + email + '&id=<?php echo @$id; ?>',
                data: {
                    'do': '1'
                },
                success: function(response) {
                    document.getElementById("ajax_response_u").innerHTML = response;
                    checkReturnU(response);
                }
            });
        }

        email = document.getElementById("email").value;
        if (email != "") {
            htmlvalue = "0";
            var req = jQuery.ajax({
                method: 'get',
                async: true,
                url: '<?php echo JURI::root() . "index.php?option=com_guru&controller=guruAuthor&tmpl=component&task=checkExistingUserE"; ?>&username=' + username + '&email=' + email + '&id=<?php echo @$id; ?>',
                data: {
                    'do': '1'
                },
                success: function(response) {
                    document.getElementById("ajax_response_e").innerHTML = response;
                    checkReturnE(response);
                },
            });
        }
    }

    function checkReturnE(check_return_e) {
        if (check_return_e != 0) {
            if (trimString(check_return_e) == '111') { // not validate email
                document.getElementById("text_red").style.display = "block";
                document.getElementById("text_redv").style.display = "none";
                valid_entry = false;
            } else if (trimString(check_return_e) == '222') { // validate email
                document.getElementById("text_red").style.display = "none";
            }
        } else if (trimString(check_return_e) == '') { // empty input
            document.getElementById("text_red").style.display = "none";
        }

    }

    function checkReturnU(check_return_u) {
        if (check_return_u != 0) {
            if (trimString(check_return_u) == '222') { // not validate username
                document.getElementById("text_red_u").style.display = "block";
                valid_entry = false;
            } else if (trimString(check_return_u) == '333') { // validate username
                document.getElementById("text_red_u").style.display = "none";
            }
        } else if (trimString(check_return_u) == '') { // empty input
            document.getElementById("text_red_u").style.display = "none";
        }
    }

    function validateAuFormAjaxButton() {
        var form = document.adminForm;
        var r = new RegExp("[\<|\>|\"|\'|\%|\;|\(|\)|\&|\+|\-]", "i");
        var x = new RegExp("[\<|\>|\"]", "i");
        var filter = /^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,13}(?:\.[a-z]{2})?)$/i
        var valid_entry = true;

        if (form.name.value == "") {
            alert("<?php echo JText::_("GURU_PROVIDE_USER_LOGIN_NAME"); ?>");
            valid_entry = false;
            return false;
        }

        if (document.getElementById('guru_username').value == "") {
            alert("<?php echo JText::_("GURU_INSERT_TEACHER_USERNAME"); ?>");
            valid_entry = false;
            return false;
        }

        if (r.exec(form.username.value) || form.username.value.length <= 3) {
            alert("<?php echo JText::_("GURU_INVALID_CHARACTERS_LOG_NAME"); ?>");
            valid_entry = false;
            return false;
        }

        if (document.getElementById('email').value == "") {
            alert("<?php echo JText::_("GURU_PROVIDE_EMAIL_ADDRESS"); ?>");
            valid_entry = false;
            return false;
        }

        if (!filter.test(form.email.value)) {
            alert("<?php echo JText::_("GURU_PROVIDE_VALID_EMAIL"); ?>");
            valid_entry = false;
            return false;
        }

        if (form.website.value.substring(0, 7) != "http://" && form.website.value.substring(0, 8) != "https://") {
            alert("<?php echo JText::_("GURU_AU_WEBSITE"); ?>");
            valid_entry = false;
            return false;
        }

        if (form.blog.value.substring(0, 7) != "http://" && form.blog.value.substring(0, 8) != "https://") {
            alert("<?php echo JText::_("GURU_AU_BLOG"); ?>");
            valid_entry = false;
            return false;
        }

        if (form.facebook.value.substring(0, 7) != "http://" && form.facebook.value.substring(0, 8) != "https://") {
            alert("<?php echo JText::_("GURU_AU_FB"); ?>");
            valid_entry = false;
            return false;
        }

        if (form.password.value == "") {
            alert("<?php echo JText::_("GURU_INSERT_PASS"); ?>");
            valid_entry = false;
            return false;
        }

        if (form.password.value != form.password2.value) {
            alert("<?php echo JText::_("DSCONFIRM_PASSWORD_MSG"); ?>");
            valid_entry = false;
            return false;
        }

        username = document.getElementById("guru_username").value;
        if (username != "") {
            htmlvalue = "0";
            var req = jQuery.ajax({
                method: 'get',
                url: '<?php echo JURI::root() . "index.php?option=com_guru&controller=guruAuthor&tmpl=component&task=checkExistingUserU"; ?>&username=' + username + '&email=' + email + '&id=<?php echo @$id; ?>',
                data: {
                    'do': '1'
                },
                success: function(response) {
                    jQuery("#ajax_response_u").empty().append(response);
                }
            });
        }
        check_return_u = document.getElementById("ajax_response_u").innerHTML;

        email = document.getElementById("email").value;
        if (email != "") {
            htmlvalue = "0";
            var req = jQuery.ajax({
                method: 'get',
                url: '<?php echo JURI::root() . "index.php?option=com_guru&controller=guruAuthor&tmpl=component&task=checkExistingUserE"; ?>&username=' + username + '&email=' + email + '&id=<?php echo @$id; ?>',
                data: {
                    'do': '1'
                },
                success: function(response) {
                    jQuery("#ajax_response_e").empty().append(response);
                }
            });
        }
        check_return_e = document.getElementById("ajax_response_e").innerHTML;

        if (check_return_e != 0) {
            if (trimString(check_return_e) == '111') { // not validate email
                document.getElementById("text_red").style.display = "block";
                document.getElementById("text_redv").style.display = "none";
                alert("<?php echo JText::_("GURU_EMAIL_IN_USE"); ?>");
                valid_entry = false;
                return false;
            } else if (trimString(check_return_e) == '222') { // validate email
                document.getElementById("text_red").style.display = "none";
            }
        } else if (trimString(check_return_e) == '') { // empty input
            document.getElementById("text_red").style.display = "none";
        }

        check_return_u = document.getElementById("ajax_response_u").innerHTML;
        if (check_return_u != 0) {
            if (trimString(check_return_u) == '222') { // not validate username
                document.getElementById("text_red_u").style.display = "block";
                alert("<?php echo JText::_("GURU_USERNAME_IN_USE"); ?>");
                valid_entry = false;
                return false;
            } else if (trimString(check_return_u) == '333') { // validate username
                document.getElementById("text_red_u").style.display = "none";
            }
        } else if (trimString(check_return_u) == '') { // empty input
            document.getElementById("text_red_u").style.display = "none";
        }

        if (!validateTerms()) {
            return false;
        }

        if (valid_entry) {
            valid_entry = checkAllFields();

            if (valid_entry) {
                if (!validateCustomFields()) {
                    return false;
                }

                document.adminForm.task.value = 'saveAuthor';
                document.adminForm.submit();
            }
        }
    }

    function validateCustomFields() {
        var is_ok = true;

        jQuery(".custom_fields_required").each(function() {
            required_text = "<?php echo JText::_("GURU_REQUIRED_CUSTOM_FIELD"); ?>";
            not_valid_url_text = "<?php echo JText::_("GURU_NOT_VALID_URL"); ?>";

            field_type = jQuery(this).attr("data-type");
            field_id = jQuery(this).attr("data-id");
            field_name = jQuery(this).attr("data-name");

            if (field_type == "text") {
                element_val = jQuery('[name="fields[' + field_id + ']"]').val();

                if (element_val == "") {
                    alert(field_name + " " + required_text);
                    is_ok = false;
                    return false;
                }
            } else if (field_type == "textarea") {
                element_val = jQuery('[name="fields[' + field_id + ']"]').val();

                if (element_val == "") {
                    alert(field_name + " " + required_text);
                    is_ok = false;
                    return false;
                }
            } else if (field_type == "radio") {
                element_val = jQuery('[name="fields[' + field_id + '][]"]:checked').val();

                if (typeof element_val == 'undefined') {
                    alert(field_name + " " + required_text);
                    is_ok = false;
                    return false;
                }
            } else if (field_type == "checkbox") {
                element_val = jQuery('[name="fields[' + field_id + '][]"]:checked').val();

                if (typeof element_val == 'undefined') {
                    alert(field_name + " " + required_text);
                    is_ok = false;
                    return false;
                }
            } else if (field_type == "url") {
                element_val = jQuery('[name="fields[' + field_id + '][url]"]').val();

                if (element_val == "") {
                    alert(field_name + " " + required_text);
                    is_ok = false;
                    return false;
                } else {
                    prot = jQuery('[name="fields[' + field_id + '][prot]"]').val();
                    url_validate = /^(http|https|ftp):\/\/[a-z0-9]+([\-\.]{1}[a-z0-9]+)*\.[a-z]{2,5}(:[0-9]{1,5})?(\/.*)?$/i;

                    if (!url_validate.test(prot + element_val)) {
                        alert(field_name + " " + not_valid_url_text);
                        is_ok = false;
                        return false;
                    }
                }
            }
        });

        return is_ok;
    }

    function checkAllFields() {
        var form = document.adminForm;
        var r = new RegExp("[\<|\>|\"|\'|\%|\;|\(|\)|\&|\+|\-]", "i");
        var x = new RegExp("[\<|\>|\"]", "i");
        var filter = /^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,13}(?:\.[a-z]{2})?)$/i
        var valid_entry = true;

        // validate name --------------------------------------------------------------
        if (form.name.value == "") {
            return false;
        }

        if (x.exec(form.name.value)) {
            return false;
        }

        // validate username --------------------------------------------------------------
        if (document.getElementById('guru_username').value == "") {
            return false;
        }

        if (r.exec(form.username.value) || form.username.value.length < 3) {
            return false;
        }


        // validate email --------------------------------------------------------------
        if (document.getElementById('email').value == "") {
            return false;
        }

        if (!filter.test(form.email.value)) {
            return false;
        }

        // validate website --------------------------------------------------------------
        if (form.website.value.substring(0, 7) != "http://" && form.website.value.substring(0, 8) != "https://") {
            return false;
        }


        // validate blog --------------------------------------------------------------
        if (form.blog.value.substring(0, 7) != "http://" && form.blog.value.substring(0, 8) != "https://") {
            return false;
        }


        // validate facebook --------------------------------------------------------------
        if (form.facebook.value.substring(0, 7) != "http://" && form.facebook.value.substring(0, 8) != "https://") {
            return false;
        }


        // validate password --------------------------------------------------------------
        if (form.password.value != "" && form.password2.value != "" && form.password.value != form.password2.value) {
            return false;
        }


        if (form.password.value == "") {
            return false;
        }


        // validate password2 --------------------------------------------------------------
        if (form.password.value != form.password2.value) {
            return false;
        }

        return true;
    }

    function trimString(str) {
        str = str.toString();
        var begin = 0;
        var end = str.length - 1;
        while (begin <= end && str.charCodeAt(begin) < 33) {
            ++begin;
        }
        while (end > begin && str.charCodeAt(end) < 33) {
            --end;
        }
        return str.substr(begin, end - begin + 1);
    }

    function deleteImage() {
        document.getElementById("view_imagelist23").src = "<?php echo JURI::root(); ?>components/com_guru/images/blank.png";
        document.getElementById("images").value = "";
    }

    function setModertorg() {
        document.getElementById('forummoderator1').style.display = 'none';
        document.getElementById('forummoderator2').style.display = 'table-row';
    }

    function validateTerms() {
        if (!validateCustomFields()) {
            return false;
        }

        <?php
        if ($terms_cond_teacher == 1) {
        ?>
            terms_cond_teacher = document.getElementById("terms_cond_teacher");
            if (terms_cond_teacher.checked == false) {
                alert("<?php echo JText::_("GURU_SELECT_TERMS_AND_COND"); ?>");
                return false;
            }

            terms_cond_teacher_gdpr = document.getElementById("terms_cond_teacher_gdpr");
            if (terms_cond_teacher_gdpr.checked == false) {
                alert("<?php echo JText::_("GURU_SELECT_TERMS_AND_COND_GDPR"); ?>");
                return false;
            }
        <?php
        }
        ?>

        return true;
    }

    function validateCustomFields() {
        var is_ok = true;

        jQuery(".custom_fields_required").each(function() {
            required_text = "<?php echo JText::_("GURU_REQUIRED_CUSTOM_FIELD"); ?>";
            not_valid_url_text = "<?php echo JText::_("GURU_NOT_VALID_URL"); ?>";

            field_type = jQuery(this).attr("data-type");
            field_id = jQuery(this).attr("data-id");
            field_name = jQuery(this).attr("data-name");

            if (field_type == "text") {
                element_val = jQuery('[name="fields[' + field_id + ']"]').val();

                if (element_val == "") {
                    alert(field_name + " " + required_text);
                    is_ok = false;
                    return false;
                }
            } else if (field_type == "textarea") {
                element_val = jQuery('[name="fields[' + field_id + ']"]').val();

                if (element_val == "") {
                    alert(field_name + " " + required_text);
                    is_ok = false;
                    return false;
                }
            } else if (field_type == "radio") {
                element_val = jQuery('[name="fields[' + field_id + '][]"]:checked').val();

                if (typeof element_val == 'undefined') {
                    alert(field_name + " " + required_text);
                    is_ok = false;
                    return false;
                }
            } else if (field_type == "checkbox") {
                element_val = jQuery('[name="fields[' + field_id + '][]"]:checked').val();

                if (typeof element_val == 'undefined') {
                    alert(field_name + " " + required_text);
                    is_ok = false;
                    return false;
                }
            } else if (field_type == "url") {
                element_val = jQuery('[name="fields[' + field_id + '][url]"]').val();

                if (element_val == "") {
                    alert(field_name + " " + required_text);
                    is_ok = false;
                    return false;
                } else {
                    prot = jQuery('[name="fields[' + field_id + '][prot]"]').val();
                    url_validate = /^(http|https|ftp):\/\/[a-z0-9]+([\-\.]{1}[a-z0-9]+)*\.[a-z]{2,5}(:[0-9]{1,5})?(\/.*)?$/i;

                    if (!url_validate.test(prot + element_val)) {
                        alert(field_name + " " + not_valid_url_text);
                        is_ok = false;
                        return false;
                    }
                }
            }
        });

        return is_ok;
    }

    function randomString() {
        var chars = "0123456789AaBbCcDdEeFfGgHhIiJjKkLlMmNnOoPpQqRrSsTtUuVvWwXxYyZz";
        var string_length = 5;
        var randomstring = '';
        for (var i = 0; i < string_length; i++) {
            var rnum = Math.floor(Math.random() * chars.length);
            randomstring += chars.substring(rnum, rnum + 1);
        }
        return randomstring;
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

<?php
$Itemid = JFactory::getApplication()->input->get('Itemid', 0);
$user = $this->user;
$lists = $user->lists;

$session = JFactory::getSession();
$registry = $session->get('registry');

$name_session = $registry->get('name', NULL);
$username_session = $registry->get('username', NULL);
$email_session = $registry->get('email', NULL);
$author_title_session = $registry->get('author_title', NULL);
$website_session = $registry->get('website', NULL);
$blog_session = $registry->get('blog', NULL);
$facebook_session = $registry->get('facebook', NULL);
$twitter_session = $registry->get('twitter', NULL);
$full_bio_session = $registry->get('full_bio', NULL);
$images_session = $registry->get('images', NULL);

if (isset($name_session) && $user->name == "") {
    $user->name = $name_session;
}

if (isset($username_session) && @$user->username == "") {
    $user->username = $username_session;
}

if (isset($email_session) && $user->email == "") {
    $user->email = $email_session;
}

if (isset($author_title_session) && $user->author_title == "") {
    $user->author_title = $author_title_session;
}

if (isset($website_session)) {
    $user->website = $website_session;
}

if (isset($blog_session)) {
    $user->blog = $blog_session;
}

if (isset($facebook_session)) {
    $user->facebook = $facebook_session;
}

if (isset($twitter_session) && $user->twitter == "") {
    $user->twitter = $twitter_session;
}

if (isset($full_bio_session) && $user->full_bio == "") {
    $user->full_bio = $full_bio_session;
}

if (isset($images_session) && $user->images == "") {
    $user->images = $images_session;
}

$config = $this->config;

$upload_mb = guruAdminHelper::getUploadMaxSize();
$upload_mb *= 1048576; //transform in bytes
$doc = JFactory::getDocument();

$config_author = json_decode($config->authorpage);
$author_t_prop = $config_author->author_image_size_type == "0" ? "width" : "heigth";

$doc->addScriptDeclaration('
        jQuery.noConflict();
        jQuery(function(){
            function createUploader(){
                var uploader = new qq.FileUploader({
                    element: document.getElementById(\'fileUploader\'),
                    action: \'' . JURI::root() . 'index.php?option=com_guru&controller=guruAuthor&tmpl=component&format=raw&task=upload_ajax_image\',
                    params:{
                        folder:\'authors\',
                        mediaType:\'image\',
                        size: ' . $config_author->author_image_size . ',
                        type: \'' . $author_t_prop . '\'
                    },
                    onSubmit: function(id,fileName){
                        jQuery(\'.qq-upload-list li\').css(\'display\',\'none\');
                    },
                    onComplete: function(id,fileName,responseJSON){
                        //alert(\'id: \'+ id + \'; filename:\' + fileName);
                        if(responseJSON.success == true){						
                            jQuery(\'.qq-upload-success\').append(\'- <span style="color:#387C44;">Upload successful</span>\');
                            if(responseJSON.locate) {
                                jQuery(\'#view_imagelist23\').attr("src", \'' . JURI::root() . '\'+responseJSON.locate +"/"+ fileName+"?timestamp=" + new Date().getTime());
                                jQuery(\'#images\').val("/"+responseJSON.locate +"/"+ fileName);
                            }
                        }
                    },
                    allowedExtensions: [\'jpg\', \'jpeg\', \'png\', \'gif\', \'JPG\', \'JPEG\', \'PNG\', \'GIF\'],
                    sizeLimit: ' . $upload_mb . ',
                    multiple: false,
                    maxConnections: 1
                });           
            }
            createUploader();
        });
    ');
//$doc->addScript('components/com_guru/js/fileuploader.js');
JHtml::stylesheet('components/com_guru/css/fileuploader.css');
$doc->setTitle(trim(JText::_('GURU_AUTHOR')) . " " . trim(JText::_('GURU_AUTHOR_PROFILE')));

$db = JFactory::getDBO();

$div_menu = $this->authorGuruMenuBar();
?>

<div class="gru-register">
    <?php
    if ($user->id > 0) {
        echo $div_menu; //MENU TOP OF AUTHORS
    }
    ?>

    <div id="no-captcha-alert" class="alert alert-error" style="display: none;">
        <a class="close" data-dismiss="alert">×</a>
        <h4 class="alert-heading">Error</h4>
        <div>
            <div class="alert-message"><?php echo JText::_("GURU_NO_CAPTCHA_AVAILABLE"); ?></div>
        </div>
    </div>

    <form class="uk-form uk-form-horizontal wk-form wk-form-horizontal form-validate" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data" onsubmit="return validateTerms();">
        <h2 class="gru-page-title"><?php echo JText::_('GURU_PROFILE_SETTINGS'); ?></h2>
        <?php
        if ($deviceType != "phone") {
        ?>
            <ul class="uk-tab wk-tab" data-uk-tab="{connect:'#tab-content'}">
                <li id="general-tab" onclick="javascript:changeTab('general-tab'); return false;" class="header-tab uk-active"><a href="#"><?php echo JText::_("GURU_GENERAL"); ?></a></li>
                <li id="photo-tab" onclick="javascript:changeTab('photo-tab'); return false;" class="header-tab"><a href="#"><?php echo JText::_("GURU_PHOTO_TAB"); ?></a></li>
                <li id="bio-tab" onclick="javascript:changeTab('bio-tab'); return false;" class="header-tab"><a href="#"><?php echo JText::_("GURU_BIO"); ?></a></li>
            </ul>

            <ul class="uk-switcher uk-margin wk-switcher wk-margin" id="tab-content">
                <li id="general-tab-content" class="uk-active uk-margin-top wk-margin-top content-tab">
                    <div class="uk-form-row wk-form-row">
                        <label class="uk-form-label wk-form-label" for="name">
                            <?php echo JText::_("GURU_AU_AUTHOR_NAME"); ?>:
                            <span class="uk-text-danger wk-text-danger">*</span>
                        </label>
                        <div class="uk-form-controls wk-form-controls">
                            <input onkeyup="validateAuForm('name');" type="text" id="name" name="name" value="<?php echo $user->name; ?>" />
                            <div class="pull-left alert alert-warning" style="display:none;" id="text_red_n"><?php echo JText::_("GURU_PROVIDE_NAME"); ?></div>
                            <div class="pull-left alert alert-warning" style="display:none;" id="text_red_ni"><?php echo JText::_("GURU_INVALID_CHARACTERS_LOG_NAME"); ?></div>
                        </div>
                    </div>

                    <div class="uk-form-row wk-form-row">
                        <label class="uk-form-label wk-form-label" for="name">
                            <?php echo JText::_("GURU_AU_AUTHOR_USERNAME"); ?>:
                            <span class="uk-text-danger wk-text-danger">*</span>
                        </label>
                        <div class="uk-form-controls wk-form-controls">
                            <input onchange="validateAuFormAjax('username');" type="text" id="guru_username" name="username" <?php if (isset($user->username) && (trim($user->username != "") && intval($user->id) != 0)) {
                                                                                                                                    echo 'disabled="disabled"';
                                                                                                                                } ?> value="<?php if (isset($user->username)) {
                                                                                                                                                                                                                                                                    echo $user->username;
                                                                                                                                                                                                                                                                } ?>" />
                            <div class="alert alert-warning" style="display:none;" id="text_red_u"><?php echo JText::_("GURU_USERNAME_IN_USE"); ?></div>
                            <div class="alert alert-warning" style="display:none;" id="text_red_ul"><?php echo JText::_("GURU_PROVIDE_USER_LOGIN_NAME"); ?></div>
                            <div class="alert alert-warning" style="display:none;" id="text_red_uc"><?php echo JText::_("GURU_INVALID_CHARACTERS_LOG_NAME"); ?></div>
                        </div>
                    </div>

                    <div class="uk-form-row wk-form-row">
                        <label class="uk-form-label wk-form-label" for="name">
                            <?php echo JText::_("GURU_AU_AUTHOR_TITLE"); ?>:
                        </label>
                        <div class="uk-form-controls wk-form-controls">
                            <input onkeyup="validateAuForm('author_title');" type="text" id="author_title" name="author_title" value="<?php echo $user->author_title; ?>" />
                        </div>
                    </div>

                    <div class="uk-form-row wk-form-row">
                        <label class="uk-form-label wk-form-label" for="name">
                            <?php echo JText::_("GURU_AU_AUTHOR_EMAIL"); ?>:
                            <span class="uk-text-danger wk-text-danger">*</span>
                        </label>
                        <div class="uk-form-controls wk-form-controls">
                            <input onchange="validateAuFormAjax('email');" type="text" id="email" name="email" <?php if (trim($user->email != "") && intval($user->id) != 0) {
                                                                                                                    echo 'disabled="disabled"';
                                                                                                                } ?> value="<?php echo $user->email; ?>" />
                            <div class="alert alert-warning" style="display:none;" id="text_red"><?php echo JText::_("GURU_EMAIL_IN_USE"); ?></div>
                            <div class="alert alert-warning" style="display:none;" id="text_rede"><?php echo JText::_("GURU_PROVIDE_EMAIL_ADDRESS"); ?></div>
                            <div class="alert alert-warning" style="display:none;" id="text_redv"><?php echo JText::_("GURU_PROVIDE_VALID_EMAIL"); ?></div>
                        </div>
                    </div>

                    <div class="uk-form-row wk-form-row">
                        <label class="uk-form-label wk-form-label" for="name">
                            <?php echo JText::_("GURU_AU_AUTHOR_WEBSITE"); ?>:
                        </label>
                        <div class="uk-form-controls wk-form-controls">
                            <input onkeyup="validateAuForm('website');" type="text" class="pull-left" name="website" id="website" value="<?php echo $user->website; ?>" />
                            <?php echo $lists['show_website']; ?>
                            <div class="alert alert-warning" style="display:none;" id="text_red_wb"><?php echo JText::_("GURU_AU_WEBSITE"); ?></div>
                        </div>
                    </div>

                    <div class="uk-form-row wk-form-row">
                        <label class="uk-form-label wk-form-label" for="name">
                            <?php echo JText::_("GURU_AU_AUTHOR_BLOG"); ?>:
                        </label>
                        <div class="uk-form-controls wk-form-controls">
                            <input onkeyup="validateAuForm('blog');" type="text" class="pull-left" name="blog" id="blog" value="<?php echo $user->blog; ?>" />
                            <?php echo $lists['show_blog']; ?>
                            <div class="alert alert-warning" style="display:none;" id="text_red_blog"><?php echo JText::_("GURU_AU_BLOG"); ?></div>
                        </div>
                    </div>

                    <div class="uk-form-row wk-form-row">
                        <label class="uk-form-label wk-form-label" for="name">
                            <?php echo JText::_("GURU_AU_AUTHOR_FACEBOOK"); ?>:
                        </label>
                        <div class="uk-form-controls wk-form-controls">
                            <input onkeyup="validateAuForm('facebook');" type="text" class="pull-left" name="facebook" id="facebook" value="<?php echo $user->facebook; ?>" />
                            <?php echo $lists['show_facebook']; ?>
                            <div class="alert alert-warning" style="display:none;" id="text_red_fb"><?php echo JText::_("GURU_AU_FB"); ?></div>
                        </div>
                    </div>

                    <div class="uk-form-row wk-form-row">
                        <label class="uk-form-label wk-form-label" for="name">
                            <?php echo JText::_("GURU_AU_AUTHOR_TWITTER"); ?>:
                        </label>
                        <div class="uk-form-controls wk-form-controls">
                            <input onkeyup="validateAuForm('twitter');" type="text" class="pull-left" name="twitter" id="twitter" value="<?php echo $user->twitter; ?>" />
                            <?php echo $lists['show_twitter']; ?>
                        </div>
                    </div>

                    <?php
                    if (intval($user->id) == 0) {
                    ?>
                        <div class="uk-form-row wk-form-row">
                            <label class="uk-form-label wk-form-label" for="name">
                                <?php echo JText::_("GURU_AU_AUTHOR_PASSWORD"); ?>:
                                <span class="uk-text-danger wk-text-danger">*</span>
                            </label>
                            <div class="uk-form-controls wk-form-controls input-group">
                                <span class="input-group-addon"><i class="fa fa-eye"></i></span>
                                <input id="password" onchange="validateAuForm('password');" type="password" name="password" size="40" value="" autocomplete="off" class="inputbox" size="40" />
                                <div class="alert alert-warning" style="display:none;" id="text_red_pass1"><?php echo JText::_("DSCONFIRM_PASSWORD_MSG"); ?></div>
                                <div class="alert alert-warning" style="display:none;" id="text_red_pass2"><?php echo JText::_("GURU_INSERT_PASS"); ?></div>
                            </div>
                        </div>

                        <div class="uk-form-row wk-form-row">
                            <label class="uk-form-label wk-form-label" for="name">
                                <?php echo JText::_("GURU_AU_AUTHOR_VERIFY_PASSWORD"); ?>:
                                <span class="uk-text-danger wk-text-danger">*</span>
                            </label>
                            <div class="uk-form-controls wk-form-controls input-group">
                                <span class="input-group-addon"><i class="fa fa-eye"></i></span>
                                <input id="password2" onchange="validateAuForm('password2');" type="password" name="password2" size="40" value="" autocomplete="off" class="inputbox" size="40" />
                                <div class="alert alert-warning" style="display:none;" id="text_red_pass3"><?php echo JText::_("DSCONFIRM_PASSWORD_MSG"); ?></div>
                                <div class="alert alert-warning" style="display:none;" id="text_red_pass4"><?php echo JText::_("GURU_INSERT_PASS"); ?></div>
                            </div>
                        </div>


                        <?php
                        if ($terms_cond_teacher == 1) {
                            $config = JFactory::getConfig();
                        ?>
                            <div class="uk-form-row wk-form-row">
                                <label class="uk-form-label wk-form-label" for="name">
                                    &nbsp;
                                </label>
                                <div class="uk-form-controls wk-form-controls">
                                    <input type="checkbox" value="1" name="terms_cond_teacher_gdpr" id="terms_cond_teacher_gdpr" />
                                    <span class="lbl"></span>
                                    <div class="gdpr-cond-message"> <?php echo JText::_("GURU_TERMS_AND_COND_GDPR_1") . " '" . $config->get('sitename') . "' " . JText::_("GURU_TERMS_AND_COND_GDPR_2"); ?> </div>
                                </div>
                            </div>

                            <div class="uk-form-row wk-form-row">
                                <label class="uk-form-label wk-form-label" for="name">
                                </label>
                                <div class="uk-form-controls wk-form-controls">
                                    <input type="checkbox" value="1" name="terms_cond_teacher" id="terms_cond_teacher" />
                                    <span class="lbl"></span>
                                    <a href="#" onclick='window.open("<?php echo JURI::root() . "index.php?option=com_guru&controller=guruAuthor&task=terms&tmpl=component"; ?>", "", "width=500, height=400"); return false;'><?php echo JText::_("GURU_TERMS_AND_COND"); ?></a>
                                </div>
                            </div>
                        <?php
                        }
                        ?>
                    <?php
                    }
                    ?>

                    <?php
                    $custom_fields_groups = $this->getCustomFieldsGroups();

                    if (isset($custom_fields_groups) && count($custom_fields_groups) > 0) {
                        $custom_class = new guruCustomFields();

                        foreach ($custom_fields_groups as $key => $custom_group) {
                            $custom_fields = $this->getCustomFields(intval($custom_group["id"]));

                            if (isset($custom_fields) && count($custom_fields) > 0) {
                    ?>
                                <legend><?php echo trim($custom_group["name"]); ?></legend>
                    <?php
                                foreach ($custom_fields as $key2 => $custom_field) {
                                    echo $custom_class->renderCustomField($custom_field);
                                }
                            }
                        }
                    }
                    ?>

                    <?php
                    if ($user->id > 0) {
                    ?>
                        <div class="uk-form-row wk-form-row">
                            <label class="uk-form-label wk-form-label" for="name">
                            </label>
                            <div class="uk-form-controls wk-form-controls">
                                <input onclick="document.adminForm.task.value ='saveAuthor';" type="submit" class="uk-button uk-button-success wk-button wk-button-success" value="<?php echo JText::_("GURU_SAVE_PROFILE") ?>" />
                            </div>
                        </div>
                    <?php
                    }
                    ?>

                    <?php
                    if (intval($user->id) == 0) {
                        $show_captcha = $configs["0"]["captcha"];

                        if ($show_captcha == "1") {
                            $usersParams = JComponentHelper::getParams('com_users');
                            $user_captcha = $usersParams->get("captcha");

                            if (!isset($user_captcha) || $user_captcha == "0") {
                                $globalConfigs = JFactory::getConfig();
                                $user_captcha = $globalConfigs->get("captcha");

                                if (!isset($user_captcha) || $user_captcha == "0") {
                                    $user_captcha = "recaptcha";
                                }
                            }

                            $plugin = JPluginHelper::getPlugin('captcha', $user_captcha);

                            if (isset($plugin->params)) {
                                $params = new JRegistry($plugin->params);
                                $public_key = $params->get("public_key", "");
                                $private_key = $params->get("private_key", "");

                                if (trim($public_key) != "" && trim($private_key) != "") {
                                    JPluginHelper::importPlugin('captcha', $user_captcha);
                                    JFactory::getApplication()->triggerEvent('onInit');
                    ?>
                                    <div class="uk-form-row wk-form-row">
                                        <label class="uk-form-label wk-form-label" for="name">
                                        </label>
                                        <div class="uk-form-controls wk-form-controls">
                                            <?php
                                            echo JFactory::getApplication()->triggerEvent('onDisplay', array('', 'jform_captcha', 'required ' . $params->get("badge", "")))["0"];
                                            ?>
                                        </div>
                                    </div>
                                <?php
                                }
                            } else {
                                ?>
                                <script>
                                    document.getElementById("no-captcha-alert").style.display = "block";
                                </script>
                    <?php
                            }
                        }
                    }
                    ?>

                </li>

                <li id="photo-tab-content" class="uk-margin-top wk-margin-top content-tab">
                    <div class="uk-form-row wk-form-row">
                        <label class="uk-form-label wk-form-label" for="name">
                            <?php echo JText::_("GURU_UPLOAD_IMAGE"); ?>:
                        </label>
                        <div class="uk-form-controls wk-form-controls">
                            <div id="fileUploader"></div>
                            <input type="hidden" name="images" id="images" value="<?php echo $user->images; ?>" />
                        </div>
                    </div>

                    <?php
                    if (isset($user->images) && $user->images != "") {
                    ?>
                        <div class="uk-form-row wk-form-row">
                            <label class="uk-form-label wk-form-label" for="name">
                                <?php echo JText::_("GURU_SEL_IMAGE"); ?>:
                            </label>
                            <div class="uk-form-controls wk-form-controls">
                                <div id='authorImageSelected'>
                                    <img id="view_imagelist23" name="view_imagelist" src='<?php echo JURI::root() . $user->images; ?>' /><br />
                                </div>
                                <br />
                                <input type="button" class="uk-button uk-button-danger wk-button wk-button-danger" value="<?php echo JText::_('GURU_REMOVE'); ?>" onclick="return deleteImage();" />
                                <input type="hidden" value="<?php echo $user->images; ?>" name="img_name" id="img_name" />
                            </div>
                        </div>
                    <?php
                    } else {
                        echo '<div class="uk-form-row wk-form-row">
                                    <label class="uk-form-label wk-form-label" for="name">
                                    </label>
                                    <div class="uk-form-controls wk-form-controls">
                                        <div id="authorImageSelected">
                                            <img id="view_imagelist23" name="view_imagelist" src="' . JURI::root() . "components/com_guru/images/blank.png" . '"/>
                                        </div>
                                    </div>
                                </div>';
                    }
                    ?>

                    <div class="uk-form-row wk-form-row">
                        <label class="uk-form-label wk-form-label" for="name">
                        </label>
                        <div class="uk-form-controls wk-form-controls">
                            <?php
                            if ($user->id > 0) {
                            ?>
                                <input onclick="document.adminForm.task.value ='saveAuthor'; document.adminForm.submit();" type="button" class="uk-button uk-button-success wk-button wk-button-success" value="<?php echo JText::_("GURU_SAVE_PROFILE") ?>" />
                            <?php
                            }
                            ?>
                        </div>
                    </div>
                </li>

                <li id="bio-tab-content" class="uk-margin-top wk-margin-top content-tab">
                    <div class="uk-form-row wk-form-row">
                        <div class="">
                            <?php
                            $st_authorpage = json_decode($configs["0"]["st_authorpage"], true);
                            $redactor_editor = true;

                            if (isset($st_authorpage["teacher_editor"]) && intval($st_authorpage["teacher_editor"]) == 0) {
                                $redactor_editor = true;
                            } elseif (isset($st_authorpage["teacher_editor"]) && intval($st_authorpage["teacher_editor"]) == 1) {
                                $redactor_editor = false;
                            }

                            if ($redactor_editor) {
                                $doc = JFactory::getDocument();
                                $doc->addStyleSheet(JURI::root() . 'components/com_guru/css/redactor.css');
                            ?>

                                <textarea id="full_bio" name="full_bio" class="useredactor" style="width:100%; height:400px;"><?php echo $user->full_bio; ?></textarea>
                            <?php
                            } else {
                                $editor = new JEditor(JFactory::getConfig()->get("editor"));
                                echo $editor->display('full_bio', $user->full_bio, '100%', '400px', '60', '20');
                            }

                            if ($user->id > 0) {
                            ?>
                                <br />
                                <input onclick="document.adminForm.task.value ='saveAuthor'; document.adminForm.submit();" type="button" class="uk-button uk-button-success wk-button wk-button-success" value="<?php echo JText::_("GURU_SAVE_PROFILE") ?>" />
                            <?php
                            }
                            ?>

                            <script type="text/javascript" language="javascript">
                                jQuery(document).ready(function() {
                                    jQuery(".useredactor").redactor({
                                        buttons: ['bold', 'italic', 'underline', 'link', 'alignment', 'unorderedlist', 'orderedlist']
                                    });
                                    jQuery(".redactor_useredactor").css("height", "400px");
                                });
                            </script>
                        </div>
                    </div>
                </li>
            </ul>
        <?php
        } else {
        ?>
            <!-- start mobile version -->
            <div class="uk-accordion wk-accordion" data-uk-accordion>
                <h3 class="uk-accordion-title wk-accordion-title"><?php echo JText::_("GURU_GENERAL"); ?></h3>
                <div class="uk-accordion-content wk-accordion-content">
                    <div class="uk-form-row wk-form-row">
                        <label class="uk-form-label wk-form-label" for="name">
                            <?php echo JText::_("GURU_AU_AUTHOR_NAME"); ?>:
                            <span class="uk-text-danger wk-text-danger">*</span>
                        </label>
                        <div class="uk-form-controls wk-form-controls">
                            <input type="text" class="inputbox" size="30" id="name" name="name" value="<?php echo $user->name; ?>" />
                        </div>
                    </div>

                    <div class="uk-form-row wk-form-row">
                        <label class="uk-form-label wk-form-label" for="name">
                            <?php echo JText::_("GURU_AU_AUTHOR_USERNAME"); ?>:
                            <span class="uk-text-danger wk-text-danger">*</span>
                        </label>
                        <div class="uk-form-controls wk-form-controls">
                            <input type="text" class="inputbox" size="30" id="guru_username" name="username" <?php if (isset($user->username) && (trim($user->username != ""))) {
                                                                                                                    echo 'disabled="disabled"';
                                                                                                                } ?> value="<?php if (isset($user->username)) {
                                                                                                                                                                                                                            echo $user->username;
                                                                                                                                                                                                                        } ?>" />
                            <div class="alert alert-warning" style="display:none;" id="text_red_u"><?php echo JText::_("GURU_USERNAME_IN_USE"); ?></div>
                            <div class="alert alert-warning" style="display:none;" id="text_red_ul"><?php echo JText::_("GURU_PROVIDE_USER_LOGIN_NAME"); ?></div>
                            <div class="alert alert-warning" style="display:none;" id="text_red_uc"><?php echo JText::_("GURU_INVALID_CHARACTERS_LOG_NAME"); ?></div>
                        </div>
                    </div>

                    <div class="uk-form-row wk-form-row">
                        <label class="uk-form-label wk-form-label" for="name">
                            <?php echo JText::_("GURU_AU_AUTHOR_TITLE"); ?>:
                        </label>
                        <div class="uk-form-controls wk-form-controls">
                            <input type="text" class="inputbox" size="30" id="author_title" name="author_title" value="<?php echo $user->author_title; ?>" />
                        </div>
                    </div>

                    <div class="uk-form-row wk-form-row">
                        <label class="uk-form-label wk-form-label" for="name">
                            <?php echo JText::_("GURU_AU_AUTHOR_EMAIL"); ?>:
                            <span class="uk-text-danger wk-text-danger">*</span>
                        </label>
                        <div class="uk-form-controls wk-form-controls">
                            <input type="text" class="inputbox" size="30" id="email" name="email" <?php if (trim($user->email != "")) {
                                                                                                        echo 'disabled="disabled"';
                                                                                                    } ?> value="<?php echo $user->email; ?>" />
                            <div class="alert alert-warning" style="display:none;" id="text_red"><?php echo JText::_("GURU_EMAIL_IN_USE"); ?></div>
                            <div class="alert alert-warning" style="display:none;" id="text_rede"><?php echo JText::_("GURU_PROVIDE_EMAIL_ADDRESS"); ?></div>
                            <div class="alert alert-warning" style="display:none;" id="text_redv"><?php echo JText::_("GURU_PROVIDE_VALID_EMAIL"); ?></div>
                        </div>
                    </div>

                    <div class="uk-form-row wk-form-row">
                        <label class="uk-form-label wk-form-label" for="name">
                            <?php echo JText::_("GURU_AU_AUTHOR_WEBSITE"); ?>:
                        </label>
                        <div class="uk-form-controls wk-form-controls">
                            <input type="text" class="inputbox" size="30" name="website" id="website" value="<?php echo $user->website; ?>" />
                        </div>
                    </div>

                    <div class="uk-form-row wk-form-row">
                        <label class="uk-form-label wk-form-label" for="name">
                            <?php echo JText::_("GURU_AU_AUTHOR_BLOG"); ?>:
                        </label>
                        <div class="uk-form-controls wk-form-controls">
                            <input type="text" class="inputbox" size="30" name="blog" id="blog" value="<?php echo $user->blog; ?>" />
                            <?php echo $lists['show_blog']; ?>
                        </div>
                    </div>

                    <div class="uk-form-row wk-form-row">
                        <label class="uk-form-label wk-form-label" for="name">
                            <?php echo JText::_("GURU_AU_AUTHOR_FACEBOOK"); ?>:
                        </label>
                        <div class="uk-form-controls wk-form-controls">
                            <input type="text" class="inputbox" size="30" name="facebook" id="facebook" value="<?php echo $user->facebook; ?>" />
                            <?php echo $lists['show_facebook']; ?>
                        </div>
                    </div>

                    <div class="uk-form-row wk-form-row">
                        <label class="uk-form-label wk-form-label" for="name">
                            <?php echo JText::_("GURU_AU_AUTHOR_TWITTER"); ?>:
                        </label>
                        <div class="uk-form-controls wk-form-controls">
                            <input type="text" class="inputbox" size="30" name="twitter" id="twitter" value="<?php echo $user->twitter; ?>" />
                            <?php echo $lists['show_twitter']; ?>
                        </div>
                    </div>

                    <?php
                    if (intval($user->id) == 0) {
                    ?>
                        <div class="uk-form-row wk-form-row">
                            <label class="uk-form-label wk-form-label" for="name">
                                <?php echo JText::_("GURU_AU_AUTHOR_PASSWORD"); ?>:
                                <span class="uk-text-danger wk-text-danger">*</span>
                            </label>
                            <div class="uk-form-controls wk-form-controls">
                                <input id="password" type="password" name="password" size="40" value="" autocomplete="off" class="inputbox" size="40" />
                            </div>
                        </div>

                        <div class="uk-form-row wk-form-row">
                            <label class="uk-form-label wk-form-label" for="name">
                                <?php echo JText::_("GURU_AU_AUTHOR_VERIFY_PASSWORD"); ?>:
                                <span class="uk-text-danger wk-text-danger">*</span>
                            </label>
                            <div class="uk-form-controls wk-form-controls">
                                <input id="password2" type="password" name="password2" size="40" value="" autocomplete="off" class="inputbox" size="40" />
                            </div>
                        </div>
                    <?php
                    }
                    ?>

                    <?php
                    if ($terms_cond_teacher == 1) {
                        $config = JFactory::getConfig();
                    ?>
                        <div class="uk-form-row wk-form-row">
                            <label class="uk-form-label wk-form-label" for="name">
                                &nbsp;
                            </label>
                            <div class="uk-form-controls wk-form-controls">
                                <input type="checkbox" value="1" name="terms_cond_teacher_gdpr" id="terms_cond_teacher_gdpr" />
                                <span class="lbl"></span>
                                <div class="gdpr-cond-message"> <?php echo JText::_("GURU_TERMS_AND_COND_GDPR_1") . " '" . $config->get('sitename') . "' " . JText::_("GURU_TERMS_AND_COND_GDPR_2"); ?> </div>
                            </div>
                        </div>

                        <div class="uk-form-row wk-form-row">
                            <label class="uk-form-label wk-form-label" for="name">
                            </label>
                            <div class="uk-form-controls wk-form-controls">
                                <input type="checkbox" value="1" name="terms_cond_teacher" id="terms_cond_teacher" />
                                <span class="lbl"></span>
                                <a href="#" onclick='window.open("<?php echo JURI::root() . "index.php?option=com_guru&controller=guruAuthor&task=terms&tmpl=component"; ?>", "", "width=500, height=400"); return false;'><?php echo JText::_("GURU_TERMS_AND_COND"); ?></a>
                            </div>
                        </div>
                    <?php
                    }
                    ?>
                </div>

                <h3 class="uk-accordion-title wk-accordion-title"><?php echo JText::_("GURU_AUTHOR_PHOTO"); ?></h3>
                <div class="uk-accordion-content wk-accordion-content">
                    <div class="uk-form-row wk-form-row">
                        <label class="uk-form-label wk-form-label" for="name">
                            <?php echo JText::_("GURU_UPLOAD_IMAGE"); ?>:
                        </label>
                        <div class="uk-form-controls wk-form-controls">
                            <div id="fileUploader"></div>
                            <input type="hidden" name="images" id="images" value="<?php echo $user->images; ?>" />
                        </div>
                    </div>

                    <?php
                    if (isset($user->images) && $user->images != "") {
                    ?>
                        <div class="uk-form-row wk-form-row">
                            <label class="uk-form-label wk-form-label" for="name">
                                <?php echo JText::_("GURU_SEL_IMAGE"); ?>:
                            </label>
                            <div class="uk-form-controls wk-form-controls">
                                <div id='authorImageSelected'>
                                    <img id="view_imagelist23" name="view_imagelist" src='<?php echo JURI::root() . $user->images; ?>' /><br />
                                </div>
                                <br />
                                <input type="button" class="uk-button uk-button-warning wk-button wk-button-warning" value="<?php echo JText::_('GURU_REMOVE'); ?>" onclick="return deleteImage();" />
                                <input type="hidden" value="<?php echo $user->images; ?>" name="img_name" id="img_name" />
                            </div>
                        </div>
                    <?php
                    } else {
                        echo "<div id='authorImageSelected'><img id='view_imagelist23' name='view_imagelist' src='" . JURI::root() . "components/com_guru/images/blank.png'/></div>";
                    }
                    ?>
                </div>

                <h3 class="uk-accordion-title wk-accordion-title"><?php echo JText::_("GURU_BIO"); ?></h3>
                <div class="uk-accordion-content wk-accordion-content">
                    <div class="uk-form-row wk-form-row">
                        <label class="uk-form-label wk-form-label" for="name">
                        </label>
                        <div class="uk-form-controls wk-form-controls">
                            <textarea id="full_bio" name="full_bio" class="useredactor" style="width:100%;"><?php echo $user->full_bio; ?></textarea>
                        </div>
                    </div>

                </div>
            </div>
        <?php
        }
        ?>

        <div id="ajax_response_u" style="display:none;"></div>
        <div id="ajax_response_e" style="display:none;"></div>

        <input type="hidden" name="Itemid" value="<?php echo $Itemid; ?>" />
        <input type="hidden" name="option" value="com_guru" />
        <input type="hidden" name="auid" value="<?php echo $user->userid; ?>" />
        <input type="hidden" name="id" value="" />
        <input type="hidden" name="task" value="saveAuthor" />
        <input type="hidden" name="returnpage" value="<?php echo (JFactory::getApplication()->input->get("returnpage", "")); ?>" />
        <input type="hidden" name="controller" value="guruLogin" />
        <input type="hidden" name="guru_teacher" value="2">

        <div class="uk-form-row uk-margin-large-top wk-form-row wk-margin-large-top">
            <label class="uk-form-label wk-form-label" for=""></label>
            <div class="uk-form-controls wk-form-controls">
                <?php
                if ($user->id <= 0) {
                ?>
                    <input type="button" onclick="history.go(-1);" class="uk-button uk-button-danger wk-button wk-button-danger" value="<?php echo JText::_("GURU_CANCEL") ?>" />
                <?php
                }
                ?>

                <?php
                if ($user->id <= 0) {
                ?>
                    <input id="guru_create_account" type="button" class="uk-button uk-button-primary wk-button wk-button-primary" onclick="validateAuFormAjaxButton('name');" class="uk-button uk-button-primary wk-button wk-button-primary" value="<?php echo JText::_("GURU_CREATE_ACCOUNT") ?>" />

                <?php
                }
                ?>

            </div>
        </div>
    </form>
</div>