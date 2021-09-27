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
 * Class to manipulate data from YouTube
 *
 * @access	public
 */
class PTableVideoYoutube extends PVideoProvider
{
	var $xmlContent = null;
	var $url = '';
	var $videoId = null;

	/**
	 * Return feedUrl of the video
	 */
	public function getFeedUrl()
	{
		return 'http://gdata.youtube.com/feeds/api/videos/' . $this->getId();
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
	
		// Connect and get the remote video
		if ( $this->xmlContent == 'Invalid id')
		{
			$this->setError(JText::_('COM_COMMUNITY_VIDEOS_INVALID_VIDEO_ID_ERROR'));
			return false;
		}

		if($this->xmlContent == 'Video not found')
		{
			$this->setError(JText::_('COM_COMMUNITY_VIDEOS_YOUTUBE_ERROR'));
			return false;
		}





		return true;
	}

	/**
	 * Extract YouTube video id from the video url submitted by the user
	 *
	 * @access	public
	 * @param	video url
	 * @return videoid
	 */
	public function getId()
	{
		if($this->videoId){
			return $this->videoId;
		}

		preg_match_all('~
	        # Match non-linked youtube URL in the wild. (Rev:20111012)
	        https?://         # Required scheme. Either http or https.
	        (?:[0-9A-Z-]+\.)? # Optional subdomain.
	        (?:               # Group host alternatives.
	          youtu\.be/      # Either youtu.be,
	        | youtube\.com    # or youtube.com followed by
	          \S*             # Allow anything up to VIDEO_ID,
	          [^\w\-\s;]       # but char before ID is non-ID char.
	        )                 # End host alternatives.
	        ([\w\-]{11})      # $1: VIDEO_ID is exactly 11 chars.
	        (?=[^\w\-]|$)     # Assert next char is non-ID or EOS.
	        (?!               # Assert URL is not pre-linked.
	          [?=&+%\w]*      # Allow URL (query) remainder.
	          (?:             # Group pre-linked alternatives.
	            [\'"][^<>]*>  # Either inside a start tag,
	          | </a>          # or inside <a> element text contents.
	          )               # End recognized pre-linked alts.
	        )                 # End negative lookahead assertion.
	        [?=&+%\w]*        # Consume any URL (query) remainder.
	        ~ix',
			$this->url, $matches);

    	if( isset($matches) && !empty($matches[1]) ){
    		return $matches[1][0];
		}

		return false;
	}

	/**
	 * Return the video provider's name
	 *
	 */
	public function getType()
	{
		return 'youtube';
	}

	public function getTitle()
	{
		$title = "";
		$db = JFactory::getDbo();
		$video_id = $this->videoId;
		
		$sql = "select youtube_key from #__guru_config";
		$db->setQuery($sql);
		$db->execute();
		$key = $db->loadColumn();
		$key = @$key["0"];
		
		if(trim($key) != ""){
			$path = 'https://www.googleapis.com/youtube/v3/videos?part=snippet&id='.$video_id.'&key='.$key;
			$data = json_decode(file_get_contents($path), true);
		
			if(isset($data["items"]["0"]["snippet"]["title"])){
				$title = trim($data["items"]["0"]["snippet"]["title"]);
			}
		}
		
		/*$title = '';
		// Store video title
		$pattern =  "/<title type='text'>(.*?)<\/title>/i";
		preg_match_all($pattern, $this->xmlContent, $matches);
		
		if($matches)
		{
		    $title = @$matches[1][0];
		}*/

		return $title;
	}

	public function getDescription()
	{
		$description = '';

			// Store description
			$pattern =  "/<content type='text'>(.*?)<\/content>/s";
			preg_match_all($pattern, $this->xmlContent, $matches);
			if($matches && !empty($matches[1][0]) )
			{
			    $description = $matches[1][0];
			}

		return $description;
	}

	public function getDuration()
	{
		$duration = 0;
		$db = JFactory::getDbo();
		$video_id = $this->videoId;
		
		$sql = "select youtube_key from #__guru_config";
		$db->setQuery($sql);
		$db->execute();
		$key = $db->loadColumn();
		$key = @$key["0"];
		
		if(trim($key) != ""){
			$path = 'https://www.googleapis.com/youtube/v3/videos?part=contentDetails&id='.$video_id.'&key='.$key;
			$data = json_decode(file_get_contents($path), true);
			
			if(isset($data["items"]["0"]["contentDetails"]["duration"])){
				$duration = trim($data["items"]["0"]["contentDetails"]["duration"]);
				$interval = new DateInterval($duration);
				$duration = $interval->h * 3600 + $interval->i * 60 + $interval->s;
			}
		}
		
		/*// Store duration
		$pattern =  "/seconds='(.+?)'/i";
		preg_match_all($pattern, $this->xmlContent, $matches);
		if($matches)
		{
			$duration = @$matches[1][0];
		}*/

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
		return CVideosHelper::getIURL('http://img.youtube.com/vi/' . $this->getId() . '/hqdefault.jpg');
	}

	/**
	 *
	 *
	 * @return $embedvideo specific embeded code to play the video
	 */
	public function getViewHTML($videoId, $videoWidth, $videoHeight)
	{
		//$config = CFactory::getConfig();
		if (!$videoId)
		{
			$videoId	= $this->getId();
		}

		$html = '';
		//if($config->get('use_youtube_iframe_embed'))
		if(true)
		{
			// Use new iframe embed method
			$html = '<iframe class="youtube-player" type="text/html" width="'.$videoWidth.'" height="'.$videoHeight.'" src="'.CVideosHelper::getIURL("http://www.youtube.com/embed/".$videoId).'" frameborder="0" allowfullscreen>
				</iframe>';
		}
		else
		{
			$html = "<embed src=\"". CVideosHelper::getIURL("http://www.youtube.com/v/" .$videoId. "&hl=en&fs=1&hd=1&showinfo=0&rel=0") . "\" type=\"application/x-shockwave-flash\" allowscriptaccess=\"always\" allowfullscreen=\"true\" width=\"".$videoWidth."\" height=\"".$videoHeight. "\" wmode=\"transparent\"></embed>";
		}

		return $html;
	}
}