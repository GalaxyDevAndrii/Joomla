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
 * Class to manipulate data from Daily Motion
 *
 * @access	public
 */
class PTableVideoDailymotion extends PVideoProvider
{
	var $xmlContent = null;
	var $url = '';
	var $videoId	= '';

	/**
	 * Return feedUrl of the video
	 */
	public function getFeedUrl()
	{
		return 'https://api.dailymotion.com/video/'.$this->getId().'?fields=description,duration%2Cthumbnail_url%2Ctitle';

		//return 'http://www.dailymotion.com/video/'.$this->getId();
	}

	/**
	 * Extract DailyMotion video id from the video url submitted by the user
	 *
	 * @access	public
	 * @param	video url
	 * @return videoid
	 */
	public function getId()
	{
        $pattern    = '/dailymotion.com\/?(.*)\/video\/(.*)/';
        preg_match( $pattern, $this->url, $match);

		$parts = explode('#', $match[2]);

        return !empty($match[2]) ? array_shift($parts) : null;
	}


	/**
	 * Return the video provider's name
	 *
	 */
	public function getType()
	{
		return 'dailymotion';
	}

	public function getTitle()
	{
		$title = '';
		$jsonmeta = json_decode($this->xmlContent);
		$title = $jsonmeta->title;
		return $title;

		// Store video title
		/*$pattern =  "/<h1 class=\"dmco_title\">(.*)(<\/h1>)?(<\/span>)/i";
		preg_match_all($pattern, $this->xmlContent, $matches);

		if( $matches && !empty($matches[1][0]) )
		{
			$title = strip_tags($matches[1][0]);
		}

		return $title;*/
	}

	public function getDescription()
	{
		$description = '';
		$jsonmeta = json_decode($this->xmlContent);
		$description = $jsonmeta->description;
		return $description;

		// Store description
		/*$pattern =  "/<meta name=\"description\" lang=\"en\" content=\"(.*)\" \/>/i";
		preg_match_all($pattern, $this->xmlContent, $matches);

		if( $matches && !empty($matches[1][0]) )
		{
			$description = trim(strip_tags($matches[1][0],'<br /><br>'));
		}

		return $description;*/
	}

	public function getDuration()
	{
		$duration = '';
		$jsonmeta = json_decode($this->xmlContent);
		$duration = isset($jsonmeta->duration)?$jsonmeta->duration:'';
		return $duration;

		// Store duration
		/*$pattern =  "'DMDURATION=(.*?)&'s";
		preg_match_all($pattern, $this->xmlContent, $matches);

		if( $matches && !empty($matches[1][0]) )
		{
            $duration   = $matches[1][0];
		}

		return $duration;*/
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
		$thumbnail = '';
		$jsonmeta = json_decode($this->xmlContent);
		$thumbnail = $jsonmeta->thumbnail_url;
		return CVideosHelper::getIURL($thumbnail);

		/*
		$pattern =  "'<link rel=\"image_src\" type=\"image/jpeg\" href=\"(.*?)\"'s";
		*/

		/*$pattern =  "'<meta property=\"og:image\" content=\"(.*?)\"'s";

		preg_match_all($pattern, $this->xmlContent, $matches);

		if( $matches && !empty($matches[1][0]) )
		{
			$thumbnail = urldecode($matches[1][0]);
		}

		return $thumbnail;*/
	}

	/**
	 *
	 *
	 * @return $embedvideo specific embeded code to play the video
	 */
	public function getViewHTML($videoId, $videoWidth, $videoHeight)
	{
		if (!$videoId)
		{
			$videoId = $this->videoId;
		}
		$embedCode = '<iframe src="'.CVideosHelper::getIURL('http://www.dailymotion.com/embed/video/' . $videoId) . '" width="' . $videoWidth . '" height="' . $videoHeight . '" frameborder="0"></iframe>';

		return $embedCode;
	}

}
