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
$div_menu = $this->authorGuruMenuBar();
$doc = JFactory::getDocument();
$doc->setTitle(trim(JText::_('GURU_AUTHOR'))." ".trim(JText::_('GURU_AUTHOR_MY_MEDIA_CAT')));
	if(isset($_REQUEST['tab'])){
		$tab = $_REQUEST['tab'];
	}
	else{
		$tab = 0;
	}
	
	$categories = $this->categories;
	if(is_array($categories) && count($categories) > 0){
		$id = $categories[0]->id;		
		$name = $categories[0]->name;
		if($categories[0]->parent_id != ""){
			$parent_id = $categories[0]->parent_id;
		}
		else{
			$parent_id = 0;
		}
		if($categories[0]->child_id != ""){
			$child_id = $categories[0]->child_id;
		}
		else{
			$child_id = 0;
		}
		$description = $categories[0]->description;
		$metatitle = $categories[0]->metatitle;
		$metakey = $categories[0]->metakey;
		$metadesc = $categories[0]->metadesc;
		$published = $categories[0]->published;
	}
	else{
		$id = "";
		$name = "";
		$parent_id = 0;
		$child_id = 0;
		$description = "";
		$metatitle = "";
		$metakey = "";
		$metadesc = "";
		$published = 1;
	}
	
?>	

<script type="text/javascript" language="javascript">
	document.body.className = document.body.className.replace("modal", "");
</script>

<script type="text/javascript" src="<?php echo JURI::base();?>/includes/js/joomla.javascript.js"></script>
<script type="text/javascript" language="javascript">	
	function savemediacat(pressbutton){
	//function submitbutton(pressbutton){
		var form = document.adminForm;
		if (pressbutton == 'cancel'){
			//submitform(pressbutton);

            form.task.value = pressbutton;
            form.submit();

			return true;
		}
		else if(pressbutton == 'save' || pressbutton == 'apply'){
			name = document.adminForm.name.value;
			if(name.length == 0){
				alert('<?php echo JText::_("GURU_INSERT_CATEG_NAME"); ?>');
				return false;
			}
		}
		
        //submitform(pressbutton);

        form.task.value = pressbutton;
        form.submit();
	}	
</script>

<div id="g_authoraddeditmedacat" class="clearfix com-cont-wrap">
    <?php 	echo $div_menu; //MENU TOP OF AUTHORS ?>
    
    <ul class="uk-subnav uk-subnav-pill wk-subnav wk-subnav-pill">
        <li><input type="button" class="uk-button uk-button-success wk-button wk-button-success" value="<?php echo JText::_("GURU_SAVE"); ?>" onclick="javascript:savemediacat('applymediacat');" /></li>
        <li><input type="button" class="uk-button uk-button-success wk-button wk-button-success" value="<?php echo JText::_("GURU_SAVE_AND_CLOSE"); ?>" onclick="javascript:savemediacat('savemediacat');" /></li>
        <li><input type="button" class="uk-button uk-button-primary wk-button wk-button-primary" value="<?php echo JText::_("GURU_CLOSE"); ?>" onclick="document.location.href='<?php echo JRoute::_("index.php?option=com_guru&view=guruauthor&task=authormymediacategories&layout=authormymediacategories"); ?>';" /></li>
	</ul>
    
	<form action="<?php echo JUri::root() ?>index.php" method="post" id="adminForm" name="adminForm" enctype="multipart/form-data" class="uk-form uk-form-horizontal wk-form wk-form-horizontal">
        <div class="uk-panel uk-panel-box uk-panel-box-secondary uk-margin-top uk-margin-bottom wk-panel wk-panel-box wk-panel-box-secondary wk-margin-top wk-margin-bottom">
        	<h3 class="uk-panel-title wk-panel-title"><?php  if(isset($id)&& $id != ""){ echo JText::_('GURU_TREEMEDIACAT').":"." [".JText::_('GURU_EDIT')."]";} else{echo JText::_('GURU_TREEMEDIACAT').":"." [".JText::_('GURU_NEW')."]";} ?></h3>
        </div>
        
        <div class="uk-form-row wk-form-row">
            <label class="uk-form-label wk-form-label" for="name">
                <?php echo JText::_("GURU_CATEGNAME");?>:
                <span class="uk-text-danger wk-text-danger">*</span>
            </label>
            <div class="uk-form-controls wk-form-controls">
                <input type="text" name="name" value="<?php if(isset($name) && $name != ""){echo $name;}?>" class="input-large"/>
                <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_MEDIA_CATEGNAME"); ?>" >
                    <img border="0" src="components/com_guru/images/icons/tooltip.png">
                </span>
            </div>
        </div>
        
        <div class="uk-form-row wk-form-row">
            <label class="uk-form-label wk-form-label" for="name">
                <?php echo JText::_("GURU_CATEGPARENT");?>:
            </label>
            <div class="uk-form-controls wk-form-controls">
                <?php echo $this->parentMediaCategory($parent_id);?>
                <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_MEDIA_CATEGPARENT"); ?>" >
                    <img border="0" src="components/com_guru/images/icons/tooltip.png">
                </span>
            </div>
        </div>
        
		<div class="uk-form-row wk-form-row">
            <label class="uk-form-label wk-form-label" for="name">
                <?php echo JText::_("GURU_DESCRIPTION");?>:
            </label>
            <div class="uk-form-controls wk-form-controls">
                <?php
					if(isset($description) && $description == ""){				   
						$descr = "";
					}
					else{
						$descr = $description;
					}					  			   
					$editor = new JEditor(JFactory::getConfig()->get("editor"));
					echo $editor->display('description', $descr, '500px;', '300px', '60', '20');
				?>
            </div>
        </div>
        
        <div class="uk-form-row wk-form-row">
            <label class="uk-form-label wk-form-label" for="name">
                <?php echo JText::_("GURU_PUBLISHED");?>:
            </label>
            <div class="uk-form-controls wk-form-controls">
                <input type="hidden" name="published" value="0">
				<?php
                    $checked = '';
                    if($published == 1){
                        $checked = 'checked="checked"';
                    }
                ?>
                <input type="checkbox" <?php echo $checked; ?> value="1" class="ace-switch ace-switch-5" name="published">
                <span class="lbl"></span>
                <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_MEDIA_PUBLISHED"); ?>" >
                    <img border="0" src="components/com_guru/images/icons/tooltip.png">
                </span>
            </div>
        </div>
        
        <div class="uk-form-row wk-form-row">
            <label class="uk-form-label wk-form-label" for="name">
                <?php echo JText::_("GURU_META_TITLE");?>:
            </label>
            <div class="uk-form-controls wk-form-controls">
                <textarea name="metatitle" class="mediacateg-meta"><?php if(isset($metatitle) && $metatitle != ""){echo $metatitle;}?></textarea>
                <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_MEDIA_META_TITLE"); ?>" >
                    <img border="0" src="components/com_guru/images/icons/tooltip.png">
                </span>
            </div>
        </div>
        
        <div class="uk-form-row wk-form-row">
            <label class="uk-form-label wk-form-label" for="name">
                <?php echo JText::_("GURU_META_KEYWORDS");?>:
            </label>
            <div class="uk-form-controls wk-form-controls">
                <textarea name="metakey" class="mediacateg-meta"><?php if(isset($metakey) && $metakey != ""){echo $metakey;}?></textarea>
                <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_MEDIA_META_KEYWORDS"); ?>" >
                    <img border="0" src="components/com_guru/images/icons/tooltip.png">
                </span>
            </div>
        </div>
        
        <div class="uk-form-row wk-form-row">
            <label class="uk-form-label wk-form-label" for="name">
                <?php echo JText::_("GURU_META_DESCRIPTION");?>:
            </label>
            <div class="uk-form-controls wk-form-controls">
                <textarea name="metadesc" class="mediacateg-meta"><?php if(isset($metadesc) && $metadesc != ""){echo $metadesc;}?></textarea>
                <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_MEDIA_META_DESCRIPTION"); ?>" >
                    <img border="0" src="components/com_guru/images/icons/tooltip.png">
                </span>
            </div>
        </div>
        
        <input type='hidden' name='tab' value='<?php echo $tab; ?>' />
        <input type='hidden' name='option' value='com_guru' />
        <input type="hidden" name="id" value="<?php if(isset($id) && $id != ""){echo $id;}?>" />
        <input type="hidden" name="controller" value="guruAuthor" />
        <input type="hidden" name="task" value="" />
    </form>
</div>