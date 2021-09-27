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
JHTML::_('behavior.tooltip');	
JHTML::_('behavior.modal');
JHTML::_('behavior.framework');

$program = $this->program;
$lists = $program->lists;
$max_reo = $this->max_reo;
$min_reo = $this->min_reo;
if($program->id == ""){
	$program->id = 0;
}
$div_menu = $this->authorGuruMenuBar();

$mmediam = $this->mmediam;
foreach($mmediam as $mmm){
	$vector[] = $mmm->id;
}

$value_optiono = JFactory::getApplication()->input->get("v", "0");
if($program->is_final == 0){
	$value_option = 0;
}
else{
	$value_option = 1;
}
if($program->id == 0){
	$value_option = $value_optiono;
}
$mainmedia = $this->mainmedia;
$configuration = $this->getConfigsObject();
$amount_quest = $this->getAmountQuestions($program->id);
$amount_quest_quizzes = $this->getAmountQuizzes($program->id);

$listDirn = "asc";
$listOrder = "ordering";
$saveOrderingUrl = JUri::root() . 'index.php?option=com_guru&controller=guruAuthor&task=saveOrderQuestions&tmpl=component';
JHtml::_('sortablelist.sortable', 'articleList', 'adminForm', strtolower($listDirn), $saveOrderingUrl);

$db = JFactory::getDBO();
$request_final = "";
if($program->is_final){
	$request_final = "&v=1";
}
$doc = JFactory::getDocument();
$doc->setTitle(trim(JText::_('GURU_AUTHOR'))." ".trim(JText::_('GURU_AUTHOR_MY_QUIZZES')));

require_once(JPATH_BASE . "/components/com_guru/helpers/Mobile_Detect.php");
$detect = new Mobile_Detect;
$deviceType = ($detect->isMobile() ? ($detect->isTablet() ? 'tablet' : 'phone') : 'computer');
//$doc->addScript('components/com_guru/js/jquery-dropdown.js');
?>

<script type="text/javascript" language="javascript">
	document.body.className = document.body.className.replace("modal", "");
</script>

<script language="javascript" type="text/javascript">
	function savequiz(pressbutton){
		var form = document.adminForm;
		if (pressbutton == 'save_quizFE' || pressbutton == 'apply_quizFE'){
			if(form['name'].value == ""){
				alert( "<?php echo JText::_("GURU_TASKS_JS_NAME");?>" );
			}
			else{
				//submitform( pressbutton );
				form.task.value = pressbutton;
				form.submit();
			}
		}
		else {
			//submitform( pressbutton );
			form.task.value = pressbutton;
            form.submit();
		}
	}

	function publish(x){
		var req = jQuery.ajax({
			method: 'get',
			url: '<?php echo JURI::root(); ?>index.php?option=com_guru&controller=guruAuthor&tmpl=component&format=raw&task=publish_quiz_ajax&id='+x+'&action=publish<?php echo $request_final; ?>',
			data: { 'do' : '1' },
			success: function(response){
				document.getElementById('publishing'+x).innerHTML='<a class="icon-ok" style="cursor: pointer; text-decoration: none;" onclick="javascript:unpublish(\''+x+'\');"></a>';
			}
		});
	}
	
	function unpublish(x){
		var req = jQuery.ajax({
			method: 'get',
			url: '<?php echo JURI::root(); ?>index.php?option=com_guru&controller=guruAuthor&tmpl=component&format=raw&task=unpublish_quiz_ajax&id='+x+'&action=unpublish<?php echo $request_final; ?>',
			data: { 'do' : '1' },
			success: function(response){
				document.getElementById('publishing'+x).innerHTML='<a class="icon-remove" style="cursor: pointer; text-decoration: none;" onclick="javascript:publish(\''+x+'\');"></a>';
			}
		});
	}
	
	function showContentQuestion(href){
			jQuery.ajax({
			  url: href,
			  success: function(response){
			   jQuery( '#myModal .modal-body').html(response);
			  }
			});
		}
</script>
<script>
	function delete_temp_m(i){
		document.getElementById('trm'+i).style.display = 'none';
		document.getElementById('mediafiletodel').value =  document.getElementById('mediafiletodel').value+','+i;
	}
	function delete_q(i,id,t){
		var ok = confirm('<?php echo JText::_('GURU_QUIZ_QUESTION_DELETE_CONFIRM') ?>')
		if (!ok) {
			return;
		}

		var deleted = i;
		var url = '<?php echo JURI::root(); ?>index.php?option=com_guru&controller=guruAuthor&tmpl=component&format=raw&task=delete_quiz_ajax&id='+id+'&f='+t+'&deleted='+deleted;	
		
		jQuery.ajax({
			async: false,
			url: url,
			success: function(response) {
		        document.getElementById('trque'+i).style.display = 'none';
			}
		});
	}
	
	function delete_fq(i, id,t){
		var ok = confirm('<?php echo JText::_('GURU_QUIZ_QUESTION_DELETE_CONFIRM') ?>')
		if (!ok) {
			return;
		}

		var deleted = i;
		var url = '<?php echo JURI::root(); ?>index.php?option=com_guru&controller=guruAuthor&tmpl=component&format=raw&task=delete_quiz_ajax&id='+id+'&f='+t+'&deleted='+deleted;
		
		jQuery.ajax({
			async: false,
			url: url,
			success: function(response) {
		        all_quizes_nr = document.getElementById("all_quizes_nr").value;
				document.getElementById("all_quizes_nr").value = (all_quizes_nr - 1);
				document.getElementById('trfque'+i).style.display = 'none';
			}
		});
	}
</script>

<?php
	if($value_option == 0){
		// regular quiz
		include_once(JPATH_SITE."/components/com_guru/views/guruauthor/tmpl/reg_q_calc.php");
	}
	else{
		// final quiz
		include_once(JPATH_SITE."/components/com_guru/views/guruauthor/tmpl/final_q_calc.php");
	}
?>