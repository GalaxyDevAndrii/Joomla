<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_helloworld
 *
 * @copyright   Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
require 'libraries/aws/autoload.php';
use Aws\Rekognition\RekognitionClient;

class HelloWorldControllerHelloWorld extends JControllerForm
{   
	public function compare($key = null, $urlVar = null)
	{
		// Check for request forgeries.

		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
        
		$app = JFactory::getApplication(); 
		$input = $app->input; 
		$model = $this->getModel('form');
        
		// Get the current URI to set in redirects. As we're handling a POST, 
		// this URI comes from the <form action="..."> attribute in the layout file above
		$currentUri = (string)JUri::getInstance();

		// Check that this user is allowed to add a new record
		if (!JFactory::getUser()->authorise( "core.create", "com_helloworld"))
		{
			$app->enqueueMessage(JText::_('JERROR_ALERTNOAUTHOR'), 'error');
			$app->setHeader('status', 403, true);

			return;
		}
        
		// get the data from the HTTP POST request
		$data  = $input->get('jform', array(), 'array');
        
		// set up context for saving form data
		$context = "$this->option.edit.$this->context";

		// save the form data and set up the redirect back to the same form, 
		// to avoid repeating them under every error condition
		$app->setUserState($context . '.data', $data);
		$this->setRedirect($currentUri);
        
		// Validate the posted data.
		// First we need to set up an instance of the form ...
		$form = $model->getForm($data, false);

		if (!$form)
		{
			$app->enqueueMessage($model->getError(), 'error');
			return false;
		}


		// Handle the uploaded file - get it from the PHP $_FILES structure
		$fileinfo = $this->input->files->get('jform', array(), 'array');
		$file1 = $fileinfo['imageinfo1']['image1'];
		$file2 = $fileinfo['imageinfo2']['image2'];
        
		// Check if any files have been uploaded
		if ($file1['error'] == 4)   // no file uploaded (see PHP file upload error conditions)
		{
			$app->enqueueMessage("Source image is not uploaded", 'warning');
			return false;
		} 
		else if ($file2['error'] == 4)   // no file uploaded (see PHP file upload error conditions)
		{
			$app->enqueueMessage("Target image is not uploaded", 'warning');
			return false;
		} 
		else 
		{
			if ($file1['error'] > 0 || $file2['error'] > 0)
			{
				$app->enqueueMessage(JText::sprintf('COM_HELLOWORLD_ERROR_FILEUPLOAD', $file['error']), 'warning');
				return false;
			}
            
			// make sure filename is clean
			jimport('joomla.filesystem.file');
			$file1['name'] = JFile::makeSafe($file1['name']);
			$file2['name'] = JFile::makeSafe($file2['name']);

			if (!isset($file1['name']))
			{
				// No filename (after the name was cleaned by JFile::makeSafe)
				$app->enqueueMessage(JText::_('COM_HELLOWORLD_ERROR_BADFILENAME'), 'warning');
				return false;
			}
			if (!isset($file2['name']))
			{
				// No filename (after the name was cleaned by JFile::makeSafe)
				$app->enqueueMessage(JText::_('COM_HELLOWORLD_ERROR_BADFILENAME'), 'warning');
				return false;
			}

			// files from Microsoft Windows can have spaces in the filenames
			$file1['name'] = str_replace(' ', '-', $file1['name']);
			$file2['name'] = str_replace(' ', '-', $file2['name']);

			// do checks against Media configuration parameters
			$mediaHelper = new JHelperMedia;
			if (!$mediaHelper->canUpload($file1) || !$mediaHelper->canUpload($file2))
			{
				// The file can't be uploaded - the helper class will have enqueued the error message
				return false;
			}
            
			// prepare the uploaded file's destination pathnames
			$mediaparams = JComponentHelper::getParams('com_media');
			$relativePathname1 = JPath::clean($mediaparams->get($path, 'images') . '/' . time() . "_1." . pathinfo($file1['name'], PATHINFO_EXTENSION));
			$relativePathname2 = JPath::clean($mediaparams->get($path, 'images') . '/' . time() . "_2." . pathinfo($file1['name'], PATHINFO_EXTENSION));
			$absolutePathname1 = JPATH_ROOT . '/' . $relativePathname1;
			$absolutePathname2 = JPATH_ROOT . '/' . $relativePathname2;
			if (JFile::exists($absolutePathname1) || JFile::exists($absolutePathname2))
			{
				$app->enqueueMessage(JText::_('COM_HELLOWORLD_ERROR_FILE_EXISTS'), 'warning');
				return false;
			}

            
			// check file contents are clean, and copy it to destination pathname
			if (!JFile::upload($file1['tmp_name'], $absolutePathname1))
			{
				// Error in upload
				$app->enqueueMessage(JText::_('COM_HELLOWORLD_ERROR_UNABLE_TO_UPLOAD_FILE'));
				return false;
			}
			if (!JFile::upload($file2['tmp_name'], $absolutePathname2))
			{
				// Error in upload
				$app->enqueueMessage(JText::_('COM_HELLOWORLD_ERROR_UNABLE_TO_UPLOAD_FILE'));
				return false;
			}

			$imagedata1 = file_get_contents($absolutePathname1);
			$imagedata2 = file_get_contents($absolutePathname2);
        
			$credentials = new Aws\Credentials\Credentials('AKIA3V2PHD4A7HB4JOHM', 'n2PUZ/Wm7wvA7obEl3ULy35cjBazeOPEUhscqX8p');

			$client = new \Aws\Rekognition\RekognitionClient([
			    'version'     => 'latest',
			    'region'      => 'us-east-1',
			    'credentials' => $credentials
			]);

			$result = $client->compareFaces([
			    'SimilarityThreshold' => 80,
			    'SourceImage' => [
			        'Bytes' => $imagedata1
			    ],
			    'TargetImage' => [
			        'Bytes' => $imagedata2
			    ],
			]);

			$similarity = $result['FaceMatches'] == null ? "Face match failed" : ($result['FaceMatches'][0]['Similarity'] > 60 ? "Face match success : Similarity is ".$result['FaceMatches'][0]['Similarity'] : "Face match failed");
        
			$this->setRedirect(
				$currentUri,
				$similarity
			);

	            
			return true;
		}
        
    }

}