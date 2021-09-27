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

jimport ("joomla.application.component.view");

class guruAdminViewguruQuizCountdown extends JViewLegacy {
	function display ($tpl =  null ) {
		$db = JFactory::getDBO(); 

		JToolBarHelper::title(JText::_('GURU_QUIZ_COUNTD'));
		JToolBarHelper::save();
		JToolBarHelper::apply();
		JToolBarHelper::cancel ('cancel', 'Cancel');
		
		?>
       <script language="JavaScript" type="text/javascript" src="<?php echo JURI::base(); ?>components/com_guru/js/freecourse.js"></script>
        <?php
		$font_family[] 	= JHTML::_('select.option', '', JText::_('GURU_DEFAULT'), 'value', 'option' );
		$font_family[] 	= JHTML::_('select.option', 'Arial', 'Arial', 'value', 'option' );
		$font_family[] 	= JHTML::_('select.option', 'Helvetica', 'Helvetica', 'value', 'option' );
		$font_family[] 	= JHTML::_('select.option', 'Garamond', 'Garamond', 'value', 'option' );
		$font_family[] 	= JHTML::_('select.option', 'sans-serif', 'Sans Serif', 'value', 'option' );
		$font_family[] 	= JHTML::_('select.option', 'Verdana', 'Verdana', 'value', 'option' );
		$lists['font_family'] = JHTML::_( 'select.genericlist', $font_family, 'parameters[font_family]', 'class="inputbox" size="1" onChange="javascript:guruchangeFont(value)" ','value', 'option');		
		$this->lists = $lists;
		
		 	 
		parent::display($tpl);
	}
}

?>