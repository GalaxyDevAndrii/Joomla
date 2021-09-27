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
 * Class to manipulate data from Truveo
 *
 * @access	public
 */
class PTableVideoTruveo extends PVideoProvider
{
	var $xmlContent = null;
	var $url 		= '';
	var $videoId	= '';

	/**
	 * Return feedUrl of the video
	 */
	public function getFeedUrl()
	{
		$videoId = explode("/",$this->videoId);
		return 'http://xml.truveo.com/apiv3?appid=1x1jhj64466mi12ia&method=truveo.videos.getVideos&query='.$videoId[2];
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

		$parser = new SimpleXMLElement($this->xmlContent);
		$videoElement = $parser->video;

		// Get Video Title
		$element = $videoElement->getElementByPath('videoset/video/title');
		$this->title = $element->data();
		// Get Video description
		$element = $videoElement->getElementByPath('videoset/video/description');
		$this->description = $element ? $element->data() : '';
		// Get Video duration
		if($videoElement->getElementByPath('videoset/video/runtime')==true){
			$element = $videoElement->getElementByPath('videoset/video/runtime');
			$this->duration = $element->data();
		}
		// Get Video thumbnail
		$element = $videoElement->getElementByPath('videoset/video/thumbnailurl');
		$this->thumbnail = $element->data();

		return true;
	}

	/**
	 * Extract video id from the video url submitted by the user
	 *
	 * @access	public
	 * @param	video url
	 * @return videoid
	 */
	public function getId()
	{
        $pattern    = '/http\:\/\/(\w{3}\.)?truveo.com\/(.*)/';
        preg_match( $pattern, $this->url, $match);

        return !empty($match[2]) ? $match[2] : null;
	}


	/**
	 * Return the video provider's name
	 *
	 */
	public function getType()
	{
		return 'truveo';
	}

	public function getTitle()
	{
		return $this->title;
	}

	public function getDescription()
	{
		return $this->description;
	}

	public function getDuration()
	{
		return $this->duration;
	}

	public function getThumbnail()
	{
		return CVideosHelper::getIURL($this->thumbnail);
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

		$videoId	= explode("/",$videoId);
		$xmlContent	= CRemoteHelper::getContent('http://xml.truveo.com/apiv3?appid=1x1jhj64466mi12ia&method=truveo.videos.getVideos&query='.$videoId[2]);
		
		$parser = new SimpleXMLElement($xmlContent);
		$videoElement = $parser->document;

		//get Video embed code
		$element	= $videoElement->getElementByPath('videoset/video/videoresultembedtag');
		$embedTag	= $element->data();

		$pattern	=  "'src=\"(.*?)\"'s";
		preg_match_all($pattern, $embedTag, $matches);
		if($matches)
		{
			$flashUrl = ' src="'.rawurldecode($matches[1][0]).'" ';
			$src = $matches[1][0];
		}
// 		$pattern	=  "'FlashVars=\'(.*?)\''s";
// 		$pattern	= '';
// 		preg_match_all($pattern, $embedTag, $matches);
// 		if(!empty($matches))
// 		{
// 			echo JUtility::dump($matches);
// 			echo count($matches);
// 			exit;
// 			$flashVar = ' FlashVars=\''.rawurldecode($matches[1][0]).'\' ';
// 		}
//		$embedCode	= "<embed ".$flashUrl.$flashVar." allowFullScreen='true' width='".$videoWidth."' height='".$videoHeight."' type='application/x-shockwave-flash' pluginspage='http://www.macromedia.com/go/getflashplayer' allowScriptAccess='always'></embed>";
		$embedCode	= "<embed flashvars='fs=1' allowfullscreen='true' src='".CVideosHelper::getIURL($src)."' type='application/x-shockwave-flash' width='$videoWidth' height='$videoHeight' wmode='transparent'></embed>";
		return $embedCode;
	}
}
