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
//These scripts are already been included from the administrator\components\com_guru\guru.php file
//$doc->addScript('components/com_guru/js/jquery.DOMWindow.js');
JHTML::_('behavior.modal');
$data_post = JFactory::getApplication()->input->post->getArray();

require_once(JPATH_BASE.'/../components/com_guru/helpers/helper.php');
$guruHelper = new guruHelper;
?>

<?php
$k = 0;
$n = count($this->listScorm);	
?>
</script>
    	<script language="javascript" type="text/javascript">
        Joomla.submitbutton = function(pressbutton){
            var form = document.adminForm;
            if (pressbutton=='duplicate') {
                if (form['boxchecked'].value == 0) {
                        alert( "<?php echo JText::_("GURU_Q_MAKESEL_JAVAMSG");?>" );
                } 
                else{
                    submitform( pressbutton );
                }
            }
            else {
                submitform( pressbutton );
            }
        }
    </script>
    <div id="editcell">
        <div id="myModal" class="modal-small modal hide">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            </div>
            <div class="modal-body">
            </div>
        </div>
        
        <div class="clearfix"></div>
           
        <form action="<?php echo JUri::root() ?>administrator/index.php" id="adminForm" name="adminForm" method="post">
        	<table style="width:100%;" cellpadding="5" cellspacing="5" bgcolor="#FFFFFF">
                <tr>
                    <td>
                    	<?php
                        	$session = JFactory::getSession();
							$registry = $session->get('registry');
							$search = $registry->get('search', "");
							$search_value = "";
							
							if(isset($data_post['search'])) {
                                $search_value = $data_post['search'];
                                $registry->set('search', $data_post['search']);
                            }
							elseif(isset($search) && trim($search) != ""){
                                $search_value = $search;
                            }
						?>
                    
                        <input type="text" name="search" value="<?php echo $search_value; ?>" />
                        <input class="btn btn-primary" type="submit" name="submit_search" value="<?php echo JText::_('GURU_SEARCHTXT');?>" />
                    </td>
                    
                    <!--td>
                        <?php echo JText::_('GURU_COURSE_PUBL');?>
                        <select onchange="document.adminForm.submit()" name="quiz_publ_status">
                        <?php 
							$session = JFactory::getSession();
							$registry = $session->get('registry');
							$quiz_publ_status = $registry->get('quiz_publ_status', "");
						
                            if(isset($quiz_publ_status) && trim($quiz_publ_status) != ""){
                                $pb = trim($quiz_publ_status);
                            }
                            
							if(isset($data_post['quiz_publ_status'])){
                                $pb = $data_post['quiz_publ_status'];
                            }
							
                            if(!isset($pb)) {$pb=NULL;}
                        ?>
                        <option <?php if($pb=='YN') { echo "selected='selected'";} ?> value="YN"><?php echo JText::_("GURU_SELECT"); ?></option>
                        <option <?php if($pb=='Y') { echo "selected='selected'";} ?> value="Y"><?php echo JText::_("GURU_PUBLISHED"); ?></option>
                        <option <?php if($pb=='N') { echo "selected='selected'";} ?> value="N"><?php echo JText::_("GURU_UNPUBLISHED"); ?></option>
                        </select>	
                    </td-->		
                </tr>
            </table>
            
            <table class="table table-striped table-bordered adminlist">
                <thead>
                    <tr>
                        <th width="5%">
                        	<input type="checkbox" onclick="Joomla.checkAll(this)" name="toggle" value="" />
                            <span class="lbl"></span>
                        </th>
                        <th width="5%"><?php echo JText::_('ID');?></th>
                        <th width="25%"><?php echo JText::_('GURU_TITLE');?></th>
                        <th width="25%"><?php echo JText::_('GURU_COURSE');?></th>
                        <th width="15%"><?php echo JText::_('GURU_PRODLSPUB');?></th>
                        <th width="15%"><?php echo JText::_('GURU_PRODLEPUB');?></th>
                    </tr>
                </thead>                
                <tbody>
                <?php
                    for ($i = 0; $i < $n; $i++){
                        $scorm = $this->listScorm[$i];
                        $id = $scorm->id;
                        $checked = JHTML::_('grid.id', $i, $id);
                                        ?>
                    <tr class="row<?php echo $k;?>"> 
                        <td align="center"><?php echo $checked;?><span class="lbl"></span></td>		
                        <td><?php echo $scorm->id;?></td>		
                        <td nowrap><a class="a_guru" href="index.php?option=com_guru&controller=guruScorm&task=edit&cid[]=<?php echo intval($id); ?>" ><?php echo $scorm->title;?></a></td>	
                        <td><?php echo $scorm->course_name;?></td>	
                        <td><?php echo $guruHelper->getDate($scorm->start)?></td>
                        <td><?php echo $guruHelper->getDate($scorm->end)?></td>
                    </tr>
                    <input type="hidden" name="valueop" value="<?php echo $ad->is_final;?>" />
                <?php 
                        $k = 1 - $k;
                    }//end for
                ?>
                    <tr>
                        <td colspan="11">
                            <div class="pagination pagination-toolbar">
                                <?php echo $this->pagination->getListFooter(); ?>
                            </div>
                            <div class="btn-group pull-left">
                                <label for="limit" class="element-invisible"><?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC');?></label>
                                <?php echo $this->pagination->getLimitBox(); ?>
                           </div>
                        </td>
            	</tr>
                </tbody>
            </table>
            <input type="hidden" name="option" value="com_guru" />
            <input type="hidden" name="task" value="" />
            <input type="hidden" name="boxchecked" value="0" />
            <input type="hidden" name="controller" value="guruScorm" />
            <input type="hidden" name="id" value="<?php echo $scorm->id;?>">
            <input type="hidden" name="old_limit" value="<?php echo JFactory::getApplication()->input->get("limitstart"); ?>" />
        </form>
        
    </div>
    <script language="javascript">
    	var first = false;
    	
        function showContentVideo(href){
        	first = true;
        	jQuery.ajax({
              url: href,
              success: function(response){
               jQuery( '#myModal .modal-body').html(response);
              }
            });
        }

    	jQuery('#myModal').on('hide', function () {
    	   jQuery('div.modal-body').html('');
    	});

    	jQuery('body').click(function () {
    		if(!first){
    			jQuery('#myModal .modal-body iframe').attr('src', '');
    		}
    		else{
    			first = false;
    		}
    	});
    </script>