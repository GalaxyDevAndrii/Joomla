<?php
/**
 * ------------------------------------------------------------------------
 * JA Masthead Module 
 * ------------------------------------------------------------------------
 * Copyright (C) 2004-2018 J.O.O.M Solutions Co., Ltd. All Rights Reserved.
 * @license - GNU/GPL, http://www.gnu.org/licenses/gpl.html
 * Author: J.O.O.M Solutions Co., Ltd
 * Websites: http://www.joomlart.com - http://www.joomlancers.com
 * ------------------------------------------------------------------------
 */

defined('_JEXEC') or die( 'Restricted access' );

jimport('joomla.form.formfield');

require_once(dirname(__FILE__).'/../behavior.php');
class JFormFieldJapanel extends JFormField {
    protected $type = 'Japanel';
    
    protected function getInput() {
    	$func = (string) $this->element['function'];
    	if(!$func) {
    		$func = 'init';
    	}
    	
    	if(method_exists($this, $func)) {
    		call_user_func_array(array($this, $func), array());
    	}
    	return null;
    }
    
    protected function init() {
    	$doc = JFactory::getDocument();
        $path = JURI::root().$this->element['path'];
        if (!version_compare(JVERSION, '3.4', 'lt')) // joomla 3.4.x not call mootools by default
        	JHtml::_('behavior.framework', true);
        $doc->addScript($path.'japanel/depend.js');
        if(version_compare(JVERSION, '3.0', 'lt')) {
        	JHTML::_('JABehavior.jquery');
        	//JHTML::_('JABehavior.jquerychosen', '.pane-slider select');
        	JHTML::_('JABehavior.jquerychosen', '.form-validate select');
        	
        	$doc->addStyleSheet($path.'japanel/style.css');
        	$doc->addScript($path.'japanel/script.js');
        } else {
        	$doc->addStyleSheet($path.'japanel/style30.css');
        	$doc->addScript($path.'japanel/script30.js');
        }
        return null;
    }
    
    protected function depend() {
		$group_name = 'jform';
    	preg_match_all('/jform\\[([^\]]*)\\]/', $this->name, $matches);
		
		if(!isset($matches[1]) || empty($matches[1])){
			preg_match_all('/jaform\\[([^\]]*)\\]/', $this->name, $matches);
			$group_name = 'jaform';
		}
		
		
		$script = '';
		if(isset($matches[1]) && !empty($matches[1])) {
			foreach ($this->element->children() as $option){
				$elms = preg_replace('/\s+/', '', (string)$option[0]);
				$script .= "
					JADepend.inst.add('".$option['for']."', {
						val: '".$option['value']."',
						elms: '".$elms."',
						group: '".$group_name . '[' . @$matches[1][0] . ']'."'
					});";
			}
		}
		if(!empty($script)) {
			$doc = JFactory::getDocument();
			$doc->addScriptDeclaration("
			$(window).addEvent('load', function(){
				".$script."
			});");
		}
    }
}