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
use Joomla\Archive\Archive;

class scormGuruImport{

	function uploadScormLesson($lesson_id, $scorm_item){
		$db = JFactory::getDbo();

		if(isset($scorm_item["0"])){
			$scorm_item = $scorm_item["0"];
		}

		$format_data = new stdClass;
		$format_data->lesson_id = intval($lesson_id);
		$format_data->storage = 'local';
		$format_data->scormtype	= 'native';

		if ($scorm_item['file']){
			$format_data->package = basename($scorm_item['file']);
		}

		$format_data->version = '';
		$format_data->grademethod = '0';
		$format_data->passing_score = '0';
		$format_data->entry = '0';
		$format_data->launch = '0';

		$this->Upload_filesOnnativescorm($lesson_id, $format_data->package, JPATH_SITE.DIRECTORY_SEPARATOR."media".DIRECTORY_SEPARATOR."scorm");

		$scormLesson = $this->getscormDataforLesson($format_data->lesson_id);

		if (!empty($scormLesson)){
			$format_data->id = $scormLesson->id;
			$db->updateObject('#__guru_scorm', $format_data, 'id');
			$scorm_id =	$format_data->id;
		}
		else{
			$db->insertObject('#__guru_scorm', $format_data);
			$scorm_id =	$db->insertid();
		}

		if ($scorm_item['file']){
			$lib_path = JPATH_SITE.DIRECTORY_SEPARATOR."components".DIRECTORY_SEPARATOR."com_guru".DIRECTORY_SEPARATOR."helpers".DIRECTORY_SEPARATOR."scormlib.php";

			if (JFile::exists($lib_path)){
				require_once $lib_path;
				$guruscormlib = new guruscormlib;

				/*extract scorm to media/scorm/lessons/SCORMZIPNAME folder */
				$ext = pathinfo($format_data->package, PATHINFO_EXTENSION);
				$scormFoldername = basename($format_data->package, "." . $ext);

				$scorm_data = new stdClass;
				$scorm_data->lesson_id = $format_data->lesson_id;
				$scorm_data->id = $scorm_id;
				$ret = $guruscormlib->scorm_parse($scorm_data,  $scormFoldername);

				return true;
			}
		}

		return false;
	}

	public function getscormDataforLesson($lessonid){
		$db = JFactory::getDbo();

		// Create a new query object.
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from($db->quoteName('#__guru_scorm'));
		$query->where($db->quoteName('lesson_id') . ' = ' . (int) $lessonid);
		$query->order('id DESC');
		$db->setQuery($query);

		return $db->loadObject();
	}

	public function Upload_filesOnnativescorm($lesson_id, $filename = '', $filepath = ''){
		/* extract scorm to media/scorm/lessons/SCORMZIPNAME folder */
		$ext = pathinfo($filename, PATHINFO_EXTENSION);
		$renamedto = basename($filename, "." . $ext);

		$extract_dir = JPATH_SITE . '/media/scorm/lessons/' . $lesson_id . '/scorm';

		if ($renamedto)
		{
			$extract_dir = JPATH_SITE . '/media/scorm/lessons/' . $lesson_id . '/' . $renamedto;
		}

		if (JFolder::exists($extract_dir)){
			JFolder::delete($extract_dir);
		}

		$archive = $filepath.DIRECTORY_SEPARATOR.$filename;
		$archive = JPath::clean($archive);
		$extract_dir = JPath::clean($extract_dir);
		$archive_class = new Archive;
		
		if ($archive_class->extract($archive, $extract_dir)){
			return true;
		}
		else{
			return false;
		}
	}

	public function scorm_insert_track($userid, $scormid, $scoid, $attempt, $element, $value, $forcecompleted=false)
	{
		$db = JFactory::getDbo();
		$id = null;

		if ($userid > 0)
		{
			if ($forcecompleted)
			{
				// TODO - this could be broadened to encompass SCORM 2004 in future
				if (($element == 'cmi.core.lesson_status') && ($value == 'incomplete'))
				{
					if ($track = self::gettrackforElementofSco($scormid, $scoid, $attempt, $userid, 'cmi.core.score.raw'))
					{
						$value = 'completed';
					}
				}

				if ($element == 'cmi.core.score.raw')
				{
					
					if ($tracktest = self::gettrackforElementofSco($scormid, $scoid, $attempt, $userid, 'cmi.core.lesson_status'))
					{
						if ($tracktest->value == "incomplete")
						{
							$tracktest->value = "completed";
							$db->updateObject('#__guru_scorm_scoes_track', $tracktest, 'id');
						}
					}
				}
			}

			$track = self::gettrackforElementofSco($scormid, $scoid, $attempt, $userid, $element);

			if ($track )
			{
				if ($element != 'x.start.time' )
				{
					// Don't update x.start.time - keep the original value.
					$track->value = $value;
					$track->timemodified = time();
					$db->updateObject('#__guru_scorm_scoes_track', $track, 'id');
					$id = $track->id;
				}
			}
			else
			{
				$track = new stdClass;
				$track->userid = $userid;
				$track->scorm_id = $scormid;
				$track->sco_id = $scoid;
				$track->attempt = $attempt;
				$track->element = $element;
				$track->value = $value;
				$track->timemodified = time();
				$id = $db->insertObject('#__guru_scorm_scoes_track', $track);
			}

			// TODO : check for multisco
			$statusvariables	=	array('cmi.completion_status', 'cmi.core.lesson_status', 'cmi.success_status' , 'cmi.core.total_time');

			if (strstr($element, '.score.raw') || (in_array($element, $statusvariables)))
			{
				$scoreandstatus	=	$this->updatelmsScormuserscore_by_attempt($scormid, $attempt, $userid);
			}
		}

		return $id;
	}

	public function gettrackforElementofSco($scormId, $scoId, $attempt, $userId, $element)
	{
		$db = JFactory::getDbo();
		$query	= $db->getQuery(true);
		$query->select('*');
		$query->from($db->quoteName('#__guru_scorm_scoes_track'));
		$query->where($db->quoteName('scorm_id') . " = " . $db->quote($scormId));
		$query->where($db->quoteName('sco_id') . " = " . $db->quote($scoId));
		$query->where($db->quoteName('attempt') . " = " . $db->quote($attempt));
		$query->where($db->quoteName('userid') . " = " . $db->quote($userId));
		$query->where($db->quoteName('element') . " = " . $db->quote($element));
		$db->setQuery($query);

		return $db->loadObject();
	}

	public function getScormDetails($scorm_id){
		$db	= JFactory::getDbo();
		$query	= $db->getQuery(true);
		$query->select('*');
		$query->from($db->quoteName('#__guru_scorm'));
		$query->where($db->quoteName('id') . " = " . $db->quote($scorm_id));
		$db->setQuery($query);

		return $db->loadObject();
	}

	public function getSCOdata($scormId, $scoId)
	{
		$db	= JFactory::getDBO();
		$query	= $db->getQuery(true);
		$query->select('*');
		$query->from($db->quoteName('#__guru_scorm_scoes'));
		$query->where($db->quoteName('scorm_id') . " = " . $db->quote($scormId));
		$query->where($db->quoteName('id') . " = " . $db->quote($scoId));

		$db->setQuery($query);
		$res = $db->loadObject();

		return $res;
	}

	public function scorm_reconstitute_array_element($sversion, $userdata, $element_name, $children)
	{
		// Reconstitute comments_from_learner and comments_from_lms
		$current = '';
		$current_subelement = '';
		$current_sub = '';
		$count = 0;
		$count_sub = 0;
		$scormseperator = '_';

		if (strcasecmp($sversion, 'SCORM_1.2') != 0)
		{
			// Scorm 1.3 elements use a . instead of an _
			$scormseperator = '.';
		}

		// Filter out the ones we want
		$element_list = array();

		foreach ($userdata as $element => $value)
		{
			if (substr($element, 0, strlen($element_name)) == $element_name)
			{
				$element_list[$element] = $value;
			}
		}

		// Sort elements in .n array order
		uksort($element_list, "scormGuruImport::scorm_element_cmp");

		// Generate JavaScript
		foreach ($element_list as $element => $value)
		{
			if (strcasecmp($sversion, 'SCORM_1.2') != 0)
			{
				$element = preg_replace('/\.(\d+)\./', ".N\$1.", $element);
				preg_match('/\.(N\d+)\./', $element, $matches);
			}
			else
			{
				$element = preg_replace('/\.(\d+)\./', "_\$1.", $element);
				preg_match('/\_(\d+)\./', $element, $matches);
			}

			if (count($matches) > 0 && $current != $matches[1])
			{
				if ($count_sub > 0)
				{
					echo '	' . $element_name . $scormseperator . $current . '.' . $current_subelement . '._count = ' . $count_sub . ";\n";
				}

				$current = $matches[1];
				$count++;
				$current_subelement = '';
				$current_sub = '';
				$count_sub = 0;
				$end = strpos($element, $matches[1]) + strlen($matches[1]);
				$subelement = substr($element, 0, $end);
				echo '	' . $subelement . " = new Object();\n";

				// Now add the children
				foreach ($children as $child)
				{
					echo '	' . $subelement . "." . $child . " = new Object();\n";
					echo '	' . $subelement . "." . $child . "._children = " . $child . "_children;\n";
				}
			}

			// Now - flesh out the second level elements if there are any
			if (strcasecmp($sversion, 'SCORM_1.2') != 0)
			{
				$element = preg_replace('/(.*?\.N\d+\..*?)\.(\d+)\./', "\$1.N\$2.", $element);
				preg_match('/.*?\.N\d+\.(.*?)\.(N\d+)\./', $element, $matches);
			}
			else
			{
				$element = preg_replace('/(.*?\_\d+\..*?)\.(\d+)\./', "\$1_\$2.", $element);
				preg_match('/.*?\_\d+\.(.*?)\_(\d+)\./', $element, $matches);
			}

			// Check the sub element type
			if (count($matches) > 0 && $current_subelement != $matches[1])
			{
				if ($count_sub > 0)
				{
					echo '	' . $element_name . $scormseperator . $current . '.' . $current_subelement . '._count = ' . $count_sub . ";\n";
				}

				$current_subelement = $matches[1];
				$current_sub = '';
				$count_sub = 0;
				$end = strpos($element, $matches[1]) + strlen($matches[1]);
				$subelement = substr($element, 0, $end);
				echo '	' . $subelement . " = new Object();\n";
			}

			// Now check the subelement subscript
			if (count($matches) > 0 && $current_sub != $matches[2])
			{
				$current_sub = $matches[2];
				$count_sub++;
				$end = strrpos($element, $matches[2]) + strlen($matches[2]);
				$subelement = substr($element, 0, $end);
				echo '	' . $subelement . " = new Object();\n";
			}

			echo '	' . $element . ' = \'' . $value . "';\n";
		}

		if ($count_sub > 0)
		{
			echo '	' . $element_name . $scormseperator . $current . '.' . $current_subelement . '._count = ' . $count_sub . ";\n";
		}

		if ($count > 0)
		{
			echo '	' . $element_name . '._count = ' . $count . ";\n";
		}
	}

	public function getUserScodata($scorm_id, $scoid, $userid, $attempt)
	{
		$mode = '';

		$app = JFactory::getApplication();
		$oluser	=	JFactory::getUser($userid);
		$scormdata	=	$this->getScormData($scorm_id);

		$userdata = new stdClass;
		$userdata->status = '';
		$userdata->score_raw = '';

		if ($usertrack = $this->scorm_get_scoestracks($scorm_id, $scoid, $userid, $attempt))
		{
			if ((strcasecmp($scormdata->version, 'SCORM_1.2') == 0)
				|| (isset($usertrack->{'cmi.exit'}) && ($usertrack->{'cmi.exit'} == 'suspend')))
			{
					foreach ($usertrack as $key => $value)
					{
						$userdata->$key = addslashes($value);
					}
			}
			else
			{
				$userdata->status = '';
				$userdata->score_raw = '';
			}
		}

		if ($app->getUserState('com_guru' . 'lesson.resetProgress') == 1)
		{
			$userdata->{'cmi.suspend_data'} = '';
			$userdata->{'cmi.lesson_location'} = '';
		}

		$userdata->student_id = $oluser->id;
		$userdata->student_name = $oluser->name;
		$userdata->mode = 'normal';

		if (!empty($mode))
		{
			$userdata->mode = $mode;
		}

		if ($userdata->mode == 'normal')
		{
			$userdata->credit = 'credit';
		}
		else
		{
			$userdata->credit = 'no-credit';
		}

		if ($scodatas = $this->scorm_get_sco($scoid))
		{
			foreach ($scodatas as $key => $value)
			{
				$userdata->$key = $value;
			}
		}

		/*else {
			print_error('cannotfindsco', 'scorm');
		}
		if (!$sco = scorm_get_sco($scoid)) {
			print_error('cannotfindsco', 'scorm');
		}*/

		// TODO : check which if to use
		if ((strcasecmp($scormdata->version, 'SCORM_1.3') == 0))
		{
			$db = JFactory::getDBO();
			$query	= $db->getQuery(true);
			$query->select('*');
			$query->from($db->quoteName('#__guru_scorm_seq_objective'));
			$query->where($db->quoteName('sco_id') . " = " . $db->quote($scoid));
			$db->setQuery($query);
			$objectives = $db->loadObjectList();

			$index = 0;

			foreach ($objectives as $objective)
			{
				if (!empty($objective->minnormalizedmeasure))
				{
					$userdata->{'cmi.scaled_passing_score'} = $objective->minnormalizedmeasure;
				}

				if (!empty($objective->objectiveid))
				{
					$userdata->{'cmi.objectives.N' . $index . '.id'} = $objective->objectiveid;
					$index++;
				}
			}
		}

		return $userdata;
	}

	public function getScormData($scorm_id)
	{
		$db	= JFactory::getDBO();
		$query	= $db->getQuery(true);
		$query->select('*');
		$query->from($db->quoteName('#__guru_scorm'));
		$query->where($db->quoteName('id') . " = " . $db->quote($scorm_id));
		$db->setQuery($query);

		return $db->loadObject();
	}

	public function scorm_get_scoestracks($scorm, $scoid, $userid, $attempt)
	{
		if ($tracks = self::gettracksofSco($scoid, $attempt, $userid))
		{
			$usertrack = new stdClass;
			$usertrack->userid = $userid;
			$usertrack->scoid = $scoid;

			// Defined in order to unify scorm1.2 and scorm2004
			$usertrack->score_raw = '';
			$usertrack->status = '';
			$usertrack->total_time = '00:00:00';
			$usertrack->session_time = '00:00:00';
			$usertrack->timemodified = 0;

			foreach ($tracks as $track)
			{
				$element = $track->element;
				$usertrack->{$element} = $track->value;

				switch ($element)
				{
					case 'cmi.core.lesson_status':
					case 'cmi.completion_status':

						if ($track->value == 'not attempted')
						{
							$track->value = 'notattempted';
						}

						$usertrack->status = $track->value;
					break;
					case 'cmi.core.score.raw':
					case 'cmi.score.raw':
						$usertrack->score_raw = (float) sprintf('%2.2f', $track->value);
					break;
					case 'cmi.core.session_time':
					case 'cmi.session_time':
						$usertrack->session_time = $track->value;
					break;
					case 'cmi.core.total_time':
					case 'cmi.total_time':
						$usertrack->total_time = $track->value;
					break;
				}

				if (isset($track->timemodified) && ($track->timemodified > $usertrack->timemodified))
				{
					$usertrack->timemodified = $track->timemodified;
				}
			}

			if (is_array($usertrack))
			{
				ksort($usertrack);
			}

			return $usertrack;
		}
		else
		{
			return false;
		}
	}

	public function gettracksofSco($scoId, $attempt, $userId)
	{
		$db = JFactory::getDBO();
		$query	= $db->getQuery(true);
		$query->select($db->quoteName(array('element', 'value')));
		$query->from($db->quoteName('#__guru_scorm_scoes_track'));
		$query->where($db->quoteName('sco_id') . " = " . $db->quote($scoId));
		$query->where($db->quoteName('attempt') . " = " . $db->quote($attempt));
		$query->where($db->quoteName('userid') . " = " . $db->quote($userId));
		$query->order('element ASC');
		$db->setQuery($query);

		return $db->loadObjectList();
	}

	public function scorm_get_sco($scoId)
	{
		
		$db = JFactory::getDBO();
		$query	= $db->getQuery(true);
		$query->select('*');
		$query->from($db->quoteName('#__guru_scorm_scoes_data'));
		$query->where($db->quoteName('sco_id') . " = " . $db->quote($scoId));
		$db->setQuery($query);
		$sco = new stdClass;

		if ($scodatas = $db->loadObjectList())
		{
			foreach ($scodatas as $scodata)
			{
				$sco->{$scodata->name} = $scodata->value;
			}
		}

		/*if ($sco = $DB->get_record('scorm_scoes', array('id'=>$id))) {
			$sco = ($what == SCO_DATA) ? new stdClass() : $sco;
			if (($what != SCO_ONLY) && ($scodatas = $DB->get_records('scorm_scoes_data', array('scoid'=>$id)))) {
				foreach ($scodatas as $scodata) {
					$sco->{$scodata->name} = $scodata->value;
				}
			} else if (($what != SCO_ONLY) && (!($scodatas = $DB->get_records('scorm_scoes_data', array('scoid'=>$id))))) {
				$sco->parameters = '';
			}
			return $sco;
		} else {
			return false;
		}*/

		return $sco;
	}

	public function scorm_element_cmp($a, $b)
	{
		preg_match('/.*?(\d+)\./', $a, $matches);
		$left = intval($matches[1]);
		preg_match('/.?(\d+)\./', $b, $matches);
		$right = intval($matches[1]);

		if ($left < $right)
		{
			// Smaller
			return -1;
		}
		elseif ($left > $right)
		{
			// Bigger
			return 1;
		}
		else
		{
			// Look for a second level qualifier eg cmi.interactions_0.correct_responses_0.pattern
			if (preg_match('/.*?(\d+)\.(.*?)\.(\d+)\./', $a, $matches))
			{
				$leftterm = intval($matches[2]);
				$left = intval($matches[3]);

				if (preg_match('/.*?(\d+)\.(.*?)\.(\d+)\./', $b, $matches))
				{
					$rightterm = intval($matches[2]);
					$right = intval($matches[3]);

					if ($leftterm < $rightterm)
					{
						// Smaller
						return -1;
					}
					elseif ($leftterm > $rightterm)
					{
						// Bigger
						return 1;
					}
					else
					{
						if ($left < $right)
						{
							// Smaller
							return -1;
						}
						elseif ($left > $right)
						{
							// Bigger
							return 1;
						}
					}
				}
			}

			// Fall back for no second level matches or second level matches are equal
			return 0;
		}
	}
}

?>