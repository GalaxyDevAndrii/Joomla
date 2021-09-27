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

class guruAdminViewguruConfigs extends JViewLegacy {
	function display ($tpl =  null ) {
		$db = JFactory::getDBO(); 

		JToolBarHelper::title(JText::_('GURU_SETTINGS'));
		JToolBarHelper::save();
		JToolBarHelper::apply();
		JToolBarHelper::cancel ('cancel', 'Cancel');
		
		$configs = $this->get('Configs');
		$this->configs = $configs;  
		
		$emails = $this->get('Emails');
		$this->emails = $emails;   
		
		$superadmins = $this->get('Admins');
		$this->superadmins = $superadmins;

		$payment_plugins = $this->get("PaymentPlugins");
		$this->payment_plugins = $payment_plugins;

		parent::display($tpl);
	}

	function getLicenseDetails($license_number){
		$return = array("domain"=>"...", "date"=>"...", "status"=>"...");

		$domain = $_SERVER['HTTP_HOST'];
		$domain = str_replace("https://", "", $domain);
		$domain = str_replace("http://", "", $domain);
		$app = JFactory::getApplication();
		$component = "guru";
		$valid_license = false;

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

			if(isset($license_details["expires"]) && trim($license_details["expires"]) != "" && trim($license_details["expires"]) == "0000-00-00 00:00:00"){
				$valid_license = true;
			}
			elseif(isset($license_details["expires"]) && trim($license_details["expires"]) != "" && trim($license_details["expires"]) != "0000-00-00 00:00:00"){
				$now = strtotime(date("Y-m-d H:i:s"));
				$license_expires = strtotime(trim($license_details["expires"]));

				if($license_expires >= $now){
					$valid_license = true;
				}
			}

			if(isset($license_details["domain"]) && isset($license_details["expires"])){
				$domain = $license_details["domain"];
				$date = $license_details["expires"];
				$status = JText::_("GURU_PROMOINACTIVE");

				if($date == "0000-00-00 00:00:00"){
					$date = JText::_("GURU_UNLIMPROMO");
				}

				if($valid_license){
					$status = JText::_("GURU_PROMOACTIVE");
				}

				$return = array("domain"=>$domain, "date"=>$date, "status"=>$status);
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

				if(isset($license_details["expires"]) && trim($license_details["expires"]) != "" && trim($license_details["expires"]) == "0000-00-00 00:00:00"){
					$valid_license = true;
				}
				elseif(isset($license_details["expires"]) && trim($license_details["expires"]) != "" && trim($license_details["expires"]) != "0000-00-00 00:00:00"){
					$now = strtotime(date("Y-m-d H:i:s"));
					$license_expires = strtotime(trim($license_details["expires"]));

					if($license_expires >= $now){
						$valid_license = true;
					}
				}
			}
			// stop check license on jomsocial -----------------------------------------------
		}

		if(isset($license_details) && is_array($license_details) && count($license_details) > 0){
			$domain = $license_details["domain"];
			$date = $license_details["expires"];
			$status = JText::_("GURU_PROMOINACTIVE");

			if($date == "0000-00-00 00:00:00"){
				$date = JText::_("GURU_UNLIMPROMO");
			}

			if($valid_license){
				$status = JText::_("GURU_PROMOACTIVE");
			}

			$return = array("domain"=>$domain, "date"=>$date, "status"=>$status);
		}

		return $return;
	}

	function getFieldsGroups(){
		$db = JFactory::getDbo();
		
		$sql = "select * from #__guru_groups ORDER BY `ordering` ASC";
		$db->setQuery($sql);
		$db->execute();
		$groups = $db->loadAssocList();

		return $groups;
	}

	function getGroupFields($group_id){
		$db = JFactory::getDbo();
		
		$sql = "select * from #__guru_fields where `group_id`=".intval($group_id)." ORDER BY `ordering` ASC";
		$db->setQuery($sql);
		$db->execute();
		$fields = $db->loadAssocList();

		return $fields;
	}
}

?>