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
$doc =JFactory::getDocument();
JHtml::script('components/com_guru/js/open_modal.js');
$input = JFactory::getApplication()->input;
$data_get = $input->get->getArray();
$list_quizzes = $this->list_quizzes;
$n = count($list_quizzes);
$cid = $input->getInt('cid', 0);
$db = JFactory::getDbo();
$query = "SELECT quizzes_ids FROM `#__guru_quizzes_final` WHERE qid = " . $cid;
$addedQuizzes = $db->setQuery($query)->loadResult();
$addedQuizzes = explode(',', $addedQuizzes);
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

<script type="text/javascript" language="javascript">
	document.body.className = document.body.className.replace("modal", "");
</script>

<h2 class="gru-page-title"><?php echo JText::_("GURU_ADD_QUIZZES_TO_FINAL_EXAM"). " ".@$quiz_name ; ?></h2>
		
<form class="g_modal_search" name="form1" action="<?php echo JUri::root(true) ?>/index.php?option=com_guru&controller=guruAuthor&task=addquizzes&no_html=1&cid=<?php echo $cid ?>&tmpl=<?php echo $input->get("tmpl", ""); ?>" method="post">

    <div class="gru-page-filters">
        <input style="margin:0px;" type="text" name="search_text"  value="<?php echo JFactory::getApplication()->input->get("search_text", "");?>" />
        <button type="submit" name="submit_search"  class="uk-button uk-button-primary wk-button wk-button-primary hidden-phone"><?php echo JText::_('GURU_SEARCHTXT');?></button>
    </div>        
</form>

<form method="post" name="adminForm" id="adminForm" action="<?php echo JUri::root() ?>index.php">
    <div id="editcell" class="clearfix">
        <table class="uk-table uk-table-striped wk-table wk-table-striped">
                <th width="1%"></th>
                <th width="1%"><?php echo JText::_("GURU_ID");?></th>
                <th><?php echo JText::_("VIEWPLUGTITLE");?></th>
        <?php
        $k = 0;
        for($i = 0; $i < count($list_quizzes); $i++){
            $id =  $list_quizzes[$i]["id"];
        ?>
            <tr class="row<?php echo $k;?>">
                <td>
                    <input 
                        type="checkbox" 
                        name="cb[]"
                        id="cb[]" 
                        value= "<?php echo $id;?>" 
                        <?php echo in_array($id, $addedQuizzes) ? 'checked' : '' ?>>
                    <span class="lbl"></span>
                </td>	
                <td><?php echo $list_quizzes[$i]["id"];?></td>
                <td><?php echo $list_quizzes[$i]["name"];?></td>
            </tr> 
        <?php
            $k = 1 - $k;
        }
        ?> 
        </table>
    </div>  
    <div>
        <input type="button" class="uk-button uk-button-success wk-button wk-button-success" onclick="savequizzes();" value="<?php echo JText::_("GURU_SAVE_PROGRAM_BTN"); ?>"> 
    </div> 
    
	<input type="hidden" value="com_guru" name="option"/>
	<input type="hidden" value="savequizzes" name="task"/>
	<input type="hidden" value="<?php echo intval($data_get['cid']);?>" name="quizid"/>
    <input id="quizzes_ids" name="quizzes_ids" type="hidden" value="" />
	<input type="hidden" value="guruAuthor" name="controller"/>
    <input type="hidden" value="<?php echo JFactory::getApplication()->input->get("tmpl", ""); ?>" name="tmpl"/>
</form>