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

defined('_JEXEC') or die('Restricted access');

require_once(JPATH_SITE . DIRECTORY_SEPARATOR . "components" . DIRECTORY_SEPARATOR . "com_guru" . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'custom_fields.php');

$user = JFactory::getUser();

$user_id = "";
$user_username = "";
$user_email = "";
$firstname = "";
$lastname = "";
$company = "";
$returnpage = JFactory::getApplication()->input->get("returnpage", "");
$Itemid = JFactory::getApplication()->input->get("Itemid", "0", "raw");
$image = "";
$is_student = false;

$document = JFactory::getDocument();
$document->setTitle(trim(JText::_('GURU_MY_ACCOUNT')));

if (isset($user)) {
    $user_id = $user->id;
    $user_username = $user->username;
    $user_email = $user->email;

    $customer_profile = $this->getCustomerProfile();

    if (isset($customer_profile) && count($customer_profile) > 0) {
        $firstname = $customer_profile["0"]["firstname"];
        $lastname = $customer_profile["0"]["lastname"];
        $company = $customer_profile["0"]["company"];
        $image = $customer_profile["0"]["image"];
        $is_student = true;
    } else {
        $is_student = false;
        $name = $user->name;
        $temp = explode(" ", $name);
        if (count($temp) == 1) {
            $firstname = $name;
        } else {
            $firstname = $temp["0"];
            unset($temp["0"]);
            $lastname = implode(" ", $temp);
        }
    }
}

include_once(JPATH_SITE . DIRECTORY_SEPARATOR . "components" . DIRECTORY_SEPARATOR . "com_guru" . DIRECTORY_SEPARATOR . "helpers" . DIRECTORY_SEPARATOR . "helper.php");
$helper = new guruHelper();
$div_menu = $helper->createStudentMenu();
$page_title_cart = $helper->createPageTitleAndCart();

?>

<script language="javascript" type="text/javascript">
    function validateForm() {
        var first_name = document.adminForm.firstname.value;
        var last_name = document.adminForm.lastname.value;

        if (first_name == "") {
            alert("Firs Name is mandatory!");
            return false;
        } else if (last_name == "") {
            alert("Last Name is mandatory!");
            return false;
        }

        if (document.adminForm.password.value != document.adminForm.password_confirm.value) {
            alert("<?php echo JText::_("DSCONFIRM_PASSWORD_MSG"); ?>");
            return false;
        }

        if (!validateCustomFields()) {
            return false;
        }

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

    function deleteImage() {
        document.getElementById("view_imagelist23").src = "<?php echo JURI::root(); ?>components/com_guru/images/blank.png";
        document.getElementById("image").value = "";
    }
</script>

<div class="gru-myprofile">
    <form onsubmit="return validateForm();" id="adminForm" name="adminForm" method="post" action="<?php echo JUri::root() ?>index.php" class="uk-form uk-form-horizontal wk-form wk-form-horizontal">
        <?php
        if (!$is_student) {
            echo '<div class="uk-alert uk-alert-warning wk-alert wk-alert-warning">' . JText::_("GURU_NOT_STUDENT_COMPLETE_PROFILE") . '</div>';
        }

        echo $div_menu;
        echo $page_title_cart;
        ?>

        <!-- Basic info -->

        <div class="uk-form-row wk-form-row">
            <label class="uk-form-label wk-form-label" for="name">
                <?php echo JText::_("GURU_FIRS_NAME"); ?>:
                <span class="uk-text-danger wk-text-danger">*</span>
            </label>
            <div class="uk-form-controls wk-form-controls">
                <input type="text" class="inputbox" size="30" id="firstname" name="firstname" value="<?php echo $firstname; ?>" />
            </div>
        </div>

        <div class="uk-form-row wk-form-row">
            <label class="uk-form-label wk-form-label" for="name">
                <?php echo JText::_("GURU_LAST_NAME"); ?>:
                <span class="uk-text-danger wk-text-danger">*</span>
            </label>
            <div class="uk-form-controls wk-form-controls">
                <input type="text" class="inputbox" size="30" id="lastname" name="lastname" value="<?php echo $lastname; ?>" />
            </div>
        </div>

        <div class="uk-form-row wk-form-row">
            <label class="uk-form-label wk-form-label" for="name">
                <?php echo JText::_("GURU_COMPANY"); ?>:
            </label>
            <div class="uk-form-controls wk-form-controls">
                <input type="text" class="inputbox" size="30" id="company" name="company" value="<?php echo $company; ?>" />
            </div>
        </div>

        <?php
        $config = $this->configs;

        $upload_mb = guruAdminHelper::getUploadMaxSize();
        $upload_mb *= 1048576; //transform in bytes
        $doc = JFactory::getDocument();

        $config_author = json_decode($config["0"]["authorpage"]);
        $author_t_prop = $config_author->author_image_size_type == "0" ? "width" : "heigth";

        $doc->addScriptDeclaration('
                jQuery.noConflict();
                jQuery(function(){
                    function createUploader(){
                        var uploader = new qq.FileUploader({
                            element: document.getElementById(\'fileUploader\'),
                            action: \'' . JURI::root() . 'index.php?option=com_guru&controller=guruLogin&tmpl=component&format=raw&task=upload_ajax_image\',
                            params:{
                                folder:\'customers\',
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
                                        jQuery(\'#image\').val("/"+responseJSON.locate +"/"+ fileName);
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
            ');
        //$doc->addScript('components/com_guru/js/fileuploader.js');
        JHtml::stylesheet('components/com_guru/css/fileuploader.css');
        ?>

        <div class="uk-form-row wk-form-row">
            <label class="uk-form-label wk-form-label" for="name">
                <?php echo JText::_("GURU_UPLOAD_IMAGE"); ?>:
            </label>
            <div class="uk-form-controls wk-form-controls">
                <div id="fileUploader"></div>
                <input type="hidden" name="image" id="image" value="<?php echo $image; ?>" />
            </div>
        </div>

        <?php
        if (isset($image) && $image != "") {
            $site_home = JURI::root();

            if (substr($site_home, -1) == '/') {
                $site_home = substr($site_home, 0, -1);
            }
        ?>
            <div class="uk-form-row wk-form-row">
                <label class="uk-form-label wk-form-label" for="name">
                    <?php echo JText::_("GURU_SEL_IMAGE"); ?>:
                </label>
                <div class="uk-form-controls wk-form-controls">
                    <div id='authorImageSelected'>
                        <img id="view_imagelist23" name="view_imagelist" src='<?php echo $site_home . $image; ?>' /><br />
                    </div>
                    <br />
                    <input type="button" class="uk-button uk-button-danger wk-button wk-button-danger" value="<?php echo JText::_('GURU_REMOVE'); ?>" onclick="return deleteImage();" />
                    <input type="hidden" value="<?php echo $image; ?>" name="img_name" id="img_name" />
                </div>
            </div>
        <?php
        } else {
        ?>
            <div class="uk-form-row wk-form-row">
                <label class="uk-form-label wk-form-label" for="name">
                </label>
                <div class="uk-form-controls wk-form-controls">
                    <div id='authorImageSelected'>
                        <img id='view_imagelist23' name='view_imagelist' src="<?php echo JURI::root(); ?>components/com_guru/images/blank.png" />
                    </div>
                </div>
            </div>
        <?php
        }
        ?>

        <div class="uk-alert wk-alert">
            <?php echo JText::_('GURU_LOGIN_INFORMATIONS'); ?>
        </div>

        <div class="uk-form-row wk-form-row">
            <label class="uk-form-label wk-form-label" for="name">
                <?php echo JText::_("GURU_PROFILE_USERNAME"); ?>:
                <span class="uk-text-danger wk-text-danger">*</span>
            </label>
            <div class="uk-form-controls wk-form-controls">
                <input type="text" class="inputbox" size="30" id="username" disabled="disabled" name="username" value="<?php echo $user_username; ?>" />
            </div>
        </div>

        <div class="uk-form-row wk-form-row">
            <label class="uk-form-label wk-form-label" for="name">
                <?php echo JText::_("GURU_EMAIL"); ?>:
                <span class="uk-text-danger wk-text-danger">*</span>
            </label>
            <div class="uk-form-controls wk-form-controls">
                <input type="text" class="inputbox" size="30" id="email" name="email" disabled="disabled" value="<?php echo $user_email; ?>" />
                <span style="font-size: 0.8em;" class="g_level_remark">
                    <?php
                    if ($user->id == "0") {
                        echo JText::_("DSEMAILNOTE");
                    }
                    ?>
                </span>
            </div>
        </div>

        <?php
        if ($returnpage != "checkout") {
        ?>
            <div class="uk-form-row wk-form-row">
                <label class="uk-form-label wk-form-label" for="name">
                    <?php echo JText::_("GURU_PROFILE_REG_PSW"); ?>:
                </label>
                <div class="uk-form-controls wk-form-controls">
                    <input type="password" class="inputbox" size="30" id="password" name="password" />
                </div>
            </div>

            <div class="uk-form-row wk-form-row">
                <label class="uk-form-label wk-form-label" for="name">
                    <?php echo JText::_("GURU_PROFILE_REG_PSW2"); ?>
                </label>
                <div class="uk-form-controls wk-form-controls">
                    <input type="password" class="inputbox" size="30" id="password_confirm" name="password_confirm" />
                </div>
            </div>
        <?php
        } else {
        ?>
            <input type="hidden" name="password" value="" />
            <input type="hidden" name="password_confirm" value="" />
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

        <div class="uk-form-row wk-form-row">
            <label class="uk-form-label wk-form-label" for="name">
                <input type="submit" value="<?php echo JText::_("GURU_SAVE"); ?>" class="uk-button uk-button-primary wk-button wk-button-primary">
            </label>
        </div>

        <input type="hidden" value="0" name="Itemid" />
        <input type="hidden" value="com_guru" name="option" />
        <input type="hidden" value="<?php echo $user_id; ?>" name="id" />
        <input type="hidden" value="saveCustomer" name="task" />
        <input type="hidden" value="<?php echo $returnpage; ?>" name="returnpage" />
        <input type="hidden" value="guruProfile" name="controller" />
        <input type="hidden" value="<?php echo $user_username; ?>" name="username" />
        <input type="hidden" value="<?php echo $user_email; ?>" name="email" />
    </form>
</div>