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

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
	$courses_promo = $this->courses_promo;
	$n = count($courses_promo);
?>
<style>
	a{
		cursor:pointer;
	}
</style>
<script>
	function redirect(id){
		window.parent.location="index.php?option=com_guru&controller=guruPrograms&task=edit&cid[]="+id;
	}
</script>
<form action="<?php echo JUri::root() ?>administrator/index.php" name="adminForm" id="adminForm" method="post">
<div id="editcell" >
    <table class="table table-striped adminlist" style="width: 100%;">
        <thead>
            <tr>
                <th>
                    <?php echo JText::_('GURU_ID');?>
                </th>
                <th>
                    <?php echo JText::_('GURU_PRODNAME');?>
                </th>
            </tr>
        </thead>
    <?php 
        for ($i = 0; $i < $n; $i++){
            $courses_promo[$i] = (Array)$courses_promo[$i];
            $id = $courses_promo[$i]["id"];
            
    ?>
            <tr> 
                    
                <td align="">
                    <?php echo $id;?>
                </td >			
                <td align="">
                    <?php echo '<a onclick="redirect(\''.$courses_promo[$i]["id"].'\');">'.$courses_promo[$i]["name"].'</a>'; ?>
                </td>		
            </tr>
        <?php 
        }
        ?>
        </tbody>
    </table>
</div>
	<input type="hidden" name="option" value="com_guru" />
	<input type="hidden" name="task" value="show" />
	<input type="hidden" name="pid" value="<?php //echo $pid; ?>" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="controller" value="guruPrograms" />
</form>
