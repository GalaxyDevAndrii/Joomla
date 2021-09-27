<?php

class GuruHelper{

	public function prepareUpdate(&$update, &$table){
		$domain = $_SERVER['HTTP_HOST'];
		$domain = str_replace("https://", "", $domain);
		$domain = str_replace("http://", "", $domain);
		$component = "guru";
		$valid_license = false;

		$db = JFactory::getDbo();
		$sql = "select `license_number` from #__guru_config";
		$db->setQuery($sql);
		$db->execute();
		$license_number = $db->loadColumn();
		$license_number = @$license_number["0"];

		$lang = JFactory::getLanguage();
		$extension = 'com_guru';
		$base_dir = JPATH_ADMINISTRATOR;
		$language_tag = '';
		$lang->load($extension, $base_dir, $language_tag, true);

		if(!isset($license_number) || trim($license_number) == ""){
			$app = JFactory::getApplication();
			$app->enqueueMessage(JText::_("GURU_REGISTERLICENSE_NUMBER"), "error");
			$app->redirect("index.php?option=com_guru&controller=guruConfigs&tab=11");
		}
		else{
			// start check license on ijoomla -----------------------------------------------
			$check_url = "https://www.ijoomla.com/index.php?option=com_digistore&controller=digistoreAutoinstaller&task=get_license_number_details&tmpl=component&format=raw&component=".$component."&domain=".urlencode($domain)."&license=".trim($license_number);
			$extensions = get_loaded_extensions();
		    $text = "";

			$license_details = file_get_contents($check_url);
			
			if(isset($license_details) && trim($license_details) != ""){
				$license_details = json_decode($license_details, true);

				if(isset($license_details["0"])){
					$license_details = $license_details["0"];
				}
				else{
					// license not exists
					$app = JFactory::getApplication();
					$app->enqueueMessage(JText::_("GURU_GET_LICENSE_HERE")." <a href='https://guru.ijoomla.com/pricing' target='_blank'>".JText::_("GURU_HERE")."</a>", "error");
					$app->redirect("index.php?option=com_guru&controller=guruConfigs&tab=11");
					die();
				}

				if(isset($license_details["expires"]) && trim($license_details["expires"]) != "" && trim($license_details["expires"]) == "0000-00-00 00:00:00"){
					$valid_license = true;
				}
				elseif(isset($license_details["expires"]) && trim($license_details["expires"]) != "" && trim($license_details["expires"]) != "0000-00-00 00:00:00"){
					$now = strtotime(date("Y-m-d H:i:s"));
					$license_expires = strtotime(trim($license_details["expires"]));

					if($license_expires >= $now){
						$valid_license = true;
					}
					else{
						$app = JFactory::getApplication();
						$app->enqueueMessage(JText::_("GURU_INVALID_LICENSE_NUMBER")." <a href='https://www.ijoomla.com/my-downloads43/licenses/42' target='_blank'>".JText::_("GURU_HERE")."</a>", "error");
						$app->redirect("index.php?option=com_guru&controller=guruConfigs&tab=11");
						die();
					}
				}
			}
			// stop check license on ijoomla -----------------------------------------------

			if($valid_license === false){
				// start check license on jomsocial -----------------------------------------------
				$check_url = "https://www.jomsocial.com/index.php?option=com_digistore&controller=digistoreAutoinstaller&task=get_license_number_details&tmpl=component&format=raw&component=".$component."&domain=".urlencode($domain)."&license=".trim($license_number);
				$extensions = get_loaded_extensions();
			    $text = "";

				$license_details = file_get_contents($check_url);

				if(isset($license_details) && trim($license_details) != ""){
					$license_details = json_decode($license_details, true);

					if(isset($license_details["0"])){
						$license_details = $license_details["0"];
					}
					else{
						// license not exists
						$app = JFactory::getApplication();
						$app->enqueueMessage(JText::_("GURU_GET_LICENSE_HERE")." <a href='https://guru.ijoomla.com/pricing' target='_blank'>".JText::_("GURU_HERE")."</a>", "error");
						$app->redirect("index.php?option=com_guru&controller=guruConfigs&tab=11");
						die();
					}

					if(isset($license_details["expires"]) && trim($license_details["expires"]) != "" && trim($license_details["expires"]) == "0000-00-00 00:00:00"){
						$valid_license = true;
					}
					elseif(isset($license_details["expires"]) && trim($license_details["expires"]) != "" && trim($license_details["expires"]) != "0000-00-00 00:00:00"){
						$now = strtotime(date("Y-m-d H:i:s"));
						$license_expires = strtotime(trim($license_details["expires"]));

						if($license_expires >= $now){
							$valid_license = true;
						}
						else{
							$app = JFactory::getApplication();
							$app->enqueueMessage(JText::_("GURU_INVALID_LICENSE_NUMBER")." <a href='https://www.ijoomla.com/my-downloads43/licenses/42' target='_blank'>".JText::_("GURU_HERE")."</a>", "error");
							$app->redirect("index.php?option=com_guru&controller=guruConfigs&tab=11");
							die();
						}
					}
				}
				// stop check license on jomsocial -----------------------------------------------
			}
		}

		if(!$valid_license){
			$app = JFactory::getApplication();
			$app->enqueueMessage(JText::_("GURU_NOT_VALID_LICENSE"), "error");
			$app->redirect("index.php?option=com_installer&view=update");
		}
		else{
			// start check ijoomla license ----------------------------------------------------------		
			$url_request = "https://www.ijoomla.com/index.php?option=com_digistore&controller=digistoreAutoinstaller&task=update_extension&tmpl=component&format=raw&component=guru&site=".urlencode($domain);
			$page_content = file_get_contents($url_request);

			if($page_content === FALSE || trim($page_content) == ""){
				$curl = curl_init();
				curl_setopt ($curl, CURLOPT_URL, $url_request);
				curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
				$page_content = curl_exec ($curl);
				curl_close ($curl);
			}
			// stop check ijoomla license ----------------------------------------------------------

			if(isset($page_content) && trim($page_content) != ""){
				$update->downloadurl->_data = $page_content;
			}
			else{
				// start check jomsocial license ----------------------------------------------------------		
				$url_request = "https://www.jomsocial.com/index.php?option=com_digistore&controller=digistoreAutoinstaller&task=update_extension&tmpl=component&format=raw&component=guru&site=".urlencode($domain);
				$page_content = file_get_contents($url_request);

				if($page_content === FALSE || trim($page_content) == ""){
					$curl = curl_init();
					curl_setopt ($curl, CURLOPT_URL, $url_request);
					curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
					$page_content = curl_exec ($curl);
					curl_close ($curl);
				}

				if(isset($page_content) && trim($page_content) != ""){
					$update->downloadurl->_data = $page_content;
				}
				// stop check jomsocial license ----------------------------------------------------------
			}

			if(trim($update->downloadurl->_data) == ""){
				$app = JFactory::getApplication();
				$app->enqueueMessage(JText::_("GURU_NOT_VALID_LICENSE"), "error");
				$app->redirect("index.php?option=com_installer&view=update", JText::_("GURU_NOT_VALID_LICENSE"), "error");
			}
		}
	}
}

?>