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
jimport( 'joomla.environment.uri' );

class CVideosHelper
{
	static public function validateVideo( $fileName )
	{
		jimport('joomla.filesystem.file');
		$fileExt	= JFile::getExt($fileName);

		$fileType	= array('flv', 'avi', 'mov', 'mp4'); // need expansion

		return in_array($fileExt, $fileType);
	}

	static public function formatDuration($duration = 0, $format = 'HH:MM:SS')
	{
		if ($format == 'seconds' || $format == 'sec') {
			$arg = explode(":", $duration);

			$hour	= isset($arg[0]) ? intval($arg[0]) : 0;
			$minute	= isset($arg[1]) ? intval($arg[1]) : 0;
			$second	= isset($arg[2]) ? intval($arg[2]) : 0;

			$sec = ($hour*3600) + ($minute*60) + ($second);
			return (int) $sec;
		}

		if ($format == 'HH:MM:SS' || $format == 'hms') {
			$timeUnits = array
			(
				'HH' => $duration / 3600 % 24,
				'MM' => $duration / 60 % 60,
				'SS' => $duration % 60
			);

			$arg = array();
			foreach ($timeUnits as $timeUnit => $value) {
				$arg[$timeUnit] = ($value > 0) ? $value : 0;
			}

			$hms = '%02s:%02s:%02s';
			$hms = sprintf($hms, $arg['HH'], $arg['MM'], $arg['SS']);
			return $hms;
		}
	}

	/**
	 *	Remove Extra Leading Zeroes
	 *	00:01:30 will became 01:30
	 *
	 *	@params	string	$hms	HH:MM:SS value
	 *	@return	string	nice HMS
	 */
	static public function toNiceHMS($hms)
	{
		$arr	= array();
		$arr	= explode(':', $hms);

		if ($arr[0] == '00') {
			array_shift($arr);
		}

		return implode(':', $arr);
	}

	static public function getVideoLinkPatterns()
	{
		// Pattern for video providers
		$pattern	= array();

		$pattern[] = '/http\:\/\/vids.myspace.com\/index.cfm\?fuseaction\=([a-zA-Z0-9][a-zA-Z0-9$_.+!*(),;\/\?:@&]*)\=(\d{1,8})/';
		$pattern[] = '/http\:\/\/(\w{3}\.)?youtube.com\/watch\?v\=([_-])?([a-zA-Z0-9][a-zA-Z0-9$_.+!*(),;\/\?:@&~=%-]*)/';
		$pattern[] = '/http\:\/\/(\w{3}\.)?vimeo.com\/(hd#)?(\d*)/';
		$pattern[] = '/http\:\/\/(\w{2}\.)?video.yahoo.com\/watch\/(\d{1,8})\/(\d{1,8})/';
		$pattern[] = '/http\:\/\/video.google.(\w{2,4})\/videoplay\?docid=(-?\d{1,19})(&.*)?/';
		$pattern[] = '/http\:\/\/(\w{3}\.)?revver.com\/video\/(\d{1,7})\/([a-zA-Z0-9][a-zA-Z0-9$_.+!*(),;\/\?:@&~=%-]*)/';
		$pattern[] = '/http\:\/\/(\w{3}\.)?flickr.com\/photos\/(.*)\/(\d{1,10})/';
		$pattern[] = '/http\:\/\/(\w{3}\.)?viddler.com\/explore\/(.*)\/videos\/(\d{1,3})\//';
		$pattern[] = '/http\:\/\/(\w{3}\.)?liveleak.com\/view\?i\=([a-zA-Z0-9][a-zA-Z0-9$_.+!*(),;\/\?:@&~=%-]*)/';
		$pattern[] = '/http\:\/\/(\w{3}\.)?break.com\/index\/(.*?).html/';
		$pattern[] = '/http\:\/\/(\w{3}\.)?dailymotion.com\/(.*)\/video\/(.*)/';
		$pattern[] = '/http\:\/\/(\w{3}\.)?blip.tv\/file\/(\d{1,7})?([a-zA-Z0-9][a-zA-Z0-9$_.+!*(),;\/\?:@&~=%-]*)/';
		$pattern[] = '/http\:\/\/(\w{3}\.)?metacafe.com\/watch\/(\d{1,7})?([a-zA-Z0-9][a-zA-Z0-9$_.+!*(),;\/\?:@&~=%-]*)/';
		$pattern[] = '/http\:\/\/(media\.)?photobucket.com\/video\/([a-zA-Z0-9][a-zA-Z0-9$_.+!*(),;\/\?:@&~=%-\s]*)/';

		return $pattern;
	}

	static public function getVideoLinkMatches( $content )
	{
		$pattern	= array();
		$matches	= array();

		$pattern	= CVideosHelper::getVideoLinkPatterns();

		for( $i = 0; $i < count( $pattern ); $i++ )
		{
			//Match the first video link
			preg_match($pattern[$i], $content, $match );

			if( $match )
			{
				$matches[]	= $match[0];
			}

		}

		return $matches;
	}

	static public function getVideoLink($content, $videoWidth='425', $videoHeight='344')
	{
		$pattern	= array();
		$videoLinks	= array();

		$pattern	= CVideosHelper::getVideoLinkPatterns();

		for( $i = 0; $i < count( $pattern ); $i++ )
		{
			//Match all video links
			preg_match_all($pattern[$i], $content, $match );

			if( $match )
			{
				$videoLinks[]	= $match[0];
			}

		}

		foreach($videoLinks as $videoLink)
		{
			// Replace the URL with the embedded code
			foreach($videoLink as $videoLinkUrl)
			{
				$parsedVideoLink	= parse_url($videoLinkUrl);
				preg_match('/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i', $parsedVideoLink['host'], $matches);
				$domain	= $matches['domain'];

				if (!empty($domain))
				{
					$provider		= explode('.', $domain);
					$providerName	= strtolower($provider[0]);
					
					if($providerName == "youtu"){
						$providerName = "youtube";
					}
					
					$libraryPath	= COMMUNITY_COM_PATH .'/libraries/videos' .'/'. $providerName . '.php';

					require_once($libraryPath);
					$className		= 'CTableVideo' . ucfirst($providerName);
					$videoObj		= new $className();
					$videoObj->init($videoLinkUrl);
					$video_id		= $videoObj->getId();
					$videoPlayer	= $videoObj->getViewHTML($video_id, $videoWidth, $videoHeight);
					$content = str_replace( $videoLinkUrl, $videoPlayer, $content );
				}
			}
		}

		return $content;
	}

	static public function getVideoReturnUrlFromRequest($videoType='default')
	{
		$mainframe	= JFactory::getApplication();
		$jinput 	= $mainframe->input;

		$creator_type	= $jinput->get('creatortype' , VIDEO_USER_TYPE, 'NONE' );
		$groupId		= JFactory::getApplication()->input->get( 'groupid' , 0 );
		$my				= JFactory::getUser();

		// we use this if redirect url is defined
		$redirectUrl	= $jinput->post->get('redirectUrl' , '' , 'STRING');
		if (!empty($redirectUrl))
		{
			return base64_decode($redirectUrl);
		}

		if ($creator_type == VIDEO_GROUP_TYPE || !empty($groupId))
		{
			$defaultUrl	= CRoute::_('index.php?option=com_community&view=groups&task=viewgroup&groupid=' . $groupId , false );
			$pendingUrl	= CRoute::_('index.php?option=com_community&view=videos&task=mypendingvideos&userid='.$my->id.'&groupid='.$groupId, false);
			return ($videoType == 'pending') ? $pendingUrl : $defaultUrl;
		}

		$defaultUrl	= CRoute::_('index.php?option=com_community&view=videos&task=myvideos&userid=' . $my->id , false );
		$pendingUrl	= CRoute::_('index.php?option=com_community&view=videos&task=mypendingvideos&userid='.$my->id, false);
		return ($videoType == 'pending') ? $pendingUrl : $defaultUrl;
	}

	static public function getVideoSize($retunType='default', $displayType='display')
	{
		$config		= CFactory::getConfig();

		switch ($displayType)
		{
			case 'wall':
				$videoSize	= $config->get('wallvideossize');
				break;
			case 'activities':
				$videoSize	= $config->get('activitiesvideosize');
				break;
			case 'display':
			default:
				$videoSize	= $config->get('videosSize');
				break;
		}

		$arrVideoSize	= array();
		$arrVideoSize	= explode('x', $videoSize, 2);

		switch ($retunType)
		{
			case 'width':
				$ret	= $arrVideoSize[0];
				break;
			case 'height':
				$ret	= $arrVideoSize[1];
				break;
			default:
				$ret	= $videoSize;
				break;
		}

		return $ret;
	}

	static public function getValidMIMEType()
	{
		$mimeType	= array(
			'video/x-flv',
			'video/mpeg',
			'video/mp4',
			'video/ogg',
			'video/quicktime',
			'video/x-ms-wmv',
			'video/3gpp',
			'video/x-msvideo',
			'video/x-dv',
			'video/x-m4v',
			'video/x-sgi-movie',
			'video/3gpp',
			'video/3gpp2',
			'video/x-la-asf',
			'video/x-ms-asf',
			'video/animaflex',
			'video/avi',
			'video/msvideo',
			'video/avs-video',
			'video/fli',
			'video/x-fli',
			'video/gl',
			'video/x-gl',
			'video/x-isvideo',
			'video/x-motion-jpeg',
			'video/x-mpeg',
			'video/x-mpeq2a',
			'video/x-qtc',
			'video/vnd.rn-realvideo',
			'video/x-scm',
			'video/vdo',
			'video/vivo',
			'video/vnd.vivo',
			'video/vosaic',
			'video/x-amt-demorun',
			'video/x-amt-showrun',
			'video/H261',
			'video/H263',
			'video/H263-1998',
			'video/H263-2000',
			'video/H264',
			'video/JPEG',
			'video/dl',
			'video/x-mng',
			'video/x-ms-wm',
			'video/x-ms-wmx',
			'video/x-ms-wvx'
		);
		return $mimeType;
	}

	public static $rand_agent=true;
	// Default curl options
	public static $default_options = array
	(
		CURLOPT_USERAGENT => "CnVideoApi (+http://www.liushan.net;version:0.11)",
		CURLOPT_CONNECTTIMEOUT => 5,
		CURLOPT_TIMEOUT        => 200,
	);

	private static function _rand_agent(){
		$useragent_arr=array(
			"CnVideoApi (+http://www.liushan.net;version:0.11)",
			"Mozilla/4.0 (compatible; MSIE 5.5; Windows NT 6.1;)",
			"Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 6.1;)",
			"Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 6.1;)",
			"Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.1;)",
			"Mozilla/4.0 (compatible; MSIE 9.0; Windows NT 6.1;)",
			"Mozilla/5.0 (Windows; U; Windows NT 6.1; zh-CN; rv:1.9.2.12) Gecko/20101026 Firefox/3.6.13",
			"Baiduspider+(+http://www.baidu.com/search/spider.htm)",
			"Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)",
			"Googlebot-Image/1.0",
			"Feedfetcher-Google; (+http://www.google.com/feedfetcher.html;)",
			"Mozilla/5.0 (compatible; Yahoo! Slurp China; http://misc.yahoo.com.cn/help.html)",
			"Mozilla/5.0 (compatible; YodaoBot/1.0; http://www.yodao.com/help/webmaster/spider/;)" ,
			"Sosospider+(+http://help.soso.com/webspider.htm)",
			"Sogou Web Sprider(compatible; Mozilla 4.0; MSIE 6.0; Windows NT 5.1; SV1; Avant Browser; InfoPath.1; .NET CLR 2.0.50727; .NET CLR1.1.4322)",
			"Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US) AppleWebKit/525.13 (KHTML, like Gecko) Chrome/0.2.149.27 Safari/525.13; InfoPath.1; .NET CLR 2.0.50727; .NET CLR1.1.4322)",
		);
		self::$default_options[CURLOPT_USERAGENT]=$useragent_arr[rand(0,(count($useragent_arr)-1))];
	}

	/**
	 * Returns the output of a remote URL. Any [curl option](http://php.net/curl_setopt)
	 * may be used.
	 *
	 *     // Do a simple GET request
	 *     $data = Remote::get($url);
	 *
	 *     // Do a POST request
	 *     $data = Remote::get($url, array(
	 *         CURLOPT_POST       => TRUE,
	 *         CURLOPT_POSTFIELDS => http_build_query($array),
	 *     ));
	 *
	 * @param   string   remote URL
	 * @param   array    curl options
	 * @return  string
	 * @throws  Videoapi_Exception
	 */
	public static function getVideoInfo($url, array $options = NULL)
	{
		if(self::$rand_agent){
			self::_rand_agent();
		}
		if ($options === NULL)
		{
			// Use default options
			$options = self::$default_options;
		}
		else
		{
			// Add default options
			$options = $options + self::$default_options;
		}

		// The transfer must always be returned
		$options[CURLOPT_RETURNTRANSFER] = TRUE;

		// Open a new remote connection
		$remote = curl_init($url);

		// Set connection options
		if ( ! curl_setopt_array($remote, $options))
		{
			throw new Videoapi_Exception("Failed to set CURL options, check CURL documentation:  http://php.net/curl_setopt_array");
		}

		// Get the response
		$response = curl_exec($remote);

		// Get the response information
		$code = curl_getinfo($remote, CURLINFO_HTTP_CODE);

		if ($code AND $code < 200 OR $code > 299)
		{
			$error = $response;
		}
		elseif ($response === FALSE)
		{
			$error = curl_error($remote);
		}

		// Close the connection
		curl_close($remote);
		if (isset($error))
		{
			//error
		}
		return $response;
	}

	static public function getValidExtensionType()
	{
		$extensionType	= array(
									'3g2',
									'3gp',
									'asf',
									'asx',
									'avi',
									'flv',
									'mov',
									'mp4',
									'mpg',
									'rm',
									'swf',
									'vob',
									'wmv',
									'm4v'
		);
		return $extensionType;
	}

	static public function getMIMEType($videoFile)
	{
		if($videoFile['type'] ==='application/octet-stream' )
		{
			$fileInfo = pathinfo($videoFile['name']);

			return CVideosHelper::mimeType($fileInfo['extension']);
		}

		return $videoFile['type'];

	}

	static public function mimeType($extension)
	{
		$mimeType	= array(
								'flv'=>'video/x-flv',
								'wmv'=>'video/x-ms-wmv'
					);

		return $mimeType[$extension];
	}
        
	static public function getIURL($url){
		$parts = explode('://', $url);
		/* replace scheme with current */
		if ( isset ($parts[0] ) ){
			$parts[0] = JUri::getInstance()->getScheme();
		}
		return @$parts[0] . '://' . @$parts[1];
	}
}

class CRemoteHelper
{
	// Return true if Curl library is installed
	static public function curlExists()
	{
		return function_exists('curl_init');
	}

	// Return content of the given url
	static public function getContent($url , $raw = false , $headerOnly = false)
	{
		if (!$url)
			return false;

		if (function_exists('curl_init'))
		{
			$ch			= curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_HEADER, true );

			if($raw){
				curl_setopt($ch, CURLOPT_BINARYTRANSFER, true );
			}

			$response	= curl_exec($ch);
			
			$curl_errno	= curl_errno($ch);
			$curl_error	= curl_error($ch);

			if ($curl_errno!=0)
			{
				$mainframe	= JFactory::getApplication();
				$err		= 'CURL error : '.$curl_errno.' '.$curl_error;
				$mainframe->enqueueMessage($err, 'error');
			}

			$code		= curl_getinfo( $ch , CURLINFO_HTTP_CODE );

			// For redirects, we need to handle this properly instead of using CURLOPT_FOLLOWLOCATION
			// as it doesn't work with safe_mode or openbase_dir set.
			if( $code == 301 || $code == 302 )
			{
				list( $headers , $body ) = explode( "\r\n\r\n" , $response , 2 );
				
				preg_match( "/(Location:|location:|URI:)(.*?)\n/" , $headers , $matches );
				
				if( !empty( $matches ) && isset( $matches[2] ) )
				{
					$url = trim($matches[2]);
					if(strpos($matches[2], "flickr") === FALSE){
						$url = "http://www.flickr.com".trim( $matches[2] );
					}
					curl_setopt( $ch , CURLOPT_URL , $url );
					curl_setopt( $ch , CURLOPT_RETURNTRANSFER, 1);
					curl_setopt( $ch , CURLOPT_HEADER, true );
					$response	= curl_exec( $ch );
				}
			}


			if(!$raw){
				if(isset($response))
					list( $headers , $body )	= explode( "\r\n\r\n" , $response , 2 );
			}

			$ret	= $raw ? $response : $body;
			$ret	= $headerOnly ? $headers : $ret;

			curl_close($ch);
			return $ret;
		}

		// CURL unavailable on this install
		return false;
	}

	// Return result of a POST
	static public function post($url, $data,$header = true)
	{
		if (!$url && !$data)
			return false;

		$response = '';
		if (function_exists('curl_init'))
		{
			$ch = curl_init();
			if($header)
				curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/x-www-form-urlencoded;charset=UTF-8'));

			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
			$response = curl_exec($ch);
			$response_code = curl_getinfo ($ch, CURLINFO_HTTP_CODE);
			curl_close($ch);
		}
		else
		{
			$dataLength	= strlen(implode('&', $data));
			$parsedUrl	= parse_url( $url );
			$fp			= fsockopen( $parsedUrl['host'], 80, $errno, $errstr, 30);

			if (!$fp)
			{
				return false; // Error
			}
			else
			{
				$out	 = 'POST ' . (isset($parsedUrl['path']) ? $parsedUrl['path'] : '/')
						. (isset($parsedUrl['query']) ? '?' . $parsedUrl['query'] : '')
						. ' HTTP/1.0' . "\r\n";
				$out	.= 'Host: ' . $parsedUrl['host'] . "\r\n";
				$out	.= "Content-Type: application/x-www-form-urlencoded\r\n";
				$out	.= 'Content-Length: ' . $dataLength . "\r\n";
				$out	.= 'Accept-Charset: UTF-8' . "\r\n";
				$out	.= 'Connection: Close' . "\r\n\r\n";
				$out	.= $data;
				fwrite($fp, $out);
				while(!feof($fp)) {
					$response .= fgets($fp, 128);
				}
				fclose($fp);
				if( $contents ) {
					list($headers, $content) = explode( "\r\n\r\n", $contents, 2 );
					$response_code = strpos( $headers, '200 OK' );
				}
			}
		}
		return $response;
	}
}


abstract class PVideoProvider extends JObject {
	abstract function getThumbnail();

    abstract function getTitle();

    abstract function getDuration();

    abstract function getType();

    abstract function getViewHTML($videoId, $videoWidth, $videoHeight);

    public function __construct($db = null) {
        parent::__construct();
    }

    /**
     * Initialize the provider with video url resource
     */
    public function init($url) {
        $this->url = $url;
        $this->videoId = $this->getId();
    }

    /**
     * Return embedded code
     *
     * @param type $videoId
     * @param type $videoWidth
     * @param type $videoHeight
     * @return type
     *
     */
    public function getEmbedCode($videoId, $videoWidth, $videoHeight) {
        return $this->getViewHTML($videoId, $videoWidth, $videoHeight);
    }

    /**
     * Return true if the video is valid.
     * This function uses a typical video privider method where they normally provide
     * a XML feed file to extract all the video info
     * @return type Boolean
     */
    public function isValid() {
		// Connect and get the remote video
        // Simple check, make sure video id exist
		
        if (empty($this->videoId)) {
            $this->setError(JText::_('COM_COMMUNITY_VIDEOS_INVALID_VIDEO_ID_ERROR'));
            return false;
        }
        // Youtube might return 'Video not found' in the content file
		$tempxmlContent = "";
		if(class_exists("guruControllerguruMedia")){
        	//$tempxmlContent = guruControllerguruMedia::getDetailsFromVideo($this->url);
			$tempxmlContent = guruControllerguruMedia::getDetailsFromVideo($this->getFeedUrl());
		}
		
        if ($tempxmlContent == false) {
            $this->setError(JText::_('COM_COMMUNITY_VIDEOS_FETCHING_VIDEO_ERROR'));
            return false;
        }

        return true;
    }

}
?>