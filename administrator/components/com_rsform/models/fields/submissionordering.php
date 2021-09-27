<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2019 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('JPATH_PLATFORM') or die;

JFormHelper::loadFieldClass('list');

class JFormFieldSubmissionordering extends JFormFieldList
{
    protected function getOptions()
    {
        $options = parent::getOptions();

        /* @var $model RsformModelSubmissions */
	    $model = JModelLegacy::getInstance('Submissions', 'RsformModel');
	    if ($headers = array_merge($model->getStaticHeaders(), $model->getHeaders()))
	    {
	    	foreach ($headers as $header)
		    {
			    $options[] = JHtml::_('select.option', $header->value . ' ASC', JText::sprintf('COM_RSFORM_SUBMISSIONS_HEADER_ORDERING_ASC', $header->label));
			    $options[] = JHtml::_('select.option', $header->value . ' DESC', JText::sprintf('COM_RSFORM_SUBMISSIONS_HEADER_ORDERING_DESC', $header->label));
		    }
	    }

	    return $options;
    }
}
