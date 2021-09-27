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
jimport ("joomla.aplication.component.model");


class guruAdminModelguruConfig extends JModelLegacy {
	var $_configs = null;
	var $_id = null;

	function __construct () {
		parent::__construct();
		$this->_id = 1;
	}

	function getConfigs() {
		if (empty ($this->_configs)) {
			$this->_configs = $this->getTable("guruConfig");
			$this->_configs->load($this->_id);
		}		
		
		//start currency drop-down
		$currencyOptions = array();
		$currencyOptions["AED"] = "United Arab Emirates dirham";
		$currencyOptions["ALL"] = "Albanian lek";
		$currencyOptions["ANG"] = "Netherlands Antillean gulden";
		$currencyOptions["ARS"] = "Argentine peso";
		$currencyOptions["AUD"] = "Australian dollar";
		$currencyOptions["AWG"] = "Aruban florin";
		$currencyOptions["BBD"] = "Barbadian dollar";
		$currencyOptions["BDT"] = "Bangladeshi taka";
		$currencyOptions["BHD"] = "Bahraini dinar";
		$currencyOptions["BIF"] = "Burundian franc";
		$currencyOptions["BMD"] = "Bermudian dollar";
		$currencyOptions["BND"] = "Brunei dollar";
		$currencyOptions["BOB"] = "Bolivian boliviano";
		$currencyOptions["BRL"] = "Brazilian real";
		$currencyOptions["BSD"] = "Bahamian dollar";
		$currencyOptions["BTN"] = "Bhutanese ngultrum";
		$currencyOptions["BWP"] = "Botswana pula";
		$currencyOptions["BZD"] = "Belize dollar";
		$currencyOptions["CAD"] = "Canadian dollar";
		$currencyOptions["CHF"] = "Swiss franc";
		$currencyOptions["CLF"] = "Unidad de Fomento";
		$currencyOptions["CLP"] = "Chilean peso";
		$currencyOptions["CNY"] = "Chinese renminbi yuan";
		$currencyOptions["COP"] = "Colombian peso";
		$currencyOptions["CRC"] = "Costa Rican colon";
		$currencyOptions["CZK"] = "Czech koruna";
		$currencyOptions["CUP"] = "Cuban peso";
		$currencyOptions["CVE"] = "Cape Verdean escudo";
		$currencyOptions["DKK"] = "Danish krone";
		$currencyOptions["DOP"] = "Dominican peso";
		$currencyOptions["DZD"] = "Algerian dinar";
		$currencyOptions["EGP"] = "Egyptian pound";
		$currencyOptions["ETB"] = "Ethiopian birr";
		$currencyOptions["EUR"] = "Euro";
		$currencyOptions["FJD"] = "Fijian dollar";
		$currencyOptions["FKP"] = "Falkland pound";
		$currencyOptions["GBP"] = "British pound";
		$currencyOptions["GIP"] = "Gibraltar pound";
		$currencyOptions["GMD"] = "Gambian dalasi";
		$currencyOptions["GNF"] = "Guinean franc";
		$currencyOptions["GTQ"] = "Guatemalan quetzal";
		$currencyOptions["GYD"] = "Guyanese dollar";
		$currencyOptions["HKD"] = "Hong Kong dollar";
		$currencyOptions["HNL"] = "Honduran lempira";
		$currencyOptions["HTG"] = "Haitian gourde";
		$currencyOptions["HUF"] = "Hungarian forint";
		$currencyOptions["IDR"] = "Indonesian rupiah";
		$currencyOptions["ILS"] = "Israeli new sheqel";
		$currencyOptions["INR"] = "Indian rupee";
		$currencyOptions["IQD"] = "Iraqi dinar";
		$currencyOptions["IRR"] = "Iranian rial";
		$currencyOptions["JMD"] = "Jamaican dollar";
		$currencyOptions["JOD"] = "Jordanian dinar";
		$currencyOptions["JPY"] = "Japanese yen";
		$currencyOptions["KES"] = "Kenyan shilling";
		$currencyOptions["KHR"] = "Cambodian riel";
		$currencyOptions["KMF"] = "Comorian franc";
		$currencyOptions["KPW"] = "North Korean won";
		$currencyOptions["KRW"] = "South Korean won";
		$currencyOptions["KWD"] = "Kuwaiti dinar";
		$currencyOptions["KYD"] = "Cayman Islands dollar";
		$currencyOptions["LAK"] = "Lao kip";
		$currencyOptions["LBP"] = "Lebanese pound";
		$currencyOptions["LKR"] = "Sri Lankan rupee";
		$currencyOptions["LRD"] = "Liberian dollar";
		$currencyOptions["LSL"] = "Lesotho loti";
		$currencyOptions["LYD"] = "Libyan dinar";
		$currencyOptions["MAD"] = "Moroccan dirham";
		$currencyOptions["MNT"] = "Mongolian togrog";
		$currencyOptions["MOP"] = "Macanese pataca";
		$currencyOptions["MRO"] = "Mauritanian ouguiya";
		$currencyOptions["MUR"] = "Mauritian rupee";
		$currencyOptions["MVR"] = "Maldivian rufiyaa";
		$currencyOptions["MWK"] = "Malawian kwacha";
		$currencyOptions["MYR"] = "Malaysian ringgit";
		$currencyOptions["NGN"] = "Nigerian naira";
		$currencyOptions["NOK"] = "Norwegian krone";
		$currencyOptions["NPR"] = "Nepalese rupee";
		$currencyOptions["NZD"] = "New Zealand dollar";
		$currencyOptions["OMR"] = "Omani rial";
		$currencyOptions["PAB"] = "Panamanian balboa";
		$currencyOptions["PEN"] = "Peruvian nuevo sol";
		$currencyOptions["PGK"] = "Papua New Guinean kina";
		$currencyOptions["PHP"] = "Philippine peso";
		$currencyOptions["PKR"] = "Pakistani rupee";
		$currencyOptions["PLN"] = "Polish Zloty";
		$currencyOptions["PYG"] = "Paraguayan guarani";
		$currencyOptions["QAR"] = "Qatari riyal";
		$currencyOptions["RON"] = "Romanian leu";
		$currencyOptions["RWF"] = "Rwandan franc";
		$currencyOptions["SAR"] = "Saudi riyal";
		$currencyOptions["SBD"] = "Solomon Islands dollar";
		$currencyOptions["SCR"] = "Seychellois rupee";
		$currencyOptions["SEK"] = "Swedish krona";
		$currencyOptions["SGD"] = "Singapore dollar";
		$currencyOptions["SHP"] = "Saint Helenian pound";
		$currencyOptions["SLL"] = "Sierra Leonean leone";
		$currencyOptions["SOS"] = "Somali shilling";
		$currencyOptions["STD"] = "Sao Tome and Principe dobra";
		$currencyOptions["RUB"] = "Russian ruble";
		$currencyOptions["SVC"] = "Salvadoran colon";
		$currencyOptions["SYP"] = "Syrian pound";
		$currencyOptions["SZL"] = "Swazi lilangeni";
		$currencyOptions["THB"] = "Thai baht";
		$currencyOptions["TND"] = "Tunisian dinar";
		$currencyOptions["TOP"] = "Tongan pa'anga";
		$currencyOptions["TRY"] = "Turkish new lira";
		$currencyOptions["TTD"] = "Trinidad and Tobago dollar";
		$currencyOptions["TWD"] = "New Taiwan dollar";
		$currencyOptions["TZS"] = "Tanzanian shilling";
		$currencyOptions["USD"] = "United States dollar";
		$currencyOptions["VND"] = "Vietnamese Dong";
		$currencyOptions["VUV"] = "Vanuatu vatu";
		$currencyOptions["WST"] = "Samoan tala";
		$currencyOptions["YER"] = "Yemeni rial";
		$currencyOptions["RSD"] = "Serbian dinar";
		$currencyOptions["ZAR"] = "South African rand";
		$currencyOptions["ZMK"] = "Zambian kwacha";
		$currencyOptions["ZWD"] = "Zimbabwean dollar";
		$currencyOptions["AMD"] = "Armenian dram";
		$currencyOptions["MMK"] = "Myanmar kyat";
		$currencyOptions["HRK"] = "Croatian kuna";
		$currencyOptions["ERN"] = "Eritrean nakfa";
		$currencyOptions["DJF"] = "Djiboutian franc";
		$currencyOptions["ISK"] = "Icelandic krona";
		$currencyOptions["KZT"] = "Kazakhstani tenge";
		$currencyOptions["KGS"] = "Kyrgyzstani som";
		$currencyOptions["LVL"] = "Latvian lats";
		$currencyOptions["LTL"] = "Lithuanian litas";
		$currencyOptions["MXN"] = "Mexican peso";
		$currencyOptions["MDL"] = "Moldovan leu";
		$currencyOptions["NAD"] = "Namibian dollar";
		$currencyOptions["NIO"] = "Nicaraguan cordoba";
		$currencyOptions["UGX"] = "Ugandan shilling";
		$currencyOptions["MKD"] = "Macedonian denar";
		$currencyOptions["UYU"] = "Uruguayan peso";
		$currencyOptions["UZS"] = "Uzbekistani som";
		$currencyOptions["AZN"] = "Azerbaijani manat";
		$currencyOptions["GHS"] = "Ghanaian cedi";
		$currencyOptions["VEF"] = "Venezuelan bolivar";
		$currencyOptions["SDG"] = "Sudanese pound";
		$currencyOptions["UYI"] = "Uruguay Peso";
		$currencyOptions["MZN"] = "Mozambican metical";
		$currencyOptions["CHE"] = "WIR Euro";
		$currencyOptions["CHW"] = "WIR Franc";
		$currencyOptions["XAF"] = "Central African CFA franc";
		$currencyOptions["XCD"] = "East Caribbean dollar";
		$currencyOptions["XOF"] = "West African CFA franc";
		$currencyOptions["XPF"] = "CFP franc";
		$currencyOptions["SRD"] = "Surinamese dollar";
		$currencyOptions["MGA"] = "Malagasy ariary";
		$currencyOptions["COU"] = "Unidad de Valor Real";
		$currencyOptions["AFN"] = "Afghan afghani";
		$currencyOptions["TJS"] = "Tajikistani somoni";
		$currencyOptions["AOA"] = "Angolan kwanza";
		$currencyOptions["BYR"] = "Belarusian ruble";
		$currencyOptions["BGN"] = "Bulgarian lev";
		$currencyOptions["CDF"] = "Congolese franc";
		$currencyOptions["BAM"] = "Bosnia and Herzegovina convert";
		$currencyOptions["MXV"] = "Mexican Unid";
		$currencyOptions["UAH"] = "Ukrainian hryvnia";
		$currencyOptions["GEL"] = "Georgian lari";
		$currencyOptions["BOV"] = "Mvdol";
		
		asort($currencyOptions);
		
		$this->_configs->lists['currency'] = JHTML::_("select.genericlist", $currencyOptions, "currency", "size=1", "value", "text", $this->_configs->currency);		
		$emails = $this->getEmails();
		
		//start date format list
		$dateOptions[]=JHTML::_('select.option','m/d/Y H:i:s','mm/dd/yyyy hh:mm:ss');
		$dateOptions[]=JHTML::_('select.option','Y-m-d H:i:s','yyyy-mm-dd hh:mm:ss');
		$dateOptions[]=JHTML::_('select.option','d.m.Y H:i','dd.mm.yyyy hh:mm');
		$dateOptions[]=JHTML::_('select.option','d-m-Y','dd-mm-yyyy');
		$dateOptions[]=JHTML::_('select.option','m/d/Y','mm/dd/yyyy');	
		$dateOptions[]=JHTML::_('select.option','Y-m-d','yyyy-mm-dd');			
					
		$this->_configs->lists['date_format']  =  JHTML::_( 'select.genericlist', $dateOptions, 'datetype', 'class="inputbox" size="1"','value', 'text', $this->_configs->datetype);	
		$targetOptions[]=JHTML::_('select.option','0',JText::_('GURU_SAME_WINDOW'));
		$targetOptions[]=JHTML::_('select.option','1',JText::_('GURU_NEW_WINDOW'));
			
		$this->_configs->lists['target']  =  JHTML::_( 'select.genericlist', $targetOptions, 'open_target', 'class="inputbox" size="1"','value', 'text', $this->_configs->open_target);
		
		$lesson_values_string = $this->_configs->lesson_window_size;
		$lesson_values_array = explode("x", $lesson_values_string);
		$lesson_height = $lesson_values_array["0"];
		$lesson_width = $lesson_values_array["1"];
		$this->_configs->lists["lesson_window_size"] = '
		<div>
			<div style="float:left;">
				<input type="text" size="5" name="lesson_window_size_width" value="'.$lesson_width.'" />
			</div>
			<div style="float:left; margin-right:-2px;"> &nbsp; 
				x &nbsp;
			</div>
			<div style="float:left;">
				<input type="text" size="5" name="lesson_window_size_height" value="'.$lesson_height.'" />
			</div>
			<div style="float:left;"> &nbsp; 
				('.JText::_("GURU_WIDTH").' x '.JText::_("GURU_HEIGHT").')&nbsp;
			</div>
		</div>
		';
		
		$lesson_values_string = $this->_configs->lesson_window_size_back;
		$lesson_values_array = explode("x", $lesson_values_string);
		$lesson_height = $lesson_values_array["0"];
		$lesson_width = $lesson_values_array["1"];
		$back_size_type = $this->_configs->back_size_type;
		$checked_joomla = $back_size_type == "0" ? ' checked="checked" ' : "";
		$checked_user = $back_size_type == "1" ? ' checked="checked" ' : "";
		
		$this->_configs->lists["lesson_window_size_back"] = '<div ><input style="width:18px!important;" type="radio" name="back_size_type" value="0" '.$checked_joomla.'>'.JText::_("GURU_BACK_SIZE_TYPE").'</div>
																			<div style="float:left;">
																				<input style="width:18px!important;" type="radio" name="back_size_type" value="1" '.$checked_user.'>
																				<input type="text" size="5" name="lesson_window_size_back_width" value="'.$lesson_width.'" />
																			</div>
																			<div style="float:left; margin-right:-2px;">&nbsp; 
																				x &nbsp;
																			</div>
																			<div style="float:left;">
																				<input type="text" size="5" name="lesson_window_size_back_height" value="'.$lesson_height.'" />
																			</div>
																			<div style="float:left;"> &nbsp;
																				('.JText::_("GURU_WIDTH").' x '.JText::_("GURU_HEIGHT").') &nbsp;
																			</div>
																		</div>';
		
		$default_video_string = $this->_configs->default_video_size ;
		$default_video_array = explode("x", $default_video_string);
		$default_video_height = $default_video_array["0"];
		$default_video_width = $default_video_array["1"];
		$this->_configs->lists["lesson_default_video_size"] = '
		<div>
			<!--
			<div style="float:left;">
				<input type="text" size="5" name="default_video_size_width" value="'.$default_video_width.'" />
			</div>
			<div style="float:left;">&nbsp; 
				x &nbsp;
			</div>
			-->
			<div style="float:left;">
				<input type="text" size="5" name="default_video_size_height" value="'.$default_video_height.'" />
			</div>
			<div style="float:left;"> &nbsp; 
				('.JText::_("GURU_HEIGHT").') &nbsp;
			</div>
		</div>
		';
		
		$hour_format[]=JHTML::_('select.option', '12', JText::_("GURU_TWELVE"));
		$hour_format[]=JHTML::_('select.option', '24', JText::_("GURU_TWENTY_FOUR"));
		$this->_configs->lists['hour_format']  =  JHTML::_( 'select.genericlist', $hour_format, 'hour_format', 'class="inputbox" size="1"','value', 'text', $this->_configs->hour_format);
		
		return $this->_configs;
	}
	function getMultipleProfileJomSocial(){
		$db = JFactory::getDBO();
		$ask = "SELECT id, name FROM `#__community_profiles` where published=1";
		$db->setQuery( $ask );
		$result = $db->loadObjectList();
		return $result;
	}

	function getAdmins() {
		$db = JFactory::getDBO();
		$sql = "select u.`id`, u.`name` from #__users u, #__user_usergroup_map ugm where u.`id`=ugm.`user_id` and ugm.`group_id` in ('7', '8')";
		$db->setQuery($sql);
		$db->execute();
		$admins = $db->loadObjectList();
		return $admins;
	}
	function getEmails() {
		$db = JFactory::getDBO();
		$ask = "SELECT * FROM `#__guru_emails` ";
		$db->setQuery( $ask );
		$where = $db->loadObjectList();
		return $where;
	}
	function getJsMProfile(){
		$db = JFactory::getDBO();
		$sql = "select count(id) from #__community_profiles";
		$db->setQuery($sql);
		$db->execute();
		$count = $db->loadColumn();
		return $count["0"];	
	}

	function store () {
		jimport('joomla.filesystem.folder');
		$item = $this->getTable('guruConfig');
		$tab = JFactory::getApplication()->input->get("tab", "0");
		$data_post = JFactory::getApplication()->input->post->getArray();
		
		if($tab == "3"){
			// we update the CSS FILE - begin
			$css=JPATH_SITE."/components/com_guru/css/trainer_style.css" ;
			@chmod($css,0777);
			$fp=@fopen($css,"w");
			fwrite($fp,stripslashes($data_post['css_file']));
			fclose($fp);
			// we update the CSS FILE - end
		}

		$imagepath = JPATH_SITE;
		
		$imagesin = JFactory::getApplication()->input->get("imagesin", "", "raw");
		
		if($imagesin != ''){
			JFolder::create($imagepath."/".$imagesin, "0755");
	    }
		
		$videoin = JFactory::getApplication()->input->get("videoin", "", "raw");
		
		if($videoin != ''){
			JFolder::create($imagepath."/".$videoin, "0755");
	    }
		
		$audioin = JFactory::getApplication()->input->get("audioin", "", "raw");
		
		if($audioin != ''){
			JFolder::create($imagepath."/".$audioin, "0755");
	    }
		
		$docsin = JFactory::getApplication()->input->get("docsin", "", "raw");
		
		if($docsin != ''){
			JFolder::create($imagepath."/".$docsin, "0755");
	    }
		
		$filesin = JFactory::getApplication()->input->get("filesin", "", "raw");
		
		if($filesin != ''){
			JFolder::create($imagepath."/".$filesin, "0755");
	    }
	
		if( !is_file(JPATH_SITE.DIRECTORY_SEPARATOR.$imagesin.DIRECTORY_SEPARATOR."certificates".DIRECTORY_SEPARATOR."thumbs") ){
			JFolder::create(JPATH_SITE.DIRECTORY_SEPARATOR.$imagesin.DIRECTORY_SEPARATOR."certificates".DIRECTORY_SEPARATOR."thumbs", "0755");
		}
		
		if( !is_file(JPATH_SITE.DIRECTORY_SEPARATOR.$imagesin.DIRECTORY_SEPARATOR."media".DIRECTORY_SEPARATOR."thumbs") ){
			JFolder::create(JPATH_SITE.DIRECTORY_SEPARATOR.$imagesin.DIRECTORY_SEPARATOR."media".DIRECTORY_SEPARATOR."thumbs", "0755");
		}
		
		$data = JFactory::getApplication()->input->post->getArray();			
		$ctgslayout = JFactory::getApplication()->input->get("ctgslayout", "", "raw");
		$ctgscols = JFactory::getApplication()->input->get("ctgscols", "", "raw");
		$ctgs_image_size = JFactory::getApplication()->input->get("ctgs_image_size", "", "raw");
		$ctgs_image_size_type = JFactory::getApplication()->input->get("ctgs_image_size_type", "", "raw");
		$ctgs_image_alignment = JFactory::getApplication()->input->get("ctgs_image_alignment", "", "raw");
		$ctgs_wrap_image = JFactory::getApplication()->input->get("ctgs_wrap_image", "", "raw");
		$ctgs_description_length = JFactory::getApplication()->input->get("ctgs_description_length", "", "raw");
		$ctgs_description_type =JFactory::getApplication()->input->get("ctgs_description_type", "", "raw");
		$ctgs_description_mode = JFactory::getApplication()->input->get("ctgs_description_mode", "", "raw");
		$ctgs_description_alignment = JFactory::getApplication()->input->get("ctgs_description_alignment", "", "raw");
		$ctgs_read_more = JFactory::getApplication()->input->get("ctgs_read_more", "", "raw");
		$ctgs_read_more_align = JFactory::getApplication()->input->get("ctgs_read_more_align", "", "raw");
		$ctgs_show_empty_catgs = JFactory::getApplication()->input->get("ctgs_show_empty_catgs", "", "raw");
		$ctgspage_array = array("ctgslayout" => $ctgslayout, "ctgscols" => $ctgscols, "ctgs_image_size" => $ctgs_image_size, "ctgs_image_size_type" => $ctgs_image_size_type, "ctgs_image_alignment" => $ctgs_image_alignment, "ctgs_wrap_image" => $ctgs_wrap_image, "ctgs_description_length" => $ctgs_description_length, "ctgs_description_type" =>$ctgs_description_type, "ctgs_description_mode" => $ctgs_description_mode, "ctgs_description_alignment"=> $ctgs_description_alignment, "ctgs_read_more" => $ctgs_read_more, "ctgs_read_more_align" => $ctgs_read_more_align, "ctgs_show_empty_catgs" => $ctgs_show_empty_catgs);
		$data["ctgspage"] = json_encode($ctgspage_array);
		
		$ctgs_page_title = JFactory::getApplication()->input->get("ctgs_page_title", "", "raw");
		$ctgs_categ_name = JFactory::getApplication()->input->get("ctgs_categ_name", "", "raw");
		$ctgs_image = JFactory::getApplication()->input->get("ctgs_image", "", "raw");
		$ctgs_description = JFactory::getApplication()->input->get("ctgs_description", "", "raw");
		$ctgs_st_read_more = JFactory::getApplication()->input->get("ctgs_st_read_more", "", "raw");
		$st_ctgspage_array = array("ctgs_page_title" => $ctgs_page_title, "ctgs_categ_name" => $ctgs_categ_name, "ctgs_image" => $ctgs_image, "ctgs_description" => $ctgs_description, "ctgs_st_read_more" => $ctgs_st_read_more);
		$data["st_ctgspage"] = json_encode($st_ctgspage_array);
		//-----------------------------------------------------
		$ctg_image_size = JFactory::getApplication()->input->get("ctg_image_size", "", "raw");
		$ctg_image_size_type = JFactory::getApplication()->input->get("ctg_image_size_type", "", "raw");
		$ctg_image_alignment = JFactory::getApplication()->input->get("ctg_image_alignment", "", "raw");
		$ctg_description_length = JFactory::getApplication()->input->get("ctg_description_length", "", "raw");
		$ctg_description_type = JFactory::getApplication()->input->get("ctg_description_type", "", "raw");
		$ctg_description_mode = JFactory::getApplication()->input->get("ctg_description_mode", "", "raw");
		$ctg_description_alignment = JFactory::getApplication()->input->get("ctg_description_alignment", "", "raw");
		$ctg_students_number = JFactory::getApplication()->input->get("ctg_students_number", "", "raw");
		
		$ctgpage_array = array("ctg_image_size" => $ctg_image_size, "ctg_image_size_type" => $ctg_image_size_type, "ctg_image_alignment" => $ctg_image_alignment, "ctg_description_length" => $ctg_description_length, "ctg_description_mode" => $ctg_description_mode, "ctg_description_type" => $ctg_description_type, "ctg_description_alignment" => $ctg_description_alignment, "ctg_students_number" => $ctg_students_number);
		$data["ctgpage"] = json_encode($ctgpage_array);
		
		$ctg_name = JFactory::getApplication()->input->get("ctg_name", "", "raw");
		$ctg_image = JFactory::getApplication()->input->get("ctg_image", "", "raw");
		$ctg_description = JFactory::getApplication()->input->get("ctg_description", "", "raw");
		$ctg_sub_title = JFactory::getApplication()->input->get("ctg_sub_title", "", "raw");
		$st_ctgpage_array = array("ctg_name" => $ctg_name, "ctg_image" => $ctg_image, "ctg_description" => $ctg_description, "ctg_sub_title" => $ctg_sub_title);
		$data["st_ctgpage"] = json_encode($st_ctgpage_array);
		//-----------------------------------------------------
		$courseslayout = JFactory::getApplication()->input->get("courseslayout", "", "raw");
		$coursescols = JFactory::getApplication()->input->get("coursescols", "", "raw");
		$courses_image_size = JFactory::getApplication()->input->get("courses_image_size", "", "raw");
		$courses_image_size_type = JFactory::getApplication()->input->get("courses_image_size_type", "", "raw");
		$courses_image_alignment = JFactory::getApplication()->input->get("courses_image_alignment", "", "raw");
		$courses_wrap_image = JFactory::getApplication()->input->get("courses_wrap_image", "", "raw");
		$courses_description_length = JFactory::getApplication()->input->get("courses_description_length", "", "raw");
		$courses_description_type = JFactory::getApplication()->input->get("courses_description_type", "", "raw");
		$courses_description_mode = JFactory::getApplication()->input->get("courses_description_mode", "", "raw");
		$courses_description_alignment = JFactory::getApplication()->input->get("courses_description_alignment", "", "raw");
		$courses_read_more = JFactory::getApplication()->input->get("courses_read_more", "", "raw");
		$courses_read_more_align = JFactory::getApplication()->input->get("courses_read_more_align", "", "raw");
		$psgspage_array = array("courseslayout" => $courseslayout, "coursescols" => $coursescols, "courses_image_size" => $courses_image_size, "courses_image_size_type" => $courses_image_size_type, "courses_image_alignment" => $courses_image_alignment, "courses_wrap_image" => $courses_wrap_image, "courses_description_length" => $courses_description_length, "courses_description_type" => $courses_description_type, "courses_description_mode" => $courses_description_mode, "courses_description_alignment" => $courses_description_alignment, "courses_read_more" => $courses_read_more, "courses_read_more_align" => $courses_read_more_align);
		$data["psgspage"] = json_encode($psgspage_array);
		
		$courses_page_title = JFactory::getApplication()->input->get("courses_page_title", "", "raw");
		$courses_name = JFactory::getApplication()->input->get("courses_name", "", "raw");
		$courses_image = JFactory::getApplication()->input->get("courses_image", "", "raw");
		$courses_description = JFactory::getApplication()->input->get("courses_description", "", "raw");
		$courses_st_read_more = JFactory::getApplication()->input->get("courses_st_read_more", "", "raw");
		$st_psgspage_array = array("courses_page_title" => $courses_page_title, "courses_name" => $courses_name, "courses_image" => $courses_image, "courses_description" => $courses_description, "courses_st_read_more" => $courses_st_read_more);
		$data["st_psgspage"] = json_encode($st_psgspage_array);
		//-----------------------------------------------------
		$course_image_size = JFactory::getApplication()->input->get("course_image_size", "", "raw");
		$course_image_size_type = JFactory::getApplication()->input->get("course_image_size_type", "", "raw");
		$course_image_alignment = JFactory::getApplication()->input->get("course_image_alignment", "", "raw");
		$course_wrap_image = JFactory::getApplication()->input->get("course_wrap_image", "", "raw");
		$show_course_image = JFactory::getApplication()->input->get("show_course_image", "", "raw");
		$show_course_studentamount = JFactory::getApplication()->input->get("show_course_studentamount", "", "raw");
		$course_author_name_show = JFactory::getApplication()->input->get("course_author_name_show", "", "raw");
		$course_released_date = JFactory::getApplication()->input->get("course_released_date", "", "raw");
		$duration = JFactory::getApplication()->input->get("duration", "", "raw");
		$quiz_status = JFactory::getApplication()->input->get("quiz_status", "0", "raw");
		$course_level = JFactory::getApplication()->input->get("course_level", "", "raw");
		$course_price = JFactory::getApplication()->input->get("course_price", "", "raw");
		$course_price_type = JFactory::getApplication()->input->get("course_price_type", "", "raw");
		$course_table_contents = JFactory::getApplication()->input->get("course_table_contents", "", "raw");
		$course_description_show = JFactory::getApplication()->input->get("course_description_show", "", "raw");
		$course_tab_price = JFactory::getApplication()->input->get("course_tab_price", "", "raw");
		$course_author = JFactory::getApplication()->input->get("course_author", "", "raw");
		$course_requirements = JFactory::getApplication()->input->get("course_requirements", "", "raw");
		$course_buy_button = JFactory::getApplication()->input->get("course_buy_button", "", "raw");
		$course_buy_button_location = JFactory::getApplication()->input->get("course_buy_button_location", "", "raw");
		$show_all_cloase_all = JFactory::getApplication()->input->get("show_all_cloase_all", "", "raw");
		$show_all_cloase_all_default = JFactory::getApplication()->input->get("show_all_cloase_all_default", "", "raw");

		$course_table_contents_ordering = JFactory::getApplication()->input->get("course_table_contents_ordering", "0", "raw");
		$course_description_show_ordering = JFactory::getApplication()->input->get("course_description_show_ordering", "0", "raw");
		$course_tab_price_ordering = JFactory::getApplication()->input->get("course_tab_price_ordering", "0", "raw");
		$course_author_ordering = JFactory::getApplication()->input->get("course_author_ordering", "0", "raw");
		$course_requirements_ordering = JFactory::getApplication()->input->get("course_requirements_ordering", "0", "raw");
		$course_exercises_ordering = JFactory::getApplication()->input->get("course_exercises_ordering", "0", "raw");
		$default_active_tab = JFactory::getApplication()->input->get("default_active_tab", "1", "raw");

		$psgpage_array = array("course_image_size" => $course_image_size, "course_image_size_type" => $course_image_size_type, "course_image_alignment" => $course_image_alignment, "course_wrap_image" => $course_wrap_image, "course_author_name_show" => $course_author_name_show, "course_released_date" => $course_released_date, "course_level" => $course_level, "course_price" => $course_price, "course_price_type" => $course_price_type, "course_table_contents" => $course_table_contents, "course_description_show" => $course_description_show, "course_tab_price" => $course_tab_price, "course_author" => $course_author, "course_requirements" => $course_requirements, "course_buy_button" => $course_buy_button, "course_buy_button_location" => $course_buy_button_location, "show_course_image" => $show_course_image, "show_course_studentamount" => $show_course_studentamount, "show_all_cloase_all" => $show_all_cloase_all, "show_all_cloase_all_default"=>$show_all_cloase_all_default, "duration"=>$duration, "quiz_status"=>$quiz_status, "course_table_contents_ordering"=>$course_table_contents_ordering, "course_description_show_ordering"=>$course_description_show_ordering, "course_tab_price_ordering"=>$course_tab_price_ordering, "course_author_ordering"=>$course_author_ordering, "course_requirements_ordering"=>$course_requirements_ordering, "course_exercises_ordering"=>$course_exercises_ordering, "default_active_tab"=>$default_active_tab);
		$data["psgpage"] = json_encode($psgpage_array);
		
		$course_name = JFactory::getApplication()->input->get("course_name", "", "raw");
		$course_image = JFactory::getApplication()->input->get("course_image", "", "raw");
		$course_top_field_name = JFactory::getApplication()->input->get("course_top_field_name", "", "raw");
		$course_top_field_value = JFactory::getApplication()->input->get("course_top_field_value", "", "raw");
		$course_tabs_module_name = JFactory::getApplication()->input->get("course_tabs_module_name", "", "raw");
		$course_tabs_step_name = JFactory::getApplication()->input->get("course_tabs_step_name", "", "raw");
		$course_description = JFactory::getApplication()->input->get("course_description", "", "raw");
		$course_price_field_name = JFactory::getApplication()->input->get("course_price_field_name", "", "raw");
		$course_price_field_value = JFactory::getApplication()->input->get("course_price_field_value", "", "raw");
		$course_author_name = JFactory::getApplication()->input->get("course_author_name", "", "raw");
		$course_author_bio = JFactory::getApplication()->input->get("course_author_bio", "", "raw");
		$course_author_image = JFactory::getApplication()->input->get("course_author_image", "", "raw");
		$course_req_field_name = JFactory::getApplication()->input->get("course_req_field_name", "", "raw");
		$course_req_field_value = JFactory::getApplication()->input->get("course_req_field_value", "", "raw");
		$course_other_button = JFactory::getApplication()->input->get("course_other_button", "", "raw");
		$course_other_background = JFactory::getApplication()->input->get("course_other_background", "", "raw");
		$st_psgpage_array = array("course_name" => $course_name, "course_image" => $course_image, "course_top_field_name" => $course_top_field_name, "course_top_field_value" => $course_top_field_value, "course_tabs_module_name" => $course_tabs_module_name, "course_tabs_step_name" => $course_tabs_step_name, "course_description" => $course_description, "course_price_field_name" => $course_price_field_name, "course_price_field_value" => $course_price_field_value, "course_author_name" => $course_author_name, "course_author_bio" => $course_author_bio, "course_author_image" => $course_author_image, "course_req_field_name" => $course_req_field_name, "course_req_field_value" => $course_req_field_value, "course_other_button" => $course_other_button, "course_other_background" => $course_other_background);
		$data["st_psgpage"] = json_encode($st_psgpage_array);
		//-----------------------------------------------------
		$authorslayout = JFactory::getApplication()->input->get("authorslayout", "", "raw");
		$authorscols = JFactory::getApplication()->input->get("authorscols", "", "raw");
		$authors_image_size = JFactory::getApplication()->input->get("authors_image_size", "", "raw");
		$authors_image_size_type = JFactory::getApplication()->input->get("authors_image_size_type", "", "raw");
		$authors_image_alignment = JFactory::getApplication()->input->get("authors_image_alignment", "", "raw");
		$authors_wrap_image = JFactory::getApplication()->input->get("authors_wrap_image", "", "raw");
		$authors_description_length = JFactory::getApplication()->input->get("authors_description_length", "", "raw");
		$authors_description_mode = JFactory::getApplication()->input->get("authors_description_mode", "", "raw");
		$authors_description_type = JFactory::getApplication()->input->get("authors_description_type", "", "raw");
		$authors_description_alignment = JFactory::getApplication()->input->get("authors_description_alignment", "", "raw");
		$authors_read_more = JFactory::getApplication()->input->get("authors_read_more", "", "raw");
		$authors_read_more_align = JFactory::getApplication()->input->get("authors_read_more_align", "", "raw");
		$authorspage_array = array("authorslayout" => $authorslayout, "authorscols" => $authorscols, "authors_image_size" => $authors_image_size, "authors_image_size_type" => $authors_image_size_type, "authors_image_alignment" => $authors_image_alignment, "authors_wrap_image" => $authors_wrap_image, "authors_description_length" => $authors_description_length, "authors_description_type" => $authors_description_type, "authors_description_mode" => $authors_description_mode, "authors_description_alignment" => $authors_description_alignment, "authors_read_more" => $authors_read_more, "authors_read_more_align" => $authors_read_more_align);
		$data["authorspage"] = json_encode($authorspage_array);
				
		$authors_page_title = JFactory::getApplication()->input->get("authors_page_title", "", "raw");
		$authors_name = JFactory::getApplication()->input->get("authors_name", "", "raw");
		$authors_image = JFactory::getApplication()->input->get("authors_image", "", "raw");
		$authors_description = JFactory::getApplication()->input->get("authors_description", "", "raw");
		$authors_st_read_more = JFactory::getApplication()->input->get("authors_st_read_more", "", "raw");
		$st_authorspage_array = array("authors_page_title" => $authors_page_title, "authors_name" => $authors_name, "authors_image" => $authors_image, "authors_description" => $authors_description, "authors_st_read_more" => $authors_st_read_more);
		$data["st_authorspage"] = json_encode($st_authorspage_array);
		//-----------------------------------------------------
		$author_image_size = JFactory::getApplication()->input->get("author_image_size", "", "raw");
		$author_image_size_type = JFactory::getApplication()->input->get("author_image_size_type", "", "raw");
		$author_image_alignment = JFactory::getApplication()->input->get("author_image_alignment", "", "raw");
		$author_wrap_image = JFactory::getApplication()->input->get("author_wrap_image", "", "raw");
		$author_description_length = JFactory::getApplication()->input->get("author_description_length", "", "raw");
		$author_description_type = JFactory::getApplication()->input->get("author_description_type", "", "raw");
		$author_description_alignment = JFactory::getApplication()->input->get("author_description_alignment", "", "raw");
		$authorpage_array = array("author_image_size" => $author_image_size, "author_image_size_type" => $author_image_size_type, "author_image_alignment" => $author_image_alignment, "author_wrap_image" => $author_wrap_image, "author_description_length" => $author_description_length, "author_description_type" => $author_description_type, "author_description_alignment" => $author_description_alignment);
		$data["authorpage"] = json_encode($authorpage_array);
		
		$author_name = JFactory::getApplication()->input->get("author_name", "", "raw");
		$author_image = JFactory::getApplication()->input->get("author_image", "", "raw");
		$author_description = JFactory::getApplication()->input->get("author_description", "", "raw");
		$author_st_read_more = JFactory::getApplication()->input->get("author_st_read_more", "", "raw");
		//add new columns for teacher page in admin(confis->tab8)
		$teacher_aprove = JFactory::getApplication()->input->get("teacher_aprove", "", "raw");
		$teacher_redirect = JFactory::getApplication()->input->get("teacher_redirect", "", "raw");
		$teacher_redirect_logout = JFactory::getApplication()->input->get("teacher_redirect_logout", "", "raw");
		$teacher_editor = JFactory::getApplication()->input->get("teacher_editor", "0", "raw");
		$teacher_group = JFactory::getApplication()->input->get("teacher_group", "", "raw");
		$teacher_add_media = JFactory::getApplication()->input->get("teacher_add_media", "", "raw");
		$teacher_edit_media = JFactory::getApplication()->input->get("teacher_edit_media", "", "raw"); 
		$teacher_add_courses = JFactory::getApplication()->input->get("teacher_add_courses", "", "raw");
		$teacher_approve_courses = JFactory::getApplication()->input->get("teacher_approve_courses", "", "raw"); 
		$teacher_edit_courses = JFactory::getApplication()->input->get("teacher_edit_courses", "", "raw");
		$teacher_publish_courses = JFactory::getApplication()->input->get("teacher_publish_courses", "", "raw");
		$teacher_unpublish_courses = JFactory::getApplication()->input->get("teacher_unpublish_courses", "", "raw");
		$teacher_delete_courses = JFactory::getApplication()->input->get("teacher_delete_courses", "", "raw");
		$teacher_add_quizzesfe = JFactory::getApplication()->input->get("teacher_add_quizzesfe", "", "raw");
		$teacher_edit_quizzesfe = JFactory::getApplication()->input->get("teacher_edit_quizzesfe", "", "raw");
		$teacher_add_students =  JFactory::getApplication()->input->get("teacher_add_students", "", "raw");
		$teacher_edit_students = JFactory::getApplication()->input->get("teacher_edit_students", "", "raw");

		$teacher_menu_courses = JFactory::getApplication()->input->get("teacher_menu_courses", "", "raw");
		$teacher_menu_students = JFactory::getApplication()->input->get("teacher_menu_students", "", "raw");
		$teacher_menu_projects = JFactory::getApplication()->input->get("teacher_menu_projects", "", "raw");
		$teacher_menu_scorm = JFactory::getApplication()->input->get("teacher_menu_scorm", "", "raw");
		$teacher_menu_quizzes = JFactory::getApplication()->input->get("teacher_menu_quizzes", "", "raw");
		$teacher_menu_media = JFactory::getApplication()->input->get("teacher_menu_media", "", "raw");
		$teacher_menu_commissions = JFactory::getApplication()->input->get("teacher_menu_commissions", "", "raw");
		$teacher_menu_grade = JFactory::getApplication()->input->get("teacher_menu_grade", "", "raw");

		//----------end new columns-----------
		$st_authorpage_array = array("author_name" => $author_name, "author_image" => $author_image, "author_description" => $author_description, "author_st_read_more" => $author_st_read_more, "teacher_aprove"=>$teacher_aprove, "teacher_redirect"=>$teacher_redirect, "teacher_redirect_logout"=>$teacher_redirect_logout, "teacher_editor"=>$teacher_editor, "teacher_group"=>$teacher_group, "teacher_add_media"=>$teacher_add_media, "teacher_edit_media"=>$teacher_edit_media, "teacher_add_courses"=>$teacher_add_courses, "teacher_approve_courses"=>$teacher_approve_courses, "teacher_edit_courses"=>$teacher_edit_courses, "teacher_publish_courses"=>$teacher_publish_courses, "teacher_unpublish_courses"=>$teacher_unpublish_courses, "teacher_delete_courses"=>$teacher_delete_courses, "teacher_add_quizzesfe"=>$teacher_add_quizzesfe, "teacher_edit_quizzesfe"=>$teacher_edit_quizzesfe, "teacher_add_students"=>$teacher_add_students, "teacher_edit_students"=>$teacher_edit_students, "teacher_menu_courses"=>$teacher_menu_courses, "teacher_menu_students"=>$teacher_menu_students, "teacher_menu_projects"=>$teacher_menu_projects, "teacher_menu_scorm"=>$teacher_menu_scorm, "teacher_menu_quizzes"=>$teacher_menu_quizzes, "teacher_menu_media"=>$teacher_menu_media, "teacher_menu_commissions"=>$teacher_menu_commissions, "teacher_menu_grade"=>$teacher_menu_grade);
		$data["st_authorpage"] = json_encode($st_authorpage_array);
		//-----------------------------------------------------
		
		$data['st_donecolor'] = '#'.$data['st_donecolor'];
		$data['st_notdonecolor'] = '#'.$data['st_notdonecolor'];
		$data['st_txtcolor'] = '#'.$data['st_txtcolor'];
		
		$database =  JFactory::getDBO();				
		
		$for_save = array("id"=>"1", "option"=>"com_guru", "controller"=>"guruConfigs");
		
		$seo_configs = array("seo"=>$data["seo"], "itemid"=>$data["selections"]);
		
		$data["seo"] = json_encode($seo_configs);
		
		//save only values from current tab
		if($tab == "0"){
			if(!isset($data["show_decimals"])){
				$data["show_decimals"] = "1";
			}
			
			$for_save["currency"] = $data["currency"];
			$for_save["datetype"] = $data["datetype"];
			$for_save["hour_format"] = $data["hour_format"];
			$for_save["open_target"] = "1"; //$data["open_target"];
			$for_save["lesson_window_size_back"] = intval($data["lesson_window_size_back_height"])."x".intval($data["lesson_window_size_back_width"]);
			$for_save["lesson_window_size"] = intval($data["lesson_window_size_height"])."x".intval($data["lesson_window_size_width"]);
			$for_save["default_video_size"] = intval($data["default_video_size_height"])."x".intval($data["default_video_size_width"]);
			$for_save["notification"] = $data["notification"];
			$for_save["show_bradcrumbs"] = $data["show_bradcrumbs"];
			$for_save["show_powerd"] = $data["show_powerd"];
			$for_save["guru_ignore_ijseo"] = $data["guru_ignore_ijseo"];
			$for_save["student_group"] = $data["student_group"];
			$for_save["currencypos"] = $data["currencypos"];
			$for_save["thousands_separator"] = $data["thousands_separator"];
			$for_save["decimals_separator"] = $data["decimals_separator"];
			$for_save["show_decimals"] = $data["show_decimals"];
			$for_save["back_size_type"] = intval($data["back_size_type"]);
			$for_save["guru_turnoffjq"] = intval($data["guru_turnoffjq"]);
			$for_save["show_bootstrap"] = intval($data["show_bootstrap"]);
			$for_save["indicate_quiz"] = intval($data["indicate_quiz"]);
			$for_save["captcha"] = intval($data["captcha"]);
			$for_save["auto_approve"] = intval($data["auto_approve"]);
			$for_save["certificate_html"] = intval($data["certificate_html"]);
			$for_save["certificate_pdf"] = intval($data["certificate_pdf"]);
			$for_save["certificate_share"] = intval($data["certificate_share"]);
			$for_save["certificate_email"] = intval($data["certificate_email"]);
			$for_save["youtube_key"] = trim($data["youtube_key"]);
			$for_save["invoice_issued_by"] = JFactory::getApplication()->input->get('invoice_issued_by', '', 'raw');
			$for_save["rtl"] = intval($data["rtl"]);
			$for_save["guru_turnoffuikit"] = intval($data["guru_turnoffuikit"]);

			$student_menus = array();
			$student_menus["student_menu_profile"] = $data["student_menu_profile"];
			$student_menus["student_menu_courses"] = $data["student_menu_courses"];
			$student_menus["student_menu_orders"] = $data["student_menu_orders"];
			$student_menus["student_menu_quizzes"] = $data["student_menu_quizzes"];
			$student_menus["student_menu_projects"] = $data["student_menu_projects"];
			$student_menus["student_menu_certificates"] = $data["student_menu_certificates"];
			$student_menus["student_menu_cart"] = $data["student_menu_cart"];

			$for_save["student_menus"] = json_encode($student_menus);
		}
		elseif($tab == "1"){
			$for_save["imagesin"] = $data["imagesin"];
			$for_save["videoin"] = $data["videoin"];
			$for_save["audioin"] = $data["audioin"];
			$for_save["docsin"] = $data["docsin"];
			$for_save["filesin"] = $data["filesin"];
		}
		elseif($tab == "2"){
			$for_save["ctgspage"] = $data["ctgspage"];
			$for_save["ctgpage"] = $data["ctgpage"];
			$for_save["psgspage"] = $data["psgspage"];
			$for_save["psgpage"] = $data["psgpage"];
			$for_save["authorspage"] = $data["authorspage"];
			$for_save["authorpage"] = $data["authorpage"];
			$for_save["course_lesson_release"] = $data["course_lesson_release"];
			$for_save["course_certificate"] = $data["course_certificate"];
			$for_save["course_exercises"] = $data["course_exercises"];
		}
		elseif($tab == "3"){
			$for_save["st_ctgspage"] = $data["st_ctgspage"];
			$for_save["st_ctgpage"] = $data["st_ctgpage"];
			$for_save["st_psgspage"] = $data["st_psgspage"];
			$for_save["st_psgpage"] = $data["st_psgpage"];
			$for_save["st_authorspage"] = $data["st_authorspage"];
			$for_save["st_authorpage"] = $data["st_authorpage"];		
		}
		elseif($tab == "4"){
			if(($data["st_width"] <= 0) && ($data["st_width"] <= 0)){
				return false;										  
				
			}
			
			$for_save["progress_bar"] = $data["progress_bar"];
			$for_save["st_donecolor"] = $data["st_donecolor"];
			$for_save["st_notdonecolor"] = $data["st_notdonecolor"];
			$for_save["st_txtcolor"] = $data["st_txtcolor"];
			$for_save["st_width"] = $data["st_width"];
			$for_save["st_height"] = $data["st_height"];		
		}
		elseif($tab == "5"){
			$for_save["fromname"] = $data["fromname"];
			$for_save["fromemail"] = $data["fromemail"];
			$for_save["admin_email"] = implode(",", $data["cid"]);			
			
			$template_emails["approve_subject"] = JFactory::getApplication()->input->get('approve_subject', '', "raw");
			$template_emails["approve_body"] = JFactory::getApplication()->input->get('approve_body', '', "raw");
			$template_emails["unapprove_subject"] = JFactory::getApplication()->input->get('unapprove_subject', '', "raw");
			$template_emails["unapprove_body"] = JFactory::getApplication()->input->get('unapprove_body', '', "raw");
			$template_emails["ask_approve_subject"] = JFactory::getApplication()->input->get('ask_approve_subject', '', "raw");
			$template_emails["ask_approve_body"] = JFactory::getApplication()->input->get('ask_approve_body', '', "raw");
			$template_emails["ask_teacher_subject"] = JFactory::getApplication()->input->get('ask_teacher_subject', '', "raw");
			$template_emails["ask_teacher_body"] = JFactory::getApplication()->input->get('ask_teacher_body', '', "raw");
			$template_emails["new_teacher_subject"] = JFactory::getApplication()->input->get('new_teacher_subject', '', "raw");
			$template_emails["new_teacher_body"] = JFactory::getApplication()->input->get('new_teacher_body', '', "raw");
			$template_emails["new_student_subject"] = JFactory::getApplication()->input->get('new_student_subject', '', "raw");
			$template_emails["new_student_body"] = JFactory::getApplication()->input->get('new_student_body', '', "raw");
			$template_emails["approved_teacher_subject"] = JFactory::getApplication()->input->get('approved_teacher_subject', '', "raw");
			$template_emails["approved_teacher_body"] = JFactory::getApplication()->input->get('approved_teacher_body', '', "raw");
			$template_emails["pending_teacher_subject"] = JFactory::getApplication()->input->get('pending_teacher_subject', '', "raw");
			$template_emails["pending_teacher_body"] = JFactory::getApplication()->input->get('pending_teacher_body', '', "raw");
			$template_emails["approve_order_subject"] = JFactory::getApplication()->input->get('approve_order_subject', '', "raw");
			$template_emails["approve_order_body"] = JFactory::getApplication()->input->get('approve_order_body', '', "raw");
			$template_emails["pending_order_subject"] = JFactory::getApplication()->input->get('pending_order_subject', '', "raw");
			$template_emails["pending_order_body"] = JFactory::getApplication()->input->get('pending_order_body', '', "raw");
			$template_emails["chek_quiz_subject"] = JFactory::getApplication()->input->get('chek_quiz_subject', '', "raw");
			$template_emails["chek_quiz_body"] = JFactory::getApplication()->input->get('chek_quiz_body', '',"raw");
			$template_emails["feedback_subject"] = JFactory::getApplication()->input->get('feedback_subject', '', "raw");
			$template_emails["feedback_body"] = JFactory::getApplication()->input->get('feedback_body', '', "raw");
			$template_emails["review_quiz_subject"] = JFactory::getApplication()->input->get('review_quiz_subject', '', "raw");
			$template_emails["review_quiz_body"] = JFactory::getApplication()->input->get('review_quiz_body', '', "raw");
			$template_emails["new_student_enrolled_subject"] = JFactory::getApplication()->input->get('new_student_enrolled_subject', '', "raw");
			$template_emails["new_student_enrolled_body"] = JFactory::getApplication()->input->get('new_student_enrolled_body', '', "raw");
			$template_emails["new_student_enrolled_admin_subject"] = JFactory::getApplication()->input->get('new_student_enrolled_admin_subject', '', "raw");
			$template_emails["new_student_enrolled_admin_body"] = JFactory::getApplication()->input->get('new_student_enrolled_admin_body', '', "raw");
			$template_emails["teacher_completed_course_subject"] = JFactory::getApplication()->input->get('teacher_completed_course_subject', '', "raw");
			$template_emails["teacher_completed_course_body"] = JFactory::getApplication()->input->get('teacher_completed_course_body', '', "raw");
			$template_emails["admin_completed_course_subject"] = JFactory::getApplication()->input->get('admin_completed_course_subject', '', "raw");
			$template_emails["admin_completed_course_body"] = JFactory::getApplication()->input->get('admin_completed_course_body', '', "raw");

			$template_emails["send_teacher_email_course_approved"] = JFactory::getApplication()->input->get('send_teacher_email_course_approved', '1', "raw");
			$template_emails["send_teacher_email_course_unapproved"] = JFactory::getApplication()->input->get('send_teacher_email_course_unapproved', '1', "raw");
			$template_emails["send_teacher_email_teacher_approved"] = JFactory::getApplication()->input->get('send_teacher_email_teacher_approved', '1', "raw");
			$template_emails["send_teacher_email_teacher_pending"] = JFactory::getApplication()->input->get('send_teacher_email_teacher_pending', '1', "raw");
			$template_emails["send_teacher_email_review_quiz"] = JFactory::getApplication()->input->get('send_teacher_email_review_quiz', '1', "raw");
			$template_emails["send_teacher_email_student_enrolled"] = JFactory::getApplication()->input->get('send_teacher_email_student_enrolled', '1', "raw");
			$template_emails["send_teacher_email_course_finished"] = JFactory::getApplication()->input->get('send_teacher_email_course_finished', '1', "raw");
			$template_emails["send_admin_email_course_approved"] = JFactory::getApplication()->input->get('send_admin_email_course_approved', '1', "raw");
			$template_emails["send_admin_email_teacher_approved"] = JFactory::getApplication()->input->get('send_admin_email_teacher_approved', '1', "raw");
			$template_emails["send_admin_email_teacher_registered"] = JFactory::getApplication()->input->get('send_admin_email_teacher_registered', '1', "raw");
			$template_emails["send_admin_email_student_registered"] = JFactory::getApplication()->input->get('send_admin_email_student_registered', '1', "raw");
			$template_emails["send_admin_email_pending_order"] = JFactory::getApplication()->input->get('send_admin_email_pending_order', '1', "raw");
			$template_emails["send_admin_email_student_enrolled"] = JFactory::getApplication()->input->get('send_admin_email_student_enrolled', '1', "raw");
			$template_emails["send_admin_email_course_finished"] = JFactory::getApplication()->input->get('send_admin_email_course_finished', '1', "raw");
			$template_emails["send_student_email_order_approved"] = JFactory::getApplication()->input->get('send_student_email_order_approved', '1', "raw");
			$template_emails["send_student_email_checked_results"] = JFactory::getApplication()->input->get('send_student_email_checked_results', '1', "raw");
			$template_emails["send_student_email_modified_feedback"] = JFactory::getApplication()->input->get('send_student_email_modified_feedback', '1', "raw");
			
			$for_save["template_emails"] = json_encode($template_emails);
		}
		elseif($tab == "6"){
			$item->content_selling = JFactory::getApplication()->input->get('content_selling',"","raw");
		}	
		elseif($tab == "7"){
			$for_save["gurujomsocialregstudent"] = $data["gurujomsocialregstudent"];
			$for_save["gurujomsocialregteacher"] = $data["gurujomsocialregteacher"];
			$for_save["gurujomsocialprofilestudent"] = $data["gurujomsocialprofilestudent"];
			$for_save["gurujomsocialprofileteacher"] = $data["gurujomsocialprofileteacher"];
			$for_save["gurujomsocialregstudentmprof"] = $data["gurujomsocialregstudentmprof"];
			$for_save["gurujomsocialregteachermprof"] = $data["gurujomsocialregteachermprof"];
		}	
		elseif($tab == "8"){
			$for_save["st_authorpage"] = $data["st_authorpage"];
			$for_save["course_is_free_show"] = intval($data["course_is_free_show"]);
			
			$for_save["terms_cond_teacher"] = $data["terms_cond_teacher"];
			$for_save["terms_cond_teacher_content"] = JFactory::getApplication()->input->get('terms_cond_teacher_content', '', 'raw');
			$for_save["mailchimp_teacher_api"] = $data["mailchimp_teacher_api"];
			$for_save["mailchimp_teacher_list_id"] = $data["mailchimp_teacher_list_id"];
			$for_save["mailchimp_teacher_auto"] = $data["mailchimp_teacher_auto"];

			if(isset($data["fields"])){
				$for_save["teacher_custom_fields"] = json_encode($data["fields"]);
			}
			else{
				$for_save["teacher_custom_fields"] = json_encode(array());
			}
		}
		elseif($tab == "9"){
			$student_params = array();
			$student_params["student_redirect"] = $data["student_redirect"];
			$student_params["student_redirect_logout"] = $data["student_redirect_logout"];

			$for_save["student_params"] = json_encode($student_params);

			$for_save["terms_cond_student"] = $data["terms_cond_student"];
			$for_save["terms_cond_student_content"] = JFactory::getApplication()->input->get('terms_cond_student_content', '', 'raw');
			$for_save["mailchimp_student_api"] = $data["mailchimp_student_api"];
			$for_save["mailchimp_student_list_id"] = $data["mailchimp_student_list_id"];
			$for_save["mailchimp_student_auto"] = $data["mailchimp_student_auto"];

			if(isset($data["fields2"])){
				$for_save["student_custom_fields"] = json_encode($data["fields2"]);
			}
			else{
				$for_save["student_custom_fields"] = json_encode(array());
			}
		}
		elseif($tab == "10"){
			$for_save["seo"] = $data["seo"];
		}
		elseif($tab == "11"){
			$license_number = $data["license_number"];
			$domain = $_SERVER['HTTP_HOST'];
			$domain = str_replace("https://", "", $domain);
			$domain = str_replace("http://", "", $domain);
			$app = JFactory::getApplication();
			$component = "guru";
			$valid_license = false;
			$expired = false;

			if(trim($license_number) == ""){
				$app->enqueueMessage(JText::_("GURU_EMPTY_LICENSE_NUMBER"), "error");
				$app->redirect("index.php?option=com_guru&controller=guruConfigs&tab=11");
				die();
			}
			else{
				// start check license on ijoomla -----------------------------------------------
				$check_url = "https://www.ijoomla.com/index.php?option=com_digistore&controller=digistoreAutoinstaller&task=get_license_number_details&tmpl=component&format=raw&component=".$component."&domain=".urlencode($domain)."&license=".trim($license_number);
				$extensions = get_loaded_extensions();
			    $text = "";

				$license_details = file_get_contents($check_url);

				if($license_details == null || $license_details === false){
					$curl = curl_init();
					curl_setopt ($curl, CURLOPT_URL, $check_url);
					curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
					$license_details = curl_exec ($curl);
					curl_close ($curl);
    			}

				if(isset($license_details) && trim($license_details) != ""){
					$license_details = json_decode($license_details, true);

					if(isset($license_details["0"])){
						// license exists
						$license_details = $license_details["0"];
					}
					else{
						// license not exists
						$app->enqueueMessage(JText::_("GURU_GET_LICENSE_HERE")." <a href='https://guru.ijoomla.com/pricing' target='_blank'>".JText::_("GURU_HERE")."</a>", "error");
						$app->redirect("index.php?option=com_guru&controller=guruConfigs&tab=11");
						die();
					}

					if(isset($license_details["expires"]) && trim($license_details["expires"]) != "" && trim($license_details["expires"]) == "0000-00-00 00:00:00"){
						$valid_license = true;
						$expired = false;
					}
					elseif(isset($license_details["expires"]) && trim($license_details["expires"]) != "" && trim($license_details["expires"]) != "0000-00-00 00:00:00"){
						$now = strtotime(date("Y-m-d H:i:s"));
						$license_expires = strtotime(trim($license_details["expires"]));

						if($license_expires >= $now){
							$valid_license = true;
							$expired = false;
						}
						else{
							$for_save["license_number"] = $data["license_number"];

							$item->bind($for_save);
							$item->check();
					    	$item->store();

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

					if($license_details == null || $license_details === false){
						$curl = curl_init();
						curl_setopt ($curl, CURLOPT_URL, $check_url);
						curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
						$license_details = curl_exec ($curl);
						curl_close ($curl);
	    			}

					if(isset($license_details) && trim($license_details) != ""){
						$license_details = json_decode($license_details, true);

						if(isset($license_details["0"])){
							// license exists
							$license_details = $license_details["0"];
						}
						else{
							// license not exists
							$app->enqueueMessage(JText::_("GURU_GET_LICENSE_HERE")." <a href='https://guru.ijoomla.com/pricing' target='_blank'>".JText::_("GURU_HERE")."</a>", "error");
							$app->redirect("index.php?option=com_guru&controller=guruConfigs&tab=11");
							die();
						}

						if(isset($license_details["expires"]) && trim($license_details["expires"]) != "" && trim($license_details["expires"]) == "0000-00-00 00:00:00"){
							$valid_license = true;
							$expired = false;
						}
						elseif(isset($license_details["expires"]) && trim($license_details["expires"]) != "" && trim($license_details["expires"]) != "0000-00-00 00:00:00"){
							$now = strtotime(date("Y-m-d H:i:s"));
							$license_expires = strtotime(trim($license_details["expires"]));

							if($license_expires >= $now){
								$valid_license = true;
								$expired = false;
							}
							else{
								$expired = true;
								$for_save["license_number"] = $data["license_number"];

								$item->bind($for_save);
								$item->check();
						    	$item->store();

						    	$app->enqueueMessage(JText::_("GURU_INVALID_LICENSE_NUMBER")." <a href='https://www.ijoomla.com/my-downloads43/licenses/42' target='_blank'>".JText::_("GURU_HERE")."</a>", "error");
								$app->redirect("index.php?option=com_guru&controller=guruConfigs&tab=11");
								die();
							}
						}
					}
					// stop check license on jomsocial -----------------------------------------------
				}
				
				if($valid_license){
					$for_save["license_number"] = $data["license_number"];
				}
				elseif(!$valid_license){
					// license not exists
					$app->enqueueMessage(JText::_("GURU_GET_LICENSE_HERE")." <a href='https://guru.ijoomla.com/pricing' target='_blank'>".JText::_("GURU_HERE")."</a>", "error");
					$app->redirect("index.php?option=com_guru&controller=guruConfigs&tab=11");
					die();
				}
			}
		}
		elseif($tab == "12"){
			$secure_key = $data["secure_key"];
			$payed_plugins = $data["payed_plugins"];

			$for_save["secure_key"] = $secure_key;
			$for_save["payed_plugins"] = $payed_plugins;
		}


		if (!$item->bind($for_save)){
			return JFactory::getApplication()->enqueueMessage($item->getError(), 'error');
			return false;

		} 
		if (!$item->check()) {
			return JFactory::getApplication()->enqueueMessage($item->getError(), 'error');
			return false;

		}
    
		if (!$item->store()) {
			return JFactory::getApplication()->enqueueMessage($item->getError(), 'error');
			return false;
		}

		return true;
	}	

	function getPaymentPlugins () {
		$db = JFactory::getDBO();
		$sql = "select * from #__extensions where folder='gurupayment'";
		$db->setQuery($sql);
		$db->execute();
		$result = $db->loadAssocList();
		return $result;
	}
};
?>