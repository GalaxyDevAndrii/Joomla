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


class guruAdminModelguruLanguage extends JModelLegacy {
	var $_plugins;
	var $_plugin;
	var $plugin_instances = array();
	var $_id = null;
	var $_installpath; 
	var $plugins_loaded = 0;
	
	function __construct () {
		parent::__construct();
		$cids = JFactory::getApplication()->input->get('cid', 0, "raw");

		$this->setId((int)$cids[0]);
		$this->loadLanguages();
	}


	function store () {
		
	}	
	
	
	function registerPlugin ($filename, $classname) {
		$install_path = $this->_installpath;
		if (!file_exists($install_path.$filename)) {
			return 0;//_NO_PLUGIN_FILE_EXISTS;	
		}
		
		require_once ($install_path.$filename);
		
		$plugin = new $classname;//$this->plugins[$classname];	
		if (!is_object($plugin) ) {
			return 0;
		}
		foreach ($this->req_methods as $method) {
			if (!method_exists ($plugin, $method) ) {
				return 0;
			}
		}
		
		if (isset($this->_plugins[$classname])) {
			$this->_plugins[$classname]->instance = $plugin;
		} else {
			$this->_plugins[$classname] = new stdClass;
			$this->_plugins[$classname]->instance = &$plugin;
		}
		
				return $plugin;

	}

	function installPlugin($path, $plugin_file = '') {
		$db = JFactory::getDBO();

		$plugin_file = trim ($plugin_file);
		if (strlen($plugin_file) < 1) return JText::_('MODPLUGNOFILENAME');
		$ext = substr ($plugin_file, strrpos($plugin_file, ".") + 1);
		if ($ext != 'zip') return JText::_('MODPLUGNOZIP');
		
		jimport('joomla.filesystem.archive');	
		if (!JArchive::extract($path.$plugin_file, $path)) {
			return JText::_('MODPLUGEXTRACTERR');
		}
		if (!file_exists($path."install")) return JText::_("MODPLUGMISSINGINSTALL");
		$install = parse_ini_file($path."install");
		if (count ($install) < 3) return JText::_("MODPLUGINSTALLCORRUPT");
		if (!isset($install['type']) || !in_array($install['type'], $this->allowed_types)) return JText::_('Bad plugin type');
		$query = "select count(*) from #__guru_plugins where type='".$install['type']."' and name='".$install['name']."'";
  		$db->setQuery($query);
   		$isthere = $db->loadResult();
		if ($isthere) return JText::_('MODPLUGALLEXIST');// 
		
		$install_path = $this->_installpath;
		
      		JFile::copy ($path.$install['filename'], $install_path.$install['filename']);    
      		@chmod($install_path.$install['filename'],0755);
	        //Add uploaded plugin to db but do not publish it.
        	if (!is_object($this->registerPlugin($install['filename'], $install['classname']))) return JText::_("MODPLUGREGERR");
       
	        $query = "insert into #__guru_plugins 
				(name, classname, value, filename, type, published, def, sandbox, reqhttps)
				 values 
				('".$install['name']."', '".$install['classname']."', '',
				'".$install['filename']."', '".$install['type']."', 0, '', '', ".$install['reqhttps'].");";
        	$db->setQuery($query);
	        $db->execute();
	        $pluginame = $install['name'];

    	if ($install['type'] == 'payment') {
			$currency = $this->_plugins[$install['classname']]->instance->insert_currency();
		        $sql = "SELECT COUNT(*) FROM #__guru_currencies WHERE plugname='" . $pluginame . "'";
		        $db->setQuery($sql);
		        
		        if ( $db->loadResult() == 0 ) {
			        foreach ($currency as $i => $v) {
			                $query = "INSERT INTO #__guru_currencies ( id , plugname, currency_name , currency_full ) VALUES ( '', '".$pluginame."','".$i."', '".$v."' )";
			                $db->setQuery($query);
		                	$db->execute();
				}   	    
		        }

		}
   		if ($install['name'] == 'paypal') {
        	    $query = "update #__guru_plugins set published=1, def='default' where filename='".$install['filename']."';";
	            $db->setQuery($query);
        	    $db->execute();
        
	    	}
	    	return JText::_("MODPLUGSUCCINSALLED");
	}

	function upload() {
		$table_entry = $this->getTable ("guruPlugin");
		jimport('joomla.filesystem.file');
		$file = JFactory::getApplication()->input->get('pluginfile', array());	
		$install_path = JPATH_ROOT.DIRECTORY_SEPARATOR."tmp".DIRECTORY_SEPARATOR."guruplugin".DIRECTORY_SEPARATOR;
		Jfolder::create ($install_path);

		if (JFile::copy($file['tmp_name'], $install_path.$file['name'], '')) {

			$res = $this->installPlugin($install_path, $file['name']);
			JFolder::delete ($install_path);

		} else {
			$res = JText::_('MODPLUGCOPYERR');
		}
		
		return $res;
	
	}


	function delete () {
		$db = JFactory::getDBO();
		$cids = JFactory::getApplication()->input->get('cid', array(0), "raw");
		$item = $this->getTable('guruPlugin');
		jimport('joomla.filesystem.file');

		foreach ($cids as $cid) {
			
			$sql = "select name from #__guru_plugins where id = '".$cid."'";
			$db->setQuery($sql);
			$plugname = $db->loadResult();
			
			$sql = "delete from #__guru_currencies where plugname = '".$plugname."'";
			$db->setQuery($sql);
			$db->execute();
			
			$item->load($cid);
			JFile::delete($this->_installpath.$item->filename);
			if (!$item->delete($cid)) {
				$this->setError($item->getError());
				return false;

			}
		}

		return true;
	}


	function publish () {
		$db = JFactory::getDBO();
		$cids = JFactory::getApplication()->input->get('cid', array(0), "raw");
		$task = JFactory::getApplication()->input->get('task', '', 'post');
		$item = $this->getTable('guruPlugin');
		if ($task == 'publish'){
			$sql = "update #__guru_plugins set published='1' where id in ('".implode("','", $cids)."')";
			$res = 1;
		} else {
			$sql = "update #__guru_plugins set published='0' where id in ('".implode("','", $cids)."')";
			$res = -1;

		}
		$db->setQuery($sql);
		if (!$db->execute() ){
			//$this->setError($db->getErrorMsg());
			return false;
		}
		return $res;
	}
};
?>