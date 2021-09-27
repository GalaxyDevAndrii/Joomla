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

$input = JFactory::getApplication()->input;
$lesson	= $input->get('lesson_id', '', 'INT');
$scorm	= $input->get('scorm', '', 'INT');
$scoid	= $input->get('scoid', '', 'INT');
$attempt =	$input->get('attempt', '', 'INT');
$mode =	$input->get('mode', '', 'STRING');

$userid = JFactory::getUser()->id;

include_once(JPATH_SITE.DIRECTORY_SEPARATOR."components".DIRECTORY_SEPARATOR."com_guru".DIRECTORY_SEPARATOR."helpers".DIRECTORY_SEPARATOR."scorm.php");
$scormhelper = new scormGuruImport();

$scormdata = $scormhelper->getScormDetails($scorm);
$sco_data = $scormhelper->getSCOdata($scormdata->id, $scormdata->entry);

$ext = pathinfo($scormdata->package, PATHINFO_EXTENSION);
$scormFoldername = basename($scormdata->package, "." . $ext);
$result	= JURI::base() . 'media/scorm/lessons/' . $lesson . '/scorm/' . @$sco_data->launch;

if ($scormFoldername && JFolder::exists(JPATH_SITE . '/media/scorm/lessons/' . $lesson . '/'. $scormFoldername)){
	$result	= JURI::base() . 'media/scorm/lessons/' . $lesson . '/'. $scormFoldername .'/' . @$sco_data->launch;
}

$version = strtolower($scormdata->version) === 'scorm_1.3' ? 'scorm_1.3' : 'scorm_1.2';

if ($mode != 'preview'){
	if (isset($sco_data->scormtype) && $sco_data->scormtype == 'asset'){
		$element = ($version	!=	'SCORM_1.2') ? 'cmi.completion_status' : 'cmi.core.lesson_status';
		$value = 'completed';
		$res = $scormhelper->scorm_insert_track($userid, $scorm, $scoid, $attempt, $element, $value);
	}
}

$LMS_api = (($version	==	'SCORM_1.2') || empty($version)) ? 'API' : 'API_1484_11';

$userscormdata	=	$scormhelper->getUserScodata($scorm,$scoid, $userid, $attempt);
include(JPATH_SITE .'/components/com_guru/helpers/'.strtolower($version).'.js.php');

?>

<script type="text/javascript">
	//doredirect();

	var myApiHandle = null;
	var myFindAPITries = 0;

	function myGetAPIHandle() {
		myFindAPITries = 0;

		if (myApiHandle == null) {
			myApiHandle = myGetAPI();
		}

		return myApiHandle;
	}

	function myFindAPI(win) {
		while ((win.<?php echo $LMS_api; ?> == null) && (win.parent != null) && (win.parent != win)) {
			myFindAPITries++;
			// Note: 7 is an arbitrary number, but should be more than sufficient
			if (myFindAPITries > 10) {
				return null;
			}

			win	= win.parent;
		}

		return win.<?php echo $LMS_api; ?>;
	}

	// hun for the API - needs to be loaded before we can launch the package
	function myGetAPI() {
		var theAPI = myFindAPI(window);

		if ((theAPI == null) && (window.opener != null) && (typeof(window.opener) != "undefined")) {
			theAPI = myFindAPI(window.opener);
		}

		if (theAPI == null) {
			return null;
		}

		return theAPI;
	}

	function doredirect() {
	   	if (myGetAPIHandle() != null) {
			location = "<?php echo $result ?>";
		}
		else {
			var timer = setInterval(function() {
				location = "<?php echo $result; ?>";
			}, 1000);
		}
	}
</script>

<script>
var site_root = "<?php echo JURI::base();?>";
var errorCode = "0";
function underscore(str) {
    str = String(str).replace(/.N/g,".");
    return str.replace(/\./g,"__");
}
jQuery(window).load(function () {

	var height = jQuery(".guru_lesson_screen", top.document).height();
	if(!height)
		height = jQuery(this).height();
	jQuery("object").css("height",height-50);
	jQuery("object").css("width",'100%');
	jQuery("#scorm_object").css("height",height-50);
	jQuery("#scorm_object").css("width",'100%');
	jQuery('body').attr('style', 'overflow: hidden !important');

	hideImage();
});
function hideImage()
{
	jQuery('#appsloading').remove();
}
</script>

<iframe id="scorm_object" frameBorder="0" type="text/html" src="<?php echo $result; ?>"></iframe>