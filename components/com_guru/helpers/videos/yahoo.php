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
 * Class to manipulate data from Yahoo
 *
 * @access	public
 */
class PTableVideoYahoo extends PVideoProvider
{
	var $xmlContent = null;
	var $url 		= '';
	var $videoId	= '';

	/**
	 * Return feedUrl of the video
	 */
	public function getFeedUrl()
	{
		//return 'http://animalvideos.yahoo.com/video-detail?vid=27094620';
		//return 'http://video.yahoo.com/watch/'.$this->videoId;
		return $this->url;
	}

	/**
	 * Extract Yahoo video id from the video url submitted by the user
	 *
	 * @access	public
	 * @param	video url
	 * @return videoid
	 */
	public function getId()
	{
		$videoId  = '';
		$urlparse = parse_url( $this->url );

		// Johny @ 2 april - yahoo video (video.yahoo) is now known as yahoo Screen (http://screen.yahoo.com)
		// lets keep the old code, just in case
		if( strpos(strtolower($urlparse['host']) , 'screen.yahoo') !== FALSE){
			$videoId = basename($urlparse['path'],'.html');	//the ID will include some permalink to be passed when generating HTML
		}
		else{
			parse_str( parse_url( $this->url, PHP_URL_QUERY ), $result );
		if(isset($result['vid'])) $videoId = $result['vid'];

			// If we have no match, need to check for alternative
			if( empty($videoId) )
			{
				$id = explode('-',$this->url);
				$id = $id[count($id)-1];
				$id = explode('.',$id);
				$videoId = $id[0];
				/*
				if($id < 1){
					$pattern =  '/flashvars="vid=(.*)&amp;/';
					preg_match( $pattern, $this->xmlContent, $match );

					if( $match[0] ){
						$videoId    = $match[0];
					}
				}
				*/
			}
		}
		return $videoId;
	}


	/**
	 * Return the video provider's name
	 *
	 */
	public function getType()
	{
		return 'yahoo';
	}

	public function getTitle()
	{
		$title = '';

		$pattern =  "'<meta property=\"og:title\" content=\"(.*?)\"'s";
		preg_match_all($pattern, $this->xmlContent, $matches);

		$title = $matches[1][0];

		if($title == ''){
			$pattern =  "'name=\"context_title\" value=\"(.*?)\"'s";
			preg_match_all($pattern, $this->xmlContent, $matches);

			$title = $matches[1][0];
		}

		return $title;
	}

	public function getDescription()
	{
		$description	= '';

		// Get description
		$pattern =  "'<meta property=\"og:description\" content=\"(.*?)\"'s";
		preg_match_all($pattern, $this->xmlContent, $matches);

		$description = $matches[1][0];

		if($description == ''){
			$pattern =  "'desc\":\"(.*?)\"'s";
			preg_match_all($pattern, $this->xmlContent, $matches);

			$description = stripslashes(@$matches[1][0]);
		}

		return $description;
	}

	public function getDuration()
	{
		$duration = null;

		// Get description
		$pattern =  "'x-duration=\"(.*?)\"'s";
		preg_match_all($pattern, $this->xmlContent, $matches);
		$duration = (isset($matches[1][0])) ? $matches[1][0] : '';

		if($duration == ''){
			$pattern =  "'durtn\":\"(.*?)\"'s";
			preg_match_all($pattern, $this->xmlContent, $matches);

			$duration = @$matches[1][0];
		}

		if($duration != ''){
			$sec = 0;
			$time = explode(':',$duration);
			if($time[0] > 0){
				$sec = $time[0]*60;
			}
			$duration = $sec + $time[1];
		}else{
			$duration = false;
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

		$thumbnail = '';

		$pattern =  "'thmb_url\":\"(.*?)\"'s";
		preg_match_all($pattern, $this->xmlContent, $matches);

		$thumbnail = stripslashes(@$matches[1][0]);

		if($thumbnail == ''){
			$pattern =  "'<meta property=\"og:image\" content=\"(.*?)\"'s";

			preg_match_all($pattern, $this->xmlContent, $matches);

			if( $matches && !empty($matches[1][0]) )
			{
				$thumbnail = urldecode($matches[1][0]);
			}
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
        $id = explode('-',$videoId);
        $id = $id[count($id)-1];

        $embedCode = (!ctype_digit($id))
					? '<object type="application/x-shockwave-flash" data="https://s.yimg.com/rx/fbp/3/player.swf?region=US&amp;site=movies&amp;onet=false&amp;version=10.2.0&amp;allowScriptAccess=always&amp;allowNetworking=all&amp;allowFullscreen=true&amp;ise=false&amp;pv=1&amp;pgsid=2143413669&amp;yvapaid=167&amp;uuid='.$videoId.'" height="'.$videoHeight.'" width="'.$videoWidth.'" id="'.$videoId.'"> </object>'
                    : '<iframe width="'.$videoWidth.'" height="'.$videoHeight.'" scrolling="no" frameborder="0" src="'.CVideosHelper::getIURL('http://screen.yahoo.com/'.$videoId.'.html?format=embed&player_autoplay=false').'"></iframe>';
		return $embedCode;
	}
}