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

jimport ("joomla.application.component.view");

class guruViewguruPrograms extends JViewLegacy {

	function display ($tpl =  null ){
		$programs = $this->get('listPrograms');
		$this->programs = $programs;
		parent::display($tpl);
	}
	
	function show ($tpl =  null ) {
		$program = $this->get('Program');
		$this->program = $program;

		$programContent = $this->get('programContent');
		$this->programContent = $programContent;

		$exercise =  $this->get('exercise');
		$this->exercise = $exercise;

		$requirements=  $this->get('ReqCourses');
		$this->requirements = $requirements;

		// we get also the DAYS for this program
		$pdays = $this->get('pdays'); 
		$this->pdays = $pdays;		

		//we extract also the CONFIG SETTINGS for curency
		$getConfigSettings = $this->get('ConfigSettings');
		$this->getConfigSettings = $getConfigSettings;

		$author = $this->get('Author');
		$this->author = $author;

		$courses =  $this->get('authorCourses');
		$this->courses = $courses;
		
		parent::display($tpl);
	}

	function details ($tpl =  null ) {
		$program = $this->get('Program');
		$this->program = $program;
		$pdays = $this->get('pdays'); 
		$this->pdays = $pdays;
		$getConfigSettings = $this->get('ConfigSettings');
		$this->getConfigSettings = $getConfigSettings;
		parent::display($tpl);
	}
	
	function showmyprograms ($tpl =  null ) {
		$program = $this->get('myprograms');
		$this->program = $program;
		$getConfigSettings = $this->get('ConfigSettings');
		$this->getConfigSettings = $getConfigSettings;
		parent::display($tpl);
	}
	
	function listCourses($tmpl = null){
		$program = $this->get('Program');
		$this->program = $program;
		
		$programContent = $this->get('programContent');
		$this->programContent = $programContent;
		
		parent::display($tpl);
	}
}

?>