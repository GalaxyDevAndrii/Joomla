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
 * Class to manipulate data from Live Leak
 *
 * @access	public
 */
class PTableVideoLiveleak extends PVideoProvider
{
	var $xmlContent = null;
	var $url 		= '';
	var $videoId	= '';
	/**
	 * Return feedUrl of the video
	 */
	public function getFeedUrl()
	{
		return 'http://www.liveleak.com/view?i=' . $this->videoId;
	}

	/**
	 * Extract LiveLeak video id from the video url submitted by the user
	 *
	 * @access	public
	 * @param	video url
	 * @return videoid
	 */
	public function getId()
	{
        $pattern    = '/http\:\/\/(\w{3}\.)?liveleak.com\/view\?i\=([a-zA-Z0-9][a-zA-Z0-9$_.+!*(),;\/\?:@&~=%-]*)/';
        preg_match( $pattern, $this->url, $match );

        return !empty($match[2]) ? $match[2] : null;
	}


	/**
	 * Return the video provider's name
	 *
	 */
	public function getType()
	{
		return 'liveleak';
	}

	public function getTitle()
	{
		$title	= '';

		$res = preg_match("/<title>LiveLeak.com - (.*)<\/title>/", $this->xmlContent, $title_matches);
        if (!$res)
            return null;

        $title = $title_matches[1];

		return $title;
	}

	public function getDescription()
	{
		$description	= '';

		// get description
		// get thumbnail
		$res = preg_match('/<meta property="og:description" content="([^"\']*)"/i', $this->xmlContent, $title_matches);
        if (!$res)
            return null;

        $description = $title_matches[1];

		return $description;
	}

	public function getDuration()
	{
		return false;
	}

	public function getThumbnail()
	{
		$thumbnail	= '';
		$noPreview  = 'http://209.197.7.204/e3m9u5m8/cds/u/nopreview.jpg';

		// get thumbnail
		$res = preg_match('/<meta property="og:image" content="(.*)"/', $this->xmlContent, $title_matches);
        if (!$res)
            return null;

        $thumbnail = $title_matches[1];

		return !empty($thumbnail) ? CVideosHelper::getIURL($thumbnail) : $noPreview;
	}

	/**
	 *
	 *
	 * @return $embedvideo specific embeded code to play the video
	 */
	public function getViewHTML( $videoId, $videoWidth, $videoHeight )
	{
		if (!$videoId)
		{
			$videoId	= $this->videoId;
		}
		
		return '<iframe width="' . $videoWidth . '" height="' . $videoHeight . '" src="'.CVideosHelper::getIURL('http://www.liveleak.com/ll_embed?i=' . $videoId) . '" frameborder="0" allowfullscreen></iframe>';
	}
}
