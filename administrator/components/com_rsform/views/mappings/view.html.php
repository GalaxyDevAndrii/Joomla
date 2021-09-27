<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2019 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

class RsformViewMappings extends JViewLegacy
{
	public function display( $tpl = null )
	{
        if (!JFactory::getUser()->authorise('forms.manage', 'com_rsform'))
        {
            throw new Exception(JText::_('COM_RSFORM_NOT_AUTHORISED_TO_USE_THIS_SECTION'));
        }

        $this->form     = $this->get('Form');
		$this->formId   = JFactory::getApplication()->input->getInt('formId');
		$this->fields   = $this->get('quickFields');
		$this->mapping 	= $this->get('mapping');
		$this->config 	= array(
			'connection' => $this->mapping->connection,
			'host' 		 => $this->mapping->host,
			'driver' 	 => !empty($this->mapping->driver) ? $this->mapping->driver : JFactory::getConfig()->get('dbtype'),
			'port' 		 => $this->mapping->port,
			'username'   => $this->mapping->username,
			'password' 	 => $this->mapping->password,
			'database'   => $this->mapping->database,
			'table' 	 => $this->mapping->table
		);

		$displayPlaceholders = RSFormProHelper::generateQuickAddGlobal('display', true);
		foreach ($this->fields as $fields)
		{
			$displayPlaceholders = array_merge($displayPlaceholders, $fields['display']);
		}

		$this->document->addScriptDeclaration('RSFormPro.Placeholders = ' . json_encode(array_values($displayPlaceholders)) . ';');
		
		parent::display($tpl);
	}
}