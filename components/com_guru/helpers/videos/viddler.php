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
 * Class to manipulate data from Viddler
 *
 * @access	public
 */
class PTableVideoViddler extends PVideoProvider
{
	var $xmlContent = null;
	var $url 		= '';
	var $videoId	= '';
	/**
	 * Return feedUrl of the video
	 */
	public function getFeedUrl()
	{
		return 'http://www.viddler.com/v/' .$this->videoId;
	}


	/**
	 * Extract Viddler video id from the video url submitted by the user
	 *
	 * @access	public
	 * @param	video url
	 * @return videoid
	 */
	public function getId()
	{
        $pattern    = '/http\:\/\/(\w{3}\.)?viddler.com\/v\/(.*)?/';
        preg_match($pattern, $this->url, $match);

        return !empty($match[2]) ? $match[2] : null;
	}


	/**
	 * Return the video provider's name
	 *
	 */
	public function getType()
	{
		return 'viddler';
	}

	public function getTitle()
	{
		$title	= '';

		// Get title
		$pattern =  "'<title>\"(.*)\"'";
		preg_match_all($pattern, $this->xmlContent, $matches);

		$title = @$matches[1][0];
		return $title;
	}

	public function getDescription()
	{
		$description	= '';

		// Get description
		$pattern = "'<meta content=\"(.*?)\" property=\"og:description\" \/>'";
		preg_match_all($pattern, $this->xmlContent, $matches);
		if($matches)
		{
			$description = trim(strip_tags($matches[1][0]));
		}

		return $description;
	}

	public function getDuration()
	{
		return false;
	}

	public function getThumbnail()
	{
		$thumbnail	= '';

		// Get thumbnail
		$pattern = "'<meta content=\"(.*?)\" property=\"og:image\" \/>'";
		preg_match_all($pattern, $this->xmlContent, $matches);
		if($matches)
		{
			$thumbnail = rawurldecode(@$matches[1][0]);
		}

		return CVideosHelper::getIURL($thumbnail);
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
			$videoId	= $this->videoId;
		}
		$this->videoId	= $videoId;

		$xmlContent = CRemoteHelper::getContent($this->getFeedUrl());

		$pattern =  "'<link rel=\"video_src\"(.*?)\/>'s";
		preg_match_all($pattern, $xmlContent, $matches);
		if($matches)
		{
			$pattern =  "'href=\"(.*?)\"'s";
			preg_match_all($pattern, $matches[1][0], $matches);
			$videoUrl= rawurldecode($matches[1][0]);
		}

		return '<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" width="'.$videoWidth.'" height="'.$videoHeight.'" id="viddler"><param name="movie" value="'.  CVideosHelper::getIURL($videoUrl).'" /><param name="allowScriptAccess" value="always" /><param name="allowFullScreen" value="true" /><param name="wmode" value="transparent"/><embed src="'.CVideosHelper::getIURL($videoUrl).'" width="'.$videoWidth.'" height="'.$videoHeight.'" type="application/x-shockwave-flash" allowScriptAccess="always" allowFullScreen="true" name="viddler" wmode="transparent"></embed></object>';

	}
}
