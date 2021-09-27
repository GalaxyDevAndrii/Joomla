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

function guruAdminCourseModal(){
?>
	<div id="myModal" class="modal hide g_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display:none;">
        <div class="modal-header">
            <button type="button" id="close" class="close" data-dismiss="modal" aria-hidden="true">x</button>
         </div>
         <div class="modal-body">
            <iframe frameborder="0" class="g_leesson_popup" id="g_lesson_level1"></iframe>
        </div>
    </div>
<?php
}
	
?>