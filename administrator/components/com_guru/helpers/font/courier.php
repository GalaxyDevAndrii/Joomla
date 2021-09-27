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
for($i=0;$i<=255;$i++)
	$fpdf_charwidths['courier'][chr($i)]=600;
$fpdf_charwidths['courierB']=$fpdf_charwidths['courier'];
$fpdf_charwidths['courierI']=$fpdf_charwidths['courier'];
$fpdf_charwidths['courierBI']=$fpdf_charwidths['courier'];
?>
