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
function jwAllVideos( &$row, $parawidth=300, $paraheight=20, $parvwidth=400, $parvheight=300) {
	// Globals
	//global $mainframe;
	$app = JFactory::getApplication();

	// JoomlaWorks reference parameters
	$plg_name					= "jw_allvideos";
	$plg_tag					= "";
	$plg_copyrights_start		= "\n\n<!-- JoomlaWorks \"AllVideos\" Plugin (v2.5.3) starts here -->\n";
	$plg_copyrights_end			= "\n<!-- JoomlaWorks \"AllVideos\" Plugin (v2.5.3) ends here -->\n\n";
    // Paths without the ending slash
	$mosConfig_absolute_path	= JPATH_SITE;
	
	$mosConfig_live_site = NULL;
	
    //$mosConfig_live_site		= $mainframe->isAdmin() ? $mainframe->getSiteURL() : JURI::base();
    if(substr($mosConfig_live_site, -1)=="/") $mosConfig_live_site = substr($mosConfig_live_site, 0, -1);
    
	// Includes
	include($mosConfig_absolute_path."/plugins/content/jw_allvideos_sources.php");
	
	// simple performance check to determine whether plugin should process further
	$grabTags = str_replace("(","",str_replace(")","",implode(array_keys($tagReplace),"|")));
	if (preg_match("#{(".$grabTags.")}#s",$row)==false) {return true;}

	// general
	$av_template			= 'getault';
	$av_compressjs			= 0;
	// video
	$vfolder 				= 'images/stories/videos';
	$vwidth 				= $parvwidth;
	$vheight 				= $parvheight;
	// audio
	$afolder 				= '';
	$awidth 				= $parawidth;
	$aheight 				= $paraheight;
	// global
	$autoplay 				= 'false';
	$transparency 			= 'transparent';
	$background 			= '';
	// FLV playback
	$av_flvcontroller 		= 'bottom';	

	if($av_flvcontroller == "over"){
		$av_flvcontroller = "&controlbar=over";
	} else {
		$av_flvcontroller = "";
	}

	// check whether plugin has been unpublished
	if (0) {
		foreach ($tagReplace as $plg_tag => $value) {
			$regex = "#{".$plg_tag."}(.*?){/".$plg_tag."}#s";
			$row = preg_replace( $regex, "", $row );
		}
		return $row;
	} else {
	
		// add CSS/JS to the head
		static $loadJWAVcss;
		if(!$loadJWAVcss) {
			$loadJWAVcss=1;
			$jwavhead = '
	<style type="text/css" media="all">
		@import "'.$mosConfig_live_site.'/plugins/content/jw_allvideos/templates/'.$av_template.'/template_css.css";
	</style>
			';
			if($av_compressjs){
			$jwavhead .= '
	<script type="text/javascript" src="'.$mosConfig_live_site.'/plugins/content/jw_allvideos/players/jw_allvideos_scripts.php"></script>
			';
			} else {
			$jwavhead .= '
	<script type="text/javascript" src="'.$mosConfig_live_site.'/plugins/content/jw_allvideos/players/silverlight.js"></script>
	<script type="text/javascript" src="'.$mosConfig_live_site.'/plugins/content/jw_allvideos/players/wmvplayer.js"></script>
	<script type="text/javascript" src="'.$mosConfig_live_site.'/plugins/content/jw_allvideos/players/quicktimeplayer/AC_QuickTime.js"></script>
			';
			}
			
			$app->addCustomHeadTag($plg_copyrights_start.$jwavhead.$plg_copyrights_end);
		}
		
	}

	// START ALLVIDEOS LOOP	
	foreach ($tagReplace as $plg_tag => $value) {
		// expression to search for
		$regex = "#{".$plg_tag."}(.*?){/".$plg_tag."}#s";			
		// process tags
		if (preg_match_all($regex, $row, $matches, PREG_PATTERN_ORDER) > 0) {
			// start the replace loop
			foreach ($matches[0] as $key => $match) {
				$tagcontent 	= preg_replace("/{.+?}/", "", $match);
				$tagparams 		= explode('|',$tagcontent);
				$tagsource 		= $tagparams[0];
				$final_vwidth 	= (@$tagparams[1]) ? $tagparams[1] : $vwidth;
				$final_vheight 	= (@$tagparams[2]) ? $tagparams[2] : $vheight;
				$final_autoplay = (@$tagparams[3]) ? $tagparams[3] : $autoplay;
								
				// replacements
				$findAVparams = array(
					"{AVSOURCE}",
					"{VFOLDER}",
					"{VWIDTH}",
					"{VHEIGHT}",
					"{AFOLDER}",
					"{AWIDTH}",
					"{AHEIGHT}",		
					"{AUTOPLAY}",
					"{TRANSPARENCY}",
					"{BACKGROUND}",
					"{CONTROLBAR}",
				);
				
				// special treatment
				if($plg_tag=="yahoo"){
					$tagsourceyahoo = explode('/',$tagsource);
					$tagsource = 'id='.$tagsourceyahoo[1].'&amp;vid='.$tagsourceyahoo[0];
				}
				if($plg_tag=="youku"){
					$tagsource = substr($tagsource,3);
				}				
				
				$replaceAVparams = array(
					$tagsource,
					$vfolder,
					$final_vwidth,
					$final_vheight,
					$afolder,
					$awidth,
					$aheight,
					$final_autoplay,
					$transparency,
					$background,
					$av_flvcontroller,
				);


				// wrap HTML around players
				$wrapstart = '<span class="allvideos">';
				$wrapend = '</span>';

				//$plg_html = JFilterOutput::ampReplace($wrapstart.str_replace($findAVparams, $replaceAVparams, $tagReplace[$plg_tag]).$wrapend);
					$plg_html = str_replace($findAVparams, $replaceAVparams, $tagReplace[$plg_tag]);				
				
				// Do the replace
				$row = preg_replace("#{".$plg_tag."}".preg_quote($tagcontent)."{/".$plg_tag."}#s", $plg_html , $row);
			} // end foreach

		} // end if
	
	} // END ALLVIDEOS LOOP	
	

	
	
	return $row;
} // END FUNCTION


function create_media_using_plugin($main_media, $configs, $aheight, $awidth, $vheight, $vwidth){
		//require_once('../../../../../../plugins/content/jw_allvideos.php');

//return NULL;
$the_base_link = explode('administrator/', $_SERVER['HTTP_REFERER']);
$the_base_link = $the_base_link[0];		
	
		if($main_media->type=='video')
		{
			if($main_media->source=='code')
				$media = $main_media->code;
			if($main_media->source=='url')
				{
					//$position_watch = strpos($main_media->url, 'www.youtube.com/watch');
					if (strpos($main_media->url, 'www.youtube.com/watch')!==false)
					{ // youtube link - begin
						$link_array = explode('=',$main_media->url);
						$link_ = $link_array[1]; 	
						$media = '{youtube}'.$link_.'{/youtube}';			
					} // youtube link - end
					elseif (strpos($main_media->url, 'www.123video.nl')!==false)
					{ // 123video.nl link - begin
						$link_array = explode('=',$main_media->url);
						$link_ = $link_array[1]; 	
						$media = '{123video}'.$link_.'{/123video}';			
					} // 123video.nl link - end
					elseif (strpos($main_media->url, 'www.aniboom.com')!==false)
					{ // aniboom.com link - begin
						$begin_tag = strpos($main_media->url, 'video');
						$remaining_link = substr($main_media->url, $begin_tag + 6, strlen($main_media->url));
						$end_tag = strpos($remaining_link, '/');
						if($end_tag===false) $end_tag = strlen($remaining_link);
						$link_ = substr($remaining_link, 0, $end_tag);	
						$media = '{aniboom}'.$link_.'{/aniboom}';	
					} // aniboom.com link - end
					elseif (strpos($main_media->url, 'www.badjojo.com')!==false)
					{ // badjojo.com [adult] link - begin
						$link_array = explode('=',$main_media->url);
						$link_ = $link_array[1]; 	
						$media = '{badjojo}'.$link_.'{/badjojo}';
						//echo $media;			
					} // badjojo.com [adult] link - end
					elseif (strpos($main_media->url, 'www.brightcove.tv')!==false)
					{ // brightcove.tv link - begin
						$begin_tag = strpos($main_media->url, 'title=');
						$remaining_link = substr($main_media->url, $begin_tag + 6, strlen($main_media->url));
						$end_tag = strpos($remaining_link, '&');
						if($end_tag===false) $end_tag = strlen($remaining_link);
						$link_ = substr($remaining_link, 0, $end_tag);	
						$media = '{brightcove}'.$link_.'{/brightcove}';	
					} // brightcove.tv link - end
					elseif (strpos($main_media->url, 'www.collegehumor.com')!==false)
					{ // collegehumor.com link - begin
						$link_array = explode(':',$main_media->url);
						$link_ = $link_array[2]; 	
						$media = '{collegehumor}'.$link_.'{/collegehumor}';
					} // collegehumor.com link - end
					elseif (strpos($main_media->url, 'current.com')!==false)
					{ // current.com link - begin
						$begin_tag = strpos($main_media->url, 'items/');
						$remaining_link = substr($main_media->url, $begin_tag + 6, strlen($main_media->url));
						$end_tag = strpos($remaining_link, '_');
						if($end_tag===false) $end_tag = strlen($remaining_link);
						$link_ = substr($remaining_link, 0, $end_tag);	
						$media = '{current}'.$link_.'{/current}';	
					} // current.com link - end
					elseif (strpos($main_media->url, 'dailymotion.com')!==false)
					{ // dailymotion.com link - begin
						$begin_tag = strpos($main_media->url, 'video/');
						$remaining_link = substr($main_media->url, $begin_tag + 6, strlen($main_media->url));
						$end_tag = strpos($remaining_link, '_');
						if($end_tag===false) $end_tag = strlen($remaining_link);
						$link_ = substr($remaining_link, 0, $end_tag);	
						$media = '{dailymotion}'.$link_.'{/dailymotion}';	
					} // dailymotion.com link - end
					elseif (strpos($main_media->url, 'espn')!==false)
					{ // video.espn.com link - begin
						$begin_tag = strpos($main_media->url, 'videoId=');
						$remaining_link = substr($main_media->url, $begin_tag + 8, strlen($main_media->url));
						$end_tag = strpos($remaining_link, '&');
						if($end_tag===false) $end_tag = strlen($remaining_link);
						$link_ = substr($remaining_link, 0, $end_tag);	
						$media = '{espn}'.$link_.'{/espn}';	
					} // video.espn.com link - end
					elseif (strpos($main_media->url, 'eyespot.com')!==false)
					{ // eyespot.com link - begin
						$link_array = explode('r=',$main_media->url);
						$link_ = $link_array[1]; 	
						$media = '{eyespot}'.$link_.'{/eyespot}';
					} // eyespot.com link - end
					elseif (strpos($main_media->url, 'flurl.com')!==false)
					{ // flurl.com link - begin
						$begin_tag = strpos($main_media->url, 'video/');
						$remaining_link = substr($main_media->url, $begin_tag + 6, strlen($main_media->url));
						$end_tag = strpos($remaining_link, '_');
						if($end_tag===false) $end_tag = strlen($remaining_link);
						$link_ = substr($remaining_link, 0, $end_tag);	
						$media = '{flurl}'.$link_.'{/flurl}';	
					} // flurl.com link - end
					elseif (strpos($main_media->url, 'funnyordie.com')!==false)
					{ // funnyordie.com link - begin
						$link_array = explode('videos/',$main_media->url);
						$link_ = $link_array[1]; 	
						$media = '{funnyordie}'.$link_.'{/funnyordie}';
					} // funnyordie.com link - end
					elseif (strpos($main_media->url, 'gametrailers.com')!==false)
					{ // gametrailers.com link - begin
						$begin_tag = strpos($main_media->url, 'player/');
						$remaining_link = substr($main_media->url, $begin_tag + 7, strlen($main_media->url));
						$end_tag = strpos($remaining_link, '.');
						if($end_tag===false) $end_tag = strlen($remaining_link);
						$link_ = substr($remaining_link, 0, $end_tag);	
						$media = '{gametrailers}'.$link_.'{/gametrailers}';	
					} // gametrailers.com link - end
					elseif (strpos($main_media->url, 'godtube.com')!==false)
					{ // godtube.com link - begin
						$link_array = explode('viewkey=',$main_media->url);
						$link_ = $link_array[1]; 	
						$media = '{godtube}'.$link_.'{/godtube}';
					} // godtube.com link - end
					elseif (strpos($main_media->url, 'gofish.com')!==false)
					{ // gofish.com link - begin
						$link_array = explode('gfid=',$main_media->url);
						$link_ = $link_array[1]; 	
						$media = '{gofish}'.$link_.'{/gofish}';
					} // gofish.com link - end
					elseif (strpos($main_media->url, 'google.com')!==false)
					{ // Google Video link - begin
						$link_array = explode('docid=',$main_media->url);
						$link_ = $link_array[1]; 	
						$media = '{google}'.$link_.'{/google}';
					} // Google Video link - end
					elseif (strpos($main_media->url, 'guba.com')!==false)
					{ // guba.com link - begin
						$link_array = explode('watch/',$main_media->url);
						$link_ = $link_array[1]; 	
						$media = '{guba}'.$link_.'{/guba}';
					} // guba.com link - end
					elseif (strpos($main_media->url, 'hook.tv')!==false)
					{ // hook.tv link - begin
						$link_array = explode('key=',$main_media->url);
						$link_ = $link_array[1]; 	
						$media = '{hook}'.$link_.'{/hook}';
					} // hook.tv link - end
					elseif (strpos($main_media->url, 'jumpcut.com')!==false)
					{ // jumpcut.com link - begin
						$link_array = explode('id=',$main_media->url);
						$link_ = $link_array[1]; 	
						$media = '{jumpcut}'.$link_.'{/jumpcut}';
					} // jumpcut.com link - end
					elseif (strpos($main_media->url, 'kewego.com')!==false)
					{ // kewego.com link - begin
						$begin_tag = strpos($main_media->url, 'video/');
						$remaining_link = substr($main_media->url, $begin_tag + 6, strlen($main_media->url));
						$end_tag = strpos($remaining_link, '.');
						if($end_tag===false) $end_tag = strlen($remaining_link);
						$link_ = substr($remaining_link, 0, $end_tag);	
						$media = '{kewego}'.$link_.'{/kewego}';	
					} // kewego.com link - end
					elseif (strpos($main_media->url, 'krazyshow.com')!==false)
					{ // krazyshow.com [adult] link - begin
						$link_array = explode('cid=',$main_media->url);
						$link_ = $link_array[1]; 	
						$media = '{krazyshow}'.$link_.'{/krazyshow}';
					} // krazyshow.com [adult] link - end
					elseif (strpos($main_media->url, 'ku6.com')!==false)
					{ // ku6.com link - begin
						$begin_tag = strpos($main_media->url, 'show/');
						$remaining_link = substr($main_media->url, $begin_tag + 5, strlen($main_media->url));
						$end_tag = strpos($remaining_link, '.');
						if($end_tag===false) $end_tag = strlen($remaining_link);
						$link_ = substr($remaining_link, 0, $end_tag);	
						$media = '{ku6}'.$link_.'{/ku6}';	
					} // ku6.com link - end
					elseif (strpos($main_media->url, 'liveleak.com')!==false)
					{ // liveleak.com link - begin
						$link_array = explode('i=',$main_media->url);
						$link_ = $link_array[1]; 	
						$media = '{liveleak}'.$link_.'{/liveleak}';
					} // liveleak.com link - end
					elseif (strpos($main_media->url, 'metacafe.com')!==false)
					{ // metacafe.com link - begin
						$begin_tag = strpos($main_media->url, 'watch/');
						$remaining_link = substr($main_media->url, $begin_tag + 6, strlen($main_media->url));
						$end_tag = strlen($remaining_link);
						$link_ = substr($remaining_link, 0, $end_tag);	
						$media = '{metacafe}'.$link_.'{/metacafe}';	
					} // metacafe.com link - end
					elseif (strpos($main_media->url, 'mofile.com')!==false)
					{ // mofile.com link - begin
						$begin_tag = strpos($main_media->url, 'com/');
						$remaining_link = substr($main_media->url, $begin_tag + 4, strlen($main_media->url));
						$end_tag = strpos($remaining_link, '/');
						if($end_tag===false) $end_tag = strlen($remaining_link);
						$link_ = substr($remaining_link, 0, $end_tag);	
						$media = '{mofile}'.$link_.'{/mofile}';	
					} // mofile.com link - end
					elseif (strpos($main_media->url, 'myspace.com')!==false)
					{ // myspace.com link - begin
						$link_array = explode('VideoID=',$main_media->url);
						$link_ = $link_array[1]; 	
						$media = '{myspace}'.$link_.'{/myspace}';
					} // myspace.com link - end
					elseif (strpos($main_media->url, 'myvideo.de')!==false)
					{ // myvideo.de link - begin
						$begin_tag = strpos($main_media->url, 'watch/');
						$remaining_link = substr($main_media->url, $begin_tag + 6, strlen($main_media->url));
						$end_tag = strpos($remaining_link, '/');
						if($end_tag===false) $end_tag = strlen($remaining_link);
						$link_ = substr($remaining_link, 0, $end_tag);	
						$media = '{myvideo}'.$link_.'{/myvideo}';	
					} // myvideo.de link - end
					elseif (strpos($main_media->url, 'redtube.com')!==false)
					{ // redtube.com [adult] link - begin
						$link_array = explode('/',$main_media->url);
						$link_ = $link_array[1]; 	
						$media = '{redtube}'.$link_.'{/redtube}';
					} // redtube.com [adult] - end
					elseif (strpos($main_media->url, 'revver.com')!==false)
					{ // revver.com link - begin
						$begin_tag = strpos($main_media->url, 'video/');
						$remaining_link = substr($main_media->url, $begin_tag + 6, strlen($main_media->url));
						$end_tag = strpos($remaining_link, '/');
						if($end_tag===false) $end_tag = strlen($remaining_link);
						$link_ = substr($remaining_link, 0, $end_tag);	
						$media = '{revver}'.$link_.'{/revver}';	
					} // revver.com link - end
					elseif (strpos($main_media->url, 'sapo.pt')!==false)
					{ // sapo.pt link - begin
						$link_array = explode('pt/',$main_media->url);
						$link_ = $link_array[1]; 	
						$media = '{sapo}'.$link_.'{/sapo}';
					} // sapo.pt - end
					elseif (strpos($main_media->url, 'sevenload.com')!==false)
					{ // sevenload.com link - begin
						$begin_tag = strpos($main_media->url, 'videos/');
						$remaining_link = substr($main_media->url, $begin_tag + 7, strlen($main_media->url));
						$end_tag = strpos($remaining_link, '-');
						if($end_tag===false) $end_tag = strlen($remaining_link);
						$link_ = substr($remaining_link, 0, $end_tag);	
						$media = '{sevenload}'.$link_.'{/sevenload}';	
					} // sevenload.com link - end
					elseif (strpos($main_media->url, 'sohu.com')!==false)
					{ // sohu.com link - begin
						$link_array = explode('/',$main_media->url);
						$link_ = $link_array[count($link_array)-1]; 	
						$media = '{sohu}'.$link_.'{/sohu}';
					} // sohu.com - end
					elseif (strpos($main_media->url, 'southparkstudios.com')!==false)
					{ // southparkstudios.com link - begin
						$begin_tag = strpos($main_media->url, 'clips/');
						$remaining_link = substr($main_media->url, $begin_tag + 6, strlen($main_media->url));
						$end_tag = strpos($remaining_link, '/');
						if($end_tag===false) $end_tag = strlen($remaining_link);
						$link_ = substr($remaining_link, 0, $end_tag);	
						$media = '{southpark}'.$link_.'{/southpark}';	
					} // southparkstudios.com link - end
					elseif (strpos($main_media->url, 'spike.com')!==false)
					{ // spike.com link - begin
						$link_array = explode('video/',$main_media->url);
						$link_ = $link_array[1]; 	
						$media = '{spike}'.$link_.'{/spike}';
					} // spike.com - end
					elseif (strpos($main_media->url, 'stickam.com')!==false)
					{ // stickam.com link - begin
						$link_array = explode('mId=',$main_media->url);
						$link_ = $link_array[1]; 	
						$media = '{stickam}'.$link_.'{/stickam}';
					} // stickam.com - end
					elseif (strpos($main_media->url, 'stupidvideos.com')!==false)
					{ // stupidvideos.com link - begin
						$link_array = explode('#',$main_media->url);
						$link_ = $link_array[1]; 	
						$media = '{stupidvideos}'.$link_.'{/stupidvideos}';
					} // stupidvideos.com - end
					elseif (strpos($main_media->url, 'tudou.com')!==false)
					{ // tudou.com link - begin
						$begin_tag = strpos($main_media->url, 'view/');
						$remaining_link = substr($main_media->url, $begin_tag + 5, strlen($main_media->url));
						$end_tag = strpos($remaining_link, '/');
						if($end_tag===false) $end_tag = strlen($remaining_link);
						$link_ = substr($remaining_link, 0, $end_tag);	
						$media = '{tudou}'.$link_.'{/tudou}';	
					} // tudou.com link - end
					elseif (strpos($main_media->url, 'ustream.tv')!==false)
					{ // ustream.tv link - begin
						$link_array = explode('recorded/',$main_media->url);
						$link_ = $link_array[1]; 	
						$media = '{ustream}'.$link_.'{/ustream}';
					} // ustream.tv - end
					elseif (strpos($main_media->url, 'veoh.com')!==false)
					{ // veoh.com link - begin
						$link_array = explode('videos/',$main_media->url);
						$link_ = $link_array[1]; 	
						$media = '{veoh}'.$link_.'{/veoh}';
					} // veoh.com - end
					elseif (strpos($main_media->url, 'videotube.de')!==false)
					{ // videotube.de link - begin
						$link_array = explode('watch/',$main_media->url);
						$link_ = $link_array[1]; 	
						$media = '{videotube}'.$link_.'{/videotube}';
					} // videotube.de - end
					elseif (strpos($main_media->url, 'vidiac.com')!==false)
					{ // vidiac.com link - begin
						$begin_tag = strpos($main_media->url, 'video/');
						$remaining_link = substr($main_media->url, $begin_tag + 6, strlen($main_media->url));
						$end_tag = strpos($remaining_link, '.');
						if($end_tag===false) $end_tag = strlen($remaining_link);
						$link_ = substr($remaining_link, 0, $end_tag);	
						$media = '{vidiac}'.$link_.'{/vidiac}';	
					} // vidiac.com link - end
					elseif (strpos($main_media->url, 'vimeo.com')!==false)
					{ // vimeo.com link - begin
						$link_array = explode('.com/',$main_media->url);
						$link_ = $link_array[1]; 	
						$media = '{vimeo}'.$link_.'{/vimeo}';
					} // vimeo.com - end
					elseif (strpos($main_media->url, 'yahoo.com')!==false)
					{ // video.yahoo.com link - begin
						$link_array = explode('watch/',$main_media->url);
						$link_ = $link_array[1]; 	
						$media = '{yahoo}'.$link_.'{/yahoo}';			
					} // video.yahoo.com - end
					elseif (strpos($main_media->url, 'youare.tv')!==false)
					{ // youare.tv link - begin
						$link_array = explode('id=',$main_media->url);
						$link_ = $link_array[1]; 	
						$media = '{youare}'.$link_.'{/youare}';			
					} // youare.tv - end
					elseif (strpos($main_media->url, 'youku.com')!==false)
					{ // youku.com link - begin
						$begin_tag = strpos($main_media->url, 'v_show/');
						$remaining_link = substr($main_media->url, $begin_tag + 7, strlen($main_media->url));
						$end_tag = strpos($remaining_link, '.');
						if($end_tag===false) $end_tag = strlen($remaining_link);
						$link_ = substr($remaining_link, 0, $end_tag);	
						$media = '{youku}'.$link_.'{/youku}';	
					} // youku.com link - end
					elseif (strpos($main_media->url, 'youmaker.com')!==false)
					{ // youmaker.com  link - begin
						$link_array = explode('id=',$main_media->url);
						$link_ = $link_array[1]; 	
						$media = '{youmaker}'.$link_.'{/youmaker}';			
					} // youmaker.com  - end
					else
					{
						//----------- not special link - begin
						$extension_array=explode('.',$main_media->url);
						$extension = $extension_array[count($extension_array)-1];
					
						if(strtolower($extension)=='flv' || strtolower($extension)=='swf' || strtolower($extension)=='mov' || strtolower($extension)=='wmv' || strtolower($extension)=='mp4' || strtolower($extension)=='divx')
							{
								$tag_begin = '{'.strtolower($extension).'remote}';
								$tag_end = '{/'.strtolower($extension).'remote}';
							}	
						if(!isset($tag_begin)) {$tag_begin=NULL;}
						if(!isset($tag_end)) {$tag_end=NULL;}	
						$media = $tag_begin.$main_media->url.$tag_end;										
						//----------- not special link - begin										
					}

					$media = jwAllVideos( $media, $aheight, $awidth, $vheight, $vwidth);			
				
				}
				
				//$media = '<a target="_blank" href="'.$main_media->url.'">'.$main_media->name.'</a>';	
			if($main_media->source=='local')
				{
					$extension_array=explode('.',$main_media->local);
					$extension = $extension_array[count($extension_array)-1];
					//echo $extension;
					if(strtolower($extension)=='flv' || strtolower($extension)=='swf' || strtolower($extension)=='mov' || strtolower($extension)=='wmv' || strtolower($extension)=='mp4' || strtolower($extension)=='divx')
						{
							$tag_begin = '{'.strtolower($extension).'remote}';
							$tag_end = '{/'.strtolower($extension).'remote}';
						}		
					if(!isset($tag_begin)){$tag_begin=NULL;}			
					if(!isset($tag_end)){$tag_end=NULL;}			
					$media = $tag_begin.$the_base_link.$configs->videoin.'/'.$main_media->local.$tag_end;
					$media = jwAllVideos( $media, $aheight, $awidth, $vheight, $vwidth);
				}	
		}	
	if($main_media->type=='audio')
		{
	
			if($main_media->source=='code')
				$media = $main_media->code;
			if($main_media->source=='url')
				{
					$extension_array=explode('.',$main_media->url);
					$extension = $extension_array[count($extension_array)-1];
					if(strtolower($extension)=='mp3' || strtolower($extension)=='wma')
						{
							$tag_begin = '{'.strtolower($extension).'remote}';
							$tag_end = '{/'.strtolower($extension).'remote}';
						}	
					$media = $tag_begin.$main_media->url.$tag_end;
					$media = jwAllVideos( $media, $aheight, $awidth, $vheight, $vwidth);
				 //$media = '<a target="_blank" href="'.$main_media->url.'">'.$main_media->name.'</a>';	
				 }
			if($main_media->source=='local')
				{
					$extension_array=explode('.',$main_media->local);
					$extension = $extension_array[count($extension_array)-1];
					if(strtolower($extension)=='mp3' || strtolower($extension)=='wma')
						{
							$tag_begin = '{'.strtolower($extension).'remote}';
							$tag_end = '{/'.strtolower($extension).'remote}';
						}	
					//$params = '';
					$media = $tag_begin.$the_base_link.$configs->audioin.'/'.$main_media->local.$tag_end;
					$media = jwAllVideos( $media, $aheight, $awidth, $vheight, $vwidth);
				}
			
		}		
	if($main_media->type=='url')
		{
			$media = '<a target="_blank" href="'.$main_media->url.'">'.$main_media->name.'</a>';	
		}

	if($main_media->type=='image')
		{

				$img_size = @getimagesize(JPATH_SITE.DIRECTORY_SEPARATOR.$configs->imagesin.'/'.$main_media->local);
				if(isset($img_size[0]) && isset($img_size[1])){
					$img_width = $img_size[0];
					$img_height = $img_size[1];
					if($img_width>0 && $img_height>0)
					{ 
						if($main_media->width > 0)
							{
								$thumb_width = $main_media->width;
								$thumb_height = $img_height / ($img_width/$main_media->width);
							}
						elseif($main_media->height > 0)	
							{
								$thumb_height = $main_media->height;
								$thumb_width = $img_width / ($img_height/$main_media->height);		
							}
						else
							{
								$thumb_height = 200;
								$thumb_width = $img_width / ($img_height/200);									
							}
		
					}
				if(isset($thumb_width) && isset($thumb_height)) {$media = '<img width="'.$thumb_width.'" height="'.$thumb_height.'" src="';} else {$media = '<img src="';}
				$media .= $the_base_link.DIRECTORY_SEPARATOR.$configs->imagesin.'/'.$main_media->local.'" />';	
			}
		}					
		if(!isset($media)) { return NULL;}
		return $media;
	}	

?>