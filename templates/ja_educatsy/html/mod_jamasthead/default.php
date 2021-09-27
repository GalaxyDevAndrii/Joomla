<?php
/**
 * ------------------------------------------------------------------------
 * JA Educatsy Template
 * ------------------------------------------------------------------------
 * Copyright (C) 2004-2018 J.O.O.M Solutions Co., Ltd. All Rights Reserved.
 * @license - Copyrighted Commercial Software
 * Author: J.O.O.M Solutions Co., Ltd
 * Websites:  http://www.joomlart.com -  http://www.joomlancers.com
 * This file may not be redistributed in whole or significant part.
 * ------------------------------------------------------------------------
 */

defined('_JEXEC') or die('Restricted access');
$mh_background = new stdClass();
$mh_background->url = '';
$mh_background->type = 'no-media bg-secondary';

if(isset($masthead['params']['background']) && !empty($masthead['params']['background'])){
    $mh_background->url = $masthead['params']['background'];
    if(preg_match('/^.*\.(mp4|ogg|webm)$/', $mh_background->url)){
        $mh_background->type = 'video';
    }else{
        $mh_background->type = 'image';
    }
}
?>
<div class="ja-masthead <?php echo $mh_background->type.' '.$params->get('moduleclass_sfx','')?>" <?php if ($mh_background && $mh_background->type == 'image') : ?>style="background-image: url('<?php echo $mh_background->url; ?>')"<?php endif; ?>>
    <?php
        // Video backround
        if($mh_background && $mh_background->type == 'video') :
        preg_match_all('/^.*\.(mp4|ogg|webm)$/', $mh_background->url, $mathes);
    ?>
        <div id="ja-masthead-bg" style="display: none;">
            <video id="ja_masthead_bg_video" loop="true" autoplay="true">
                <source type="video/<?php echo $mathes[1][0] ?>" src="<?php echo $mh_background->url ?>" />
            </video>
        </div>
        <script type="text/javascript">

            jQuery(document).ready(function($){
                $(".ja-masthead<?php echo $params->get('moduleclass_sfx','')?>").addClass("masthead-video");
            });

            jQuery(window).load(function() {
                var video = jQuery("#ja_masthead_bg_video");
                var videoWrap = jQuery("#ja-masthead-bg");

                //Responsive for background-image
                var videoHeight = video.get(0).videoHeight;
                var videoWidth = video.get(0).videoWidth;
                var videoAspect = videoHeight / videoWidth;
                var dHeight = videoWrap.height();
                var dWidth = videoWrap.width();
                var divAspect = dHeight / dWidth;

                if(videoAspect > divAspect){
                    videoWidth = dWidth;
                    videoHeight = videoWidth * videoAspect;
                    video.css('margin-top','-'+(videoHeight-dHeight)/2+'px');
                    video.css('margin-left','0');
                }else{
                    videoHeight = dHeight;
                    videoWidth = videoHeight / videoAspect;
                    video.css('margin-left','-'+(videoWidth-dWidth)/2+'px');
                    video.css('margin-top','0');
                }

                video.css('width',videoWidth+'px');
                video.css('height',videoHeight+'px');

                jQuery("#ja-masthead-bg").css("display","block");
            });

            jQuery(window).resize(function() {
                var video = jQuery("#ja_masthead_bg_video");
                var videoWrap = jQuery("#ja-masthead-bg");

                var vHeight = video.get(0).videoHeight;
                var vWidth = video.get(0).videoWidth;
                var vAspect = vHeight / vWidth;
                var w = videoWrap.width();
                var h = videoWrap.height();
                var divAsp = h / w ;

                if(vAspect > divAsp){
                    vWidth = w;
                    vHeight = vWidth * vAspect;
                    video.css('margin-top','-'+(vHeight-h)/2+'px');
                    video.css('margin-left','0');
                }else{
                    vHeight = h;
                    vWidth = vHeight / vAspect;
                    video.css('margin-left','-'+(vWidth-w)/2+'px');
                    video.css('margin-top','0');
                }

                video.css('width',vWidth+'px');
                video.css('height',vHeight+'px');

                jQuery("#ja-masthead-bg").css("display","block");
            });
        </script>
    <?php endif; ?>
    <div class="container">
        <div class="ja-masthead-detail">
            <div class="block-breadcrumb">
                <?php echo JHtml::_('content.prepare', '{loadposition breadcrumbs}'); ?>
            </div>
            
            <h1 class="ja-masthead-title"><?php echo $masthead['title']; ?></h1>
            <?php
            // Alkis - Removed JA Masthead Descriptions because they were not working properly
            /*
            <?php if ($masthead['description'] != '') : ?>
              <div class="ja-masthead-description"><?php echo $masthead['description']; ?></div>
            <?php endif; ?>
            */
            ?>
        </div>
    </div>
</div>