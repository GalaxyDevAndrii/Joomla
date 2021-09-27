<?php
/*------------------------------------------------------------------------
# com_guru
# ------------------------------------------------------------------------
# author    iJoomla
# copyright Copyright (C) 2013 ijoomla.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.ijoomla.com
# Technical Support:  Forum - http://www.ijoomla.com.com/forum/index/
-------------------------------------------------------------------------*/

defined( '_JEXEC' ) or die( 'Restricted access' );

jimport ('joomla.application.component.controller');

class guruControllerguruMedia extends guruController {
	var $_model = null;
	
	function __construct () {
		parent::__construct();
		$this->registerTask ("add", "edit");
		$this->registerTask ("", "listMedia");
		$this->_model = $this->getModel("guruMedia");
		$this->registerTask ("unpublish", "publish");
		$this->registerTask('ajax_add_video', 'ajaxAddVideo');
		$this->registerTask('ajax_add_mass_video', 'ajaxAddMassVideo');
	}

	function listMedia() {
		$view = $this->getView("guruMedia", "html");
		$view->setModel($this->_model, true);
		$view->display();
	}
	
	function upload() { 
		JFactory::getApplication()->input->set ("hidemainmenu", 1);
		$view = $this->getView("guruMedia", "html");
		$view->setLayout("editForm");
		$view->setModel($this->_model, true);
		$model = $this->getModel("adagencyConfig");
		$view->setModel($model);
		$view->uploadflash();
		$view->editForm();
	
	}

	function edit () {
		JFactory::getApplication()->input->set ("hidemainmenu", 1);
		$view = $this->getView("guruMedia", "html");
		$view->setLayout("editForm");
		$view->setModel($this->_model, true);
	
		//$model = $this->getModel("adagencyConfig");
		//$view->setModel($model);
		$view->editForm();

	}
	
	function changes() {
		JFactory::getApplication()->input->set ("hidemainmenu", 1);
		$view = $this->getView("guruMedia", "html");
		$view->setLayout("editForm");
		$view->setModel($this->_model, true);
		$view->editForm();
	}

	function save () {
		if ($this->_model->store() ) {

			$msg = JText::_('AD_ADSAVED');
		} else {
			$msg = JText::_('AD_ADSAVEFAIL');
		}
		$link = "index.php?option=com_guru&view=guruMedia";
		$this->setRedirect($link, $msg);

	}


	function cancel () {
	 	$msg = JText::_('AD_SAVECANCEL');
		$link = "index.php?option=com_guru&view=guruMedia";
		$this->setRedirect($link, $msg);


	}
	
	function publish () {
		$res = $this->_model->publish();
		if (!$res) {
			$msg = JText::_('PACKAGEBLOCKERR');
		} elseif ($res == -1) {
		 	$msg = JText::_('PACKAGEUNPUB');
		} elseif ($res == 1) {
			$msg = JText::_('PACKAGEPUB');
		} else {
                 	$msg = JText::_('PACKAGEUNSPEC');
		}
		
		$link = "index.php?option=com_guru&view=guruMedia";
		$this->setRedirect($link, $msg);


	}
	
	function unpublish () {
		$res = $this->_model->unpublish();
		if (!$res) {
			$msg = JText::_('PACKAGEBLOCKERR');
		} elseif ($res == -1) {
		 	$msg = JText::_('PACKAGEUNPUB');
		} elseif ($res == 1) {
			$msg = JText::_('PACKAGEPUB');
		} else {
                 	$msg = JText::_('PACKAGEUNSPEC');
		}
		
		$link = "index.php?option=com_guru&view=guruMedia";
		$this->setRedirect($link, $msg);
	}
	
	static public function getDetailsFromVideo($url , $raw = false , $headerOnly = false){
		if (!$url){
			return false;
		}

		if(function_exists('curl_init')){
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
			
			if ($curl_errno!=0){
				/*$mainframe	= JFactory::getApplication();
				$err		= 'CURL error : '.$curl_errno.' '.$curl_error;
				$mainframe->enqueueMessage($err, 'error');*/
			}

			$code		= curl_getinfo( $ch , CURLINFO_HTTP_CODE );

			// For redirects, we need to handle this properly instead of using CURLOPT_FOLLOWLOCATION
			// as it doesn't work with safe_mode or openbase_dir set.
			if( $code == 301 || $code == 302 ){
				list( $headers , $body ) = explode( "\r\n\r\n" , $response , 2 );

				preg_match( "/(Location:|URI:|location)(.*?)\n/" , $headers , $matches );

				if( !empty( $matches ) && isset( $matches[2] ) ){
					$url	= trim( $matches[2] );
					curl_setopt( $ch , CURLOPT_URL , $url );
					curl_setopt( $ch , CURLOPT_RETURNTRANSFER, 1);
					curl_setopt( $ch , CURLOPT_HEADER, true );
					$response	= curl_exec( $ch );
				}
			}

			if(!$raw){
				if(isset($response)){
					@list($headers, $body) = @explode("\r\n\r\n", $response, 2);
				}
			}

			$ret	= $raw ? $response : $body;
			$ret	= $headerOnly ? $headers : $ret;

			curl_close($ch);
			return $ret;
		}
		// CURL unavailable on this install
		return false;
	}
	
	function str_ireplace($search, $replace, $str, $count = NULL) {
        if ($count === FALSE) {
            return $this->_utf8_ireplace($search, $replace, $str);
        } else {
            return $this->_utf8_ireplace($search, $replace, $str, $count);
        }
    }
	
	public function _utf8_ireplace($search, $replace, $str, $count = NULL) {

        if (!is_array($search)) {

            $slen = strlen($search);
            $lendif = strlen($replace) - $slen;
            if ($slen == 0) {
                return $str;
            }

            $search = mb_strtolower($search);

            $search = preg_quote($search, '/');
            $lstr = mb_strtolower($str);
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
	
	public function getProvider($videoLink)
	{
		$providerName	= 'invalid';

		if (! empty($videoLink))
		{
			$origvideolink = $videoLink;

			//if it using https
            $videoLink	= $this->str_ireplace( 'https://' , 'http://' , $videoLink );
			$videoLink	= $this->str_ireplace( 'http://' , '' , $videoLink );
        		//$this->str_ireplace issue fix for J1.6
			if($videoLink === $origvideolink) $videoLink = str_ireplace( 'http://' , '' , $videoLink );

			$videoLink = 'http://'. $videoLink;
			
			$parsedLink = parse_url( $videoLink );

			preg_match('/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i', $parsedLink['host'], $matches);

			if ( !empty($matches['domain'])){
				$domain		= $matches['domain'];
				$provider		= explode('.', $domain);
				$providerName	= strtolower($provider[0]);

				// For youtube, they might be using youtu.be address
				if($domain == 'youtu.be' || $domain == 'youtu'){
					$providerName = 'youtube';
				}

				if($parsedLink['host'] === 'new.myspace.com'){
					$providerName = 'invalid';
				}
			}

		}

		$libraryPath	= JPATH_ROOT .'/components/com_guru/helpers/videos' .'/'. $providerName . '.php';
		
		if (!JFile::exists($libraryPath)){
			$providerName	= 'invalid';
			$libraryPath	= JPATH_ROOT .'/components/com_guru/helpers/videos/invalid.php';
		}
		
		require_once(JPATH_ROOT .'/components/com_guru/helpers/videos/helper.php');
		require_once($libraryPath);
		$className	= 'PTableVideo' . ucfirst($providerName);
		$table		= new $className();

		return $table;
	}
	
	function transformDuration($seconds){
		$timer = "00:00";
		if($seconds >= 60){
			$minutes = (int)($seconds / 60);
			$seconds = $seconds % 60;
			
			if(strlen($minutes) == 1){
				$minutes = "0".$minutes;
			}
			if(strlen($seconds) == 1){
				$seconds = "0".$seconds;
			}
			
			$timer = $minutes.":".$seconds;
		}
		else{
			$timer = "00:".$seconds;
		}
		
		if($timer == "00" || $timer == "00:" || $timer == "00:0" || $timer == "00:00"){
			$timer = "N/A";
		}
		
		return $timer;
	}
	
	function ajaxAddVideo(){
		$url = JFactory::getApplication()->input->get("url", "", "raw");
		
		if(strpos(" ".$url, "http") === FALSE){
			$url = "http://".$url;
		}
		
		if(strpos($url, "youtube") !== FALSE){
			$url = str_replace("watch", "", $url);
		}

		$provider = $this->getProvider($url);
		
		$provider->url = $url;
		$provider->videoId = $provider->getId();
		
		$video_details = $this->getDetailsFromVideo($provider->getFeedUrl());

		$provider->xmlContent = $video_details;
		$isValid = $provider->isValid();

		if($isValid){
			$this->title	= $provider->getTitle();
			$this->type		= $provider->getType();
			$this->video_id	= $provider->getId();
			$this->duration	= $provider->getDuration();
			$this->status	= 'ready';
			$this->thumb	= $provider->getThumbnail();
			$this->path 	= $url;
			$this->description=	$provider->getDescription();
			$this->status	= 'ready';
		}

		$title = $provider->getTitle();
		$image = $provider->getThumbnail();
		$duration = $this->transformDuration($provider->getDuration());
		$description = $provider->getDescription();
		
		$return =  '';
		$return .= '<div class="hasTip hasTooltip" data-toggle="tooltip" title="" data-placement="top" data-original-title="'.trim($description).'">
						<div class="row-fluid">
							<div class="cVideo-Thumb span3 g_margin_top">
								<img alt="'.trim($title).'" src="'.trim($image).'">
								<input type="hidden" name="video-name" id="video-name" value="'.trim($title).'" />
								<input type="hidden" name="image_url" id="image_url" value="'.trim($image).'" />
								<input type="hidden" name="duration" id="duration" value="'.trim($duration).'" />
								<b>'.trim($duration).'</b>
							</div>
							<div class="cVideo-Content span8">
								<b>'.trim($title).'</b>
								<div><a href="#" onclick="javascript:changeVideo(); return false;" class="uk-button uk-button-small wk-button wk-button-small creator-change-video">Change Video</a></div>
								<input type="hidden" id="video-description" name="video-description" value="'.str_replace('"', '\"', trim($description)).'" />
							</div>
						</div>
					</div>
					';
		echo $return;
		die();
	}
	
	function ajaxAddMassVideo(){
		$host = JFactory::getApplication()->input->get("host", "0");
		$playlist_id = JFactory::getApplication()->input->get("list", "");
		$api = JFactory::getApplication()->input->get("api", "");
		$page = JFactory::getApplication()->input->get("page", "1");
		$start_with = JFactory::getApplication()->input->get("start_with", "1");
		$per_page = JFactory::getApplication()->input->get("per_page", "25");
		$table = "";
		
		if($host == "1"){ // YouTube
			$url = "https://www.googleapis.com/youtube/v3/playlistItems?part=snippet&maxResults=".intval($per_page)."&playlistId=".$playlist_id."&key=".$api;
			$data = json_decode(file_get_contents($url), true); // data for page 1
			
			if($start_with > 1){
				// start pagination
				for($i=2; $i<=$start_with; $i++){
					if(isset($data["nextPageToken"])){
						$url = "https://www.googleapis.com/youtube/v3/playlistItems?part=snippet&maxResults=".intval($per_page)."&pageToken=".trim($data["nextPageToken"])."&playlistId=".$playlist_id."&key=".$api;
						$data = json_decode(file_get_contents($url), true); // data for page $i
					}
				}
			}
			
			$video = $data["items"];
			$nVideo = count($video);
			
			$table = '<table class="table table-striped table-bordered adminlist">
						<tr>
							<th width="5%" class="center">#</th>
							<th width="5%" class="center">
								<input type="checkbox" onclick="Joomla.checkAll(this)" checked="checked" name="toggle" value="" />
								<span class="lbl"></span>
							</th>
							<th width="30%">'.JText::_("GURU_THUMBNAIL").'</th>
							<th width="30%">'.JText::_("GURU_TITLE").'</th>
							<th width="30%">'.JText::_("GURU_DESCRIPTION").'</th>
						</tr>';
			if($nVideo > 0){
				for($i=0; $i<$nVideo; $i++){
					$breaks = array("<br />","<br>","<br/>");  
    				$video[$i]['snippet']['description'] = str_ireplace($breaks, "\r\n", $video[$i]['snippet']['description']); 
					
					$table .= '	<tr>
									<td class="center">'.($i+1).'</td>
									<td class="center">
										<input type="checkbox" checked="checked" onclick="Joomla.isChecked(this.checked);" value="'.$i.'" name="cid[]" id="cb'.$i.'">
										<span class="lbl"></span>
									</td>
									<td>
										<img src="'.$video[$i]['snippet']['thumbnails']["default"]['url'].'" />
										<input type="hidden" name="image[]" value="'.$video[$i]['snippet']['thumbnails']["default"]['url'].'" />
									</td>
									<td>
										<textarea name="title[]">'.$video[$i]['snippet']['title'].'</textarea>
										<input type="hidden" name="url[]" value="https://www.youtube.com/watch?v='.$video[$i]['snippet']["resourceId"]['videoId'].'" />
									</td>
									<td>
										<textarea name="description[]">'.$video[$i]['snippet']['description'].'</textarea>
									</td>
								</tr>';
				}
			}
			
			$table .= '</table>';
		}
		elseif($host == 2){ // Vimeo
			$url = "http://vimeo.com/api/v2/album/".$playlist_id."/videos.json?page=".intval($page);
			
			$vimeo_content = file_get_contents($url);
			
			if(!isset($vimeo_content) || $vimeo_content === false){
				if(function_exists('curl_init')){
					$ch			= curl_init();
					curl_setopt($ch, CURLOPT_URL, $url);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
					curl_setopt($ch, CURLOPT_HEADER, false );
					$vimeo_content	= curl_exec($ch);
					curl_close($ch);
				}
			}
			
			$data = json_decode($vimeo_content, true);
			$nVideo = count($data);
			
			$table = '<table class="table table-striped table-bordered adminlist">
						<tr>
							<th width="5%" class="center">#</th>
							<th width="5%" class="center">
								<input type="checkbox" checked="checked" onclick="Joomla.checkAll(this)" name="toggle" value="" />
								<span class="lbl"></span>
							</th>
							<th width="30%">'.JText::_("GURU_THUMBNAIL").'</th>
							<th width="30%">'.JText::_("GURU_TITLE").'</th>
							<th width="30%">'.JText::_("GURU_DESCRIPTION").'</th>
						</tr>';
			if($nVideo > 0){
				for($i=0; $i<$nVideo; $i++){
				 	$breaks = array("<br />","<br>","<br/>");  
    				$data[$i]['description'] = str_ireplace($breaks, "\r\n", $data[$i]['description']); 
					$table .= '	<tr>
									<td class="center">'.($i+1).'</td>
									<td class="center">
										<input type="checkbox" checked="checked" onclick="Joomla.isChecked(this.checked);" value="'.$i.'" name="cid[]" id="cb'.$i.'">
										<span class="lbl"></span>
									</td>
									<td>
										<img src="'.$data[$i]['thumbnail_small'].'" />
										<input type="hidden" name="image[]" value="'.$data[$i]['thumbnail_small'].'" />
									</td>
									<td>
										<textarea name="title[]">'.$data[$i]['title'].'</textarea>
										<input type="hidden" name="url[]" value="'.$data[$i]["url"].'" />
									</td>
									<td>
										<textarea name="description[]">'.$data[$i]['description'].'</textarea>
									</td>
								</tr>';
				}
			}
			else{
				echo '<div class="alert alert-error">
						<h4 class="alert-heading"></h4>
						<p>'.JText::_("GURU_NO_FOUND_FETCH_VIDEO").'</p>
						<ul>
							<li>
								'.JText::_("GURU_NO_FOUND_FETCH_VIDEO1").'
							</li>
							<li>
								'.JText::_("GURU_NO_FOUND_FETCH_VIDEO2").'
							</li>
						</ul>
						
					  </div>';
			}
			
			$table .= '</table>';
		}
		
		echo $table;
		die();
	}

};

?>