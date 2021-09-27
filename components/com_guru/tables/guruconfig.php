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

class TableguruConfig extends JTable {
	var $id = null;
	var $direction = null;
	var $currency = null;
	var $datetype = null;
	var $dificulty = null;
	var $show_empty_categ = null;
	var $display_tasks = null;
	var $groupe_tasks = null;
	var $display_media = null;
	var $btnback = null;
	var $btnhome = null;                       
	var $btnnext = null;
	var $dofirst = null;
	var $imagesin = null;
	var $videoin = null;
	var $audioin = null;
	var $docsin = null;
	var $filesin = null;
	var $fromname = null;
	var $fromemail = null;
	var $regemail = null;
	var $orderemail = null;
	var $ctgpage = null;
	var $st_ctgpage = null;
	var $ctgspage = null;
	var $st_ctgspage = null;
	var $psgspage = null;
	var $st_psgpage = null;
	var $psgpage = null;
	var $st_psgspage = null;
	var $authorspage = null;
	var $st_authorspage = null;
	var $authorpage = null;
	var $st_authorpage = null;	
	var $st_donecolor = null;
	var $st_notdonecolor = null;
	var $st_txtcolor = null;
	var $st_width = null;
	var $st_height = null;
	var $progress_bar = null;
	var $video_display = null;
	var $audio_display = null;
	var $content_selling=null;
	var $open_target=null;
	
	function __construct (&$db) {
		parent::__construct('#__guru_config', 'id', $db);
	}

};


?>