<?php
/**
 * ------------------------------------------------------------------------
 * JA Masthead Module 
 * ------------------------------------------------------------------------
 * Copyright (C) 2004-2018 J.O.O.M Solutions Co., Ltd. All Rights Reserved.
 * @license - GNU/GPL, http://www.gnu.org/licenses/gpl.html
 * Author: J.O.O.M Solutions Co., Ltd
 * Websites: http://www.joomlart.com - http://www.joomlancers.com
 * ------------------------------------------------------------------------
 */

defined('_JEXEC') or die('Restricted access');
$mh_background = new stdClass();
$mh_background->url = '';
$mh_background->type = '';

if(isset($masthead['params']['background']) && !empty($masthead['params']['background'])){
    $mh_background->url = $masthead['params']['background'];
    if(preg_match('/^.*\.(mp4|ogg|webm)$/', $mh_background->url)){
        $mh_background->type = 'video';
    }else{
        $mh_background->type = 'image';
    }
}
?>
<div class="ja-masthead<?php echo $params->get('moduleclass_sfx','')?>" <?php if ($mh_background && $mh_background->type == 'image') : ?>style="background-image: url('<?php echo $mh_background->url; ?>')"<?php endif; ?>>
	<?php
        // Video backround 
        if($mh_background && $mh_background->type == 'video') : 
        preg_match_all('/^.*\.(mp4|ogg|webm)$/', $mh_background->url, $mathes);
    ?>
        <div id="ja-masthead-bg">
            <video id="ja_masthead_bg_video" loop="true" autoplay="true">
                <source type="video/<?php echo $mathes[1][0] ?>" src="<?php echo $mh_background->url ?>" />
            </video>
        </div>
        <script type="text/javascript">
            jQuery(document).ready(function($){
                $("div.jamasthead"<?php echo $params->get('moduleclass_sfx','')?>).css("background-image","none");
                $(".ja-masthead<?php echo $params->get('moduleclass_sfx','')?>").addClass("masthead-video");
                
                //Responsive for background-image
                var video = $("#ja_masthead_bg_video");
                var videoHeight = video.get(0).videoHeight;
                var videoWidth = video.get(0).videoWidth;
                var videoAspect = videoHeight / videoWidth;
                var dHeight = $("#ja-masthead-bg").height();
                var dWidth = $("#ja-masthead-bg").width();
                var divAspect = dHeight / dWidth;
                
                if(videoAspect > divAspect){
                    videoWidth = dWidth;
                    videoHeight = videoWidth * videoAspect;
                    video.css('width',videoWidth+'px');
                    video.css('height',videoHeight+'px');
                    video.css('top','-'+(videoHeight-dHeight)/2+'px');
                    video.css('margin-left','0');
                }else{
                    videoHeight = dHeight;
                    videoWidth = videoHeight / videoAspect;
                    video.css('width',videoWidth+'px');
                    video.css('height',videoHeight+'px');
                    video.css('margin-left','-'+(videoWidth-dWidth)/2+'px');
                    video.css('margin-top','0');
                }
                    
                $(window).resize(function(){
                    var vHeight = video.get(0).videoHeight;
                    var vWidth = video.get(0).videoWidth;
                    var vAspect = vHeight / vWidth;
                    var w = $("#ja-masthead-bg").width();
                    var h = $("#ja-masthead-bg").height();
                    var divAsp = h / w ;
                    
                    if(vAspect > divAsp){
                        vWidth = w;
                        vHeight = vWidth * vAspect;
                        video.css('width',vWidth+'px');
                        video.css('height',vHeight+'px');
                        video.css('top','-'+(vHeight-h)/2+'px');
                        video.css('margin-left','0');
                    }else{
                        vHeight = h;
                        vWidth = vHeight / vAspect;
                        video.css('width',vWidth+'px');
                        video.css('height',vHeight+'px');
                        video.css('margin-left','-'+(vWidth-w)/2+'px');
                        video.css('margin-top','0');
                    }
                });
            });
        </script>
    <?php endif; ?>
    <div class="ja-masthead-detail">
		<h3 class="ja-masthead-title"><?php echo $masthead['title']; ?></h3>
        <?php if ($masthead['description'] != '') : ?>
		  <div class="ja-masthead-description"><?php echo $masthead['description']; ?></div>
        <?php endif; ?>
	</div>
</div>	