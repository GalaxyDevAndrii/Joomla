<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2019 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

require_once JPATH_ADMINISTRATOR.'/components/com_rsform/helpers/field.php';
require_once JPATH_ADMINISTRATOR . '/components/com_rsform/helpers/calendar.php';

class RSFormProFieldCalendar extends RSFormProField
{
	protected $customId;
	
	// backend preview
	public function getPreviewInput()
	{
		$layout  	= $this->getProperty('CALENDARLAYOUT', 'FLAT');
		
		return RSFormProHelper::getIcon('calendar') . ' ' . JText::_('RSFP_COMP_FVALUE_' . $layout);
	}
	
	// functions used for rendering in front view
	public function getFormInput() {
		$calendar = RSFormProCalendar::getInstance('YUICalendar');
	
		$value 		= (string) $this->getValue();
		$name		= $this->getName();
		$id			= $this->getId();
		$layout  	= $this->getProperty('CALENDARLAYOUT', 'FLAT');
		$format 	= $this->getProperty('DATEFORMAT');
		$readonly	= $this->getProperty('READONLY', 'NO');
		$attr		= $this->getAttributes('input');
		$type		= 'text';
		$additional = '';
		$zIndex	  	= $this->getZIndex();
		$position	= $this->getPosition();
		// Create a unique ID for this calendar.
		$this->customId = $this->formId.'_'.$position;
		
		$hiddenValue = '';
		$hidden = JFactory::getApplication()->input->get('hidden', array(), 'array');
		$hiddenName = $this->formId.'_'.$this->getProperty('NAME', '');
		if (!empty($hidden[$hiddenName])) {
			$hiddenValue = preg_replace('#[^0-9\/]+#i', '', $hidden[$hiddenName]);
		} else {
			if (!empty($value)) {
				// Let's allow the 'now' keyword
				if (strtolower($value) == 'now')
				{
					$value = JFactory::getDate('now')->format($format);
				}
				if (JFactory::getLanguage()->getTag() != 'en-GB')
				{
					$value = RSFormProCalendar::fixValue($value, $format);
				}
				// Try to create a date to see if it's valid
				$date = DateTime::createFromFormat($format, $value);
				if ($date !== false)
				{
					$hiddenValue = $date->format('m/d/Y');
				}
				else
				{
					$value = '';
					$hiddenValue = '';
				}
			}
		}
		
		// set the calendar script
		$config = array(
			'offset'			 => $this->getProperty('VALIDATIONCALENDAROFFSET', 1),
			'layout' 	 		 => $layout,
			'dateFormat' 		 => $format,
			'value' 	 		 => $hiddenValue,
			'minDate' 	 		 => $this->isCode($this->getProperty('MINDATE', '')),
			'maxDate' 			 => $this->isCode($this->getProperty('MAXDATE', '')),
			'validationCalendar' => $this->getProperty('VALIDATIONCALENDAR', ''),
			'formId' 			 => $this->formId,
			'customId' 			 => $this->customId
		);
		$calendar->setCalendar($config);
		
		// Parse Additional Attributes for the input textbox
		if ($attr) {
			foreach ($attr as $key => $values) {
				// @new feature - Some HTML attributes (type, size, maxlength) can be overwritten
				// directly from the Additional Attributes area
				if ($key == 'type' && strlen($values)) {
					${$key} = $values;
					continue;
				}
				$additional .= $this->attributeToHtml($key, $values);
			}
		}
		
		// This is the textbox used to display the date
		$input = '<input'.
				 ' id="txtcal'.$this->customId.'"'.
				 ' name="'.$this->escape($name).'"'.
				 ' type="'.$this->escape($type).'"';
		// Is it read only?
		if ($readonly) {
			$input .= ' readonly="readonly"';
		}
		// Add the value
		$input .= ' value="'.$this->escape($value).'"';
		// Additional HTML
		$input .= $additional;
		// Close the tag
		$input .= ' />';
		
		// Flat layouts need a <br /> so that the calendar is shown below the textbox
		if ($layout == 'FLAT') {
			// Let's just substitute the "button" with a <br /> tag
			$button = '<br />';
		} elseif ($layout == 'POPUP') {
			$attr  		= $this->getAttributes('button');
			$label 		= $this->getProperty('POPUPLABEL', '...');
			$additional = '';
			
			// Parse Additional Attributes for the button
			if ($attr) {
				foreach ($attr as $key => $values) {
					$additional .= $this->attributeToHtml($key, $values);
				}
			}
			
			// Create the popup button
			$button = $this->getButtonInput($label, $additional);
		}
		
		// This is the calendar HTML container
		$hide = '';
		// If this is a popup layout, hide the container so that it's only shown when the button is clicked
		if ($layout == 'POPUP') {
			$hide = 'clear:both;display:none;position:absolute;';
		}
		$container = '<div'.
					' id="cal'.$this->customId.'Container"'.
					' style="'.$hide.'z-index:'.$zIndex.'">'.
					'</div>';

		// This is the hidden field of the calendar
		$hidden = '<input'.
				  ' id="hiddencal'.$this->customId.'"'.
				  ' type="hidden"'.
				  ' name="hidden['.$this->formId.'_'.$id.']"'.
			      ' data-rsfp-original-date="' . $this->escape($hiddenValue) . '"' .
				  ' />';
		
		$html = $this->setFieldOutput($input, $button, $container, $hidden, $layout);
		
		return $html;
	}

	protected function getButtonInput($label, $additional)
	{
		return '<input id="btn' . $this->customId . '" type="button" value="' . $this->escape($label) . '"' . $additional . ' />';
	}
	
	// set the field output - function needed for overwriting in the layout classes
	protected function setFieldOutput($input, $button, $container, $hidden, $layout) {
		return $input.$button.$container.$hidden;
	}
	
	// @desc Gets the position of this calendar in the current form (eg. if it's the only calendar in the form, the position is 0,
	//		 if it's the second calendar the position is 1 and so on).
	protected function getPosition() {
		return RSFormProCalendar::getInstance('YUICalendar')->getPosition($this->formId, $this->componentId);
	}
	
	protected function getZIndex() {
		return (9999 - (int) $this->getProperty('Order'));
	}
	
	// @desc All calendars should have a 'rsform-calendar-box' class for easy styling
	//		 Since the calendar is composed of multiple items, we need to differentiate the attributes through the $type parameter
	public function getAttributes($type='input') {
		$attr = parent::getAttributes();
		if (strlen($attr['class'])) {
			$attr['class'] .= ' ';
		}
		
		if ($type == 'input') {
			$attr['class'] .= 'rsform-calendar-box';
			$layout	= $this->getProperty('CALENDARLAYOUT', 'FLAT');
			if ($layout == 'FLAT') {
				$attr['class'] .= ' txtCal';
			}
		} elseif ($type == 'button') {
			unset($attr['aria-required'], $attr['aria-invalid'], $attr['aria-describedby']);

			$attr['class'] .= 'btnCal rsform-calendar-button';
			if (!empty($attr['onclick'])) {
				$attr['onclick'] .= ' ';
			} else {
				$attr['onclick'] = '';
			}
			
			$attr['onclick'] .= "RSFormPro.YUICalendar.showHideCalendar('cal".$this->customId."Container');";
		}
		
		return $attr;
	}


	public function processValidation($validationType = 'form', $submissionId = 0)
	{
		$validate 	= $this->getProperty('VALIDATIONDATE', true);
		$required 	= $this->isRequired();
		$format 	= $this->getProperty('DATEFORMAT');
		$value 		= $this->getValue();

		if ($required && !strlen(trim($value)))
		{
			return false;
		}

		if ($validate && strlen(trim($value)))
		{
			if (JFactory::getLanguage()->getTag() != 'en-GB')
			{
				$value = RSFormProCalendar::fixValue($value, $format);
			}

			$validDate = DateTime::createFromFormat($format, $value);

			if ($validDate)
			{
				$validDate = $validDate->format($format);
			}

			if ($validDate !== $value)
			{
				return false;
			}
		}

		return true;
	}
}