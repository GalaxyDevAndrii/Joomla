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
 * Class to manipulate data from Blip
 *
 * @access	public
 */
class PTableVideoBlip extends PVideoProvider
{
	var $xmlContent = null;
	var $url = '';
	var $videoId = null;

	/**
	 * Return feedUrl of the video
	 */
	public function getFeedUrl()
	{
		return $this->url.'?skin=rss';
	}

	/**
	 * Extract Blip video id from the video url submitted by the user
	 *
	 * @access	public
	 * @param	video url
	 * @return	videoid
	 */
	public function getId()
	{

		$videoId  = '';

		$pattern =  "/<blip:item_id>(.*)<\/blip:item_id>/";
		preg_match( $pattern, $this->xmlContent, $match );

		if( isset($match[1]) ){
			$videoId    = $match[1];
		}

		if($videoId == ''){
			$id = explode('-',$this->url);
			$videoId = $id[count($id)-1];
		}

		return $videoId;
	}

	/**
	 * Return the video provider's name
	 *
	 */
	public function getType()
	{
		return 'blip';
	}

	public function getTitle()
	{
		$title = '';
		// Store video title
		$pattern =  "/<title>(.*)<\/title>/i";
		preg_match_all($pattern, $this->xmlContent, $matches);
		if($matches)
		{
			$title = isset($matches[1][1])?$matches[1][1]:'';
            if(empty($title)){
                $title = $matches[1][2];
            }
		}

		return $title;
	}

	public function getDescription()
	{
		$description = '';
		// Store description
		$pattern =  "'<blip\:puredescription>(.*?)<\/blip\:puredescription>'s";
		preg_match_all($pattern, $this->xmlContent, $matches);

		if($matches)
		{
			$description = $this->str_ireplace( '&apos;' , "'" , $matches[1][0] );
			$description = $this->str_ireplace( '<![CDATA[', '', $description );
			$description = $this->str_ireplace( ']]>', '', $description );
		}

		return $description;
	}

	public function getDuration()
	{
		$duration = '';
		// Store duration
		$pattern =  "'<blip:runtime>(.*?)<\/blip:runtime>'s";
		preg_match_all($pattern, $this->xmlContent, $matches);
		if($matches)
		{
			$duration = $matches[1][0];
		}

		return $duration;
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
		$pattern =  "'<media:thumbnail url=\"(.*?)\"'s";
		preg_match_all($pattern, $this->xmlContent, $matches);

		if( !empty($matches[1][0]) )
		{
			$thumbnail = $matches[1][0];
		}
		else
		{
			$thumbnail = 'http://a.blip.tv/skin/blipnew/placeholder_video.gif';
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

		$remoteFile	= 'http://blip.tv/file/'.$videoId.'?skin=rss';
		$xmlContent = CRemoteHelper::getContent($remoteFile);
		// get embedFile
		$pattern	= "'<blip:embedLookup>(.*?)<\/blip:embedLookup>'s";
		$embedFile	= '';
		preg_match_all($pattern, $xmlContent, $matches);
		if($matches)
		{
			$embedFile = $matches[1][0];
		}

		return '<iframe src="'.CVideosHelper::getIURL('http://blip.tv/play/'.$embedFile.'.x?p=1').'" width="'.$videoWidth.'" height="'.$videoHeight.'" frameborder="0" allowfullscreen></iframe><embed type="application/x-shockwave-flash" src="'.CVideosHelper::getIURL('http://a.blip.tv/api.swf#'.$embedFile).'" style="display:none"></embed>';
	}


	public function getEmbedCode($videoId, $videoWidth, $videoHeight)
	{
		return $this->getViewHTML($videoId, $videoWidth, $videoHeight);
	}
	
	function str_ireplace($search, $replace, $str, $count = NULL) {
        if ($count === FALSE) {
            return self::_utf8_ireplace($search, $replace, $str);
        } else {
            return self::_utf8_ireplace($search, $replace, $str, $count);
        }
    }
	
	public function _utf8_ireplace($search, $replace, $str, $count = NULL) {

        if (!is_array($search)) {

            $slen = strlen($search);
            $lendif = strlen($replace) - $slen;
            if ($slen == 0) {
                return $str;
            }

            $search = strtolower($search);

            $search = preg_quote($search, '/');
            $lstr = strtolower($str);
            $i = 0;
            $matched = 0;
            while (preg_match('/(.*)' . $search . '/Us', $lstr, $matches)) {
                if ($i === $count) {
                    break;
                }
                $mlen = strlen($matches[0]);
                $lstr = substr($lstr, $mlen);
                $str = substr_replace($str, $replace, $matched + strlen($matches[1]), $slen);
                $matched += $mlen + $lendif;
                $i++;
            }
            return $str;
        } else {

            foreach (array_keys($search) as $k) {

                if (is_array($replace)) {

                    if (array_key_exists($k, $replace)) {

                        $str = $this->_utf8_ireplace($search[$k], $replace[$k], $str, $count);
                    } else {

                        $str = $this->_utf8_ireplace($search[$k], '', $str, $count);
                    }
                } else {

                    $str = $this->_utf8_ireplace($search[$k], $replace, $str, $count);
                }
            }
            return $str;
        }
    }
}
