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
 * Class to manipulate data from Metacafe
 *
 * @access	public
 */
class PTableVideoMetacafe extends PVideoProvider
{
	var $xmlContent = null;
	var $url 		= '';
	var $videoId	= '';

	/**
	 * Return feedUrl of the video
	 */
	public function getFeedUrl()
	{
		$videoId = explode("/", $this->videoId);
		return 'http://www.metacafe.com/api/item/' . $videoId[0].'/';
	}

	/*
	 * Return true if successfully connect to remote video provider
	 * and the video is valid
	 */
	public function isValid()
	{
		// Connect and get the remote video
        if ( !parent::isValid())
		{
			return false;
		}

		if(class_exists('JFeedFactory'))
		{
			$feed = new JFeedFactory;
			$rssDoc = $feed->getFeed($this->getFeedUrl());
			$this->title = $rssDoc[0]->title;
			$this->data = $rssDoc[0]->content;
		}
		else
		{

			$rssDoc = JFactory::getFeedParser($this->getFeedUrl(), 0);

			foreach ($rssDoc->get_items() as $item):
				$enclosures	= $item->get_enclosures();
				$this->duration	= $enclosures[0]->get_duration();
				$this->title = $item->get_title();
				$this->data = $item->get_description();
			endforeach;
		}

		return true;
	}

	/**
	 * Extract MetaCafe video id from the video url submitted by the user
	 *
	 * @access	public
	 * @param	video url
	 * @return videoid
	 */
	public function getId()
	{
        $pattern    = '/http\:\/\/\w{3}\.?metacafe.com\/watch\/(.*)\//';
        preg_match( $pattern, $this->url, $match );

        return !empty($match[1]) ? $match[1] : null;
	}


	/**
	 * Return the video provider's name
	 *
	 */
	public function getType()
	{
		return 'metacafe';
	}

	public function getTitle()
	{
		if(!empty($this->title)){
			return $this->title;
		}
		$pattern =  "'<title>(.*?)</title>'s";
		preg_match_all($pattern, $this->data, $matches);
		if(!empty($matches))
		{
			$title = trim($matches[1][1]);
		}

		return $title;

	}

	/**
	 * Get video's description from videoid
	 *
	 * @access 	public
	 * @param 	videoid
	 * @return desctiption
	 */
	public function getDescription()
	{
		$description	= '';

		$pattern =  "'<p>(.*?)<br>'s";
		preg_match_all($pattern, $this->data, $matches);

		if($matches)
		{
			$description = trim($matches[1][0]);
		}
		return $description;
	}

	/**
	 * Get video duration
	 *
	 * @return $duration seconds
	 */
	public function getDuration()
	{
		$duration 	= '';

		$pattern	=  "'</a> \((.*?)\)<br/>'s";
		preg_match_all($pattern, $this->data, $matches);

		if($matches)
		{
			$duration = trim($matches[1][0]);
			$duration = explode(":",$duration);
			$duration = ($duration[0]*60) + ($duration[1]);
		}

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
		$thumbnail	= '';

		$pattern =  "'<img src=\"(.*?)\"'s";
		preg_match_all($pattern, $this->data, $matches);
		if($matches)
		{
			$thumbnail = trim($matches[1][0]);
		}

		return CVideosHelper::getIURL($thumbnail);
	}

	/**
	 *
	 *
	 * @return $embedvideo specific embeded code to play the video
	 */
	public function getViewHTML( $videoId, $videoWidth, $videoHeight )
	{
		// video is no longer supported, ask the redirect to actual page
        return '<span style="color: #ffffff;">'.JText::sprintf('COM_COMMUNITY_VIDEO_METACAFE_NOTICE', 'http://www.metacafe.com/watch/' . $videoId).'</span>';
	}
}
