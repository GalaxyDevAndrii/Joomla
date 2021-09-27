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
$db = JFactory::getDBO();
$div_menu = $this->authorGuruMenuBar();
$my_media_cat = $this->mymediacat;
$config = $this->config;
$search = JFactory::getApplication()->input->get("filter_search", "");
$state = JFactory::getApplication()->input->get("filter_state", "-1");
$doc = JFactory::getDocument();
$doc->setTitle(trim(JText::_('GURU_AUTHOR'))." ".trim(JText::_('GURU_AUTHOR_MY_MEDIA_CAT')));
$data_post = JFactory::getApplication()->input->post->getArray();
?>

<script type="text/javascript" language="javascript">
	document.body.className = document.body.className.replace("modal", "");
</script>

<script language="javascript" type="application/javascript">
	function deleteAuthorMediaCat(){
		if(confirm("<?php echo JText::_("GURU_REMOVE_AUTHOR_COURSES"); ?>")){
			document.adminForm.task.value='removeMediaCat';
			document.adminForm.submit();
		}
	}
	function newAuthorMediaCategory(){
		document.adminForm.task.value='authoraddeditmediacat';
		document.adminForm.submit();	
	}
	function duplicateMediaCat(){
		document.adminForm.task.value='duplicateMediaCat';
		document.adminForm.submit();	
	}
	function newAuthorMedia(){
		document.adminForm.task.value='editMedia';
		document.adminForm.submit();
	}
	function unpublishMediaCat(){
		document.adminForm.task.value='unpublishMediaCat';
		document.adminForm.submit();
	}
	function publishMediaCat(){
		document.adminForm.task.value='publishMediaCat';
		document.adminForm.submit();
	}
</script>	

<div id="g_mycoursesauthor" class="gru-mycategoriesauthor">
    <?php 	echo $div_menu; //MENU TOP OF AUTHORS ?>
    
    <ul class="uk-subnav uk-subnav-pill wk-subnav wk-subnav-pill">
        <li><input type="button" class="uk-button uk-button-success wk-button wk-button-success" value="<?php echo JText::_('GURU_NEWCATEGORY'); ?>" onclick="newAuthorMediaCategory();"/></li>
        <li><input type="button" class="uk-button uk-button-success wk-button wk-button-success" value="<?php echo JText::_('GURU_DUPLICATE'); ?>" onclick="duplicateMediaCat();"/></li>
        <li><input type="button" class="uk-button uk-button-primary wk-button wk-button-primary" value="<?php echo JText::_('GURU_UNPUBLISH'); ?>" onclick="unpublishMediaCat();"/></li>
        <li><input type="button" class="uk-button uk-button-primary wk-button wk-button-primary" value="<?php echo JText::_('GURU_PUBLISH'); ?>" onclick="publishMediaCat();"/></li>
        <li><input type="button" class="uk-button uk-button-danger wk-button wk-button-danger" value="<?php echo JText::_('GURU_DELETE'); ?>" onclick="deleteAuthorMediaCat();"/></li>
    </ul>
    
    <h2 class="gru-page-title"><?php echo JText::_('GURU_AUTHOR_MY_MEDIA_CAT');?></h2>
    
	<form  action="<?php echo JUri::root() ?>index.php" class="form-horizontal" id="adminForm" method="post" name="adminForm" enctype="multipart/form-data">
		<div class="gru-page-filters">
			<input type="text" class="form-control" name="filter_search" value="<?php if(isset($data_post['filter_search'])) echo $data_post['filter_search'];?>" >
            <button class="uk-button uk-button-primary wk-button wk-button-primary hidden-phone" type="submit"><?php echo JText::_("GURU_SEARCH"); ?></button>
            &nbsp;
            <select name="filter_state" onchange="adminForm.submit();">
                <option value="-1" <?php if($state == "-1"){echo 'selected="selected"';} ?>><?php echo JText::_("GURU_SELECT_STATUS"); ?></option>
                <option value="1" <?php if($state == "1"){echo 'selected="selected"';} ?>><?php echo JText::_("GURU_PUBLISHED"); ?></option>
                <option value="0" <?php if($state == "0"){echo 'selected="selected"';} ?>><?php echo JText::_("GURU_UNPUBLISHED"); ?></option>
            </select>
        </div>
        
        <div class="clearfix">
            <div class="g_table_wrap g_margin_top">
                <table id="g_authormediacat" class="uk-table uk-table-striped wk-table wk-table-striped">
                    <tr>
                        <th class="g_cell_1"><input type="checkbox" name="checkall-toggle" value="" onclick="Joomla.checkAll(this);" /></th>
                        <th class="g_cell_2 hidden-phone"><?php echo JText::_('GURU_ID'); ?></th>
                        <th class="g_cell_3"><?php echo JText::_('GURU_NAME'); ?></th>
                        <th class="g_cell_4"><?php echo JText::_("GURU_PUBL"); ?></th>
                    </tr>

       
                    <?php 
                    $n =  count($my_media_cat);
                    for ($i = 0; $i < $n; $i++):
                        $id = $my_media_cat[$i]["id"];
                        $checked = JHTML::_('grid.id', $i, $id);
                       
                        $published = JHTML::_('grid.published', $my_media_cat, $i );
                    ?>
                         <tr class="guru_row">
                            <td class="g_cell_1"><?php echo $checked;?></td>
                            <td class="g_cell_2 hidden-phone"><?php echo $id;?></td>
                            <td class="guru_product_name g_cell_3">
                                <?php
                                    $line = "";
                                    for($j=0; $j<$my_media_cat[$i]["level"]; $j++){
                                        $line .= '&#151;';
                                    }
                                ?>
                                 <a href="<?php echo JUri::root() ?>index.php?option=com_guru&view=guruauthor&task=authoraddeditmediacat&id=<?php echo intval($my_media_cat[$i]["id"]); ?>"><?php echo $line."(".$my_media_cat[$i]["level"].") ".$my_media_cat[$i]["name"]; ?></a>
                           </td>
                                <td class="g_cell_4">
                                     <?php
                                        if($my_media_cat[$i]["published"] == 0){
                                            echo '<a title="Publish Item" onclick="return listItemTask(\'cb'.$i.'\', \'publishMediaCat\')" href="#">
                                                    <img alt="Unpublished" src="components/com_guru/images/icons/publish_x.png">
                                                  </a>';
                                        }
                                        else{
                                            echo '<a title="Unpublish Item" onclick="return listItemTask(\'cb'.$i.'\',\'unpublishMediaCat\')" href="#">
                                                    <img alt="Published" src="components/com_guru/images/icons/tick.png">
                                                  </a>';
                                        }
                                    ?>
                                </td>
                        </tr>
                    <?php 
                        endfor;
                    ?>	
                 </table>
            </div>
       </div>
       
       <?php
            echo $this->pagination->getLimitBox();
            $pages = $this->pagination->getPagesLinks();
            include_once(JPATH_SITE.DIRECTORY_SEPARATOR."components".DIRECTORY_SEPARATOR."com_guru".DIRECTORY_SEPARATOR."helpers".DIRECTORY_SEPARATOR."helper.php");
            $helper = new guruHelper();
            $pages = $helper->transformPagination($pages);
            echo $pages;
        ?>
       
        <input type="hidden" name="task" value="<?php echo JFactory::getApplication()->input->get("task", ""); ?>" />
        <input type="hidden" name="option" value="com_guru" />
        <input type="hidden" name="controller" value="guruAuthor" />
        <input type="hidden" name="boxchecked" value="" />
    </form>
</div>