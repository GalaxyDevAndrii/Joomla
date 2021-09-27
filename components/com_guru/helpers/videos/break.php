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
 * Class to manipulate data from Break
 *
 * @access	public
 */
class PTableVideoBreak extends PVideoProvider
{
	var $xmlContent = null;
	var $url = '';
	var $videoId	= '';

	/**
	 * Return feedUrl of the video
	 */
	public function getFeedUrl()
	{
		return 'http://www.break.com/' . $this->getId();
	}

	/*
	 * Return true if successfully connect to remote video provider
	 * and the video is valid
	 */
	public function isValid()
	{
		if ( !parent::isValid())
		{
			return false;
		}

		// Return Break error
		$pattern =  "'<span id=\"sitemap404msg\">(.*?)<\/span>'s";
		preg_match_all($pattern, $this->xmlContent, $matches);

		if( !empty($matches[1][0]) )
		{
			$errormsg = 'COM_COMMUNITY_VIDEOS_FETCHING_VIDEO_ERROR' . strip_tags( $matches[1][0] );
			$this->setError	( JText::_($errormsg) );
			return false;
		}

		return true;
	}

	/**
	 * Extract Break video id from the video url submitted by the user
	 *
	 * @access	public
	 * @param	video url
	 * @return videoid
	 */
	public function getId()
	{
	   $pattern    = '/http\:\/\/(\w{3}\.)?break.com\/(.*)/';
	   preg_match( $pattern, $this->url, $match );

       return !empty( $match[2] ) ? $match[2] : null;
	}


	/**
	 * Return the video provider's name
	 *
	 */
	public function getType()
	{
		return 'break';
	}

	public function getTitle()
	{
		$title = '';
		// Store video title
		//$pattern =  "'<meta name=\"embed_video_title\" id=\"vid_title\" content=\"(.*?)\"( \/)?(>)'s";
		$pattern =  "'<meta name=\"embed_video_title\" content=\"(.*?)\"( \/)?(>)'s";
		preg_match_all($pattern, $this->xmlContent, $matches);
		if($matches)
		{
			if(isset($matches[1][0])) $title = $matches[1][0];
		}

		return $title;
	}

	public function getDescription()
	{
		$description = '';
		// Store description
		//$pattern =  "'<meta name=\"embed_video_description\" id=\"vid_desc\" content=\"(.*?)\"( \/)?(>)'s";
		$pattern =  "'<meta name=\"embed_video_description\" content=\"(.*?)\"( \/)?(>)'s";
		preg_match_all($pattern, $this->xmlContent, $matches);
		if($matches)
		{
			$description = $matches[1][0];
		}

		return $description;
	}

	public function getDuration()
	{
		return 0;
	}

	/**
	 * Get video's thumbnail
	 *
	 * @access 	public
	 * @param 	videoid
	 * @return url
	 */
	public function getThumbnail()
	{
		$thumbnail = '';
		// Store thumbnail
		$pattern =  "'<meta name=\"embed_video_thumb_url\" content=\"(.*?)\"( \/)?(>)'s";
		preg_match_all($pattern, $this->xmlContent, $matches);
		if($matches)
		{
			$thumbnail = $matches[1][0];
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

		$remoteFile	= 'http://www.break.com/'.$videoId;
		$xmlContent = CRemoteHelper::getContent($remoteFile);

		$pattern =  "'<meta name=\"embed_video_url\" content=\"(.*?)\"( /)?(>)'s";
		preg_match_all($pattern, $xmlContent, $matches);
		if($matches)
		{
				$videoUrl = $matches[1][0];
		}

        //break.com must always be redirected to their own site
        $html = '<span style="color: #ffffff;"><a href="' . $remoteFile . '" target="_blank" style="color: #ffffff; text-decoration: underline;">'.JText::_('COM_COMMUNITY_VIDEO_BREAK_NOTICE').'</a></span>';

        return $html;
	}
}