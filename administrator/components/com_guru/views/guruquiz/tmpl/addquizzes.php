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
$doc = JFactory::getDocument();

$list_quizzes = $this->list_quizzes;
$n = count($list_quizzes);

$input = JFactory::getApplication()->input;
$data_post = $input->post->getArray();
$data_get = $input->get->getArray();
$cid = $input->get('cid');
$cid = @$cid[0];

$db = JFactory::getDbo();
$query = "SELECT quizzes_ids FROM `#__guru_quizzes_final` WHERE qid = " . $db->q($cid);
$finalQuizzes = $db->setQuery($query)->loadResult();
$finalQuizzes = explode(',', $finalQuizzes);

$query = "SELECT `name` FROM `#__guru_quiz` WHERE id = " . $db->q($cid);
$quiz_name = $db->setQuery($query)->loadResult();

?>
<script type="text/javascript" language="javascript">
	function savequizzes() {
		var chks = document.getElementsByName('cb[]');
		var quizzIds = [];
		for (var i = 0; i < chks.length; i++) {
			if (chks[i].checked) {
				quizzIds.push(chks[i].value);
			}
		}

		if (quizzIds.length) {
			document.getElementById('quizzes_ids').value = quizzIds.join(',');
			document.adminForm.submit();
		} else {
			alert("Please select at least one quiz.");
			return false;
		}
	}
</script>


<h2 class="g_modal_title"><?php echo JText::_("GURU_ADD_QUIZZES_TO_FINAL_EXAM") . " " . $quiz_name; ?></h2>
<form class="g_modal_search" name="form1" action="<?php echo JUri::root(true) ?>/administrator/index.php?option=com_guru&controller=guruQuiz&task=addquizzes&no_html=1&cid[]=<?php echo $data_get['cid'][0]; ?>&tmpl=<?php echo JFactory::getApplication()->input->get("tmpl", ""); ?>" method="post">
	<table style="padding-left:2px; font-size:11px;">
		<td>
			<input type="text" name="search_text" style="height:18px; margin-bottom:0px !important;" value="<?php if (isset($data_post['search_text'])) echo $data_post['search_text']; ?>" />
			<input type="submit" class="btn" name="submit_search" style="height:26px;" value="<?php echo JText::_('GURU_SEARCHTXT'); ?>" />
		</td>
	</table>
</form>
<input type="hidden" value="search_text" name="search_text" />
<form method="post" name="adminForm" id="adminForm" action="<?php JRoute::_('index.php?option=com_guru&controller=guruQuiz&task=addquizzes&tmpl=component&cid[]=' . $cid) ?>">

	<div id="editcell">
		<br />

		<table class="table table-striped">
			<th></th>
			<th><?php echo JText::_("GURU_ID"); ?></th>
			<th><?php echo JText::_("VIEWPLUGTITLE"); ?></th>
			<?php $k = 0; ?>
			<?php foreach ($list_quizzes as $quiz): ?>
				<tr class="row<?php echo $k; ?>">
					<td>
						<input 
							type="checkbox" 
							name="cb[]" 
							id="cb[]" 
							value="<?php echo $quiz["id"]; ?>"
							<?php echo in_array($quiz['id'], $finalQuizzes) ? 'checked' : '' ?>>
						<span class="lbl"></span>
					</td>
					<td><?php echo $quiz["id"]; ?></td>
					<td><?php echo $quiz["name"]; ?></td>
				</tr>
			<?php $k = 1 - $k ?>
			<?php endforeach ?>
		</table>
	</div>
	<br />
	<div style="margin-left:3px;">
		<input type="button" class="btn" onclick="savequizzes();" value="<?php echo JText::_("GURU_SAVE_PROGRAM_BTN"); ?>">
	</div>
	<input type="hidden" value="com_guru" name="option" />
	<input type="hidden" value="savequizzes" name="task" />
	<input type="hidden" value="<?php echo intval($data_get['cid'][0]); ?>" name="quizid" />
	<input id="quizzes_ids" name="quizzes_ids" type="hidden" value="" />
	<input type="hidden" value="guruQuiz" name="controller" />
	<input type="hidden" value="<?php echo $input->get("tmpl", ""); ?>" name="tmpl" />
</form>