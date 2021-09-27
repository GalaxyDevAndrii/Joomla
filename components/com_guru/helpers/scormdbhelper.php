<?php

// no direct access
defined('_JEXEC') or die('Restricted access');
jimport( 'joomla.html.html' );
jimport( 'joomla.html.parameter' );
jimport( 'joomla.utilities.date');

class gurudbhelper
{

	function get_records($col,$table,$wherearr='',$ordering='',$operation='')
	{

		$db = JFactory::getDBO();

		$where = array();
		$wherestr ='';

		$query = "SELECT $col FROM #__$table";
		if(!empty($wherearr))
		{
		   foreach($wherearr as $column=>$val)
		   {
				$where[] = $column."= '".$val ."' " ;
			}
			$wherestr = 'where ' . implode(' AND ' ,$where );
			$query .=	' '. $wherestr .' ';
		}
		if($ordering)
		{
			$query .=	' ORDER BY '. $ordering .' ';
		}
		 $query;
		$db->setQuery($query);

		if($operation =='loadResult')
			return $db->loadResult();
		else if($operation =='loadObject')
			return $db->loadObject();
		else if($operation =='loadObjectList')
			return $db->loadObjectList();
		else
			return $db->loadColumn();
	}
	function delete_records($table,$wherearr='')
	{
		$db = JFactory::getDBO();
		$where = array();
		$wherestr ='';

		$query = "DELETE FROM #__$table";
		if(!empty($wherearr))
		{
		   foreach($wherearr as $col=>$val)
		   {
				$where[] = $col."= '".$val ."' " ;
			}
			$wherestr = 'where ' . implode(' AND ' ,$where );
			$query .=	' '. $wherestr .' ';
		}

		$db->setQuery($query);
		$db->execute();
	}

}
