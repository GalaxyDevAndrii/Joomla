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
class PTableVideoFile extends PVideoProvider
{
	var $xmlContent	= null;
	var $url		= '';

	/**
	 * Return feedUrl of the video
	 */
	public function getFeedUrl()
	{
		return true;
	}


	/*
	 * Return true if successfully connect to remote video provider
	 * and the video is valid
	 */
	public function isValid()
	{
		return true;
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
		//check for embed code format
		//$pos_e = strpos($this->url, "youtube.com/v/");
		$pos_u = strpos($this->url, "video/");

		////TODO: User regular expression instead
		if ($pos_u === false) {
			return null;
		} else if ($pos_u) {
			$pos_u_start = $pos_u + 6;

			$code = substr($this->url, $pos_u_start);
			$code = strip_tags($code );
			$code = preg_replace("/[^a-zA-Z0-9s_-]/", "", $code);
		}

		return $code;
	}


	/**
	 * Return the video provider's name
	 *
	 */
	public function getType()
	{
		return 'file';
	}

	public function getTitle()
	{
		return true;
	}

	public function getDescription()
	{
		return true;
	}

	public function getDuration()
	{
		return true;
	}

	public function getThumbnail()
	{
		return true;
	}

	public function isMp4($file){
		$path_parts = pathinfo($file);                
		if( isset($path_parts['extension']) && $path_parts['extension']=='mp4'){
			return true;
		}
		return false;
	}

	/**
	 *
	 *
	 * @return $embedvideo specific embeded code to play the video
	 */
	public function getViewHTML($videoId, $videoWidth, $videoHeight)
	{
		$video	= JTable::getInstance( 'Video' , 'CTable' );
		$video->load( $videoId );

		$tmpl	= new CTemplate();
		$tmpl->setRef( 	'video'		, $video );
		$tmpl->set( 	'switch'	, 'default' );

		$isMp4 = $this->isMp4($video->getFlv());
		if(!$isMp4){
			return $tmpl->fetch( 'videos.flowplayer' );
		}else{
			return $tmpl->fetch( 'videos.html5' );
		}
		
	}

	public function getEmbedCode($videoId, $videoWidth, $videoHeight)
	{
		$video	= JTable::getInstance( 'Video' , 'CTable' );
		$video->load( $videoId );

		$tmpl	= new CTemplate();
		$tmpl->setRef( 	'video'		, $video );
		$tmpl->set( 	'switch'	, 'pureObjectTag' );

		$isMp4 = $this->isMp4($video->getFlv());
		if(!$isMp4){
			return $tmpl->fetch( 'videos.flowplayer' );
		}else{
			return $tmpl->fetch( 'videos.html5' );
		}
	}
}