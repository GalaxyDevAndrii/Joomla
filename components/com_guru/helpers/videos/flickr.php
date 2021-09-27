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
 * Class to manipulate data from Flickr
 *
 * @access	public
 */
class PTableVideoFlickr extends PVideoProvider
{
	var $xmlContent = null;
	var $url = '';
	var $videoId	= '';

	/**
	 * Return feedUrl of the video
	 */
	public function getFeedUrl()
	{
		return 'http://www.flickr.com/photos/'.$this->getId();
	}

	public function isValid()
	{
		$vid 				= explode('/',$this->getId());
		$url 				= 'http://api.flickr.com/services/rest/';
		$data['method']		= 'flickr.photos.getInfo';
		//Change api key later.Now is testing
		$data['api_key'] 	= 'c216101654dab6ffbe864ed15155a520';
		$data['photo_id'] 	= $vid[1];
		$data['format']		= 'php_serial';

		$this->xmlContent 	= unserialize(CRemoteHelper::post($url,$data,false));

		return true;
	}

	/**
	 * Extract Flickr video id from the video url submitted by the user
	 *
	 * @access	public
	 * @param	video url
	 * @return videoid
	 */
	public function getId()
	{
        $pattern    = '/http\:\/\/\w{3}\.?flickr.com\/photos\/(.*)/';
        preg_match( $pattern, $this->url, $match );

        return !empty($match[1]) ? $match[1] : null ;
	}


	/**
	 * Return the video provider's name
	 *
	 */
	public function getType()
	{
		return 'flickr';
	}

	public function getTitle()
	{

		$title = $this->xmlContent['photo']['title']['_content'];
		return $title;
	}

	public function getDescription()
	{
		$description	= $this->xmlContent['photo']['description']['_content'];
		return $description;
	}

	public function getDuration()
	{
		return $this->xmlContent['photo']['video']['duration'];
	}


	/**
	 *
	 * @param $videoId
	 * @return unknown_type
	 */
	public function getThumbnail()
	{
		$thumbnail = 'http://farm'.$this->xmlContent['photo']['farm'].'.staticflickr.com/'.$this->xmlContent['photo']['server'].'/'.$this->xmlContent['photo']['id'].'_'.$this->xmlContent['photo']['secret'].'_z.jpg';
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

		$xmlContent = CRemoteHelper::getContent('http://www.flickr.com/photos/'.$videoId);
		$pattern =  "'<link rel=\"video_src\" href=\"(.*?)\"( \/)?(>)'s";
		preg_match_all($pattern, $xmlContent, $matches);
		if($matches)
		{
			$videoUrl = rawurldecode($matches[1][0]);
		}

		return '<embed width="'.$videoWidth.'" height="'.$videoHeight.'" wmode="transparent" allowFullScreen="true" type="application/x-shockwave-flash" src="'.  CVideosHelper::getIURL($videoUrl).'"/>';
	}
}