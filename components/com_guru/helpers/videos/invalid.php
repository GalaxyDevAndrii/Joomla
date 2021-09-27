<?php
/**
* @copyright (C) 2013 iJoomla, Inc. - All rights reserved.
* @license GNU General Public License, version 2 (http://www.gnu.org/licenses/gpl-2.0.html)
* @author iJoomla.com <webmaster@ijoomla.com>
* @url https://www.jomsocial.com/license-agreement
* The PHP code portions are distributed under the GPL license. If not otherwise stated, all images, manuals, cascading style sheets, and included JavaScript *are NOT GPL, and are released under the IJOOMLA Proprietary Use License v1.0
* More info at https://www.jomsocial.com/license-agreement
*/
defined('_JEXEC') or die('Restricted access');

//require_once (COMMUNITY_COM_PATH.'/models/videos.php');

/**
 * Class to manipulate data from YouTube
 *
 * @access	public
 */
class PTableVideoInvalid extends PVideoProvider
{
	var $xmlContent = null;
	var $url = '';
	var $videoId	= '';

	/**
	 * Return feedUrl of the video
	 */
	public function getFeedUrl()
	{
		return 'http://gdata.youtube.com/feeds/api/videos/' . $this->getId();
	}

	public function init($url)
	{
		$this->url = $url;
	}

	/*
	 * Return true if successfully connect to remote video provider
	 * and the video is valid
	 */
	public function isValid()
	{
		$this->setError(JText::_('COM_COMMUNITY_VIDEOS_PROVIDER_NOT_SUPPORTED_ERROR'));
		return false;
	}

	/**
	 * Extract YouTube video id from the video url submitted by the user
	 *
	 * @access	public
	 * @param	video url
	 * @return videoid
	 */
	public function getId()
	{
		return '';
	}

	/**
	 * Return the video provider's name
	 *
	 */
	public function getType()
	{
		return 'invalid';
	}

	public function getTitle()
	{
		$title = '';
		return $title;
	}

	public function getDescription()
	{
		$description = '';
		return $description;
	}

	public function getDuration()
	{
		$duration = 0;
		return $duration;
	}

	/**
	 * Get video's thumbnail URL from videoid
	 *
	 * @access 	public
	 * @param 	videoid
	 * @return url
	 */
	public function getThumbnail()
	{
		return CVideosHelper::getIURL('http://img.youtube.com/vi/' . $this->getId() . '/default.jpg');
	}

	/**
	 *
	 *
	 * @return $embedvideo specific embeded code to play the video
	 */
	public function getViewHTML($videoId, $videoWidth, $videoHeight)
	{
		return "";
	}
}
