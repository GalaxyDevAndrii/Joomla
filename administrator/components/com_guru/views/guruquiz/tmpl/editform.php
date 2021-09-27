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
JHTML::_('behavior.tooltip');
JHTML::_('behavior.modal');
JHtml::_('behavior.calendar');

$input = JFactory::getApplication()->input;
$cid = $input->get('cid');
$program = $this->program;
$lists = $program->lists;
$max_reo = $this->max_reo;
$min_reo = $this->min_reo;
if ($program->id == "") {
	$program->id = 0;
}

//$editorul  = JFactory::getEditor();
$mmediam = ((array)$this->mmediam) ? $this->mmediam : array();
foreach ($mmediam as $mmm) {
	$vector[] = $mmm->id;
}

$value_optiono = $input->get("v", "0");
if ($program->is_final == 0) {
	$value_option = 0;
} else {
	$value_option = 1;
}
if ($program->id == 0) {
	$value_option = $value_optiono;
}
$mainmedia = $this->mainmedia;
$configuration = guruAdminModelguruQuiz::getConfigs();
$amount_quest = guruAdminModelguruQuiz::getAmountQuestions($program->id);
$amount_quest_quizzes = guruAdminModelguruQuiz::getAmountQuizzes($program->id);

$data_post = $input->post->getArray();
$data_get = $input->get->getArray();
$data_request = $input->request->getArray();

$listDirn = "asc";
$listOrder = "ordering";
$saveOrderingUrl = 'index.php?option=com_guru&controller=guruQuiz&task=saveOrderQuestions&tmpl=component';
JHtml::_('sortablelist.sortable', 'articleList', 'adminForm', strtolower($listDirn), $saveOrderingUrl);

$db = JFactory::getDBO();
$sql = "Select datetype FROM #__guru_config where id=1 ";
$db->setQuery($sql);
$format_date = $db->loadColumn();
$dateformat = $format_date[0];

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

$request_final = "";
if ($value_optiono == "1") {
	$request_final = "&v=1";
}

?>
<?php //WE ARE NOT LONGER BEEN USING AJAX FROM prototype-1.6.0.2.js, INSTEAD WE WILL BE USING jQuery.ajax({}) function 
?>
<script type="text/javascript" src="<?php //echo JURI::base().'components/com_guru/views/gurutasks/tmpl/prototype-1.6.0.2.js'; 
									?>"></script>

<script language="javascript" type="text/javascript">
	var first = false;

	function timeToStamp(string_date) {
		var form = document.adminForm;
		var time_format = form["time_format"].value;
		myDate = string_date.split(" ");
		myDate = myDate[0].split("-");

		if (myDate instanceof Array && myDate.length > 1) {} else {
			myDate = myDate[0].split("/");
		}
		var newDate = '';

		switch (time_format) {
			case "%m/%d/%Y %H:%M:%S":
				newDate = myDate[0] + "/" + myDate[1] + "/" + myDate[2];
				break;
			case "%Y-%m-%d %H:%M:%S":
				newDate = myDate[1] + "/" + myDate[2] + "/" + myDate[0];
				break;
			case "%d-%m-%Y":
				newDate = myDate[1] + "/" + myDate[0] + "/" + myDate[2];
				break;
			case "%m/%d/%Y":
				newDate = myDate[0] + "/" + myDate[1] + "/" + myDate[2];
				break;
			case "%Y-%m-%d":
				newDate = myDate[1] + "/" + myDate[0] + "/" + myDate[2];
				break;
		}

		return newDate;
	}

	function validDateTime(datetime) {
		var form = document.adminForm;
		var time_format = form["time_format"].value;

		if (datetime == "") {
			return false;
		}

		switch (time_format) {
			case "%m/%d/%Y %H:%M:%S":
				var date_regex = /^(0[1-9]|1[0-2])\/(0[1-9]|[1-2][0-9]|3[0-1])\/[0-9]{4} [0-9]{2}:[0-9]{2}:[0-9]{2}$/;
				if (!(date_regex.test(datetime))) {
					return false;
				}
				break;
			case "%Y-%m-%d %H:%M:%S":
				var date_regex = /^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1]) [0-9]{2}:[0-9]{2}:[0-9]{2}$/;
				if (!(date_regex.test(datetime))) {
					return false;
				}
				break;
			case "%d-%m-%Y":
				var date_regex = /^(0[1-9]|[1-2][0-9]|3[0-1])-(0[1-9]|1[0-2])-[0-9]{4}$/;
				if (!(date_regex.test(datetime))) {
					return false;
				}
				break;
			case "%m/%d/%Y":
				var date_regex = /^(0[1-9]|1[0-2])\/(0[1-9]|[1-2][0-9]|3[0-1])\/[0-9]{4}$/;
				if (!(date_regex.test(datetime))) {
					return false;
				}
				break;
			case "%Y-%m-%d":
				var date_regex = /^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/;
				if (!(date_regex.test(datetime))) {
					return false;
				}
				break;
		}
		return true;
	}

	function page_refresh() {
		if (<?php echo intval(@$data_request["cid"]["0"]); ?> > 0) {
			document.location = 'index.php?option=com_guru&controller=guruQuiz&task=edit&cid[]=' + <?php echo intval(@$data_request["cid"]["0"]); ?> + '&tb=q';
		} else {
			submitform('applynew');
		}
	}

	function publish(x) {
		var url = 'index.php?option=com_guru&controller=guruQuiz&tmpl=component&format=raw&task=ajax_request&id=' + x + '&action=publish<?php echo $request_final; ?>';

		jQuery.ajax({
			url: url,
			method: 'get',
			asynchronous: 'true',
			success: function(response) {
				document.getElementById('publishing' + x).innerHTML = '<a class="icon-ok" style="cursor: pointer; text-decoration: none;" onclick="javascript:unpublish(\'' + x + '\');"></a>';
			}
		});

		/*var req = jQuery.ajax({
			async: false,
			method: 'get',
			url: 'index.php?option=com_guru&controller=guruQuiz&tmpl=component&format=raw&task=ajax_request&id='+x+'&action=publish<?php echo $request_final; ?>',
			data: { 'do' : '1' },
			onComplete: function(response){
				document.getElementById('publishing'+x).innerHTML='<a class="icon-ok" style="cursor: pointer; text-decoration: none;" onclick="javascript:unpublish(\''+x+'\');"></a>';
			}
		})*/
	}

	function unpublish(x) {
		var url = 'index.php?option=com_guru&controller=guruQuiz&tmpl=component&format=raw&task=ajax_request&id=' + x + '&action=unpublish<?php echo $request_final; ?>';

		jQuery.ajax({
			url: url,
			method: 'get',
			asynchronous: 'true',
			success: function(response) {
				document.getElementById('publishing' + x).innerHTML = '<a class="icon-remove" style="cursor: pointer; text-decoration: none;" onclick="javascript:publish(\'' + x + '\');"></a>';
			}
		});

		/*var req = jQuery.ajax({
			async: false,
			method: 'get',
			url: 'index.php?option=com_guru&controller=guruQuiz&tmpl=component&format=raw&task=ajax_request&id='+x+'&action=unpublish<?php echo $request_final; ?>',
			data: { 'do' : '1' },
			onComplete: function(response){
				document.getElementById('publishing'+x).innerHTML='<a class="icon-remove" style="cursor: pointer; text-decoration: none;" onclick="javascript:publish(\''+x+'\');"></a>';
			}
		})*/
	}

	function showContentQuestion(href) {
		jQuery.ajax({
			url: href,
			success: function(response) {
				jQuery('#myModal .modal-body').html(response);
			}
		});
	}

	function editOptionsQ() {
		display = document.getElementById("button-options2").style.display;

		if (display == "none") {
			document.getElementById("button-options2").style.display = "";
		} else {
			document.getElementById("button-options2").style.display = "none";
		}
	}
	jQuery(document).click(function(e) {
		if (jQuery(e.target).attr('id') != 'guru-dropdown-toggle' && jQuery(e.target).attr('id') != 'icon-bell' && jQuery(e.target).attr('id') != 'badge-important' && jQuery(e.target).attr('id') != 'new-options-button2') {
			if (eval(document.getElementById("button-options2"))) {
				document.getElementById("button-options2").style.display = "none";
			}
		}
	})

	function showContent1(href) {
		first = true;
		jQuery('#myModal1 .modal-bodyc iframe').attr('src', href);
		screen_height = window.innerHeight;
		document.getElementById('myModal1').style.height = (screen_height - 110) + 'px';
		document.getElementById('quiz_edit').style.height = (screen_height - 150) + 'px';
	}
	jQuery('body').click(function() {
		if (!first) {
			jQuery('#myModal1 .modal-bodyc iframe').attr('src', '');
		} else {
			first = false;
		}
	});
</script>
<style>
	#rowquestion {
		background-color: #ffffff;
	}

	#rowquestion tr {
		background-color: #ffffff;
	}

	#rowquestion td {
		background-color: #ffffff;
	}

	.pagination-list {
		margin-top: 20px;
	}

	div.modal {
		z-index: 9999;
		margin-left: -34%;
		top: 6%;
		padding: 10px;
		width: 70%;
	}

	.modal-backdrop,
	.modal-backdrop.fade.in {
		opacity: 0.4 !important;
	}

	div.modal-header {
		padding: 5px;
	}
</style>
<script>
	function delete_temp_m(i) {
		document.getElementById('trm' + i).style.display = 'none';
		document.getElementById('mediafiletodel').value = document.getElementById('mediafiletodel').value + ',' + i;
	}

	function delete_q(i, id, t) {
		var ok = confirm('This will delete question immediately. Are you sure?');
		if (!ok) {
			return;
		}

		var deleted = i;
		var url = 'index.php?option=com_guru&controller=guruQuiz&tmpl=component&format=raw&task=ajax_request&id=' + id + '&deleted=' + deleted + '&f=' + t;

		jQuery.ajax({
			url: url,
			method: 'get',
			asynchronous: 'true',
			success: function(response) {
				document.getElementById('trque' + i).style.display = 'none';
			}
		});
	}

	function delete_fq(i, id, t) {
		var ok = confirm('This will delete question immediately. Are you sure?');
		if (!ok) {
			return;
		}

		var deleted = i;
		var url = 'index.php?option=com_guru&controller=guruQuiz&tmpl=component&format=raw&task=ajax_request&id=' + id + '&deleted=' + deleted + '&f=' + t;

		jQuery.ajax({
			url: url,
			method: 'get',
			asynchronous: 'true',
			success: function(response) {
				document.getElementById('trfque' + i).style.display = 'none';
			}
		});
	}
</script>
<?php if ($value_option == 0) { ?>
	<script language="javascript" type="text/javascript">
		function timeToStamp(string_date) {
			var form = document.adminForm;
			var time_format = form["time_format"].value;
			myDate = string_date.split(" ");
			myDate = myDate[0].split("-");

			if (myDate instanceof Array && myDate.length > 1) {} else {
				myDate = myDate[0].split("/");
			}
			var newDate = '';

			switch (time_format) {
				case "%m/%d/%Y %H:%M:%S":
					newDate = myDate[0] + "/" + myDate[1] + "/" + myDate[2];
					break;
				case "%Y-%m-%d %H:%M:%S":
					newDate = myDate[1] + "/" + myDate[2] + "/" + myDate[0];
					break;
				case "%d-%m-%Y":
					newDate = myDate[1] + "/" + myDate[0] + "/" + myDate[2];
					break;
				case "%m/%d/%Y":
					newDate = myDate[0] + "/" + myDate[1] + "/" + myDate[2];
					break;
				case "%Y-%m-%d":
					newDate = myDate[1] + "/" + myDate[0] + "/" + myDate[2];
					break;
			}

			return newDate;
		}

		function validDateTime(datetime) {
			var form = document.adminForm;
			var time_format = form["time_format"].value;

			if (datetime == "") {
				return false;
			}

			switch (time_format) {
				case "%m/%d/%Y %H:%M:%S":
					var date_regex = /^(0[1-9]|1[0-2])\/(0[1-9]|[1-2][0-9]|3[0-1])\/[0-9]{4} [0-9]{2}:[0-9]{2}:[0-9]{2}$/;
					if (!(date_regex.test(datetime))) {
						return false;
					}
					break;
				case "%Y-%m-%d %H:%M:%S":
					var date_regex = /^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1]) [0-9]{2}:[0-9]{2}:[0-9]{2}$/;
					if (!(date_regex.test(datetime))) {
						return false;
					}
					break;
				case "%d-%m-%Y":
					var date_regex = /^(0[1-9]|[1-2][0-9]|3[0-1])-(0[1-9]|1[0-2])-[0-9]{4}$/;
					if (!(date_regex.test(datetime))) {
						return false;
					}
					break;
				case "%m/%d/%Y":
					var date_regex = /^(0[1-9]|1[0-2])\/(0[1-9]|[1-2][0-9]|3[0-1])\/[0-9]{4}$/;
					if (!(date_regex.test(datetime))) {
						return false;
					}
					break;
				case "%Y-%m-%d":
					var date_regex = /^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/;
					if (!(date_regex.test(datetime))) {
						return false;
					}
					break;
			}
			return true;
		}

		function isFloat(nr) {
			return parseFloat(nr.match(/^-?\d*(\.\d+)?$/)) > 0;
		}

		function isInt(n) {
			return parseInt(n) === n;
		}

		Joomla.submitbutton = function(pressbutton) {
			var form = document.adminForm;
			if (pressbutton == 'save' || pressbutton == 'apply') {
				if (form['name'].value == "") {
					alert("<?php echo JText::_("GURU_TASKS_JS_NAME"); ?>");
				}

				if (isNaN(parseInt(form['author'].value))) {
					alert("<?php echo JText::_("GURU_CS_QAUTHOR"); ?>");
				} else {
					//---------------------------------
					start_date = form['startpublish'].value;
					end_date = form['endpublish'].value;

					if (end_date != "Never" && end_date != "") {
						if (!validDateTime(start_date) || !validDateTime(end_date)) {
							alert("<?php echo JText::_("GURU_INVALID_DATE"); ?>");
							return false;
						}
					}

					if (form['endpublish'].value != "Never" && form['endpublish'].value != "") {
						start_date = new Date(timeToStamp(start_date)).getTime();
						end_date = new Date(timeToStamp(end_date)).getTime();

						if (Date.parse(start_date) > Date.parse(end_date)) {
							alert("<?php echo JText::_("GURU_DATE_GRATER"); ?>");
							return false;
						}
					}

					max_score_pass = document.getElementById("max_score_pass").value;
					limit_time_l = document.getElementById("limit_time_l").value;
					limit_time_f = document.getElementById("limit_time_f").value;

					if (!isFloat(max_score_pass) || max_score_pass <= 0) {
						alert("<?php echo JText::_("GURU_INVALID_NUMBER_AVG"); ?>");
						return false;
					}

					//---------------------------------
					submitform(pressbutton);
				}
			} else {
				submitform(pressbutton);
			}
		}
	</script>
	<div id="myModal" class="modal hide">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		</div>
		<div class="modal-body">
		</div>
	</div>

	<div id="myModal1" class="modal hide" style="">
		<div class="modal-header">
			<button type="button" id="close" class="close" data-dismiss="modal" aria-hidden="true">x</button>
		</div>
		<div class="modal-bodyc" style="background-color:#FFFFFF;">
			<iframe style="" id="quiz_edit" width="100%" frameborder="0"></iframe>
		</div>
	</div>
	<form action="<?php echo JUri::root() ?>administrator/index.php" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">
		<div class="well">
			<?php
			if ($program->id < 1) {
				echo JText::_('GURU_QUIZ') . ": [" . JText::_('GURU_NEW') . "]";
			} else {
				echo JText::_('GURU_QUIZ') . ": [" . JText::_('GURU_EDIT') . "]";
			}
			?>
		</div>

		<?php
		$session = JFactory::getSession();
		$registry = $session->get('registry');

		$added_question = $registry->get('added_question', null);
		$active_pagination = $input->get("active_pagination", "0");
		$class_general = "active";
		$class_question = "";
		$class_message = "";
		$class_publish = "";

		if ($active_pagination == "1" || isset($added_question)) {
			$class_general = "";
			$class_question = "active";
			$class_publish = "";
			$class_message = "";

			$session = JFactory::getSession();
			$registry = $session->get('registry');
			$added_question = $registry->get('added_question', "");

			if (isset($added_question) && trim($added_question) != "") {
				$registry->set('added_question', "");
			}
		}
		?>

		<ul class="nav nav-tabs">
			<li class="nav-item <?php echo $class_general; ?>"><a class="nav-link" href="#general" data-toggle="tab"><?php echo JText::_('GURU_GENERAL'); ?></a></li>
			<li class="nav-item <?php echo $class_question; ?>"><a class="nav-link <?php echo $class_question; ?>" href="#question" data-toggle="tab"><?php echo JText::_('GURU_QUESTIONS'); ?></a></li>
			<li class="nav-item <?php echo $class_message; ?>"><a class="nav-link <?php echo $class_message; ?>"" href=" #message" data-toggle="tab"><?php echo JText::_('GURU_PASS_FAIL_MESSAGE'); ?></a></li>
			<li class="nav-item <?php echo $class_publish; ?>"><a class="nav-link <?php echo $class_publish; ?>"" href=" #publish" data-toggle="tab"><?php echo JText::_('GURU_PUBLISHING'); ?></a></li>
		</ul>

		<div class="tab-content">
			<div class="tab-pane <?php echo $class_general; ?>" id="general">
				<table class="adminform">
					<tr>
						<td width="15%">
							<?php echo JText::_('GURU_NAME'); ?>:<font color="#ff0000">*</font>
						</td>
						<td>
							<input class="inputbox" type="text" id="name" name="name" size="40" maxlength="255" value="<?php echo $program->name; ?>" />
							<span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_QUIZ_NAME"); ?>">
								<img border="0" src="components/com_guru/images/icons/tooltip.png">
							</span>
						</td>
					</tr>
					<tr>
						<td>
							<?php echo JText::_('GURU_AUTHOR'); ?>:<font color="#ff0000">*</font>
						</td>
						<td>
							<?php echo $lists['author']; ?>
							<span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_AUTHOR_Q"); ?>">
								<img border="0" src="components/com_guru/images/icons/tooltip.png">
							</span>
						</td>
					</tr>
					<tr>
						<td>
							<?php echo JText::_('GURU_PRODDESC'); ?>:
						</td>
						<td>
							<?php //echo $editorul->display( 'description', ''.stripslashes($program->description),'100%', '300px', '20', '60' );
							?>
							<textarea name="description" id="description" cols="40" rows="8"><?php echo stripslashes($program->description); ?></textarea>
							<span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_QUIZ_PRODDESC"); ?>">
								<img border="0" src="components/com_guru/images/icons/tooltip.png">
							</span>
						</td>
					</tr>
					<tr>
						<td>
							<?php echo JText::_('GURU_SHOW_CORRECT_ANS'); ?>:
						</td>
						<td>
							<fieldset class="radio btn-group" id="show_correct_ans">
								<?php
								$no_checked = "";
								$yes_cheched = "";

								if ($program->show_correct_ans == "0") {
									$no_checked = 'checked="checked"';
								} else {
									$yes_cheched = 'checked="checked"';
								}
								?>
								<input type="hidden" name="show_correct_ans" value="0">
								<label for="checkbox1_show_correct_ans" class="" style="display: none;"></label>
								<input type="checkbox" <?php echo $yes_cheched; ?> value="1" name="show_correct_ans" id="checkbox1_show_correct_ans" class="ace-switch ace-switch-5">
								<span class="lbl"></span>
							</fieldset>
							<span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_SHOW_CORRECT_ANS"); ?>">
								<img border="0" src="components/com_guru/images/icons/tooltip.png">
							</span>
						</td>
					</tr>
					<tr>
						<td width="15%">
							<?php echo JText::_('GURU_MINIMUM_SCORE_QUIZ'); ?>:
						</td>
						<td>
							<?php if (isset($program->max_score)) {
								$program->max_score = $program->max_score;
							} else {
								$program->max_score = 70;
							}



							?>
							<input type="text" id="max_score_pass" name="max_score_pass" value="<?php echo $program->max_score; ?>" style="float:left !important;" />&nbsp;
							<span style="float:left !important; padding-top:4px; padding-left:19px;">% &nbsp;</span>

							<select id="show_max_score_pass" name="show_max_score_pass" style="float:left !important;">
								<option value="0" <?php if ($program->pbl_max_score == "0") {
														echo 'selected="selected"';
													} ?>><?php echo JText::_("GURU_SHOW"); ?></option>
								<option value="1" <?php if ($program->pbl_max_score == "1") {
														echo 'selected="selected"';
													} ?>><?php echo JText::_("GURU_HIDE"); ?></option>
							</select>
							<span class="editlinktip hasTip" title="<?php echo JText::_('GURU_MAX_SCORE_TOOLTIP'); ?>">
								<img border="0" src="components/com_guru/images/icons/tooltip.png">
							</span>
						</td>
					</tr>
					<tr>
						<td width="15%">
							<?php echo JText::_('GURU_QUIZ_CAN_BE_TAKEN'); ?>:
						</td>
						<td>
							<select id="nb_quiz_taken" name="nb_quiz_taken" style="float:left !important;">
								<option value="1" <?php if ($program->time_quiz_taken == "1") {
														echo 'selected="selected"';
													} ?>>1</option>
								<option value="2" <?php if ($program->time_quiz_taken == "2") {
														echo 'selected="selected"';
													} ?>>2</option>
								<option value="3" <?php if ($program->time_quiz_taken == "3") {
														echo 'selected="selected"';
													} ?>>3</option>
								<option value="4" <?php if ($program->time_quiz_taken == "4") {
														echo 'selected="selected"';
													} ?>>4</option>
								<option value="5" <?php if ($program->time_quiz_taken == "5") {
														echo 'selected="selected"';
													} ?>>5</option>
								<option value="6" <?php if ($program->time_quiz_taken == "6") {
														echo 'selected="selected"';
													} ?>>6</option>
								<option value="7" <?php if ($program->time_quiz_taken == "7") {
														echo 'selected="selected"';
													} ?>>7</option>
								<option value="8" <?php if ($program->time_quiz_taken == "8") {
														echo 'selected="selected"';
													} ?>>8</option>
								<option value="9" <?php if ($program->time_quiz_taken == "9") {
														echo 'selected="selected"';
													} ?>>9</option>
								<option value="10" <?php if ($program->time_quiz_taken == "10") {
														echo 'selected="selected"';
													} ?>>10</option>
								<option value="20" <?php if ($program->time_quiz_taken == "20") {
														echo 'selected="selected"';
													} ?>>20</option>
								<option value="30" <?php if ($program->time_quiz_taken == "30") {
														echo 'selected="selected"';
													} ?>>30</option>
								<option value="40" <?php if ($program->time_quiz_taken == "40") {
														echo 'selected="selected"';
													} ?>>40</option>
								<option value="50" <?php if ($program->time_quiz_taken == "50") {
														echo 'selected="selected"';
													} ?>>50</option>
								<option value="11" <?php if ($program->time_quiz_taken == "11") {
														echo 'selected="selected"';
													} ?>><?php echo JText::_("GURU_UNLIMPROMO"); ?></option>
							</select>

							<span style="float:left !important;padding-top:4px;padding-left:2px;">&nbsp;<?php echo JText::_("GURU_TIMES_T"); ?>&nbsp;</span>
							<select id="show_nb_quiz_taken" name="show_nb_quiz_taken" style="float:left !important;">
								<option value="0" <?php if ($program->show_nb_quiz_taken == "0") {
														echo 'selected="selected"';
													} ?>><?php echo JText::_("GURU_SHOW"); ?></option>
								<option value="1" <?php if ($program->show_nb_quiz_taken == "1") {
														echo 'selected="selected"';
													} ?>><?php echo JText::_("GURU_HIDE"); ?></option>
							</select>&nbsp;
							<span class="editlinktip hasTip" title="<?php echo JText::_('GURU_TIMES_TAKEN_TOOLTIP'); ?>">
								<img border="0" src="components/com_guru/images/icons/tooltip.png">
							</span>
						</td>
					</tr>

					<tr>
						<td width="15%">
							<?php echo JText::_('GURU_SELECT_UP_TO'); ?>:
						</td>
						<td style="padding-top:5px">
							<select id="nb_quiz_select_up" name="nb_quiz_select_up" style="float:left !important;">
								<?php
								if (isset($amount_quest) && $amount_quest != 0) {
								?>
									<option value="0"> <?php echo JText::_("GURU_SHOW_ALL_QUESTIONS"); ?> </option>
									<?php
									for ($i = $amount_quest; $i >= 1; $i--) {
									?>
										<option value="<?php echo $i; ?>" <?php if ($program->nb_quiz_select_up == $i) {
																				echo 'selected="selected"';
																			} ?>>
											<?php echo $i; ?>
										</option>
									<?php
									}
								} else {
									?>
									<option value="0"><?php echo "Please add questions first"; ?></option>
								<?php
								}
								?>
							</select>

							<span style="float:left !important; padding-top:4px; padding-left:2px;">&nbsp;<?php echo JText::_('GURU_QUESTION_RANDOM'); ?>&nbsp;</span>
							<select id="show_nb_quiz_select_up" name="show_nb_quiz_select_up" style="float:left !important;">
								<option value="0" <?php if ($program->show_nb_quiz_select_up == "0") {
														echo 'selected="selected"';
													} ?>><?php echo JText::_("GURU_SHOW"); ?></option>
								<option value="1" <?php if ($program->show_nb_quiz_select_up == "1") {
														echo 'selected="selected"';
													} ?>><?php echo JText::_("GURU_HIDE"); ?></option>
							</select>&nbsp;
							<span class="editlinktip hasTip" title="<?php echo JText::_('GURU_QUESTIONS_NUMBER_TOOLTIP'); ?>">
								<img border="0" src="components/com_guru/images/icons/tooltip.png">
							</span>
						</td>
					</tr>
					<tr>
						<td width="15%">
							<?php echo JText::_('GURU_QUESTIONS_PER_PAGE'); ?>:
						</td>
						<td style="padding-top:5px">
							<?php
							$questions_per_page = $program->questions_per_page;
							?>
							<select name="questions_per_page">
								<option value="5" <?php if ($questions_per_page == "5") {
														echo 'selected="selected"';
													} ?>>5</option>
								<option value="10" <?php if ($questions_per_page == "10") {
														echo 'selected="selected"';
													} ?>>10</option>
								<option value="15" <?php if ($questions_per_page == "15") {
														echo 'selected="selected"';
													} ?>>15</option>
								<option value="20" <?php if ($questions_per_page == "20") {
														echo 'selected="selected"';
													} ?>>20</option>
								<option value="25" <?php if ($questions_per_page == "25") {
														echo 'selected="selected"';
													} ?>>25</option>
								<option value="30" <?php if ($questions_per_page == "30") {
														echo 'selected="selected"';
													} ?>>30</option>
								<option value="50" <?php if ($questions_per_page == "50") {
														echo 'selected="selected"';
													} ?>>50</option>
								<option value="100" <?php if ($questions_per_page == "100") {
														echo 'selected="selected"';
													} ?>>100</option>
								<option value="0" <?php if ($questions_per_page == "0") {
														echo 'selected="selected"';
													} ?>>All</option>
							</select>
							&nbsp;
							<span class="editlinktip hasTip" title="<?php echo JText::_('GURU_QUESTIONS_PER_PAGE_TOOLTIP'); ?>">
								<img border="0" src="components/com_guru/images/icons/tooltip.png">
							</span>
						</td>
					</tr>

					<tr>
						<td width="15%">
							<?php echo JText::_('GURU_IF_STUDENT_FAILED'); ?>:
						</td>
						<td style="padding-top:5px">
							<select name="student_failed">
								<option value="0" <?php if ($program->student_failed == "0") {
														echo 'selected="selected"';
													} ?>><?php echo JText::_("GURU_STUDENT_CONTINUE"); ?></option>
								<option value="1" <?php if ($program->student_failed == "1") {
														echo 'selected="selected"';
													} ?>><?php echo JText::_("GURU_STUDENT_NOT_CONTINUE"); ?></option>
							</select>
							&nbsp;
							<span class="editlinktip hasTip" title="<?php echo JText::_('GURU_IF_STUDENT_FAILED_TIP'); ?>">
								<img border="0" src="components/com_guru/images/icons/tooltip.png">
							</span>
						</td>
					</tr>

					<tr>
						<td style=" font-weight:bold; font-size:25px"><?php echo JText::_("GURU_TIMER"); ?>:</td>
					</tr>
					<tr>
						<td width="15%">
							<?php echo JText::_('GURU_QUIZ_LIMIT_TIME'); ?>:
						</td>
						<td style="padding-top:5px">
							<?php if (isset($program->limit_time)) {
								$program->limit_time = $program->limit_time;
							} else {
								$program->limit_time = 3;
							}



							?>
							<select id="limit_time_l" name="limit_time_l" class="pull-left" style="margin-right:10px;">
								<option value="0"> <?php echo JText::_("GURU_UNLIMPROMO"); ?> </option>
								<?php
								for ($i = 1; $i <= 150; $i++) {
									$selected = "";
									$time = JText::_("GURU_MINUTES");

									if ($i == $program->limit_time) {
										$selected = 'selected="selected"';
									}

									if ($i == 1) {
										$time = JText::_("GURU_MINUTE");
									}

									echo '<option value="' . $i . '" ' . $selected . '>' . $i . " " . $time . '</option>';
								}
								?>
							</select>

							<select id="show_limit_time" name="show_limit_time" style="float:left !important;">
								<option value="0" <?php if ($program->show_limit_time == "0") {
														echo 'selected="selected"';
													} ?>><?php echo JText::_("GURU_SHOW"); ?></option>
								<option value="1" <?php if ($program->show_limit_time == "1") {
														echo 'selected="selected"';
													} ?>><?php echo JText::_("GURU_HIDE"); ?></option>
							</select>
							&nbsp;

							<span class="editlinktip hasTip" title="<?php echo JText::_('GURU_QUIZ_LIMIT_TIME_TOOLTIP'); ?>">
								<img border="0" src="components/com_guru/images/icons/tooltip.png">
							</span>
						</td>
					</tr>
					<tr>
						<td width="15%">
							<?php echo JText::_('GURU_SHOW_COUNTDOWN'); ?>:
						</td>
						<td style="padding-top:9px">
							<select id="show_countdown" name="show_countdown" style="float:left !important;">
								<option value="0" <?php if ($program->show_countdown == "0") {
														echo 'selected="selected"';
													} ?>><?php echo JText::_("GURU_SHOW"); ?></option>
								<option value="1" <?php if ($program->show_countdown == "1") {
														echo 'selected="selected"';
													} ?>><?php echo JText::_("GURU_HIDE"); ?></option>
							</select>&nbsp;
							<span class="editlinktip hasTip" title="<?php echo JText::_('GURU_SHOW_COUNTDOWN_TOOLTIP'); ?>">
								<img border="0" src="components/com_guru/images/icons/tooltip.png">
							</span>
						</td>
					</tr>
					<tr>
						<td width="15%">
							<?php echo JText::_('GURU_FINISH_ALERT'); ?>:
						</td>
						<td style="padding-top:5px">
							<?php if (isset($program->limit_time_f)) {
								$program->limit_time_f = $program->limit_time_f;
							} else {
								$program->limit_time_f = 1;
							}



							?>
							<select id="limit_time_f" name="limit_time_f" class="pull-left">
								<option value="2" <?php if ($program->limit_time_f == "2") {
														echo 'selected="selected"';
													} ?>> 2 <?php echo JText::_("GURU_MINUTES"); ?> </option>
								<option value="1" <?php if ($program->limit_time_f == "1") {
														echo 'selected="selected"';
													} ?>> 1 <?php echo JText::_("GURU_MINUTE"); ?> </option>
								<option value="30" <?php if ($program->limit_time_f == "30") {
														echo 'selected="selected"';
													} ?>> 30 <?php echo JText::_("GURU_SECONDS"); ?> </option>
								<option value="15" <?php if ($program->limit_time_f == "15") {
														echo 'selected="selected"';
													} ?>> 15 <?php echo JText::_("GURU_SECONDS"); ?> </option>
							</select>

							<span style="float:left !important; padding-top:4px; padding-left:2px;">&nbsp;<?php echo JText::_('GURU_MINUTES_BEFORE_IS_UP'); ?>&nbsp;</span>

							<select id="show_finish_alert" name="show_finish_alert" style="float:left !important;">
								<option value="0" <?php if ($program->show_finish_alert == "0") {
														echo 'selected="selected"';
													} ?>><?php echo JText::_("GURU_SHOW"); ?></option>
								<option value="1" <?php if ($program->show_finish_alert == "1") {
														echo 'selected="selected"';
													} ?>><?php echo JText::_("GURU_HIDE"); ?></option>
							</select>
							&nbsp;

							<span class="editlinktip hasTip" title="<?php echo JText::_('GURU_FINISH_ALERT_TOOLTIP'); ?>">
								<img border="0" src="components/com_guru/images/icons/tooltip.png">
							</span>
						</td>
					</tr>

					<tr>
						<td width="15%">
							<?php echo JText::_('GURU_RESET_TIMER'); ?>:
						</td>
						<td>
							<fieldset class="radio btn-group" id="reset_time">
								<?php
								$no_checked = "";
								$yes_cheched = "";

								if ($program->reset_time == "0") {
									$no_checked = 'checked="checked"';
								} else {
									$yes_cheched = 'checked="checked"';
								}
								?>
								<input type="hidden" name="reset_time" value="0">
								<label for="reset_time" class="" style="display: none;"></label>
								<input type="checkbox" <?php echo $yes_cheched; ?> value="1" id="reset_time" name="reset_time" class="ace-switch ace-switch-5">
								<span class="lbl"></span>
							</fieldset>

							<span class="editlinktip hasTip" title="<?php echo JText::_('GURU_RESET_TIMER_TOOLTIP'); ?>">
								<img border="0" src="components/com_guru/images/icons/tooltip.png">
							</span>
						</td>
					</tr>

					<tr>
						<td width="15%">
							<?php echo JText::_('GURU_RETAKE_PASSED_QUIZ'); ?>:
						</td>
						<td>
							<fieldset class="radio btn-group" id="retake_passed_quiz">
								<?php
								$no_checked = "";
								$yes_cheched = "";

								if ($program->retake_passed_quiz == "0") {
									$no_checked = 'checked="checked"';
								} else {
									$yes_cheched = 'checked="checked"';
								}
								?>
								<input type="hidden" name="retake_passed_quiz" value="0">
								<label for="retake_passed_quiz" class="" style="display: none;"></label>
								<input type="checkbox" <?php echo $yes_cheched; ?> value="1" id="retake_passed_quiz" name="retake_passed_quiz" class="ace-switch ace-switch-5">
								<span class="lbl"></span>
							</fieldset>

							<span class="editlinktip hasTip" title="<?php echo JText::_('GURU_RETAKE_PASSED_QUIZ_TOOLTIP'); ?>">
								<img border="0" src="components/com_guru/images/icons/tooltip.png">
							</span>
						</td>
					</tr>

				</table>
			</div>
			<div class="tab-pane <?php echo $class_question; ?>" id="question">
				<?php
				$cid = $input->get("cid", array(), "raw");

				if (is_array($cid) && isset($cid["0"]) && intval($cid["0"]) > 0) {
				?>
					<table class="table">
						<tr>
							<td>
								<div>
									<div style="float:left;">
										<div id="new-options2" style="position:relative;">
											<input type="button" id="new-options-button2" onclick="editOptionsQ();" class="btn btn-success g_toggle_button" value="<?php echo JText::_('GURU_ADD_QUESTION') . '&nbsp; &#9660;'; ?>">
											<div id="button-options2" class="button-options" style="display:none;">
												<ul style="font-size: 14px;">
													<li>
														<a data-toggle="modal" data-target="#myModal1" onClick="showContent1('index.php?option=com_guru&controller=guruQuiz&task=addquestion&tmpl=component&cid[]=<?php echo $program->id; ?>&type=true_false&new_add=1');" href="#"><?php echo JText::_("GURU_QUIZ_TRUE_FALSE"); ?></a>
													</li>
													<li>
														<a data-toggle="modal" data-target="#myModal1" onClick="showContent1('index.php?option=com_guru&controller=guruQuiz&task=addquestion&tmpl=component&cid[]=<?php echo $program->id; ?>&type=single&new_add=1');" href="#"><?php echo JText::_("GURU_QUIZ_SINGLE_CHOICE"); ?></a>
													</li>
													<li>
														<a data-toggle="modal" data-target="#myModal1" onClick="showContent1('index.php?option=com_guru&controller=guruQuiz&task=addquestion&tmpl=component&cid[]=<?php echo $program->id; ?>&type=multiple&new_add=1');" href="#"><?php echo JText::_("GURU_QUIZ_MULTIPLE_CHOICE"); ?></a>
													</li>
													<li>
														<a data-toggle="modal" data-target="#myModal1" onClick="showContent1('index.php?option=com_guru&controller=guruQuiz&task=addquestion&tmpl=component&cid[]=<?php echo $program->id; ?>&type=essay&new_add=1');" href="#"><?php echo JText::_("GURU_QUIZ_ESSAY"); ?></a>
													</li>
												</ul>
											</div>
										</div>
									</div>

									<div style="float:left;">
										<span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_QUIZ_ADD_QUESTION"); ?>">
											<img border="0" src="components/com_guru/images/icons/tooltip.png">
										</span>
									</div>
								</div>
								<br /><br />
								<table id="articleList" class="table" cellspacing="1" cellpadding="5" border="0" bgcolor="#cccccc" width="100%">
									<thead>
										<tr>
											<th>
												<?php echo JHtml::_('grid.sort', '<i class="icon-menu-2"></i>', 'a.ordering', $listDirn, $listOrder, null, 'asc', 'JGRID_HEADING_ORDERING'); ?>
											</th>
											<th></th>
											<th width="42%">
												<strong><?php echo JText::_('GURU_QUESTION'); ?></strong>
											</th>
											<th width="15%">
												<strong><?php echo JText::_('GURU_TYPE'); ?></strong>
											</th>
											<th width="17%">
												<strong><?php echo JText::_('GURU_REMOVE'); ?></strong>
											</th>
											<th width="17%">
												<strong><?php echo JText::_('GURU_COPY'); ?></strong>
											</th>
											<th width="12%">
												<strong><?php echo JText::_('GURU_EDIT'); ?></strong>
											</th>
											<th width="14%">
												<strong><?php echo JText::_('GURU_PUBLISHED'); ?></strong>
											</th>
										</tr>
										<thead>
										<tbody id="rowquestion">
											<?php
											if (isset($data_post['deleteq'])) {
												$hide_q2del = $data_post['deleteq'];
											} else {
												$hide_q2del = ',';
											}
											$hide_q2del = explode(',', $hide_q2del);
											$i = 0;
											foreach ($mmediam as $mmedial) {
												$link2_remove = '<font color="#FF0000"><span onClick="delete_q(' . $mmedial->id . ',' . $program->id . ',0)">' . JText::_('GURU_REMOVE') . '</span></font>';
											?>

												<tr class="row<?php echo $i % 2; ?>" id="trque<?php echo $mmedial->id; ?>" <?php if (in_array($mmedial->id, $hide_q2del)) { ?> style="display:none" <?php } ?>>
													<td>
														<span class="sortable-handler active" style="cursor: move;">
															<i class="icon-menu"></i>
														</span>
														<input type="text" class="width-20 text-area-order " value="<?php echo $mmedial->question_order; ?>" size="5" name="order[]" style="display:none;">
													</td>
													<td width="5%" style="text-align:center; visibility:hidden;"><?php $checked = JHTML::_('grid.id', $i, $mmedial->id);
																													echo $checked; ?></td>
													<td id="tdq<?php echo $mmedial->id ?>" width="42%">
														<?php
														$mmedial->question_content = strip_tags($mmedial->question_content);
														?>

														<a data-toggle="modal" data-target="#myModal1" onClick="showContent1('index.php?option=com_guru&controller=guruQuiz&task=addquestion&tmpl=component&cid[]=<?php echo $program->id . '&qid=' . $mmedial->id; ?>&type=<?php echo $mmedial->type; ?>');" href="#"><?php if (strlen($mmedial->question_content) > 55) {
																																																																															echo mb_substr(str_replace("\'", "&acute;", $mmedial->question_content), 0, 55) . '...';
																																																																														} else {
																																																																															echo str_replace("\'", "&acute;", $mmedial->question_content);
																																																																														} ?></a>
													</td>
													<td width="15%" nowrap="nowrap">
														<?php
														if ($mmedial->type == "true_false") {
															echo JText::_("GURU_QUIZ_TRUE_FALSE");
														} elseif ($mmedial->type == "single") {
															echo JText::_("GURU_QUIZ_SINGLE_CHOICE");
														} elseif ($mmedial->type == "multiple") {
															echo JText::_("GURU_QUIZ_MULTIPLE_CHOICE");
														} elseif ($mmedial->type == "essay") {
															echo JText::_("GURU_QUIZ_ESSAY");
														}
														?>
													</td>
													<td width="17%">
														<?php echo $link2_remove; ?>
													</td>
													<td width="17%">
														<a href="index.php?option=com_guru&controller=guruQuiz&task=copyquestion&tmpl=component&qid=<?php echo intval($program->id); ?>&question=<?php echo intval($mmedial->id); ?>">
															<?php echo JText::_("GURU_COPY_QUESTION"); ?>
														</a>
													</td>
							</td>
							<td width="12%">
								<a data-toggle="modal" data-target="#myModal1" onClick="showContent1('index.php?option=com_guru&controller=guruQuiz&task=addquestion&tmpl=component&cid[]=<?php echo $program->id . '&qid=' . $mmedial->id; ?>&type=<?php echo $mmedial->type; ?>');" href="#"><?php echo JText::_('GURU_EDIT'); ?></a>
								<?php if ($mmedial->published == 1) {
													echo "<input type='hidden' id='publ" . $mmedial->id . "' name='publish_q[" . $mmedial->id . "]' value='1' />";
												} else {
													echo "<input type='hidden' id='publ" . $mmedial->id . "' name='publish_q[" . $mmedial->id . "]' value='0' />";
												} ?>
							</td>
							<td width="14%" id="publishing<?php echo $mmedial->id; ?>">
								<?php
												if ($mmedial->published == 1) {
													echo '<a class="icon-ok" style="cursor: pointer; text-decoration: none;" onclick="javascript:unpublish(\'' . $mmedial->id . '\');"></a>';
												} else {
													echo '<a class="icon-remove" style="cursor: pointer; text-decoration: none;" onclick="javascript:publish(\'' . $mmedial->id . '\');"></a>';
												}
								?>
							</td>
						</tr>
					<?php
												$i++;
											}

											//end foreach
					?>
					<input type="hidden" value="<?php if (isset($data_post['newquizq'])) echo $data_post['newquizq']; ?>" id="newquizq" name="newquizq" />
					<input type="hidden" value="<?php if (isset($data_post['deleteq'])) echo $data_post['deleteq']; ?>" id="deleteq" name="deleteq" />
					</tbody>
					</table>
					</td>
					</tr>
					</table>

					<div class="pagination pagination-toolbar">
						<?php
						$limit = $this->pagination->getLimitBox();
						$limit = str_replace('onchange="', 'onchange="document.adminForm.active_pagination.value=1; ', $limit);
						echo $limit;
						$media = $this->media;
						$app = JFactory::getApplication('site');
						$limit = $app->getUserStateFromRequest('limit', 'limit', $app->getCfg('list_limit'), 'int');
						$limitstart = $input->get("limitstart", "0");
						$total = @$media->total;

						if ($total <= $limit) {
							// no pagination
						} else {
							$nr_pages = 0;
							if (intval($limit) > 0) {
								$nr_pages = ceil($total / $limit);
							}

							echo '<ul class="pagination-list">';
							for ($i = 1; $i <= $nr_pages; $i++) {
								$current_page = ($limitstart / $limit) + 1;

								if ($current_page == $i) {
									echo '<li class="active"><a>' . $i . '</a></li>';
								} else {
									echo '<li>';
									echo 	'<a href="#" onclick="document.adminForm.limitstart.value=' . (($i - 1) * $limit) . '; document.adminForm.active_pagination.value=1; document.adminForm.submit(); return false;">' . $i . '</a>';
									echo '</li>';
								}
							}
							echo '</ul>';
						}

						echo '<input type="hidden" name="limitstart" value="' . $limitstart . '" />';
						echo '<input type="hidden" name="active_pagination" value="0" />';
						?>
					</div>
				<?php
				} else {
				?>
					<div class="well well-minimized">
						<?php echo JText::_("GURU_SAVE_QUIZ_FIRST"); ?>
					</div>
				<?php
				}
				?>
			</div>

			<div class="tab-pane <?php echo $class_message; ?>" id="message">
				<?php
				//$editor = JFactory::getEditor();
				$editor  = new JEditor(JFactory::getConfig()->get("editor"));
				?>
				<table width="100%">
					<tr>
						<td valign="top" width="20%">
							<?php
							echo JText::_("GURU_PASS_MESSAGE");
							?>
						</td>
						<td>
							<?php
							echo $editor->display('pass_message', stripslashes($program->pass_message), '100%', '220px', '20', '50');
							?>
						</td>
					</tr>
				</table>

				<table width="100%" style="margin-top:20px;">
					<tr>
						<td valign="top" width="20%">
							<?php
							echo JText::_("GURU_FAIL_MESSAGE");
							?>
						</td>
						<td>
							<?php
							echo $editor->display('fail_message', stripslashes($program->fail_message), '100%', '220px', '20', '50');
							?>
						</td>
					</tr>
				</table>

				<table width="100%" style="margin-top:20px;">
					<tr>
						<td valign="top" width="20%">
							<?php
							echo JText::_("GURU_PENDING_MESSAGE");
							?>
						</td>
						<td>
							<?php
							echo $editor->display('pending_message', stripslashes($program->pending_message), '100%', '220px', '20', '50');
							?>
						</td>
					</tr>
				</table>
			</div>

			<div class="tab-pane <?php echo $class_publish; ?>" id="publish">
				<table class="adminform">
					<tr>
						<td width="15%">
							<?php echo JText::_('GURU_PRODLPBS'); ?>
						</td>
						<td width="85%">
							<?php echo $lists['published']; ?>
							<span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_QUIZ_PRODLPBS"); ?>">
								<img border="0" src="components/com_guru/images/icons/tooltip.png">
							</span>
						</td>
					</tr>
					<tr>
						<td valign="top" align="left">
							<?php echo JText::_('GURU_PRODLSPUB'); ?>
						</td>
						<td>
							<?php
							if ($program->id < 1) {
								$jnow 	= new JDate('now');
								$date 	= $jnow->toSQL();

								$start_publish =  date("" . $dateformat . "", strtotime($date));
							} else {
								$start_publish =  date("" . $dateformat . "", strtotime($program->startpublish));
							}

							echo JHTML::_('calendar', $start_publish, 'startpublish', 'startpublish', $format, array("showTime" => "", "todayBtn" => "", "weekNumbers" => "", "fillTable" => "", "singleHeader" => "")); // calendar change

							?>
							<span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_QUIZ_PRODLSPUB"); ?>">
								<img border="0" src="components/com_guru/images/icons/tooltip.png">
							</span>
						</td>
					</tr>
					<tr>
						<td valign="top" align="left">
							<?php echo JText::_('GURU_PRODLEPUB'); ?>
						</td>
						<td>
							<?php
							if (substr($program->endpublish, 0, 4) == '0000' || $program->endpublish == JText::_('GURU_NEVER') || $program->id < 1) $program->endpublish = "";
							else $program->endpublish = date("" . $dateformat . "", strtotime($program->endpublish));

							echo JHTML::_('calendar', $program->endpublish, 'endpublish', 'endpublish', $format, array("showTime" => "", "todayBtn" => "", "weekNumbers" => "", "fillTable" => "", "singleHeader" => "")); // calendar change

							?>

							<span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_QUIZ_PRODLEPUB"); ?>">
								<img border="0" src="components/com_guru/images/icons/tooltip.png">
							</span>
						</td>
					</tr>
				</table>
			</div>

			<?php
			$cid = $input->get("cid", array(), "raw");
			$v = $input->get("v", "", "raw");
			$e = $input->get("e", "", "raw");
			?>

			<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
			<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
			<?php echo JHtml::_('form.token'); ?>
			<input type="hidden" name="id" value="<?php echo @$cid["0"]; ?>" />
			<input type="hidden" name="task" value="edit" />
			<input type="hidden" name="valueop" value="<?php echo $value_option; ?>" />
			<input type="hidden" name="option" value="com_guru" />
			<input type="hidden" name="image" value="<?php if (isset($data_post['image'])) {
															echo $data_post['image'];
														} else {
															echo $program->image;
														} ?>" />
			<input type="hidden" name="controller" value="guruQuiz" />
			<input type="hidden" name="time_format" id="time_format" value="<?php echo $format; ?>" />
			<input type="hidden" name="cid[]" id="cid" value="<?php echo @$cid["0"]; ?>" />
			<input type="hidden" name="v" id="v" value="<?php echo $v; ?>" />
			<input type="hidden" name="e" id="e" value="<?php echo $e; ?>" />
	</form>

<?php } else {
?>
	<script language="javascript" type="text/javascript">
		function timeToStamp(string_date) {
			var form = document.adminForm;
			var time_format = form["time_format"].value;
			myDate = string_date.split(" ");
			myDate = myDate[0].split("-");

			if (myDate instanceof Array && myDate.length > 1) {} else {
				myDate = myDate[0].split("/");
			}
			var newDate = '';

			switch (time_format) {
				case "%m/%d/%Y %H:%M:%S":
					newDate = myDate[0] + "/" + myDate[1] + "/" + myDate[2];
					break;
				case "%Y-%m-%d %H:%M:%S":
					newDate = myDate[1] + "/" + myDate[2] + "/" + myDate[0];
					break;
				case "%d-%m-%Y":
					newDate = myDate[1] + "/" + myDate[0] + "/" + myDate[2];
					break;
				case "%m/%d/%Y":
					newDate = myDate[0] + "/" + myDate[1] + "/" + myDate[2];
					break;
				case "%Y-%m-%d":
					newDate = myDate[1] + "/" + myDate[0] + "/" + myDate[2];
					break;
			}

			return newDate;
		}

		function validDateTime(datetime) {
			var form = document.adminForm;
			var time_format = form["time_format"].value;

			if (datetime == "") {
				return false;
			}

			switch (time_format) {
				case "%m/%d/%Y %H:%M:%S":
					var date_regex = /^(0[1-9]|1[0-2])\/(0[1-9]|[1-2][0-9]|3[0-1])\/[0-9]{4} [0-9]{2}:[0-9]{2}:[0-9]{2}$/;
					if (!(date_regex.test(datetime))) {
						return false;
					}
					break;
				case "%Y-%m-%d %H:%M:%S":
					var date_regex = /^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1]) [0-9]{2}:[0-9]{2}:[0-9]{2}$/;
					if (!(date_regex.test(datetime))) {
						return false;
					}
					break;
				case "%d-%m-%Y":
					var date_regex = /^(0[1-9]|[1-2][0-9]|3[0-1])-(0[1-9]|1[0-2])-[0-9]{4}$/;
					if (!(date_regex.test(datetime))) {
						return false;
					}
					break;
				case "%m/%d/%Y":
					var date_regex = /^(0[1-9]|1[0-2])\/(0[1-9]|[1-2][0-9]|3[0-1])\/[0-9]{4}$/;
					if (!(date_regex.test(datetime))) {
						return false;
					}
					break;
				case "%Y-%m-%d":
					var date_regex = /^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/;
					if (!(date_regex.test(datetime))) {
						return false;
					}
					break;
			}
			return true;
		}

		function isFloat(nr) {
			return parseFloat(nr.match(/^-?\d*(\.\d+)?$/)) > 0;
		}

		Joomla.submitbutton = function(pressbutton) {
			var form = document.adminForm;
			if (pressbutton == 'save' || pressbutton == 'apply') {
				if (form['name'].value == "") {
					alert("<?php echo JText::_("GURU_TASKS_JS_NAME"); ?>");
				}

				if (isNaN(parseInt(form['author'].value))) {
					alert("<?php echo JText::_("GURU_CS_QAUTHOR"); ?>");
				} else {
					start_date = form['startpublish'].value;
					end_date = form['endpublish'].value;

					if (end_date != "Never" && end_date != "") {
						if (!validDateTime(start_date) || !validDateTime(end_date)) {
							alert("<?php echo JText::_("GURU_INVALID_DATE"); ?>");
							return false;
						}
					}

					if (form['endpublish'].value != "Never" && form['endpublish'].value != "") {
						start_date = new Date(timeToStamp(start_date)).getTime();
						end_date = new Date(timeToStamp(end_date)).getTime();

						if (Date.parse(start_date) > Date.parse(end_date)) {
							alert("<?php echo JText::_("GURU_DATE_GRATER"); ?>");
							return false;
						}
					}

					max_score_pass = document.getElementById("max_score_pass").value;
					limit_time_l = document.getElementById("limit_time_l").value;
					limit_time_f = document.getElementById("limit_time_f").value;

					if (!isFloat(max_score_pass) || max_score_pass <= 0) {
						alert("<?php echo JText::_("GURU_INVALID_NUMBER_AVG"); ?>");
						return false;
					}

					submitform(pressbutton);
				}
			} else {
				submitform(pressbutton);
			}
		}
	</script>
	<form action="<?php echo JUri::root() ?>administrator/index.php" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">
		<div class="well">
			<?php
			if ($program->id < 1) {
				echo JText::_('GURU_FINAL_EXAM_QUIZ1') . ": [" . JText::_('GURU_NEW') . "]";
			} else {
				echo JText::_('GURU_FINAL_EXAM_QUIZ1') . ": [" . JText::_('GURU_EDIT') . "]";
			}
			?>
		</div>

		<ul class="nav nav-tabs">
			<li class="active nav-item"><a class="nav-link active" href="#general1" data-toggle="tab"><?php echo JText::_('GURU_GENERAL'); ?></a></li>
			<li class="nav-item"><a class="nav-link" href="#quizzesincl" data-toggle="tab"><?php echo JText::_('GURU_QUIZZES_INCLUDED'); ?></a></li>
			<li class="nav-item"><a class="nav-link" href="#message2" data-toggle="tab"><?php echo JText::_('GURU_PASS_FAIL_MESSAGE'); ?></a></li>
			<li class="nav-item"><a class="nav-link" href="#publish2" data-toggle="tab"><?php echo JText::_('GURU_PUBLISHING'); ?></a></li>
		</ul>
		<div class="tab-content">
			<div class="tab-pane active" id="general1">
				<table class="">
					<tr>
						<td width="15%">
							<?php echo JText::_('GURU_NAME'); ?>:<font color="#ff0000">*</font>
						</td>
						<td>
							<input class="inputbox" type="text" id="name" name="name" size="40" maxlength="255" value="<?php echo $program->name; ?>" />
							<span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_QUIZ_NAME"); ?>">
								<img border="0" src="components/com_guru/images/icons/tooltip.png">
							</span>
						</td>
					</tr>
					<tr>
						<td>
							<?php echo JText::_('GURU_AUTHOR'); ?>:<font color="#ff0000">*</font>
						</td>
						<td>
							<?php echo $lists['author']; ?>
							<span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_AUTHOR_Q"); ?>">
								<img border="0" src="components/com_guru/images/icons/tooltip.png">
							</span>
						</td>
					</tr>
					<tr>
						<td>
							<?php echo JText::_('GURU_PRODDESC'); ?>:
						</td>
						<td>
							<?php //echo $editorul->display( 'description', ''.stripslashes($program->description),'100%', '300px', '20', '60' );
							?>
							<textarea name="description" id="description" cols="40" rows="8"><?php echo stripslashes($program->description); ?></textarea>
							<span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_QUIZ_PRODDESC"); ?>">
								<img border="0" src="components/com_guru/images/icons/tooltip.png">
							</span>
						</td>
					</tr>
					<tr>
						<td>
							<?php echo JText::_('GURU_SHOW_CORRECT_ANS'); ?>:
						</td>
						<td>
							<fieldset class="radio btn-group" id="show_correct_ans">
								<?php
								$no_checked = "";
								$yes_cheched = "";

								if ($program->show_correct_ans == "0") {
									$no_checked = 'checked="checked"';
								} else {
									$yes_cheched = 'checked="checked"';
								}
								?>
								<input type="hidden" name="show_correct_ans" value="0">
								<label for="checkbox2_show_correct_ans" class="" style="display: none;"></label>
								<input type="checkbox" <?php echo $yes_cheched; ?> value="1" id="checkbox2_show_correct_ans" name="show_correct_ans" class="ace-switch ace-switch-5">
								<span class="lbl"></span>
							</fieldset>
							<span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_SHOW_CORRECT_ANS"); ?>">
								<img border="0" src="components/com_guru/images/icons/tooltip.png">
							</span>
						</td>
					</tr>
					<tr>
						<td width="15%">
							<?php echo JText::_('GURU_MINIMUM_SCORE_FINAL_QUIZ'); ?>:
						</td>
						<td>
							<?php if (isset($program->max_score)) {
								$program->max_score = $program->max_score;
							} else {
								$program->max_score = 70;
							}



							?>
							<input class="inputbox" type="text" id="max_score_pass" name="max_score_pass" size="6" value="<?php echo $program->max_score; ?>" style="float:left !important;" />&nbsp;
							<span style="float:left !important; padding-top:4px; padding-left:20px;">%&nbsp;&nbsp;</span>

							<select id="show_max_score_pass" name="show_max_score_pass" style="float:left !important;">
								<option value="0" <?php if ($program->pbl_max_score == "0") {
														echo 'selected="selected"';
													} ?>><?php echo JText::_("GURU_SHOW"); ?></option>
								<option value="1" <?php if ($program->pbl_max_score == "1") {
														echo 'selected="selected"';
													} ?>><?php echo JText::_("GURU_HIDE"); ?></option>
							</select>
							<span class="editlinktip hasTip" title="<?php echo JText::_('GURU_MAX_SCORE_TOOLTIP'); ?>">
								<img border="0" src="components/com_guru/images/icons/tooltip.png">
							</span>
						</td>
					</tr>
					<tr>
						<td width="15%">
							<?php echo JText::_('GURU_QUIZ_CAN_BE_TAKEN'); ?>:
						</td>
						<td>
							<select id="nb_quiz_taken" name="nb_quiz_taken" style="float:left !important;">
								<option value="1" <?php if ($program->time_quiz_taken == "1") {
														echo 'selected="selected"';
													} ?>>1</option>
								<option value="2" <?php if ($program->time_quiz_taken == "2") {
														echo 'selected="selected"';
													} ?>>2</option>
								<option value="3" <?php if ($program->time_quiz_taken == "3") {
														echo 'selected="selected"';
													} ?>>3</option>
								<option value="4" <?php if ($program->time_quiz_taken == "4") {
														echo 'selected="selected"';
													} ?>>4</option>
								<option value="5" <?php if ($program->time_quiz_taken == "5") {
														echo 'selected="selected"';
													} ?>>5</option>
								<option value="6" <?php if ($program->time_quiz_taken == "6") {
														echo 'selected="selected"';
													} ?>>6</option>
								<option value="7" <?php if ($program->time_quiz_taken == "7") {
														echo 'selected="selected"';
													} ?>>7</option>
								<option value="8" <?php if ($program->time_quiz_taken == "8") {
														echo 'selected="selected"';
													} ?>>8</option>
								<option value="9" <?php if ($program->time_quiz_taken == "9") {
														echo 'selected="selected"';
													} ?>>9</option>
								<option value="10" <?php if ($program->time_quiz_taken == "10") {
														echo 'selected="selected"';
													} ?>>10</option>
								<option value="20" <?php if ($program->time_quiz_taken == "20") {
														echo 'selected="selected"';
													} ?>>20</option>
								<option value="30" <?php if ($program->time_quiz_taken == "30") {
														echo 'selected="selected"';
													} ?>>30</option>
								<option value="40" <?php if ($program->time_quiz_taken == "40") {
														echo 'selected="selected"';
													} ?>>40</option>
								<option value="50" <?php if ($program->time_quiz_taken == "50") {
														echo 'selected="selected"';
													} ?>>50</option>
								<option value="11" <?php if ($program->time_quiz_taken == "11") {
														echo 'selected="selected"';
													} ?>><?php echo JText::_("GURU_UNLIMPROMO"); ?></option>
							</select>

							<span style="float:left !important;padding-top:4px;padding-left:2px;">&nbsp;<?php echo JText::_("GURU_TIMES_T"); ?>&nbsp;</span>
							<select id="show_nb_quiz_taken" name="show_nb_quiz_taken" style="float:left !important;">
								<option value="0" <?php if ($program->show_nb_quiz_taken == "0") {
														echo 'selected="selected"';
													} ?>><?php echo JText::_("GURU_SHOW"); ?></option>
								<option value="1" <?php if ($program->show_nb_quiz_taken == "1") {
														echo 'selected="selected"';
													} ?>><?php echo JText::_("GURU_HIDE"); ?></option>
							</select>&nbsp;
							<span class="editlinktip hasTip" title="<?php echo JText::_('GURU_TIMES_TAKEN_TOOLTIP'); ?>">
								<img border="0" src="components/com_guru/images/icons/tooltip.png">
							</span>
						</td>
					</tr>
					<tr>
						<td width="15%">
							<?php echo JText::_('GURU_SELECT_UP_TO'); ?>:
						</td>
						<td style="padding-top:5px">
							<select id="nb_quiz_select_up" name="nb_quiz_select_up" style="float:left !important;">
								<?php
								if (isset($program->nb_quiz_select_up)) {
									$program->nb_quiz_select_up = $program->nb_quiz_select_up;
								} else {
									$program->nb_quiz_select_up = 10;
								}

								for ($i = 1; $i <= 100; $i++) { ?>
									<option value="<?php echo $i; ?>" <?php if ($program->nb_quiz_select_up == $i) {
																			echo 'selected="selected"';
																		} ?>><?php echo $i; ?></option>
								<?php
								}
								?>
							</select>

							<span style="float:left !important; padding-top:4px; padding-left:2px;">&nbsp;<?php echo JText::_('GURU_QUESTION_RANDOM'); ?>&nbsp;</span>
							<select id="show_nb_quiz_select_up" name="show_nb_quiz_select_up" style="float:left !important;">
								<option value="0" <?php if ($program->show_nb_quiz_select_up == "0") {
														echo 'selected="selected"';
													} ?>><?php echo JText::_("GURU_SHOW"); ?></option>
								<option value="1" <?php if ($program->show_nb_quiz_select_up == "1") {
														echo 'selected="selected"';
													} ?>><?php echo JText::_("GURU_HIDE"); ?></option>
							</select>&nbsp;
							<span class="editlinktip hasTip" title="<?php echo JText::_('GURU_QUESTIONS_NUMBER_TOOLTIP'); ?>">
								<img border="0" src="components/com_guru/images/icons/tooltip.png">
							</span>
						</td>
					</tr>
					<tr>
						<td width="15%">
							<?php echo JText::_('GURU_QUESTIONS_PER_PAGE'); ?>:
						</td>
						<td style="padding-top:5px">
							<?php
							$questions_per_page = $program->questions_per_page;
							?>
							<select name="questions_per_page">
								<option value="5" <?php if ($questions_per_page == "5") {
														echo 'selected="selected"';
													} ?>>5</option>
								<option value="10" <?php if ($questions_per_page == "10") {
														echo 'selected="selected"';
													} ?>>10</option>
								<option value="15" <?php if ($questions_per_page == "15") {
														echo 'selected="selected"';
													} ?>>15</option>
								<option value="20" <?php if ($questions_per_page == "20") {
														echo 'selected="selected"';
													} ?>>20</option>
								<option value="25" <?php if ($questions_per_page == "25") {
														echo 'selected="selected"';
													} ?>>25</option>
								<option value="30" <?php if ($questions_per_page == "30") {
														echo 'selected="selected"';
													} ?>>30</option>
								<option value="50" <?php if ($questions_per_page == "50") {
														echo 'selected="selected"';
													} ?>>50</option>
								<option value="100" <?php if ($questions_per_page == "100") {
														echo 'selected="selected"';
													} ?>>100</option>
								<option value="0" <?php if ($questions_per_page == "0") {
														echo 'selected="selected"';
													} ?>>All</option>
							</select>
							&nbsp;
							<span class="editlinktip hasTip" title="<?php echo JText::_('GURU_QUESTIONS_PER_PAGE_TOOLTIP'); ?>">
								<img border="0" src="components/com_guru/images/icons/tooltip.png">
							</span>
						</td>
					</tr>
					<tr>
						<td style=" font-weight:bold; font-size:25px"><?php echo JText::_("GURU_TIMER"); ?>:</td>
					</tr>
					<tr>
						<td width="15%">
							<?php echo JText::_('GURU_EXAM_LIMIT_TIME'); ?>:
						</td>
						<td style="padding-top:5px">
							<?php if (isset($program->limit_time)) {
								$program->limit_time = $program->limit_time;
							} else {
								$program->limit_time = 3;
							}



							?>
							<select id="limit_time_l" name="limit_time_l" class="pull-left" style="margin-right:10px;">
								<option value="0"> <?php echo JText::_("GURU_UNLIMPROMO"); ?> </option>
								<?php
								for ($i = 1; $i <= 150; $i++) {
									$selected = "";
									$time = JText::_("GURU_MINUTES");

									if ($i == $program->limit_time) {
										$selected = 'selected="selected"';
									}

									if ($i == 1) {
										$time = JText::_("GURU_MINUTE");
									}

									echo '<option value="' . $i . '" ' . $selected . '>' . $i . " " . $time . '</option>';
								}
								?>
							</select>

							<select id="show_limit_time" name="show_limit_time" style="float:left !important;">
								<option value="0" <?php if ($program->show_limit_time == "0") {
														echo 'selected="selected"';
													} ?>><?php echo JText::_("GURU_SHOW"); ?></option>
								<option value="1" <?php if ($program->show_limit_time == "1") {
														echo 'selected="selected"';
													} ?>><?php echo JText::_("GURU_HIDE"); ?></option>
							</select>&nbsp;
							<span class="editlinktip hasTip" title="<?php echo JText::_('GURU_QUIZ_LIMIT_TIME_TOOLTIP'); ?>">
								<img border="0" src="components/com_guru/images/icons/tooltip.png">
							</span>
						</td>
					</tr>
					<tr>
						<td width="15%">
							<?php echo JText::_('GURU_SHOW_COUNTDOWN'); ?>:
						</td>
						<td style="padding-top:9px">
							<select id="show_countdown" name="show_countdown" style="float:left !important;">
								<option value="0" <?php if ($program->show_countdown == "0") {
														echo 'selected="selected"';
													} ?>><?php echo JText::_("GURU_SHOW"); ?></option>
								<option value="1" <?php if ($program->show_countdown == "1") {
														echo 'selected="selected"';
													} ?>><?php echo JText::_("GURU_HIDE"); ?></option>
							</select>&nbsp;
							<span class="editlinktip hasTip" title="<?php echo JText::_('GURU_SHOW_COUNTDOWN_TOOLTIP'); ?>">
								<img border="0" src="components/com_guru/images/icons/tooltip.png">
							</span>
						</td>
					</tr>
					<tr>
						<td width="15%">
							<?php echo JText::_('GURU_FINISH_ALERT'); ?>:
						</td>
						<td style="padding-top:5px">
							<?php if (isset($program->limit_time_f)) {
								$program->limit_time_f = $program->limit_time_f;
							} else {
								$program->limit_time_f = 1;
							}



							?>
							<select id="limit_time_f" name="limit_time_f" class="pull-left">
								<option value="2" <?php if ($program->limit_time_f == "2") {
														echo 'selected="selected"';
													} ?>> 2 <?php echo JText::_("GURU_MINUTES"); ?> </option>
								<option value="1" <?php if ($program->limit_time_f == "1") {
														echo 'selected="selected"';
													} ?>> 1 <?php echo JText::_("GURU_MINUTE"); ?> </option>
								<option value="30" <?php if ($program->limit_time_f == "30") {
														echo 'selected="selected"';
													} ?>> 30 <?php echo JText::_("GURU_SECONDS"); ?> </option>
								<option value="15" <?php if ($program->limit_time_f == "15") {
														echo 'selected="selected"';
													} ?>> 15 <?php echo JText::_("GURU_SECONDS"); ?> </option>
							</select>

							<span style="float:left !important; padding-top:4px; padding-left:2px;">&nbsp;<?php echo JText::_('GURU_MINUTES_BEFORE_IS_UP'); ?>&nbsp;</span>

							<select id="show_finish_alert" name="show_finish_alert" style="float:left !important;">
								<option value="0" <?php if ($program->show_finish_alert == "0") {
														echo 'selected="selected"';
													} ?>><?php echo JText::_("GURU_SHOW"); ?></option>
								<option value="1" <?php if ($program->show_finish_alert == "1") {
														echo 'selected="selected"';
													} ?>><?php echo JText::_("GURU_HIDE"); ?></option>
							</select>&nbsp;
							<span class="editlinktip hasTip" title="<?php echo JText::_('GURU_FINISH_ALERT_TOOLTIP'); ?>">
								<img border="0" src="components/com_guru/images/icons/tooltip.png">
							</span>
						</td>
					</tr>

					<tr>
						<td width="15%">
							<?php echo JText::_('GURU_RESET_TIMER'); ?>:
						</td>
						<td>
							<fieldset class="radio btn-group" id="reset_time">
								<?php
								$no_checked = "";
								$yes_cheched = "";

								if ($program->reset_time == "0") {
									$no_checked = 'checked="checked"';
								} else {
									$yes_cheched = 'checked="checked"';
								}
								?>
								<input type="hidden" name="reset_time" value="0">
								<label for="reset_time" class="" style="display: none;"></label>
								<input type="checkbox" <?php echo $yes_cheched; ?> value="1" id="reset_time" name="reset_time" class="ace-switch ace-switch-5">
								<span class="lbl"></span>
							</fieldset>

							<span class="editlinktip hasTip" title="<?php echo JText::_('GURU_RESET_TIMER_TOOLTIP'); ?>">
								<img border="0" src="components/com_guru/images/icons/tooltip.png">
							</span>
						</td>
					</tr>

				</table>
			</div>
			<div class="tab-pane" id="quizzesincl">
				<?php if ($cid): ?>
					<table class="table">
						<tr>
							<td>
								<div>
									<div style="float:left;">
										<a rel="{handler: 'iframe', size: {x: 800, y: 550}}" href="index.php?option=com_guru&controller=guruQuiz&task=addquizzes&tmpl=component&cid[]=<?php echo $program->id; ?>" class="modal" style="padding: 5px !important;"><?php echo JText::_("GURU_ADD_QUIZZES"); ?></a>&nbsp;
									</div>
									<div style="float:left;">
										<span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_QUIZZES"); ?>">
											<img border="0" src="components/com_guru/images/icons/tooltip.png">
										</span>
									</div>
								</div>
								<br /><br />
								<?php
								$db = JFactory::getDBO();
								$data_request = $input->request->getArray();

								$sql = "SELECT quizzes_ids FROM #__guru_quizzes_final WHERE qid = " . intval(@$data_request["cid"]["0"]);
								$db->setQuery($sql);
								$result = $db->loadAssocList();

								$listofids = array();
								foreach ($result as $value) {
									//$result_ids = explode(",",trim($value['quizzes_ids']));
									$listofids = array_merge($listofids, (array)$value["quizzes_ids"]);
								}
								$listofids = implode(",", array_unique($listofids));
								$listofids = str_replace(",,", ",", $listofids);
								$listofids = "0" . $listofids;



								$sql = "SELECT id, name, published FROM #__guru_quiz WHERE id IN (" . $listofids . ")";
								$db->setQuery($sql);
								$result_name = $db->loadAssocList();

								?>
								<table cellspacing="1" cellpadding="5" border="0" bgcolor="#cccccc" width="100%">
									<tbody id="rowquestion" <?php if (!isset($result_name)) {
																echo 'style="display: none;"';
															} ?>>
										<tr>
											<td width="42%">
												<strong><?php echo JText::_('GURU_TREEQUIZ'); ?></strong>
											</td>
											<td width="17%">
												<strong><?php echo JText::_('GURU_REMOVE'); ?></strong>
											</td>
											<td width="12%">
												<!-- <strong><?php //echo JText::_('Edit');
																?></strong>-->
											</td>
											<td width="14%">
												<strong><?php echo JText::_('GURU_PUBLISHED'); ?></strong>
											</td>
										</tr>
										<?php
										if (isset($data_post['deleteq'])) {
											$hide_q2del = $data_post['deleteq'];
										} else {
											$hide_q2del = ',';
										}
										$hide_q2del = explode(',', $hide_q2del);

										for ($i = 0; $i < count($result_name); $i++) {
											$link2_remove = '<font color="#FF0000"><span style="cursor: pointer;" onClick="delete_fq(' . $result_name[$i]["id"] . ',' . intval(@$data_request["cid"]["0"]) . ', 1)">Remove</span></font>';
											$sql = "SELECT 	published FROM #__guru_quizzes_final WHERE qid=" . $result_name[$i]["id"];
											$db->setQuery($sql);
											$published = $db->loadColumn();
										?>

											<tr id="trfque<?php echo $result_name[$i]["id"]; ?>" <?php if (in_array($result_name[$i]["id"], $hide_q2del)) { ?> style="display:none" <?php } ?>>
												<td width="42%">
													<strong><?php echo $result_name[$i]["name"]; ?></strong>
												</td>
												<td width="17%">
													<?php echo $link2_remove;  ?>
												</td>
												<td width="12%">
													<?php if (@$published["0"] == 1) {
														echo "<input type='hidden' id='publ" . $result_name[$i]["id"] . "' name='publish_q[" . $result_name[$i]["id"] . "]' value='1' />";
													} else {
														echo "<input type='hidden' id='publ" . $result_name[$i]["id"] . "' name='publish_q[" . $result_name[$i]["id"] . "]' value='0' />";
													} ?>
												</td>
												<td width="14%" id="publishing<?php echo $result_name[$i]["id"]; ?>">
													<?php
													if ($result_name[$i]["published"] == 1) {
														echo '<a class="icon-ok" style="cursor: pointer; text-decoration: none;" onclick="javascript:unpublish(\'' . $result_name[$i]["id"] . '\');"></a>';
													} else {
														echo '<a class="icon-remove" style="cursor: pointer; text-decoration: none;" onclick="javascript:publish(\'' . $result_name[$i]["id"] . '\');"></a>';
													}
													?>
												</td>
											</tr>
										<?php } ?>


										<input type="hidden" value="<?php if (isset($data_post['newquizq'])) echo $data_post['newquizq']; ?>" id="newquizq" name="newquizq">
										<input type="hidden" value="<?php if (isset($data_post['deleteq'])) echo $data_post['deleteq']; ?>" id="deleteq" name="deleteq">
									</tbody>
								</table>
							</td>
						</tr>
					</table>
				<?php else: ?>
					<div class="well well-minimized">
						<?php echo JText::_("GURU_SAVE_QUIZ_FIRST"); ?>
					</div>
				<?php endif ?>
			</div>

			<div class="tab-pane" id="message2">
				<?php
				//$editor = JFactory::getEditor();
				$editor  = new JEditor(JFactory::getConfig()->get("editor"));
				?>
				<table width="100%">
					<tr>
						<td valign="top" width="20%">
							<?php
							echo JText::_("GURU_PASS_MESSAGE");
							?>
						</td>
						<td>
							<?php
							echo $editor->display('pass_message', stripslashes($program->pass_message), '100%', '220px', '20', '50');
							?>
						</td>
					</tr>
				</table>

				<table width="100%" style="margin-top:20px;">
					<tr>
						<td valign="top" width="20%">
							<?php
							echo JText::_("GURU_FAIL_MESSAGE");
							?>
						</td>
						<td>
							<?php
							echo $editor->display('fail_message', stripslashes($program->fail_message), '100%', '220px', '20', '50');
							?>
						</td>
					</tr>
				</table>

				<table width="100%" style="margin-top:20px;">
					<tr>
						<td valign="top" width="20%">
							<?php
							echo JText::_("GURU_PENDING_MESSAGE");
							?>
						</td>
						<td>
							<?php
							echo $editor->display('pending_message', stripslashes($program->pending_message), '100%', '220px', '20', '50');
							?>
						</td>
					</tr>
				</table>
			</div>

			<div class="tab-pane" id="publish2">
				<table class="adminform">
					<tr>
						<td width="15%">
							<?php echo JText::_('GURU_PRODLPBS'); ?>
						</td>
						<td width="85%">
							<?php echo $lists['published']; ?>
							<span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_QUIZ_PRODLPBS"); ?>">
								<img border="0" src="components/com_guru/images/icons/tooltip.png">
							</span>
						</td>
					</tr>
					<tr>
						<td valign="top" align="left">
							<?php echo JText::_('GURU_PRODLSPUB'); ?>
						</td>
						<td>
							<?php
							if ($program->id < 1) {
								$jnow 	= new JDate('now');
								$date 	= $jnow->toSQL();

								$start_publish =  date("" . $dateformat . "", strtotime($date));
							} else {
								$start_publish =  date("" . $dateformat . "", strtotime($program->startpublish));
							}

							echo JHTML::_('calendar', $start_publish, 'startpublish', 'startpublish', $format, array("showTime" => "", "todayBtn" => "", "weekNumbers" => "", "fillTable" => "", "singleHeader" => "")); // calendar change

							?>
							<span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_QUIZ_PRODLSPUB"); ?>">
								<img border="0" src="components/com_guru/images/icons/tooltip.png">
							</span>
						</td>
					</tr>
					<tr>
						<td valign="top" align="left">
							<?php echo JText::_('GURU_PRODLEPUB'); ?>
						</td>
						<td>
							<?php
							if (substr($program->endpublish, 0, 4) == '0000' || $program->endpublish == JText::_('GURU_NEVER') || $program->id < 1) $program->endpublish = "";
							else $program->endpublish = date("" . $dateformat . "", strtotime($program->endpublish));

							echo JHTML::_('calendar', $program->endpublish, 'endpublish', 'endpublish', $format, array("showTime" => "", "todayBtn" => "", "weekNumbers" => "", "fillTable" => "", "singleHeader" => "")); // calendar change

							?>
							<span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_QUIZ_PRODLEPUB"); ?>">
								<img border="0" src="components/com_guru/images/icons/tooltip.png">
							</span>
						</td>
					</tr>

				</table>
			</div>

			<?php
			$cid = $input->get("cid", array(), "raw");
			$v = $input->get("v", "");
			$e = $input->get("e", "");
			?>

			<input type="hidden" name="id" value="<?php echo @$cid["0"]; ?>" />
			<input type="hidden" name="task" value="edit" />
			<input type="hidden" name="valueop" value="<?php echo $value_option; ?>" />
			<input type="hidden" name="option" value="com_guru" />
			<input type="hidden" name="image" value="<?php if (isset($data_post['image'])) {
															echo $data_post['image'];
														} else {
															echo $program->image;
														} ?>" />
			<input type="hidden" name="controller" value="guruQuiz" />
			<input type="hidden" name="time_format" id="time_format" value="<?php echo $format; ?>" />
			<input type="hidden" name="cid" id="cid" value="<?php echo @$cid["0"]; ?>" />
			<input type="hidden" name="v" id="v" value="<?php echo $v; ?>" />
			<input type="hidden" name="e" id="e" value="<?php echo $e; ?>" />
			<input type="hidden" value="" id="new_quiz_added" name="new_quiz_added" />
	</form>
<?php } ?>