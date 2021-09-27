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
 * Class to manipulate data from vimeo video
 *
 * @access	public
 */
class PTableVideoVimeo extends PVideoProvider
{
	var $xmlContent = null;
	var $url 		= '';
	var $videoId = null;
	
	/**
	 * Return feedUrl of the video
	 */
	public function getFeedUrl()
	{
		return 'http://vimeo.com/api/v2/video/' .$this->videoId.'.xml';
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
		
		//get vimeo error
		if(strpos($this->xmlContent, 'not found.')){
			$vimeoError	= JText::_('COM_COMMUNITY_VIDEOS_FETCHING_VIDEO_ERROR');
			$this->setError( $vimeoError );
			return false;
		}

		$parser = new SimpleXMLElement($this->xmlContent);
		$videoElement = $parser->video;

		if( empty($videoElement) )
		{
			$this->setError( JText::_('COM_COMMUNITY_VIDEOS_FETCHING_VIDEO_ERROR') );
			return false;
		}

		//get Video title
		$this->title = (string)$videoElement->title;

		//Get Video duration
		$this->duration = (int)$videoElement->duration;

		//Get Video thumbnail
		$this->thumbnail = (string)$videoElement->thumbnail_large;

		//Get Video description
		$this->description = (string)$videoElement->description;

		return true;
	}


	/**
	 * Extract Vimeo video id from the video url submitted by the user
	 *
	 * @access	public
	 * @param	video url
	 * @returns videoid
	 */
	public function getId()
	{
	    $pattern = '/vimeo.com\/(hd#)?(channels\/[a-zA-Z0-9]*#)?(\d*)/';
	    preg_match($pattern, $this->url, $match);

            if(!empty($match[3]))
            {
                return $match[3];
            }
            else
            {
               return !empty( $match[2] ) ? $match[2] : null;
            }

	}

	/**
	 * Return the video provider's name
	 *
	 */
	public function getType()
	{
		return 'vimeo';
	}

	public function getTitle()
	{
		$title	= '';
		$title	= $this->title;

		return $title;
	}

	/**
	 *
	 * @param $videoId
	 * @return unknown_type
	 */
	public function getDescription()
	{
		$description	= '';
		$description = $this->description;
		return $description;
	}

	public function getDuration()
	{
		$duration	= '';
		$duration	= $this->duration;

		return $duration;
	}

	/**
	 *
	 * @param $videoId
	 * @return unknown_type
	 */
	public function getThumbnail()
	{
		$thumbnail	= '';
		$thumbnail	= $this->thumbnail;

		return CVideosHelper::getIURL($thumbnail);
	}

	/**
	 *
	 *
	 * @return $embedCode specific embeded code to play the video
	 */
	/*public function getViewHTML($videoId, $videoWidth, $videoHeight)
	{
		if (!$videoId)
		{
			$videoId	= $this->videoId;
		}

		$uniqid = uniqid('vimeo');

		$embedCode = '<div class="guru-lesson-video-wrapper">';
		
		$embedCode .= '<iframe id="' . $uniqid . '" width="' . $videoWidth . '" height="' . $videoHeight . '" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>';
		
		$embedCode .= '<script>
			(function() {
				var timer = setInterval(function() {
					var iframe = document.getElementById("' . $uniqid . '"),
						url, blocked, favicon, img;

					if (iframe) {
						clearInterval( timer );
						url = "' . CVideosHelper::getIURL('http://player.vimeo.com/video/' . $videoId ) . '";
						blocked = document.getElementById("' . $uniqid . '-blocked");
						favicon = "https://vimeo.com/favicon.ico";

						img = new Image();
						img.onload = function() { iframe.src = url };
						img.onerror = function() { iframe.style.visibility = "hidden"; blocked.style.display = "block" };
						img.src = favicon;
					}

				}, 200 );
			})();
		</script>';

        return $embedCode;
	}*/
	
	public function getViewHTML($videoId, $videoWidth, $videoHeight)
	{
		if (!$videoId)
		{
			$videoId	= $this->videoId;
		}

		$embedCode = '<iframe src="'.CVideosHelper::getIURL('http://player.vimeo.com/video/' . $videoId ).'" width="' . $videoWidth . '" height="' . $videoHeight . '" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>';

        return $embedCode;
	}
}
