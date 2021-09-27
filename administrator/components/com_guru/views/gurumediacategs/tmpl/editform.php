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
	
	$action = JFactory::getApplication()->input->get("action", "");
	
?>	
<script type="text/javascript" src="<?php echo JURI::base();?>/includes/js/joomla.javascript.js"></script>
<script type="text/javascript" language="javascript">	
	Joomla.submitbutton = function(pressbutton){
	//function submitbutton(pressbutton){
		var form = document.adminForm;
		if (pressbutton == 'cancel'){
			submitform(pressbutton);
			return true;
		}
		else if(pressbutton == 'save' || pressbutton == 'apply'){
			name = document.adminForm.name.value;
			if(name.length == 0){
				alert('<?php echo JText::_("GURU_INSERT_CATEG_NAME"); ?>');
				return false;
			}
		}
		submitform(pressbutton);
	}	
</script>

<form action="<?php echo JUri::root() ?>administrator/index.php" method="post" id="adminForm" name="adminForm" enctype="multipart/form-data">
	<?php
		if($action == "from_media"){
	?>
    		<div class="row-fluid">
            	<div class="span12">
                    <button class="btn btn-small btn-success pull-right" onclick="Joomla.submitbutton('save')">
                        <span class="icon-save"></span>
                        <?php echo JText::_("GURU_SV_AND_CL"); ?>
                    </button>
            	</div>
			</div>
	<?php
		}
		else{
	?>
			<div class="well">
				<?php if(isset($id)&& $id != ""){ echo JText::_('GURU_TREEMEDIACAT').":"." [".JText::_('GURU_EDIT')."]";} else{ echo JText::_('GURU_TREEMEDIACAT').":"." [".JText::_('GURU_NEW')."]";} ?>
			</div>
	<?php
		}
	?>
    
	 <ul class="nav nav-tabs">
            <li class="active"><a href="#general1" data-toggle="tab"><?php echo JText::_('GURU_GENERAL');?></a></li>
            <li><a href="#meta" data-toggle="tab">Meta Tags</a></li>
         </ul>
         <div class="tab-content">
         	<div class="tab-pane active" id="general1">
                <table class='adminform'>
                    <tr>
                        <td>		
                            <table width='100%'>
                                <tr>
                                    <td width="15%">
                                        <?php echo JText::_('GURU_CATEGNAME');?>: <font style="color:red;">*</font>
                                    </td>
                                    <td>
                                        <input type="text" name="name" value="<?php if(isset($name) && $name != ""){echo $name;}?>"/>
                                        <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_MEDIA_CATEGNAME"); ?>" >
                                            <img border="0" src="components/com_guru/images/icons/tooltip.png">
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td width="15%">
                                        <?php echo JText::_('GURU_CATEGPARENT');?>:
                                    </td>
                                    <td>                    	
                                        <?php echo $this->parentCategory($parent_id);?>
                                        <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_MEDIA_CATEGPARENT"); ?>" >
                                            <img border="0" src="components/com_guru/images/icons/tooltip.png">
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td width="15%">
                                        <?php echo JText::_('GURU_DESCRIPTION');?>:
                                    </td>
                                    <td>                     	                                      	
                                        <?php
                                             if(isset($description) && $description == ""){				   
                                                $descr = "";
                                              }
                                              else{
                                                $descr = $description;
                                              }					  			   
                                             //$editor = JFactory::getEditor();												     											   				    		
                                             $editor  = new JEditor(JFactory::getConfig()->get("editor"));
                                             echo $editor->display('description', $descr, '530px;', '300px', '60', '20');
                                   ?>
                                    </td>
                                    <td>
                                        <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_MEDIA_DESCRIPTION"); ?>" >
                                            <img border="0" src="components/com_guru/images/icons/tooltip.png">
                                        </span>
                                    </td>
                                </tr>
                                 <tr>
                                    <td width="15%">
                                        <?php echo JText::_('GURU_PUBLISHED');?>:
                                    </td>
                                    <td>
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
                                   </td>
                                </tr>
                            </table>			
                        </td>
                    </tr>
                </table>
               </div> 
			  <div class="tab-pane" id="meta">
                <table class='adminform'>
                    <tr>
                        <td>		
                            <table width='100%'>
                                <tr>
                                    <td width="15%">
                                        <?php echo JText::_('GURU_META_TITLE');?>:
                                    </td>
                                    <td>
                                        <textarea name="metatitle"><?php if(isset($metatitle) && $metatitle != ""){echo $metatitle;}?></textarea>
                                        <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_MEDIA_META_TITLE"); ?>" >
                                            <img border="0" src="components/com_guru/images/icons/tooltip.png">
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td width="15%">
                                        <?php echo JText::_('GURU_META_KEYWORDS');?>:
                                    </td>
                                    <td>
                                        <textarea name="metakey"><?php if(isset($metakey) && $metakey != ""){echo $metakey;}?></textarea>
                                        <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_MEDIA_META_KEYWORDS"); ?>" >
                                            <img border="0" src="components/com_guru/images/icons/tooltip.png">
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td width="15%">
                                        <?php echo JText::_('GURU_META_DESCRIPTION');?>:
                                    </td>
                                    <td>
                                        <textarea name="metadesc"><?php if(isset($metadesc) && $metadesc != ""){echo $metadesc;}?></textarea>
                                        <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_MEDIA_META_DESCRIPTION"); ?>" >
                                            <img border="0" src="components/com_guru/images/icons/tooltip.png">
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
			</div>
	
    <input type="hidden" name="action" value="<?php echo $action; ?>" />
	<input type='hidden' name='tab' value='<?php echo $tab; ?>' />
	<input type='hidden' name='option' value='com_guru' />
    <input type="hidden" name="id" value="<?php if(isset($id) && $id != ""){echo $id;}?>" />
    <input type="hidden" name="controller" value="guruMediacategs" />
    <input type="hidden" name="task" value="edit" />
	
</form>