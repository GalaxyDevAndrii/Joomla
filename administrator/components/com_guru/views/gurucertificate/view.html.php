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

class guruAdminViewguruCertificate extends JViewLegacy {
    
    function display ($tpl =  null ) { 
        JToolBarHelper::title(JText::_('GURU_CERTIFICATES_MANAGER'), 'generic.png');	
		JToolBarHelper::save();
		JToolBarHelper::apply();
		JToolBarHelper::cancel();
		//JToolBarHelper::addNew('edit', 'New');
		//JToolBarHelper::editList();
		//JToolBarHelper::publishList();
		//JToolBarHelper::unpublishList();
		//JToolBarHelper::deleteList('Are you sure you want to delete this plan?');
        
		$certificates_details = $this->get('CertificatesDetails');
		$this->certificates_details = $certificates_details;
		
        $pagination = $this->get( 'Pagination' );
        $this->pagination = $pagination;

        parent::display($tpl);
    }

    function listcertificates ($tpl =  null ) { 
        JToolBarHelper::title(JText::_('GURU_CERTIFICATES_MANAGER'), 'generic.png');	
		JToolBarHelper::cancel();
        
		$certificates = $this->get('Items');
		$this->certificates = $certificates;

		$teachers = $this->get("Teachers");
		$this->teachers = $teachers;
		
        $pagination = $this->get( 'Pagination' );
        $this->pagination = $pagination;

        parent::display($tpl);
    }
    
    function edit($tpl = null)
    {
        
        $plan = $this->get('CurrentPlan');
        $isNew = ($plan->id == NULL);
        
        $text = $isNew?JText::_('GURU_NEW'):JText::_('GURU_EDIT');
        $this->action = $text;

		JToolBarHelper::title(JText::_('GURU_SUBS_PLAN').":<small>[".$text."]</small>");
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
        
        $action = ($plan->id != NULL) ? trim(JText::_('GURU_EDIT_SUBPL')) : 
                                                        trim(JText::_('GURU_ADD_SUBPL'));
        
        /* subscr duration */    
        $subcrub_duration[] = JHTML::_('select.option','0','Unlimited');
        for($i = 1; $i <= 31; $i++) {
            $subcrub_duration[] = JHTML::_('select.option', $i, $i);
        }

        $lists['duration_count'] = JHTML::_('select.genericlist', $subcrub_duration, 'term', 
        ' class="inputbox" size="1" onchange="checkUnlimited(this)" ', 'value', 'text', $plan->term);
        
        $subcrub_duration_type[] = JHTML::_('select.option', 'hours', strtolower(JText::_('GURU_EHOURS')));
        $subcrub_duration_type[] = JHTML::_('select.option', 'days', strtolower(JText::_('GURU_REAL_DAYS')));
		$subcrub_duration_type[] = JHTML::_('select.option', 'weeks', strtolower(JText::_('GURU_REAL_WEEKS')));
        $subcrub_duration_type[] = JHTML::_('select.option', 'months', strtolower(JText::_('GURU_EMONTH')));
        $subcrub_duration_type[] = JHTML::_('select.option', 'years', strtolower(JText::_('GURU_EYEAR')));
        $lists['duration_type'] = JHTML::_('select.genericlist', $subcrub_duration_type, 'period', 
            'class="inputbox" size="1" '.((($plan->term == 0))?'style="display:none;"':''), 
            'value', 'text', $plan->period);        
        /* subscr duration type - END */
        
        /* Published */		
		if($plan->published == NULL){
			$plan->published = 1;
		}
		$lists['published']  = '<div style="float:left;">';
		if($plan->published == 1){ 
			$lists['published'] .= 	'<input type="radio" name="published" value="1" checked="checked" >&nbsp;'.JText::_("GURU_YES").'&nbsp;';
		}
		else{
			$lists['published'] .= 	'<input type="radio" name="published" value="1">&nbsp;'.JText::_("GURU_YES").'&nbsp;';
		}		
        $lists['published'] .= '</div>';           
        $lists['published'] .= '<div style="float:left;">'; 
		if($plan->published == 0){ 
			$lists['published'] .= 	'<input type="radio" name="published" value="0" checked="checked" >&nbsp;'.JText::_("GURU_NO").'&nbsp;';
		}
		else{
			$lists['published'] .= 	'<input type="radio" name="published" value="0">&nbsp;'.JText::_("GURU_NO").'&nbsp;';
		}            
        $lists['published'] .= '</div>'; 				
       /* $published[] = JHTML::_( 'select.option', '1', JText::_("JAS_YES") );
        $published[] = JHTML::_( 'select.option', '0', JText::_("JAS_NO") );
		if($plan->published == NULL){
			$plan->published = 1;
		}
        $lists['published'] = JHTML::_('select.radiolist', $published, 'published', 'class="inputbox" ', 'value', 'text', $plan->published, 'published');*/
        /* Published - END */
        
        $this->lists = $lists;
        $this->plan = $plan;
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

	function getCourseDetails($course_id){
		$model = $this->getModel("guruCertificate");
		$course = $model->getCourseDetails($course_id);

		return $course;
	}

	function getCourseAuthor($author_ids){
		$model = $this->getModel("guruCertificate");
		$authors = $model->getCourseAuthor($author_ids);

		return $authors;
	}
    
}

?>