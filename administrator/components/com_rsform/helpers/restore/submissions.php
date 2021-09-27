<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2019 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

class RSFormProRestoreSubmissions
{
	// JDatabase instance
	protected $db;

	// Holds an array of the XML data.
	protected $formId;
	
	public function __construct($options = array()) {
		$path 	  			= &$options['path'];
		$this->formId		= isset($options['formId']) ? (int) $options['formId'] : 0;
		
		$this->db = JFactory::getDbo();
		
		// Check if the form's xml exists
		if (!file_exists($path)) {
			throw new Exception(sprintf('The file %s does not exist!', $path));
		}
		
		if (!is_readable($path)) {
			throw new Exception(sprintf('File %s is not readable!', $path));
		}
		
		// Attempt to load the XML data
		libxml_use_internal_errors(true);
		
		if (class_exists('DOMDocument')) {
			$dom = new DOMDocument('1.0', 'UTF-8');
			$dom->strictErrorChecking = false;
			$dom->validateOnParse = false;
			$dom->recover = true;
			$dom->loadXML(file_get_contents($path));
			
			$this->xml = simplexml_import_dom($dom);
		} else {
			$this->xml = simplexml_load_file($path);
		}
		
		if ($this->xml === false) {
			$errors = array();
			foreach (libxml_get_errors() as $error) {
				$errors[] = 'Message: '.$error->message.'; Line: '.$error->line.'; Column: '.$error->column;
			}
			throw new Exception(sprintf('Error while parsing XML: %s<br/>', implode('<br />', $errors)));
		}
	}
	
	public function restore()
	{
		foreach ($this->xml->children() as $submission)
		{
			$data = array(
				'FormId' => $this->formId
			);
		
			foreach ($submission as $property => $value)
			{
				// Skip form ID for now
				if ($property == 'values')
				{
					continue;
				}

				if ($property === 'UserId')
				{
					$value = (int) (string) $value;
				}

				$data[$property] = (string) $value;
			}

			$data = (object) $data;

			$this->db->insertObject('#__rsform_submissions', $data, 'SubmissionId');
			
			$submissionId = $data->SubmissionId;
			
			// insert submission values
			if (isset($submission->values))
			{
				foreach ($submission->values->children() as $value)
				{
					$data = array(
						'FormId' => $this->formId,
						'SubmissionId' => $submissionId,
						'FieldName' => (string) $value->fieldname,
						'FieldValue' => (string) $value->fieldvalue
					);

					$data = (object) $data;

					$this->db->insertObject('#__rsform_submission_values', $data);
				}
			}
		}
	}
	
}