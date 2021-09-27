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

defined('_JEXEC') or die('Restricted access');

class guruAdminHelper
{
	public static function getUploadMaxSize()
	{
		$max_upload 	= self::toMb(ini_get('upload_max_filesize'));
		$max_post 		= self::toMb(ini_get('post_max_size'));
		$memory_limit 	= self::toMb(ini_get('memory_limit'));
		$upload_mb 		= min($max_upload, $max_post, $memory_limit);

		return $upload_mb;
	}

	public static function toMb($str)
	{
		$size = (int) $str;
		if (preg_match('/\d+K/', $str)) {
			return round($size / 1024, 2);
		}

		if (preg_match('/\d+M/', $str)) {
			return $size;
		}

		if (preg_match('/\d+G/', $str)) {
			return $size * 1024;
		}
	}

	function create_thumbnails($images, $width, $height, $width_old, $height_old, $type)
	{
		$mosConfig_absolute_path = JPATH_ROOT;
		$mosConfig_live_site = JURI::base();
		$width_2 = $width;
		$height_2 = $height;
		if (!is_dir(JPATH_ROOT . DIRECTORY_SEPARATOR . "images" . DIRECTORY_SEPARATOR . "stories" . DIRECTORY_SEPARATOR . "guru_thumb")) {
			mkdir(JPATH_ROOT . DIRECTORY_SEPARATOR . "images" . DIRECTORY_SEPARATOR . "stories" . DIRECTORY_SEPARATOR . "guru_thumb", 0777);
		}
		if ($images == "") {
			return;
		}

		$images = trim($images);
		$get_path = explode('/', $images);
		$nr = (count($get_path) - 1);
		$photo_name = $get_path[$nr];

		unset($get_path[$nr]);
		$path = implode("/", $get_path);

		if (file_exists(JPATH_ROOT . DIRECTORY_SEPARATOR . "images" . DIRECTORY_SEPARATOR . "stories" . DIRECTORY_SEPARATOR . "guru_thumb" . DIRECTORY_SEPARATOR . $type . "thumb_" . $photo_name)) {
			$img_size_thumb = @getimagesize(JPATH_ROOT . DIRECTORY_SEPARATOR . "images" . DIRECTORY_SEPARATOR . "stories" . DIRECTORY_SEPARATOR . "guru_thumb" . DIRECTORY_SEPARATOR . $type . "thumb_" . $photo_name);
			$width_thumb = $img_size_thumb[0];
			$height_thumb = $img_size_thumb[1];
			if ($width_thumb == intval($width) && $height_thumb == intval($height)) {
				return "guru_thumb/" . $type . "thumb_" . $photo_name;
			}
		}

		$name_array = explode('.', $photo_name);
		$extension = $name_array[count($name_array) - 1];

		if (strtolower($extension) == 'jpg' || strtolower($extension) == 'jpeg') {
			$gdimg = @imagecreatefromjpeg($images);
			$ext = "jpg";
		}

		if (strtolower($extension) == 'gif') {
			$gdimg = @imagecreatefromgif($images);
			$ext = "gif";
		}
		if (strtolower($extension) == 'png') {
			$gdimg = @imagecreatefrompng($images);
			$ext = "png";
		}

		if ($ext != 'gif') {
			$image_p = imagecreatetruecolor($width_2, $height_2);
			$trans = imagecolorallocate($image_p, 0, 0, 0);
			imagecolortransparent($image_p, $trans);
			imagecopyresampled($image_p, $gdimg, 0, 0, 0, 0, $width_2, $height_2, $width_old, $height_old);
		} else {
			$image_p = imagecreate($width_2, $height_2);
			$trans = imagecolorallocate($image_p, 0, 0, 0);
			imagecolortransparent($image_p, $trans);
			imagecopyresized($image_p, $gdimg, 0, 0, 0, 0, $width_2, $height_2, $width_old, $height_old);
		}

		$name = $type . "thumb_" . $photo_name;

		if ($ext == "jpg") $upload_th = imagejpeg($image_p, JPATH_ROOT . DIRECTORY_SEPARATOR . "images" . DIRECTORY_SEPARATOR . "stories" . DIRECTORY_SEPARATOR . "guru_thumb" . DIRECTORY_SEPARATOR . $name, 100);
		if ($ext == "gif") $upload_th = imagegif($image_p, JPATH_ROOT . DIRECTORY_SEPARATOR . "images" . DIRECTORY_SEPARATOR . "stories" . DIRECTORY_SEPARATOR . "guru_thumb" . DIRECTORY_SEPARATOR . $name, 100);
		if ($ext == "png") $upload_th = imagepng($image_p, JPATH_ROOT . DIRECTORY_SEPARATOR . "images" . DIRECTORY_SEPARATOR . "stories" . DIRECTORY_SEPARATOR . "guru_thumb" . DIRECTORY_SEPARATOR . $name, 9);
		@chmod(JPATH_ROOT . DIRECTORY_SEPARATOR . "images" . DIRECTORY_SEPARATOR . "stories" . DIRECTORY_SEPARATOR . "guru_thumb" . DIRECTORY_SEPARATOR . $name, 0755);
		if ($upload_th) {
			return 'guru_thumb/' . $name;
		} else {
			return $images;
		}
	}

	function create_media_using_plugin($main_media, $configs, $aheight, $awidth, $vheight, $vwidth, $layout_id = "")
	{
		if ($main_media->type == 'video') {
			if ($main_media->source == 'code')
				$media = $main_media->code;
			if ($main_media->source == 'url') {
				//$position_watch = strpos($main_media->url, 'www.youtube.com/watch');
				if (strpos($main_media->url, 'www.youtube.com/watch') !== false) { // youtube link - begin
					$link_array = explode('=', $main_media->url);
					$link_ = $link_array[1];
					$media = '{youtube}' . $link_ . '{/youtube}';
				} // youtube link - end
				elseif (strpos($main_media->url, 'www.123video.nl') !== false) { // 123video.nl link - begin
					$link_array = explode('=', $main_media->url);
					$link_ = $link_array[1];
					$media = '{123video}' . $link_ . '{/123video}';
				} // 123video.nl link - end
				elseif (strpos($main_media->url, 'www.aniboom.com') !== false) { // aniboom.com link - begin
					$begin_tag = strpos($main_media->url, 'video');
					$remaining_link = substr($main_media->url, $begin_tag + 6, strlen($main_media->url));
					$end_tag = strpos($remaining_link, '/');
					if ($end_tag === false) $end_tag = strlen($remaining_link);
					$link_ = substr($remaining_link, 0, $end_tag);
					$media = '{aniboom}' . $link_ . '{/aniboom}';
				} // aniboom.com link - end
				elseif (strpos($main_media->url, 'www.badjojo.com') !== false) { // badjojo.com [adult] link - begin
					$link_array = explode('=', $main_media->url);
					$link_ = $link_array[1];
					$media = '{badjojo}' . $link_ . '{/badjojo}';
					echo $media;
				} // badjojo.com [adult] link - end
				elseif (strpos($main_media->url, 'www.brightcove.tv') !== false) { // brightcove.tv link - begin
					$begin_tag = strpos($main_media->url, 'title=');
					$remaining_link = substr($main_media->url, $begin_tag + 6, strlen($main_media->url));
					$end_tag = strpos($remaining_link, '&');
					if ($end_tag === false) $end_tag = strlen($remaining_link);
					$link_ = substr($remaining_link, 0, $end_tag);
					$media = '{brightcove}' . $link_ . '{/brightcove}';
				} // brightcove.tv link - end
				elseif (strpos($main_media->url, 'www.collegehumor.com') !== false) { // collegehumor.com link - begin
					$link_array = explode(':', $main_media->url);
					$link_ = $link_array[2];
					$media = '{collegehumor}' . $link_ . '{/collegehumor}';
				} // collegehumor.com link - end
				elseif (strpos($main_media->url, 'current.com') !== false) { // current.com link - begin
					$begin_tag = strpos($main_media->url, 'items/');
					$remaining_link = substr($main_media->url, $begin_tag + 6, strlen($main_media->url));
					$end_tag = strpos($remaining_link, '_');
					if ($end_tag === false) $end_tag = strlen($remaining_link);
					$link_ = substr($remaining_link, 0, $end_tag);
					$media = '{current}' . $link_ . '{/current}';
				} // current.com link - end
				elseif (strpos($main_media->url, 'dailymotion.com') !== false) { // dailymotion.com link - begin
					$begin_tag = strpos($main_media->url, 'video/');
					$remaining_link = substr($main_media->url, $begin_tag + 6, strlen($main_media->url));
					$end_tag = strpos($remaining_link, '_');
					if ($end_tag === false) $end_tag = strlen($remaining_link);
					$link_ = substr($remaining_link, 0, $end_tag);
					$media = '{dailymotion}' . $link_ . '{/dailymotion}';
				} // dailymotion.com link - end
				elseif (strpos($main_media->url, 'espn') !== false) { // video.espn.com link - begin
					$begin_tag = strpos($main_media->url, 'videoId=');
					$remaining_link = substr($main_media->url, $begin_tag + 8, strlen($main_media->url));
					$end_tag = strpos($remaining_link, '&');
					if ($end_tag === false) $end_tag = strlen($remaining_link);
					$link_ = substr($remaining_link, 0, $end_tag);
					$media = '{espn}' . $link_ . '{/espn}';
				} // video.espn.com link - end
				elseif (strpos($main_media->url, 'eyespot.com') !== false) { // eyespot.com link - begin
					$link_array = explode('r=', $main_media->url);
					$link_ = $link_array[1];
					$media = '{eyespot}' . $link_ . '{/eyespot}';
				} // eyespot.com link - end
				elseif (strpos($main_media->url, 'flurl.com') !== false) { // flurl.com link - begin
					$begin_tag = strpos($main_media->url, 'video/');
					$remaining_link = substr($main_media->url, $begin_tag + 6, strlen($main_media->url));
					$end_tag = strpos($remaining_link, '_');
					if ($end_tag === false) $end_tag = strlen($remaining_link);
					$link_ = substr($remaining_link, 0, $end_tag);
					$media = '{flurl}' . $link_ . '{/flurl}';
				} // flurl.com link - end
				elseif (strpos($main_media->url, 'funnyordie.com') !== false) { // funnyordie.com link - begin
					$link_array = explode('videos/', $main_media->url);
					$link_ = $link_array[1];
					$media = '{funnyordie}' . $link_ . '{/funnyordie}';
				} // funnyordie.com link - end
				elseif (strpos($main_media->url, 'gametrailers.com') !== false) { // gametrailers.com link - begin
					$begin_tag = strpos($main_media->url, 'player/');
					$remaining_link = substr($main_media->url, $begin_tag + 7, strlen($main_media->url));
					$end_tag = strpos($remaining_link, '.');
					if ($end_tag === false) $end_tag = strlen($remaining_link);
					$link_ = substr($remaining_link, 0, $end_tag);
					$media = '{gametrailers}' . $link_ . '{/gametrailers}';
				} // gametrailers.com link - end
				elseif (strpos($main_media->url, 'godtube.com') !== false) { // godtube.com link - begin
					$link_array = explode('viewkey=', $main_media->url);
					$link_ = $link_array[1];
					$media = '{godtube}' . $link_ . '{/godtube}';
				} // godtube.com link - end
				elseif (strpos($main_media->url, 'gofish.com') !== false) { // gofish.com link - begin
					$link_array = explode('gfid=', $main_media->url);
					$link_ = $link_array[1];
					$media = '{gofish}' . $link_ . '{/gofish}';
				} // gofish.com link - end
				elseif (strpos($main_media->url, 'google.com') !== false) { // Google Video link - begin
					$link_array = explode('docid=', $main_media->url);
					$link_ = $link_array[1];
					$media = '{google}' . $link_ . '{/google}';
				} // Google Video link - end
				elseif (strpos($main_media->url, 'guba.com') !== false) { // guba.com link - begin
					$link_array = explode('watch/', $main_media->url);
					$link_ = $link_array[1];
					$media = '{guba}' . $link_ . '{/guba}';
				} // guba.com link - end
				elseif (strpos($main_media->url, 'hook.tv') !== false) { // hook.tv link - begin
					$link_array = explode('key=', $main_media->url);
					$link_ = $link_array[1];
					$media = '{hook}' . $link_ . '{/hook}';
				} // hook.tv link - end
				elseif (strpos($main_media->url, 'jumpcut.com') !== false) { // jumpcut.com link - begin
					$link_array = explode('id=', $main_media->url);
					$link_ = $link_array[1];
					$media = '{jumpcut}' . $link_ . '{/jumpcut}';
				} // jumpcut.com link - end
				elseif (strpos($main_media->url, 'kewego.com') !== false) { // kewego.com link - begin
					$begin_tag = strpos($main_media->url, 'video/');
					$remaining_link = substr($main_media->url, $begin_tag + 6, strlen($main_media->url));
					$end_tag = strpos($remaining_link, '.');
					if ($end_tag === false) $end_tag = strlen($remaining_link);
					$link_ = substr($remaining_link, 0, $end_tag);
					$media = '{kewego}' . $link_ . '{/kewego}';
				} // kewego.com link - end
				elseif (strpos($main_media->url, 'krazyshow.com') !== false) { // krazyshow.com [adult] link - begin
					$link_array = explode('cid=', $main_media->url);
					$link_ = $link_array[1];
					$media = '{krazyshow}' . $link_ . '{/krazyshow}';
				} // krazyshow.com [adult] link - end
				elseif (strpos($main_media->url, 'ku6.com') !== false) { // ku6.com link - begin
					$begin_tag = strpos($main_media->url, 'show/');
					$remaining_link = substr($main_media->url, $begin_tag + 5, strlen($main_media->url));
					$end_tag = strpos($remaining_link, '.');
					if ($end_tag === false) $end_tag = strlen($remaining_link);
					$link_ = substr($remaining_link, 0, $end_tag);
					$media = '{ku6}' . $link_ . '{/ku6}';
				} // ku6.com link - end
				elseif (strpos($main_media->url, 'liveleak.com') !== false) { // liveleak.com link - begin
					$link_array = explode('i=', $main_media->url);
					$link_ = $link_array[1];
					$media = '{liveleak}' . $link_ . '{/liveleak}';
				} // liveleak.com link - end
				elseif (strpos($main_media->url, 'metacafe.com') !== false) { // metacafe.com link - begin
					$begin_tag = strpos($main_media->url, 'watch/');
					$remaining_link = substr($main_media->url, $begin_tag + 6, strlen($main_media->url));
					$end_tag = strlen($remaining_link);
					$link_ = substr($remaining_link, 0, $end_tag);
					$media = '{metacafe}' . $link_ . '{/metacafe}';
				} // metacafe.com link - end
				elseif (strpos($main_media->url, 'mofile.com') !== false) { // mofile.com link - begin
					$begin_tag = strpos($main_media->url, 'com/');
					$remaining_link = substr($main_media->url, $begin_tag + 4, strlen($main_media->url));
					$end_tag = strpos($remaining_link, '/');
					if ($end_tag === false) $end_tag = strlen($remaining_link);
					$link_ = substr($remaining_link, 0, $end_tag);
					$media = '{mofile}' . $link_ . '{/mofile}';
				} // mofile.com link - end
				elseif (strpos($main_media->url, 'myspace.com') !== false) { // myspace.com link - begin
					$link_array = explode('VideoID=', $main_media->url);
					$link_ = $link_array[1];
					$media = '{myspace}' . $link_ . '{/myspace}';
				} // myspace.com link - end
				elseif (strpos($main_media->url, 'myvideo.de') !== false) { // myvideo.de link - begin
					$begin_tag = strpos($main_media->url, 'watch/');
					$remaining_link = substr($main_media->url, $begin_tag + 6, strlen($main_media->url));
					$end_tag = strpos($remaining_link, '/');
					if ($end_tag === false) $end_tag = strlen($remaining_link);
					$link_ = substr($remaining_link, 0, $end_tag);
					$media = '{myvideo}' . $link_ . '{/myvideo}';
				} // myvideo.de link - end
				elseif (strpos($main_media->url, 'redtube.com') !== false) { // redtube.com [adult] link - begin
					$link_array = explode('/', $main_media->url);
					$link_ = $link_array[1];
					$media = '{redtube}' . $link_ . '{/redtube}';
				} // redtube.com [adult] - end
				elseif (strpos($main_media->url, 'revver.com') !== false) { // revver.com link - begin
					$begin_tag = strpos($main_media->url, 'video/');
					$remaining_link = substr($main_media->url, $begin_tag + 6, strlen($main_media->url));
					$end_tag = strpos($remaining_link, '/');
					if ($end_tag === false) $end_tag = strlen($remaining_link);
					$link_ = substr($remaining_link, 0, $end_tag);
					$media = '{revver}' . $link_ . '{/revver}';
				} // revver.com link - end
				elseif (strpos($main_media->url, 'sapo.pt') !== false) { // sapo.pt link - begin
					$link_array = explode('pt/', $main_media->url);
					$link_ = $link_array[1];
					$media = '{sapo}' . $link_ . '{/sapo}';
				} // sapo.pt - end
				elseif (strpos($main_media->url, 'sevenload.com') !== false) { // sevenload.com link - begin
					$begin_tag = strpos($main_media->url, 'videos/');
					$remaining_link = substr($main_media->url, $begin_tag + 7, strlen($main_media->url));
					$end_tag = strpos($remaining_link, '-');
					if ($end_tag === false) $end_tag = strlen($remaining_link);
					$link_ = substr($remaining_link, 0, $end_tag);
					$media = '{sevenload}' . $link_ . '{/sevenload}';
				} // sevenload.com link - end
				elseif (strpos($main_media->url, 'sohu.com') !== false) { // sohu.com link - begin
					$link_array = explode('/', $main_media->url);
					$link_ = $link_array[count($link_array) - 1];
					$media = '{sohu}' . $link_ . '{/sohu}';
				} // sohu.com - end
				elseif (strpos($main_media->url, 'southparkstudios.com') !== false) { // southparkstudios.com link - begin
					$begin_tag = strpos($main_media->url, 'clips/');
					$remaining_link = substr($main_media->url, $begin_tag + 6, strlen($main_media->url));
					$end_tag = strpos($remaining_link, '/');
					if ($end_tag === false) $end_tag = strlen($remaining_link);
					$link_ = substr($remaining_link, 0, $end_tag);
					$media = '{southpark}' . $link_ . '{/southpark}';
				} // southparkstudios.com link - end
				elseif (strpos($main_media->url, 'spike.com') !== false) { // spike.com link - begin
					$link_array = explode('video/', $main_media->url);
					$link_ = $link_array[1];
					$media = '{spike}' . $link_ . '{/spike}';
				} // spike.com - end
				elseif (strpos($main_media->url, 'stickam.com') !== false) { // stickam.com link - begin
					$link_array = explode('mId=', $main_media->url);
					$link_ = $link_array[1];
					$media = '{stickam}' . $link_ . '{/stickam}';
				} // stickam.com - end
				elseif (strpos($main_media->url, 'stupidvideos.com') !== false) { // stupidvideos.com link - begin
					$link_array = explode('#', $main_media->url);
					$link_ = $link_array[1];
					$media = '{stupidvideos}' . $link_ . '{/stupidvideos}';
				} // stupidvideos.com - end
				elseif (strpos($main_media->url, 'tudou.com') !== false) { // tudou.com link - begin
					$begin_tag = strpos($main_media->url, 'view/');
					$remaining_link = substr($main_media->url, $begin_tag + 5, strlen($main_media->url));
					$end_tag = strpos($remaining_link, '/');
					if ($end_tag === false) $end_tag = strlen($remaining_link);
					$link_ = substr($remaining_link, 0, $end_tag);
					$media = '{tudou}' . $link_ . '{/tudou}';
				} // tudou.com link - end
				elseif (strpos($main_media->url, 'ustream.tv') !== false) { // ustream.tv link - begin
					$link_array = explode('recorded/', $main_media->url);
					$link_ = $link_array[1];
					$media = '{ustream}' . $link_ . '{/ustream}';
				} // ustream.tv - end
				elseif (strpos($main_media->url, 'veoh.com') !== false) { // veoh.com link - begin
					$link_array = explode('videos/', $main_media->url);
					$link_ = $link_array[1];
					$media = '{veoh}' . $link_ . '{/veoh}';
				} // veoh.com - end
				elseif (strpos($main_media->url, 'videotube.de') !== false) { // videotube.de link - begin
					$link_array = explode('watch/', $main_media->url);
					$link_ = $link_array[1];
					$media = '{videotube}' . $link_ . '{/videotube}';
				} // videotube.de - end
				elseif (strpos($main_media->url, 'vidiac.com') !== false) { // vidiac.com link - begin
					$begin_tag = strpos($main_media->url, 'video/');
					$remaining_link = substr($main_media->url, $begin_tag + 6, strlen($main_media->url));
					$end_tag = strpos($remaining_link, '.');
					if ($end_tag === false) $end_tag = strlen($remaining_link);
					$link_ = substr($remaining_link, 0, $end_tag);
					$media = '{vidiac}' . $link_ . '{/vidiac}';
				} // vidiac.com link - end
				elseif (strpos($main_media->url, 'vimeo.com') !== false) { // vimeo.com link - begin
					$link_array = explode('.com/', $main_media->url);
					$link_ = $link_array[1];
					$media = '{vimeo}' . $link_ . '{/vimeo}';
				} // vimeo.com - end
				elseif (strpos($main_media->url, 'yahoo.com') !== false) { // video.yahoo.com link - begin
					$link_array = explode('watch/', $main_media->url);
					$link_ = $link_array[1];
					$media = '{yahoo}' . $link_ . '{/yahoo}';
				} // video.yahoo.com - end
				elseif (strpos($main_media->url, 'youare.tv') !== false) { // youare.tv link - begin
					$link_array = explode('id=', $main_media->url);
					$link_ = $link_array[1];
					$media = '{youare}' . $link_ . '{/youare}';
				} // youare.tv - end
				elseif (strpos($main_media->url, 'youku.com') !== false) { // youku.com link - begin
					$begin_tag = strpos($main_media->url, 'v_show/');
					$remaining_link = substr($main_media->url, $begin_tag + 7, strlen($main_media->url));
					$end_tag = strpos($remaining_link, '.');
					if ($end_tag === false) $end_tag = strlen($remaining_link);
					$link_ = substr($remaining_link, 0, $end_tag);
					$media = '{youku}' . $link_ . '{/youku}';
				} // youku.com link - end
				elseif (strpos($main_media->url, 'youmaker.com') !== false) { // youmaker.com  link - begin
					$link_array = explode('id=', $main_media->url);
					$link_ = $link_array[1];
					$media = '{youmaker}' . $link_ . '{/youmaker}';
				} // youmaker.com  - end
				else {
					//----------- not special link - begin
					$extension_array = explode('.', $main_media->url);
					$extension = $extension_array[count($extension_array) - 1];

					if (strtolower($extension) == 'flv' || strtolower($extension) == 'swf' || strtolower($extension) == 'mov' || strtolower($extension) == 'wmv' || strtolower($extension) == 'mp4' || strtolower($extension) == 'divx') {
						$tag_begin = '{' . strtolower($extension) . 'remote}';
						$tag_end = '{/' . strtolower($extension) . 'remote}';
					}
					if (!isset($tag_begin)) {
						$tag_begin = NULL;
					}
					if (!isset($tag_end)) {
						$tag_end = NULL;
					}
					$media = $tag_begin . $main_media->url . $tag_end;

					//----------- not special link - begin										
				}
				$media = $this->jwAllVideos($media, $aheight, $awidth, $vheight, $vwidth, $layout_id, $main_media->auto_play);
			}

			if ($main_media->source == 'local') {
				$extension_array = explode('.', $main_media->local);
				$extension = $extension_array[count($extension_array) - 1];

				if (strtolower($extension) == 'flv' || strtolower($extension) == 'swf' || strtolower($extension) == 'mov' || strtolower($extension) == 'wmv' || strtolower($extension) == 'divx') {
					$tag_begin = '{' . strtolower($extension) . 'remote}';
					$tag_end = '{/' . strtolower($extension) . 'remote}';
				}

				if (!isset($tag_begin)) {
					$tag_begin = NULL;
				}

				if (!isset($tag_end)) {
					$tag_end = NULL;
				}

				if (strtolower($extension) == 'flv' || strtolower($extension) == 'swf' || strtolower($extension) == 'mov' || strtolower($extension) == 'wmv' || strtolower($extension) == 'divx') {
					$video_path = str_replace("/administrator", "", JURI::base()) . $configs->videoin . '/' . $main_media->local;

					if (isset($main_media->exception) && intval($main_media->exception) == "1") {
						$video_path = $main_media->local;
					}

					$media = $tag_begin . $video_path . $tag_end;
					$media = $this->jwAllVideos($media, $aheight, $awidth, $vheight, $vwidth, $layout_id, $main_media->auto_play);
				} elseif (strtolower($extension) == 'mp4') {
					$video_path = str_replace("/administrator", "", JURI::base()) . $configs->videoin . '/' . $main_media->local;

					if (isset($main_media->exception) && intval($main_media->exception) == "1") {
						$video_path = $main_media->local;
					}

					$media = $video_path;

					$media = '
							<video width="100%" controls preload="none">
								<source src="' . $media . '" type="video/mp4" />
							</video>
						';
				}
			}
		}
		if ($main_media->type == 'audio') {
			if ($main_media->source == 'code')
				$media = $main_media->code;
			if ($main_media->source == 'url') {
				$extension_array = explode('.', $main_media->url);
				$extension = $extension_array[count($extension_array) - 1];
				if (strtolower($extension) == 'mp3' || strtolower($extension) == 'wma' || strtolower($extension) == 'm4a') {
					$tag_begin = '{' . strtolower($extension) . 'remote}';
					$tag_end = '{/' . strtolower($extension) . 'remote}';
				}
				$media = $tag_begin . $main_media->url . $tag_end;
				$media = $this->jwAllVideos($media, $aheight, $awidth, $vheight, $vwidth, $layout_id, $main_media->auto_play);
				//$media = '<a target="_blank" href="'.$main_media->url.'">'.$main_media->name.'</a>';	
			}
			if ($main_media->source == 'local') {
				$extension_array = explode('.', $main_media->local);
				$extension = $extension_array[count($extension_array) - 1];

				if (strtolower($extension) == 'mp3' || strtolower($extension) == 'wma' || strtolower($extension) == 'm4a') {
					$tag_begin = '{' . strtolower($extension) . 'remote}';
					$tag_end = '{/' . strtolower($extension) . 'remote}';
				}
				//$params = '';
				$media = $tag_begin . str_replace("/administrator", "", JURI::base()) . $configs->audioin . '/' . $main_media->local . $tag_end;
				$media = $this->jwAllVideos($media, $aheight, $awidth, $vheight, $vwidth, $layout_id, $main_media->auto_play);
			}
		}
		if ($main_media->type == 'url') {
			$media = '<a target="_blank" href="' . $main_media->url . '">' . $main_media->name . '</a>';
		}
		if ($main_media->type == 'docs') {
			if ($main_media->source == 'url')
				$media = '<a target="_blank" href="' . $main_media->url . '">' . $main_media->name . '</a>';
			if ($main_media->source == 'local')
				$media = '<a target="_blank" href="' . str_replace("/administrator", "", JURI::base()) . '/' . $configs->docsin . '/' . $main_media->local . '">' . $main_media->name . '</a>';
		}

		if (isset($media)) {
			return $media;
		} else {
			return NULL;
		}
	}

	function jwAllVideos(&$row, $parawidth = 300, $paraheight = 20, $parvwidth = 400, $parvheight = 300, $layout_id = "", $auto_play)
	{
		if (!JPluginHelper::isEnabled('content', 'jw_allvideos')) {
			return 'Please install and enable plugin JW AllVideos https://www.joomlaworks.net/extensions/free/allvideos ';
		}

		JPluginHelper::importPlugin('content', 'jw_allvideos');
		$app = JFactory::getApplication();
		$item = new stdClass;
		$item->text = $row;
		$params = new JRegistry;
		$params->set('awidth', $parawidth);
		$params->set('aheight', $paraheight);
		$params->set('vwidth', $parvwidth);
		$params->set('vheight', $parvheight);
		$params->set('autoplay', 0);
		$params->set('playerTemplate', 'Framed');
		$app->triggerEvent('onContentPrepare', array('context.jwallvideo', &$item, &$params));
		$row = $item->text;
		return $item->text;
	}

	function getRevenueByDate($date)
	{
		$db = JFactory::getDBO();
		$sql = "SELECT amount, amount_paid, currency FROM #__guru_order WHERE status='Paid'";
		$db->setQuery($sql);
		$db->execute();
		$result = $db->loadAssocList();

		$sum = array();
		foreach ($result as $value) {

			if (isset($value["amount_paid"]) && trim($value["amount_paid"]) != "" && trim($value["amount_paid"]) != "-1") {
				$sum[$value["currency"]] += $value['amount_paid'];
			} else {
				$sum[$value["currency"]] += $value['amount'];
			}
		}

		return $sum['USD'];
	}
};
