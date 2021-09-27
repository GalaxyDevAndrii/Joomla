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

class guruAdminViewguruSubremind extends JViewLegacy {
                                                                                  
    function display ($tpl =  null ) { 
        JToolBarHelper::title(JText::_('GURU_REMINDS_MANAGER'), 'generic.png');		
		JToolBarHelper::addNew('edit', 'New');
		JToolBarHelper::editList();
		JToolBarHelper::publishList();
		JToolBarHelper::unpublishList();
		JToolBarHelper::deleteList(JText::_('GURU_SYS_EMAILS_DELETE'));
        $reminds = $this->get('Items');
        $this->reminds = $reminds;
        $this->pagination = $this->get('Pagination');
		$this->state = $this->get('State');
        parent::display($tpl);
    }
    
    function edit($tpl = null)
    {
        
        $email = $this->get('CurrentRemind');
        $isNew = ($email->id == NULL);
        //$editor = JFactory::getEditor();
        $editor  = new JEditor(JFactory::getConfig()->get("editor"));
        
        $text = $isNew ? JText::_('GURU_NEW') : JText::_('GURU_EDIT');
        
		JToolBarHelper::title(JText::_('GURU_EMAIL_REMIND').":<small>[" . trim($text) . "]</small>");
		JToolBarHelper::save();
        
		if ($isNew) {
			JToolBarHelper::spacer();
			JToolBarHelper::divider();
			JToolBarHelper::cancel();
		} else {
			JToolBarHelper::spacer();
			JToolBarHelper::apply();
			JToolBarHelper::divider();
			JToolBarHelper::cancel ('cancel', 'Close');
		}     
        
        $action = !$isNew ? trim( JText::_('GURU_EDIT').' '. strtolower(JText::_('GURU_EMAIL_REMIND')) ) :
                        trim( JText::_('GURU_NEW').' '. strtolower(JText::_('GURU_EMAIL_REMIND')) );
         
        $lists['subject'] = "<input type='text' style='width:60%' id='subject' name='subject' value='" . $email->subject . "'/>";

         /* Email Types */
        for ($i =0; $i<=12; $i++) {
            $type[] = JHTML::_('select.option', $i, JText::_('GURU_REM_EXP' . $i));
        }
		
        $lists['term'] = JHTML::_('select.genericlist', $type, 'term', 'class="inputbox" size="1"', 'value', 'text', $email->term);
        /* Email Types - END */
         
        /* Published */
        if ( !isset($email->published) || ($email->published == NULL) ) {
            $email->published = '1';
        }        
        
		$lists['published'] = '<input type="hidden" name="published" value="0">';
		if($email->published == 0){ 
			$lists['published'] .= '<input type="checkbox" value="1" class="ace-switch ace-switch-5" name="published">';
		}
		else{
			$lists['published'] .= '<input type="checkbox" checked="checked" value="1" class="ace-switch ace-switch-5" name="published">';
		}
		$lists['published'] .= '<span class="lbl"></span>';
          
        $this->editor = $editor;
        $this->lists = $lists;
        $this->email = $email;
        $this->action = $action;        
        
        parent::display($tpl);
    }

    function approve( &$row, $i, $imgY = 'tick.png', $imgX = 'publish_x.png', $prefix='' )
	{
		if($row->published=='1') {
			$img = $imgY;
			$task = "unapprove";
			$alt = JText::_('Unapprove');
			$action = JText::_('Unapprove item');
		} elseif ($row->published=='0') {
			$img = $imgX;
			$task = "approve";
			$alt = JText::_('Approve');
			$action = JText::_('Approve item');
		} else {return false;}

		$href = '
		<a href="javascript:void(0);" onclick="return listItemTask(\'cb'. $i .'\',\''. $prefix.$task .'\')" title="'. $action .'">
		<img src="components/com_guru/images/'. $img .'" border="0" alt="'. $alt .'" /></a>'
		;
		return $href;
	}
}

?>